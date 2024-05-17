<?php

namespace StitchDigital\Simpro\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \StitchDigital\Simpro\Simpro
 */
class Simpro extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \StitchDigital\Simpro\Simpro::class;
    }
}
