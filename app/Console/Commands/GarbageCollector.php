<?php

namespace App\Console\Commands;

use App\Models\Physical\DAL\AbstractMongoDb;

use App\Services\AbstractSupport;

class GarbageCollector extends AbstractCommand
{
    const TARGET_ERROR_LOG = 'error-log';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'support:garbage-collector {target} {--before=} {--dry-run}';

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
    protected $beforeDt;

    /**
     *
     */
    private function cleanErroLog()
    {
        if ( ! $this->beforeDt) {
            $this->err('You must specify a date before that all entries will be deleted');
        }

        $query = AbstractSupport::logEntryFactory()->newQuery();

        $query->where(AbstractMongoDb::CREATED_AT, '<', $this->beforeDt);
        $entries = $query->get();

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
        $this->beforeDt = $this->parseTimeOption('before');

        $target = $this->argument('target');

        switch ($target) {
            case self::TARGET_ERROR_LOG:
                $this->cleanErroLog();
                break;
        }
    }
}
