<?php  
 /**
	* @file
	* Custom Assets
	*
	* $Id: view.main.php 
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
* @class Custom_Assets_Main_View
* @brief This class handle the client View needed resources
*/
class Custom_Assets_Main_View
{

	/**
	* Relative URL to plugin dir
	* 
	* @return void
	*/
	private $uri;

	/**
	* Absolute path to plugin dir
	* 
	* @return void
	*/
	private $path;
	
	public function __construct( $url, $path )
	{
		$this->path = $path;
		$this->uri = $url;

		if( !defined( 'DS' ) )
			define('DS', DIRECTORY_SEPARATOR);

		add_action( 'admin_print_scripts', array( &$this, 'enqueue_assets' ) );
		add_action('init', array( &$this, 'boostrap_ui' ));
	}
	public function boostrap_ui($value='')
	{
		add_action( 'admin_menu', array( &$this, 'register_admin_menus' ) );
	}
	/**
	* Admin menus
	* 
	* The menu and sub-menu displayed in admin panel
	* 
	* @since 0.1
	* @return void
	*/

	public function register_admin_menus()
	{
		global $submenu;

		add_menu_page( 
			'Custom Assets', 
			'Custom Assets', 
			'manage_options', 
			'custom-assets', 
			array( 
				&$this, 'custom_assets' 
			), 
			'dashicons-welcome-write-blog' 
		);
		add_submenu_page( 
			'custom-assets', 
			__( 'Add CSS',  'custom_assets' ),
			__( 'Add CSS',  'custom_assets' ),
			'manage_options', 
			'add-css-file', 
			array( 
				&$this, 'add_css_item' 
			) 
		); 
		add_submenu_page( 
			'custom-assets', 
			__( 'Add JS',  'custom_assets' ),
			__( 'Add JS',  'custom_assets' ),
			'manage_options', 
			'add-js-inline', 
			array( 
				&$this, 'add_js_item' 
			) 
		); 
		add_submenu_page( 
			'custom-assets', 
			__( 'Edit', 'custom_assets' ), 
			null, 
			'manage_options', 
			'edit_custom-assets', 
			array( 
				&$this, 'edit_item' 
			) 
		); 
		add_submenu_page( 
			'custom-assets', 
			__( 'Delete', 'custom_assets' ), 
			null, 
			'manage_options', 
			'delete_custom-assets', 
			array( 
				&$this, 'delete_item' 
			) 
		); 
	}
	/**
	* Load needed assets
	* 
	* Load js neede to perform ajax call
	* 
	* @since 0.1
	* @return void
	*/
	public function enqueue_assets()
	{
		$page = get_current_screen();	


		if( 
			$page->id == 'custom-assets_page_add-css-file' 
			|| $page->id == 'custom-assets_page_add-js-inline'
			|| $page->id == 'toplevel_page_custom-assets'
			|| $page->id == 'custom-assets_page_edit_custom-assets'
			|| $page->id == ' custom-assets_page_delete_custom-assets' 
		) :
			// Our js
			wp_enqueue_script( 
			  'main-js-custom-assets',
			  $this->uri . 'assets/js/custom-assets.min.js', 
			  array('jquery'), 
			  filemtime( $this->path . DS .'assets'.DS.'js'.DS.'custom-assets.min.js' ), 
			  true 
			);
			wp_enqueue_script( 
			  'ajaxorg-ace-js',
			  $this->uri . 'dist/js/ace/ace.js', 
			  array('jquery'), 
			  filemtime( $this->path . DS .'dist'.DS.'js'.DS.'ace'.DS.'ace.js' ), 
			  true 
			);

			// Our stylesheet
			wp_enqueue_style( 
				'custom-assets-style', 
				$this->uri . 'assets/css/custom-assets.min.css',
				array(),
				filemtime( $this->path . DS . 'assets'.DS.'css'.DS.'custom-assets.min.css' ),
				null
			);

			// Our ajax helper url
			wp_localize_script( 
				'main-js-custom-assets', 
				'ajax_utils', 
				array( 
					'ajaxurl' => admin_url( 'admin-ajax.php' ),
					'strings' => array(
						'cancel' => __( 'cancel', 'custom_assets' ),
						'addRemoteJs' => __( 'Add remote JS', 'custom_assets' )
					)
				)
			);
		endif;
	}
	/**
	* Add CSS view
	* 
	* Render html markup
	* 
	* @return string
	*/
	public function add_css_item()
	{	
		$message = __( 'Adds remotely hosted resources.', 'custom_assets' );		
		ob_start();
		print '
			<div class="wrap">
				<h2>Custom Assets</h2>
				<div id="item-added" class="updated below-h2" style="display: none;">
					<p>'.__( 'CSS resource added.', 'custom_assets' ).'</p>	
				</div>
				<div class="editor-holder">
					<h3>Css Inline</h3>
					<h4 class="icon-dashicon">'.__( 'Enter the custom CSS', 'custom_assets' ).'</h4>
					<!--<div class="holder">
						<span class="editor-scheme"><span class="scheme-selector"><span class="dark">dark</span><span class="light">light</span></span></span>
					</div>-->
					<pre id="css-editor"><code></code></pre>
					<p>'.__( 'Styles will be automatically displayed with the class <code>.custom-content-file</code>. You can alter <a href="javascript:;" id="edit-class-css">this class</a>.', 'custom_assets' ).'</p>
					<form name="css-save-action" id="save-css">
						<label for="on_footer">'.__( 'Print resource in the:', 'custom_assets' ).' </label>
						<select name="on_footer" id="on-footer">
							<option value="0">Footer</option>
							<option value="1">Header</option>
						</select>
						<p><a href="javascript:;" id="add-remote-css" title="'.$message.'">'.__( 'Add remote CSS', 'custom-assets').'</a></p>
						<p id="remote-css">
							<input type="text" name="remote_css" placeholder="http://cdn.org.com/file.js?ver=00001" class="remote-resource large-text code">
							<span class="description">'.__( 'Enter the link refs the remote resource.', 'custom_assets' ).'</span>
						</p>
						<textarea name="temp_code" id="temp_code"></textarea>
					</form>
					<button class="button button-primary save-code-to-db">'.__( 'Save', 'custom_assets' ).'</button>
					<span style="display: none;" class=" spinner-saving spinner">Saving...</span>

				</div>
			</div>
		';
		
		$markup = ob_get_contents();
		ob_end_clean();

		printf( '%s', $markup );

	}
	/**
	* Edit item view
	* 
	* Render html markup
	* 
	* @return string
	*/
	public function edit_item($id=0)
	{	
		$id = $_GET['id'];
		$post = get_post( $id );
		if( $post != null && $post->post_type == 'custom_assets_data' ) :

			//print_r($post);
			$meta = get_post_meta($id);
			//print_R($meta);
			$checked = ( $meta['display_'][0] == 1 ) ? ' checked ' : '';
			var_dump($meta['display_'][0]);
			$message = __( 'Adds remotely hosted resources.', 'custom_assets' );		
			ob_start();
			print '
				<div class="wrap">
					<h2>Custom Assets</h2>
					<div id="item-added" class="updated below-h2" style="display: none;">
						<p>'.__( 'Changes saved.', 'custom_assets' ).'</p>	
					</div>
					<div class="editor-holder">
						<h3>'.__( 'Edit resource', 'custom_assets' ).'</h3>
						<p>Id: <strong>#'. $id.'</strong></p> 
						<p>'.__( 'Last Modified', 'custom_assets' ).': <i>'.date('d-m-Y H:i:s', strtotime( $post->post_date ) ).'</i></p>
						<!--<div class="holder">
							<span class="editor-scheme"><span class="scheme-selector"><span class="dark">dark</span><span class="light">light</span></span></span>
						</div>-->
						<pre id="'.urldecode( $meta['res-type'][0] ).'-editor"><code>'.urldecode( $meta['inline'][0] ).'</code></pre>
						<p>'.__( 'Styles will be automatically displayed with the class', 'custom_assets' ).' <code>.custom-content-file</code>. '.__( 'You can alter', 'custom_assets' ).' <a href="javascript:;" id="edit-class-css">'.__( 'this class', 'custom_assets' ).'</a>.</p>
						<form name="'.urldecode( $meta['res-type'][0] ).'-save-action" id="save-'.urldecode( $meta['res-type'][0] ).'">
							<label for="on_footer">
								'.__( 'Print resource in the:', 'custom_assets' ).' 
							</label>
							<select name="on_footer" id="on-footer">
								<option value="0">Footer</option>
								<option value="1">Header</option>
							</select>
							<p><a href="javascript:;" id="add-remote-'.urldecode( $meta['res-type'][0] ).'" title="'.$message.'">'.__( 'Add remote '.urldecode( $meta['res-type'][0] ), 'custom-assets' ).'</a></p>
							<p id="remote-'.urldecode( $meta['res-type'][0] ).'">
								<input type="text" name="remote_'.urldecode( $meta['res-type'][0] ).'" value="'.urldecode( $meta['remote'][0] ).'" placeholder="http://cdn.org.com/file.js?ver=00001" class="remote-resource large-text code">
								<span class="description">'.__( 'Enter the link refs the remote resource.', 'custom_assets' ).'</span>
							</p>
							<textarea name="temp_code" id="temp_code"></textarea>
							<input type="hidden" name="editing_id" value="'.$id.'">
							<p>
								<input type="radio" '.$checked.' name="disabled" value="0"> Inativo
								<input type="radio" '.$checked.' name="disabled" value="1"> Ativo
							</p>
						</form>
						<button class="button button-primary save-code-to-db">'.__( 'Save', 'custom_assets' ).'</button>
						<span style="display: none;" class=" spinner-saving spinner">Saving...</span>

					</div>
				</div>
			';
			
			$markup = ob_get_contents();
			ob_end_clean();

			printf( '%s', $markup );
		else :

			?>
			<div class="wrap">
				<h2>Custom Assets</h2>
				<p><?php print __( 'Unknown ID.', 'custom_assets' ); ?></p>
			</div>
			<?php
		endif;
	}
	/**
	* Add JS view
	* 
	* Render html markup
	* 
	* @return string
	*/
	public function add_js_item()
	{	

		$message = __( 'Adds remotely hosted resources.', 'custom_assets' );	
		
		ob_start();
		print '
			<div class="wrap">
				<h2>Custom Assets</h2>
				<div id="item-added" class="updated below-h2" style="display: none;">
					<p>'.__( 'Resource added.', 'custom_assets' ).'</p>	
				</div>
				<div class="editor-holder">
					<h3>'.__( 'JS Inline', 'custom_assets' ).'</h3>
					<h4 class="icon-dashicon">'.__( 'Enter the custom JavaScript', 'custom_assets' ).'</h4>
					<!--<div class="holder">
						<span class="editor-scheme"><span class="scheme-selector"><span class="dark">dark</span><span class="light">light</span></span></span>
					</div>-->
					<pre id="js-editor"><code></code></pre>
					<p>'.__( 'Scripts will be automatically displayed with the class <code>.custom-content-file</code>. You can alter <a href="javascript:;" id="edit-class-css">this class</a>.', 'custom_assets' ).'</p>
					<form name="js-save-action" id="save-js">
						<label for="on_footer">
							'.__( 'Print resource in the:', 'custom_assets' ).' 
						</label>
						<select name="on_footer" id="on-footer">						
							<option value="0">Footer</option>
							<option value="1">Header</option>
						</select>
						<p><a href="javascript:;" id="add-remote-js" title="'.$message.'">'.__( 'Add remote JS' , 'custom-assets').'</a></p>
						<p id="remote-js">
							<input type="text" name="remote_js" placeholder="http://cdn.org.com/file.js?ver=00001" class="remote-resource large-text code">
							<span class="description">'.__( 'Enter the link refs the remote resource.', 'custom_assets' ).'</span>
						</p>
						<textarea name="temp_code" id="temp_code"></textarea>
					</form>
					<button class="button button-primary save-code-to-db">'.__( 'Save', 'custom-assets' ).'</button>
					<span style="display: none;" class=" spinner-saving spinner">Saving...</span>
				</div>
			</div>
		';
		
		$markup = ob_get_contents();
		ob_end_clean();

		printf( '%s', $markup );

	}
	/**
	* Options
	* 
	* Render options html markup
	* 
	* @return string
	*/
	public static function options_holder($id)
	{
		ob_start();
		print '
			<span class="edit-item item-edit-'.$id.'">
				<a href="admin.php?page=edit_custom-assets&id='.$id.'">'.__( 'Edit', 'custom_assets' ).'</a>
			</span> 
			<span class="comfirm-delete-item item-delete-comfirm-'.$id.'" data-id="'.$id.'">
				<a class="cancel-exlusion" data-id="'.$id.'" href="javascript:;">'.__( 'Cancel', 'custom_assets' ).'</a>
				<a href="javascript:;" class="yes-remove remove">'.__( 'Comfirm', 'custom_assets' ).'</a>
			</span>
			<span class="delete-item item-delete-'.$id.'" data-id="'.$id.'">
				<a href="javascript:;" class="remove">'.__( 'Delete', 'custom_assets' ).'</a>
			</span>
		';
		
		$markup = ob_get_contents();
		ob_end_clean();

		printf( '%s', $markup );
	}
	/**
	* Format link to resource
	* 
	* @return string
	*/
	public static function remote_resource_markup($url)
	{
		$url_truncate = substr_replace($url, '...', 42/2, strlen($url)-42);
		$url_truncate = ( $url_truncate == '...' ) ? '--' : $url_truncate;


		if( $url_truncate != '--' ) :
			ob_start();

			print '<a href="'.$url.'" title="'.$url.'" class="remote-resource">'.$url_truncate.'</a>';
			
			$markup = ob_get_contents();
			ob_end_clean();

			printf( '%s', $markup );
		else :
			print __( 'Inline resource', 'custom_assets' );
		endif;
	}
	/**
	* Custom Assets listing Table
	* 
	* @return string
	*/
	public function custom_assets()
	{

		global $assets_list_table;
		$assets_list_table = new Assets_Listing_Table();

		?>

		<div class="wrap">
			<h2>Custom Assets</h2>
		<?php
		$assets_list_table->prepare_items();
		$assets_list_table->search_box( 'search', 'search_id' );
		$assets_list_table->display();
		?>
		</div>

		<?php
	}
	/**
	* Format link to resource
	* @param $str a string with the code content
	*
	* @return string
	*/
	public static function size_readable($str)
	{
		$bytes = Custom_Assets_Main_Model::bytes($str);
		$lines = explode("\n", urldecode( $str ) );

		$contents = $lines[0];
		$contents .= $lines[1];
		$contents .= $lines[2];

		if( isset( $lines[2] ) && isset( $lines[1] ) )
			$contents .= __( 'Edit to see more...', 'custom_assets' );
		if( $bytes == '0 B' )
			return '0 B';

		return "<abbr title=\"$contents\">$bytes</abbr>";
	}
	/**
	* Delete an item by ID
	*
	* @return string || void
	*/
	public static function delete_item()
	{
		$id = $_GET['_id'];

		$post_meta = get_post( $id );
		if( $post_meta != null && $post_meta->post_type == 'custom_assets_data' ) :
			wp_delete_post( $id );
			print '<script>window.location.href=\'admin.php?page=custom-assets\';</script>';

		else :
		?>
			<div class="wrap">
				<h2>Custom Assets</h2>
				<p><?php print __( 'Unknown ID.', 'custom_assets' ); ?></p>
			</div>
		<?php
		endif;

	}
}
?>