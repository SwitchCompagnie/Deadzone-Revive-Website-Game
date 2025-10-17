<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlayerObject extends Model
{
    protected $table = 'player_objects';
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