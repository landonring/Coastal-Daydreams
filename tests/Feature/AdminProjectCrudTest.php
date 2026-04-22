<?php

namespace Tests\Feature;

use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminProjectCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_update_toggle_and_delete_projects(): void
    {
        Storage::fake('public');

        $session = [config('admin.session_key') => true];

        $this->withSession($session)->post('/admin/projects', [
            'title' => 'New Project',
            'category' => 'Photography',
            'image' => UploadedFile::fake()->image('project.jpg', 1600, 1200),
            'description' => 'Initial description',
            'is_sold' => false,
        ])->assertRedirect('/admin/dashboard');

        $project = Project::firstOrFail();

        Storage::disk('public')->assertExists($project->image_path);
        $this->assertSame('New Project', $project->title);
        $this->assertFalse($project->is_sold);

        $oldPath = $project->image_path;

        $this->withSession($session)->post("/admin/projects/{$project->id}", [
            '_method' => 'put',
            'title' => 'Updated Project',
            'category' => 'Art',
            'image' => UploadedFile::fake()->image('updated.jpg', 1600, 1200),
            'description' => 'Updated description',
            'is_sold' => true,
        ])->assertRedirect('/admin/dashboard');

        $project->refresh();

        $this->assertSame('Updated Project', $project->title);
        $this->assertSame('Art', $project->category);
        $this->assertTrue($project->is_sold);
        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($project->image_path);

        $this->withSession($session)->patch("/admin/projects/{$project->id}/sold")
            ->assertRedirect('/admin/dashboard');

        $project->refresh();
        $this->assertFalse($project->is_sold);

        $currentPath = $project->image_path;

        $this->withSession($session)->delete("/admin/projects/{$project->id}")
            ->assertRedirect('/admin/dashboard');

        $this->assertDatabaseMissing('projects', ['id' => $project->id]);
        Storage::disk('public')->assertMissing($currentPath);
    }

    public function test_admin_can_reorder_projects(): void
    {
        $session = [config('admin.session_key') => true];

        $first = Project::create([
            'title' => 'First',
            'slug' => 'first',
            'category' => 'Art',
            'sort_order' => 1,
            'image_path' => 'projects/first.jpg',
            'images' => ['projects/first.jpg'],
        ]);

        $second = Project::create([
            'title' => 'Second',
            'slug' => 'second',
            'category' => 'Art',
            'sort_order' => 2,
            'image_path' => 'projects/second.jpg',
            'images' => ['projects/second.jpg'],
        ]);

        $this->withSession($session)->patch("/admin/projects/{$second->id}/move", [
            'direction' => 'up',
        ])->assertRedirect('/admin/dashboard');

        $first->refresh();
        $second->refresh();

        $this->assertSame(2, $first->sort_order);
        $this->assertSame(1, $second->sort_order);
    }
}
