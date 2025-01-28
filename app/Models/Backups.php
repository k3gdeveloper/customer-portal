<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Backups extends Model
{
    use HasFactory;

    protected $table = 'backup';
    protected $primaryKey = 'id';
    //public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'bktext',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'id_device', "id",
    ];

    public function dispositivo()
    {
        return $this->belongsTo(Devices::class,'id');
    }

}
