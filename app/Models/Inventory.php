<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $table = 'inventory';
    protected $primaryKey = 'player_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'player_id',
        'data_json'
    ];

    protected $casts = [
        'data_json' => 'array'
    ];

    public function player()
    {
        return $this->belongsTo(PlayerAccount::class, 'player_id', 'player_id');
    }
}