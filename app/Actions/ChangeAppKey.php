<?php

namespace App\Actions;

use Illuminate\Support\Str;
use Illuminate\Encryption\Encrypter;
use Illuminate\Database\Eloquent\Model;
use Laravel\SerializableClosure\SerializableClosure;

class ChangeAppKey
{
    public function __invoke(string $appKey): void
    {
        config()->set('app.key', $appKey);

        app()->extend('encrypter', function ($service, $app) use ($appKey) {
            $config = $app->make('config')->get('app');

            return new Encrypter($this->parseKey($appKey), $config['cipher']);
        });

        Model::$encrypter = app('encrypter');

        if (class_exists(SerializableClosure::class)) {
            SerializableClosure::setSecretKey($this->parseKey($appKey));
        }
    }

    protected function parseKey(string $key): false|string
    {
        if (Str::startsWith($key, $prefix = 'base64:')) {
            $key = base64_decode(Str::after($key, $prefix));
        }

        return $key;
    }
}
