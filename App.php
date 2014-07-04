<?php
namespace Icebreath;

class App {
	
	/**
	 * 
	 * This function is called by the system
	 * once all settings and APIs have been 
	 * loaded into the system. From here the
	 * rest of the system shall be loaded
	 * 
	 */
	public static function run()
	{
		require_once ICE_BAS_DIR . '/routes.php';
		$current_route = str_replace(ICEBREATH_SUB_DIR, '', $_SERVER['REQUEST_URI']);
		$current_route = strtok($current_route, '?');
		
		if(API\Tools::startsWith($current_route, "/format"))
		{
		    $format = trim($current_route, '/');
		    $format = explode('/', $format);
		    $format = $format[1];
		    define('REQUESTED_RESPONSE_TYPE', $format);
		    $current_route = str_replace("/format/$format", '', $current_route);
		}
		
		$_SESSION['CURRENT_ROUTE'] = $current_route;
		
		if(!API\Route::does_route_exist($current_route))
			API\Tools::ib_die_nicely("The route you are trying to visit [$current_route] doesn't exist!");
		
		$current_controller = API\Route::get_controller_for_route($current_route);
		require_once ICE_CON_DIR . "/$current_controller.php";
		
		if(is_dir(ICE_CON_DIR . "/$current_controller"))
		    foreach (glob(ICE_CON_DIR . "/$current_controller/*.php") as $sub)
    			require_once $sub;
		
		$controller = new $current_controller();
		
		$controller->handleRoute($current_route);
	}

}
