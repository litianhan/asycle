<?php
/**
 * Date: 2017/12/8
 * Time: 15:05
 */
class ExecFunc extends \Asycle\Core\Job{

    /**
     * 处理作业逻辑
     */
    public function handle()
    {
        $params = $this->params(); // 队列执行所需参数
        call_user_func_array($params[0] ?? '',$params[1] ?? []);
    }
}