<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('instituicoes', 'api\CreditSimulatorController@getInstitutions');
Route::get('convenios', 'api\CreditSimulatorController@getAgreements');
Route::get('taxasInstituicoes', 'api\CreditSimulatorController@getInstitutionsFeels');
Route::post('simularCredito', 'api\CreditSimulatorController@creditSimulator');

/*
Route::post('auth/login', 'AuthController@login');

Route::group(['middleware' => ['apiJwt']], function() {
	Route::get('instituicoes', 'api\CreditSimulatorController@getInstitutions');
	Route::get('convenios', 'api\CreditSimulatorController@getAgreements');
	Route::get('taxasInstituicoes', 'api\CreditSimulatorController@getInstitutionsFeels');
	Route::post('simularCredito', 'api\CreditSimulatorController@creditSimulator');
});