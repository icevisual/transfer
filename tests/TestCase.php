<?php

use function Symfony\Component\Debug\header;
class TestCase extends Illuminate\Foundation\Testing\TestCase
{
    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://localhost';

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        return $app;
    }
    
    public static function setUpBeforeClass(){
        parent::setUpBeforeClass();
    }
    
    protected static function stdout($message){
//         fwrite(STDOUT, $message. "\r\n" );
    }
    
    protected static function _print($var){
        fwrite(STDOUT, "\r\n" );
        dump($var);
        fwrite(STDOUT, "\r\n" );
    }
    
}
