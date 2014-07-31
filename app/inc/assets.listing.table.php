<?php

if( ! class_exists( 'WP_List_Table' ) ) {
require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
 
class Assets_Listing_Table extends WP_List_Table 
{
 
	public $assets = array();

	public function __construct()
	{

		global $status, $page;

		$this->assets = $this->fetch_assets_items();
		parent::__construct( 
		array(
			'singular' => __( 'assets', 'custom_assets' ), 
			'plural' => __( 'assets', 'custom_assets' ), 
			'ajax' => true 
		));
	}

	public function fetch_assets_items()
	{
	    $data = [];

	    $posts = new WP_Query(
	    	array(
	    		'post_type' => 'custom_assets_data',
	    		'posts_per_page' => -1
	    	)
	    );
	    foreach ($posts->posts as $key => $value) {
	    	$data[] = array(
	    		'meta' => get_post_meta( $value->ID ),
	    		'item-id' => $value->ID 
	    	);
	    }

	    return $data;
	}
	public function column_default( $item, $column_name )
	{
		switch( $column_name ):
			case 'item-id':
				return $item[ $column_name ];
				break;
			case 'options':
				return Custom_Assets_Main_View::options_holder($item['item-id']);
				break;
			case 'inline':
				$inline_code = $item['meta'][ 'inline' ][0];

				$size = ( Custom_Assets_Main_View::size_readable($inline_code) == '0 B' ) 
					? 'Remote Resource'
						: Custom_Assets_Main_View::size_readable($inline_code) ;

				return $size;
			case 'state':
				return ( $item['meta'][ 'display_' ][0] == 0 ) ? 'Disabled ' : 'Active ';
				break;
				//return  explode("\n", urldecode( $item['meta'][ $column_name ][0] ) )[0];
				break;
			case 'in-footer':
				return  ( $item['meta'][ $column_name ][0] == 0 ) ? 'Footer' : 'Header' ;
				break;
			case 'res-type':
				return  strtoupper( $item['meta'][ $column_name ][0] );
				break;
			case 'remote':
				return  Custom_Assets_Main_View::remote_resource_markup( urldecode( $item['meta'][ $column_name ][0] ) );
				break;
			default:
				return $item['meta'][ $column_name ][0];
				break;
		endswitch;
	}

	public function get_sortable_columns() 
	{
		$sortable_columns = array(
			'#' => array(
				'item-id',
				true,
			),
		);

		return $sortable_columns;
	}

	public function get_columns()
	{	
		$columns = array(
			'item-id' => __( '#', 'custom_assets' ),
			'remote' => __( 'Remote resource', 'custom_assets' ),
			'state'  => __( 'State', 'custom_assets' ),
			'in-footer' => __( 'Placement', 'custom_assets' ),
			'res-type' => __( 'Type', 'custom_assets' ),
			'inline' => __( 'Size', 'custom_assets' ),
			'options' => __( 'Options', 'custom_assets' ),
		);

		return $columns;
	}
 
	public function prepare_items()
	{
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();

		$this->_column_headers = array( 
			$columns, 
			$hidden, 
			$sortable 
		);

		$per_page = 10;
		$current_page = $this->get_pagenum();
		$total_items = count( $this->assets );

		$this->found_data = array_slice( 
			$this->assets,
			( ( $current_page-1 )* $per_page ), 
			$per_page 
		);

		$this->set_pagination_args( 
			array(
				'total_items' => $total_items, 
				'per_page' => $per_page 
			) 
		);

		$this->items = $this->found_data;
	}
 
}