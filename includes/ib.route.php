<?php
namespace Icebreath\API;

class Route {
	
	/*Holds a list of URIs to Controllers*/
	private static $routes = array();
	/*Holds a list of RESTful controllers*/
	private static $restful_routes = array();
	
	/**
	 * 
	 * Binds a URI to its corresponding controller
	 * 
	 * @param string $uri The URI to link to the controller
	 * @param string $controller The controller to link to the URI
	 */
	public static function add_route($uri, $controller) {
		if(Route::does_route_exist($uri))
			Tools::ib_die_nicely("A controller already exists for the URI '$uri'. Failed to bind $uri::$controller");
			
		array_push(Route::$routes, array("URI" => $uri, "Controller" => $controller));
	}
	
	/**
	 * 
	 * Binds a RESTful URI to its corresponding controller
	 * 
	 * @param string $base_uri The RESTful URI to link to the controller
	 * @param string $controller The RESTful controller to link to the URI
	 */
	public static function add_restful_route($base_uri, $controller) {
		if(Route::does_route_exist($base_uri))
			Tools::ib_die_nicely("A controller already exists for the URI '$base_uri'. Failed to bind $base_uri::$controller");
			
		array_push(Route::$restful_routes, array("Base" => $base_uri, "Controller" => $controller));
	}
	
	/**
	 * 
	 * Check if a URI already has a controller bound
	 * to it in the system
	 * 
	 * @param string $uri The URI to check a link for
	 * 
	 * @return boolean
	 */
	public static function does_route_exist($uri) {
	    $uri = str_replace(ICEBREATH_SUB_DIR, '', $uri);
		foreach(Route::$restful_routes as $rest)
		{
			$restful_uri_base = trim($uri, '/');
			$restful_uri_base = explode("/", $restful_uri_base);
			$restful_uri_base = $restful_uri_base[0];
			
		 	if($rest['Base'] == ('/' . $restful_uri_base))
				return true;
		}
		
		foreach(Route::$routes as $route)
			if($route['URI'] == $uri)
				return true;
		
		return false;
	}	
	
	/**
	 * 
	 * Returns the name of the controller for a route, will
	 * return false if no controller was found for a route
	 * 
	 * @param string $uri The URI to get the controller for
	 * 
	 * @return string/boolean
	 */
	public static function get_controller_for_route($uri) {
	    $uri = str_replace(ICEBREATH_SUB_DIR, '', $uri);
		foreach(Route::$restful_routes as $rest)
		{
			$restful_uri_base = trim($uri, '/');
			$restful_uri_base = explode("/", $restful_uri_base);
			$restful_uri_base = $restful_uri_base[0];
			
			if($rest['Base'] == ('/' . $restful_uri_base))
				return $rest['Controller'];
		}
		
		foreach(Route::$routes as $route)
			if($route['URI'] == $uri)
				return $route['Controller'];
			
		return false;
	}
	
	public static function get_routes_list() {
	    return Route::$routes;
	}
	
	public static function get_restful_routes_list() {
	    return Route::$restful_routes;
	}
}
