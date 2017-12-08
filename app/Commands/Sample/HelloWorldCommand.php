<?php
namespace App\Commands\Sample;

use Asycle\Core\Command;

/**
 * Date: 2017/11/25
 * Time: 15:17
 */
class HelloWorldCommand extends Command{

    public function handle()
    {
        echo 'Hello world! Command!';
    }
}