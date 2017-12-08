<?php
namespace App\Jobs\Sample;
use Asycle\Core\Job;

/**
 * Date: 2017/11/25
 * Time: 10:23
 */
class SampleJob extends Job{

    /**
     * 处理作业逻辑
     */
    public function handle()
    {
       // $params = $this->getParams();
        echo 'Hello world! job!';
    }
}