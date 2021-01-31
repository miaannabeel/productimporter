<?php namespace Edata\ProductImporter\Models;

use Model;

/**
 * Model
 */
class Files extends Model
{
    use \October\Rain\Database\Traits\Validation;
    
    /*
     * Disable timestamps by default.
     * Remove this line if timestamps are defined in the database table.
     */
    public $timestamps = false;


    /**
     * @var string The database table used by the model.
     */
    public $table = 'edata_productimporter_files';

    /**
     * @var array Validation rules
     */
    public $rules = [
    ];
    public $attachOne = [
        'file_name' => \System\Models\File::class
    ];
}
