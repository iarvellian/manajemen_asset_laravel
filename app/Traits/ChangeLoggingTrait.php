<?php

namespace App\Traits;

use App\Models\ChangeLogs;

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
        $jsonData = $model->toJson();
        $arrayJsonData = json_decode($jsonData, true);
        $arrayOriginalData = "";

        if (is_array($arrayJsonData)) {
            $arrayJsonData = json_encode($arrayJsonData);
        }

        if (count($model->getOriginal()) > 0) {
            $originalData = $model->getOriginal();

            if (is_array($originalData)) {
                $arrayOriginalData = json_encode($originalData);
            }
        }

        ChangeLogs::create([
            'nama_tabel' => $model->getTable(),
            'aksi' => $action,
            'data_lama' => ($action === 'deleted') ? $arrayJsonData : $arrayOriginalData,
            'data_baru' => ($action === 'created' || $action === 'updated') ? $arrayJsonData : null,
            'user_id' => auth()->id(),
        ]);
    }
}