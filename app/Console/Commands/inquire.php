<?php

namespace App\Console\Commands;

use App\Repositories\Requests\Repo;
use App\Services\Requests\ImageGenerating\Service;
use Illuminate\Console\Command;

class inquire extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:inquire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $list = app(Repo::class)->getReadyRecords();
        foreach ($list as $item) {
            app(Service::class)->generate($item);
        }
    }
}
