<?php
class BookingHub_Form_Builder {

    function __construct() {
		//add_action( 'admin_menu', [$this, 'options_page'] );
        add_action( 'admin_enqueue_scripts', [$this, 'admin_enqueue_scripts'] );
	}
    /*function options_page() {
        add_submenu_page(
            'edit.php?post_type=bhub-booking',
            __('Form Builder', 'hub-booking'),
            __('Form Builder', 'hub-booking'),
            'manage_options',
            'form_builder',
            [$this,'form_builder']
        );
    }*/

    function admin_enqueue_scripts() {
        //wp_enqueue_script( 'form-js', BHUB_URL . 'assets/vendor/form-js/form-editor.umd.js', [], '1.1.0', false );
        wp_enqueue_style( 'form-js', BHUB_URL . 'assets/vendor/form-js/form-js.css', [], '1.1.0' );
        wp_enqueue_style( 'form-js-editor', BHUB_URL . 'assets/vendor/form-js/form-js-editor.css', [], '1.1.0' );  
    }
    
    function form_builder() {
		//$this->prepare_items()->display();
		require_once BHUB_PATH . 'includes/dashboard/getFromData.php';
		$formDatas = new BookingHub_Form_Builder_data;
		echo '<div class="wrap" id="form-builder-list-table">';
		echo '<h1 class="wp-heading-inline">Form Builder Lists</h1>';
		echo '<a class="page-title-action" href="post-new.php?post_type=bhub-booking">Add New</a>';
		echo '<form method="post">';
		$formDatas->prepare_items();
		$formDatas->search_box('Search Records','search_record');
		$formDatas->display();
		echo '</form>';
		echo '</div>';
	}

}
//wp_redirect( esc_url( add_query_arg() ) );
//exit;
new BookingHub_Form_Builder();