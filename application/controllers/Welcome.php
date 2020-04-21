<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends Base_Controller
{

    public function index()
    {
        $this->renderView('welcome_message');
    }
}
