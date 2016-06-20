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
 * Companies Model.
 *
 * @apiEntity Company
 * @apiEntityRequiredProperty string name Company name
 * @apiEntityProperty string slug Slug based on company's name
 * @apiEntityProperty string public_key Public Key for management calls
 * @apiEntityProperty int created Company creation Unixtimestamp
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $public_key
 * @property string $private_key
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property int $created
 */
class Company extends Model {
    use SoftDeletes, CreatedUnixTimestamp;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'companies';

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
    protected $fillable = ['name', 'parent_id'];

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
    protected $visible = ['name', 'slug', 'public_key', 'created'];

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
     * Get the company's credentials.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function credentials() {
        return $this->hasMany('App\\Model\\Credential');
    }
}
