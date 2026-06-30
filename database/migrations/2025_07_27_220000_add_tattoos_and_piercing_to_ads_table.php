<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ads', function (Blueprint $table) {
            if (!Schema::hasColumn('ads', 'tattoos')) {
                $table->string('tattoos')->nullable()->after('hair_color');
            }
            
            if (!Schema::hasColumn('ads', 'piercing')) {
                $table->string('piercing')->nullable()->after('tattoos');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ads', function (Blueprint $table) {
            $columnsToRemove = ['tattoos', 'piercing'];
            
            foreach ($columnsToRemove as $column) {
                if (Schema::hasColumn('ads', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
