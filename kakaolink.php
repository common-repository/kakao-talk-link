<?php
/* 
Plugin Name: Kakao Talk Link
Plugin URI: http://syncst.com/?page_id=113
Description: Send Contents Link to Kakao Talk App
Author: Jongmyoung Kim 
Version: 1.6
Author URI: http://icansoft.com/ 
License: GPL2
*/

/* Copyright 2013 Jongmyoung Kim (email : kimsreal@gmail.com)
 This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
*/

add_action('init', 'my_plugin_init');
add_filter('the_content', 'kakaolink');
add_action('admin_menu', 'kk_menu');

function my_plugin_init(){
  wp_enqueue_script('jquery');
  wp_enqueue_script('kakao', plugins_url( 'kakao.link.js', __FILE__ ));
}

function kakaolink ($content) {
	
	$option = kk_get_options_stored();
	
	//$strKakaoSend = "kakao.link('talk').send({ msg : '".get_the_title()."', url : '".get_permalink()."', appid : 'syncst', appver : '1.0', appname : 'Syncst', type : 'link'});";
	
	$strBlogInfo = get_bloginfo('name');
	$strBlogInfo = str_replace("&#039;", "", $strBlogInfo);
	$strTitle = get_the_title();
	$strTitle = str_replace("&#039;", "", $strTitle);

	$out .="<div class='kakaotalk_link' style='float:".$option['position'].";'><a href=\"javascript:SendKakao('".$strBlogInfo."', '".$strTitle."', '".get_permalink()."');\"><img src='".plugins_url( 'kakaotalk.png', __FILE__ )."' alt='Smart phone only'></a></div>";
	return $content.$out;
}

function kk_menu() {
	add_options_page('Kakao Talk Link Option', 'Kakao Talk Link', 'manage_options', 'kk_options', 'kk_options');
}

function kk_options () {

	$option_name = 'kk_options';

	$out = '';
	
	// See if the user has posted us some information
	if( isset($_POST['kk_position'])) {
		$option = array();

		$option['position'] = esc_html($_POST['kk_position']);
		
		update_option($option_name, $option);
		// Put a settings updated message on the screen
		$out .= '<div class="updated"><p><strong>'.__('Settings saved.', 'menu-test' ).'</strong></p></div>';
	}
	
	//GET ARRAY OF STORED VALUES
	$option = kk_get_options_stored();
	
	$sel_left = ($option['position']=='left') ? 'selected="selected"' : '';
	$sel_right  = ($option['position']=='right' ) ? 'selected="selected"' : '';

  // SETTINGS FORM
	$out .= '
	<div class="wrap">
		<h2>'.__( 'Kakao Talk Link', 'menu-test' ).'</h2>
		<div id="poststuff" style="padding-top:10px; position:relative;">
			<div style="float:left; width:74%; padding-right:1%;">
			
				<form id="kk_form" name="form1" method="post" action="">
					<div class="postbox">
						<h3>'.__("General options", 'menu-test' ).'</h3>
						<div class="inside">
							<table>
							<tr><td>'.__("Position", 'menu-test' ).':</td>
							<td><select name="kk_position">
								<option value="left" '.$sel_left.' > '.__('left', 'menu-test' ).'</option>
								<option value="right"  '.$sel_right.'  > '.__('right', 'menu-test' ).'</option>
								</select>
							</td></tr>
							</table>
						</div>
					</div>
					<p class="submit">
						<input type="submit" name="Submit" class="button-primary" value="'.esc_attr('Save Changes').'" />
					</p>
				</form>
				
			</div>
		</div>
	</div>
	';
	echo $out;
}

function kk_get_options_stored () {
	//GET ARRAY OF STORED VALUES
	$option = get_option('kk_options');
	 
	if ($option===false)
	{
		$option = kk_get_options_default();
		add_option('kk_position', $option);
	}
	else if(!is_array($option))
	{
		$option = json_decode($option, true);
	}
	
	return $option;
}

function kk_get_options_default ($position='right') {
	$option = array();
	$option['position'] = $position;
	return $option;
}

