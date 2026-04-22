<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Inertia\Inertia;
use Inertia\Response;

class ProjectShowController extends Controller
{
    public function __invoke(Project $project): Response
    {
        $orderedProjects = Project::query()
            ->ordered()
            ->get(['id', 'slug', 'title']);

        $currentIndex = $orderedProjects->search(fn (Project $item) => $item->id === $project->id);
        $previousProject = $currentIndex !== false && $currentIndex < $orderedProjects->count() - 1
            ? $orderedProjects[$currentIndex + 1]
            : null;
        $nextProject = $currentIndex !== false && $currentIndex > 0
            ? $orderedProjects[$currentIndex - 1]
            : null;

        return Inertia::render('Projects/Show', [
            'project' => [
                'id' => $project->id,
                'slug' => $project->slug,
                'title' => $project->title,
                'category' => $project->category,
                'description' => $project->description,
                'images' => $project->image_urls,
                'hero_image' => $project->image_url,
                'is_sold' => $project->is_sold,
                'location' => $project->location,
                'medium' => $project->medium,
                'year' => $project->year,
                'created_at' => $project->created_at?->format('Y'),
            ],
            'previousProject' => $previousProject ? [
                'title' => $previousProject->title,
                'slug' => $previousProject->slug,
            ] : null,
            'nextProject' => $nextProject ? [
                'title' => $nextProject->title,
                'slug' => $nextProject->slug,
            ] : null,
        ]);
    }
}
