<?php
namespace App\Controllers\Sample;
use App\Controllers\BaseController;
use Asycle\Core\Http\Response;

/**
 * Date: 2017/11/16
 * Time: 16:32
 */
class Welcome extends BaseController {

    public function index()
    {
       return Response::view('welcome');
    }
}