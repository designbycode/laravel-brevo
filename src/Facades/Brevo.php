<?php

namespace Designbycode\LaravelBrevo\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Designbycode\LaravelBrevo\Brevo
 */
class Brevo extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Designbycode\LaravelBrevo\Brevo::class;
    }
}
