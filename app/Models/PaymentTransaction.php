<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentTransaction extends Model
{
    protected $fillable = [
        'student_id', 'dest_bank_info', 'user_bank', 'user_account_no',
        'user_account_name', 'amount', 'notes', 'receipt_path', 'status', 'actioned_by'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
