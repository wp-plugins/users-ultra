<?php
define('uultraxoousers_pro_url','http://usersultra.com/');
/* Load plugin text domain (localization) */
add_action('init', 'xoousers_load_textdomain');
function xoousers_load_textdomain() {
	load_plugin_textdomain( 'xoousers', false,'/lang/');
}
		
add_action('init', 'xoousers_output_buffer');
function xoousers_output_buffer() {
		ob_start();
}