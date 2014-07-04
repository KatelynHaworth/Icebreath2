<?php
use Icebreath\API\Controller;
use Icebreath\API\Tools;

class BaseController extends Controller {
	
	public function handleRoute($uri)
	{
		Tools::build_response("Icebreath is online and working. System version: " . ICEBREATH_VERSION);
	}
}
