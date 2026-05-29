<?php

namespace App\Models\Concerns;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use ReflectionProperty;

/**
 * @mixin Model
 *
 * @method static void registerModelEvent(string $event, \Closure|string $callback)
 */
trait LogsActivity
{
    public static function bootLogsActivity(): void
    {
        static::registerModelEvent('created', function (Model $model): void {
            /** @var static&Model $model */
            $model->writeActivityLog('tambah');
        });

        static::registerModelEvent('updated', function (Model $model): void {
            /** @var static&Model $model */
            if (! $model->wasChanged()) {
                return;
            }

            $model->writeActivityLog('mengubah');
        });

        static::registerModelEvent('deleted', function (Model $model): void {
            /** @var static&Model $model */
            $model->writeActivityLog('menghapus');
        });
    }

    protected function writeActivityLog(string $action): void
    {
        ActivityLog::record(
            $action,
            $this->resolveActivityObject(),
            $this->resolveActivityMenu()
        );
    }

    protected function resolveActivityMenu(): string
    {
        $class = static::class;

        if (! property_exists($class, 'activityMenu')) {
            return 'data';
        }

        $property = new ReflectionProperty($class, 'activityMenu');

        if (! $property->isStatic()) {
            return 'data';
        }

        $value = $property->getValue();

        return is_string($value) && $value !== '' ? $value : 'data';
    }

    protected function resolveActivityObject(): string
    {
        if (method_exists($this, 'activityObjectName')) {
            return $this->activityObjectName();
        }

        foreach (['nama', 'product_name', 'desk', 'deskripsi', 'kat', 'id'] as $attribute) {
            $value = $this->getAttribute($attribute);
            if ($value !== null && $value !== '') {
                return (string) $value;
            }
        }

        return 'item';
    }
}
