<?php

namespace Asycle\Core\Queue;

use Asycle\Core\Job;


/**
 * Date: 2017/8/30
 * Time: 14:29
 */
class DelayQueue
{
    protected $handler = null;

    public function __construct()
    {
    }

    public function setHandler(DelayHandlerInterface $handler)
    {

        $this->handler = $handler;

    }

    /**
     * 添加任务到队列中
     * @param Job $job
     * @param int $delaySeconds
     * @return bool
     */
    public function push(Job $job, $delaySeconds = 0): bool
    {
        if ($this->handler instanceof DelayHandlerInterface) {
            return $this->handler->push($job, $delaySeconds);
        }
        return false;
    }

    /**
     * 获取一个可执行的任务
     * @param int $timeToAck
     * @return mixed
     */
    public function pop($timeToAck = 0)
    {
        if ($this->handler instanceof DelayHandlerInterface) {
            return $this->handler->pop($timeToAck);
        }
        return false;
    }

    /**
     * 应答任务处理成功
     * @param Job $job
     * @return mixed
     */
    public function ack(Job $job)
    {
        if ($this->handler instanceof DelayHandlerInterface) {
            return $this->handler->ack($job);
        }
        return false;
    }

    /**
     * 清空队列
     * @return bool
     */
    public function flush(): bool
    {
        if ($this->handler instanceof DelayHandlerInterface) {
            return $this->handler->flush();
        }
        return false;
    }
    /**
     * 准备就绪的任务数量
     * @return int
     */
    public function readyCount(): int
    {
        if ($this->handler instanceof DelayHandlerInterface) {
            return $this->handler->readyCount();
        }
        return 0;

    }

    /**
     * 未应答的任务数量
     * @return int
     */
    public function notAckCount(): int
    {
        if ($this->handler instanceof DelayHandlerInterface) {
            return $this->handler->notAckCount();
        }
        return 0;

    }

    /**
     * 执行时间未到，等待中的任务数量
     * @return int
     */
    public function waitCount(): int
    {
        if ($this->handler instanceof DelayHandlerInterface) {
            return $this->handler->waitCount();
        }
        return 0;
    }
}