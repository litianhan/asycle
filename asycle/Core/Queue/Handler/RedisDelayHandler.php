<?php

namespace Asycle\Core\Queue\Handler;
use Asycle\Core\Job;
use Asycle\Core\Queue\DelayHandlerInterface;

/**
 * Date: 2017/9/5
 * Time: 20:41
 */
class RedisDelayHandler implements DelayHandlerInterface {
    protected $redis = null;
    protected $queueKey = '';
    protected $backupQueueKey = '';
    public function __construct(\Redis $redis,string $queueKey)
    {
        if (empty($queueKey)) {
            throw  new \InvalidArgumentException('队列名称不能为空');
        }
        $this->redis = $redis;
        //集群中使用hash tag 避免两条队列分散到不同的slot
        $this->queueKey = '{'.$queueKey.'}';
        $this->backupQueueKey = '{'.$queueKey . '}backup';
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