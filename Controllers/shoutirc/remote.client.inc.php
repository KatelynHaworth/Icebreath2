<?php
/**
 * This script is based on the 'remote.client.inc.php'
 * script that came with the WebRequest-2.0.16.zip
 * package from http://shoutirc.com/
 * 
 * This script has been built to used in conjunction
 * with the Icebreath2 shoutirc controller
 **/
 
namespace ShoutIRC;

use Icebreath\API\Tools;

class RemoteClient {
    private $user_level;
    private $user_flags;
    private $fsock;
    private $error;
    
    public function get_error() { return $this->error; }
    public function set_error($msg) { $this->error = $msg; if(ICEBREATH_DEBUG){ Tools::ib_die_nicely($msg); }}
    public function get_user_level() { $this->user_level; }
    public function get_user_flags() { $this->user_flags; }
    
    public function __construct($username="", $password="", $host="", $port="")
    {
        if ($username == "") { $username = SHOUTIRC_RADIOBOT_USER; }
        if ($password == "") { $password = SHOUTIRC_RADIOBOT_PASS; }
        if ($host == "") { $host = SHOUTIRC_SERVER_HOST; }
        if ($port == "") { $port = SHOUTIRC_SERVER_PORT; }
        
        $this->fsock = @fsockopen($host, $port, $errno, $errstr, 10);
        
        if($this->fsock !== FALSE)
        {
            $login_command = $username . "\xFE" . $password . "\xFE\x17\xFEIcebreath2 ShoutIRC Remote Client";
            
            $data = pack("VV", 0, strlen($login_command)) . $login_command;
			fwrite($this->fsock, $data, strlen($data));
            $response = new RemoteHeaderStruct();
            $response->receive($this->fsock);
            
            if($response->getCommand() != 1)
            {
                $this->set_error("Failed to login in to the ShoutIRC bot!");
                $this->disconnect();
                return;
            }
            
            $this->user_level = unpack("C", substr($response->getData(), 0, 1));
		    $this->user_level = $this->user_level[1];
		    if ($this->user_level == 0) {
		        $this->user_flags = unpack("V", substr($response->getData(), 1, 4));
		        $this->user_flags = $this->user_flags[1];
		    }
        }
        else
            $this->set_error("Failed to connect to the ShoutIRC host server. Error[$errno]: $errstr");
    }
    
    public function disconnect()
    {
        if($this->fsock != null && $this->fsock !== FALSE)
            fclose($this->fsock);
    }
    
    public function send_command($command, $get_response=true)
    {
        /*if(!is_a($command, "RemoteHeaderStruct"))
            return false;*/
            
        fwrite($this->fsock, $command->getData(), $command->getLength());
        fflush($this->fsock);
        
        if($get_response)
        {
            $response = new RemoteHeaderStruct();
            $response->receive($this->fsock);
            return $response;
        }
        else
            return null;
    }
}


class RemoteHeaderStruct {
    private $data;
    private $code;
    private $length;
    
    public function __construct($code="\x00", $data="")
    {
        $this->code = $code;
        $this->data = $this->pack_long($code) . $this->pack_long(strlen($data)) . $data;
        $this->length = strlen($this->data);
    }
    
    public function receive($fsock)
    {
        $data = fread($fsock, 8);
        
        if(strlen($data) != 8)
            Tools::ib_die_nicely("ShoutIRC Remote Header length data is not 8 bytes long!");
        
        $this->code = unpack("V", substr($data, 0, 4));
		$this->code = $this->code[1];
		$this->length = unpack("V", substr($data, 4, 4));			
		$this->length = $this->length[1];
		
		$this->data = fread($fsock, $this->length);
    }
    
    public function getData() { return $this->data; }
    public function getCommand() { return $this->code; }
    public function getLength() { return $this->length; }
    private function pack_long($val) { return pack("V", $val); }
}