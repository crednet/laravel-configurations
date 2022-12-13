<?php

namespace Credpal\Configurations\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $title
 * @property string $name
 * @property string $default
 * @property string|null $value
 * @property string|null $value_type
 * @property mixed|null $data
 * @method static Configuration create(array $attributes)
 * @method static void delete()
 * @method static Configuration|null first(array $attributes = [])
 * @method static Configuration update(array $attributes)
 * @method static Configuration updateOrCreate(array $array1, array $array2)
 * @method static int upsert(array $values, array $uniqueBy, array $update = null)
 * @method static Builder|Configuration whereIn(string $column, array $values)
 * @method static Builder|Configuration whereName(string $key)
 */
class Configuration extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'name',
        'default',
        'value',
        'value_type',
        'data',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'data' => 'json',
    ];

    /**
     * Get the current value of a configuration
     * Or return the preferred value if the configuration doesn't exist
     *
     * @param string $key
     * @param mixed|null $default
     *
     * @return mixed
     */
    public static function getValue(string $key, mixed $default = null): mixed
    {
        $config = self::whereName($key)->first(['default', 'value']);
        return $config?->value ?? $config?->default ?? $default;
    }

    /**
     * Get the default value of a configuration
     * Or return the preferred value if the configuration doesn't exist
     *
     * @param string $key
     * @param mixed|null $default
     *
     * @return mixed
     */
    public static function getDefault(string $key, mixed $default = null): mixed
    {
        return self::whereName($key)->first(['default'])?->default ?? $default;
    }
}
