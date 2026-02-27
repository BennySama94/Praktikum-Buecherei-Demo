<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $fillable = [
        'title',
        'author',
        'isbn',
        'genre',
        'year',
        'total_copies',
    ];

    protected $appends = ['available_copies'];

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    public function getAvailableCopiesAttribute(): int
    {
        return $this->total_copies - $this->loans()->where('status', 'active')->count();
    }

    public function scopeWithAvailability($query)
    {
        return $query->withCount([
            'loans as active_loans_count' => fn ($q) => $q->where('status', 'active'),
        ]);
    }
}
