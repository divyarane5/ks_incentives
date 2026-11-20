<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/** Saved WhatsApp messages with placeholders like {name} and {project}. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('message_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->text('body');
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        $now = now();
        DB::table('message_templates')->insert(array_map(fn ($t) => $t + ['created_at' => $now, 'updated_at' => $now], [
            ['sort_order' => 0, 'name' => 'First hello', 'body' => "Hi {name}, this is {my_name}. Thank you for your enquiry about {project}. When is a good time to call you?"],
            ['sort_order' => 1, 'name' => 'Could not reach you', 'body' => "Hi {name}, I tried calling you about {project} but couldn't get through. Please let me know a convenient time, or call me on {my_phone}. - {my_name}"],
            ['sort_order' => 2, 'name' => 'Site visit confirmation', 'body' => "Hi {name}, your site visit to {project} is confirmed for {date}. I'll share the location shortly. See you there! - {my_name}, {my_phone}"],
            ['sort_order' => 3, 'name' => 'Meeting reminder', 'body' => "Hi {name}, a reminder of our meeting on {date} about {project}. - {my_name}"],
            ['sort_order' => 4, 'name' => 'Follow-up', 'body' => "Hi {name}, just following up on {project}. Do you have any questions I can help with? - {my_name}"],
        ]));
    }

    public function down(): void
    {
        Schema::dropIfExists('message_templates');
    }
};
