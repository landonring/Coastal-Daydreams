<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'category',
        'sort_order',
        'hover_preview_enabled',
        'image_path',
        'images',
        'description',
        'location',
        'medium',
        'year',
        'is_sold',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'hover_preview_enabled' => 'boolean',
        'images' => 'array',
        'is_sold' => 'boolean',
        'year' => 'integer',
        'created_at' => 'datetime',
    ];

    protected $appends = [
        'image_url',
        'image_urls',
    ];

    protected function imageUrl(): Attribute
    {
        return Attribute::get(
            fn () => $this->image_urls[0] ?? null,
        );
    }

    protected function imageUrls(): Attribute
    {
        return Attribute::get(function (): array {
            $paths = $this->images;

            if (! is_array($paths) || $paths === []) {
                $paths = $this->image_path ? [$this->image_path] : [];
            }

            return collect($paths)
                ->filter()
                ->map(fn (string $path) => '/storage/'.ltrim($path, '/'))
                ->values()
                ->all();
        });
    }

    public function getImagePaths(): array
    {
        $paths = $this->images;

        if (! is_array($paths) || $paths === []) {
            return $this->image_path ? [$this->image_path] : [];
        }

        return collect($paths)
            ->filter()
            ->values()
            ->all();
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderByDesc('created_at');
    }

    public function resolveRouteBinding($value, $field = null): ?self
    {
        if (($field ?? 'id') === 'slug') {
            return $this->where('slug', $value)->first();
        }

        return parent::resolveRouteBinding($value, $field);
    }

    public function getRouteKeyName(): string
    {
        return 'id';
    }

    public function syncPrimaryImage(): void
    {
        $paths = $this->getImagePaths();
        $this->image_path = $paths[0] ?? null;
        $this->images = $paths;
    }

    protected function title(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => trim($value),
        );
    }
}
