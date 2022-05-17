<?php


namespace App\Console\Commands;

use Carbon\Carbon;
use DB,File;
use Illuminate\Console\Command;

class Reservation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reservation:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reservation data download completed';

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
        //
    }
}
