<?php
namespace App\Gather\Utils;

class Ftp
{

    var $connector;

    var $getback;
    // 连接FTP
    public function __construct($ftp_server, $port, $uname, $passwd)
    {
        $this->connector = @ftp_connect($ftp_server, $port);
        $this->login_result = @ftp_login($this->connector, "$uname", "$passwd");
        if ((! $this->connector) && (! $this->login_result)) {
            echo "FTP connection has failed! \n";
            echo "Attempted to connect to $ftp_server for user $uname \n";
            die();
        } else {
            echo "Connected to $ftp_server, for user $uname \n";
        }
    }

    public function lastmodtime($value)
    {
        $getback = ftp_mdtm($this->connector, $value);
        return $getback;
    }
    // 更改当前目录
    public function changedir($targetdir)
    {
        $getback = ftp_chdir($this->connector, $targetdir);
        return $getback;
    }
    // 获取当前目录
    public function getdir()
    {
        $getback = ftp_pwd($this->connector);
        return $getback;
    }
    // 获取文件列表
    public function get_file_list($directory)
    {
        $getback = ftp_nlist($this->connector, $directory);
        return $getback;
    }
    // 获取文件
    public function get_file($file_to_get, $mode, $mode2)
    {
        $realfile = basename($file_to_get);
        $filename = $realfile;
        $checkdir = @$this->changedir($realfile);
        if ($checkdir == TRUE) {
            ftp_cdup($this->connector);
            echo "\n[DIR] $realfile";
        } else {
            echo "..... " . $realfile . "\n";
            $getback = ftp_get($this->connector, $filename, $realfile, $mode);
            if ($mode2) {
                $delstatus = ftp_delete($this->connector, $file_to_get);
                if ($delstatus == TRUE) {
                    echo "File $realfile on $host deleted \n";
                }
            }
        }
        return $getback;
    }

    public function mode($pasvmode)
    {
        $result = ftp_pasv($this->connector, $pasvmode);
    }
    // 退出
    public function ftp_bye()
    {
        ftp_quit($this->connector);
        return $getback;
    }
}

class Sftp
{
    
    // 初始配置为NULL
    private $config = NULL;
    
    // 连接为NULL
    private $conn = NULL;
    
    // 是否使用秘钥登陆
    private $use_pubkey_file = false;
    
    
    public function __construct($host,$port,$user,$passwd){
       
        $config = [
            'host' => $host,
            'port' => $port,
            'user' => $user,
            'passwd' => $passwd,
        ];
        $this->init($config);
        $this->conn = $this->connect();
    }
    
    // 初始化
    public function init($config)
    {
        $this->config = $config;
    }
    // 连接ssh ,连接有两种方式(1) 使用密码
    
    // (2) 使用秘钥
    public function connect()
    {
        $methods['hostkey'] = $this->use_pubkey_file ? 'ssh-rsa' : [];
        $conn = ssh2_connect($this->config['host'], $this->config['port'], $methods);
        // (1) 使用秘钥的时候
        if ($use_pubkey_file) {
            // 用户认证协议
            $rc = ssh2_auth_pubkey_file(
                $conn,
                $this->config['user'],
                $this->config['pubkey_file'],
                $this->config['privkey_file'],
                $this->config['passphrase']
                );
            // (2) 使用登陆用户名字和登陆密码
        } else {
            $rc = ssh2_auth_password($conn, $this->config['user'], $this->config['passwd']);
        }
        return $rc;
    }
    
    
    public function getFileList($dir){
        $res =  ssh2_exec($this->conn, 'lls '.$dir);
        edump($res);
    }
    
    // 传输数据 传输层协议,获得数据
    public function download($remote, $local)
    {
        return ssh2_scp_recv($this->conn, $remote, $local);
    }
    
    // 传输数据 传输层协议,写入ftp服务器数据
    public function upload($remote, $local, $file_mode = 0664)
    {
        return ssh2_scp_send($this->conn, $local, $remote, $file_mode);
    }
    
    // 删除文件
    public function remove($remote)
    {
        $sftp = ssh2_sftp($this->conn);
        $rc = false;
        if (is_dir("ssh2.sftp://{$sftp}/{$remote}")) {
            $rc = false;
            // ssh 删除文件夹
            $rc = ssh2_sftp_rmdir($sftp, $remote);
        } else {
            // 删除文件
            $rc = ssh2_sftp_unlink($sftp, $remote);
        }
        
        return $rc;
    }
}