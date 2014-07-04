<?php
namespace Icebreath\API;

class Tools {
	
	/**
	 * 
	 * Returns the URL used to access this application
	 * 
	 * @return string
	 */
	public static function ib_get_host() {
		return 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . "{$_SERVER['HTTP_HOST']}:{$_SERVER['SERVER_PORT']}";
	}
	
	/**
	 * 
	 * Allows the system to throw an error and call <b>die()</b>
	 * in a way that returns an understandable error output for
	 * the user of the system
	 *
	 * @param string/Exception $reason The reason why the system was called to die
	 * @param string $http_level (Optional) The HTTP return code
	 * 
	 */
	public static function ib_die_nicely($reason)
	{
		$built_response = array("status" => "error", "error" => $reason, "timestamp" => time());
		$response_type = (defined('REQUESTED_RESPONSE_TYPE') ? REQUESTED_RESPONSE_TYPE : ICEBREATH_RESPONSE_TYPE);
	    
	    if($response_type === "JSON")
	    {
	        header("HTTP/1.1 200 OK");
	        header("Content-type: application/json");
	        die(json_encode($built_response));
	    }
	    else if($response_type === "JSONp")
	    {
	        header("HTTP/1.1 200 OK");
	        header("Content-type: application/javascript");
	        $data = json_encode($built_response);
	        die("var icebreath_response_data = [$data];");
	    }
	    else if($response_type === "XML")
	    {
	        header("HTTP/1.1 200 OK");
	        header("Content-type: text/xml");
	        $xml = new \SimpleXMLElement('<?xml version="1.0"?><icebreath version="' . ICEBREATH_VERSION . '"/>');
	        Tools::array_to_xml($built_response, $xml);
	        die($xml->asXML());
	    }
	    else if($response_type === "HTML")
	    {
	        header("HTTP/1.1 200 OK");
	        header("Content-type: text/html");
	        die(Tools::array_to_table($built_response));
	    }
	    else if($response_type === "TEXT")
	    {
	        header("HTTP/1.1 200 OK");
	        header("Content-type: text/html");
	        die(Tools::array_to_text($built_response));
	    }
	}
	
	/**
	 * 
	 * Searchs a string [<b>$haystack</b>] to see
	 * if it starts with <b>$needle</b>
	 * 
	 * @param string $haystack The string to be searched
	 * @param string $needle Item to search for
	 * 
	 * @return boolean
	 */
	public static function startsWith($haystack, $needle) {
	    return $needle === "" || strpos($haystack, $needle) === 0;
	}
	
	/**
	 * 
	 * Searchs a string [<b>$haystack</b>] to see
	 * if it ends with <b>$needle</b>
	 * 
	 * @param string $haystack The string to be searched
	 * @param string $needle Item to search for
	 * 
	 * @return boolean
	 */
	public static function endsWith($haystack, $needle) {
	    return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
	}
	
	public static function build_response($response)
	{
	    $built_response = array("status" => "success", "result" => $response, "timestamp" => time());
	    $response_type = (defined('REQUESTED_RESPONSE_TYPE') ? REQUESTED_RESPONSE_TYPE : ICEBREATH_RESPONSE_TYPE);
	    
	    if($response_type === "JSON")
	    {
	        header("HTTP/1.1 200 OK");
	        header("Content-type: application/json");
	        echo json_encode($built_response);
	    }
	    else if($response_type === "JSONp")
	    {
	        header("HTTP/1.1 200 OK");
	        header("Content-type: application/javascript");
	        $data = json_encode($built_response);
	        echo "var icebreath_response_data = [$data];";
	    }
	    else if($response_type === "XML")
	    {
	        header("HTTP/1.1 200 OK");
	        header("Content-type: text/xml");
	        $xml = new \SimpleXMLElement('<?xml version="1.0"?><icebreath version="' . ICEBREATH_VERSION . '"/>');
	        Tools::array_to_xml($built_response, $xml);
	        echo $xml->asXML();
	    }
	    else if($response_type === "HTML")
	    {
	        header("HTTP/1.1 200 OK");
	        header("Content-type: text/html");
	        echo Tools::array_to_table($built_response);
	    }
	    else if($response_type === "TEXT")
	    {
	        header("HTTP/1.1 200 OK");
	        header("Content-type: text/html");
	        echo Tools::array_to_text($built_response);
	    }
	}
	
	private static function array_to_xml($data, &$xml) {
        foreach($data as $key => $value) {
            if(is_array($value)) {
                if(!is_numeric($key)){
                    $subnode = $xml->addChild("$key");
                    Tools::array_to_xml($value, $subnode);
                }
                else{
                    $subnode = $xml->addChild("item$key");
                    Tools::array_to_xml($value, $subnode);
                }
            }
            else {
                $xml->addChild("$key","$value");
            }
        }
    }
    
    private static function array_to_table($data)
    {
    	$html = "<table border=\"1\">";
    	
    	foreach($data as $key => $value) {
            if(is_array($value))
    			$html .= "<tr><td>" . $key . "</td><td>" . Tools::array_to_table($value) . "</td></tr>";
    		else if(is_bool($value))
    			$html .= "<tr><td>" . $key . "</td><td>" . ($value ? "true" : "false") . "</td></tr>";
    		else
    			$html .= "<tr><td>" . $key . "</td><td>" . $value . "</td></tr>";
        }
    	
    	$html .= "</table>";
    	
    	return $html;
    }
    
    private static function array_to_text($data)
    {
        $html = "";
        
        foreach($data as $key => $value)
            if(is_array($value))
                $html .= "<p><span>" . Tools::array_to_text($value) . "</span></p>";
            else if(is_bool($value))
                $html .= "<p><b>" . $key . ":</b> <span>" . ($value ? "true" : "false") . "</span></p>";
            else if($value == null)
                $html .= "<p><b>" . $key . ":</b> <span>null</span></p>";
            else
                $html .= "<p><b>" . $key . ":</b> <span>" . $value . "</span></p>";
            
        return $html;
    }
	
	/**
	 * 
	 * Returns a JSON response message, this
	 * is normall used by data controllers to
	 * return raw data
	 * 
	 * @param array $message An array of message data to return
	 * @param int $level The level code, <b>0</b> for OK, more for the error type
	 * @param string $code The string resposne related to the <b>$level</b>
	 */
	public static function ib_return_json_message($message, $level=0, $code="OK")
	{
		header("HTTP/1.1 " . $level . " " . $code);
		header("Content-type: text/json");
		header("Content-type: application/json");
		$output = array(
			"response" => $code,
			"level" => $level,
			"data" => $message
		);
		echo json_encode($output);
	}
}
