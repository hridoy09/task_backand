<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KycSubmission extends Model
{
    use HasFactory;

    protected $casts = [
        'submitted_data' => 'array'
    ];

    protected $fillable = [
        'submitted_data',
        'user_id',
        'full_name',
        'document_number',
        'document_front',
        'document_back',
        'status',
    ];
}
