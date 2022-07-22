<?php

namespace App\Console\Commands;

use App\Activity;
use App\RateActivity;
use App\VariableReport;
use Carbon\Carbon;
use App\Services\GoogleSheet;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RateActivityReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sycn:rateActivityReport';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reporte de calificaciones para las actividades';

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
    public function handle( GoogleSheet $googleSheet )
    {
        $eventId = 101;

        $variable = VariableReport::query()
            ->where('name', 'lastRateActivityIDSync')
            ->first();
                
        $rowsActivity = RateActivity::with([
            "event:id,name",
            "activity:id,name",
            "user:id,name,lastname,email"
            ])
            ->where('event_id', $eventId)
            ->where('id', '>', $variable->value)
            ->orderBy('id')
            ->limit(100)
            ->get(); 
        
        //Log::info("RateActivity...");
        //Log::info($rowsActivity);

        if( $rowsActivity->count() === 0 ){
            return  true;
        }

        $finalData = collect();
        $lastId = 0;

        foreach ($rowsActivity as $row){  
            $fullName = $row->user->name . ' ' . $row->user->lastname;
            $dateRegister = new Carbon($row->created_at, 'America/Bogota');

            $finalData->push([
                $row->id,
                $row->rate,
                $dateRegister->toDateTimeString(),
                $row->user->id,        
                $fullName,
                $row->user->email,
                $row->activity->id,
                $row->activity->name,
                $row->event->id,
                $row->event->name,
            ]);

            $lastId = $row->id;
        } 
               
        $googleSheet->saveDataToSheet(
            $finalData->toArray(),
            '1k6N3SqEsfJe73JGRjD_j8610AxOZK3LVk7oKI-JRcvI',
            'rate_activity',
        );

        $variable->value = $lastId;
        $variable->save();

        return true; 
    }
}
