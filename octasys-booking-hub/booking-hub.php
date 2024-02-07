<?php
/**
 * Plugin Name: Booking Hub
 * Description: Powerfull Booking Management System.
 * Plugin URI: https://.liquid-themes.com/
 * Version: 1.0
 * Author: Liquid Themes
 * Author URI: https://liquid-themes.com/
 * Text Domain: booking-hub
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

define( 'BHUB_PATH', plugin_dir_path( __FILE__ ) );
define( 'BHUB_URL', plugin_dir_url( __FILE__ ) );
define( 'BHUB_VERSION', get_file_data( __FILE__, array('Version' => 'Version'), false)['Version'] );

require_once BHUB_PATH . 'includes/plugin.php';

wp_enqueue_script('jquery');
function bhubFormRendor($atts) {
    $postId = $atts['form_id'];
	$formData = get_post_meta($postId, 'formData', true);
	$formId = get_post_meta($postId, 'form_id', true);
	$formDatas = '<style>.fjs-editor-container{max-height:calc(100vh - 125px);}</style><link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:ital,wght@0,400;0,600;1,400&display=swap" rel="stylesheet"><link rel="stylesheet" href="'.BHUB_URL . 'assets/vendor/form-js/form-js.css"><link rel="stylesheet" href="'.BHUB_URL . 'assets/vendor/form-js/form-js-editor.css"><div id="bhub_form"></div><script src="'.BHUB_URL . 'assets/vendor/form-js/form-viewer.umd.js"><script src="https://unpkg.com/browse/@bpmn-io/form-js@1.6.4/dist/form-playground.umd.js"></script><script>const schema = {
					"components":'.base64_decode($formData).',
					"type": "default",
					"id": "'.$formId.'"
				};
				
				const data = {};
				const customForm = FormViewer.createForm({
				  schema,
				  data,
				  container: document.querySelector("#bhub_form")
				});
				//customForm.importSchema(schema, data);
				//FormViewer.submit();
				customForm.then(async (form) => {
					form.on("submit", (event) => {
					  //alert("errors " + JSON.stringify(event.errors));
					  if(JSON.stringify(event.errors) != "{}") {
						console.log(event.data, event.errors);
					  } else {
						//console.log("Success Form "+event.data);
						jQuery(document).ready( function($){
							$.ajax({
								url: "'.admin_url( "admin-ajax.php" ).'",
								type: "POST",
								data:{ 
								  action: "myaction",
								  formData: event.data,
								  formId: "'.$formId.'",
								  form_post_id: "'.$postId.'"
								},
								success: function( sendData ){
								  console.log( sendData );
								  alert("Your Form is successfully submit!");
								  window.location = "";
								}
							});
						});
					  }
					});
				});
				</script>';
	return $formDatas;
}
add_shortcode('bhubForm', 'bhubFormRendor');

add_action( "wp_ajax_myaction", "saveFormData" );
add_action( "wp_ajax_nopriv_myaction", "saveFormData" );
function saveFormData(){
	//echo '<pre>';print_r($_POST);
	$my_post = array(
		'post_title'    => wp_strip_all_tags( $_POST['formId'] ).'_'.uniqid(),
		'post_content'  => addslashes(json_encode($_POST['formData'])),
		'post_excerpt'  => trim($_POST['form_post_id']),
		'post_status'   => 'publish',
		'post_type'   => 'bhub-booking'
	);
	$post_id = wp_insert_post( $my_post, true );
	update_post_meta($post_id, "form_id", $_POST["formId"]);
	update_post_meta($post_id, "formData", $_POST["formData"]);
  wp_die();
}