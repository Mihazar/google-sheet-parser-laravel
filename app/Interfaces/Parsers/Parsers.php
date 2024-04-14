<?php

namespace App\Interfaces\Parsers;

use App\Interfaces\Api\Api;

interface Parsers {

    public function __construct(Api $api);

    public function getData($params) : object;

    public function setData() : bool;
}
