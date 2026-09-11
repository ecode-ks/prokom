<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('companies') && !Schema::hasColumn('companies', 'language')) {
            Schema::table('companies', function (Blueprint $table) {
                $table->string('language', 5)->default('sq');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('companies') && Schema::hasColumn('companies', 'language')) {
            Schema::table('companies', function (Blueprint $table) {
                $table->dropColumn('language');
            });
        }
    }
};
