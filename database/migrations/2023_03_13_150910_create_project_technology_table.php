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
        Schema::create('project_technology', function (Blueprint $table) {
            $table->id();

            // // Metodo splittato per eventuale assegnazione di nome di colonne :
            // // Metto le colonne
            // $table->unsignedBigInteger('project_id');
            // $table->unsignedBigInteger('technology_id');
            // // Assegno la relazione
            // $table->foreign('project_id')->references('id')->on('projects');
            // $table->foreign('technology_id')->references('id')->on('technologies');

            //? Metodo unica riga con constrained on delete cancella l'intera riga nella tabella (cascade)
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('technology_id')->constrained()->onDelete('cascade');

            // Eventualmente se vuoi utilizzare i tymestamps devo andare nel model della Technology e aggiungere ->withTimestamps
            // $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_technology');
    }
};
