<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Cron extends Model
{
    //
    protected $primaryKey = 'command';
    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = ['command', 'next_run', 'last_run'];
    
    
    public static function shouldIRun($command, $weeks) {
        // look up the command we wish to run in the database
        $cron = Cron::find($command);
        // grab the current time
        $now  = Carbon::now();
        // compare the next scheduled time for the command to be run
        if ($cron && $cron->next_run > $now->timestamp) {
            // return false if we're not due to run it yet - we'll be running this check a lot more often than actually processing it, so typically this should return false.
            return false;
        }
        
        // if this is the first time, then create the entry in the db, otherwise update the existing entry
        Cron::updateOrCreate(
            ['command'  => $command],
            // the example uses minutes, to advance it one hour, but I wish to advance it weekly, so lets change the $minutes into weeks.
            // ['next_run' => Carbon::now()->addMinutes($minutes)->timestamp,
            
            // when the function is called we will be passing 1 week as the $weeks value.
            ['next_run' => Carbon::now()->addWeeks($weeks)->timestamp,
             'last_run' => Carbon::now()->timestamp]
        );
        return true;
    }
}
