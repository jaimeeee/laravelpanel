<?php

/**
 * Set routes inside the web middleware.
 */
Route::group(['middleware' => 'web', 'prefix' => config('panel.url')], function () {

    /*
     * Route to the panel's dashboard
     */
    Route::get('/', 'Jaimeeee\Panel\Controllers\PanelController@dashboard');

    /*
     * List certain model
     */
    Route::get('{entity}', 'Jaimeeee\Panel\Controllers\PanelController@formList')->where('entity', '[a-z]+');
    Route::get('{entity}/{id}/{child}', 'Jaimeeee\Panel\Controllers\PanelController@childrenList')
        ->where(['entity' => '[a-z]+', 'id' => '[0-9]+', 'child' => '[a-z]+']);

    /*
     * Simple CRUD methods
     */
    Route::get('{entity}/create', 'Jaimeeee\Panel\Controllers\PanelController@create')->where('entity', '[a-z]+');
    Route::post('{entity}/create', 'Jaimeeee\Panel\Controllers\PanelController@publish')->where('entity', '[a-z]+');
    Route::get('{entity}/edit/{id}', 'Jaimeeee\Panel\Controllers\PanelController@edit')->where(['entity' => '[a-z]+', 'id' => '[0-9]+']);
    Route::post('{entity}/edit/{id}', 'Jaimeeee\Panel\Controllers\PanelController@update')->where(['entity' => '[a-z]+', 'id' => '[0-9]+']);
    Route::get('{entity}/delete/{id}', 'Jaimeeee\Panel\Controllers\PanelController@delete')->where(['entity' => '[a-z]+', 'id' => '[0-9]+']);

    /*
     * Children CRUD methods
     */
    Route::get('{entity}/{record}/{child}/create', 'Jaimeeee\Panel\Controllers\PanelController@create')->where(['entity' => '[a-z]+', 'record' => '[0-9]+', 'child' => '[a-z]+']);
    Route::post('{entity}/{record}/{child}/create', 'Jaimeeee\Panel\Controllers\PanelController@publish')->where(['entity' => '[a-z]+', 'record' => '[0-9]+', 'child' => '[a-z]+']);
    Route::get('{entity}/{record}/{child}/edit/{id}', 'Jaimeeee\Panel\Controllers\PanelController@edit')->where(['entity' => '[a-z]+', 'record' => '[0-9]+', 'child' => '[a-z]+', 'id' => '[0-9]+']);
    Route::post('{entity}/{record}/{child}/edit/{id}', 'Jaimeeee\Panel\Controllers\PanelController@update')->where(['entity' => '[a-z]+', 'record' => '[0-9]+', 'child' => '[a-z]+', 'id' => '[0-9]+']);
    Route::get('{entity}/{record}/{child}/delete/{id}', 'Jaimeeee\Panel\Controllers\PanelController@delete')->where(['entity' => '[a-z]+', 'record' => '[0-9]+', 'child' => '[a-z]+', 'id' => '[0-9]+']);
});
