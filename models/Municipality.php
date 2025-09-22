<?php namespace Rainlab\Location\Models;

use Model;

/**
 * Municipality Model
 *
 * @link https://docs.octobercms.com/3.x/extend/system/models.html
 */
class Municipality extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /**
     * @var string table name
     */
    public $table = 'rainlab_location_municipalities';

    /**
     * @var array rules for validation
     */
    public $rules = [];

    public $hasMany = [
        'postalCodes' => ['Rainlab\Location\Models\PostalCode']
    ];
}
