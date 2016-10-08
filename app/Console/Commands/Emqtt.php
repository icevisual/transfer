<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Extensions\Mqtt\BluerhinosMqtt;
use BinSoul\Net\Mqtt\Flow\OutgoingConnectFlow;
use BinSoul\Net\Mqtt\Packet\ConnectRequestPacket;
use sskaje\mqtt\MQTT ;
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


    public function test1Action()
    {
               
        $mqtt = new MQTT("tcp://192.168.5.21:1883/");
        
        $context = stream_context_create();
        $mqtt->setSocketContext($context);
        
        Debug::Enable();
        
        //$mqtt->setAuth('sskaje', '123123');
        $mqtt->setKeepalive(3600);
        $connected = $mqtt->connect();
        if (!$connected) {
            die("Not connected\n");
        }
        $topics['/words'] = 2;
        
        $mqtt->subscribe($topics);
        
//         #$mqtt->unsubscribe(array_keys($topics));
        
        $callback = new \App\Extensions\Mqtt\MySubscribeCallback();
        
        $mqtt->setHandler($callback);
        
        $mqtt->loop();
    }
    
    
    public function testAction()
    {
        $mqtt = new BluerhinosMqtt('192.168.5.21', 1883, "PHP"); // Change client name to something unique
        if (! $mqtt->connect()) {
            exit(1);
        }
        $topics['/word'] = array(
            "qos" => 0,
            "function" => "procmsg"
        );
        $mqtt->subscribe($topics, 0);
        while ($mqtt->proc()) {
//             echo '['.now().']'.PHP_EOL;
        }
        $mqtt->close();

        function procmsg($topic, $msg)
        {
            echo "Msg Recieved: " . date("r") . "\nTopic:{$topic}\n$msg\n";
        }
    }
    
    
    public function publishAction(){
        $mqtt = new Mqtt('192.168.5.21', 1883, "PHP"); // Change client name to something unique
        if (! $mqtt->connect()) {
            exit(1);
        }
        $mqtt->publish("hello","Hello World! at ".date("r"),0);
        
    }
    
    
    
}


