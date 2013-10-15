<?php

namespace Xiidea\Commands;


class ProxyController {

    public function __construct(){

    }

    public function __call($method, $args){
       return $this;
    }

    public function __get($property){
        return $this;
    }

}