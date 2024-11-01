<?php


if ( ! defined( 'ABSPATH' ) ) exit; 

	if(!function_exists('wpss_pre')){
	function wpss_pre($data){
			if(isset($_GET['debug'])){
				wpss_pree($data);
			}
		}	 
	} 	
	if(!function_exists('wpss_pree')){
	function wpss_pree($data){
				echo '<pre>';
				print_r($data);
				echo '</pre>';	
		
		}	 
	} 
	function wpss_user_has_role($user_id, $role_name){
		$user_meta = get_userdata($user_id);
		$user_roles = $user_meta->roles;
		return in_array($role_name, $user_roles);
	}
	function sanitize_wpss_data( $input ) {

		if(is_array($input)){
		
			$new_input = array();
	
			foreach ( $input as $key => $val ) {
				$new_input[ $key ] = (is_array($val)?sanitize_wpss_data($val):sanitize_text_field( $val ));
			}
			
		}else{
			$new_input = sanitize_text_field($input);
			if(stripos($new_input, '@') && is_email($new_input)){
				$new_input = sanitize_email($new_input);
			}

			if(stripos($new_input, 'http') || wp_http_validate_url($new_input)){
				$new_input = esc_url($new_input);
			}
		}
		

		
		return $new_input;
	}		
	function wpss_admin_menu()
	{
		global $wpss_data, $wpss_url;		
		$title = str_replace(array('WooCommerce', 'WordPress', 'Synchronizer', 'Sage 100'), array('WC', 'WP', 'Sync', 'Sage'), $wpss_data['Name']);
		add_menu_page(
			$title,
			$title,
			'manage_options',
			'wpss_settings',
			'wpss_settings',
			$wpss_url.'/img/sage.png' ,
			6
		);
	}
	function wpss_settings(){ 
		if ( !current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.', 'sync-sage-100' ) );
		}
		global $wpdb; 
		include('wpss_settings.php');	
	}
	//add_action( 'wp_enqueue_scripts', 'wpss_enqueue_scripts' );
	add_action( 'admin_enqueue_scripts', 'wpss_enqueue_scripts' );
	
	function wpss_enqueue_scripts() 
	{
		global $wpss_options;

		$translation_array = array(

			'this_url' => admin_url( 'admin.php?page=wpss_settings' ),
			'wpss_tab' => (isset($_GET['t'])?$_GET['t']:'0'),
			'wpss_nonce' => wp_create_nonce('wpss_nonce_action'),
			'ajax_url' => admin_url( 'admin-ajax.php' ),

		);
		
		if(is_admin()){
			if(isset($_GET['page']) && in_array($_GET['page'], array('wpss_settings'))){
				
				wp_enqueue_style('fontawesome', plugins_url('css/fontawesome.min.css', dirname(__FILE__)));
				wp_enqueue_style('wpsu-slimselect-css', plugins_url('css/slimselect.min.css', dirname(__FILE__)) );
				wp_enqueue_script('wpsu-slimselect-scripts', plugins_url('js/slimselect.min.js', dirname(__FILE__)), array( 'jquery' ), date('Ym'), true );
	

				
				wp_enqueue_script('wpss-scripts', plugins_url('js/admin-scripts.js', dirname(__FILE__)), array( 'jquery' ), date('Ymdhi'), true );
				wp_enqueue_script('wpss-blockUI', plugins_url('js/jquery.blockUI.js', dirname(__FILE__)), array( 'jquery' ), date('Ymdhi'), true );
				
				
	
				wp_enqueue_script('bootstrap', plugins_url('js/bootstrap.min.js', dirname(__FILE__)), array( 'jquery' ), date('Ym'), true );
				wp_enqueue_style('bootstrap', plugins_url('css/bootstrap.min.css', dirname(__FILE__)), array(), date('Ym'));
				

				wp_localize_script('wpss-scripts', 'wpss_obj', $translation_array);


			}
			wp_enqueue_style('wpss-dashboard', plugins_url('css/dashboard-style.css', dirname(__FILE__)), array(), time(), 'all');
			if(
					((isset($_GET['page']) && in_array($_GET['page'], array('wpss_settings'))) 
				|| 
					(isset($_GET['post_type']) && in_array($_GET['post_type'], array('shop_order'))))
			){
				wp_enqueue_style('wpss-style', plugins_url('css/admin-style.css', dirname(__FILE__)), array(), time(), 'all');
			}
		}
				
	}
		
	
	function wpss_plugin_links($links) { 

		global $wpss_premium_link, $wpss_pro;


		$settings_link = '<a href="admin.php?page=wpss_settings">'.__('Settings', 'sync-sage-100').'</a>';

		
		if($wpss_pro){
			array_unshift($links, $settings_link); 
		}else{
			 
			$wpss_premium_link = '<a href="'.esc_url($wpss_premium_link).'" title="'.__('Go Premium', 'sync-sage-100').'" target="_blank">'.__('Go Premium', 'sync-sage-100').'</a>'; 
			array_unshift($links, $settings_link, $wpss_premium_link); 
		
		}
				
		
		return $links; 
	}	

	function wpss_dir_files($dir){
		$ret = array();
		if ($handle = @opendir($dir)) {
			while (false !== ($entry = readdir($handle))) {
				$entry_ext = explode('.', $entry);
				$entry_ext = end($entry_ext);
				
				if ($entry != "." && $entry != ".." && in_array($entry_ext, array('xlsx', 'xml', 'csv'))) {
					$ret[$entry_ext][] = $dir.$entry;
				}
			}
			closedir($handle);
		}
		
		return $ret;
	}
	
	

	if(!function_exists('wpss_update_settings')){
		function wpss_update_settings() {
			
			$ret = array();
			if(array_key_exists('action', $_POST) && $_POST['action']=='wpss_update_settings'){
				if (
					! isset( $_POST['nonce'] )
					|| ! wp_verify_nonce( $_POST['nonce'], 'wpss_nonce_action' )
				) {
					
				}else{
					$wpss_data = get_option('wpss_data');
					$key = sanitize_wpss_data($_POST['key']);
					$val = sanitize_wpss_data($_POST['val']);
					
					if($key){
						$wpss_data[$key] = $val;
						update_option('wpss_data', $wpss_data);
					}
				}
			}
			wp_send_json($ret);
		}
		add_action( 'wp_ajax_wpss_update_settings', 'wpss_update_settings' );
	}
	
	function wpss_get($key=''){
		$wpss_settings = get_option('wpss_data');
		return (array_key_exists('wpss-users-files', $wpss_settings)?$wpss_settings['wpss-users-files']:'');
	}
