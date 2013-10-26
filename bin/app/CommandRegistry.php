<?php

function CommandRegistry($kernel){
    $commands =  array(
        new \Xiidea\Commands\DumpCommand( $kernel ),
        new \Xiidea\Commands\CacheClearCommand( $kernel )
    );

    return $commands;
}
