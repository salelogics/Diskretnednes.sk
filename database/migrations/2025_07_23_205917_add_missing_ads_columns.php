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
        // Zoznam všetkých stĺpcov ktoré môžu chýbať
        $columnsToAdd = [
            'services' => function($table) { $table->text('services')->nullable(); },
            'location' => function($table) { $table->text('location')->nullable(); },
            'prices' => function($table) { $table->text('prices')->nullable(); },
            'working_hours' => function($table) { $table->text('working_hours')->nullable(); },
            'additional_info' => function($table) { $table->text('additional_info')->nullable(); },
            'is_verified' => function($table) { $table->boolean('is_verified')->default(false); },
            'is_fake' => function($table) { $table->boolean('is_fake')->default(false); },
            'is_agency' => function($table) { $table->boolean('is_agency')->default(false); },
            'rejection_reason' => function($table) { $table->text('rejection_reason')->nullable(); },  
            'reviewed_at' => function($table) { $table->timestamp('reviewed_at')->nullable(); },
            'expires_at' => function($table) { $table->timestamp('expires_at')->nullable(); },
            'phone_views' => function($table) { $table->integer('phone_views')->default(0); },
            'whatsapp_views' => function($table) { $table->integer('whatsapp_views')->default(0); },
            'viber_views' => function($table) { $table->integer('viber_views')->default(0); },
            'telegram_views' => function($table) { $table->integer('telegram_views')->default(0); },
            'favorites_count' => function($table) { $table->integer('favorites_count')->default(0); },
            'whatsapp' => function($table) { $table->string('whatsapp')->nullable(); },
            'viber' => function($table) { $table->string('viber')->nullable(); },
            'telegram' => function($table) { $table->string('telegram')->nullable(); },
            'email' => function($table) { $table->string('email')->nullable(); }
        ];

        foreach ($columnsToAdd as $columnName => $columnDefinition) {
            if (!Schema::hasColumn('ads', $columnName)) {
                try {
                    Schema::table('ads', function (Blueprint $table) use ($columnDefinition) {
                        $columnDefinition($table);
                    });
                } catch (\Exception $e) {
                    // Ignoruj ak sa stĺpec nepodarí pridať
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $columnsToRemove = [
            'services', 'location', 'prices', 'working_hours', 'additional_info',
            'is_verified', 'is_fake', 'is_agency', 'rejection_reason', 'reviewed_at',
            'expires_at', 'phone_views', 'whatsapp_views', 'viber_views', 'telegram_views',
            'favorites_count', 'whatsapp', 'viber', 'telegram', 'email'
        ];
        
        foreach ($columnsToRemove as $column) {
            if (Schema::hasColumn('ads', $column)) {
                try {
                    Schema::table('ads', function (Blueprint $table) use ($column) {
                        $table->dropColumn($column);
                    });
                } catch (\Exception $e) {
                    // Ignoruj ak sa stĺpec nepodarí odstrániť
                }
            }
        }
    }
};
