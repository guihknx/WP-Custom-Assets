<?php
/*
   Plugin Name: WP Custom Assets
   Version: 0.1
   Author: guihkx
   Author URI: https://github.com/guihknx/
   Description: Allow include external js/css resource without edit theme or plugin itself
   License: GPLv2
*/
require 'app/app.init.php';

class Custom_Assets
{
    private $init;

    public function __construct()
    {
      $this->init = Custom_Assets_Main_Initialize::init( plugin_dir_url( __FILE__ ), dirname( __FILE__ ) );
    }
}

$plugin_instance = new Custom_Assets();

?>