<?php
namespace App\Console;

use Illuminate\Console\Command;

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
}
