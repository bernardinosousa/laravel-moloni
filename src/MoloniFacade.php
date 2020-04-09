<?php

namespace Tiagosimoesdev\Moloni;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Spatie\Analytics\Analytics
 */
class MoloniFacade extends Facade
{
    protected static function getFacadeAccessor()
    {

        return 'laravel-moloni';
    }
}
