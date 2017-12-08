<?php

namespace App\Models\Sample;

use Asycle\Core\Database\Model;

/**
 * Date: 2017/11/29
 * Time: 19:28
 */
class Users extends Model{
    protected $table = 'users';
    protected $connection = 'test';
}