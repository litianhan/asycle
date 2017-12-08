<?php

namespace Asycle\Core\Queue\Handler;
use Asycle\Core\Database\MongoHelper;
use Asycle\Core\Job;
use Asycle\Core\Queue\DelayHandlerInterface;

/**
 * Date: 2017/9/5
 * Time: 20:41
 */
class MongodbDelayHandler implements DelayHandlerInterface {

    protected $mongoHelper = null;
    protected $namespace = '';
    protected $silent = false;
    protected $timeout = 3;
    public function __construct(MongoHelper $mongoHelper,$namespace,$timeout = 3)
    {
        $this->mongoHelper = $mongoHelper;
        $this->namespace = $namespace;
        $this->timeout = $timeout;
    }

    /**
     * 添加任务到队列中
     * @param Job $job
     * @param int $delaySeconds
     * @return bool
     */
    public function push(Job $job, $delaySeconds = 0): bool
    {
        // TODO: Implement push() method.
    }

    /**
     * 获取一个可执行的任务
     * @param int $timeToAck
     * @return mixed
     */
    public function pop($timeToAck = 0)
    {
        // TODO: Implement pop() method.
    }

    /**
     * 应答任务处理成功
     * @param Job $job
     * @return mixed
     */
    public function ack(Job $job)
    {
        // TODO: Implement ack() method.
    }

    /**
     * 清空队列
     * @return bool
     */
    public function flush(): bool
    {
        // TODO: Implement flush() method.
    }

    /**
     * 准备就绪的任务数量
     * @return int
     */
    public function readyCount(): int
    {
        // TODO: Implement readyCount() method.
    }

    /**
     * 未应答的任务数量
     * @return int
     */
    public function notAckCount(): int
    {
        // TODO: Implement notAckCount() method.
    }

    /**
     * 执行时间未到，等待中的任务数量
     * @return int
     */
    public function waitCount(): int
    {
        // TODO: Implement waitCount() method.
    }
}