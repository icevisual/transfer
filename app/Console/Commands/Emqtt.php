<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Extensions\Mqtt\BluerhinosMqtt;
use BinSoul\Net\Mqtt\Flow\OutgoingConnectFlow;
use BinSoul\Net\Mqtt\Packet\ConnectRequestPacket;
use sskaje\mqtt\MQTT;
use sskaje\mqtt\Debug;
use sskaje\mqtt\MessageHandler;

class Emqtt extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'em {action=test} {msg=hello}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'EMQ test';

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
        $mqtt = new MQTT("tcp://192.168.5.21:1883/");
        
        $context = stream_context_create();
        $mqtt->setSocketContext($context);
        
//         Debug::Enable();
        
        // $mqtt->setAuth('sskaje', '123123');
        $mqtt->setKeepalive(36);
        $connected = $mqtt->connect();
        if (! $connected) {
            die("Not connected\n");
        }
        
        $this->connection = $mqtt;
    }

    public function template(callable $function)
    {
        $this->init();
        
        call_user_func_array($function, [
            $this->connection
        ]);
    }

    public function testAction()
    {
        $this->template(function ($mqtt) {
            
            $topics['/0CRngr3ddpVzUBoeF'] = 2;
            $mqtt->subscribe($topics);
            
            // #$mqtt->unsubscribe(array_keys($topics));
            
            $callback = new \App\Extensions\Mqtt\MySubscribeCallback();
            
            $mqtt->setHandler($callback);
            
            $mqtt->loop();
        });
    }

    public function publishAction()
    {
        $this->template(function ($mqtt) {
            $msg = "eb6881e09a258c1dc1c672cbce0abff16d4ad61a24384ddd743c5a19e6ac3dc54d2c890b65e9fa8c6ba5b3f211e17e4d";
            $mqtt->publish_async('/0CRngr3ddpVzUBoeF', $msg, 0, 0);
        });
    }
}


