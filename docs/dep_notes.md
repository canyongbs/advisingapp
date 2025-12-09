# Composer

### Dec. 9th 2025

Locked `laravel/socialite` to `^v5.23.2, !=5.24.0` as `5.24.0` has a conflict with the way `barryvdh/laravel-ide-helper` generates.

To resolve https://github.com/barryvdh/laravel-ide-helper/pull/1745 or something similiar will need to be patched in and released in `barryvdh/laravel-ide-helper`. Once that is done we can test removing the `!=5.24.0` restriction on `laravel/socialite` and ensure both packages work together in their latest versions.
