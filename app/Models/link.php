<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_company',
        'graphic',
        'map',
        'ticket',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
