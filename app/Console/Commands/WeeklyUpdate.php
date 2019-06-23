<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class WeeklyUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'weekly:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check if the employee is under their permitted daily hours at the end of the week';

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
     * @return mixed
     */
    public function handle()
    {
        $userUnder = User::whereNotNull('latest_flexi_balance')->where('latest_flexi_balance', '<', DB::raw('-' . 'daily_hours_permitted'))->get();
        foreach ($userUnder as $singleUser) {
            //TODO - Email user and manager to notify that the flexible balance has dropped below the daily permitted balance at the end of the week.
        }
    }
}
