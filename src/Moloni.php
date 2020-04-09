<?php

namespace Tiagosimoesdev\Moloni;

use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Macroable;


class Moloni
{
    use Macroable;


    public function __construct()
    {
        echo 123;
    }

    /**
     * @param string $viewId
     *
     * @return $this
     */
    public function teste()
    {
        return 44;
    }
}
