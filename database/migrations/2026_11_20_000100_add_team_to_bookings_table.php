<?php

use App\Services\BookingTeamResolver;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Saves the sales team on each booking as it was on the booking date, so a manager's
 * incentive follows the team at the time of the booking, not today's org chart.
 * Adds columns only; existing data is not changed apart from filling these new columns.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->foreignId('tl_user_id')->nullable()->after('sales_user_id')->constrained('users')->nullOnDelete();
            $table->foreignId('sr_tl_user_id')->nullable()->after('tl_user_id')->constrained('users')->nullOnDelete();
            $table->foreignId('ch_user_id')->nullable()->after('sr_tl_user_id')->constrained('users')->nullOnDelete();
            // auto: filled from the reporting chain; manual: set by a person on the booking edit page
            $table->string('team_source', 10)->nullable()->after('ch_user_id');
        });

        // Fill the team for existing bookings from the reporting chain on each booking date.
        $resolver = app(BookingTeamResolver::class);
        DB::table('bookings')->whereNotNull('sales_user_id')->orderBy('id')
            ->select('id', 'sales_user_id', 'booking_date')
            ->chunk(200, function ($rows) use ($resolver) {
                foreach ($rows as $row) {
                    $team = $resolver->teamFor((int) $row->sales_user_id, $row->booking_date);
                    DB::table('bookings')->where('id', $row->id)->update($team + ['team_source' => 'auto']);
                }
            });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropConstrainedForeignId('tl_user_id');
            $table->dropConstrainedForeignId('sr_tl_user_id');
            $table->dropConstrainedForeignId('ch_user_id');
            $table->dropColumn('team_source');
        });
    }
};
