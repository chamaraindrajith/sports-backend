<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\SetNewsController;

class setNews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setnews:cron';

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
        $date = date_format(now(), 'Y-m-d');
        $date_plus1 = date_format(now(), 'Y-m-d')+1;

        $SetNewsController = new SetNewsController;

        $SetNewsController->getNewsByDate("cricket", $date);
        $SetNewsController->getNewsByDate("soccer", $date);
        $SetNewsController->getNewsByDate("basketball", $date);

        $SetNewsController->getNewsByDate("cricket", $date_plus1);
        $SetNewsController->getNewsByDate("soccer", $date_plus1);
        $SetNewsController->getNewsByDate("basketball", $date_plus1);

        Log::info("Cron is working fine! setnews " . $date . " - " . $date_plus1);
        return 0;
    }
}
