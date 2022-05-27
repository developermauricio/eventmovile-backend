<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\NotificationNew;
use Illuminate\Console\Command;
use \PhpMqtt\Client\Facades\MQTT;

class ScheduleNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schedule:notification_news';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Este comando permite enviar las notificaciones del evento programadas';

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
        $date = Carbon::now();
        $notification = NotificationNew::where('end_time', '<=', $date->format('Y-m-d H:i:s'))->where('send', 1)->first();

        if($notification){
            $notification->send = 2;
            $notification->save();
            MQTT::publish('notification_news_event', 'Nueva Notificación');
        }
    }
}
