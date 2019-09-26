<?php

namespace Laravel\Serverless\Knative\Console\Runtime;

use Docker\API\Model\BuildInfo;
use Illuminate\Console\Command;
use Laravel\Serverless\Config;
use Docker\Docker;
use Docker\Context\Context;

class BuildCommand extends Command
{
    protected $name = 'serverless:runtime:build';

    protected $description = 'Builds your application docker image runtime';

    /** @var Docker */
    protected $docker = null;

    public function __construct()
    {
        $this->docker = Docker::create();

        parent::__construct();
    }

    public function handle()
    {
        $phpModules     = Config::phpModules();
        $runtimeImage   = config('serverless.image');

        $this->buildRuntimeDockerImage($runtimeImage, $phpModules);
    }

    private function buildRuntimeDockerImage(string $imageName, array $phpModules = [])
    {
        $this->info('Building runtime docker image');
        $context = str_replace(base_path(), '', config('serverless.storage') . '/context');
        $buildContext   = new Context(
            base_path()
        );

        // todo: .dockerignore

        $buildStream    = $this->docker->imageBuild($buildContext->toStream(), [
            't' => $imageName, 'dockerfile' => $context . '/Dockerfile',
            'buildargs' => json_encode([
                'SERVERLESS_PHP_MODULES' => implode(' ', $phpModules),
                'SERVERLESS_CTX_PATH'    => $context
            ])
        ]);

        $buildStream->onFrame(function (BuildInfo $buildInfo) {
            if ($error = $buildInfo->getError()) {
                throw new \RuntimeException($error);
            } else {
                $stream = $buildInfo->getStream();
                if (0 === strpos($stream, 'Step')) {
                    $this->line("$stream");
                } elseif ($stream) {
                    $this->output->write($stream, false, $this->output::VERBOSITY_VERBOSE);
                }
            }
        });

        $buildStream->wait();
    }
}
