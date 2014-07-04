<?php

use Icebreath\API\RESTfulController;
use Icebreath\API\Tools;
use Icebreath\API\Route;

class icecast extends RESTfulController {
	
	public function __construct() {
		parent::__construct('/icecast');
	}
	
	/****UNUSED IN THIS CONTROLLER****/
	public function restful_post($args) {Tools::ib_die_nicely("This HTTP mode is not supported by this controller");}
	public function restful_put($args){Tools::ib_die_nicely("This HTTP mode is not supported by this controller");}
	public function restful_delete($args){Tools::ib_die_nicely("This HTTP mode is not supported by this controller");}
	/*********************************/
	
	public function restful_get($args)
	{
	    if($args == null || count($args) < 1 )
	        Tools::build_response(array("usage" => "/icecast/[stats]/{mount}"));
	    else
	    {
	        $mode = $args[0];

            if($mode == "stats")
                if(count($args) >= 2)
                    $this->getStats($args[1]);
                else
                    $this->getStats();
            else
                Tools::ib_die_nicely("Unsupported mode requested! [$mode]");
	    }
	}
	
	private function getStats_Singular($mount_point_name)
	{
	    if((ICECAST_SERVER_HOST == null || ICECAST_SERVER_HOST == "") || (ICECAST_SERVER_PORT == null || ICECAST_SERVER_PORT == "") || (ICECAST_SERVER_USER == null || ICECAST_SERVER_USER == "") || (ICECAST_SERVER_PORT == null || ICECAST_SERVER_PORT == ""))
	        Tools::ib_die_nicely("No IceCAST server config exists. If this is a mistake please contact your admin at: " . ICEBREATH_ADMIN . ". If you are the admin, please check you Icebreath config");
	    
	    $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "http://".ICECAST_SERVER_USER.":".ICECAST_SERVER_PASS."@".ICECAST_SERVER_HOST.":".ICECAST_SERVER_PORT."/admin/stats");  
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); 
        curl_setopt($curl, CURLOPT_TIMEOUT, 10); 
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.2) Icebreath/'.ICEBREATH_VERSION.' (Firefox cURL emulated)');  
        $data = curl_exec($curl);
    	$http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	    
	    if($http_code == "0" || empty($data))
	        Tools::id_die_nicely("Failed to connect to IceCAST server. [Code: " . $http_code . "] [Server:http://".ICECAST_SERVER_HOST.":".ICECAST_SERVER_PORT."]");
	    
	    if($http_code != "200")
    	    Tools::ib_die_nicely("Failed to log into IceCAST server. [Code: " . $http_code . "] [Server:http://".ICECAST_SERVER_HOST.":".ICECAST_SERVER_PORT."]");
    	    
    	$data = new SimpleXMLElement($data);
	    
	    $server_data = new ServerStruct();
	    $server_data->server_version = (string)$data->server_id;
	    $server_data->server_admin = (string)$data->admin;
	    $server_data->server_location = ICEBREATH_LOCATION;
	    $server_data->server_listeners_total = (int)$data->listeners;
	    $server_data->server_listeners_unique = 0;
	    $server_data->server_listeners_peak = 0;
	    $server_data->server_listeners_max = 0;
	    $server_data->server_streams = array();
	    
	    foreach($data->source as $mount_point)
	    {
	        if(str_replace('/', '', $mount_point["mount"]) != $mount_point_name)
	            continue;
	            
	        if($mount_point->source_ip == null)
	        {
	            $stream = new StreamStruct();
	            $stream->stream_online = false;
	            $stream->stream_name = (string)$mount_point["mount"];
	            $stream->stream_error = "Stream is offline! It has no connected source!";
	            array_push($server_data->server_streams, $stream);
	            continue;
	        }
	        
	        $stream = new StreamStruct();
	        $stream->stream_online = true;
	        $stream->stream_title = (string)$mount_point->server_name;
	        $stream->stream_description = (string)$mount_point->server_description;
	        $stream->stream_name = (string)$mount_point["mount"];
	        $stream->stream_genre = (string)$mount_point->genre;
	        
	        $stream->stream_audio_info = new AudioInfoStruct();
	        $audio_info = explode(';', (string)$mount_point->audio_info);
	        foreach($audio_info as $info)
	        {
	            $info_data = explode('=', $info);
	            $stream->stream_audio_info->$info_data[0] = $info_data[1];
	        }
	        
	        $stream->stream_mime = (string)$mount_point->server_type;
	        $stream->stream_listeners = (int)$mount_point->listeners;
	        
	        $stream->stream_listeners_peak = (int)$mount_point->listener_peak;
	        $server_data->server_listeners_peak += (int)$mount_point->listener_peak;
	        
	        $stream->stream_listeners_max = ($mount_point->max_listeners == "unlimited" ? -1 : (int)$mount_point->max_listeners);
	        if($mount_point->max_listeners == "unlimited")
	            $server_data->server_listeners_max =  -1;
	        else
	            $server_data->server_listeners_max += (int)$mount_point->max_listeners;
	        
	        $stream->stream_listeners_unique = $this->getUniqueListenersOnMount((string)$mount_point["mount"]);
	        $server_data->server_listeners_unique += $stream->stream_listeners_unique;
	        
	        $stream->stream_url = (string)$mount_point->listenurl;
	        
	        $stream->stream_nowplaying = new NowPlayingStruct();
    	    $stream->stream_nowplaying->text = (string)$mount_point->title;
    	    
    	    $nowplaying = explode(" - ", (string)$mount_point->title);
    	    $stream->stream_nowplaying->artist = $nowplaying[0];
    	    
    	    for($index = 1; $index < count($nowplaying); $index++)
    	    {
    	        if($index > 1)
    	           $stream->stream_nowplaying->song .= " - ";
    	           
    	        $stream->stream_nowplaying->song .= $nowplaying[$index];
    	    }
    	    
    	    
    	    
    	    array_push($server_data->server_streams, $stream);
	    }
	    
	    $this->generateResponse($server_data);
	}
	
	private function getStats($mount_point_name="")
	{
	    if((ICECAST_SERVER_HOST == null || ICECAST_SERVER_HOST == "") || (ICECAST_SERVER_PORT == null || ICECAST_SERVER_PORT == "") || (ICECAST_SERVER_USER == null || ICECAST_SERVER_USER == "") || (ICECAST_SERVER_PORT == null || ICECAST_SERVER_PORT == ""))
	        Tools::ib_die_nicely("No IceCAST server config exists. If this is a mistake please contact your admin at: " . ICEBREATH_ADMIN . ". If you are the admin, please check you Icebreath config");
	    
	    $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "http://".ICECAST_SERVER_USER.":".ICECAST_SERVER_PASS."@".ICECAST_SERVER_HOST.":".ICECAST_SERVER_PORT."/admin/stats");  
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); 
        curl_setopt($curl, CURLOPT_TIMEOUT, 10); 
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.2) Icebreath/'.ICEBREATH_VERSION.' (Firefox cURL emulated)');  
        $data = curl_exec($curl);
    	$http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	    
	    if($http_code == "0" || empty($data))
	        Tools::id_die_nicely("Failed to connect to IceCAST server. [Code: " . $http_code . "] [Server:http://".ICECAST_SERVER_HOST.":".ICECAST_SERVER_PORT."]");
	    
	    if($http_code != "200")
    	    Tools::ib_die_nicely("Failed to log into IceCAST server. [Code: " . $http_code . "] [Server:http://".ICECAST_SERVER_HOST.":".ICECAST_SERVER_PORT."]");
    	    
    	$data = new SimpleXMLElement($data);
	    
	    $server_data = new ServerStruct();
	    $server_data->server_version = (string)$data->server_id;
	    $server_data->server_admin = (string)$data->admin;
	    $server_data->server_location = ICEBREATH_LOCATION;
	    $server_data->server_listeners_total = (int)$data->listeners;
	    $server_data->server_listeners_unique = 0;
	    $server_data->server_listeners_peak = 0;
	    $server_data->server_listeners_max = 0;
	    $server_data->server_streams = array();
	    
	    foreach($data->source as $mount_point)
	    {
	        $server_data->server_listeners_peak += (int)$mount_point->listener_peak;
	        if($mount_point->max_listeners == "unlimited")
	            $server_data->server_listeners_max =  -1;
	        else
	            $server_data->server_listeners_max += (int)$mount_point->max_listeners;
	        $unique_listeneres = $this->getUniqueListenersOnMount((string)$mount_point["mount"]);
	        $server_data->server_listeners_unique += $unique_listeneres;
	        
	        if(!empty($mount_point_name) && str_replace('/', '', $mount_point["mount"]) != $mount_point_name)
	            continue;
	            
	        if($mount_point->source_ip == null)
	        {
	            $stream = new StreamStruct();
	            $stream->stream_online = false;
	            $stream->stream_name = (string)$mount_point["mount"];
	            $stream->stream_error = "Stream is offline! It has no connected source!";
	            array_push($server_data->server_streams, $stream);
	            continue;
	        }
	        
	        $stream = new StreamStruct();
	        $stream->stream_online = true;
	        $stream->stream_title = (string)$mount_point->server_name;
	        $stream->stream_description = (string)$mount_point->server_description;
	        $stream->stream_name = (string)$mount_point["mount"];
	        $stream->stream_genre = (string)$mount_point->genre;
	        
	        $stream->stream_audio_info = new AudioInfoStruct();
	        $audio_info = explode(';', (string)$mount_point->audio_info);
	        foreach($audio_info as $info)
	        {
	            $info_data = explode('=', $info);
	            $stream->stream_audio_info->$info_data[0] = $info_data[1];
	        }
	        
	        $stream->stream_mime = (string)$mount_point->server_type;
	        $stream->stream_listeners = (int)$mount_point->listeners;
	        $stream->stream_listeners_peak = (int)$mount_point->listener_peak;
	        $stream->stream_listeners_max = ($mount_point->max_listeners == "unlimited" ? -1 : (int)$mount_point->max_listeners);
	        $stream->stream_listeners_unique = $unique_listeneres;
	        $stream->stream_url = (string)$mount_point->listenurl;
	        
	        $stream->stream_nowplaying = new NowPlayingStruct();
    	    $stream->stream_nowplaying->text = (string)$mount_point->title;
    	    
    	    $nowplaying = explode(" - ", (string)$mount_point->title);
    	    $stream->stream_nowplaying->artist = $nowplaying[0];
    	    
    	    for($index = 1; $index < count($nowplaying); $index++)
    	    {
    	        if($index > 1)
    	           $stream->stream_nowplaying->song .= " - ";
    	           
    	        $stream->stream_nowplaying->song .= $nowplaying[$index];
    	    }
    	    
    	    $stream->stream_nowplaying->dj = $this->getCurrentDJ();
    	    
    	    array_push($server_data->server_streams, $stream);
	    }
	    
	    $this->generateResponse($server_data);
	}
	
	private function getUniqueListenersOnMount($mount)
	{
	    $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "http://".ICECAST_SERVER_USER.":".ICECAST_SERVER_PASS."@".ICECAST_SERVER_HOST.":".ICECAST_SERVER_PORT."/admin/listclients?mount=$mount");  
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); 
        curl_setopt($curl, CURLOPT_TIMEOUT, 10); 
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.2) Icebreath/'.ICEBREATH_VERSION.' (Firefox cURL emulated)');  
        $data = curl_exec($curl);
    	$http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	    
	    if($http_code == "0" || empty($data))
	        Tools::id_die_nicely("Failed to connect to IceCAST server. [Code: " . $http_code . "] [Server:http://".ICECAST_SERVER_HOST.":".ICECAST_SERVER_PORT."]");
	    
	    if($http_code != "200")
    	    Tools::ib_die_nicely("Failed to log into IceCAST server. [Code: " . $http_code . "] [Server:http://".ICECAST_SERVER_HOST.":".ICECAST_SERVER_PORT."]");
    	    
    	$data = new SimpleXMLElement($data);
    	$unique_listener_array = array();
    	
    	foreach($data->source->listener as $listener)
    	    if(!in_array($listener->IP, $unique_listener_array))
    	        array_push($unique_listener_array, $listener->IP);
    	
    	return count($unique_listener_array);
	}
	
	private function getCurrentDJ()
	{
	    if(Route::does_route_exist('/shoutirc') && file_exists(ICE_CON_DIR . '/shoutirc/remote.client.inc.php'))
	    {
	        require_once ICE_CON_DIR . '/shoutirc/remote.client.inc.php';
	        $remote = new ShoutIRC\RemoteClient();
	        $stream_info = $remote->send_command(new ShoutIRC\RemoteHeaderStruct(0x12));
	        return $stream_info->getData();
	    }
	    else
	        return "No ShoutIRC controller installed!";
	}
	
	private function generateResponse($server_data)
	{
	    $response = array();

	    $response["server_version"] = $server_data->server_version;
	    $response["server_admin"] = $server_data->server_admin;
	    $response["server_location"] = $server_data->server_location;
	    $response["server_listeners_total"] = $server_data->server_listeners_total;
	    $response["server_listeners_unique"] = $server_data->server_listeners_unique;
	    $response["server_listeners_peak"] = $server_data->server_listeners_peak;
	    $response["server_listeners_max"] = $server_data->server_listeners_max;
	    $server_streams = array();
	    
	    if($server_data->server_streams)
	    foreach($server_data->server_streams as $stream_data)
	    {
	        $stream_nowplaying = array();
	        if($stream_data->stream_nowplaying)
	            $stream_nowplaying = array(
	                "song" => $stream_data->stream_nowplaying->song,
	                "artist" => $stream_data->stream_nowplaying->artist,
	                "text" => $stream_data->stream_nowplaying->text,
	                "dj" => $stream_data->stream_nowplaying->dj
	            );
	        
	        $stream_audio_info = array();
	        if($stream_data->stream_audio_info)
	            $stream_audio_info = array(
	                "bitrate" => $stream_data->stream_audio_info->bitrate,
	                "samplerate" => $stream_data->stream_audio_info->samplerate,
	                "channels" => $stream_data->stream_audio_info->channels
	            );
	        
	        array_push($server_streams, array(
	            "stream_online" => $stream_data->stream_online,
                "stream_title" => $stream_data->stream_title,
                "stream_description" => $stream_data->stream_description,
                "stream_name" => $stream_data->stream_name,
                "stream_genre" => $stream_data->stream_genre,
                "stream_audio_info" => $stream_audio_info,
                "stream_mime" => $stream_data->stream_mime,
                "stream_listeners" => $stream_data->stream_listeners,
                "stream_listeners_unique" => $stream_data->stream_listeners_unique,
                "stream_listeners_peak" => $stream_data->stream_listeners_peak,
                "stream_listeners_max" => $stream_data->stream_listeners_max,
                "stream_url" => $stream_data->stream_url,
                "stream_nowplaying" => $stream_nowplaying,
                "stream_song_history" => $stream_data->stream_song_history,
                "stream_error" => $stream_data->stream_error
	        ));
	    }
	    
	    $response["server_streams"] = $server_streams;
	    
	    Tools::build_response($response);
	}
}

class ServerStruct {
    public $server_version;
    public $server_admin;
    public $server_location;
    public $server_listeners_total;
    public $server_listeners_unique;
    public $server_listeners_peak;
    public $server_listeners_max;
    public $server_streams;
}

class StreamStruct {
    public $stream_online;
    public $stream_title;
    public $stream_description;
    public $stream_name;
    public $stream_genre;
    public $stream_audio_info;
    public $stream_mime;
    public $stream_listeners;
    public $stream_listeners_unique;
    public $stream_listeners_peak;
    public $stream_listeners_max;
    public $stream_url;
    public $stream_nowplaying;
    public $stream_song_history;
    public $stream_error;
}

class NowPlayingStruct {
    public $song;
    public $artist;
    public $text;
    public $dj;
}

class AudioInfoStruct {
    public $bitrate;
    public $samplerate;
    public $channels;
}