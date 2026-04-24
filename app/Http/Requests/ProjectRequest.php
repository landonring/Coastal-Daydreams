<?php

namespace App\Http\Requests;

use App\Models\Project;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        /** @var Project|null $project */
        $project = $this->route('project');

        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('projects', 'slug')->ignore($project?->id),
            ],
            'category' => ['required', 'string', Rule::in(['Photography', 'Art'])],
            'image' => ['nullable', 'image', 'max:15360'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'max:15360'],
            'new_image_keys' => ['nullable', 'array'],
            'new_image_keys.*' => ['string', 'max:120'],
            'image_order' => ['required', 'array', 'min:1'],
            'image_order.*' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:4000'],
            'location' => ['nullable', 'string', 'max:255'],
            'medium' => ['nullable', 'string', 'max:255'],
            'year' => ['nullable', 'integer', 'between:1000,9999'],
            'hover_preview_enabled' => ['nullable', 'boolean'],
            'is_sold' => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $slug = strtolower(trim((string) $this->input('slug')));

        if ($slug === '') {
            $slug = Str::slug((string) $this->input('title'));
        }

        if (! $this->filled('image_order') && $this->hasFile('image')) {
            $this->merge([
                'new_image_keys' => ['legacy-single'],
                'image_order' => ['new:legacy-single'],
            ]);
        }

        $this->merge([
            'slug' => $slug,
            'hover_preview_enabled' => $this->boolean('hover_preview_enabled', true),
            'is_sold' => $this->input('category') === 'Art'
                ? $this->boolean('is_sold')
                : false,
        ]);
    }
}
