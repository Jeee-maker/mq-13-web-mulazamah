<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MulazamahMonthly extends Model
{
    protected $fillable = [
        'student_id',
        'hijri_year',
        'hijri_month',
        'is_paid',
        'amount',
    ];

    protected function casts(): array
    {
        return [
            'is_paid' => 'boolean',
        ];
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
