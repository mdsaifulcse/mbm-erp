<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use DB,File;
use Illuminate\Console\Command;
// use Rap2hpoutre\FastExcel\FastExcel;

class SampleRequestionExport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'samplerequestion:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Hourly sample requestion check ';

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
        //
        

    }
}
