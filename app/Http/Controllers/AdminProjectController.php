<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProjectRequest;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class AdminProjectController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Dashboard', [
            'projects' => Project::query()
                ->latest()
                ->get()
                ->map(fn (Project $project) => $this->transformProject($project))
                ->values(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/ProjectForm', [
            'project' => null,
            'categories' => ['Photography', 'Art'],
        ]);
    }

    public function store(ProjectRequest $request): RedirectResponse
    {
        $imagePaths = $this->storeOrderedImages($request);

        $project = new Project([
            ...$request->safe()->except(['image', 'images', 'new_image_keys', 'image_order']),
            'images' => $imagePaths,
            'image_path' => $imagePaths[0],
        ]);

        $project->save();

        return to_route('admin.dashboard')->with('success', "\"{$project->title}\" was added.");
    }

    public function edit(Project $project): Response
    {
        return Inertia::render('Admin/ProjectForm', [
            'project' => $this->transformProject($project),
            'categories' => ['Photography', 'Art'],
        ]);
    }

    public function update(ProjectRequest $request, Project $project): RedirectResponse
    {
        $existingImagePaths = $project->getImagePaths();
        $imagePaths = $this->storeOrderedImages($request, $project);
        $data = $request->safe()->except(['image', 'images', 'new_image_keys', 'image_order']);
        $data['images'] = $imagePaths;
        $data['image_path'] = $imagePaths[0];

        $project->update($data);

        $removedImages = array_diff($existingImagePaths, $imagePaths);

        if ($removedImages !== []) {
            Storage::disk('public')->delete($removedImages);
        }

        return to_route('admin.dashboard')->with('success', "\"{$project->title}\" was updated.");
    }

    public function destroy(Project $project): RedirectResponse
    {
        Storage::disk('public')->delete($project->getImagePaths());
        $title = $project->title;
        $project->delete();

        return to_route('admin.dashboard')->with('success', "\"{$title}\" was deleted.");
    }

    public function toggleSold(Project $project): RedirectResponse
    {
        $project->update([
            'is_sold' => ! $project->is_sold,
        ]);

        return to_route('admin.dashboard')->with(
            'success',
            $project->is_sold
                ? "\"{$project->title}\" marked as sold."
                : "\"{$project->title}\" marked as available.",
        );
    }

    private function transformProject(Project $project): array
    {
        return [
            'id' => $project->id,
            'slug' => $project->slug,
            'title' => $project->title,
            'category' => $project->category,
            'image_url' => $project->image_url,
            'image_urls' => $project->image_urls,
            'image_paths' => $project->getImagePaths(),
            'description' => $project->description,
            'location' => $project->location,
            'medium' => $project->medium,
            'year' => $project->year,
            'is_sold' => $project->is_sold,
            'created_at' => $project->created_at?->format('M j, Y'),
        ];
    }

    private function storeOrderedImages(ProjectRequest $request, ?Project $project = null): array
    {
        $storedNewImages = [];

        if ($request->hasFile('image')) {
            $storedNewImages['new:legacy-single'] = $request->file('image')->store('projects', 'public');
        }

        foreach ($request->file('images', []) as $index => $file) {
            $key = $request->input("new_image_keys.{$index}");

            if (! is_string($key) || $key === '') {
                continue;
            }

            $storedNewImages["new:{$key}"] = $file->store('projects', 'public');
        }

        $existingImages = collect($project?->getImagePaths() ?? [])
            ->mapWithKeys(fn (string $path) => ["existing:{$path}" => $path])
            ->all();

        $orderedImages = collect($request->input('image_order', []))
            ->map(fn (string $token) => $existingImages[$token] ?? $storedNewImages[$token] ?? null)
            ->filter()
            ->unique()
            ->values()
            ->all();

        if ($orderedImages === []) {
            throw ValidationException::withMessages([
                'images' => 'Add at least one image for this project.',
            ]);
        }

        return $orderedImages;
    }
}
