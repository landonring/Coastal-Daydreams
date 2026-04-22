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
            'aboutPhotos' => $this->aboutPhotos(),
            'hasCustomPassword' => (bool) Setting::query()
                ->where('key', config('admin.password_setting_key'))
                ->exists(),
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

        return to_route('admin.settings.edit')->with('success', 'About photos updated.');
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

    private function resolveAboutPhotoUrl(string $path): string
    {
        if (str_starts_with($path, 'public:')) {
            return str($path)->after('public:')->toString();
        }

        return '/storage/'.ltrim($path, '/');
    }
}
