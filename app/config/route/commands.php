<?php
use \Asycle\Core\Console;
/**
 * 命令行配置
 */
Console::defaultCommandNamespace('App\Commands');

//执行：php $APP_PATH_BASE/command sample
Console::command('sample','Sample\HelloWorldCommand',[]);