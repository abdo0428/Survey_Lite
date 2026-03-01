<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('surveys', function (Blueprint $table) {
            $table->string('status', 20)->default('draft')->after('description');
            $table->timestamp('published_at')->nullable()->after('public_token');
            $table->timestamp('closed_at')->nullable()->after('published_at');
            $table->string('duplicate_policy', 20)->default('cookie_only')->after('closed_at');
        });

        DB::table('surveys')->where('is_active', true)->update([
            'status' => 'published',
            'published_at' => now(),
            'duplicate_policy' => 'cookie_only',
        ]);

        DB::table('surveys')->where('is_active', false)->update([
            'status' => 'draft',
            'duplicate_policy' => 'cookie_only',
        ]);

        Schema::table('responses', function (Blueprint $table) {
            $table->dropUnique('responses_survey_id_user_id_unique');
            $table->dropUnique('responses_survey_id_ip_hash_unique');

            $table->string('respondent_fingerprint', 64)->nullable()->after('ip_hash');
            $table->index(['survey_id', 'user_id']);
            $table->index(['survey_id', 'ip_hash']);
            $table->index(['survey_id', 'respondent_fingerprint'], 'responses_survey_fingerprint_index');
        });
    }

    public function down(): void
    {
        Schema::table('responses', function (Blueprint $table) {
            $table->dropIndex('responses_survey_id_user_id_index');
            $table->dropIndex('responses_survey_id_ip_hash_index');
            $table->dropIndex('responses_survey_fingerprint_index');
            $table->dropColumn('respondent_fingerprint');

            $table->unique(['survey_id', 'user_id']);
            $table->unique(['survey_id', 'ip_hash']);
        });

        Schema::table('surveys', function (Blueprint $table) {
            $table->dropColumn(['status', 'published_at', 'closed_at', 'duplicate_policy']);
        });
    }
};
