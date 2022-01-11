<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use App\Events\NotificationMeetingEvent;

class VerifyMeetings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'verify-meetings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verify if there are active meetings';

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
        $date = new \DateTime();
        $dateStandar = $date->format('Y-m-d');
        $dateTime = $date->format('H:i:s');
        
        $date->add(new \DateInterval('PT1M'));
        $newDate = $date->format('H:i:s');
                
        $res = DB::table('meetings')
        ->where('meetings.state',2)
        ->whereDate('meetings.start',$dateStandar)
        ->whereTime('meetings.start', '>', $dateTime)
        ->whereTime('meetings.start', '<', $newDate)
        ->get();
        //dd($res);
        //dd($dateTime.' - '.$newDate);
        foreach($res as $data){
            $guess = DB::table('meeting_rel_users')
            ->select('*')
            ->where('meeting_id',$data->id)
            ->first();

            event(new NotificationMeetingEvent($guess->user_id,$data));
            event(new NotificationMeetingEvent($data->creator_id,$data));
            
        }

        return 1;
    }
}
