<?php
namespace AntFinancial\Sdk;

use phpseclib\Net\SFTP;
use AntFinancial\Exceptions\AntSFTPException;

class SFTPUtil
{

    private static $instance = [];

    private static $initialized = false;

    /**
     *
     * @var \phpseclib\Net\SFTP
     */
    public $sftp = false;

    public function __construct($config)
    {
        extract($config);
        $this->sftp = new SFTP($host);
        if (! $this->sftp->login($username, $pass)) {
            throw new AntSFTPException('SFTP Login Error');
        }
    }

    /**
     * 
     * @param array $config
     * @return \AntFinancial\Sdk\SFTPUtil
     */
    public static function getInstance(array $config)
    {
        ksort($config);
        $key = sha1(json_encode($config));
        if (! isset(self::$instance[$key])) {
            self::$instance[$key] = new static($config);
        }
        return self::$instance[$key];
    }
    
    public function is_file($path)
    {
        return $this->sftp->is_file($path);
    }

    
    public function download($remote_file,$local_file){
        if($this->sftp->is_file($remote_file)){
            return $this->sftp->get($remote_file,$local_file);
        }else{
            return false;
        }
    }
    
    
    public function upload($local_file, $remote_file)
    {
        $pathinfo = pathinfo($remote_file);
        $dirname = $pathinfo['dirname'];
        $path = '';
        if (! $this->sftp->is_dir($dirname)) {
            foreach (explode('/', $dirname) as $v) {
                if ($v) {
                    $path .= '/' . $v;
                    if (! $this->sftp->is_dir($path)) {
                        $mkdir = $this->sftp->mkdir($path);
                        $this->sftp->chmod(0775, $path);
                    }
                }
            }
        }
        $ret = $this->sftp->put($remote_file, file_get_contents($local_file));
        if (! $ret) {
            throw new AntSFTPException('Failed To Upload File', get_defined_vars());
        }
        $this->sftp->chmod(0775, $remote_file);
        return $ret;
    }

    public static function Main()
    {
        
        // sftp地址：115.124.16.69
        // 账号：yqzl001_dada
        // 密码：dada_123456
        // $SftpUtil = new SftpUtil('115.124.16.69', 22, 'yqzl001_dada', 'dada_123456');
        // $SftpUtil->getFileList('/');
        $SFTP = new SFTP('115.124.16.69');
        if (! $SFTP->login('yqzl001', '3hflsnvlywor')) {
            exit('Login Failed');
        }
        // $res = $SFTP->exec('rm test/test.sftp');
        // dump($res);
        $remote_file = 'h2h_batchPay_226610000053926796174_20160122112339280049186360.xls';
        $remote_file = '/download/H2H/batchPayResult/226610000053926796174';
        dump($SFTP->get($remote_file, ant_test_path('226610000053926796174')));
        // $SFTP->lstat($filename)
        // $SFTP->chdir($dir)
        // dump($SFTP->nlist('/download/H2H/batchPay/template/'));
        dump($SFTP->lstat('20160226'));
        // dump($SFTP->rmdir('test'));
        // dump($SFTP->mkdir('test'));
        // dump($SFTP->is_dir('test'));
        // dump($SFTP->put('test/test.sftp', ''));
        // dump($SFTP->delete('test/test.sftp'));
        dump($SFTP->pwd());
    }
}





