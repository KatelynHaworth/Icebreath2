<?php

use Icebreath\API\RESTfulController;
use Icebreath\API\Tools;
use Icebreath\API\Route;

class shoutcast extends RESTfulController {
	
	public function __construct() {
		parent::__construct('/shoutcast');
	}
	
	/****UNUSED IN THIS CONTROLLER****/
	public function restful_post($args) {Tools::ib_die_nicely("This HTTP mode is not supported by this controller");}
	public function restful_put($args){Tools::ib_die_nicely("This HTTP mode is not supported by this controller");}
	public function restful_delete($args){Tools::ib_die_nicely("This HTTP mode is not supported by this controller");}
	/*********************************/
	
	public function restful_get($args)
	{
	    if($args == null || count($args) < 2 )
	        Tools::build_response(array("usage" => "/shoutcast/[version]/stats/{id}"));
	    else
	    {
	        $version = $args[0];
	        $mode = $args[1];
	        
	        if($version == 1)
	        {
	            if($mode == "stats")
    	           $this->getShoutcast1Stats();
	            else
	                Tools::ib_die_nicely("Non-supported mode requested!");
	        }
	        else if($version == 2)
	        {
	            if($mode == "stats")
	                $this->getShoutcast2Stats();
	            else
	                Tools::ib_die_nicely("Non-supported mode requested!");
	        }
	        else
	            Tools::ib_die_nicely("Non-supported version requested!");
	    }
	}
	
	private function getShoutcast1Stats()
	{
	    $v1_servers = unserialize(constant("SHOUTCAST_V1_SERVERS"));
	    if(count($v1_servers) == 0)
	        Tools::ib_die_nicely("No SHOUTcastV1 servers have been added to the config. If this is a mistake please contact your admin at: " . ICEBREATH_ADMIN . ". If you are the admin, please check you Icebreath config");
	        
	    $server_data = new ServerStruct();
	    
	    $server_data->server_version = "SHOUTcast Server Version 1.x";
	    $server_data->server_admin = ICEBREATH_ADMIN;
	    $server_data->server_location = ICEBREATH_LOCATION;
	    $server_data->server_listeners_total = 0;
	    $server_data->server_listeners_unique = 0;
	    $server_data->server_streams = array();
    	
    	foreach($v1_servers as $server)
    	{
    	    $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, "http://".$server["HOST"].":".$server["PORT"]."/admin.cgi?pass=".$server["PASS"]."&mode=viewxml");  
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); 
            curl_setopt($curl, CURLOPT_TIMEOUT, 10); 
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.2) Icebreath/'.ICEBREATH_VERSION.' (Firefox cURL emulated)');  
            $data = curl_exec($curl);
    	    $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    	    
    	    if($http_code == "0" || empty($data))
    	    {
    	        $stream = new StreamStruct();
    	        $stream->stream_online = false;
    	        $stream->stream_error = "Failed to connect to SHOUTcastV1 server. [Code: " . $http_code . "] [Server:http://".$server["HOST"].":".$server["PORT"]."]";
    	        array_push($server_data->server_streams, $stream);
    	        continue;
    	    }
    	    
    	    if($http_code != "200")
    	    {
    	        $stream = new StreamStruct();
    	        $stream->stream_online = false;
    	        $stream->stream_error = "Failed to log into SHOUTcastV1 server. [Code: " . $http_code . "] [Server:http://".$server["HOST"].":".$server["PORT"]."]";
    	        array_push($server_data->server_streams, $stream);
    	        continue;
    	    }
    	    
    	    $data = new SimpleXMLElement($data);
    	    
    	    $server_data->server_listeners_total += (int)$data->CURRENTLISTENERS;
    	    $server_data->server_listeners_unique += (int)$data->REPORTEDLISTENERS;
    	    $server_data->server_listeners_peak += (int)$data->PEAKLISTENERS;
    	    $server_data->server_listeners_max += (int)$data->MAXLISTENERS;
    	    
    	    $stream = new StreamStruct();
    	    $stream->stream_online = true;
    	    $stream->stream_title = (string)$data->SERVERTITLE;
    	    $stream->stream_name = $server["NAME"];
    	    $stream->stream_genre = (string)$data->SERVERGENRE;
    	    
    	    $stream->stream_audio_info = new AudioInfoStruct();
    	    $stream->stream_audio_info->bitrate = (int)$data->BITRATE;
    	    
    	    $stream->stream_mime = (string)$data->CONTENT;
    	    $stream->stream_listeners = (int)$data->CURRENTLISTENERS;
    	    $stream->stream_listeners_unique = (int)$data->REPORTEDLISTENERS;
    	    $stream->stream_url = "http://".$server["HOST"].":".$server["PORT"]."/;stream";
    	    $stream->stream_nowplaying = new NowPlayingStruct();
    	    $stream->stream_nowplaying->text = (string)$data->SONGTITLE;
    	    
    	    $nowplaying = explode(" - ", (string)$data->SONGTITLE);
    	    $stream->stream_nowplaying->artist = $nowplaying[0];
    	    
    	    for($index = 1; $index < count($nowplaying); $index++)
    	    {
    	        if($index > 1)
    	           $stream->stream_nowplaying->song .= " - ";
    	           
    	        $stream->stream_nowplaying->song .= $nowplaying[$index];
    	    }
    	    
    	    $stream->stream_nowplaying->dj = $this->getCurrentDJ();
    	    
    	    $stream->stream_song_history = array();
    	    foreach($data->SONGHISTORY->SONG as $song)
    	        array_push($stream->stream_song_history, (string)$song->TITLE);
    	    
    	    array_push($server_data->server_streams, $stream);
    	}
	    
	    
	    $this->generateResponse($server_data);
	}
	
	private function getShoutcast2Stats()
	{
	    if((SHOUTCAST_V2_SERVER_HOST == null || SHOUTCAST_V2_SERVER_HOST == "") || (SHOUTCAST_V2_SERVER_PORT == null || SHOUTCAST_V2_SERVER_PORT == ""))
	        Tools::ib_die_nicely("No SHOUTcast V2 server config exists. If this is a mistake please contact your admin at: " . ICEBREATH_ADMIN . ". If you are the admin, please check you Icebreath config");
	   
	    $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "http://".SHOUTCAST_V2_SERVER_HOST.":".SHOUTCAST_V2_SERVER_PORT."/statistics");  
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); 
        curl_setopt($curl, CURLOPT_TIMEOUT, 10); 
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.2) Icebreath/'.ICEBREATH_VERSION.' (Firefox cURL emulated)');  
        $data = curl_exec($curl);
	    $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	    
	    if($http_code == "0" || empty($data))
	    {
	        $stream = new StreamStruct();
	        $stream->stream_online = false;
	        $stream->stream_error = "Failed to connect to SHOUTcastV2 server. [Code: " . $http_code . "] [Server:http://".SHOUTCAST_V2_SERVER_HOST.":".SHOUTCAST_V2_SERVER_PORT."]";
	        array_push($server_data->server_streams, $stream);
	        continue;
	    }
	    
	    $data = new SimpleXMLElement($data);
	    $data = $data->STREAMSTATS;
	    
	    $server_data = new ServerStruct();
	    $server_data->server_version = "SHOUTcast Server Version " . (string)$data->VERSION;
	    $server_data->server_admin = ICEBREATH_ADMIN;
	    $server_data->server_location = ICEBREATH_LOCATION;
	    $server_data->server_listeners_total = (int)$data->CURRENTLISTENERS;
	    $server_data->server_listeners_unique = (int)$data->UNIQUELISTENERS;
	    $server_data->server_listeners_peak = (int)$data->PEAKLISTENERS;
	    $server_data->server_listeners_max = (int)$data->MAXLISTENERS;
	    $server_data->server_streams = array();
	    
	    foreach($data->STREAM as $stream_data)
	    {
	       $stream = new StreamStruct();
    	    $stream->stream_online = true;
    	    $stream->stream_title = (string)$stream_data->SERVERTITLE;
    	    $stream->stream_name = "Server_Stream_" . $stream_data["id"];
    	    $stream->stream_genre = (string)$stream_data->SERVERGENRE;
    	    
    	    $stream->stream_audio_info = new AudioInfoStruct();
    	    $stream->stream_audio_info->bitrate = (int)$stream_data->BITRATE;
    	    
    	    $stream->stream_mime = (string)$stream_data->CONTENT;
    	    $stream->stream_listeners = (int)$stream_data->CURRENTLISTENERS;
    	    $stream->stream_listeners_unique = (int)$stream_data->UNIQUELISTENERS;
    	    $stream->stream_listeners_peak = (int)$stream_data->PEAKLISTENERS;
    	    $stream->stream_listeners_max = (int)$stream_data->MAXLISTENERS;
    	    $stream->stream_url = "http://".SHOUTCAST_V2_SERVER_HOST.":".SHOUTCAST_V2_SERVER_PORT.((string)$stream_data->STREAMPATH);
    	    $stream->stream_nowplaying = new NowPlayingStruct();
    	    $stream->stream_nowplaying->text = (string)$stream_data->SONGTITLE;
    	    
    	    $nowplaying = explode(" - ", (string)$stream_data->SONGTITLE);
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