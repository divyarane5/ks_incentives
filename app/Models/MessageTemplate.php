<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MessageTemplate extends Model
{
    public const PLACEHOLDERS = [
        '{name}' => "Lead's first name",
        '{full_name}' => "Lead's full name",
        '{project}' => 'First project linked to the lead',
        '{date}' => 'Next follow-up date and time',
        '{my_name}' => 'Your name',
        '{my_phone}' => 'Your phone number',
    ];

    protected $fillable = ['name', 'body', 'is_active', 'sort_order'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    /** The message with this lead's and sender's details filled in. */
    public function textFor(Lead $lead, User $sender): string
    {
        return strtr($this->body, [
            '{name}' => strtok(trim($lead->name), ' ') ?: 'there',
            '{full_name}' => $lead->name,
            '{project}' => $lead->projects->first()?->name ?? 'our projects',
            '{date}' => $lead->next_follow_up_at?->format('D d M, h:i A') ?? 'the agreed time',
            '{my_name}' => $sender->name,
            '{my_phone}' => $sender->phone ?? '',
        ]);
    }
}
