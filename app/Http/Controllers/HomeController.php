<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Setting;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render('Home', [
            'aboutPhotos' => $this->aboutPhotos(),
            'projects' => Project::query()
                ->ordered()
                ->get()
                ->map(fn (Project $project) => $this->transformProject($project))
                ->values(),
        ]);
    }

    private function transformProject(Project $project): array
    {
        return [
            'id' => $project->id,
            'slug' => $project->slug,
            'title' => $project->title,
            'category' => $project->category,
            'image' => $project->image_url,
            'images' => $project->image_urls,
            'hover_preview_enabled' => $project->hover_preview_enabled,
            'description' => $project->description,
            'is_sold' => $project->is_sold,
            'created_at' => $project->created_at?->toIso8601String(),
        ];
    }

    private function aboutPhotos(): array
    {
        $defaultAltText = [
            'Jennifer Williams kayaking on calm coastal water',
            'Jennifer Williams presenting a wall display of artwork and photography',
            'Jennifer Williams walking in sunlit desert dunes',
            'A coastal pier at sunset photographed by Jennifer Williams',
            'Jennifer Williams standing beside framed coastal artwork',
            'Black and white portrait of Jennifer Williams with a camera in the landscape',
        ];

        return collect(Setting::getArray('about_photo_paths', $this->defaultAboutPhotoPaths()))
            ->values()
            ->map(fn (string $path, int $index) => [
                'src' => $this->resolveAboutPhotoUrl($path),
                'alt' => $defaultAltText[$index] ?? 'Jennifer Williams about photo '.($index + 1),
            ])
            ->all();
    }

    private function defaultAboutPhotoPaths(): array
    {
        return [
            'public:/images/about-1.jpg',
            'public:/images/about-2.jpg',
            'public:/images/about-3.jpg',
            'public:/images/about-4.jpg',
            'public:/images/about-5.jpg',
            'public:/images/about-6.jpg',
        ];
    }

    private function resolveAboutPhotoUrl(string $path): string
    {
        if (str_starts_with($path, 'public:')) {
            return str($path)->after('public:')->toString();
        }

        return '/storage/'.ltrim($path, '/');
    }
}
