<?php
namespace Icebreath\API;

abstract class RESTfulController extends Controller {
	
	protected $base_uri = "";
	
	public function __construct($base_uri) {
		$this->base_uri = $base_uri;
	}
	
	public function handleRoute($uri)
	{
		$http_method = $_SERVER['REQUEST_METHOD'];
		$args = str_replace(ICEBREATH_SUB_DIR, '', $uri);
		
		if (substr($args, 0, strlen($this->base_uri)) == $this->base_uri) {
    		$args = substr($args, strlen($this->base_uri));
		} 
		
		$args = trim($args, '/');
		$args = explode('/', $args);
		
		if(count($args) == 1 && $args[0] == "")
		    $args = null;
		
		if($http_method == "GET")
			$this->restful_get($args);
		else if($http_method == "PUT")
			$this->restful_put($args);
		else if($http_method == "POST")
			$this->restful_post($args);
		else if($http_method == "DELETE")
			$this->restful_delete($args);
		else
			Tools::ib_die_nicely("Unknown HTTP method [$http_method], RESTful does not support it!");
	}
	
	/*Ordered in CRUD | Create, Read, Update, Delete*/
	abstract protected function restful_put($args);
	abstract protected function restful_get($args);
	abstract protected function restful_post($args);
	abstract protected function restful_delete($args);
}
