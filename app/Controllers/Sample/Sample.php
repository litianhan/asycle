<?php
namespace App\Controllers\Sample;
use App\Controllers\BaseController;
use Asycle\Core\Http\Response;
use Asycle\Core\Connection;
use Asycle\Core\Filter;
use Asycle\Core\Session\Handler\FileHandler;
use Asycle\Core\Session\Session;

/**
 * Date: 2017/11/16
 * Time: 16:33
 */
class Sample extends BaseController
{
    /**
     * restful get
     */
    public function get(){
        echo __METHOD__;
    }
    /**
     * 示例
     * @return bool
     */
    public function template()
    {
        $data = [
            'title'=> '使用样例',
            'option1'=>true,
            'switch'=>1,
            'while'=>5,
            'strings'=>[
                'string1','string2'
            ]
        ];
        return Response::view('sample',$data);
    }
    public function validate(){

        $_GET = [
            'id'=>100,
            'username'=>'asycle'
        ];
        $filter = new Filter($_GET);
        $filter->requiredKey('id')->isInteger(1);
        $filter->requiredKey('username')->isUsername(6,32);
        if($filter->hasError()){
            die('参数错误');
        }

        $params = $filter->fetchAll();
        var_dump($params);
    }
    public function mysql(){

        $db = Connection::db('test');
        $records = $db->table('users')->get();
        var_dump($records);
    }

    public function redis(){

        $redis= Connection::redis('default');
        $redis->set('key','asycle');
        $value = $redis->get('key');
        var_dump($value);
    }
    public function mongodb(){

    }
    public function session(){

        Session::setHandler(new FileHandler());
        Session::start();
    }

    public function cache(){

    }

}