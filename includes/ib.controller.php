<?php
namespace Icebreath\API;

/**
 * The base template for route controllers, it
 * defines one abstract function for all sub-classes
 * to override which is called when a route is visted
 */
abstract class Controller {
	
	/**
	 * 
	 * Passes the requested route to the controller
	 * for processing and handling by the conroller
	 * itself.
	 * 
	 * @param string $uri The visted route
	 */
	abstract protected function handleRoute($uri);
	
}
