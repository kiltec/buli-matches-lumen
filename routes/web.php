<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/
$router->get('/', ['as' => 'home', function() {
    return view('home.index');
}]);

$router->get('all-matches/{year}', [
    'as' => 'all-matches',
    'uses' => 'MatchListingController@index'
]);

$router->get('upcoming-matches', [
    'as' => 'upcoming-matches',
    'uses' => 'MatchListingController@upcoming'
]);

$router->get('/win-loss-ratios/{year}', [
    'as' => '/win-loss-ratios',
    'uses' => 'LeagueStatisticsController@winLossRatios'
]);
