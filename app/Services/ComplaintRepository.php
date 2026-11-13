<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use RuntimeException;

/**
 * Temporary JSON data source for citizen complaint views.
 * Replace this repository with an API/database implementation when the backend is ready.
 */
class ComplaintRepository
{
    private string $path;

    public function __construct()
    {
        $this->path = resource_path('denuncias/denuncias.json');
    }

    public function all(): Collection
    {
        if (! File::exists($this->path)) {
            return collect();
        }

        return collect(json_decode(File::get($this->path), true) ?: []);
    }

    public function find(string $id): ?array
    {
        return $this->all()->firstWhere('complaintId', $id);
    }

    public function findByPublicId(string $publicId): ?array
    {
        return $this->all()->firstWhere('publicId', $publicId);
    }

    public function create(array $complaint): void
    {
        $items = $this->all()->push($complaint)->values();
        $json = json_encode($items->all(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        if ($json === false || File::put($this->path, $json, true) === false) {
            throw new RuntimeException('Não foi possível salvar a denúncia no arquivo JSON.');
        }
    }
}
