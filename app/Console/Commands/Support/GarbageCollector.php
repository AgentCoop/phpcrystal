<?php

namespace App\Console\Commands\Support;

use Illuminate\Console\Command;


class GarbageCollector extends Command
{
    const TARGET_ERROR_LOG = 'error-log';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'support:garbage-collector {target} {--dry-run}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    /**
     * @var bool
     */
    protected $dryRunFlag;

    /**
     *
     */
    private function cleanErroLog()
    {

    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->dryRunFlag = $this->option('dry-run');
        $target = $this->argument('target');

        switch ($target) {
            case self::TARGET_ERROR_LOG:
                $this->cleanErroLog();
                break;
        }
    }
}
