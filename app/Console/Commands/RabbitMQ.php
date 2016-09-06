<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQ extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mq {action=test} {msg=hello}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'rabbit MQ';

    /**
     *
     * @var \PhpAmqpLib\Channel\AMQPChannel  
     */
    protected $channel = null;
    
    /**
     *
     * @var \PhpAmqpLib\Connection\AMQPStreamConnection
     */
    protected $connection = null;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $action = $this->argument('action');
        $funcName = strtolower($action) . 'Action';
        if (method_exists($this, $funcName)) {
            call_user_func([
                $this,
                $funcName
            ]);
        } else {
            $this->error(PHP_EOL . 'No Action Found');
        }
    }

    public function init()
    {
        $this->connection = new AMQPStreamConnection('192.168.5.46', 5672, 'guest', 'guest');
//         $this->connection = new AMQPStreamConnection('120.26.200.128', 5672, 'guest', 'guest');
        $this->channel = $this->connection->channel();
    }
    
    public function template(callable $function){
        
        $this->init();
        
        call_user_func_array($function, [
            $this->connection, 
            $this->channel
        ]);
        
        $this->channel->close();
        $this->connection->close();
    }
    
    
    public function less6_pAction(){
        $fibonacci_rpc = new FibonacciRpcClient();
        
        $msg = $this->argument('msg');
        $msg = intval($msg);
        $msg || $msg = 30;
        $response = $fibonacci_rpc->call($msg);
        echo " [.] Got ", $response, "\n";
    }
    
    
    public function less6_rAction()
    {
        $this->template(function(\PhpAmqpLib\Connection\AMQPStreamConnection $connection,\PhpAmqpLib\Channel\AMQPChannel  $channel){
        
            $channel->queue_declare('rpc_queue', false, false, false, false);
    
            function fib($n) {
                static $cache = [0,1,1,2,3];
                if(isset($cache[$n])){
                    return $cache[$n];
                }
                $cache[$n] = fib($n-1) + fib($n-2);
                return fib($n-1) + fib($n-2);
                
                if ($n == 0)
                    return 0;
                if ($n == 1)
                    return 1;
                return fib($n-1) + fib($n-2);
            }
            
            echo '['.now().']'." [x] Awaiting RPC requests\n";
            $callback = function($req) {
                $n = intval($req->body);
                echo '['.now().']'." [.] fib(", $n, ")\n";
            
                $msg = new AMQPMessage(
                    (string) fib($n),
                    array('correlation_id' => $req->get('correlation_id'))
                    );
                echo '['.now().']'." [.] Done.\n";
                $req->delivery_info['channel']->basic_publish(
                    $msg, '', $req->get('reply_to'));
                $req->delivery_info['channel']->basic_ack(
                    $req->delivery_info['delivery_tag']);
            };
            
            $channel->basic_qos(null, 1, null);
            $channel->basic_consume('rpc_queue', '', false, false, false, false, $callback);
            
            while(count($channel->callbacks)) {
                $channel->wait();
            }
        });
    }
    
    
    
    
    
    
    public function less5_pAction(){
        $this->template(function(\PhpAmqpLib\Connection\AMQPStreamConnection $connection,\PhpAmqpLib\Channel\AMQPChannel  $channel){
            $argv = $this->argument('msg');
    
            $data = $argv;
            if(empty($data)) $data = "info Hello World!";
    
            $segments = explode(' ',$data);
    
            if(count($segments) > 1 ){
                $routing_key = $segments[0];
                $data = explode(' ',$data,2)[1];
            }else{
                $routing_key = 'anonymous.info';
            }
    
            $channel->exchange_declare('topic_logs', 'topic', false, false, false);
    
            $msg = new AMQPMessage($data);
    
            $channel->basic_publish($msg, 'topic_logs',$routing_key);
    
            echo " [x] Sent ",$routing_key,':', $data, "\n";
    
        });
    }
    
    
    public function less5_rAction()
    {
        $this->template(function(\PhpAmqpLib\Connection\AMQPStreamConnection $connection,\PhpAmqpLib\Channel\AMQPChannel  $channel){
    
            $channel->exchange_declare('topic_logs', 'topic', false, false, false);
    
            list($queue_name, ,) = $channel->queue_declare("", false, false, true, false);
    
            $argv = $this->argument('msg');
    
            $severities = explode(' ', $argv);
            if(empty($severities )) {
                file_put_contents('php://stderr', "Usage: [info] [warning] [error]\n");
                exit(1);
            }
    
            foreach($severities as $severity) {
                $channel->queue_bind($queue_name, 'topic_logs', $severity);
            }
    
            echo '['.now().']'.' [*] Waiting for logs. To exit press CTRL+C', "\n";
    
            $callback = function($msg){
                echo '['.now().']'.' [x] ',$msg->delivery_info['routing_key'], ':', $msg->body, "\n";
            };
    
            $channel->basic_consume($queue_name, '', false, true, false, false, $callback);
    
            while(count($channel->callbacks)) {
                $channel->wait();
            }
        });
    }
    
    

    public function less4_pAction(){
        $this->template(function(\PhpAmqpLib\Connection\AMQPStreamConnection $connection,\PhpAmqpLib\Channel\AMQPChannel  $channel){
            $argv = $this->argument('msg');
    
            $data = $argv;
            if(empty($data)) $data = "info Hello World!";
            
            $severityArray = [
                'error','info','warning'
            ];
            $severityArray = array_flip($severityArray);
            $segments = explode(' ',$data);
            
            if(count($segments) > 1 && isset($severityArray[$segments[0]]) ){
                $severity = $segments[0];
                $data = explode(' ',$data,2)[1];
            }else{
                $severity = 'info';
            }
    
            $channel->exchange_declare('direct_logs', 'direct', false, false, false);
            
            $msg = new AMQPMessage($data);
    
            $channel->basic_publish($msg, 'direct_logs',$severity);
    
            echo " [x] Sent ",$severity,':', $data, "\n";
    
        });
    }
    
    
    public function less4_rAction()
    {
        $this->template(function(\PhpAmqpLib\Connection\AMQPStreamConnection $connection,\PhpAmqpLib\Channel\AMQPChannel  $channel){
    
            $channel->exchange_declare('direct_logs', 'direct', false, false, false);
            
            list($queue_name, ,) = $channel->queue_declare("", false, false, true, false);
    
            $argv = $this->argument('msg');
            
            $severityArray = [
                'error','info','warning'
            ];
            $severityArray = array_flip($severityArray);
            
            $severities = explode(' ', $argv);
            if(empty($severities ) || !isset($severityArray[$severities[0]]) ) {
                file_put_contents('php://stderr', "Usage: [info] [warning] [error]\n");
                exit(1);
            }
            
            foreach($severities as $severity) {
                $channel->queue_bind($queue_name, 'direct_logs', $severity);
            }
            
            echo '['.now().']'.' [*] Waiting for logs. To exit press CTRL+C', "\n";
            
            $callback = function($msg){
                echo '['.now().']'.' [x] ',$msg->delivery_info['routing_key'], ':', $msg->body, "\n";
            };
    
            $channel->basic_consume($queue_name, '', false, true, false, false, $callback);

            while(count($channel->callbacks)) {
                $channel->wait();
            }
        });
    }
    
    public function less3_pAction(){
        $this->template(function(\PhpAmqpLib\Connection\AMQPStreamConnection $connection,\PhpAmqpLib\Channel\AMQPChannel  $channel){
            
            $argv = $this->argument('msg');
            
            $data = $argv;
            if(empty($data)) $data = "info: Hello World!";
            
            $channel->exchange_declare('logs', 'fanout', false, false, false);
            
            $msg = new AMQPMessage($data);
            
            $channel->basic_publish($msg, 'logs');
            
            echo " [x] Sent ", $data, "\n";
            
        });
    }
    
    
    public function less3_rAction()
    {
        $this->template(function(\PhpAmqpLib\Connection\AMQPStreamConnection $connection,\PhpAmqpLib\Channel\AMQPChannel  $channel){
        
            $channel->exchange_declare('logs', 'fanout', false, false, false);
            
            list($queue_name, ,) = $channel->queue_declare("", false, false, true, false);
            
            $channel->queue_bind($queue_name, 'logs');
            
            echo '['.now().']'.' [*] Waiting for logs. To exit press CTRL+C', "\n";
            
            $callback = function($msg){
                echo '['.now().']'.' [x] ', $msg->body, "\n";
            };
            
            $channel->basic_consume($queue_name, '', false, true, false, false, $callback);
            
            while(count($channel->callbacks)) {
                $channel->wait();
            }
        
        });
    }
    
    
    
    
    public function less2_pAction(){
        $this->init();
        
        $argv = $this->argument('msg');
        
        $queue_name = 'task_queue_durable';
        
        $data = $argv;
        if(empty($data)) $data = "Hello World!";
        
        $this->channel->queue_declare($queue_name, false, true, false, false);
        
        $msg = new AMQPMessage($data,
            array('delivery_mode' => 2) # make message persistent
        );
        
        $this->channel->basic_publish($msg, '', $queue_name);
        
        echo '['.now()."] [x] Sent ", $data, "\n";

        $this->channel->close();
        $this->connection->close();
    }
    
    
    public function less2_rAction()
    {
        $this->init();
        
        
        $queue_name = 'task_queue_durable';
        
        // Create Queue If Not Exists
        $this->channel->queue_declare($queue_name, false, true, false, false);
    
        echo '['.now().'] [*] Waiting for messages. To exit press CTRL+C', "\n";

        $callback = function(\PhpAmqpLib\Message\AMQPMessage $msg) {
            $now = date('Y-m-d H:i:s');
            echo "[{$now}] [x] Received ", $msg->body, "\n";
            sleep(substr_count($msg->body, '.'));
            $now = date('Y-m-d H:i:s');
            echo "[{$now}] [x] Done", "\n";
            $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
        };
        
        $this->channel->basic_qos(null, 1, null);
        $this->channel->basic_consume($queue_name, '', false, false, false, false, $callback);
        mt_mark('');
        while(count($this->channel->callbacks)) {
            mt_mark('start');
            $this->channel->wait();
            
            echo "[".now()."]", "\n";
            dump(mt_mark('start','end'));
            mt_mark('[clear]');
        }
    
        $this->channel->close();
        $this->connection->close();
    }
    
    public function less1_pAction()
    {
        $this->init();
        
        $queue_name = 'hello';
        
        $this->channel->queue_declare($queue_name, false, false, false, false);
        
        $msg = new AMQPMessage('Hello World!');
        $this->channel->basic_publish($msg, '', $queue_name);
        
        echo " [x] Sent 'Hello World!'\n";
        
        $this->channel->close();
        $this->connection->close();
    }
    
    
    public function less1_rAction()
    {
        $this->init();
        $this->channel->queue_declare('hello', false, false, false, false);
        
        echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";
    
        
        $callback = function($msg) {
            $now = date('Y-m-d H:i:s');
            echo "[{$now}] [x] Received ", $msg->body, "\n";
        };
        
        $this->channel->basic_consume('hello', '', false, true, false, false, $callback);
        
        while(count($this->channel->callbacks)) {
            $this->channel->wait();
        }
        
        
        $this->channel->close();
        $this->connection->close();
    }
    
    
}

class FibonacciRpcClient {
    private $connection;
    private $channel;
    private $callback_queue;
    private $response;
    private $corr_id;

    public function __construct() {
        $this->connection = new AMQPStreamConnection(
            '192.168.5.46', 5672, 'guest', 'guest');
        $this->channel = $this->connection->channel();
        list($this->callback_queue, ,) = $this->channel->queue_declare(
            "", false, false, true, false);
        $this->channel->basic_consume(
            $this->callback_queue, '', false, false, false, false,
            array($this, 'on_response'));
    }
    public function on_response($rep) {
        if($rep->get('correlation_id') == $this->corr_id) {
            $this->response = $rep->body;
        }
    }

    public function call($n) {
        $this->response = null;
        $this->corr_id = uniqid();

        $msg = new AMQPMessage(
            (string) $n,
            array('correlation_id' => $this->corr_id,
                'reply_to' => $this->callback_queue)
        );
        $this->channel->basic_publish($msg, '', 'rpc_queue');
        while(!$this->response) {
            echo '['.now().']'.' [*]',"\n";
            $this->channel->wait();
            echo '['.now().']'.' [*]',"\n";
        }
        return intval($this->response);
    }
};


