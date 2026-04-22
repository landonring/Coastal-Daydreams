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
        Schema::table('projects', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('title');
            $table->json('images')->nullable()->after('image_path');
            $table->string('location')->nullable()->after('description');
            $table->string('medium')->nullable()->after('location');
            $table->unsignedSmallInteger('year')->nullable()->after('medium');
        });

        $projects = DB::table('projects')->orderBy('id')->get();
        $usedSlugs = [];

        foreach ($projects as $project) {
            $baseSlug = Str::slug($project->title ?: 'project');
            $slug = $baseSlug !== '' ? $baseSlug : 'project';
            $suffix = 2;

            while (in_array($slug, $usedSlugs, true) || DB::table('projects')->where('slug', $slug)->where('id', '!=', $project->id)->exists()) {
                $slug = "{$baseSlug}-{$suffix}";
                $suffix++;
            }

            $usedSlugs[] = $slug;

            $images = $project->image_path ? [$project->image_path] : [];

            DB::table('projects')
                ->where('id', $project->id)
                ->update([
                    'slug' => $slug,
                    'images' => json_encode($images, JSON_UNESCAPED_SLASHES),
                ]);
        }
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['slug', 'images', 'location', 'medium', 'year']);
        });
    }
};
