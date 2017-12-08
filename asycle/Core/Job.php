<?php

namespace Asycle\Core;
/**
 * Date: 2017/11/8
 * Time: 21:00
 */
abstract class Job {
    /**
     * 作业参数
     * @var array
     */
    protected $params = [];
    /**
     * id
     * @var array
     */
    protected $id = '';
    public function __construct($params = [],$id = '')
    {
        $this->params = $params;
        $this->id = $id;
    }

    /**
     * 处理作业逻辑
     */
    abstract public function handle();

    /**
     * 获取作业的参数
     * @return array
     */
    public function params(){
        return $this->params;
    }

    /**
     * 设置作业参数
     * @param $params
     * @return $this
     */
    public function setParams($params){
        $this->params = $params;
        return $this;
    }

    /**
     * 返回uuid
     * @return array|string
     */
    public function getId(){
        return $this->id;
    }

    /**
     * 设置id
     * @param $id
     * @return $this
     */
    public function setId($id){
        $this->id = $id;
        return $this;
    }
}