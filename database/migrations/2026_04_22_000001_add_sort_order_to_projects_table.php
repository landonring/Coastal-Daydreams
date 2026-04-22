<?php

use App\Models\Project;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->unsignedInteger('sort_order')->nullable()->after('category');
        });

        $order = 1;

        Project::query()
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get()
            ->each(function (Project $project) use (&$order): void {
                $project->forceFill([
                    'sort_order' => $order++,
                ])->saveQuietly();
            });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('sort_order');
        });
    }
};
