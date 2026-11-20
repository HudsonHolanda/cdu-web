<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('complaint_deletion_requests', function (Blueprint $table) {
            $table->id('complaint_deletion_request_id');
            $table->foreignId('complaint_id')->nullable()->constrained('complaints', 'complaint_id')->nullOnDelete();
            $table->foreignId('requested_by')->constrained('users', 'id_user')->cascadeOnDelete();
            $table->string('status', 20)->default('pending');
            $table->text('reason');
            $table->foreignId('reviewed_by')->nullable()->constrained('users', 'id_user')->nullOnDelete();
            $table->text('reviewer_reason')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
            $table->index(['complaint_id', 'status']);
            $table->index(['requested_by', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complaint_deletion_requests');
    }
};
