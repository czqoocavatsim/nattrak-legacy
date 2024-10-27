<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('datalink_authorities', function (Blueprint $table) {
            $table->string('id')->unique()->primary();
            $table->string('name');
            $table->string('prefix')->unique();
            $table->boolean('auto_acknowledge_participant')->default(false);
            $table->boolean('valid_rcl_target')->default(true);
            $table->boolean('system')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('datalink_authorities');
    }
};
