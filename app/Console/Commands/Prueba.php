<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Traits\HelperApp;
use Illuminate\Support\Facades\Log;

class Prueba extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:prueba';
    use HelperApp;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // $this->verifyYourInvitation('hola','hola','hola');
        // $this->verifyActivityIfFree(71, '');
        Log::info('Hola mundo desde');
    }
}
