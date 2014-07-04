<?php

use Icebreath\API\RESTfulController;
use Icebreath\API\Tools;

use ShoutIRC\RemoteClient;
use ShoutIRC\RemoteHeaderStruct;

class shoutirc extends RESTfulController {
	
	public function __construct() {
		parent::__construct('/shoutirc');
	}
	
	//Create
	public function restful_put($args)
	{
	    //TODO: Add handling
	}
	
	//Read
	public function restful_get($args)
	{
	    if($args == null || count($args) < 1)
    	    Tools::build_response(array("usage" => "/shoutirc/[artist|song]/{id}"));
    	else
    	{
    	    if($args[0] == "song")
    	        if(count($args) >= 2)
    	            $this->getSong($args[1]);
    	        else
    	            $this->getSongList();
    	    else if($args[0] == "artist")
    	        if(count($args) >= 2)
    	            $this->getArtist($args[1]);
    	        else
    	            $this->getArtistList();
    	}
	}
	
    //Update
	public function restful_post($args)
	{
	    //TODO: Add handling
	}
	
	//Delete
    public function restful_delete($args)
    {
	    //TODO: Add handling
	}
	
	private function getSong($id)
	{
	    $db = new PDO('mysql:host=' . SHOUTIRC_DATABASE_HOST . ';dbname=' . SHOUTIRC_DATABASE_NAME, SHOUTIRC_DATABASE_USER, SHOUTIRC_DATABASE_PASS);
	    $stmt = $db->prepare("SELECT * FROM `AutoDJ` WHERE `ID`=?");
        $stmt->execute(array($id));
        $songs = $stmt->fetchAll(PDO::FETCH_ASSOC);
	    $db = null;
	    Tools::build_response($songs[0]);
	}
	
	private function getSongList()
	{
	    try
	    {
	        $db = new PDO('mysql:host=' . SHOUTIRC_DATABASE_HOST . ';dbname=' . SHOUTIRC_DATABASE_NAME, SHOUTIRC_DATABASE_USER, SHOUTIRC_DATABASE_PASS);
	        $songs = array();
	        foreach($db->query("SELECT * FROM `AutoDJ` ORDER BY `Artist` ASC") as $song)
	            array_push($songs, array(
	                "ID" => $song["ID"],
	                "Title" => $song["Title"],
	                "Artist" => $song["Artist"],
	                "Album" => $song["Album"],
	                "Length" => $song["SongLen"],
	                "ReqCount" => $song["ReqCount"]
	            ));
	       $db = null;
	       Tools::build_response($songs);
	    }
	    catch (PDOException $ex) {
	        Tools::ib_die_nicely("ShoutIRC Controller | getSongList() | Database PDO Error | " . $ex->getMessage());
	    }
	}
	
	private function getArtist($id)
	{
	    $db = new PDO('mysql:host=' . SHOUTIRC_DATABASE_HOST . ';dbname=' . SHOUTIRC_DATABASE_NAME, SHOUTIRC_DATABASE_USER, SHOUTIRC_DATABASE_PASS);
	    $stmt = $db->prepare("SELECT * FROM `AutoDJ_Artist` WHERE `ID`=?");
        $stmt->execute(array($id));
        $artists = $stmt->fetchAll(PDO::FETCH_ASSOC);
	    $db = null;
	    Tools::build_response($artists[0]);
	}
	
	private function getArtistList()
	{
	    try
	    {
	        $db = new PDO('mysql:host=' . SHOUTIRC_DATABASE_HOST . ';dbname=' . SHOUTIRC_DATABASE_NAME, SHOUTIRC_DATABASE_USER, SHOUTIRC_DATABASE_PASS);
	        $artists = array();
	        foreach($db->query("SELECT * FROM `AutoDJ_Artist` ORDER BY `Name` ASC") as $artist)
	            array_push($artists, array(
	                "ID" => $artist["ID"],
	                "Name" => $artist["Name"]
	            ));
	       $db = null;
	       Tools::build_response($artists);
	    }
	    catch (PDOException $ex) {
	        Tools::ib_die_nicely("ShoutIRC Controller | getArtistList() | Database PDO Error | " . $ex->getMessage());
	    }
	}
}