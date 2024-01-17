<?php

namespace App\Casts;

use Illuminate\Support\Str;
use Illuminate\Encryption\Encrypter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class LandlordEncrypted implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        $encrypter = new Encrypter($this->parseKey(app('originalAppKey')), config('app.cipher'));

        return $encrypter->decrypt($value);
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        $encrypter = new Encrypter($this->parseKey(app('originalAppKey')), config('app.cipher'));

        return $encrypter->encrypt($value);
    }

    protected function parseKey(string $configKey): false|string
    {
        if (Str::startsWith($key = $configKey, $prefix = 'base64:')) {
            $key = base64_decode(Str::after($key, $prefix));
        }

        return $key;
    }
}
