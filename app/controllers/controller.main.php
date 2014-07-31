<?php  
 /**
	* @file
	* Custom Assets
	*
	* $Id: controller.main.php 
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
* @class Custom_Assets_Main_Controller
* @brief Create and handle WP Interface
*/
class Custom_Assets_Main_Controller
{
	/**
	* Holder for nstance of model class
	* 
	* @return void
	*/
	private $model;

	public function __construct(  $url, $path )
	{
		$this->model = new Custom_Assets_Main_Model( $url, $path );
		add_action( 'init', array( &$this, 'bootstrap' ) );
	}
	/**
	* Register Ajax actions and register WP Custom Post Type
	* 
	* @return void
	*/
	public function bootstrap()
	{
		add_action( 'wp_ajax_wpaci17089_add', array( &$this, 'add_custom_assets' ) );
		add_action( 'wp_ajax_nopriv_wpaci17089_add', array( &$this, 'add_custom_assets' ) );
		$this->register_cpt_storage();
	}
	/**
	* Register WP Custom Post Type
	* 
	* @return void
	*/
	public function register_cpt_storage()
	{
		register_post_type( 'custom_assets_data',
			array(
				'labels' => array(
					'name' => __( 'Custom Assets' ),
					'singular_name' => __( 'Custom Asssets' )
				),
				'public' => true,
				'has_archive' => true,
			)
		);
	}
	/**
	* Create, Update custom asset
	* 
	* @return void
	*/
	public function add_custom_assets()
	{
		$data = $_POST;
		$type = $_POST['type'];
		$inner_code = esc_html( $data['inner'] );

		$probably_id = ( $data['inner']['editing_id'] != '' ) 
			? $data['inner']['editing_id'] 
				: '';

		$args = array( 
			'post_type' => 'custom_assets_data' 
		);

		if( $probably_id != '' ) :
			$post_id = $probably_id;
		else:
			$post_id = wp_insert_post( $args );
		endif;

		// check if have meta so it's already exists...
		$post_meta = get_post($id);

		if( $inner_code['remote_'.$type] != "" )
			update_post_meta($post_id, 'remote', $inner_code['remote_'.$type]);

		if( $inner_code['temp_code'] != "" )
			update_post_meta($post_id, 'inline', $inner_code['temp_code']);

		if( $inner_code['disabled'] != "" )
			update_post_meta($post_id, 'display_', $inner_code['disabled']);


		$to_update = array(
			'in-footer' => $inner_code['on_footer'],
			'res-type' => $data['type'],
		);

		if( $inner_code['disabled'] == '' && $post_meta !== null )
			$to_update['display_'] = 0;


		foreach ($to_update as $key => $value)
			update_post_meta($post_id, $key, $value);

		die(0);
	}

}
?>