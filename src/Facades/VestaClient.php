<?php

namespace Docchula\VestaClient\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Docchula\VestaClient\VestaClient
 */
class VestaClient extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Docchula\VestaClient\VestaClient::class;
    }
}
