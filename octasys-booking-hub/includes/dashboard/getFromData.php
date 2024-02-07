<?php
if (!class_exists('WP_List_Table')) {
	require_once (ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}
class BookingHub_Form_Builder_data extends WP_List_Table {

    public function __construct() {
		parent::__construct(array(
			'singular' => 'form_builder',
			'plural' => 'form_builder',
			'ajax' => false
		));
	}
	public static function get_records($per_page = 20, $page_number = 1) {
		global $wpdb;
		$sql = "select * from ".$wpdb->prefix."posts WHERE `post_type` = 'bhub-booking' AND `post_status` != 'auto-draft' ";
		if (isset($_REQUEST['s'])) {
			$sql.= ' AND `post_title` LIKE "%' . $_REQUEST['s'] . '%"';
		}
		if (!empty($_REQUEST['orderby'])) {
			$sql.= ' ORDER BY ' . esc_sql($_REQUEST['orderby']);
			$sql.= !empty($_REQUEST['order']) ? ' ' . esc_sql($_REQUEST['order']) : ' ASC';
		}
		$sql.= " LIMIT $per_page";
		$sql.= ' OFFSET ' . ($page_number - 1) * $per_page;
		//echo $sql;
		$result = $wpdb->get_results($sql, 'ARRAY_A');
		return $result;
	}
	function get_columns() {
		$columns = [
			'cb' => '<input type="checkbox" />', 
			'post_title' => __('Title') , 
			'shortCode' => __( 'Form Short Code' ),
			'totalEntry' => __( 'Total Entry' ),
			'post_date' => __('Date') , 
		];
		return $columns;
	}
	public function get_hidden_columns() {
		array([
			'created_at' => __('created_at','wth')
		]);
		return array();
	}
	public function get_sortable_columns() {
		$sortable_columns = array(
			'post_date' => array('post_date',true)
		);
		return $sortable_columns;
	}
	public function column_default( $item, $column_name ) {
		global $wpdb;
		switch ( $column_name ) {
			case 'post_title':
				return '<strong><a href="post.php?post='.$item['ID'].'&action=edit">'.$item[ $column_name ].'</a></strong>';
			case 'shortCode':
				return '<input type="text" value="[bhubForm form_id='.$item['ID'].']" readonly />';
			case 'totalEntry':
				$querystr = "SELECT * FROM $wpdb->posts WHERE `post_excerpt` = ".$post_id." AND `post_type` = 'bhubformdata'";     
				$totalBookings = $wpdb->get_results($querystr);
		
				return count($totalBookings);
			case 'post_date':
				return $item[ $column_name ];
			default:
			return print_r( $item, true );
		}
	}
	function column_cb($item) {
		return sprintf('<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['ID']);
	}
	//public function column_your_name( $item ) {}
	function get_bulk_actions() {
		$actions = ['bulk-delete' => 'Delete'];
		return $actions;
	}
	function delete_records($id) {
		global $wpdb;
		$wpdb->delete($wpdb->prefix."posts", ['ID' => $id], ['%d']);
	}
	function record_count() {
		global $wpdb;
		$sql = "SELECT COUNT(*) FROM ".$wpdb->prefix."posts WHERE `post_status` != 'auto-draft'";
		return $wpdb->get_var($sql);
	}
	function no_items() {
		_e('No record found in the database.', 'wth');
	}
	function process_bulk_action() {
		if ( 'delete' === $this->current_action() ) {	    
			self::delete_records( absint( $_GET['record'] ) );
		}

		if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' ) || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' )) {
			$delete_ids = esc_sql( $_POST['bulk-delete'] );
			foreach ( $delete_ids as $id ) {
				self::delete_records( $id );
			}
		}
	}
	function prepare_items(){
	
		$columns = $this->get_columns();
		$hidden = $this->get_hidden_columns();
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );
		$this->process_bulk_action();
		$per_page = $this->get_items_per_page('records_per_page', 20);
		$current_page = $this->get_pagenum();
		$total_items = self::record_count();
		$data = self::get_records($per_page, $current_page);
		$this->set_pagination_args( [
			'total_items' => $total_items,
			'per_page' => $per_page,
		]);
		$this->items = $data;
	}
}