<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Activity;

class ClearCalendarData extends Command
{
    protected $signature = 'calendar:clear';
    protected $description = 'Clear all activities from the calendar';

    public function handle()
    {
        $count = Activity::count();
        
        if ($count === 0) {
            $this->info('Calendar is already empty.');
            return 0;
        }

        Activity::truncate();
        $this->info("Successfully cleared {$count} activities from the calendar.");
        return 0;
    }
}
