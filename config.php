<?php
 
define( "ICEBREATH_RESPONSE_TYPE",      "JSON" );               //JSON | JSONp | XML | HTML | TEXT
define( "ICEBREATH_SUB_DIR",            null );                 //The URL location that Icebreath is stored in if not the index
define( "ICEBREATH_LOCATION",           "Your/Location" );      //Your timezone location, see: http://www.php.net//manual/en/timezones.php
define( "ICEBREATH_DEBUG",              true  );                //If true Icebreath is in debug mode, php error wont be handled by your PHP server it self and the route /debug will be enabled
define( "ICEBREATH_ADMIN",              "you@yoursite.com" );   //Your e-mail of contact purposes 

/*****CONTROLLER CONFIGS******/


/**==SHOUTcast==**/
define( "SHOUTCAST_V2_SERVER_HOST", null /*"yoursite.com"*/);   //SHOUTcast V2 Server Host Address/IP
define( "SHOUTCAST_V2_SERVER_PORT", null /*"8000"*/);           //SHOUTcast V2 Server Port

                                                                //SHOUTcast V1 Server Array
define( "SHOUTCAST_V1_SERVERS", serialize(array(
        /*array("HOST" => "yoursite.com", "PORT" => "8000", "PASS" => "hackme", "NAME" => "Server Name"),*/
    )));
    
/**==IceCAST==**/
define( "ICECAST_SERVER_HOST", null /*"yoursite.com"*/ );       //IceCAST Server Host Address/IP
define( "ICECAST_SERVER_USER", null /*"admin"*/ );              //IceCAST Server Admin Username
define( "ICECAST_SERVER_PASS", null /*"hackme"*/ );             //IceCAST Server Admin Password
define( "ICECAST_SERVER_PORT", null /*"8000"*/ );               //IceCAST Server Port
