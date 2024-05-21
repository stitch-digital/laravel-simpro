<?php

namespace StitchDigital\Simpro;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use StitchDigital\Simpro\Commands\SimproCommand;

class SimproServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-simpro')
            ->hasConfigFile('simpro')
            ->hasViews()
            ->hasMigration('create_laravel-simpro_table')
            ->hasCommand(SimproCommand::class);
    }
}
