<?php
	Route::post('/api/import', 'Edata\ProductImporter\Controllers\Imports@fileImport')->middleware('web');
?>
