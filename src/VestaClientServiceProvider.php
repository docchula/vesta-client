<?php

namespace Docchula\VestaClient;

use Docchula\VestaClient\Commands\RetrieveStudentCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class VestaClientServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('vesta-client')
            ->hasConfigFile()
            ->hasCommand(RetrieveStudentCommand::class);
    }
}
