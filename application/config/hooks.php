<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	https://codeigniter.com/user_guide/general/hooks.html
|
*/

$hook['pre_system'] = function()
{
    spl_autoload_register(function() {
        include_once(dirname(dirname(__FILE__)) . '/core/Gen_Controller.php');
    });
};

$hook['pre_controller'] = function()
{
    spl_autoload_register(function() {
        include_once(dirname(dirname(__FILE__)) . '/core/Base_Model.php');
    });
};
