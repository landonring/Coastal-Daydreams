<?php

namespace Tests\Feature;

use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_home_page_renders(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Home'));
    }

    public function test_the_home_page_uses_project_sort_order(): void
    {
        Project::create([
            'title' => 'Later',
            'slug' => 'later',
            'category' => 'Art',
            'sort_order' => 2,
            'image_path' => 'projects/later.jpg',
            'images' => ['projects/later.jpg'],
        ]);

        Project::create([
            'title' => 'Sooner',
            'slug' => 'sooner',
            'category' => 'Art',
            'sort_order' => 1,
            'image_path' => 'projects/sooner.jpg',
            'images' => ['projects/sooner.jpg'],
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSeeInOrder(['Sooner', 'Later']);
    }
}
