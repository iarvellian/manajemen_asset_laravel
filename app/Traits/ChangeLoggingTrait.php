<?php

namespace App\Traits;

use App\Models\changeLogs;

trait ChangeLoggingTrait
{
    public static function bootChangeLoggingTrait()
    {
        static::created(function ($model) {
            self::createChangeLog($model, 'created');
        });

        static::updated(function ($model) {
            self::createChangeLog($model, 'updated');
        });

        static::deleted(function ($model) {
            self::createChangeLog($model, 'deleted');
        });
    }

    protected static function createChangeLog($model, $action)
    {
        // Convert model attributes to an array
        $jsonData = $model->toArray();

        // Retrieve original data if available
        $originalData = ($action !== 'created') ? $model->getOriginal() : null;

        // Format created_at and updated_at if they exist
        if (isset($jsonData['created_at'])) {
            $jsonData['created_at'] = \Carbon\Carbon::parse($jsonData['created_at'])->format('d M Y');
        }

        if (isset($jsonData['updated_at'])) {
            $jsonData['updated_at'] = \Carbon\Carbon::parse($jsonData['updated_at'])->format('d M Y');
        }

        // Format created_at and updated_at in original data if they exist
        if ($originalData !== null) {
            if (isset($originalData['created_at'])) {
                $originalData['created_at'] = \Carbon\Carbon::parse($originalData['created_at'])->format('d M Y');
            }

            if (isset($originalData['updated_at'])) {
                $originalData['updated_at'] = \Carbon\Carbon::parse($originalData['updated_at'])->format('d M Y');
            }
        }

        // Convert arrays to JSON strings
        $jsonData = json_encode($jsonData);
        $arrayOriginalData = json_encode($originalData);

        ChangeLogs::create([
            'nama_tabel' => $model->getTable(),
            'aksi' => $action,
            'data_lama' => ($action === 'deleted') ? $jsonData : $arrayOriginalData,
            'data_baru' => ($action === 'created' || $action === 'updated') ? $jsonData : json_encode([]),
            'user_id' => auth()->id(),
        ]);
    }
}