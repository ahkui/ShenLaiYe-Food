<?php

namespace App\Console\Commands;

use App\Jobs\FetchNextPage;
use Illuminate\Console\Command;

class FetchLocations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'location:fetch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'get location from feng chia univercity';

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
        FetchNextPage::dispatch('24.178829,120.646438', 9999, 'food')->delay(now()->addSecond(5));
        FetchNextPage::dispatch('25.047884, 121.520166', 9999, 'food')->delay(now()->addSecond(5));
    }
}
