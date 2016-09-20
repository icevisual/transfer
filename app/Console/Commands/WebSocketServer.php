<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use WebSocket\Client;
use WebSocket\Server;

class WebSocketServer extends Command
{

    protected $signature = 'ws';

    protected $description = 'web socket server';

    public function handle()
    {
        new WS('localhost', 4000);
        
//         $this->chartRoom();
    }

    public function chartRoom()
    {
        global $clients;
        // 发送消息的方法
        function send_message($msg)
        {
            global $clients;
            foreach ($clients as $changed_socket) {
                @socket_write($changed_socket, $msg, strlen($msg));
            }
            return true;
        }
        
        // 解码数据
        function unmask($text)
        {
            $length = ord($text[1]) & 127;
            if ($length == 126) {
                $masks = substr($text, 4, 4);
                $data = substr($text, 8);
            } elseif ($length == 127) {
                $masks = substr($text, 10, 4);
                $data = substr($text, 14);
            } else {
                $masks = substr($text, 2, 4);
                $data = substr($text, 6);
            }
            $text = "";
            for ($i = 0; $i < strlen($data); ++ $i) {
                $text .= $data[$i] ^ $masks[$i % 4];
            }
            return $text;
        }
        
        // 编码数据
        function mask($text)
        {
            $b1 = 0x80 | (0x1 & 0x0f);
            $length = strlen($text);
            
            if ($length <= 125)
                $header = pack('CC', $b1, $length);
            elseif ($length > 125 && $length < 65536)
                $header = pack('CCn', $b1, 126, $length);
            elseif ($length >= 65536)
                $header = pack('CCNN', $b1, 127, $length);
            return $header . $text;
        }
        
        // 握手的逻辑
        function perform_handshaking($receved_header, $client_conn, $host, $port)
        {
            $headers = array();
            $lines = preg_split("/\r\n/", $receved_header);
            foreach ($lines as $line) {
                $line = chop($line);
                if (preg_match('/\A(\S+): (.*)\z/', $line, $matches)) {
                    $headers[$matches[1]] = $matches[2];
                }
            }
            
            $secKey = $headers['Sec-WebSocket-Key'];
            $secAccept = base64_encode(pack('H*', sha1($secKey . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11')));
            $upgrade = "HTTP/1.1 101 Web Socket Protocol Handshake\r\n" . "Upgrade: websocket\r\n" . "Connection: Upgrade\r\n" . "WebSocket-Origin: $host\r\n" . "WebSocket-Location: ws://$host:$port/demo/shout.php\r\n" . "Sec-WebSocket-Accept:$secAccept\r\n\r\n";
            socket_write($client_conn, $upgrade, strlen($upgrade));
        }
        
        $host = '127.0.0.1';
        $port = '9505';
        $null = NULL;
        // 创建tcp socket
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1);
        socket_bind($socket, 0, $port);
        
        // 监听端口
        socket_listen($socket);
        
        // 连接的client socket 列表
        $clients = array(
            $socket
        );
        
        // 设置一个死循环,用来监听连接 ,状态
        while (true) {
            
            $changed = $clients;
            socket_select($changed, $null, $null, 0, 10);
            
            // 如果有新的连接
            if (in_array($socket, $changed)) {
                // 接受并加入新的socket连接
                $socket_new = socket_accept($socket);
                $clients[] = $socket_new;
                
                // 通过socket获取数据执行handshake
                $header = socket_read($socket_new, 1024);
                perform_handshaking($header, $socket_new, $host, $port);
                
                // 获取client ip 编码json数据,并发送通知
                socket_getpeername($socket_new, $ip);
                $response = mask(json_encode(array(
                    'type' => 'system',
                    'message' => $ip . ' connected'
                )));
                send_message($response);
                $found_socket = array_search($socket, $changed);
                unset($changed[$found_socket]);
            }
            
            // 轮询 每个client socket 连接
            foreach ($changed as $changed_socket) {
                
                // 如果有client数据发送过来
                while (socket_recv($changed_socket, $buf, 1024, 0) >= 1) {
                    // 解码发送过来的数据
                    $received_text = unmask($buf);
                    $tst_msg = json_decode($received_text);
                    $user_name = $tst_msg->name;
                    $user_message = $tst_msg->message;
                    
                    // 把消息发送回所有连接的 client 上去
                    $response_text = mask(json_encode(array(
                        'type' => 'usermsg',
                        'name' => $user_name,
                        'message' => $user_message
                    )));
                    send_message($response_text);
                    break 2;
                }
                
                // 检查offline的client
                $buf = @socket_read($changed_socket, 1024, PHP_NORMAL_READ);
                if ($buf === false) {
                    $found_socket = array_search($changed_socket, $clients);
                    socket_getpeername($changed_socket, $ip);
                    unset($clients[$found_socket]);
                    $response = mask(json_encode(array(
                        'type' => 'system',
                        'message' => $ip . ' disconnected'
                    )));
                    send_message($response);
                }
            }
        }
        // 关闭监听的socket
        socket_close($sock);
    }

    public function WebSocketServerDemo()
    {

        function save_coverage_data($test_id)
        {
            if (! function_exists('xdebug_get_code_coverage'))
                return;
            
            $data = xdebug_get_code_coverage();
            xdebug_stop_code_coverage();
            
            if (! is_dir($GLOBALS['PHPUNIT_COVERAGE_DATA_DIRECTORY'])) {
                mkdir($GLOBALS['PHPUNIT_COVERAGE_DATA_DIRECTORY'], 0777, true);
            }
            $file = $GLOBALS['PHPUNIT_COVERAGE_DATA_DIRECTORY'] . '/' . $test_id . '.' . md5(uniqid(rand(), true));
            
            echo "Saving coverage data to $file...\n";
            file_put_contents($file, serialize($data));
        }
        
        // Setting timeout to 200 seconds to make time for all tests and manual runs.
        $server = new Server(array(
            'port' => 8859,
            'timeout' => 200
        ));
        
        echo $server->getPort(), "\n";
        
        while ($connection = $server->accept()) {
            $test_id = $server->getPath();
            $test_id = substr($test_id, 1);
            
            if (function_exists('xdebug_get_code_coverage'))
                xdebug_start_code_coverage(XDEBUG_CC_UNUSED | XDEBUG_CC_DEAD_CODE);
            
            if (class_exists('PHPUnit_Extensions_SeleniumCommon_ExitHandler'))
                PHPUnit_Extensions_SeleniumCommon_ExitHandler::init();
            
            try {
                while (1) {
                    
                    echo file_get_contents('php://input') . PHP_EOL;
                    
                    $message = $server->receive();
                    $messageArray = json_decode($message, 1);
                    $command = array_get($messageArray, 'message');
                    echo "Received $message\n\n";
                    
                    if ($command === 'exit') {
                        echo microtime(true), " Client told me to quit.  Bye bye.\n";
                        echo microtime(true), " Close response: ", $server->close(), "\n";
                        echo microtime(true), " Close status: ", $server->getCloseStatus(), "\n";
                        save_coverage_data($test_id);
                        exit();
                    }
                    
                    if ($command === 'Dump headers') {
                        $server->send(implode("\r\n", $server->getRequest()));
                    } elseif ($auth = $server->getHeader('Authorization')) {
                        $server->send("$auth - $message", 'text', false);
                    } else {
                        $server->send($message, 'text', false);
                    }
                }
            } catch (WebSocket\ConnectionException $e) {
                echo "\n", microtime(true), " Client died: $e\n";
                save_coverage_data($test_id);
            }
        }
    }
}

class WS
{

    protected  $master;

    protected $sockets = array();

    protected $debug = true;

    protected $handshake = false;
    
    protected $connection = [];

    public function __construct($address, $port)
    {
        $this->master = socket_create(AF_INET, SOCK_STREAM, SOL_TCP) or die("socket_create() failed");
        socket_set_option($this->master, SOL_SOCKET, SO_REUSEADDR, 1) or die("socket_option() failed");
        socket_bind($this->master, $address, $port) or die("socket_bind() failed");
        socket_listen($this->master, 20) or die("socket_listen() failed");
        
        $this->sockets[] = $this->master;
        $this->say("Server Started : " . date('Y-m-d H:i:s'));
        $this->say("Listening on   : " . $address . " port " . $port);
        $this->say("Master socket  : " . $this->master . "\n");
        $i = 0;
        while (true) {
            $this->log( ++$i.' START');
            $socketArr = $this->sockets;
            $write = NULL;
            $except = NULL;
            socket_select($socketArr, $write, $except, NULL); // 自动选择来消息的socket 如果是握手 自动选择主机
            foreach ($socketArr as $socket) {
                if ($socket == $this->master) { // 主机
                    $client = socket_accept($this->master);
                    if ($client < 0) {
                        $this->log("socket_accept() failed");
                        continue;
                    } else {
                        $this->connect($client);
                    }
                } else {
                    $this->log("^^^^");
                    $bytes = @socket_recv($socket, $buffer, 2048, 0);
                    $this->log("^^^^");
                    if ($bytes == 0) {
                        $this->disConnect($socket);
                        $this->clearConnect($socket);
                    } else {
                        if (!$this->isConnected($socket)) {
                            $this->doHandShake($socket, $buffer);
                            $this->setConnected($socket);
                        } else {
                            $buffer = $this->decode($buffer);
                            $this->send($socket, $buffer);
                        }
                    }
                }
            }
            $this->log($i.' END');
        }
    }
    
    public function clearConnect($socket){
        foreach ( $this->connection as $key => $v){
            if($socket == $v){
               unset($this->connection[$key]);
            }
        }
        unset($this->connection[$socket]);
    }
    
    public function setConnected($socket){
        $this->connection[] = $socket;
    }
    
    public function isConnected($socket){
        foreach ( $this->connection as $v){
            if($socket == $v){
                return true;
            }
        }
        return false;
    }
    

    public function send($client, $msg)
    {
        $this->log("> " . $msg);
        $msg = $this->frame($msg);
        socket_write($client, $msg, strlen($msg));
        $this->log("! " . strlen($msg));
    }

    public function connect($socket)
    {
        array_push($this->sockets, $socket);
        $this->say("\n" . $socket . " CONNECTED!");
    }

    public function disConnect($socket)
    {
        $index = array_search($socket, $this->sockets);
        socket_close($socket);
        $this->say($socket . " DISCONNECTED!");
        if ($index >= 0) {
            array_splice($this->sockets, $index, 1);
        }
    }

    public function doHandShake($socket, $buffer)
    {
        $this->log("\nRequesting handshake...");
        $this->log($buffer);
        list ($resource, $host, $origin, $key) = $this->getHeaders($buffer);
        $this->log("Handshaking...");
        $upgrade = "HTTP/1.1 101 Switching Protocol\r\n" . "Upgrade: websocket\r\n" . "Connection: Upgrade\r\n" . "Sec-WebSocket-Accept: " . $this->calcKey($key) . "\r\n\r\n"; // 必须以两个回车结尾
        $this->log($upgrade);
        $sent = socket_write($socket, $upgrade, strlen($upgrade));
        $this->handshake = true;
        $this->log("Done handshaking...");
        return true;
    }

    public function getHeaders($req)
    {
        $r = $h = $o = $key = null;
        if (preg_match("/GET (.*) HTTP/", $req, $match)) {
            $r = $match[1];
        }
        if (preg_match("/Host: (.*)\r\n/", $req, $match)) {
            $h = $match[1];
        }
        if (preg_match("/Origin: (.*)\r\n/", $req, $match)) {
            $o = $match[1];
        }
        if (preg_match("/Sec-WebSocket-Key: (.*)\r\n/", $req, $match)) {
            $key = $match[1];
        }
        return array(
            $r,
            $h,
            $o,
            $key
        );
    }

    public function calcKey($key)
    {
        // 基于websocket version 13
        $accept = base64_encode(sha1($key . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11', true));
        return $accept;
    }

    public function decode($buffer)
    {
        $len = $masks = $data = $decoded = null;
        $len = ord($buffer[1]) & 127;
        
        if ($len === 126) {
            $masks = substr($buffer, 4, 4);
            $data = substr($buffer, 8);
        } else 
            if ($len === 127) {
                $masks = substr($buffer, 10, 4);
                $data = substr($buffer, 14);
            } else {
                $masks = substr($buffer, 2, 4);
                $data = substr($buffer, 6);
            }
        for ($index = 0; $index < strlen($data); $index ++) {
            $decoded .= $data[$index] ^ $masks[$index % 4];
        }
        return $decoded;
    }

    public function frame($s)
    {
        $a = str_split($s, 125);
        if (count($a) == 1) {
            return "\x81" . chr(strlen($a[0])) . $a[0];
        }
        $ns = "";
        foreach ($a as $o) {
            $ns .= "\x81" . chr(strlen($o)) . $o;
        }
        return $ns;
    }

    public function say($msg = "")
    {
        echo '['.now().'] '.$msg . "\n";
    }

    public function log($msg = "")
    {
        if ($this->debug) {
            echo '['.now().'] '.$msg . "\n";
        }
    }
}



