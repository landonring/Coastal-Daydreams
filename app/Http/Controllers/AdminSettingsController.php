<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminPasswordUpdateRequest;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class AdminSettingsController extends Controller
{
    public function edit(): Response
    {
        return Inertia::render('Admin/Settings', [
            'hasCustomPassword' => (bool) Setting::query()
                ->where('key', config('admin.password_setting_key'))
                ->exists(),
        ]);
    }

    public function bio(): Response
    {
        return Inertia::render('Admin/Bio', [
            'aboutPhotos' => $this->aboutPhotos(),
            'photographyParagraphs' => $this->photographyParagraphs(),
            'artParagraphs' => $this->artParagraphs(),
        ]);
    }

    public function update(AdminPasswordUpdateRequest $request): RedirectResponse
    {
        $currentHash = AdminAuthController::resolvedPasswordHash();

        if (! $currentHash || ! Hash::check($request->string('current_password')->toString(), $currentHash)) {
            throw ValidationException::withMessages([
                'current_password' => 'The current password is incorrect.',
            ]);
        }

        Setting::setValue(
            config('admin.password_setting_key'),
            Hash::make($request->string('password')->toString()),
        );

        return to_route('admin.settings.edit')->with('success', 'Admin password updated.');
    }

    public function updateAboutPhotos(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'images' => ['array'],
            'images.*' => ['image', 'max:10240'],
            'new_image_keys' => ['array'],
            'new_image_keys.*' => ['string'],
            'image_order' => ['required', 'array', 'min:1'],
            'image_order.*' => ['string'],
        ]);

        $currentPaths = Setting::getArray('about_photo_paths', $this->defaultAboutPhotoPaths());
        $storedNewImages = [];

        foreach ($request->file('images', []) as $index => $file) {
            $key = $validated['new_image_keys'][$index] ?? null;

            if (! is_string($key) || $key === '') {
                continue;
            }

            $storedNewImages["new:{$key}"] = $file->store('about', 'public');
        }

        $existingImages = collect($currentPaths)
            ->mapWithKeys(fn (string $path) => ["existing:{$path}" => $path])
            ->all();

        $orderedPaths = collect($validated['image_order'])
            ->map(fn (string $token) => $existingImages[$token] ?? $storedNewImages[$token] ?? null)
            ->filter()
            ->unique()
            ->values()
            ->all();

        if ($orderedPaths === []) {
            throw ValidationException::withMessages([
                'images' => 'Add at least one photo for the About section.',
            ]);
        }

        $removedPaths = array_diff($currentPaths, $orderedPaths);
        $removedCustomPaths = array_values(array_filter(
            $removedPaths,
            fn (string $path) => ! str_starts_with($path, 'public:'),
        ));

        if ($removedCustomPaths !== []) {
            Storage::disk('public')->delete($removedCustomPaths);
        }

        Setting::setArray('about_photo_paths', $orderedPaths);

        return to_route('admin.bio.edit')->with('success', 'About photos updated.');
    }

    public function updateAboutContent(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'photography_text' => ['required', 'string'],
            'art_text' => ['required', 'string'],
        ]);

        $photographyParagraphs = $this->splitParagraphs($validated['photography_text']);
        $artParagraphs = $this->splitParagraphs($validated['art_text']);

        if ($photographyParagraphs === []) {
            throw ValidationException::withMessages([
                'photography_text' => 'Add at least one photography paragraph.',
            ]);
        }

        if ($artParagraphs === []) {
            throw ValidationException::withMessages([
                'art_text' => 'Add at least one art paragraph.',
            ]);
        }

        Setting::setArray('about_photography_paragraphs', $photographyParagraphs);
        Setting::setArray('about_art_paragraphs', $artParagraphs);

        return to_route('admin.bio.edit')->with('success', 'About bio updated.');
    }

    private function aboutPhotos(): array
    {
        return collect(Setting::getArray('about_photo_paths', $this->defaultAboutPhotoPaths()))
            ->values()
            ->map(fn (string $path, int $index) => [
                'url' => $this->resolveAboutPhotoUrl($path),
                'path' => $path,
                'name' => 'About photo '.($index + 1),
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

    private function splitParagraphs(string $text): array
    {
        return collect(preg_split("/\r?\n\s*\r?\n/", trim($text)) ?: [])
            ->map(fn (string $paragraph) => trim($paragraph))
            ->filter()
            ->values()
            ->all();
    }

    private function resolveAboutPhotoUrl(string $path): string
    {
        if (str_starts_with($path, 'public:')) {
            return str($path)->after('public:')->toString();
        }

        return '/storage/'.ltrim($path, '/');
    }
}
