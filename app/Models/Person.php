<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Person extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'cpf',
        'type',
        'phone',
        'email',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Scope para filtrar por tipo
     */
    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    /**
     * Scope para buscar por nome
     */
    public function scopeByName(Builder $query, string $name): Builder
    {
        return $query->where('name', 'like', '%' . $name . '%');
    }

    /**
     * Scope para buscar por CPF
     */
    public function scopeByCpf(Builder $query, string $cpf): Builder
    {
        return $query->where('cpf', $cpf);
    }

    /**
     * Accessor para formatar o CPF
     */
    public function getFormattedCpfAttribute(): string
    {
        $cpf = $this->cpf;
        if (strlen($cpf) === 11) {
            return substr($cpf, 0, 3) . '.' . substr($cpf, 3, 3) . '.' . substr($cpf, 6, 3) . '-' . substr($cpf, 9, 2);
        }
        return $cpf;
    }

    /**
     * Accessor para o tipo em português
     */
    public function getTypeNameAttribute(): string
    {
        return $this->type === 'individual' ? 'Pessoa Física' : 'Pessoa Jurídica';
    }
}
