<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Response as HttpResponse;

class SitemapController extends Controller
{
    public function __invoke(): HttpResponse
    {
        $projects = Project::query()
            ->latest('updated_at')
            ->get(['slug', 'updated_at', 'created_at']);

        $homeLastModified = $projects->max(
            fn (Project $project) => ($project->updated_at ?? $project->created_at)?->toAtomString(),
        );

        return response()
            ->view('sitemap.xml', [
                'homeLastModified' => $homeLastModified,
                'projects' => $projects,
            ])
            ->header('Content-Type', 'application/xml');
    }
}
