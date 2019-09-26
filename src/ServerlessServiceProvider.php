<?php

namespace Laravel\Serverless\Knative;

use Illuminate\Support\ServiceProvider;

class ServerlessServiceProvider extends ServiceProvider
{
    public function boot()
    {

    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/serverless.php', 'serverless'
        );

        $this->commands([
            Console\InstallCommand::class,
            Console\RuntimeCommand::class,
            Console\Runtime\BuildCommand::class,
            Console\Runtime\PushCommand::class,
            Console\ManifestCommand::class,
        ]);
    }

    public function provides()
    {
        return [

        ];
    }
}
