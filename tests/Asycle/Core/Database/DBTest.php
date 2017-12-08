<?php
/**
 * User: tenhanlee
 * Date: 2017/9/16
 * Time: 下午1:50
 */
class DBTest extends \Asycle\Core\TestCase{
    public function testCanQuery(){
        $db = \Asycle\Core\Connection::db('test');
        $res = $db->query('SELECT now();');
        $this->assertNotEquals(false,$res);
    }
}