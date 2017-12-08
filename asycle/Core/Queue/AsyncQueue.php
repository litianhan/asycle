<?php

namespace Asycle\Core\Queue;

use Asycle\Core\Job;


/**
 * Date: 2017/8/30
 * Time: 14:29
 */
class AsyncQueue
{
    protected $handler = null;
    public function __construct()
    {
    }

    public function setHandler(AsyncHandlerInterface $handler)
    {
        $this->handler = $handler;
    }

    /**
     * 添加任务到队列中
     * @param Job $job
     * @return int
     */
    public function push(Job $job): int
    {
        if ($this->handler instanceof AsyncHandlerInterface) {
            return $this->handler->push( $job);
        }
        return false;
    }

    /**
     * 获取一个可执行的任务
     * @return mixed
     */
    public function pop()
    {
        if ($this->handler instanceof AsyncHandlerInterface) {
            return $this->handler->pop();
        }
        return false;
    }

    /**
     * 删除已处理备份的任务
     * @param Job $job
     * @return bool
     */
    public function removeReserved(Job $job){
        if ($this->handler instanceof AsyncHandlerInterface) {
            return $this->handler->removeReserved($job);
        }
        return false;
    }

    /**
     * 清空队列
     * @return bool
     */
    public function flush(): bool
    {
        if ($this->handler instanceof AsyncHandlerInterface) {
            return $this->handler->flush();
        }
        return false;
    }
    /**
     * 已处理保留的任务数量
     * @return int
     */
    public function reservedCount(): int
    {
        if ($this->handler instanceof AsyncHandlerInterface) {
            return $this->handler->reservedCount();
        }
        return 0;
    }

    /**
     * 任务数量
     * @return int
     */
    public function count(): int
    {
        if ($this->handler instanceof AsyncHandlerInterface) {
            return $this->handler->count();
        }
        return 0;

    }
}