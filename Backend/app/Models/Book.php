<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Book extends Model
{

    use HasFactory;

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
        if (isset($this->attributes['active_loans_count'])) {
            return $this->total_copies - (int) $this->attributes['active_loans_count'];
        }

        return $this->total_copies - $this->loans()->where('status', 'active')->count();
    }

    public function scopeWithAvailability($query)
    {
        return $query->withCount([
            'loans as active_loans_count' => fn($q) => $q->where('status', 'active'),
        ]);
    }
}
