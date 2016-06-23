<?php
/**
 * Copyright (c) 2012-2016 Veridu Ltd <https://veridu.com>
 * All rights reserved.
 */

namespace App\Model;

use App\Extension\CreatedUnixTimestamp;
use App\Helper\Utils;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Credentials Model.
 *
 * @apiEntity Credential
 * @apiEntityRequiredProperty string name
 * @apiEntityProperty string slug
 * @apiEntityProperty bool production
 * @apiEntityProperty string public
 * @apiEntityProperty int created
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property bool $production
 * @property int $company_id
 * @property string $public
 * @property string $private
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property int $created
 */
class Credential extends Model {
    use SoftDeletes, CreatedUnixTimestamp;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'credentials';

    /**
     * The storage format of the model's date columns.
     *
     * @var string
     */
    protected $dateFormat = 'Y-m-d H:i:s';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'production', 'company_id'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $visible = ['id', 'name', 'slug', 'production', 'public', 'created'];

    /**
     * The accessors to append to the mode's array form.
     *
     * @var array
     */
    protected $appends = ['created'];

    /**
     * Name Attribute Mutator.
     *
     * @param string $value
     *
     * @return void
     */
    public function setNameAttribute($value) {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Utils::slugify($value);
    }

    /**
     * Get the owner company.
     *
     * @return App\Model\Company
     */
    public function company() {
        return $this->belongsTo('App\\Model\\Company');
    }
}
