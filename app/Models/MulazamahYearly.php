<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MulazamahYearly extends Model
{
    protected $fillable = [
        'student_id', 'hijri_year', 'total_paid', 'total_debt', 'total_expected'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
