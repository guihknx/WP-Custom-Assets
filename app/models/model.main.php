<?php  
 /**
	* @file
	* Custom Assets
	*
	* $Id: model.main.php 
	*
	* This program is free software: you can redistribute it and/or modify
	* it under the terms of the GNU General Public License as published by
	* the Free Software Foundation, either version 3 of the License, or
	* (at your option) any later version.
	*
	* This program is distributed in the hope that it will be useful,
	* but WITHOUT ANY WARRANTY; without even the implied warranty of
	* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	* GNU General Public License for more details.
	*
	* You should have received a copy of the GNU General Public License
	* along with this program. If not, see <http://www.gnu.org/licenses/>.
	*
	* @package   Custom Assets
	* @version   1.0.0
	* @author    Guilherme Henrique
	* @copyright Copyright (c) 2010 by Guilherme Henrique. All rights reserved to their respective owners.
*/
  
/**
* @class Custom_Assets_Main_Model
* @brief This class will handle all transactions between DB and WP Interface
*/
class Custom_Assets_Main_Model
{
	/**
	* Holder for nstance of view class
	* 
	* @return void
	*/
	private $view;

	public function __construct( $url, $path )
	{
		$this->view = new Custom_Assets_Main_View( $url, $path );
	}
	/**
	* Holder for nstance of view class
	* 
	* @param string $bytes an packed UTF-8 binary string
	* @return void
	*/
	public static function bytes($bytes)
	{
		$bytes = strlen($bytes);

		$kbytes = sprintf("%.02f", (int)$bytes/1024);
		$mbytes = sprintf("%.02f", (int)$kbytes/1024);
		$gbytes = sprintf("%.02f", (int)$mbytes/1024);
		$tbytes = sprintf("%.02f", (int)$gbytes/1024);


		if($tbytes >= 1)
			return $tbytes . " TB";
		if($gbytes >= 1)
			return $gbytes . " GB";
		if($mbytes >= 1)
			return $mbytes . " MB";
		if($kbytes >= 1)
			return $kbytes . " KB";

		return $bytes . " B";
	}
  
}
?>