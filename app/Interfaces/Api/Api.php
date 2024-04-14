<?php

namespace App\Interfaces\Api;

interface Api {

    public function auth() : object;

    public function getData(array $params) : array;

    public function setData();

}
