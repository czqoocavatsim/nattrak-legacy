<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('clx_messages', function (Blueprint $table) {
            $table->string('entry_time_restriction_interval_callsign')->nullable();
            $table->integer('entry_time_restriction_interval_minutes')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('clx_messages', function (Blueprint $table) {
            $table->dropColumn('entry_time_restriction_interval_callsign');
            $table->dropColumn('entry_time_restriction_interval_minutes');
        });
    }
};
