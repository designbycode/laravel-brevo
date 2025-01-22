<?php

namespace Designbycode\LaravelBrevo;

use Designbycode\LaravelBrevo\Commands\BrevoCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

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
            ->hasConfigFile();
        //            ->hasCommand(BrevoCommand::class);
    }
}
