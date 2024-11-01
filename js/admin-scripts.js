jQuery(document).ready(function ($) {
	
	
	$('.wpss_wrapper_div a.nav-tab').click(function(){
	
		$(this).siblings().removeClass('nav-tab-active');
		$(this).addClass('nav-tab-active');
		// , form:not(.wrap.wc_settings_div .nav-tab-content)'
		$('.nav-tab-content').hide();
		$('.nav-tab-content').eq($(this).index()).show();
		window.history.replaceState('', '', wpss_obj.this_url+'&t='+$(this).index());
		$('form input[name="wpss_tn"]').val($(this).index());
		wpss_obj.wpss_tab_tab = $(this).index();
		wpss_obj.wpss_tab = $(this).index();
	
	});
	
	function parse_query_string(query) {
	  var vars = query.split("&");
	  var query_string = {};
	  for (var i = 0; i < vars.length; i++) {
		var pair = vars[i].split("=");
		// If first entry with this name
		if (typeof query_string[pair[0]] === "undefined") {
		  query_string[pair[0]] = decodeURIComponent(pair[1]);
		  // If second entry with this name
		} else if (typeof query_string[pair[0]] === "string") {
		  var arr = [query_string[pair[0]], decodeURIComponent(pair[1])];
		  query_string[pair[0]] = arr;
		  // If third or later entry with this name
		} else {
		  query_string[pair[0]].push(decodeURIComponent(pair[1]));
		}
	  }
	  return query_string;
	}	
		
	var query = window.location.search.substring(1);
	var qs = parse_query_string(query);		
	
	if(typeof(qs.t)!='undefined'){
		$('.wpss_wrapper_div a.nav-tab').eq(qs.t).click();
		
	}
	
	$('body').on('click', '.wpss_upload_dir_nodes a.wpss-file-selection', function(){
		var path = $(this).data('file');
		wpss_data_update('wpss-users-files', path);
	});

	function wpss_data_update(key, val){
		$.blockUI({message:''});
		var data = {
					'action': 'wpss_update_settings',
					'nonce': wpss_obj.wpss_nonce,
					'key':key,
					'val':val
			};

		$.post(wpss_obj.ajax_url, data, function(resp){
			document.location.reload();
		});		
	}
	
	$('a.wpss-load-user-file').click(function(){
		document.location.href = wpss_obj.this_url+'&t=0&action=wpss-load-user-file';
	});
	$('a.wpss-select-file-fields').click(function(){
		$('.wpss-column-selection').show();
		$(this).hide();
	});
	

});