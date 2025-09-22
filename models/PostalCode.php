<?php namespace Rainlab\Location\Models;

use Model;

/**
 * PostalCode Model
 *
 * @link https://docs.octobercms.com/3.x/extend/system/models.html
 */
class PostalCode extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /**
     * @var string table name
     */
    public $table = 'rainlab_location_postal_codes';

    /**
     * @var array rules for validation
     */
    public $rules = [];

    public $belongsTo = [
        'municipality' => ['Rainlab\Location\Models\Municipality']
    ];
}
