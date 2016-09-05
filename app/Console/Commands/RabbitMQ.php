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
        
        $this->channel->queue_declare('hello', false, false, false, false);
        
        $msg = new AMQPMessage('Hello World!');
        $this->channel->basic_publish($msg, '', 'hello');
        
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
