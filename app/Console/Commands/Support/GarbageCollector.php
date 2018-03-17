<?php

namespace App\Console\Commands\Support;

use App\Services\AbstractSupport;

use App\Console\Commands\AbstractCommand;

class GarbageCollector extends AbstractCommand
{
    const TARGET_ERROR_LOG = 'logs.error';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'support:garbage-collector {target} {--until=} {--dry-run}';

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
     * @var \Carbon\Carbon
     */
    protected $untilDt;

    /**
     *
     */
    private function cleanErroLog()
    {
        if ( ! $this->untilDt) {
            $this->err('You must specify a date until that all entries will be deleted');
        }

        $entries = ErrorEntry::selectUntil($this->untilDt);

        if ( ! $this->dryRunFlag) {
            foreach ($entries as $entry) {
                $entry->delete();
            }
        }

        $this->inf('%d of error log entries have been deleted' .  ( $this->dryRunFlag ? ' (DRY RUN)'  : '' ),
            $entries->count());
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->dryRunFlag = $this->option('dry-run');
        $this->untilDt = $this->parseDateTimeOptionValue('until');

        $target = $this->argument('target');

        switch ($target) {
            case self::TARGET_ERROR_LOG:
                $this->cleanErroLog();
                break;
        }
    }
}

