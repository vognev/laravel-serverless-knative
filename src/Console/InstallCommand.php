<?php

namespace Laravel\Serverless\Knative\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Laravel\Serverless\Knative\Helper;
use Laravel\Serverless\Config;

class InstallCommand extends Command
{
    protected $name = 'serverless:install';

    public function handle()
    {
        $config_path = config_path('serverless.php');
        if (! File::exists($config_path)) {
            File::copy(Helper::package_path('config/serverless.php'), $config_path);
        }

        $storage_path = config('serverless.storage');
        if (! File::exists($storage_path)) {
            File::copyDirectory(Helper::package_path('templates/storage'), $storage_path);
        }

        $context_path = $storage_path . '/context';
        if (! File::exists($context_path)) {
            $runtimes = array_map('basename', File::directories(
                Helper::package_path('runtimes')
            ));

            $this->copyDirectory(
                Helper::package_path('runtimes', $this->choice('Which runtime to use?', $runtimes)),
                $context_path
            );
        }

        $dockerignore_path = base_path('.dockerignore');
        if (! File::exists($dockerignore_path)) {
            File::copy(Helper::package_path('templates/.dockerignore'), $dockerignore_path);
        }

        $phpconf_path = base_path('.php.conf.d');
        if (! File::exists($phpconf_path)) {
            File::makeDirectory($phpconf_path);

            foreach (Config::phpModules() as $module) {
                if (File::exists( $ini = Helper::package_path("templates/php.conf.d/$module.ini") )) {
                    File::copy($ini, $phpconf_path . '/' . basename($ini));
                }
            }
        }
    }

    private function copyDirectory($directory, $destination, $options = null)
    {
        $options = $options ?: \FilesystemIterator::SKIP_DOTS;

        if (! is_dir($destination)) {
            mkdir($destination, 0755, true);
        }

        $items = new \FilesystemIterator($directory, $options);

        foreach ($items as $item) {
            $target = $destination.'/'.$item->getBasename();

            if ($item->isDir()) {
                $path = $item->getPathname();

                if (! $this->copyDirectory($path, $target, $options)) {
                    return false;
                }
            }
            else {
                if (! copy($item->getPathname(), $target)) {
                    return false;
                }

                chmod($target, fileperms($items->getPathname()));
            }
        }

        return true;
    }
}
