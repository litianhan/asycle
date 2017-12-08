<?php

namespace Asycle\Core;

/**
 * Date: 2017/11/25
 * Time: 15:16
 */
abstract class Command{
    protected $arguments = [];
    protected $options = [];
    public function __construct($arguments = [],$options = [])
    {
        $this->arguments = $arguments;
        $this->options= $options;
    }

    /**
     * 返回所有选项
     * @return array
     */
    public function getOptions(){
        return $this->options;
    }
    /**
     * 获取命令行选项
     * @param $key
     * @param null $default
     * @return mixed|null
     */
    public function option($key,$default = null){
        return $this->options[ltrim($key,'-')] ?? $default;
    }

    /**
     * 获取命令行参数
     * @param $key
     * @param null $default
     * @return mixed|null
     */
    public function argument($key,$default = null){
        return $this->arguments[$key] ?? $default;
    }

    /**
     * 返回所有参数
     * @return array
     */
    public function getArguments(){
        return $this->arguments;
    }
    /**
     * 处理命令行逻辑
     */
    abstract public function handle();
}