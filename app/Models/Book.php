<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    use HasFactory;

    protected $fillable = ['isbn', 'title', 'author', 'publisher', 'published_year', 'category', 'stock', 'available_stock', 'description'];
    protected $casts = ['published_year' => 'integer', 'stock' => 'integer', 'available_stock' => 'integer'];

    public function loans(): HasMany { return $this->hasMany(Loan::class); }
}
