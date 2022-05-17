<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;

class pullMarchData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'marc oracle data inport';

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
       
    }
}
