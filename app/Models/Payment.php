<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'employer_profile_id',
        'application_id',
        'amount',
        'payment_method',
        'payment_status',
        'transaction_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    // Relationships
    public function employerProfile()
    {
        return $this->belongsTo(EmployerProfile::class);
    }

    public function application()
    {
        return $this->belongsTo(Application::class);
    }
}
