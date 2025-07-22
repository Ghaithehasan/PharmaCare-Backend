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
        Schema::create('damaged_medicines', function (Blueprint $table) {
            $table->id(); // رقم التسجيل الفريد
            // $table->unsignedBigInteger('medicine_id'); // رقم الدواء المرتبط بالهالك
            $table->integer('quantity_talif');
            $table->enum('reason', ['expired', 'damaged', 'storage_issue']); // سبب التلف (منتهي الصلاحية، تالف، سوء تخزين)
            $table->text('notes')->nullable(); // ملاحظات إضافية
            $table->timestamp('damaged_at')->useCurrent(); // تاريخ تسجيل الهالك
            $table->timestamps();
            // $table->foreign('medicine_id')->references('id')->on('medicines')->onDelete('cascade');
            $table->unsignedBigInteger('medicine_batch_id'); // رقم الدفعة المرتبطة بالتلف
            $table->foreign('medicine_batch_id')->references('id')->on('medicine_batches')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('damaged_medicines');
    }
};
