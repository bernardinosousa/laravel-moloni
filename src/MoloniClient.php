<?php

namespace Tiagosimoesdev\Moloni;

use DateTime;
use Google_Service_Analytics;
use Illuminate\Contracts\Cache\Repository;

class MoloniClient
{
    /** @var \Google_Service_Analytics */
    protected $service;

    /** @var \Illuminate\Contracts\Cache\Repository */
    protected $cache;

    /** @var int */
    protected $cacheLifeTimeInMinutes = 0;

    public function __construct()
    {
        return 33;
    }
}
