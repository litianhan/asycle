<?php

namespace Asycle\Core\Queue;

use Asycle\Core\Job;

/**
 * Date: 2017/8/30
 * Time: 13:58
 */
interface AsyncHandlerInterface
{
    /**
     * 添加任务到队列中
     * @param Job $job
     * @return int
     */
    public function push(Job $job): int;

    /**
     * 获取一个可执行的任务
     * @return mixed
     */
    public function pop();

    /**
     * 删除已处理备份的任务
     * @param Job $job
     * @return bool
     */
    public function removeReserved(Job $job);


    /**
     * 清空队列
     * @return bool
     */
    public function flush(): bool;


    /**
     * 已处理保留的任务数量
     * @return int
     */
    public function reservedCount(): int;

    /**
     * 等待处理任务数量
     * @return int
     */
    public function count(): int;
}