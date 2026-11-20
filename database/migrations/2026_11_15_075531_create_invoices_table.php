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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();

            $table->string('serie');
            $table->string('correlativo');
            $table->string('razon_social');
            $table->string('ruc');
            $table->decimal('total', 10, 2);
            $table->date('fecha_envio');
            $table->string('estado');
            $table->string('path_pdf');
            $table->string('path_xml');
            $table->string('path_cdr');
            $table->string('hash');
            $table->foreignId('sale_id')->constrained()->onDelete('restrict');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
