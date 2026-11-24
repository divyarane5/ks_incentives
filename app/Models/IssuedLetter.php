<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IssuedLetter extends Model
{
    protected $fillable = ['user_id', 'letter_template_id', 'type', 'subject', 'body', 'letter_date', 'reference', 'issued_by', 'letter_signatory_id', 'no_signature', 'signed_pdf'];

    protected $casts = ['letter_date' => 'date', 'no_signature' => 'boolean'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function issuer()
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public function signatory()
    {
        return $this->belongsTo(LetterSignatory::class, 'letter_signatory_id');
    }

    public function template()
    {
        return $this->belongsTo(LetterTemplate::class, 'letter_template_id');
    }
}
