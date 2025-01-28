<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'group_product_id',
        'id_company',  // Adicione 'id_company' se necessário
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Defina a relação com o modelo GroupProduct.
     */
    public function groupProduct()
    {
        return $this->belongsTo(GroupProduct::class, 'group_product_id');
    }

    /**
     * Defina a relação com o modelo Company (empresa do usuário).
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'id_company'); // Relacionamento com a empresa
    }
}
