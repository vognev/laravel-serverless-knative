<?php

namespace Laravel\Serverless\Knative\Console;

use Illuminate\Console\Command;
use Laravel\Serverless\Knative\Helper;
use Illuminate\Support\Str;

class RuntimeCommand extends Command
{
    protected $name = 'serverless:runtime';

    protected $description = 'Builds an Pushing your application runtime image';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->call('serverless:runtime:build');
        $this->call('serverless:runtime:push');
    }
}
