<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQCtl extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mqctl {action=test} {msg=hello}';

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
        /**
         * Send & Receive Msg To/From RabbitMQ
         * Params
         * Design Topic
         * Bind DeviceID
         */
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
        $this->connection = new AMQPStreamConnection('192.168.5.21', 5672, 'guest', 'guest');
        // $this->connection = new AMQPStreamConnection('120.26.200.128', 5672, 'guest', 'guest');
        $this->channel = $this->connection->channel();
    }

    public function template(callable $function)
    {
        $this->init();
        call_user_func_array($function, [
            $this->connection,
            $this->channel
        ]);
        $this->channel->close();
        $this->connection->close();
    }

    public function publishAction()
    {
        $this->template(function (\PhpAmqpLib\Connection\AMQPStreamConnection $connection, \PhpAmqpLib\Channel\AMQPChannel $channel) {
            $argv = $this->argument('msg');
            
            $exchangeName = 'scentrealm.iot';
            
            $deviceID = '0V355CiFS6L6aqzYV';
            $routingKeyPrefix = 'aliyun.';
            
            $channel->exchange_declare($exchangeName, 'topic', false, false, true);
            
            $data = 'hello';
            $data = $this->ask('What is your name?');
            
            $msg = new AMQPMessage($data);
            
            $channel->basic_publish($msg, $exchangeName, $routingKeyPrefix.$deviceID);
            
            echo '[' . now() . ']' ." [x] Sent ", $deviceID, ':', $data, "\n";
        });
    }

    public function receiveAction()
    {
        $this->template(function (\PhpAmqpLib\Connection\AMQPStreamConnection $connection, \PhpAmqpLib\Channel\AMQPChannel $channel) {
            
            $exchangeName = 'scentrealm.iot';
            
            $routingKeyPrefix = 'aliyun.';
            
            $channel->exchange_declare($exchangeName, 'topic', false, false, true);
            
            list ($queue_name, , ) = $channel->queue_declare("", false, false, true, false);
            
            $channel->queue_bind($queue_name, $exchangeName, $routingKeyPrefix.'*');
            
            echo '[' . now() . ']' . ' [*] Waiting for logs. To exit press CTRL+C', "\n";
            
            $callback = function ($msg) {
                echo '[' . now() . ']' . ' [x] ', $msg->delivery_info['routing_key'], ':', $msg->body, "\n";
                
                $deviceID = explode('.', $msg->delivery_info['routing_key']);
                $exitCode = \Artisan::call('iot', [
                    'msg' => $msg->body,
                    'deviceID' => end($deviceID)
                ]);
                // Send Msg to Aliyun Iot
                
                $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
            };
            
            $channel->basic_consume($queue_name, '', false, false, false, false, $callback);
            
            while (count($channel->callbacks)) {
                $channel->wait();
            }
        });
    }
    
    public function persistenceAction(){
        
        
        
        // connect RMQ comsume random queue subscribe , bind to iot exchange with a binding key of 
        // aliyun.#
        // tencent
                
    }
    
}



