<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    /**
     * Defina a relação com o modelo UserProductPermission.
     */
    public function permissions()
    {
        return $this->hasMany(UserProductPermission::class);
    }

    /**
     * Defina a relação com o modelo Company (empresa do produto).
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'id_company'); // Relacionamento com a empresa
    }

    public function show()
{
    $idCompany = auth()->user()->company_id; // Ou de onde você estiver pegando o ID da empresa
    return view('seu-template', compact('idCompany'));
}

}
