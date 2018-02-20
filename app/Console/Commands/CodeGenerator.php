<?php

namespace App\Console\Commands;

class CodeGenerator extends AbstractCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'support:code-generator';

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
    private function routes()
    {

    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

    }
}
