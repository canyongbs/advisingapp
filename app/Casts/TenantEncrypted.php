<?php

namespace App\Casts;

use Exception;
use App\Models\Tenant;
use Illuminate\Support\Str;
use Illuminate\Encryption\Encrypter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class TenantEncrypted implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        $key = $model instanceof Tenant
            ? (new Encrypter($this->parseKey(app('originalAppKey')), config('app.cipher')))->decrypt($attributes['key'])
            : (
                Tenant::checkCurrent()
                    ? Tenant::current()->key
                    : throw new Exception('Unable to resolve tenant for encryption key')
            );

        if (is_null($key)) {
            throw new Exception('Tenant key required for encryption is null');
        }

        $encrypter = new Encrypter($this->parseKey($key), config('app.cipher'));

        return $encrypter->decrypt($value);
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        $key = $model instanceof Tenant
            ? (new Encrypter($this->parseKey(app('originalAppKey')), config('app.cipher')))->decrypt($attributes['key'])
            : (
                Tenant::checkCurrent()
                ? Tenant::current()->key
                : throw new Exception('Unable to resolve tenant for encryption key')
            );

        if (is_null($key)) {
            throw new Exception('Tenant key required for encryption is null');
        }

        $encrypter = new Encrypter($this->parseKey($key), config('app.cipher'));

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
