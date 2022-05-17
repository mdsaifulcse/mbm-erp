<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;

class pullstyle extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'style:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Oracle style import completed';

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
