<?php
/**
 * Welcome to Icebreath2!
 * 
 * What you are looking at right now is the configuration file for Icebreath2
 * it holds all the settings for Icebreath2 itself and it's controllers.
 * 
 * Just have a look at the instuction blocks, like this one, above each set of
 * settings to get an idea of what is what and what to change.
 * 
 *------------------------------------------------------------------------------
 * 
 * The following settings are for Icebreath2.
 * 
 * ICEBREATH_RESPONSE_TYPE
 *      
 *      - This changes the default format that data is returned in it can be set
 *        to JSON, JSONp, XML, HTML or TEXT. Even if a default format is set, 
 *        the response format can still be changed in the request itself by 
 *        adding `/format/[TYPE]/` to the front of the request.
 * 
 * ICEBREATH_SUB_DIR
 * 
 *      - If you have Icebreath2 installed in a sub directory then this is a
 *        requirement for you. Lets say you have Icebreath2 in
 *        `http://yoursite.com/icebreath/`, then this settings would be set to
 *        `/icebreath`.
 *          
 * ICEBREATH_LOCATION
 * 
 *      - This setting is pretty much the time zone location that is primarily
 *        used by this system. For more information on time zones, have a look
 *        at: http://www.php.net//manual/en/timezones.php
 * 
 * ICEBREATH_DEBUG
 * 
 *      - When this setting is set to `true` all php errors are handled by your
 *        PHP engine AND Icebreath2, along with this, the route '/debug' becomes
 *        enabled. You can use this route to get information about your system to
 *        help developers debug your problem if you have encounter one.
 * 
 * ICEBREATH_ADMIN
 * 
 *      - Your primary contact E-Mail address, this isn't mandatory but most
 *        controllers return this in error's so that users can contact you to
 *        report a problem
 **/
define( "ICEBREATH_RESPONSE_TYPE",      "JSON" );
define( "ICEBREATH_SUB_DIR",            null );
define( "ICEBREATH_LOCATION",           "Your/Location" );
define( "ICEBREATH_DEBUG",              true );
define( "ICEBREATH_ADMIN",              "you@yoursite.com" );


/**
 * SHOUTcast is cross-platform proprietary software for streaming media over the 
 * Internet. The software, developed by Nullsoft, allows digital audio content, 
 * primarily in MP3 or HE-AAC format, to be broadcast to and from media player 
 * software, enabling the creation of Internet radio "stations".
 * 
 * The SHOUTcast controller for Icebreath2 allows the retrieval of server data
 * so it can be accessed by other system to display stream data, for example,
 * using JavaScript and jQuery to take the data and display it nicely on your
 * station's website.
 * 
 * This controller is compatable with both SHOUTcast version 1 and 2.
 *------------------------------------------------------------------------------
 * 
 * The following settings are for the SHOUTcast controller.
 * 
 * SHOUTCAST_V2_SERVER_HOST
 * 
 *      - The host address or IP of your SHOUTcast version 2 server
 * 
 * SHOUTCAST_V2_SERVER_PORT
 * 
 *      - The port on which your SHOUTcast version 2 server is running on
 * 
 * SHOUTCAST_V1_SERVERS
 * 
 *      - This is a list of different SHOUTcast version 1 servers. You can have
 *        one or more set, there is no limmit.
 * 
 *          + HOST - SHOUTcast version 1 server host address or IP
 *          + PORT - SHOUTcast version 1 server port
 *          + PASS - SHOUTcast version 1 server admin password
 *          + NAME - Unique name for the SHOUTcast version 1 server
 **/

define( "SHOUTCAST_V2_SERVER_HOST", null /*"yoursite.com"*/);
define( "SHOUTCAST_V2_SERVER_PORT", null /*"8000"*/);
define( "SHOUTCAST_V1_SERVERS", serialize(array(
    /*array("HOST" => "yoursite.com", "PORT" => "8000", "PASS" => "hackme", "NAME" => "Server Name"),*/
)));
    

/**
 * The Icecast server is capable of streaming content as Vorbis over standard 
 * HTTP, Theora over HTTP, MP3 over the communications protocol used by 
 * SHOUTcast, AAC, and NSV over the SHOUTcast protocol (Theora, AAC, and NSV are 
 * only supported in version 2.2.0 and newer). 
 * 
 * The Icecast controller is great for the task of simply and quickly gathering
 * stream data from your server for displaying on your website, mobile 
 * application or even more!
 * 
 * This controller is was tested with Icecast 2.3, older version unknown
 *------------------------------------------------------------------------------
 * 
 * The following settings are for the Icecast controller.
 * 
 * ICECAST_SERVER_HOST
 * 
 *      - The host address or IP of your Icecast server
 * 
 * ICECAST_SERVER_PORT
 * 
 *      - The port on which your Icecast server is running on
 * 
 * ICECAST_SERVER_USER
 * 
 *      - The username for your Icecast admin account
 * 
 * ICECAST_SERVER_PASS
 * 
 *      - The password used to login into your ICecast admin account
 * 
 **/
define( "ICECAST_SERVER_HOST", null /*"yoursite.com"*/ ); 
define( "ICECAST_SERVER_PORT", null /*"8000"*/ );
define( "ICECAST_SERVER_USER", null /*"admin"*/ );
define( "ICECAST_SERVER_PASS", null /*"hackme"*/ );


/**
 * ShoutIRC is a all-in-one system for online radio stations, it can act as a
 * AutoDJ that accepts song requests, it can interact with users on IRC, tweet
 * to your stations twitter and more!
 * 
 * ShoutIRC can broadcast to SHOUTcast v1/2, Icecast and Streamcast in many 
 * formats such as MP3, AAC+, Ogg and many more! Check it out: http://shoutirc.com
 * 
 * This controller is not finished and may still have bugs and unfinished
 * features!
 *------------------------------------------------------------------------------
 * 
 * The following settings are for the ShoutIRC controller.
 * 
 * SHOUTIRC_SERVER_HOST
 * 
 *      - Your ShoutIRC server host address or IP
 * 
 * SHOUTIRC_SERVER_PORT
 * 
 *      - The remote port set in your `ircbot.conf`
 * 
 * SHOUTIRC_RADIOBOT_USER
 * 
 *      - A RadioBot user with the flags: +hrniqsa
 * 
 * SHOUTIRC_RADIOBOT_PASS
 * 
 *      - The password for the RadioBot user
 * 
 * SHOUTIRC_DATABASE_HOST
 * 
 *      - The database host used by the ShoutIRC AutoDJ
 * 
 * SHOUTIRC_DATABASE_USER
 * 
 *      - Username to access the database
 * 
 * SHOUTIRC_DATABASE_PASS
 * 
 *      - The password linked to the database user
 * 
 * SHOUTIRC_DATABASE_NAME
 * 
 *      - The name of the database (Normally: ShoutIRC)
 * 
 **/
define( "SHOUTIRC_SERVER_HOST",   null /*"yoursite.com"*/ );
define( "SHOUTIRC_SERVER_PORT",   null /*"10001"*/ );
define( "SHOUTIRC_RADIOBOT_USER", null /*"RadioBot"*/ );
define( "SHOUTIRC_RADIOBOT_PASS", null /*"hackme"*/ );
define( "SHOUTIRC_DATABASE_HOST", null /*"yoursite.com"*/ );
define( "SHOUTIRC_DATABASE_USER", null /*"RadioBot"*/ );
define( "SHOUTIRC_DATABASE_PASS", null /*"hackme"*/ );
define( "SHOUTIRC_DATABASE_NAME", null /*"ShoutIRC"*/ );
