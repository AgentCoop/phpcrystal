<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;

use Carbon\Carbon;

abstract class AbstractCommand extends Command
{
    const DATETIME_LABEL_START_OF_DAY = 'start-of-day';
    const DATETIME_LABEL_START_OF_WEEK = 'start-of-week';
    const DATETIME_LABEL_START_OF_MONTH = 'start-of-month';
    const DATETIME_LABEL_START_OF_YEAR = 'start-of-year';

    /**
     * @return void
     */
    protected function err(...$args)
    {
        $errorMsg = sprintf(...$args);

        $this->output->write('<error>' . $errorMsg . '</error>', true);

        exit -1;
    }

    /**
     * @return void
    */
    protected function ln(...$args)
    {
        $this->line(sprintf(...$args));
    }

    /**
     * @return void
    */
    protected function inf(...$args)
    {
        $this->info(sprintf(...$args));
    }

    /**
     *
     */
    protected function parseDateTimeOptionValue($name)
    {
        $now = Carbon::parse();

        switch ($this->option($name)) {
            case self::DATETIME_LABEL_START_OF_DAY:
                return $now->startOfDay();

            case self::DATETIME_LABEL_START_OF_WEEK:
                return $now->startOfWeek();

            case self::DATETIME_LABEL_START_OF_MONTH:
                return $now->startOfMonth();

            case self::DATETIME_LABEL_START_OF_YEAR:
                return $now->startOfYear();

            default:
                return null;
        }
    }
}
