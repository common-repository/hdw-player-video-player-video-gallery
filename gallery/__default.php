<?php

global $wpdb;
$table_name = $wpdb->prefix . "hdwplayer_gallery";
$data       = array();

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