<?php

Route::group(['namespace' => 'Abs\UomPkg', 'middleware' => ['web', 'auth'], 'prefix' => 'uom-pkg'], function () {
	//Uom
	Route::get('/uom/get-list', 'UomController@getUomList')->name('getUomList');
	Route::get('/uom/get-form-data', 'UomController@getUomFormData')->name('getUomFormData');
	Route::post('/uom/save', 'UomController@saveUom')->name('saveUom');
	Route::get('/uom/delete', 'UomController@deleteUom')->name('deleteUom');
	Route::get('/uom/get-filter-data', 'UomController@getUomFilterData')->name('getUomFilterData');

});