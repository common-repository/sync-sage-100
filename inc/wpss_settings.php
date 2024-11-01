<?php defined( 'ABSPATH' ) or die( __('No script kiddies please!', 'sync-sage-100') );
	
	//if(!is_admin()){ require_once ABSPATH . 'wp-admin/includes/user.php'; }
	
	if ( !current_user_can( 'manage_options' ) ) {
		wp_die( __( 'You do not have sufficient permissions to access this page.', 'sync-sage-100' ) );
	}
	global $wpss_data, $wpss_pro, $wpss_premium_link, $wp_roles, $wpss_url, $wpss_options;
	$current_theme = str_replace(array(' ', '-'), '_', strtolower(get_option('current_theme')));
	$wpss_users_file = wpss_get('wpss-users-files');

?>
	
	


	
	
	<div class="container-fluid wpss_wrapper_div wrap">

        <div class="pl-3 row mb-2 mt-4">
            <div class="icon32" id="icon-options-general"><br></div><h4><i class="fas fa-cogs"></i> &nbsp;<?php echo esc_html($wpss_data['Name']); ?> <?php echo esc_html('('.$wpss_data['Version'].($wpss_pro?') Pro':')')); ?> - <?php echo __('Settings', 'sync-sage-100'); ?></h4>
			<?php if(!$wpss_pro && $wpss_premium_link): ?>
                <a href="<?php echo esc_url($wpss_premium_link); ?>" target="_blank" class="btn btn-info btn-sm" style="position:absolute; right:15px; width:100px; color:#fff;"><?php echo __('Go Premium', 'sync-sage-100'); ?></a>
			<?php endif; ?>
        </div>


        <h2 class="nav-tab-wrapper">
            <a class="nav-tab nav-tab-active"><i style="color:#008061" class="fas fa-users"></i> <?php echo __('Users', 'sync-sage-100'); ?></a>
            
            <a class="nav-tab" style="float:right"><i style="color:#FF151C" class="fas fa-headset"></i> <?php echo __('Help', 'sync-sage-100'); ?></a>
        </h2>

		

		<div class="nav-tab-content hide wpss_input_wrapper">
        
            

            <div class="wpss_setting_alert alert d-none w-75 mt-4 mb-4">
                <strong><?php _e('Success!', 'sync-sage-100'); ?></strong> <?php _e('Settings are updated successfully.', 'sync-sage-100'); ?>
            </div>

            <form class="wpss_users_form">

                <?php wp_nonce_field( 'wpss_nonce_action', 'wpss_nonce' ); ?>

                <?php 

                           

                ?>

                <div class="row mt-3">
                    <div class="col-md-12">
          
          
                    
<?php 
	$default_upload_dir              = wp_upload_dir();
	$default_upload_dir['basedir']   = str_replace( '\\', '/', $default_upload_dir['basedir'] );

	$wc_wpss_upload_dir = stripslashes(get_option( 'woocommerce_wpss_upload_dir', $default_upload_dir['basedir']));
	$wc_wpss_upload_dir = str_replace('\\', '/', $wc_wpss_upload_dir);
	
	
	if($wc_wpss_upload_dir!=''){
		
		$wc_wpss_upload_dir = explode('/', $wc_wpss_upload_dir);
		if(!empty($wc_wpss_upload_dir)){

			$nodes = $wc_wpss_upload_dir;
		
?>	
<style type="text/css">

</style>
<ul class="wpss_upload_dir_nodes wpss_download_section">
<?php
			$nodes_arr = array();
			for($d=0; $d<=count($nodes); $d++){
				
				

				$node_dir = '';
				for($di=0; $di<$d; $di++){
					$node_dir .= $nodes[$di].'/';
				}
				
				if($node_dir){
					$valid_status = is_dir($node_dir);
					$writable_status = is_writable($node_dir);
					$node_dir = str_replace(array('ORDER_ID'), array(''), $node_dir);
					$node_dir = str_replace(array('//'), array('/'), $node_dir);
					if(!in_array($node_dir, $nodes_arr)){
						$nodes_arr[] = $node_dir;
?>			
<li class="<?php echo ($valid_status?'valid_node':'invalid_node'); ?> <?php echo ($writable_status?'writable':'not_writable'); ?> <?php echo $wc_wpss_upload_dir==$node_dir?'active':'';?>">
<?php 					
					echo esc_url($node_dir);
					
					
					
					echo '<div class="wpss_legends">';
					echo '<span>'.esc_html($valid_status?'Valid Directory':'Invalid Directory').'</span>';
					echo '&nbsp;|&nbsp;<span class="writable_status">'.esc_html($writable_status?'':'Not').' Writable</span>';
					echo '</div>';
					
					$dir_files = wpss_dir_files($node_dir);
					if(!empty($dir_files)){
?>
<ul>
<?php						
						foreach($dir_files as $file_ext => $files){
?>
<li>
<?php													
							if(!empty($files)){
								foreach($files as $file){
?>
<a class="wpss-file-selection" data-file="<?php echo esc_attr($file); ?>"><?php echo '<span>'.esc_html($node_dir).'</span>'.esc_html(basename($file)); ?> <?php echo ($wpss_users_file==$file?'<i class="fas fa-check-circle"></i>':''); ?></a>
<?php							
								}
							}
?>
</li>
<?php													
						}
?>
</ul>
<?php												
					}

 ?>
</li>
<?php
					}
				}
			}
?>		
</ul>
<?php 
		}
	}
?>	
<?php
	$SimpleXLSX = WPSS_PLUGIN_DIR.'/lib/SimpleXLSX.php';
if($wpss_users_file && file_exists($wpss_users_file) && file_exists($SimpleXLSX)){
?>

<?php	
	if(array_key_exists('action', $_GET) && $_GET['action']=='wpss-load-user-file'){
	@include_once($SimpleXLSX);
	
	if(class_exists('SimpleXLSX')){
		if ( $xlsx = SimpleXLSX::parse($wpss_users_file) ) {
		  $all_rows = $xlsx->rows();
		  $all_columns = current($all_rows);
		  if(!empty($all_columns)){
?>
<a class="btn btn-secondary wpss-select-file-fields"><?php _e('Click here to select relevant fields from total of', 'sync-sage-100'); ?> <?php echo esc_html(count($all_columns)); ?> <?php _e('fields', 'sync-sage-100'); ?>.</a>
<ul class="wpss-column-selection">
<?php			  
			  foreach($all_columns as $column){
?>
<li><label for="col-<?php echo esc_attr($column); ?>"><input id="col-<?php echo esc_attr($column); ?>" type="checkbox" name="consider_columns[]" value="<?php echo esc_attr($column); ?>" /> <?php echo esc_html($column); ?></label></li>
<?php				  
			  }
?>
</ul>
<?php			  
			  
		  }
		} else {
		  echo esc_html(SimpleXLSX::parseError());
		}
	}else{
		echo '<small>'.esc_html($SimpleXLSX).'<br />SimpleXLSX not found.</small>';
	}
	
	}else{
?>
<a class="btn btn-success wpss-load-user-file"><?php echo __('File', 'sync-sage-100').' "'.esc_html(basename($wpss_users_file)).'" '.__('found', 'sync-sage-100').'. '.__('Click here to load file data.', 'sync-sage-100'); ?></a>
<?php		
	}
	
}

?>                    
                    </div>
                    
                </div>

            </form>
        
        </div>

        

        

		            

        <div class="nav-tab-content hide">
        
            <div class="row mt-3">
                <div class="col-md-12">                
        

 <ul class="position-relative m-0 p-0">
            <li><a class="btn btn-sm btn-info" href="https://wordpress.org/support/plugin/<?php echo esc_attr(basename($wpss_url)); ?>/" target="_blank"><?php _e('Open a Ticket on Support Forums', 'sync-sage-100'); ?> &nbsp;<i class="fas fa-tag"></i></a></li>
            <li><a class="btn btn-sm btn-warning" href="http://demo.androidbubble.com/contact/" target="_blank"><?php _e('Contact Developer', 'sync-sage-100'); ?> &nbsp;<i class="fas fa-headset"></i></a></li>
            <?php if($wpss_premium_link): ?><li><a class="btn btn-sm btn-secondary" href="<?php echo esc_attr($wpss_premium_link); ?>/?help" target="_blank"><?php _e('Need Urgent Help?', 'sync-sage-100'); ?> &nbsp;<i class="fas fa-phone"></i></i></a></li><?php endif; ?>
            <li style="display:none"><iframe width="560" height="315" src="https://www.youtube.com/embed/VyaF_20bg2U" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></li>
        </ul>
                            
                </div>
            </div>
        
        </div>

        


	</div>

    <script type="text/javascript" language="javascript">

        jQuery(document).ready(function($){


            <?php if(isset($_GET['t'])): $t = sanitize_wpss_data($_GET['t']); ?>

                $('.nav-tab-wrapper .nav-tab:nth-child(<?php echo esc_attr($t+1); ?>)').click();

            <?php endif; ?>

        });

    </script>
    <style type="text/css">
		.woocommerce-message, .update-nag, #message, .notice.notice-error, .error.notice, div.notice, div.fs-notice, div.wrap > div.updated{ display:none !important; }
	</style>