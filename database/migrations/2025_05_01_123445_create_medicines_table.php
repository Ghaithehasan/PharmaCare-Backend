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
        Schema::create('medicines', function (Blueprint $table) {
            $table->id();
            $table->string('medicine_name')->unique();
            $table->string('sentific_name')->nullable();
            $table->string('arabic_name')->nullable();
            $table->string('bar_code');
            $table->enum('type',['package','unit']);
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('medicine_form_id')->nullable();
            $table->foreign('category_id')->references('id')->on('categories')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('medicine_form_id')->references('id')->on('medicine_forms')->nullOnDelete();
            $table->integer('quantity');
            $table->integer('alert_quantity')->default(10);
            $table->decimal('people_price', 8, 2);
            $table->decimal('supplier_price', 8, 2);
            $table->decimal('tax_rate', 5, 2);
            $table->date('expiry_date');
            $table->date('last_notification_date')->nullable();
            $table->json('alternative_ids')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicines');
    }
};
