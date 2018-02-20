<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;

use Carbon\Carbon;

abstract class AbstractCommand extends Command
{
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
    protected function parseTimeOption($name)
    {
        $now = Carbon::parse();

        switch ($this->option($name)) {
            case 'last-day':
                return $now->subDay(1);

            case 'last-week':
                return $now->subWeek(1);

            case 'last-month':
                return $now->subMonth(1);

            case 'last-year':
                return $now->subYear(1);

            default:
                return null;
        }
    }
}
