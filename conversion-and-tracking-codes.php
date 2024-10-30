<?php
/*
Plugin Name:  Conversion and Tracking Codes
Plugin URI:   https://codeflask.com/conversion-and-tracking-codes/
Description:  Put your google analyticscode. You can also put your conversion code page wise.
Version:      1.0.5
Author:       Code Flask
Author URI:   https://codeflask.com/
License:      GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
Text Domain:  conversion-and-tracking-codes
*/

function catc_add_css_and_my_js_files_admin() {
    wp_enqueue_style( 'catc-conversion-and-tracking-codes-style', plugins_url('assets/css/style.css', __FILE__), false, time(), 'all');
	wp_enqueue_script('catc-conversion-and-tracking-codes-script', plugins_url('assets/js/custom.js', __FILE__), array('jquery'), time(), true);
}

add_action( 'admin_init','catc_add_css_and_my_js_files_admin');

function catc_theme_settings_page()
{
    ?>
	    <div class="wrap">
	    <h1>Header and Footer Tracking Codes</h1>
	    <form method="post" action="options.php" enctype="multipart/form-data">
	        <?php
	            settings_fields("section");
	            do_settings_sections("theme-options");      
	            submit_button(); 
	        ?>          
	    </form>
		</div>
	<?php
}

function catc_page_wise_conversion_page()
{
    ?>
	    <div class="wrap">
	    <h1>Page Wise Conversion Tracking</h1>
		<form method="post" action="options.php" enctype="multipart/form-data">
	        <?php
	            settings_fields("section_page_codes");
	            do_settings_sections("theme-options_page_codes");      
	            submit_button(); 
	        ?>          
	    </form>
		</div>
	<?php
}

function catc_add_theme_menu_item()
{
	add_menu_page("Header and Footer Tracking Codes", "Header and Footer Tracking Codes", "manage_options", "conversion-and-tracking-codes", "catc_theme_settings_page", null, 99);
    add_submenu_page( 'conversion-and-tracking-codes', 'Page Wise Conversion Tracking', 'Page Wise Conversion Tracking', 'manage_options', 'conversion-tracking-page-wise', 'catc_page_wise_conversion_page');
}

add_action("admin_menu", "catc_add_theme_menu_item");

function catc_display_text_header_codes()
{
	?>
		<div class="sample_code_head">
		<pre>
			&lt;script async src="https://www.googletagmanager.com/gtag/js?id=AW-<span style="background-color:yellow;>">123456789</span>">&lt;/script&gt;
			&lt;script&gt;
				window.dataLayer = window.dataLayer || [];
				function gtag(){dataLayer.push(arguments);}
				gtag('js', new Date());
				gtag('config', 'AW-<span style="background-color:yellow;>">123456789</span>');
			&lt;/script&gt;
		</pre>
		Enter the GA code highlighted in <span style="background-color:yellow;>">yellow.</span> Like in the above sample code.
		<input type="text" name="catc_header_tracking_codes" id="catc_header_tracking_codes" value="<?php echo esc_html(esc_attr(get_option('catc_header_tracking_codes'))); ?>" />
		</div>
	<?php
}
function catc_display_text_no_of_pages()
{
	?>
		<input min="1" type="number" max="25" name="catc_no_of_pages" id="catc_no_of_pages" value="<?php echo esc_html(esc_attr(get_option('catc_no_of_pages'))); ?>" />
	<?php
}

function catc_display_text_page_name( array $args )
{
	$argsp = array(
		'sort_order' => 'asc',
		'sort_column' => 'post_title',
		'post_type' => 'page',
		'post_status' => array('publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit', 'trash')
	); 
	$pages = get_pages($argsp);
		echo '<select name="catc_select_page'.esc_html(esc_attr($args['num'])).'" id="catc_select_page'.esc_html(esc_attr($args['num'])).'"><option value="0">== Select Page ==</option>';
		foreach($pages as $pg)
		{
			if(get_option('catc_select_page'.$args['num'])==$pg->ID){
				echo '<option selected value="'.esc_html(esc_attr($pg->ID)).'">'.esc_html(esc_attr(get_the_title($pg->ID))).'</option>';
			}else{
				echo '<option value="'.esc_html(esc_attr($pg->ID)).'">'.esc_html(esc_attr(get_the_title($pg->ID))).'</option>';
			}
		}
		echo '</select>';
	?>
	<?php
}

function catc_display_text_page_code( array $args )
{
	?>
		<div class="sample_code_head_page">
			<code class="sample_code_head_page_snippet">
			&lt;script&gt;
				gtag('event', 'conversion', {'send_to': '<span style="background-color:yellow;>">AW-123456789/QcXZCJjSmM0DEOr5r123</span>'});
			&lt;/script&gt;
			</code>
			Enter the GA code highlighted in <span style="background-color:yellow;>">yellow.</span> Like in the above sample code
			<input type="text" style="width:auto;" name="catc_page_code<?php echo esc_html(esc_attr($args['num']));?>" id="catc_page_code<?php echo esc_html(esc_attr($args['num']));?>" value="<?php echo esc_html(esc_attr(get_option('catc_page_code'.$args['num']))); ?>" />
		</div>
	<?php
}

function catc_display_theme_panel_fields()
{
	add_settings_section("section", "", null, "theme-options");
	add_settings_field("catc_header_tracking_codes", "Header Codes <br>(Scripts before closing head tag)", "catc_display_text_header_codes", "theme-options", "section");
	add_settings_field("catc_no_of_pages", "No of Pages for Conversion Code", "catc_display_text_no_of_pages", "theme-options", "section");
	register_setting("section", "catc_header_tracking_codes");
	register_setting("section", "catc_no_of_pages");

	add_settings_section("section_page_codes", "", null, "theme-options_page_codes");

	if (!get_option('catc_no_of_pages')) {
		$kend=1;
	}else{
		$kend=get_option('catc_no_of_pages');
	}
	for($k=1;$k<=$kend;$k++){
		$args['num']=$k;
		add_settings_field("catc_select_page".$k, "Select Page ".$k, "catc_display_text_page_name", "theme-options_page_codes", "section_page_codes",$args);
		add_settings_field("catc_page_code".$k, "Page Conversion Code ".$k, "catc_display_text_page_code", "theme-options_page_codes", "section_page_codes",$args);
		register_setting("section_page_codes", "catc_select_page".$k);
		register_setting("section_page_codes", "catc_page_code".$k);
	}
}

add_action("admin_init", "catc_display_theme_panel_fields");


add_action('wp_head', 'catc_header_code_snippet');
function catc_header_code_snippet(){
echo '<script async src="https://www.googletagmanager.com/gtag/js?id=AW-'.esc_html(esc_attr(get_option('catc_header_tracking_codes'))).'"></script>
<script>
	window.dataLayer = window.dataLayer || [];
	function gtag(){dataLayer.push(arguments);}
	gtag(\'js\', new Date());
	gtag(\'config\', \'AW-'.esc_html(esc_attr(get_option('catc_header_tracking_codes'))).'\');
</script>';
}

add_action('wp_footer', 'catc_body_code_snippet');
function catc_body_code_snippet(){
	if (!get_option('catc_no_of_pages')) {
		$kendf=1;
	}else{
		$kendf=get_option('catc_no_of_pages');
	}
	for($k=1;$k<=$kendf;$k++){
		$page_id = get_queried_object_id();
		if($page_id==get_option('catc_select_page'.$k)){
			echo "<script> gtag('event', 'conversion', {'send_to': '".esc_html(esc_attr(get_option('catc_page_code'.$k)))."'}); </script>";
		}
	}
}

add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'add_action_links' );

function add_action_links ( $actions ) {
   $mylinks = array(
      '<a href="' . admin_url( 'admin.php?page=conversion-and-tracking-codes' ) . '">Settings</a>',
   );
   $actions = array_merge( $actions, $mylinks );
   return $actions;
}