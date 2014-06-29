<?php

function CommandRegistry($kernel){
    $commands =  array(
        new \Xiidea\Commands\DumpCommand( $kernel ),
        new \Xiidea\Commands\CacheClearCommand( $kernel ),
        new \Xiidea\Commands\ServerRunCommand( $kernel )
    );

    return $commands;
}
