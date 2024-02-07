<?php 

function register_bookinghub_post_type() {

    // Booking
    $labels = array(
        'name'               => 'Bookings',
        'singular_name'      => 'Booking',
        'menu_name'          => 'All Booking',
        'name_admin_bar'     => 'Booking',
        'all_items'          => 'All Booking',
        'search_items'       => 'Search Booking',
        'parent_item_colon'  => 'Parent Booking:',
        'not_found'          => 'No bookings found.',
        'not_found_in_trash' => 'No bookings found in Trash.'
    );

    $args = array(
        'labels'              => $labels,
        'public'              => false,
        'publicly_queryable'  => false,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'query_var'           => true,
        'menu_icon'           => 'dashicons-calendar',
        'rewrite'             => array( 'slug' => 'booking' ),
        'capability_type'     => 'post',
        'has_archive'         => false,
        'hierarchical'        => false,
        'supports'            => array( 'title' ),
		'capabilities' => array(
            //'create_posts' => 'do_not_allow'
        )
    );
	add_filter( 'post_row_actions', 'remove_row_actions', 10, 1 );
	function remove_row_actions( $actions ) {
		if( get_post_type() === 'bhub-booking' )
			unset( $actions['edit'] );
			unset( $actions['view'] );
			unset( $actions['trash'] );
			unset( $actions['inline hide-if-no-js'] );
		return $actions;
	}
	add_action( 'admin_init', 'remove_cpt_submenus_add_newBooking' );
	function remove_cpt_submenus_add_newBooking()
	{
		global $submenu;
		unset(
			//$submenu['edit.php?post_type=bhub-booking'][5], 
			$submenu['edit.php?post_type=bhub-booking'][10] 
		);
		
		if (isset($_GET['post_type']) && $_GET['post_type'] == 'bhub-booking') {
			echo '<style type="text/css">
			//#favorite-actions, .add-new-h2, .tablenav { display:none; }
			.wrap .page-title-action { display:none !important; }
			.tablenav select option:nth-child(2){ 
			  display:none;
			}
			</style>';
		}
	}
	function bookingEdit1() {
		add_meta_box( 'meta-box-id', __( 'Add Booking', 'textdomain' ), 'bookingEdit2', 'bhub-booking' );
	}
	add_action( 'add_meta_boxes', 'bookingEdit1' );
	function bookingEdit2() {
		echo '<script>window.location="edit.php?post_type=bhub-booking"</script>';
	}

	function define_columns() {
		$columns = array(
			'cb' => '<input type="checkbox" />',
			'title' => __( 'Title' ),
			'shortCode' => __( 'Form Short Code' ),
			'totalEntry' => __( 'Total Entry' ),
			'date' => __( 'Date' ),
		);

		return $columns;
	}
	add_filter( 'manage_form_builder_posts_columns', 'define_columns' );

	function define_columns2() {
		$columns = array(
			'cb' => '<input type="checkbox" />',
			'title' => __( 'Title' ),
			'formDetails' => __( 'Form Details' ),
			'date' => __( 'Date' ),
		);

		return $columns;
	}
	add_filter( 'manage_bhub-booking_posts_columns', 'define_columns2' );
	
	add_action('manage_bhub-booking_posts_custom_column', function($column_key, $post_id) {
		if ($column_key == 'formDetails') {
			global $wpdb;
			$formId = get_post_meta($post_id, 'form_id', true);
			$querystr = "SELECT P.* FROM $wpdb->posts as P, $wpdb->postmeta as PM WHERE P.post_type = 'form_builder' AND PM.post_id = P.ID AND PM.meta_key = 'form_id' AND PM.meta_value = '".$formId."'";     
			//echo $querystr;
			$formRecoreds = $wpdb->get_results($querystr);
			$formData = get_post_meta($post_id, 'formData', true);
			
			if(count($formRecoreds) > 0) {
			
				$formFieldsData = get_post_meta($formRecoreds[0]->ID, 'formData', true);
				
				//echo $formId;
				//echo '<pre>';print_r($posts);echo '</pre>';
				$formFieldsDataArr = json_decode(base64_decode($formFieldsData), true);
				//echo '<pre>';print_r($formData);echo '</pre>';
				echo "<p><strong>Form Name: </strong> ".$formRecoreds[0]->post_title."</p>";
				echo '<table border="1">';
				for($h = 0; $h < count($formFieldsDataArr); $h++) {
					if(isset($formFieldsDataArr[$h]['key']) && !isset($formFieldsDataArr[$h]['action'])) {
			?>
					<tr>
						<th><strong>
							<?php 
								if(isset($formFieldsDataArr[$h]['label'])) {
									echo $formFieldsDataArr[$h]['label'];
									//echo '<pre>';print_r($formFieldsDataArr[$h]);echo '</pre>';
								}
								if(isset($formFieldsDataArr[$h]['dateLabel'])) {
									echo " ".$formFieldsDataArr[$h]['dateLabel'];
								}
								if(isset($formFieldsDataArr[$h]['timeLabel'])) {
									echo " ".$formFieldsDataArr[$h]['timeLabel'];
								}
							?>
							: </strong>
						</th>
						<td>
							<?php
								//$formData
								if(isset($formData[$formFieldsDataArr[$h]['key']])) {
									echo $formData[$formFieldsDataArr[$h]['key']];
								}
							?>
						</td>
					</tr>
			<?php 
					}
				}
				echo '</table>';
			}
		} else if ($column_key == 'totalEntry') {
			
			global $wpdb;
			$querystr = "SELECT * FROM $wpdb->posts WHERE `post_excerpt` = ".$post_id." AND `post_type` = 'bhub-booking'";     
			$posts = $wpdb->get_results($querystr);

			//echo count($posts).' <a href="edit.php?post_type=bhub-booking&page=bookinghub-datas&post_id='.$post_id.'">View All Data</a>';
			echo count($posts);
		}
	}, 10, 2);

	function my_cpt_columns( $columns) {
		$columns["shortCode"] = "Form Short Code";
		$columns['totalEntry'] = "Total Entry";
		return $columns;
	}
	add_filter('manage_form_builder_posts_columns', 'my_cpt_columns');
	
	add_action('manage_form_builder_posts_custom_column', function($column_key, $post_id) {
		if ($column_key == 'shortCode') {
			echo '<input type="text" value="[bhubForm form_id='.$post_id.']" readonly />';
		} else if ($column_key == 'totalEntry') {
			
			global $wpdb;
			$querystr = "SELECT * FROM $wpdb->posts WHERE `post_excerpt` = ".$post_id." AND `post_type` = 'bhub-booking'";     
			$posts = $wpdb->get_results($querystr);

			//echo count($posts).' <a href="edit.php?post_type=bhub-booking&page=bookinghub-datas&post_id='.$post_id.'">View All Data</a>';
			echo count($posts);
		}
	}, 10, 2);

    register_post_type( 'bhub-booking', $args );
    
	$labels1 = array(
        'name'               => 'Form Builder Lists',
        'singular_name'      => 'FormBuilder',
        'menu_name'          => 'Form Builder',
        'name_admin_bar'     => 'Form Builder',
        'add_new'            => 'Create New Form',
        'add_new_item'       => 'Add New Form',
        'new_item'           => 'New Form',
        'edit_item'          => 'Edit Form',
        'view_item'          => 'View Form Builder',
        'all_items'          => 'All Form Builder',
        'search_items'       => 'Search Form Builder',
        'parent_item_colon'  => 'Parent Form Builder:',
        'not_found'          => 'No Form found.',
        'not_found_in_trash' => 'No Form found in Trash.'
    );

    $args1 = array(
        'labels'              => $labels1,
        'public'              => false,
        'publicly_queryable'  => false,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'query_var'           => true,
        'menu_icon'           => 'dashicons-format-aside',
        'rewrite'             => array( 'slug' => 'FormBuilder' ),
        'capability_type'     => 'post',
        'has_archive'         => false,
        'hierarchical'        => false,
        'supports'            => array( 'title' )
    );
	
	register_post_type( 'form_builder', $args1 );
	
	function define_columns1() {
		$columns = array(
			'cb' => '<input type="checkbox" />',
			'title' => __( 'Title' ),
			'shortCode' => __( 'Form Short Code' ),
			'totalEntry' => __( 'Total Entry' ),
			'date' => __( 'Date' ),
		);

		return $columns;
	}
	add_filter( 'manage_bhub_form_builder_posts_columns', 'define_columns1' );

	function my_cpt_columns1( $columns) {
		$columns["shortCode"] = "Form Short Code";
		$columns['totalEntry'] = "Total Entry";
		return $columns;
	}
	add_filter('manage_bhub_form_builder_posts_columns', 'my_cpt_columns1');
	
	add_action('manage_bhub_form_builder_posts_custom_column', function($column_key, $post_id) {
		if ($column_key == 'shortCode') {
			echo '<input type="text" value="[bhubForm form_id='.$post_id.']" readonly />';
		} else if ($column_key == 'totalEntry') {
			
			global $wpdb;
			$querystr = "SELECT * FROM $wpdb->posts WHERE `post_excerpt` = ".$post_id." AND `post_type` = 'bhub-booking'";     
			$posts = $wpdb->get_results($querystr);

			//echo count($posts).' <a href="edit.php?post_type=bhub-booking&page=bookinghub-datas&post_id='.$post_id.'">View All Data</a>';
			echo count($posts);
		}
	}, 10, 2);
	
	function wpdocs_register_meta_boxes() {
		add_meta_box( 'meta-box-id', __( 'Create Form', 'textdomain' ), 'wpdocs_my_display_callback', 'form_builder' );
	}
	add_action( 'add_meta_boxes', 'wpdocs_register_meta_boxes' );
	function wpdocs_my_display_callback() {
		$postId = 0;
		if(isset($_REQUEST['post'])) {
			$postId = $_REQUEST['post'];
		}
		$formData = get_post_meta($postId, 'formData', true);
		$formId = get_post_meta($postId, 'form_id', true);
		if($formId == "") {
			$formId = 'Form_'.uniqid();
		}
		//echo 'Edit Post ID: '.$formId;
		$formDataArr = json_decode(base64_decode($formData), true);
		//echo base64_decode($formData);
		//echo '<pre>';print_r($formDataArr);echo '</pre>';
	?>
	<style>.fjs-editor-container{max-height:calc(100vh - 125px);}</style>
        <div id="bhub_form"></div>
		<script src="<?php echo BHUB_URL;?>assets/vendor/form-js//form-editor.umd.js"></script>
		<script>
		
			<?php if($formData == "") {?>

            const schema =  {
                "components": [
                {
                    "type": "text",
                    "text": "# Custom Booking Form"
                },
                {
                    "key": "first_name",
                    "label": "First Name",
                    "type": "textfield",
                    "validate": {
                    "required": true
                    },
                    "layout": {
                    "columns": 8,
                    "row": "Row_1"
                    }
                },
                {
                    "key": "last_name",
                    "label": "Last Name",
                    "type": "textfield",
                    "validate": {
                    "required": true
                    },
                    "layout": {
                    "columns": 8,
                    "row": "Row_1"
                    }
                },
                {
                    "key": "phone_number",
                    "label": "Phone Number",
                    "type": "textfield",
                    "validate": {
                    "required": true,
                    "validationType": "phone"
                    }
                },
                {
                    "key": "email_address",
                    "label": "Email Address",
                    "type": "textfield",
                    "validate": {
                    "required": true
                    }
                },
                {
                    "key": "amount",
                    "label": "Booking Amount",
                    "type": "number",
                    "validate": {
                    "min": 0,
                    "max": 1000
                    }
                },
                {
                    "key": "booking_datetime",
                    "type": "datetime",
                    "subtype": "datetime",
                    "dateLabel": "Booking Date",
                    "timeLabel": "Booking Time",
                    "timeSerializingFormat": "utc_normalized",
                    "timeInterval": 15,
                    "use24h": false
                },
                {
                    "key": "submit",
                    "label": "Submit",
                    "type": "button"
                },
                {
                    "action": "reset",
                    "key": "reset",
                    "label": "Reset",
                    "type": "button"
                }
                ],
                "type": "default",
				"id": "<?php echo $formId?>"
            };
            
			<?php } else {?>
				const schema = {
					"components":<?php echo base64_decode($formData);?>,
					"type": "default",
					"id": "<?php echo $formId?>"
				};
			<?php }?>
			const container = document.querySelector('#bhub_form');
            //const data = document.querySelector('#bhub_form');

            const formEditor = FormEditor.createFormEditor({
                container,
                schema
            });

            // get form data
            formEditor.then((formEditorInstance) => {
				const schema = formEditorInstance._state.schema;
				const components = schema.components;

				const componentsJSON = JSON.stringify(components);
				jQuery("#bhub_form").append('<input type="hidden" id="form_id" name="form_id" value="<?php echo $formId?>" /><input type="hidden" id="formData" name="formData" value="'+btoa(componentsJSON)+'" />');
				jQuery("#bhub_form").append('<p align="center"><input type="button" id="saveForm" name="saveForm" value="Save Form Data" /></p>');
				console.log(componentsJSON);
				//formEditor.importSchema(schema);
				
				//update_option('your_option_name', componentsJSON);
            }).catch((error) => {
                console.error(error);
            });
			
			jQuery(document).on('click', '#saveForm', function() {
				//alert('Hi');
				formEditor.then((formEditorInstance) => {
					const schema = formEditorInstance._state.schema;
					const components = schema.components;

					const componentsJSON = JSON.stringify(components);
					jQuery("#formData").val(btoa(componentsJSON));
					console.log(componentsJSON);
					//formEditor.importSchema(schema);
					
					//update_option('your_option_name', componentsJSON);
				}).catch((error) => {
					console.error(error);
				});
			});
			//formEditor.exportSchema(schema);
			/*formEditor.on('changed', ({ schema }) => {
				schema !== formEditor.saveSchema();
			});*/

			/*const components1 = schema.components;

			const componentsJSON1 = JSON.stringify(components1);
			jQuery("#bhub_form").append('<input type="hidden" id="formData" name="formData" value="'+componentsJSON1+'" />');
			console.log(componentsJSON1);*/
        </script>
	<?php
	}
	
	function wpdocs_save_meta_box( $post_id ) {
	
		//echo '<pre>';print_r($_POST);
		//echo base64_decode($_POST["formData"]);
		update_post_meta($post_id, "form_id", $_POST["form_id"]);
		update_post_meta($post_id, "formData", $_POST["formData"]);
		//die;
		//$wpdb->query($wpdb->prepare("UPDATE $table_name SET time='$current_timestamp' WHERE userid=$userid"));
	}
	add_action( 'save_post', 'wpdocs_save_meta_box' );

}

add_action( 'init', 'register_bookinghub_post_type' );

