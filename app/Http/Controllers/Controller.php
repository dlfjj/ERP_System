<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Redirect;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function respondWithErrors(string $url, array $messages = [])
    {
        if(empty($messages)){
            $messages = ['Unknow Error, Please tyr again later.'];
        }

        return Redirect::to($url)
            ->with('flash_error','Operation failed')
            ->withErrors($messages)
            ->withInput();
    }
}
