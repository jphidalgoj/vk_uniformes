<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Banner extends Model
{
    /** @return array<int, array{path: string, asset: string, alt: string, url?: string}> */
    public function slides(): array
    {
        return array_slice($this->content['slides'] ?? [[
            'path' => $this->image_path ?? '',
            'asset' => $this->content['asset'] ?? '',
            'alt' => $this->image_alt ?: $this->title,
        ]], 0, 4);
    }

    /** @return array<int, array{url: string, alt: string, destination: string}> */
    public function slideImages(): array
    {
        return collect($this->slides())->map(function (array $slide): array {
            $image = new self(['image_path' => $slide['path'] ?? null, 'content' => ['asset' => $slide['asset'] ?? '']]);
            $slideUrl = $slide['url'] ?? '';
            $destination = ($slideUrl && str_starts_with($slideUrl, '/') && ! str_starts_with($slideUrl, '//') && ! str_contains($slideUrl, '\\'))
                ? url($slideUrl)
                : $this->destination();

            return [
                'url' => $image->imageUrl() ?? '',
                'alt' => $slide['alt'] ?: $this->title,
                'destination' => $destination,
            ];
        })->filter(fn (array $slide): bool => $slide['url'] !== '')->values()->all();
    }

    public function imageUrl(): ?string
    {
        if ($this->image_path) {
            return Storage::disk('public')->url($this->image_path);
        }

        $asset = $this->content['asset'] ?? '';

        return $asset && ! str_contains($asset, '..') && preg_match('#^(slider|secciones|images|fotos|demo-products)/#', $asset)
            ? asset($asset) : null;
    }

    public function destination(): string
    {
        $url = $this->button_url ?? '';

        return str_starts_with($url, '/') && ! str_starts_with($url, '//') && ! str_contains($url, '\\')
            ? url($url) : route('store.catalog');
    }

    /** @return array<int, array{url: string, title: string, path: string}> */
    public function videos(): array
    {
        $rawVideos = $this->content['videos'] ?? [];
        if (empty($rawVideos) && ! empty($this->content['video_url'])) {
            $rawVideos = [[
                'url' => $this->content['video_url'],
                'title' => $this->title,
                'path' => '',
            ]];
        }

        return collect($rawVideos)->map(function ($item, int $idx): array {
            $url = '';
            if (! empty($item['path'])) {
                $url = Storage::disk('public')->url($item['path']);
            } elseif (! empty($item['url'])) {
                $url = $item['url'];
            }

            return [
                'url' => $url,
                'title' => ! empty($item['title']) ? $item['title'] : 'Video '.($idx + 1),
                'path' => $item['path'] ?? '',
            ];
        })->filter(fn (array $v): bool => ! empty($v['url']))->values()->take(3)->all();
    }

    protected $fillable = [
        'key',
        'eyebrow',
        'title',
        'description',
        'image_path',
        'image_alt',
        'button_text',
        'button_url',
        'is_active',
        'sort_order',
        'content',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
            'content' => 'array',
        ];
    }
}
