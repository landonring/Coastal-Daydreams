<?php

namespace Tests\Feature;

use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SitemapTest extends TestCase
{
    use RefreshDatabase;

    public function test_sitemap_includes_home_and_project_pages(): void
    {
        $project = Project::create([
            'title' => 'Sitemap Project',
            'slug' => 'sitemap-project',
            'category' => 'Photography',
            'image_path' => 'projects/test.jpg',
            'images' => ['projects/test.jpg'],
            'description' => 'Testing sitemap output.',
            'is_sold' => false,
        ]);

        $response = $this->get('/sitemap.xml');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/xml');
        $response->assertSee(url('/'), false);
        $response->assertSee(route('projects.show', $project->slug), false);
    }
}
