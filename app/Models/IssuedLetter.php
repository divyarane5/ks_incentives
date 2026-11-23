<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IssuedLetter extends Model
{
    protected $fillable = ['user_id', 'letter_template_id', 'type', 'subject', 'body', 'letter_date', 'reference', 'issued_by'];

    protected $casts = ['letter_date' => 'date'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function issuer()
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public function template()
    {
        return $this->belongsTo(LetterTemplate::class, 'letter_template_id');
    }
}
