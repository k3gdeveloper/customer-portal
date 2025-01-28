<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    use HasFactory;

    protected $table = 'logs';
    protected $primaryKey = 'id';

    protected $fillable = [
        'logtext',
        'id_device'
    ];

    protected $hidden = [
        'update_at'
    ];

    public function dispositivo()
    {
        return $this->belongsTo(Devices::class, 'id_device');
    }
}
