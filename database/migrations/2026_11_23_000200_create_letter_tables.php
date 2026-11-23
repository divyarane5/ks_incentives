<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * KeyPeople letters: templates with {fill-ins}, and the letters issued to people (a frozen copy).
 * New tables only. Six starter templates are added; HR edits them on KeyPeople > Letters.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('letter_templates')) {
            Schema::create('letter_templates', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('type', 30)->default('other'); // offer, appointment, confirmation, increment, relieving, experience, other
                $table->string('subject')->nullable();
                $table->longText('body');
                $table->boolean('active')->default(true);
                $table->timestamps();
            });

            $now = now();
            $t = fn ($name, $type, $subject, $body) => ['name' => $name, 'type' => $type, 'subject' => $subject, 'body' => $body, 'active' => true, 'created_at' => $now, 'updated_at' => $now];

            DB::table('letter_templates')->insert([
                $t('Offer letter', 'offer', 'Offer of employment', <<<'TXT'
Dear {first_name},

We are pleased to offer you the position of **{designation}** in the {department} team at {company}, based at our {location} office.

Your annual cost to company (CTC) will be **{annual_ctc}** ({annual_ctc_words}). The detailed salary structure is attached.

Your expected date of joining is **{joining_date}**. You will be on probation for {probation_days} days from your date of joining.

Please sign and return a copy of this letter as your acceptance.

We look forward to welcoming you.
TXT),
                $t('Appointment letter', 'appointment', 'Letter of appointment', <<<'TXT'
Dear {first_name},

Further to your acceptance of our offer, we are pleased to appoint you as **{designation}** in the {department} team at {company} with effect from **{joining_date}**. Your employee code is {employee_code}.

- Your annual CTC is {annual_ctc} ({annual_ctc_words}).
- You will be on probation for {probation_days} days. On successful completion you will be confirmed in writing.
- You will report to {manager}.
- Either side may end this employment by giving {notice_days} days' notice in writing.

You are expected to follow the company's policies as updated from time to time.

Please sign a copy of this letter as your acceptance.
TXT),
                $t('Confirmation letter', 'confirmation', 'Confirmation of employment', <<<'TXT'
Dear {first_name},

We are pleased to inform you that, on successful completion of your probation, your employment as **{designation}** is confirmed with effect from **{confirm_date}**.

All other terms of your appointment remain the same.

We thank you for your contribution and wish you continued success with {company}.
TXT),
                $t('Increment letter', 'increment', 'Revision in compensation', <<<'TXT'
Dear {first_name},

In recognition of your performance and contribution, we are pleased to revise your annual cost to company to **{annual_ctc}** ({annual_ctc_words}) with effect from **{salary_effective_from}**.

All other terms of your employment remain unchanged.

We look forward to your continued contribution to {company}.
TXT),
                $t('Relieving letter', 'relieving', 'Relieving letter', <<<'TXT'
Dear {first_name},

This is to confirm that your resignation has been accepted and you are relieved from the services of {company} at the close of business on **{leaving_date}**.

We confirm that you have handed over your responsibilities and company property.

We thank you for your services and wish you the very best for the future.
TXT),
                $t('Experience letter', 'experience', 'To whom it may concern', <<<'TXT'
This is to certify that **{title} {name}** (employee code {employee_code}) worked with {company} from **{joining_date}** to **{leaving_date}**. At the time of leaving, {first_name} was working as **{designation}** in the {department} team.

During this period we found {first_name} sincere and hardworking.

We wish {first_name} all the best for the future.
TXT),
            ]);
        }

        if (! Schema::hasTable('issued_letters')) {
            Schema::create('issued_letters', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('letter_template_id')->nullable();
                $table->string('type', 30)->default('other');
                $table->string('subject')->nullable();
                $table->longText('body');                 // filled-in text, frozen when issued
                $table->date('letter_date');
                $table->string('reference', 60)->nullable();
                $table->unsignedBigInteger('issued_by')->nullable();
                $table->timestamps();
                $table->index('user_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('issued_letters');
        Schema::dropIfExists('letter_templates');
    }
};
