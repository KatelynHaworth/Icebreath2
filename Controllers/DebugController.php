<?php
use Icebreath\API\Controller;
use Icebreath\API\Tools;
use Icebreath\API\Route;

class DebugController extends Controller {
	
	public function handleRoute($uri)
	{
	    if(!ICEBREATH_DEBUG)
	        Tools::ib_die_nicely("Whoops! You shouldn't be here, debug mode has been disabled! If you are the admin of this system and are trying to check the system status, please enabling debuging in the Icebreath config");
	        
	    $php_extensions = array();
	    foreach(get_loaded_extensions() as $key => $value)
	        $php_extensions["extension_$key"] = $value;
	        
		Tools::build_response(array(
		    "icebreath_version" => ICEBREATH_VERSION,
		    "icebreath_response_type" => ICEBREATH_RESPONSE_TYPE,
		    "icebreath_sub_dir" => ICEBREATH_SUB_DIR,
		    "icebreath_location" => ICEBREATH_LOCATION,
		    "icebreath_admin" => ICEBREATH_ADMIN,
		    "icebreath_routes" => Route::get_routes_list(),
		    "icebreath_restful_routes" => Route::get_restful_routes_list(),
		    "icebreath_base_dir" => ICE_BAS_DIR,
		    "icebreath_api_dir" => ICE_API_DIR,
		    "icebreath_controllers_dir" => ICE_CON_DIR,
		    "http_method" => $_SERVER['REQUEST_METHOD'],
		    "php_version" => PHP_VERSION,
		    "php_self" => $_SERVER['PHP_SELF'],
		    "php_server_address" => $_SERVER['SERVER_ADDR'],
		    "php_session_id" => session_id(),
		    "php_session_items" => $_SESSION,
		    "php_loaded_extensions" => $php_extensions
		));
	}
}
