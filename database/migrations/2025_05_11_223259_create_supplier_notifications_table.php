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
        Schema::create('supplier_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade'); // ربط الإشعار بالمورد
            $table->string('notification_type'); // نوع الإشعار (دفعة متأخرة، نقص مخزون، إلخ)
            $table->text('message'); // نص الإشعار
            $table->boolean('is_read')->default(false); // حالة القراءة
            $table->timestamps();
        });
    }



    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_notifications');
    }
};
