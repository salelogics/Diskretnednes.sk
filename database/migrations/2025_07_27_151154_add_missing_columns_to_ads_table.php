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
            // Pridáme všetky chýbajúce stĺpce ktoré sa používajú v seederoch
            
            if (!Schema::hasColumn('ads', 'contact_methods')) {
                $table->json('contact_methods')->nullable()->after('experience');
            }
            
            if (!Schema::hasColumn('ads', 'hours')) {
                $table->json('hours')->nullable()->after('contact_methods');
            }
            
            if (!Schema::hasColumn('ads', 'practices')) {
                $table->json('practices')->nullable()->after('hours');
            }
            
            if (!Schema::hasColumn('ads', 'height')) {
                $table->integer('height')->nullable()->after('practices');
            }
            
            if (!Schema::hasColumn('ads', 'weight')) {
                $table->integer('weight')->nullable()->after('height');
            }
            
            if (!Schema::hasColumn('ads', 'breast_size')) {
                $table->string('breast_size')->nullable()->after('weight');
            }
            
            if (!Schema::hasColumn('ads', 'eye_color')) {
                $table->string('eye_color')->nullable()->after('breast_size');
            }
            
            if (!Schema::hasColumn('ads', 'hair_color')) {
                $table->string('hair_color')->nullable()->after('eye_color');
            }
            
            if (!Schema::hasColumn('ads', 'orientation')) {
                $table->string('orientation')->nullable()->after('hair_color');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ads', function (Blueprint $table) {
            $columnsToRemove = [
                'contact_methods', 'hours', 'practices', 'height', 'weight', 
                'breast_size', 'eye_color', 'hair_color', 'orientation'
            ];
            
            foreach ($columnsToRemove as $column) {
                if (Schema::hasColumn('ads', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
