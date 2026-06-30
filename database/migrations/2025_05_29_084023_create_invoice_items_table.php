<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('invoice_items')) {
            Schema::create('invoice_items', function (Blueprint $table) {
                $table->id();

                $table->integer('quantity')->default(1);
                $table->string('quantity_unit')->nullable(); // such as hours, days, number, ...

                /**
                * All amount are in the same currency
                **/
                $table->bigInteger('unit_price')->nullable();
                $table->string('currency')->nullable();
                
                /**
                * Store taxes as an amount for each unit
                * Total taxes will be computed by multiplying with quantity
                **/
                $table->bigInteger('unit_taxes')->nullable();
                $table->bigInteger('unit_discount')->nullable();
                $table->string('discount_type')->nullable(); // fixed, percentage

                $table->string('title')->nullable();
                $table->text('description')->nullable();
                $table->timestamps();
                
                $table->morphs('owner');
                $table->morphs('buyer');
                $table->morphs('seller');
                $table->morphs('discountable');
                
                $table->index(['created_at']);
                $table->index(['updated_at']);
                $table->index(['currency']);
                $table->index(['title']);
                $table->index(['discount_type']);
                $table->index(['unit_price']);
                $table->index(['unit_taxes']);
                $table->index(['unit_discount']);
                $table->index(['quantity']);
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('invoice_items');
    }
};
