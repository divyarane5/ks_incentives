<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LetterTemplate extends Model
{
    public const TYPES = [
        'offer' => 'Offer', 'appointment' => 'Appointment', 'confirmation' => 'Confirmation',
        'increment' => 'Increment', 'relieving' => 'Relieving', 'experience' => 'Experience', 'other' => 'Other',
    ];

    protected $fillable = ['name', 'type', 'subject', 'body', 'active'];

    protected $casts = ['active' => 'boolean'];
}
