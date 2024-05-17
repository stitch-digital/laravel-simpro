<?php

namespace StitchDigital\Simpro;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use StitchDigital\Simpro\Commands\SimproCommand;

class SimproServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-simpro')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel-simpro_table')
            ->hasCommand(SimproCommand::class);
    }
}
