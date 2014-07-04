<?php
use Icebreath\API\Route;

Route::add_route('/', "BaseController");
Route::add_route('/debug', "DebugController");
Route::add_restful_route('/shoutcast', 'shoutcast');
Route::add_restful_route('/icecast', 'icecast');
Route::add_restful_route('/shoutirc', 'shoutirc');