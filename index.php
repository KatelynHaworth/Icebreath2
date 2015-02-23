<?php
define( "ICEBREATH_VERSION", "2.0.0_BETA" );
/**
 * 
 * @Author Liam 'Auzzie' Haworth
 * @name Icebreath
 * @version 2.0.0_BETA
 * @copyright Copyright (c) Liam 'Auzzie' Haworth <liam@auzzie.pw>, 2013-2014
 * 
 * Icebreath is designed to be a quick and easy to use
 * moduler based data access and controll system
 * for ShoutIRC along with SHOUTcast and Icecast
 *
 * Copyright (c) Liam 'Auzzie' Haworth <production@hiveradio.net>, 2013-2014.
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
 
 /*Sets the default time zone to UTC to protect the system from date/time problems*/
 date_default_timezone_set('UTC');
 
 /*Define the base locations of this script on the system and the APIs location on the system*/
 define("DS", DIRECTORY_SEPARATOR);
 define("ICE_BAS_DIR" , getcwd());
 define("ICE_API_DIR" , ICE_BAS_DIR . DS . 'includes');
 define("ICE_CON_DIR" , ICE_BAS_DIR . DS . 'Controllers');
 define("ICE_VEN_DIR" , ICE_BAS_DIR . DS . 'vendor');
 
 /*Load system config*/
 require_once ICE_BAS_DIR . '/config.php';
 
 /*Load all API's into the system*/
 require_once ICE_API_DIR . '/ib.all.php';
 
 /*Register the global PHP error handler*/
 function ib_error_catch($error_level, $error_message, $error_file, $error_line) {
 	Icebreath\API\Tools::ib_die_nicely("PHP [$error_level] - $error_message - $error_file:$error_line");
 }
 set_error_handler("ib_error_catch");
 
 /*Register catch for fatal errors*/
 function ib_fatal_catch() {
 	$errfile = "unknown file";
	$errstr  = "shutdown";
	$errno   = E_CORE_ERROR;
	$errline = 0;

	$error = error_get_last();

	if( $error !== NULL) {
		$errno   = $error["type"];
		$errfile = $error["file"];
		$errline = $error["line"];
		$errstr  = $error["message"];
		
		ib_error_catch($errno, $errstr, $errfile, $errline);
  	}
 }
 ini_set('display_errors', ICEBREATH_DEBUG);
 register_shutdown_function("ib_fatal_catch");
 
 /*If all is good above then start the main application*/	
 require_once ICE_BAS_DIR . "/App.php";
 Icebreath\App::run();
?>
