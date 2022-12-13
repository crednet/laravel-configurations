<?php

namespace Credpal\Configurations\Services;

use Credpal\Configurations\Models\Configuration;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ConfigurationService
{
    public function updateOrCreate(array $data): bool
    {
        $configurations = Configuration::whereIn(
            'name',
            array_map(fn ($config) => $config['name'], $data)
        )
            ->get();

        DB::transaction(function () use ($data, $configurations) {
            $configsChanged = [];
            foreach ($data as $params) {
                $configuration = $configurations->firstWhere('name', $params['name']);
                if (!$configuration) {
                    $configuration = Configuration::create($params);
                    $configsChanged[] = $params['name'];
                }

                $configuration->fill($params);
                if ($configuration->isDirty()) {
                    $configuration->save();
                    $configsChanged[] = $params['name'];
                }
            }

            if (count($configsChanged)) {
                $this->clearConfigCache($configsChanged);
            }
        });

        return true;
    }

    public function getAll(array $keys = []): Collection
    {
        return Cache::tags(['configurations'])
            ->remember(
                'configurations',
                86400 * 365,
                fn () => count($keys)
                    ? Configuration::whereIn('name', $keys)->get()
                    : Configuration::all(),
            );
    }

    public function get(string $key): mixed
    {
        return Cache::tags(['configurations'])
            ->remember(
                $key,
                86400 * 365,
                fn () => Configuration::getValue($key),
            );
    }

    public function resetAll(): void
    {
        Configuration::update(['value' => DB::raw('default')]);
        $this->clearAllCache();
    }

    public function reset(array $keys): void
    {
        Configuration::whereIn('name', $keys)->update(['value' => DB::raw('default')]);
        $this->clearConfigCache($keys);
    }

    public function deleteAll(): void
    {
        Configuration::delete();
        $this->clearAllCache();
    }

    public function delete(array $keys): void
    {
        Configuration::whereIn('name', $keys)->delete();
        $this->clearConfigCache($keys);
    }

    protected function clearAllCache(): void
    {
        Cache::tags(['configurations'])->flush();
    }

    protected function clearConfigCache(array $keys): void
    {
        array_walk_recursive(
            $keys,
            fn ($key) => Cache::tags(['configurations'])
                ->forget($key)
        );
    }
}
