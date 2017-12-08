<?php

namespace Asycle\Core\Queue\Handler;
use Asycle\Core\Job;
use Asycle\Core\Queue\AsyncHandlerInterface;

/**
 * Date: 2017/9/5
 * Time: 20:41
 */
class RedisAsyncHandler implements AsyncHandlerInterface {
    protected $redis = null;
    protected $queueKey = '';
    protected $backupQueueKey = '';
    public function __construct(\Redis $redis,string $queueKey)
    {
        if (empty($queueKey)) {
            throw  new \InvalidArgumentException('队列名称不能为空');
        }
        $this->redis = $redis;
        //集群中使用hash tag :{} ，避免两条队列分散到不同的slot
        $this->queueKey = '{'.$queueKey.'}';
        $this->backupQueueKey = '{'.$queueKey . '}backup';
    }
    /**
     * 添加任务到队列中
     * @param Job $job
     * @return int
     */
    public function push(Job $job): int
    {
        if(is_null($this->redis)){
            return false;
        }
        $res = $this->redis->lPush($this->queueKey,$job);
        return intval($res);
    }

    /**
     * 获取一个可执行的任务
     * @return mixed
     */
    public function pop()
    {
        if(is_null($this->redis)){
            return false;
        }
        return $this->redis->rpoplpush($this->queueKey,$this->backupQueueKey);
    }

    /**
     * 删除已处理备份的任务
     * @param Job $job
     * @return bool
     */
    public function removeReserved(Job $job)
    {
        if(is_null($this->redis)){
            return false;
        }
        return $this->redis->lPop($this->backupQueueKey);
    }

    /**
     * 清空队列
     * @return bool
     */
    public function flush(): bool
    {
        if(is_null($this->redis)){
            return false;
        }
        $this->redis->delete($this->queueKey,$this->backupQueueKey);
        return true;
    }

    /**
     * 已处理保留的任务数量
     * @return int
     */
    public function reservedCount(): int
    {
        if(is_null($this->redis)){
            return 0;
        }
        $res = $this->redis->lLen($this->backupQueueKey);
        return  intval($res);
    }

    /**
     * 等待处理任务数量
     * @return int
     */
    public function count(): int
    {
        if(is_null($this->redis)){
            return 0;
        }
        $res = $this->redis->lLen($this->queueKey);
        return  intval($res);
    }
}