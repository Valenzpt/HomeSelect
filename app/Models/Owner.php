<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Owner extends Model
{
    protected $table = 'owners';

    protected $fillable = [
        'name',
        'phone_number',
        'email',
    ];

    /**
     * Get all of the comments for the Owner
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function apartments()
    {
        return $this->hasMany(Apartment::class, 'owner_id');
    }
}
