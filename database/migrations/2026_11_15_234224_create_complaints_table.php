<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('complaints', function (Blueprint $table) {
            $table->id('complaint_id');
            $table->foreignId('id_user')->constrained('users', 'id_user')->cascadeOnDelete();
            $table->string('title');
            $table->text('description');
            $table->string('image_url')->nullable()->unique();
            $table->unsignedTinyInteger('status')->default(0)->index();
            $table->boolean('high_priority')->default(false);
            $table->double('latitude');
            $table->double('longitude');
            $table->string('address');
            $table->string('district');
            $table->string('cep', 8);
            $table->string('complement')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};