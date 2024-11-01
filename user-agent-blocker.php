<?php
/*
Plugin Name: User Agent Blocker
Plugin URI:  https://www.adhityar.com/plugins/block-bots-by-user-agent
Description: Block bad/unwanted robots/users/crawler from your site by it's User-Agent using .htaccess
Version:     1.0.2
Author:      Adhitya
Author URI:  https://profile.wordpress.org/adhitya03
Text Domain: uab
License:     GPL2

Block Bots by User Agent is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

Block Bots by User Agent is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Block Bots by User Agent. If not, see http://www.gnu.org/licenses/gpl-2.0.html.
*/

define('UAB_HTACCESS_FILE', ABSPATH.'.htaccess');
define('UAB_HTACCES_MARKER', 'USER AGENT BLOCKER');
define('UAB_NL', "\n");

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_menu', 'uab_submenu' );
function uab_submenu(){
	if(function_exists('add_menu_page')){
		$uab_menu = add_management_page( 'User Agent Blocker', 'User Agent Blocker', 'manage_options', 'user-agent-blocker', 'user_agent_blocker' );
	}
	add_action("admin_print_scripts-$uab_menu",'uab_css');
}

function uab_update_htaccess( $user_agent ){
	if( !empty( $user_agent ) ){
		//remove special character that can make an Server error 500!
		$validated_user_agent = preg_replace( '/[^\p{L}\p{N}\s+,-_]/u', '', preg_replace( "/[^a-zA-Z0-9,-_]+/", "",  $user_agent ) ) ;
		//delete if the last character is comma, that can make an error 403
		if( substr( $validated_user_agent, -1 ) == ',' ){
			$validated_user_agent = rtrim($validated_user_agent,", ");
		}
		$user_agent_check = explode( ',',  $validated_user_agent);
		if( count( $user_agent_check ) > 1 ){
			$validated_user_agent = '^.*('. str_replace( ',', '|', $validated_user_agent) .').*$';
		}
		$htaccess_data = '<IfModule mod_rewrite.c>'.UAB_NL.'RewriteEngine On'.UAB_NL.'RewriteCond %{HTTP_USER_AGENT} '.$validated_user_agent.' [NC]'.UAB_NL.'RewriteRule .* - [F,L]'.UAB_NL.'</IfModule>'.UAB_NL;
	}else{
		$htaccess_data = "";
	}
	insert_with_markers( UAB_HTACCESS_FILE,  UAB_HTACCES_MARKER, $htaccess_data);
}

function uab_deactivate(){
	insert_with_markers( UAB_HTACCESS_FILE,  UAB_HTACCES_MARKER, "" );
}
register_deactivation_hook( __FILE__, 'uab_deactivate' );

function uab_uninstall(){
	uab_deactivate();
	uab_delete_marker();
}
register_uninstall_hook( __FILE__ ,'uab_uninstall' );

function user_agent_blocker(){

	uab_display();

	if( !empty( $_POST[ 'update'] ) ){
		$retrieved_nonce = $_POST['_wpnonce'];
		if ( wp_verify_nonce($retrieved_nonce, 'submit_user_agent' ) ){
			$user_agent = sanitize_textarea_field( $_POST[ 'user-agent'] );
			uab_update_htaccess( $user_agent );
			echo '<meta http-equiv="refresh" content="0">';
		}else{
			die( 'Failed security check' );
		}
	}
}

function uab_delete_marker(){
	// get .htaccess content
	$read_handle = fopen(UAB_HTACCESS_FILE, "r");
	$htaccess = fread($read_handle, filesize(UAB_HTACCESS_FILE));
	fclose( $read_handle );
	// remove the marker created by this plugin
	$delete_marker = str_replace( '# END USER AGENT BLOCKER', '', str_replace( '# BEGIN USER AGENT BLOCKER' , '', $htaccess) );
	// write .htaccess after the marker has been deleted with the same content as it was taken above
	$write_handle = fopen(UAB_HTACCESS_FILE, "w");
	fwrite( $write_handle, $delete_marker );
	fclose( $write_handle );
}

function uab_display(){
	echo '<div class="wrap">
				<h2> User Agent Blocker </h2>
				<div class="inside">
					<form id="sf" action="" method="post">';

						if ( function_exists( 'wp_nonce_field' ) ) {
							wp_nonce_field( 'submit_user_agent' );
						}

	echo				'<input type="hidden" name="update" value="update">
						<textarea name="user-agent" placeholder="If you want to block more than one user agents, please separate with comma(,)">'. uab_blocked_user() .'</textarea>
						<span class="psbmt"><button class="button-primary" type="submit"/>Save Changes</button></span>
					</form>';

					uab_list();

	echo		'</div>
			</div>';
}

function uab_list(){
	// list of user agent that should not to block
	$dont_block = array(
		array(
			'name' => 'AdSense',
			'user_agent' => 'Mediapartners-Google'
		),
		array(
			'name' => 'Googlebot Images',
			'user_agent' => 'Googlebot-Image'
		),
		array(
			'name' => 'Googlebot News',
			'user_agent' => 'Googlebot-News'
		),
		array(
			'name' => 'Googlebot Video',
			'user_agent' => 'Googlebot-Video'
		),
		array(
			'name' => 'Googlebot',
			'user_agent' => 'Googlebot'
		),
		array(
			'name' => 'Yandex',
			'user_agent' => 'YandexBot/3.0'
		),
		array(
			'name' => 'Bing',
			'user_agent' => 'Bingbot'
		),

	);

	//list of user agent that you may block
	$may_block = array(
		array(
			'name' => 'Facebook: Social Media',
			'user_agent' => 'Facebot'
		),
		array(
			'name' => 'Ahrefs: SEO Tool',
			'user_agent' => 'AhrefsBot'
		),
		array(
			'name' => 'Semrush: SEO Tool',
			'user_agent' => 'SemrushBot,SemrushBot-SA'
		),
		array(
			'name' => 'MOZ: SEO Tool',
			'user_agent' => 'rogerbot,dotbot'
		),
		array(
			'name' => 'Majestic SEO: SEO Tool',
			'user_agent' => 'MJ12Bot'
		),
		array(
			'name' => 'Woorank: SEO Ranking Tool',
			'user_agent' => 'woobot'
		),
		array(
			'name' => 'Fat Rank: SEO Tools',
			'user_agent' => 'Siteliner'
		),
	);

echo '<div class="l">
	<h2>'. __( 'You <span class="red">Should Not Block</span> This User Agents.!' ) .'</h2>
	<table>
		<thead>
			<tr>
				<td>'. __( 'Name/Type' ) .'</td>
				<td>'. __( 'User Agent (product token)' ) .'</td>
			</tr>
		</thead>
		<tbody>';

		foreach ( $dont_block as $db ){
			echo '<tr>
					<td>'.esc_html( $db['name'] ).'</td>
					<td>'.esc_html( $db['user_agent'] ).'</td>
				</tr>';
		}

	echo '</tbody>
	</table>
</div>';

echo '<div class="r">
<h2>'. __( 'You May Block This User Agents.!' ) .'</h2>
<p>'.__( "If you do not find the user agent that you need, please do google and write the user agent directly in the text area." ).'</p>
<table>
	<thead>
		<tr>
			<td>'. __( 'Name/Type' ) .'</td>
			<td>'. __( 'User Agent (product token)' ) .'</td>
		</tr>
	</thead>
	<tbody>';

foreach ( $may_block as $mb ){
	echo '<tr>
				<td>'.esc_html( $mb['name'] ) .'</td>
				<td>'.esc_html( $mb['user_agent'] ) .'</td>
			</tr>';
}

echo '</tbody>
</table>
</div>';
}

function uab_blocked_user(){

	$begin_marker = "# BEGIN ".UAB_HTACCES_MARKER;
	$end_marker = "# END ".UAB_HTACCES_MARKER;
	$blocked_user_agent = null;
	$htaccess = file_get_contents(UAB_HTACCESS_FILE);
	$pos = strripos($htaccess, $begin_marker);
	if ($pos !== FALSE) {
		$begin_uab_htaccess = explode($begin_marker.UAB_NL.'<IfModule mod_rewrite.c>'.UAB_NL.'RewriteEngine On'.UAB_NL.'RewriteCond %{HTTP_USER_AGENT} ', $htaccess);
		if( !empty( $begin_uab_htaccess[1] ) ){
			$end_uab_htaccess = explode(' [NC]'.UAB_NL.'RewriteRule .* - [F,L]'.UAB_NL.'</IfModule>'.UAB_NL.UAB_NL.$end_marker, $begin_uab_htaccess[1]);
			$blocked_user_agent = str_replace( '|', ',', str_replace( array( '^.*(', ').*$' ), '', $end_uab_htaccess[0]) );
		}
	}

	return esc_html( $blocked_user_agent );
}

function uab_css(){
	echo '<style>
			.red{color:#900;}
			.gr{color:#390;}
			.gy,h3 i,.bi{color:#999;}
			.t,.bi{text-align:center;}
			.c,hr,#lw{clear:both;}
			br.c{line-height:0;}
			label,.l{float:left;}
			.r{float:right;}
			.l, .r{width: 50%}
			#sf label,#lw,.cs{position:relative;}
			#sf label:after,#sf label b,.xp,.sl,.sl:before{position:absolute;}
			.inside,#sf label b,.sl:before{background:#fff;}
			.inside,.bi{border:1px solid #e5e5e5;}
			.inside{border:1px solid #e5e5e5;min-height:600px;padding:40px;}
			.inside,.bi{border-radius:3px;}
			hr,#lw,.sl{background:#ddd;}
			hr{border:0 none;height:1px;color:#ddd;}
			#wpbody h2,#wpbody h3,#wpbody h4,#wpbody p,#wpbody ul,#wpbody form,hr{margin:0 0 20px 0;padding:0;}
			#sf textarea{width:98%;}
			#sf .xt{height:250px;}
			label{width:150px;}
			.ed,#sf label b,.bi i,.bi b{display:block;}
			.bi,.cs{display:inline-block;}
			.ed{margin-left:150px;}
			input[type="text"],input[type="url"]{width:250px;}
			#wpbody h3,#wpbody h4{font-family:Roboto,sans-serif;font-weight:700;text-transform:uppercase;}
			#wpbody p,#wpbody li,input[type="text"],input[type="url"],#sf textarea{font-size:14px;line-height:21px;}
			.bi b{font-size:12px;line-height:14px;}
			#wpbody .updated p{margin:0;padding:10px 0;}
			#sf input[type="text"],input[type="url"],#sf textarea{background:rgba(0,0,0,0.02);margin:0 5px 10px 0;}
			#sf input[type="text"]:focus,input[type="url"]:focus,#sf textarea:focus{box-shadow:0 0 0 none;outline: none;}
			a,#sf input,#sf textarea{transition:all .5s ease-in-out;-moz-transition:all .5s ease-in-out;-webkit-transition:all .5s ease-in-out;-o-transition:all .5s ease-in-out;}
			i{transition:all .3s ease-in-out;-moz-transition:all .3s ease-in-out;-webkit-transition:all .3s ease-in-out;-o-transition:all .3s ease-in-out;}
			em,i,b{font-weight:normal;font-style:normal;}
			#sf label:after{content:":";}
			#sf label.cs:after,#sf label.nd:after,.sl:before{content:"";}
			#sf label:after{right:15px;z-index:2;}
			#sf label b{padding:5px;top:0;right:5px;z-index:3;}
			.bi{width:75px;min-height:75px;margin:0 20px 20px 0;padding:20px;text-decoration:none;background:#f5f5f5;box-shadow:inset 0 40px 40px rgba(255,255,255,0.8);}
			.bi:hover,.bi:focus{border:1px solid #ccc;background:#f1f1f1;box-shadow:inset 0 50px 50px rgba(255,255,255,0.9),0 2px 3px rgba(0,0,0,0.1);}
			#wpbody .bi i{font-size:40px;margin-bottom:10px;}
			h3 i{margin-right:5px;}
			#lw,.xp{width:100%;height:5px;}
			#lw{padding:0;margin:10px auto;}
			.xp{margin:0;}
			.xp,input:checked + .sl{background:#390;}
			input:focus + .sl{box-shadow:0 0 1px #390;}
			@-moz-keyframes fxp {0% {width:0px;}100%{width:100%;}}
			@-webkit-keyframes fxp {0% {width:0px;}100%{width:100%;}}
			.cs{width:60px;height:34px;}
			.cs input{opacity:0;width:0;height:0;}
			.sl{cursor:pointer;top:0;left:0;right:0;bottom:0;}
			.sl,.sl:before{-webkit-transition:.4s;transition:.4s;}
			.sl:before{height:26px;width:26px;left:4px;bottom:4px;}
			input:checked + .sl:before{-webkit-transform:translateX(26px);-ms-transform:translateX(26px);transform:translateX(26px);}
			.sl.round{border-radius:34px;}
			.sl.round:before{border-radius:50%;}
			table {border-collapse: collapse;width: 90%;}
			table td, table th { border: 1px solid #ddd; padding: 8px;}
			table tr:nth-child(even){backmenu-areaground-color: #f2f2f2;}			
			table tr:hover {background-color: #ddd;}
			table thead tr td {padding-top: 12px;padding-bottom: 12px;text-align: left; font-weight: 900}
		</style>';
}