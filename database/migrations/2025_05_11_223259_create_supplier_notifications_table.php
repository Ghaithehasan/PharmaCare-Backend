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
            $table->enum('notification_type',['new_order','welcome']); // نوع الإشعار (دفعة متأخرة، نقص مخزون، إلخ)
            $table->text('message'); // نص الإشعار
            $table->json('data')->nullable(); // لتخزين البيانات الإضافية مثل تفاصيل الطلب
            $table->boolean('is_read')->default(false); // حالة القراءة
            $table->timestamp('read_at')->nullable();
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
