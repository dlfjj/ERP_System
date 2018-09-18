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

    public function redirectWithErrors(string $url, array $messages = [], array $with = [])
    {
        if(empty($with)) {
            $with = ['flash_error','Operation failed'];
        }

        if(empty($messages)) {
            $messages = ['Un-know Error, Please try again later.'];
        }

        return Redirect::to($url)
            ->with($with)
            ->withErrors($messages)
            ->withInput();
    }

    public function redirectWithSuccessMessage(string $url)
    {
        return Redirect::to($url)
            ->with('flash_success','Operation succeeded');
    }
}
