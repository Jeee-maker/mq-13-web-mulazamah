<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'user_id', 'gender', 'total_paid', 'total_debt', 'total_expected'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function yearly()
    {
        return $this->hasMany(MulazamahYearly::class, 'student_id');
    }

    public function monthly()
    {
        return $this->hasMany(MulazamahMonthly::class, 'student_id');
    }
}
