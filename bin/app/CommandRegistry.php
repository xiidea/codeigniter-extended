<?php

function CommandRegistry($kernel){
    $commands =  array(
        new \Xiidea\Commands\DumpCommand( $kernel ),
    );

    return $commands;
}
