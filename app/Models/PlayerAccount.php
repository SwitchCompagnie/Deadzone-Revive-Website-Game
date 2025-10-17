<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlayerAccount extends Model
{
    protected $table = 'player_accounts';
    protected $primaryKey = 'player_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'player_id',
        'hashed_password',
        'email',
        'display_name',
        'avatar_url',
        'created_at',
        'last_login',
        'country_code',
        'server_metadata_json'
    ];

    protected $casts = [
        'created_at' => 'integer',
        'last_login' => 'integer',
        'server_metadata_json' => 'array'
    ];

    public function inventory()
    {
        return $this->hasOne(Inventory::class, 'player_id', 'player_id');
    }

    public function playerObjects()
    {
        return $this->hasOne(PlayerObject::class, 'player_id', 'player_id');
    }

    public function neighborHistory()
    {
        return $this->hasOne(NeighborHistory::class, 'player_id', 'player_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($player) {
            $player->inventory()->delete();
            $player->neighborHistory()->delete();
            $player->playerObjects()->delete();
        });
    }
}