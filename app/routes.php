<?php

// First apart: Basic routes

Route::get('/', ['uses' => 'PXController@index']);

Route::get('/ajax/index_more', ['uses' => 'PXController@loadMore']);

Route::get('/user/{id}', ['uses' => 'PXController@photosByUser']);


// Second part: Posting

Route::post('/ajax/photo/vote', ['uses' => 'PXController@vote']);

Route::post('/ajax/photo/favorite', ['uses' => 'PXController@favorite']);

Route::get('/photo/{id}', ['uses' => 'PXController@show']);

Route::post('/photo/comment', ['uses' => 'PXController@comment']);

Route::get('/upload', ['uses' => 'PagesController@photoUpload']);

Route::post('/photo/upload', ['as' => 'photo.upload', 'uses' => 'PXController@upload']);
