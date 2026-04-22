<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>{{ url('/') }}</loc>
        @if ($homeLastModified)
            <lastmod>{{ $homeLastModified }}</lastmod>
        @endif
        <changefreq>weekly</changefreq>
        <priority>1.0</priority>
    </url>
@foreach ($projects as $project)
    <url>
        <loc>{{ route('projects.show', $project->slug) }}</loc>
        @if ($project->updated_at || $project->created_at)
            <lastmod>{{ ($project->updated_at ?? $project->created_at)->toAtomString() }}</lastmod>
        @endif
        <changefreq>monthly</changefreq>
        <priority>0.8</priority>
    </url>
@endforeach
</urlset>
