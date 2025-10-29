<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $primaryKey = 'category_id';

    protected $fillable = [
        'name',
        'description',
        'color',
        'icon',
        'type'
    ];

    // Relationships
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'category_id');
    }
}
