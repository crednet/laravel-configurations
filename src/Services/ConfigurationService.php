<?php

namespace Credpal\Configurations\Services;

use Credpal\Configurations\Models\Configuration;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ConfigurationService
{
    public function updateOrCreate(array $data): Collection
    {
        Configuration::upsert($data['configurations'], ['name'], ['title', 'name', 'value', 'value_type', 'data']);
        return Configuration::whereIn(
            'name',
            array_map(fn ($config) => $config['name'], $data['configurations'])
        )->get();
    }

    public function getAll(array $keys = []): Collection
    {
        return count($keys)
            ? Configuration::whereIn('name', $keys)->get()
            : Configuration::all();
    }

    public function get(string $key): mixed
    {
        return Configuration::getValue($key);
    }

    public function resetAll(): void
    {
        Configuration::update(['value' => DB::raw('default')]);
    }

    public function reset(array $keys): void
    {
        Configuration::whereIn('name', $keys)->update(['value' => DB::raw('default')]);
    }

    public function deleteAll(): void
    {
        Configuration::delete();
    }

    public function delete(array $keys): void
    {
        Configuration::whereIn('name', $keys)->delete();
    }
}
