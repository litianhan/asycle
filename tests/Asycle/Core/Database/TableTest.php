<?php

use Asycle\Core\TestCase;

/**
 * User: tenhanlee
 * Date: 2017/9/16
 * Time: 下午1:52
 */
class TableTest extends TestCase{

    public function testInsertAndGetId(){
        $db = \Asycle\Core\Connection::db('test');
        $res = $db->table('users')->insertOneAndGetId([
            'username'=>uniqid(),
            'password'=>sha1(123456),
        ]);
        $this->assertGreaterThan(0,$res);

    }
    public function testInsertBatch(){
        $db = \Asycle\Core\Connection::db('test');
        $res = $db->table('users')->insertBatch([
            [
                'username'=>uniqid(),
                'password'=>sha1(123456),
            ],[
                'username'=>uniqid(),
                'password'=>sha1(123456),
            ]
            ]);
        $this->assertEquals(2,$res);
    }
    public function testGet(){
        $db = \Asycle\Core\Connection::db('test');
        $res = $db->table('users')->get();
        $this->assertArrayHasKey(0,$res);
    }
    public function testWhere(){
        $db = \Asycle\Core\Connection::db('test');
        $res = $db->table('users')
            ->where('id','>',0)
            ->get();
        $this->assertArrayHasKey(0,$res);
    }
    public function testLeftJoin(){
        $db = \Asycle\Core\Connection::db('test');
        $res = $db->table('users')
            ->asTable('a')
            ->leftJoinAs('users_email','b')
            ->on('a.id','b.uid')
            ->get();
        $this->assertArrayHasKey(0,$res);
    }
    public function testUnion(){
        $db = \Asycle\Core\Connection::db('test');
        $res = $db->table('users')
            ->selectAndUnionAll(['id'],'users_email')
            ->get(['id']);
        $this->assertArrayHasKey(0,$res);
    }
}