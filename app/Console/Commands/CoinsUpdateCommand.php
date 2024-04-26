<?php

namespace App\Console\Commands;

use App\Modules\Coin\Jobs\UpdateCoinsJob;
use Illuminate\Console\Command;

use function dispatch_sync;

class CoinsUpdateCommand extends Command
{
    protected $signature = 'app:coins-update-command';

    protected $description = 'This command updates coins by an api call.';

    public function handle(): int
    {
        $updateCoinsJob = dispatch_sync(new UpdateCoinsJob());

        if ($updateCoinsJob) {
            return Command::SUCCESS;
        }

        return Command::FAILURE;
    }
}
