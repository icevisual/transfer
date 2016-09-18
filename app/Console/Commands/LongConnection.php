<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Workerman\Worker;

class LongConnection extends Command
{

    protected $signature = 'lc';

    protected $description = 'LongConnection';

    public function handle()
    {
        // Create a Websocket server
        $ws_worker = new Worker("websocket://0.0.0.0:2346");
        
        // 4 processes
        $ws_worker->count = 4;
        
        // Emitted when new connection come
        $ws_worker->onConnect = function ($connection) {
            echo "New connection\n";
        };
        
        // Emitted when data received
        $ws_worker->onMessage = function ($connection, $data) {
            // Send hello $data
            $connection->send('hello ' . $data);
        };
        
        // Emitted when connection closed
        $ws_worker->onClose = function ($connection) {
            echo "Connection closed\n";
        };
        
        // Run worker
        Worker::runAll();
    }

    protected function ss()
    {
        $sfd = stream_socket_server('tcp://0.0.0.0:1234', $errno, $errstr);
        stream_set_blocking($sfd, 0);
        $base = event_base_new();
        $event = event_new();
        event_set($event, $sfd, EV_READ | EV_PERSIST, 'ev_accept', $base);
        event_base_set($event, $base);
        event_add($event);
        event_base_loop($base);

        function ev_accept($socket, $flag, $base)
        {
            $connection = stream_socket_accept($socket);
            stream_set_blocking($connection, 0);
            $buffer = event_buffer_new($connection, 'ev_read', NULL, 'ev_error', $connection);
            event_buffer_base_set($buffer, $base);
            event_buffer_timeout_set($buffer, 30, 30);
            event_buffer_watermark_set($buffer, EV_READ, 0, 0xffffff);
            event_buffer_priority_set($buffer, 10);
            event_buffer_enable($buffer, EV_READ | EV_PERSIST);
        }

        function ev_error($buffer, $error, $connection)
        {
            event_buffer_disable($buffer, EV_READ | EV_WRITE);
            event_buffer_free($buffer);
            fclose($connection);
        }

        function ev_read($buffer, $connection)
        {
            $read = event_buffer_read($buffer, 256);
            // do something....
        }
    }

    protected function s()
    {
        $sfd = socket_create(AF_INET, SOCK_STREAM, 0);
        socket_bind($sfd, "0.0.0.0", 1234);
        socket_listen($sfd, 511);
        socket_set_option($sfd, SOL_SOCKET, SO_REUSEADDR, 1);
        socket_set_nonblock($sfd);
        $rfds = array(
            $sfd
        );
        $wfds = array();
        do {
            $rs = $rfds;
            $ws = $wfds;
            $es = array();
            $ret = socket_select($rs, $ws, $es, 3);
            // read event
            foreach ($rs as $fd) {
                if ($fd == $sfd) {
                    $cfd = socket_accept($sfd);
                    socket_set_nonblock($cfd);
                    $rfds[] = $cfd;
                    echo "new client coming, fd=$cfd\n";
                } else {
                    $msg = socket_read($fd, 1024);
                    if ($msg <= 0) {
                        // close
                    } else {
                        // recv msg
                        echo "on message, fd=$fd data=$msg\n";
                    }
                }
            }
            // write event
            foreach ($ws as $fd) {
                socket_write($fd, '123');
            }
        } while (true);
    }
}