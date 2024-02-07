<?php
/**
 * Class Bookinghub_Datas
 *
 * Configure the plugin bookinghub-datas page.
 */
class BookingHub_Bookinghub_Datas {

	/**
	 * The Plugin Settings constructor.
	 */
	function __construct() {
		//add_action( 'admin_init', [$this, 'settings_init'] );
		//add_action( 'admin_init', [$this, 'settings_init'] );
		add_action( 'admin_menu', [$this, 'options_page'] );
	}
	
	function options_page() {
        add_submenu_page(
            'edit.php?post_type=bhub-booking',
            __('', ''),
            __('', ''),
            'manage_options',
            'bookinghub-datas',
            [$this,'bookinghub_datas']
        );
    
    }

	/**
	 * Render the settings page.
	 */
	function bookinghub_datas() : void {
		$post_id = 0;
		if(isset($_REQUEST['post_id'])) {
			$post_id = trim($_REQUEST['post_id']);
		}
		global $wpdb;
		$querystr = "SELECT * FROM $wpdb->posts WHERE `post_excerpt` = ".$post_id." AND `post_type` = 'bhub-booking'";     
		//echo $querystr;
		$posts = $wpdb->get_results($querystr);
		$formData = get_post_meta($post_id, 'formData', true);
		$formId = get_post_meta($post_id, 'form_id', true);
		//echo '<pre>';print_r($posts);echo '</pre>';
		$formDataArr = json_decode(base64_decode($formData), true);
		/*for($n = 0; $n < count($formDataArr); $n++) {
			$formDataArr[$n]['field_action'] = $formDataArr[$n]['action'];
		}*/
		//echo '<pre>';print_r($formDataArr);echo '</pre>';
?>
		<div class="wrap">
			<h1 style="float:left;">Form ID: <?php echo $formId;?></h1>
			<h2 style="float:right;"><a href="edit.php?post_type=bhub-booking">Back to All Forms</a></h2>
			<?php if(count($posts) > 0) {?>
			<table width="100%" border="1">
				<thead>
					<tr>
					<?php 
						for($h = 0; $h < count($formDataArr); $h++) {
							if(isset($formDataArr[$h]['key']) && !isset($formDataArr[$h]['action'])) {
					?>
								<th>
									<?php 
										if(isset($formDataArr[$h]['label'])) {
											echo $formDataArr[$h]['label'];
											//echo '<pre>';print_r($formDataArr[$h]);echo '</pre>';
										}
										if(isset($formDataArr[$h]['dateLabel'])) {
											echo " ".$formDataArr[$h]['dateLabel'];
										}
										if(isset($formDataArr[$h]['timeLabel'])) {
											echo " ".$formDataArr[$h]['timeLabel'];
										}
									?>
								</th>
					<?php 
							}
						}
					?>
					</tr>
				</thead>
				<tbody>
					<?php for($r = 0; $r < count($posts); $r++) {?>
						<tr>
							<?php 
								for($h1 = 0; $h1 < count($formDataArr); $h1++) {
									if(isset($formDataArr[$h1]['key']) && !isset($formDataArr[$h1]['action'])) {
							?>
								<td>
									<?php 
										//echo $formDataArr[$h1]['label'];
										$postArr = (array)$posts[$r];
										$itmArr = json_decode($postArr['post_content'], true);
										//echo '<pre>';print_r($itmArr);echo '</pre>';
										if(isset($itmArr[$formDataArr[$h1]['key']])) {
											echo $itmArr[$formDataArr[$h1]['key']];
										}
									?>
								</td>
							<?php 
									}
								}
							?>
						</tr>
					<?php }?>
				</tbody>
			</table>
			<?php } else {?>
			<div style="display: inline-block;width: 100%;padding: 20px 0px;border: 1px solid;">
				<h3 align="center">Not any Data.<h3>
			</div>
			<?php }?>
		</div>
		<?php
	}

}

new BookingHub_Bookinghub_Datas();

