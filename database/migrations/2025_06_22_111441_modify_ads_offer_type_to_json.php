<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Ak už je offer_type JSON, preskočíme
        if (Schema::hasColumn('ads', 'offer_type')) {
            $columnType = DB::select("SHOW COLUMNS FROM ads WHERE Field = 'offer_type'")[0]->Type ?? '';
            if (strpos($columnType, 'json') !== false) {
                return; // Už je JSON, nič nerobíme
            }
        }
        
        try {
            // Pridáme nový JSON stĺpec iba ak neexistuje
            if (!Schema::hasColumn('ads', 'offer_types')) {
                Schema::table('ads', function (Blueprint $table) {
                    $table->json('offer_types')->nullable();
                });
            }
            
            // Migrácia existujúcich dát zo starého stĺpca do nového (iba ak existuje starý)
            if (Schema::hasColumn('ads', 'offer_type') && Schema::hasColumn('ads', 'offer_types')) {
                DB::table('ads')->whereNotNull('offer_type')->orderBy('id')->chunk(100, function ($ads) {
                    foreach ($ads as $ad) {
                        if (!empty($ad->offer_type) && is_string($ad->offer_type)) {
                            DB::table('ads')
                                ->where('id', $ad->id)
                                ->update(['offer_types' => json_encode([$ad->offer_type])]);
                        }
                    }
                });
            }
            
            // Odstránime starý stĺpec ak existuje a nie je už JSON
            if (Schema::hasColumn('ads', 'offer_type')) {
                $columnType = DB::select("SHOW COLUMNS FROM ads WHERE Field = 'offer_type'")[0]->Type ?? '';
                if (strpos($columnType, 'json') === false) {
                    Schema::table('ads', function (Blueprint $table) {
                        $table->dropColumn('offer_type');
                    });
                }
            }
            
            // Premenujeme nový stĺpec iba ak existuje offer_types ale neexistuje offer_type
            if (Schema::hasColumn('ads', 'offer_types') && !Schema::hasColumn('ads', 'offer_type')) {
                Schema::table('ads', function (Blueprint $table) {
                    $table->renameColumn('offer_types', 'offer_type');
                });
            }
            
        } catch (\Exception $e) {
            // V prípade chyby, môžeme logovať ale nebudeme hádzať výnimku
            // Log::error('Migration failed: ' . $e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            // Ak je offer_type JSON, vráťme ho na string
            if (Schema::hasColumn('ads', 'offer_type')) {
                $columnType = DB::select("SHOW COLUMNS FROM ads WHERE Field = 'offer_type'")[0]->Type ?? '';
                if (strpos($columnType, 'json') !== false) {
                    
                    // Premenujeme späť
                    Schema::table('ads', function (Blueprint $table) {
                        $table->renameColumn('offer_type', 'offer_types');
                    });
                    
                    // Pridáme starý string stĺpec
                    Schema::table('ads', function (Blueprint $table) {
                        $table->string('offer_type')->nullable();
                    });
                    
                    // Migrácia dát späť
                    if (Schema::hasColumn('ads', 'offer_types')) {
                        DB::table('ads')->whereNotNull('offer_types')->orderBy('id')->chunk(100, function ($ads) {
                            foreach ($ads as $ad) {
                                $offerTypes = json_decode($ad->offer_types, true);
                                if (is_array($offerTypes) && !empty($offerTypes)) {
                                    DB::table('ads')
                                        ->where('id', $ad->id)
                                        ->update(['offer_type' => $offerTypes[0]]);
                                }
                            }
                        });
                    }
                    
                    // Odstránime JSON stĺpec
                    if (Schema::hasColumn('ads', 'offer_types')) {
                        Schema::table('ads', function (Blueprint $table) {
                            $table->dropColumn('offer_types');
                        });
                    }
                }
            }
        } catch (\Exception $e) {
            // V prípade chyby pri rollback, ignorujeme
        }
    }
};
