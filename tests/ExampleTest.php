<?php
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ExampleTest extends TestCase
{

    public function eqtestProvider(){
        return [
            [1,'1'],
            [2,2],
            [3,'3'],
        ];
    }
    
    /**
     * @dataProvider eqtestProvider
     * @param unknown $a
     * @param unknown $b
     */
    public function testEquality($a,$b) {
        $this->assertEquals($a,$b );
    }
    
    
    public function testExpectFooActualFoo(){
        $this->expectOutputString('foo');
        print 'foo';
    }
    
    public function write($file, $content) {
        $file = fopen($file, 'w');
        if($file == false) {
            return false;
        }
        return true;
    }
        
    
    public function testFileWriting(){
        
        $this->assertFalse(@$this->write('unwriteablefile', 'stuff'));
        
    }
        
    /**
     * @expectedException ErrorException
     */
    public function testFailingInclude(){
        include 'not_existing_file.php';
    }
    
    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Some
     * @expectedExceptionMessageRegExp !^Some!
     * @expectedExceptionCode 20
     */
    public function testException(){
        throw new InvalidArgumentException('Some Message',20);
    }
    
    public function testException1(){
//         $this->setExpectedException('InvalidArgumentException','Message',302);
        $this->setExpectedExceptionRegExp('InvalidArgumentException','/^Some.*/',30);
        throw new InvalidArgumentException('Some Message',30);
    }
    
    
    
    public function dtProvider(){
        return [
            ['provider1'],
            ['provider1'],
            ['provider1'],
        ];
    }
 
    
    /**
     * @dataProvider additionProvider
     * @param unknown $a
     * @param unknown $b
     * @param unknown $expected
     */
    public function testAdd($a,$b,$expected){
        $this->assertEquals($expected, $a + $b);
    }
    
    public function additionProvider(){
//         return new CsvFileIterator();
        return [
            [0,0,0],
            [0,1,1],
            [1,1,2],
        ];
    }
    
    

    /**
     * A basic functional test example.
     *
     * @return void
     */
    public function testBasicExample()
    {
        $actual = 'qw';
        $this->assertEquals('qw', $actual);
        // $this->visit('/test')
        // ->see('-1');
        return 'one';
    }

    /**
     * @depends testBasicExample
     */
    public function testOne()
    {
        $this->assertEquals([
            'one'
        ], func_get_args());
        return 'two';
    }

    /**
     * @depends testOne
     */
    public function testTwo()
    {
        $this->assertEquals([
            'two'
        ], func_get_args());
        return 'three';
    }

    /**
     * @depends testOne
     * @depends testTwo
     */
    public function testThree()
    {
        $this->assertEquals([
            'two',
            'three'
        ], func_get_args());
    }
    
    
    /**
     * @depends testBasicExample
     * @depends testOne
     * @dataProvider dtProvider
     */
    public function testMuti(){
    
        $this->assertEquals([
            'provider1',
            'one',
            'two',
        ], func_get_args());
    
    }
    
}
