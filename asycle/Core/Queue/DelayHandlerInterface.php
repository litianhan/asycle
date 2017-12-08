<?php

namespace Asycle\Core\Queue;

use Asycle\Core\Job;

/**
 * Date: 2017/8/30
 * Time: 13:58
 */
interface DelayHandlerInterface
{
    /**
     * 添加任务到队列中
     * @param Job $job
     * @param int $delaySeconds
     * @return bool
     */
    public function push(Job $job, $delaySeconds = 0): bool;

    /**
     * 获取一个可执行的任务
     * @param int $timeToAck
     * @return mixed
     */
    public function pop($timeToAck = 0);

    /**
     * 应答任务处理成功
     * @param Job $job
     * @return mixed
     */
    public function ack(Job $job);

    /**
     * 清空队列
     * @return bool
     */
    public function flush(): bool;

    /**
     * 准备就绪的任务数量
     * @return int
     */
    public function readyCount(): int;

    /**
     * 未应答的任务数量
     * @return int
     */
    public function notAckCount(): int;

    /**
     * 执行时间未到，等待中的任务数量
     * @return int
     */
    public function waitCount(): int;
}