<?php
Route::group(['namespace' => 'Abs\UomPkg\Api', 'middleware' => ['api', 'auth:api']], function () {
	Route::group(['prefix' => 'api/uom-pkg'], function () {
		Route::post('punch/status', 'PunchController@status');
	});
});