<?php

namespace App\Http\Controllers\Api\Commands;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class ExecuteCommand extends Controller
{
    public function VerifyTransactionPayu()
    {
        try {
            Artisan::call('TRANSACTION:PAYU');
            return ['message' => 'Execute command'];
        } catch (\Throwable $th) {
            log::info('Error execute command:'.$th);
            return ['message' => 'Error execute command'];
        }
    }
}
