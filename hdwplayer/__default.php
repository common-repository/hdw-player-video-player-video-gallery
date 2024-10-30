<?php

global $wpdb;
$table_name = $wpdb->prefix . "hdwplayer";
$data       = array();

$siteurl = get_option ( 'siteurl' );
$js = $siteurl . '/wp-content/plugins/' . basename ( dirname(dirname ( __FILE__ )) ) . '/js/jquery-1.9.1.min.js';
$jsac = $siteurl . '/wp-content/plugins/' . basename ( dirname(dirname ( __FILE__ )) ) . '/js/jquery.autocomplete.min.js';

$playlist   = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."hdwplayer_playlist");
$gallery   = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."hdwplayer_gallery");
$video = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."hdwplayer_videos");
/******************************************************************
/* Execute Actions
******************************************************************/
switch($_GET['opt']) {
	case 'add'   :
		require_once('__add.php');
		break;
	case 'edit'  :
		require_once('__edit.php');
		break;
	case 'delete':
		require_once('__delete.php');
		break;
	default:
		require_once('__grid.php');		
}

?>