<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Devices extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'devices';
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id_netbox', 'name', 'so', 'ip', 'ssh', 'rsakey', 'user', 'password'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        //
    ];

    public function backups()
    {
        return $this->hasMany(Backups::class, 'id_device');
    }

    public function logs()
    {
        return $this->hasMany(Log::class, 'id_device');
    }
}
