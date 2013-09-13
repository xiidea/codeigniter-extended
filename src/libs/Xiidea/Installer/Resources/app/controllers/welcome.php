<?php


class Welcome extends CIX_Controller {

	public function access_map(){
        return array(
            'index'=>'view',
            'greeting'=>'view'
        );
    }

    public function index()
	{
        //Auto detect template: welcome/index.twig or welcome/index.php
        $this->render(array('title'=>'Welcome to Codeigniter!'));
	}

    public function greeting()
    {
        //$this->render('welcome.html.twig', array('title'=>'Welcome to Codeigniter!');
        $this->render('welcome_message', array('title'=>'Welcome to Codeigniter!'));
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */