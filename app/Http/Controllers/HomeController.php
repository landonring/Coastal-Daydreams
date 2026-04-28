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
            'photographyParagraphs' => $this->photographyParagraphs(),
            'artParagraphs' => $this->artParagraphs(),
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

    private function photographyParagraphs(): array
    {
        return Setting::getArray('about_photography_paragraphs', $this->defaultPhotographyParagraphs());
    }

    private function artParagraphs(): array
    {
        return Setting::getArray('about_art_paragraphs', $this->defaultArtParagraphs());
    }

    private function defaultPhotographyParagraphs(): array
    {
        return [
            'With a camera as her constant companion, Jennifer has spent a lifetime chasing the fleeting beauty of the world.',
            'Growing up she was raised in the military life, in the rhythm of constant relocation and transitory experiences. She learned early on that memories fade, but a photograph can hold a moment forever. Each image became a keepsake, a gentle reminder of faces and places, a slice of time that might otherwise be lost forever.',
            'Her journey eventually led her to the Central Coast of California. A place she now calls home and her photography flourished. Jennifer finds inspiration in the ever-changing dance of light and landscape. Her lens seeks not just to capture a scene, but to distill the feeling of a single unrepeatable instant.',
            'Through her work, she invites you to linger, to look a little longer, and to let yourself be drawn into the quiet magic of the world.',
            'In every photograph, Jennifer hopes to offer a brief sanctuary, a place where, for just a heartbeat, time stands still.',
        ];
    }

    private function defaultArtParagraphs(): array
    {
        return [
            'Jennifer is a self taught artist with a flare for imagination.',
            'Working primarily with acrylic and painting on canvas, she creates whimsical wildflowers and surreal landscapes that evolve organically in the moment.',
            'Without a set plan or predetermined vision Jennifer allows each piece to develop intuitively as she paints. Her work reflects a spontaneous, imaginative process where each painting unfolds naturally, guided by the brush and the energy of the moment.',
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
