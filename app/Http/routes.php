<?php

Route::get('/{domain}/{module}', '\Sardoj\Uccello\Http\Controllers\IndexController@process')->name('index');
Route::get('/{domain}/{module}/list', '\Sardoj\Uccello\Http\Controllers\ListController@process')->name('list');
Route::get('/{domain}/{module}/detail', '\Sardoj\Uccello\Http\Controllers\DetailController@process')->name('detail');
Route::get('/{domain}/{module}/edit', '\Sardoj\Uccello\Http\Controllers\EditController@process')->name('edit');