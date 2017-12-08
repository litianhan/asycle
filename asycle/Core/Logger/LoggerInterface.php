<?php

namespace Asycle\Core\Logger;

interface LoggerInterface
{
    const EMERGENCY = 'emergency';
    const ALERT = 'alert';
    const CRITICAL = 'critical';
    const ERROR = 'error';
    const WARNING = 'warning';
    const NOTICE = 'notice';
    const INFO = 'info';
    const DEBUG = 'debug';
    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @return null
     */
    public function log($level, $message);
}