<?php
/**
* Plugin Name: Social Bookmarking Buttons
* Plugin URI: http://wordpress.org
* Description: Adds social bookmarking buttons with count to the end of each post for users to share your posts. HTML validated and fully cross-browser compatible.
* Version: 1.0
*
* Author: Not Found
* Author URI: http://downloadplugin.wordpress.com/
* License: GPL
*/

if (!defined(sbb_INIT)) define('sbb_INIT', 1);
else return;

// create custom plugin settings menu
add_action('admin_menu', 'sbb_create_menu');
add_filter('plugin_action_links', 'plugin_links', 10, 2 );

function sbb_create_menu() {

	//create new top-level menu
	add_submenu_page('options-general.php','Social Bookmarking Buttons Settings', 'Social Bookmarking Buttons', 'administrator', __FILE__, 'sbb_admin');

if (((float)substr(get_bloginfo('version'),0,3)) >= 2.7) {
  if (is_admin()){
	//call register settings function
	add_action( 'admin_init', 'register_sbb' );
  }
}
}

function register_sbb() {
//register our settings
register_setting( 'sbb', 'sbb_button_digg');
register_setting( 'sbb', 'sbb_button_tweet');
register_setting( 'sbb', 'sbb_button_like');
register_setting( 'sbb', 'sbb_button_stumble');
register_setting( 'sbb', 'sbb_display_post');
register_setting( 'sbb', 'sbb_display_page');
register_setting( 'sbb', 'sbb_display_home');
register_setting( 'sbb', 'sbb_vertical');
}

function plugin_links($links, $file) {
$this_plugin = plugin_basename(__FILE__);

    if ($file == $this_plugin){
        $settings_link = '<a href="admin.php?page=share-post/share-post.php">Settings</a>';
        array_unshift($links, $settings_link);
    }
    return $links;
}

function sbb_admin(){
add_option( 'sbb_button_digg', 'digg' );
add_option( 'sbb_button_tweet', 'tweet' );
add_option( 'sbb_button_like', 'like' );
add_option( 'sbb_button_stumble', 'stumble' );
add_option( 'sbb_display_post', 'post' );
add_option( 'sbb_display_page', false );
add_option( 'sbb_display_home', false );
add_option( 'sbb_vertical', true );  
?>
<div class="wrap">
<h2>Social Bookmarking Buttons Options</h2>

<form method="post" action="options.php">
<?php wp_nonce_field('update-options'); ?>
<table class="form-table">
<tr valign="top">
<th scope="row">Buttons to Display</th>
<td>Digg: <input type="checkbox" value="digg" name="sbb_button_digg" <?php if(get_option('sbb_button_digg')=='digg')echo 'checked'; ?> /></td>
<td>Tweet: <input type="checkbox" value="tweet" name="sbb_button_tweet"  <?php if(get_option('sbb_button_tweet')=='tweet') echo 'checked'; ?> /></td>
<td>Like: <input type="checkbox" value="like" name="sbb_button_like" <?php if(get_option('sbb_button_like')=='like') echo 'checked'; ?> /></td>
<td>StumbleUpon: <input type="checkbox" value="stumble" name="sbb_button_stumble" <?php if(get_option('sbb_button_stumble')=='stumble') echo 'checked'; ?> /></td>
</tr> 
<tr valign="top">
<th scope="row">Layout</th>
<td>Vertical Count: <input type="radio" value="vertical" name="sbb_vertical" <?php if(get_option('sbb_vertical')=='vertical') echo 'checked'; ?> /></td>
<td>Horizontal Count: <input type="radio" value="horizontal" name="sbb_vertical" <?php if(get_option('sbb_vertical')=='horizontal') echo 'checked'; ?> /></td>
</tr> 
<tr valign="top">
<th scope="row">Where to Display</th>
<td>Post: <input type="checkbox" value="post" name="sbb_display_post" <?php if(get_option('sbb_display_post')=='post') echo 'checked'; ?> /></td>
<td>Page: <input type="checkbox" value="page" name="sbb_display_page" <?php if(get_option('sbb_display_page')=='page') echo 'checked'; ?> /></td>
<td>Homepage: <input type="checkbox" value="home" name="sbb_display_home" <?php if(get_option('sbb_display_home')=='home') echo 'checked'; ?> /></td>
</tr>
</table>
<input type="hidden" name="action" value="update" />
<input type="hidden" name="page_options" value="sbb_button_digg,sbb_button_tweet,sbb_button_like,sbb_button_stumble,sbb_display_post,sbb_display_page,sbb_display_home,sbb_vertical" />
<p class="submit">
<input type="submit" class="button-primary" value="Save Changes" />
</p>
</form>
</div>
<?php
}

function sbb_footer(){
if((get_option('sbb_display_post',true) && is_single()) || (get_option('sbb_display_page') && is_page()) || (get_option('sbb_display_home') && is_home())) {
echo '<script type="text/javascript">';
if(get_option('sbb_button_digg',true))
	echo '(function(){var s=document.createElement("SCRIPT"),s1=document.getElementsByTagName("SCRIPT")[0];s.type="text/javascript";s.async=true;s.src="http://widgets.digg.com/buttons.js";s1.parentNode.insertBefore(s,s1)})();';
if(get_option('sbb_button_twitter',true))
	echo '(function(){var s=document.createElement("SCRIPT"),s1=document.getElementsByTagName("SCRIPT")[0];s.type="text/javascript";s.async=true;s.src="http://platform.twitter.com/widgets.js";s1.parentNode.insertBefore(s,s1)})();';
echo '</script>';
}
}

function sbb_buttons($content)
{
if(get_option('sbb_vertical')=='horizontal'){
$vh=array('Compact','','button');
$h=true;
}else{
$vh=array('Medium',' data-count="vertical"','box');
$h=false;
}

if((get_option('sbb_display_post',true) && is_single()) || (get_option('sbb_display_page') && is_page()) || (get_option('sbb_display_home') && is_home()) && (get_option('sbb_button_digg',true)||(get_option('sbb_button_tweet',true))||(get_option('sbb_button_like',true)))) {
global $is_IE;
$output='<ul style="clear:both;height:'.($h ? '21' : '68').'px;width:100%;overflow:hidden;">';
$purl=get_permalink();$url=urlencode($purl);

// Digg
if(get_option('sbb_button_digg',true)){
$style='display:inline;float:left;margin:0 20px 0 0;padding:0;list-style-type:none;';
$output.='<li style="'.$style.'"><span class="digg-button"><a class="DiggThisButton Digg'.$vh[0].'" href="http://digg.com/submit?url='.$url.'&amp;related=no"></a></span></li>';
}

// Twitter
if(get_option('sbb_button_tweet',true)){
$style='display:inline;float:left;margin:0 20px 0 0;padding:0;list-style-type:none;'.($h ? 'width:90px;' : 'width:55px;' );
$output .='<li style="'.$style.'"><a href="http://twitter.com/share" class="twitter-share-button"'.$vh[1].'>Tweet</a></li>';
}

// StumbleUpon
if(get_option('sbb_button_stumble',true)){
$divstyle='display:inline;float:left;margin:0 20px 0 0;padding:0;list-style-type:none;';
$style='border:none;overflow:hidden;width:'.($h ? '74' : '50').'px;height:'.($h ? '18' : '60').'px;';
if (!$is_IE)
$output.='<li style="'.$divstyle.'"><object style="'.$style.'" data="http://www.stumbleupon.com/badge/embed/'.( $h ? '1' : '5' ).'/?url='.$url.'"></object></li>';
else
$output.='<li style="'.$divstyle.'"><iframe style="'.$style.'" src="http://www.stumbleupon.com/badge/embed/'.( $h ? '1' : '5' ).'/?url='.$url.'" scrolling="no" frameborder="0" allowTransparency="true"></iframe></li>';
}

// Facebook
if(get_option('sbb_button_like',true)){
$divstyle='display:inline;float:left;margin:0 20px 0 0;padding:0;'.( $h ? 'width:75px;' : 'width:46px;margin-right:20px;').'list-style-type:none;
$style='display:inline;border:none;overflow:hidden;width:'.( $h ? 75 : 46 ).'px;height:'.($h ? '21' : '68').'px;';
$fburl= $url.'&amp;layout='.$vh[2].'_count&amp;show_faces=false&amp;width=71&amp;action=like&amp;colorscheme=light&amp;font=arial';
if (!$is_IE)
$output.='<li style="'.$divstyle.'"><object style="'.$style.'" data="http://www.facebook.com/plugins/like.php?href='.$fburl.'"></object></li>';
else
$output.='<li style="'.$divstyle.'"><iframe style="'.$style.'" src="http://www.facebook.com/plugins/like.php?href='.$fburl.'" scrolling="no" frameborder="0" allowTransparency="true"></iframe></li>';
}

$output.='</ul>';
return $content.$output;
}else{
return $content;
}
}

add_action('wp_footer','sbb_footer');
add_filter('the_content','sbb_buttons');
?>