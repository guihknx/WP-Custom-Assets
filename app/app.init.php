<?php

require 'controllers/controller.main.php';
require 'views/view.main.php';
require 'models/model.main.php';
require 'inc/assets.listing.table.php';

class Custom_Assets_Main_Initialize
{

	private $controller;
	private $ui;
	private static $instance;

	public function __construct()
	{

	}
	public static function init($url, $path) 
	{
		new Custom_Assets_Main_Controller( $url, $path );

        if (!isset(self::$instance) || self::$instance === null) {
            self::$instance = new self;
        }

        return self::$instance;
    }
}