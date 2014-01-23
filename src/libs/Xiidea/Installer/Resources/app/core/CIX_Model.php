<?php

class CIX_Model extends CI_Base_Model
{
    public function __construct()
    {
        if(get_class($this) == __CLASS__){
            return;
        }

        parent::__construct();
    }

    protected function get_current_user()
    {
        return $this->ezrbac->getCurrentUserID();
    }
}