<?php

class CIX_Model extends CI_Base_Model
{
    protected function get_current_user()
    {
        return $this->ezrbac->getCurrentUserID();
    }
}