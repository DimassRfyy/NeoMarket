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
        Schema::create('product_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId("product_id")->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('total_price');
            $table->boolean('is_paid');
            $table->string('proof');
            $table->softDeletes();
            $table->timestamps();

            $table->foreignId("creator_id")->constrained("users")->onDelete("cascade");
            $table->foreignId("buyer_id")->constrained("users")->onDelete("cascade");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_orders');
    }
};
