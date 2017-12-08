<?php
use Asycle\Core\Http\Request;
use Asycle\Core\TestCase;

/**
 * User: Administrator
 * Date: 2017/9/12
 * Time: 17:58
 */
class RequestTest extends TestCase{
    public function testInputInt(){
        $_GET['number'] = 1;
        $this->assertEquals(1,Request::input('number',0));
    }
}