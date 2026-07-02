<?php

namespace App\Models;

use Database\Factories\RoomFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Room extends Model
{
    /** @use HasFactory<RoomFactory> */
    use HasFactory;

    protected $fillable = [
        'room_number',
        'price',
        'status',
        'facilities',
    ];

    protected function casts(): array
    {
        return [
            'facilities' => 'array',
            'price' => 'integer',
        ];
    }

    public function tenants(): HasMany
    {
        return $this->hasMany(Tenant::class);
    }

    public function currentTenant(): HasOne
    {
        return $this->hasOne(Tenant::class)->latestOfMany();
    }
}
