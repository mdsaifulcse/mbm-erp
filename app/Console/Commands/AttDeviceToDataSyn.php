<?php

namespace App\Console\Commands;

use App\Repository\Hr\AttendacneSynRepository;
use Illuminate\Console\Command;

class AttDeviceToDataSyn extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:device_syn';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Attendance syn from other device';

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
    public function handle(AttendacneSynRepository $attSyn)
    {
        return $attSyn->deviceToDataSyn();
    }
}
