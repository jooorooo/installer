<?php
Route::group(['prefix' => 'Installer@install', 'namespace' => 'Simexis\Installer\Controllers\Install'], function()
{
	Route::group(['middleware' => 'installerCanInstall'], function(){
		Route::get('/', [
			'as' => 'installer::welcome',
			'uses' => 'HomeController@index'
		]);
		Route::get('permissions', [
			'as' => 'installer::permissions',
			'uses' => 'PermissionsController@index'
		]);
		Route::get('database', [
			'as' => 'installer::database',
			'uses' => 'DatabaseController@index'
		]);
		Route::post('database', [
			'as' => 'installer::database',
			'uses' => 'DatabaseController@post'
		]);
	});
	Route::get('finish', [
		'as' => 'installer::finish',
		'uses' => 'FinishController@index'
	]);
});

Route::group(['prefix' => 'Installer@update', 'namespace' => 'Simexis\Installer\Controllers\Update'], function()
{
    Route::group(['middleware' => 'installerCanUpdate'], function(){
        Route::get('/', [
            'as' => 'installer::upgrade',
            'uses' => 'HomeController@index'
        ]);
        Route::get('process', [
            'as' => 'installer::process',
            'uses' => 'UpgradeController@index'
        ]);
    });
});

Route::group(['prefix' => 'Installer@assets', 'namespace' => 'Simexis\Installer\Controllers'], function()
{
    Route::get(sprintf('stylesheets?%s', app('Simexis\Installer\Helpers\Render')->getModifiedTime('css')), [
		'as' => 'installer::assets.css',
		'uses' => 'AssetController@css'
	]);
    Route::get('stylesheets', 'AssetController@css');
	
    Route::get(sprintf('javascript?%s', app('Simexis\Installer\Helpers\Render')->getModifiedTime('js')), [
		'as' => 'installer::assets.js',
		'uses' => 'AssetController@js'
	]);
	
    Route::get('javascript', 'AssetController@js');
});