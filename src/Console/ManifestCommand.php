<?php

namespace Laravel\Serverless\Knative\Console;

use Illuminate\Console\Command;
use Laravel\Serverless\Knative\Helper;
use Illuminate\Support\Str;

class ManifestCommand extends Command
{
    protected $name = 'serverless:manifest';

    protected $description = 'Deploys your application to knative';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $config         = config('serverless');
        $templatePath   = Helper::package_path('templates', 'service.yml.blade.php');

        $template       = view()->file(
            $templatePath, array_merge($config, [
                'service' => Str::slug(config('app.name', 'laravel'), '-')
            ])
        )->render();

        $this->getOutput()->writeln($template);
    }
}
