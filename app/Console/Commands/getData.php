<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\GetDataController;

class getData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:cron';

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
        $date_min2 = date_format(now(), 'Ymd')-2;
        $date_min1 = date_format(now(), 'Ymd')-1;
        $date = date_format(now(), 'Ymd');
        $date_plus1 = date_format(now(), 'Ymd')+1;
        $date_plus2 = date_format(now(), 'Ymd')+2;

        $GetDataController = new GetDataController;

        $GetDataController->getDataByDate("cricket", $date);
        $GetDataController->getDataByDate("soccer", $date);
        $GetDataController->getDataByDate("basketball", $date);

        Log::info("Cron is working fine! Set " . $date);
        return 0;
    }
}
