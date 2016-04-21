<?php
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class Example2Test extends TestCase
{

//     public $stack;

//     public function setUp()
//     {
//         self::stdout(__METHOD__);
//         $this->stack = array();
//     }

//     public function testEmpty()
//     {
//         self::stdout(__METHOD__);
//         $this->assertTrue(empty($this->stack));
//     }

//     public function testPush()
//     {
//         self::stdout(__METHOD__);
//         array_push($this->stack, 'foo');
//         $this->assertEquals('foo', $this->stack[count($this->stack) - 1]);
//         $this->assertFalse(empty($this->stack));
//     }

//     public function testPop()
//     {
//         self::stdout(__METHOD__);
//         array_push($this->stack, 'foo');
//         $this->assertEquals('foo', array_pop($this->stack));
//         $this->assertTrue(empty($this->stack));
//     }
    
    
    public static function setUpBeforeClass()
    {
        self::stdout(__METHOD__);
    }
    public function setUp()
    {
        self::stdout(__METHOD__);
    }
    protected function assertPreConditions()
    {
        self::stdout(__METHOD__);
    }
    
    public function dataProviderTestOne(){
        self::stdout(__METHOD__);
        return [
            [1,2],
            [2,3],
            [3,4],
        ];
    }
    
    /**
     * @dataProvider dataProviderTestOne
     */
    public function testOne()
    {
        self::stdout(__METHOD__);
        $this->assertTrue(TRUE);
    }
    
    /**
     * @requires PHP 7.0.0
     */
    public function testTwo()
    {
        self::stdout(__METHOD__);
        $this->markTestIncomplete('Incomplete');
//         $this->assertTrue(FALSE);
    }
    protected function assertPostConditions()
    {
        self::stdout(__METHOD__);
    }
    public function tearDown()
    {
        self::stdout(__METHOD__);
    }
    public static function tearDownAfterClass()
    {
        self::stdout(__METHOD__);
    }
    public function onNotSuccessfulTest(Exception $e)
    {
        self::stdout(__METHOD__);
        throw $e;
    }
    
    
    
}
