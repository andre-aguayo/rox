<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'enrollment_number',
        'birth_date',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    /**
     * @return BelongsToMany<Subject>
     */
    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class)
            ->withTimestamps();
    }
}
