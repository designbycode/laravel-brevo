<?php

namespace Designbycode\LaravelBrevo;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Designbycode\LaravelBrevo\Commands\BrevoCommand;

class BrevoServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-brevo')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel_brevo_table')
            ->hasCommand(BrevoCommand::class);
    }
}
