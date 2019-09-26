<?php

namespace Laravel\Serverless\Knative\Console\Runtime;

use Docker\API\Model\PushImageInfo;
use Illuminate\Console\Command;
use Docker\Docker;

class PushCommand extends Command
{
    protected $name = 'serverless:runtime:push';

    protected $description = 'Pushes your application docker image runtime';

    /** @var Docker */
    protected $docker = null;

    public function __construct()
    {
        $this->docker = Docker::create();

        parent::__construct();
    }

    public function handle()
    {
        $this->pushRuntimeDockerImage(
            config('serverless.image'),
            config('serverless.auth')
        );
    }

    private function pushRuntimeDockerImage(string $imageName, string $registryAuth)
    {
        $this->info('Pushing runtime docker image');

        $pushStream = $this->docker->imagePush($imageName, [], [
            'X-Registry-Auth' => $registryAuth
        ]);

        $progressBar = $this->getOutput()->createProgressBar(1.0);
        $pushStream->onFrame(function (PushImageInfo $pushInfo) use ($progressBar) {
            $status = $pushInfo->getStatus();
            $error  = $pushInfo->getError();

            if ($status) {
                switch ($status) {
                    case 'Pushing':
                        $progressBar->setProgress(
                            $pushInfo->getProgressDetail()->getCurrent() /
                            $pushInfo->getProgressDetail()->getTotal()
                        );
                        break;
                    default:
                        $this->getOutput()->writeln($status);
                        break;

                }
            } elseif ($error) {
                $this->getOutput()->writeln($error);
            } else {
                $this->getOutput()->writeln("Done");
            }
        });

        $pushStream->wait();
    }
}
