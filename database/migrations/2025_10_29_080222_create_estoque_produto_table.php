<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('estoque_produto', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produto_id')->constrained('produtos')->cascadeOnDelete();
            $table->foreignId('estoque_id')->constrained('estoques')->cascadeOnDelete();
            $table->unsignedInteger('quantidade')->default(0);
            $table->timestamps();

            $table->unique(['produto_id', 'estoque_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estoque_produto');
    }
};
