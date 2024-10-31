<?php
/*
Plugin Name: Post 2 Tabs
Plugin URI: http://www.giuseppesurace.com/
Description: Simple plugin that allows you to generate a tab container widget based on Jquery UI tabs. Each tab is a category with a number of posts customizable. 
It will display last posts of your favorite categories just put each category ID number separated by comma.
Version: : 1.0
Author: Giuseppe Surace
Author URI: http://www.giuseppesurace.com
*/

register_activation_hook(__FILE__,'install');
add_action('wp_head', 'p2t_Jspush');
add_action('plugins_loaded','p2t_DisplayWidget');
add_action('admin_menu', 'p2t_options_menu');

$plugindir   = "post2tabs/";
$plugin_root = get_option('siteurl') . '/wp-content/plugins/'.$plugindir;


function p2t_options_menu() {
    add_menu_page('Post2tabs', 'Post 2 Tabs', 8, __FILE__, 'p2t_options_page');

}


function p2t_DisplayWidget()
{
    if ( !function_exists(
        'register_sidebar_widget') )
    {
        return;
    }

    register_sidebar_widget('Post2Tabs Widget','Post2Tabs');
	//register_widget_control('Post2tabs Widget','Post2Tabs_widget_options', 200, 200);
	
}

function wpcat_init_method() {
    wp_enqueue_script('jquery');            
	wp_enqueue_script('ui-core','/' . PLUGINDIR . '/post2tabs/js/ui.core.js',array('jquery'));
	wp_enqueue_script('ui-tabs','/' . PLUGINDIR . '/post2tabs/js/ui.tabs.js',array('jquery'));

}    

add_action('init', wpcat_init_method);

function p2t_Jspush() { 
		$plugindir   = "post2tabs/";
		$plugin_root = get_option('siteurl') . '/wp-content/plugins/'.$plugindir;
		
			?>
		<link rel="stylesheet" href="<?php echo $plugin_root; ?>js/ui.tabs.css" type="text/css" media="screen" />

		<script type="text/javascript">
		  jQuery(document).ready(function($){
		    $('.tabContainer > ul').tabs({ selected: 0 });
		  });

		</script>
<?php }


//funzione tronca testo
function p2t_Truncate($string, $limit, $break=".", $pad="...") { 
	if(strlen($string) <= $limit) return $string; // is $break present between $limit and the end of the string?  
	if(false !== ($breakpoint = strpos($string, $break, $limit))) { if($breakpoint < strlen($string) - 1) { $string = substr($string, 0, $breakpoint) . $pad; } } return $string; }



function Post2Tabs()
{
	global $wpdb;
	$plugindir   = "post2tabs/";
	$plugin_root = get_option('siteurl') . '/wp-content/plugins/'.$plugindir;
	$cat_ids=get_option(cat_ids);
	$categories = explode(",", $cat_ids);
	$table_name = $wpdb->prefix . "posts";            
	$n = get_option(number_of_posts);
	//div container
	$menu = '<div id="tabContainer" class="tabContainer">';
    $menu.=  '<ul>';
	foreach($categories as $category) 
	{			
		$this_category = get_category($category);
        $menu.=  '<li><a href="#fragment-'.$category.'"><span>'.$this_category->cat_name.'</span></a></li>';
	}   
    $menu.='</ul>';

	
	
	foreach($categories as $category) 
	{
		global $post;
		$myposts = get_posts('numberposts='.$n.'&category='.$category.'&order=DESC');
		$menu.='<div id="fragment-'.$category.'"><ul>';
			global $post;
			foreach($myposts as $post) {
				
					$menu.='<li><a href="'.get_permalink().'">'.$post->post_title.'</a></li>';
					//$menu.='<p>'.$post->post_content.'</p>';		
				 	//p2t_get_image($post->ID,50,50);
				 
			}
			$current_cat = get_category($category);
			
			$menu.='</ul>
			<p>visualizza tutti gli articoli in: <a href="'.get_category_link($category).'">'.$current_cat->cat_name.'</a></p>
			<small>
Powered by
<a href="http://www.giuseppesurace.com">Post 2 tabs Wordpress</a>
</small>
			</div>';
	}
	
	$menu.='</div>';
	
	echo $menu;
			
	}
	
	
	


/*widget options+++++++++++++++++++++++++++++++++++++*/
function p2t_widget_options() {
		if ($_POST['p2t_widget_cat_ids']) {
			$post_ids=$_POST['p2t_widget_cat_ids'];
			$widget_posts=$_POST['p2t_widget_posts'];
			$titolo=$_POST['p2t_widget_title'];
			update_option('p2t_widget_post_ids',$post_ids);
			update_option('p2t_widget_title',$_POST['widget_title']);
			update_option('p2t_widget_timeout',$_POST['timeout']);
		
			
		}
		$post_ids = get_option('widget_post_ids');
		$titolo = get_option('widget_title');
		//titolo
		echo '<p><label for="widget_title">'.__('Widget title: ', 'postslide').': <input id="widget_title" name="widget_title"  type="text" value="'.$titolo.'" /></label></p>';
		echo '<p><label for="widget_post_ids">'.__('post IDs', 'postslide').': <input id="widget_post_ids" name="widget_post_ids"  type="text" value="'.get_option(widget_post_ids).'" /></label></p>';
		

		
			
	}




// mt_options_page() displays the page content for the Test Options submenu
function p2t_options_page() {

    // variables for the field and option names 
    $post_ids_option = 'post_ids';
    $hidden_field_name = 'mt_submit_hidden';
    $data_field_name = 'post_ids';

  
    if( $_POST[$hidden_field_name] == 'Y' ) {
        // Read their posted value
        $cat_ids_val = $_POST['cat_ids'];
		$number_of_posts_val = $_POST['number_of_posts'];
	// Save the posted value in the database
        update_option('cat_ids', $cat_ids_val );
		update_option('number_of_posts', $number_of_posts_val );
		
        // Put an options updated message on the screen

?>
<div class="updated"><p><strong><?php _e('Options saved.', 'post2tabs' ); ?></strong></p></div>
<?php

    }
	// Read in existing option value from database
    $cat_ids = get_option('cat_ids');
	$number_of_posts = get_option('number_of_posts');




    // Now display the options editing screen

    echo '<div class="wrap">';

    // header

    echo "<h2>" . __( 'Post 2 UI Tabs Management', 'post2tabs' ) . "</h2>";

    // options form
    
    ?>

<form name="form1" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
<input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">
<table class="form-table">
<tbody>
<tr class="form-required">
<th valign="top" scope="row">
<label for="cat_ids"><?php _e("Category ID separated by comma:", 'post2tabs' ); ?></label>
</th><td>
<input type="text" name="cat_ids" value="<?php echo $cat_ids; ?>" size="20">
</p></td>
</tr>

<tr class="form-required">
<th valign="top" scope="row">
<label for="number_of_posts"><?php _e("How many posts for each tabs?:", 'post2tabs' ); ?></label>
</th><td>
<input type="text" name="number_of_posts" value="<?php echo $number_of_posts; ?>" size="20">
</p></td>
</tr>


<tr class="form-required">
<th valign="top" scope="row"></th>
<td>
<p class="submit">
<input type="submit" class="button-primary" name="Submit" value="<?php _e('Update Options', 'post2tabs' ) ?>" />
</p>
</td>
</tr>
</tbody>
</table>

</form>
</div>

<?php
 
}
?>