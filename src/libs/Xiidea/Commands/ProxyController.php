<?php

/*
 * This file is part of the CIX package.
 *
 * (c) Roni Saha <roni.cse@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

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