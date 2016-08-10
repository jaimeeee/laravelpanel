<?php

/**
 * Set routes inside the web middleware
 */
Route::group(['middleware' => 'web', 'prefix' => config('panel.url')], function() {
    
    /**
     * Route to the panel's dashboard
     */
    Route::get('/', 'Jaimeeee\Panel\Controllers\PanelController@dashboard');
    
    /**
     * List certain model
     */
    Route::get('{entity}', 'Jaimeeee\Panel\Controllers\PanelController@list')->where('entity', '[a-z]+');
    
    /**
     * CRUD methods
     */
    Route::get('{entity}/create', 'Jaimeeee\Panel\Controllers\PanelController@create')->where('entity', '[a-z]+');
    Route::post('{entity}/create', 'Jaimeeee\Panel\Controllers\PanelController@publish')->where('entity', '[a-z]+');
    Route::get('{entity}/{id}', 'Jaimeeee\Panel\Controllers\PanelController@edit')->where(['entity' => '[a-z]+', 'id' => '[0-9]+']);
    Route::post('{entity}/{id}', 'Jaimeeee\Panel\Controllers\PanelController@update')->where(['entity' => '[a-z]+', 'id' => '[0-9]+']);
    Route::get('{entity}/{id}/delete', 'Jaimeeee\Panel\Controllers\PanelController@delete')->where(['entity' => '[a-z]+', 'id' => '[0-9]+']);
});
