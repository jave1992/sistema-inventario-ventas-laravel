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
            $table->date('fecha_envio')->nullable();
            $table->string('estado');
            $table->string('path_pdf')->nullable();
            $table->string('path_xml')->nullable();
            $table->string('path_cdr')->nullable();
            $table->string('hash')->nullable();
            $table->foreignId('sale_id')->constrained()->onDelete('cascade');

            $table->timestamps();
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
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
