<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            $table->uuid('public_id')->nullable()->unique();
            $table->string('category', 80)->nullable();
        });

        DB::table('complaints')->whereNull('public_id')->chunkById(100, function ($complaints) {
            foreach ($complaints as $complaint) {
                DB::table('complaints')->where('complaint_id', $complaint->complaint_id)
                    ->update(['public_id' => (string) Str::uuid()]);
            }
        }, 'complaint_id');

        Schema::create('complaint_images', function (Blueprint $table) {
            $table->id('complaint_image_id');
            $table->foreignId('complaint_id')->constrained('complaints', 'complaint_id')->cascadeOnDelete();
            $table->string('path')->unique();
            $table->string('disk', 40)->default('public');
            $table->timestamps();
            $table->index('complaint_id');
        });

        Schema::create('complaint_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('complaint_id')->constrained('complaints', 'complaint_id')->cascadeOnDelete();
            $table->unsignedTinyInteger('from_status')->nullable();
            $table->unsignedTinyInteger('to_status');
            $table->foreignId('changed_by')->constrained('users', 'id_user')->cascadeOnDelete();
            $table->timestamps();
            $table->index(['complaint_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complaint_status_histories');
        Schema::dropIfExists('complaint_images');
        Schema::table('complaints', function (Blueprint $table) {
            $table->dropUnique(['public_id']);
            $table->dropColumn(['public_id', 'category']);
        });
    }
};
