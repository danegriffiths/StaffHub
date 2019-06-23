<?php

namespace App\Console\Commands;

use App\Balance;
use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MonthlyUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'monthly:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check if the employee is over their permitted weekly hours at the end of the month';

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
        $userOver = User::whereNotNull('latest_flexi_balance')->where('latest_flexi_balance', '>', DB::raw('flexi_balance'))->get();
        foreach ($userOver as $singleUser) {
            //TODO - Email user and manager to notify that the maximum flexible balance has breached at the end of the month.
        }

    }
}
