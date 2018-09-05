<?php
/**
 * Created by PhpStorm.
 * User: tahirawan
 * Date: 11/08/2018
 * Time: 22:34
 */

namespace App\Console\Commands;

use Illuminate\Contracts\Session\Session;
use Illuminate\Console\Command;

class TestCommand extends Command
{

    protected $signature = 'laravel:test';

    public function handle(Session $session)
    {
        $key = 'this-test';
        $id = null;
        //$session->put($key, 'my-value-123');
        $id = $session->get($key);
        dd($id);
        dd('testing...');
    }

}