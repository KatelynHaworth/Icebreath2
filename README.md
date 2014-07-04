             _____         _                    _   _      _____ 
            |_   _|       | |                  | | | |    / __  \
              | |  ___ ___| |__  _ __ ___  __ _| |_| |__  `' / /'
              | | / __/ _ \ '_ \| '__/ _ \/ _` | __| '_ \   / /  
             _| || (_|  __/ |_) | | |  __/ (_| | |_| | | |./ /___
             \___/\___\___|_.__/|_|  \___|\__,_|\__|_| |_|\_____/
             
 =+ Author:             Liam 'Auzzie' Haworth <production@hiveradio.net>
 =+ Last Update:        20/06/2014
 =+ Latest Version:     2.0.0_BETA
 
 === About Icebreath2
 
 Icebreath2 is a RESTful and modular data api based around broadcasting services
 such as SHOUTcast and Icecast. It connects to your broadcasting server software
 to pull stats from it, organize them and then spit them out in either, JSON, XML,
 HTML or one of the other supported response formats
 
 === Setup
 
 Setting up Icebreath2 should be fairly simple. Most systems will come with everything
 needed already installed but just in case, here is a list of what is needed.
 
 + PHP 5.3+ (Older Version May Work, Tested On PHP 5.3.3)
 + PHP cURL extension
 + PHP libxml and SimpleXML extensions
 + PHP Sessions MUST be enabled!
 

 Once those items have been installed just drop Icebreath2 from the "upload" folder
 into your web directory, if you want to put it in a sub directory that is fine, 
 just make sure to also set it in your Icebreath2 config. If you need some help 
 with your NGINX config, have a look in the "examples" folder for more help.
 
 === Change Log
 
 + Version 2.0.0_BETA: Initial Beta Release
 
 === Notes

 Please not that this project is still in the beta stage and that some things may
 note work how they are expected to.
 
 === Copyright Notice
 
 Copyright (c) Liam 'Auzzie' Haworth <production@hiveradio.net>, 2013-2014.
 
 Permission is hereby granted, free of charge, to any person obtaining a copy
 of this software and associated documentation files (the "Software"), to deal
 in the Software without restriction, including without limitation the rights
 to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 copies of the Software, and to permit persons to whom the Software is
 furnished to do so, subject to the following conditions:
 
 The above copyright notice and this permission notice shall be included in
 all copies or substantial portions of the Software.
 
 THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 THE SOFTWARE.