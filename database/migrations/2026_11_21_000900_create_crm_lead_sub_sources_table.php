<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Sub sources: a fixed list under each source (Website > Microsite, Portal, ...).
 * Leads keep the sub source name in leads.sub_source; this list keeps the spelling consistent
 * and feeds the pick list, the filter and the report. Filled from the sub sources already used.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lead_sub_sources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('source_id')->constrained('lead_sources')->cascadeOnDelete();
            $table->string('name', 100);
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
            $table->unique(['source_id', 'name']);
        });

        $seen = [];
        $rows = DB::table('leads')->whereNotNull('source_id')->whereNotNull('sub_source')->where('sub_source', '!=', '')
            ->select('source_id', 'sub_source')->distinct()->orderBy('source_id')->get();
        foreach ($rows as $r) {
            $name = mb_substr(preg_replace('/\s+/u', ' ', trim($r->sub_source)), 0, 100);
            $key = $r->source_id.'|'.mb_strtolower($name);
            if (! isset($seen[$key])) {
                $seen[$key] = $name;
                DB::table('lead_sub_sources')->insert([
                    'source_id' => $r->source_id, 'name' => $name, 'created_at' => now(), 'updated_at' => now(),
                ]);
            }
            // "microsite " and "Microsite" become one spelling on the leads too.
            if ($r->sub_source !== $seen[$key]) {
                DB::table('leads')->where('source_id', $r->source_id)->where('sub_source', $r->sub_source)
                    ->update(['sub_source' => $seen[$key]]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_sub_sources');
    }
};
