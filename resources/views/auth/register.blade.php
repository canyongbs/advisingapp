@extends('layouts.app')

@section('content')
    <section class="relative h-full min-h-screen w-full py-40">
        <div class="container mx-auto h-full px-4">
            <div class="flex h-full content-center items-center justify-center">
                <div class="w-full px-4 lg:w-6/12">
                    <div
                        class="relative mb-6 flex w-full min-w-0 flex-col break-words rounded-lg border-0 bg-blueGray-200 shadow-lg">
                        <div class="mb-0 rounded-t px-6 py-6">
                            <div class="mb-3 text-center">
                                <h6 class="text-sm font-bold text-blueGray-500">
                                    {{ __('global.register') }}
                                </h6>
                            </div>
                            <hr class="border-b-1 mt-6 border-blueGray-300" />
                        </div>
                        <div class="flex-auto px-4 py-10 pt-0 lg:px-10">
                            <form
                                method="POST"
                                action="{{ route('register') }}"
                            >
                                @csrf
                                <div class="relative mb-3 w-full">
                                    <label
                                        class="mb-2 block text-xs font-bold uppercase text-blueGray-600"
                                        for="name"
                                    >
                                        {{ __('global.user_name') }}
                                    </label>
                                    <input
                                        class="{{ $errors->has('name') ? ' ring ring-red-300' : '' }} w-full rounded border-0 bg-white px-3 py-3 text-sm text-blueGray-600 placeholder-blueGray-300 shadow focus:outline-none focus:ring"
                                        id="name"
                                        name="name"
                                        type="text"
                                        value="{{ old('name') }}"
                                        placeholder="{{ __('global.user_name') }}"
                                        required
                                        autofocus
                                        autocomplete="name"
                                    />
                                    @error('name')
                                        <div class="text-red-500">
                                            <small>{{ $message }}</small>
                                        </div>
                                    @enderror
                                </div>
                                <div class="relative mb-3 w-full">
                                    <label
                                        class="mb-2 block text-xs font-bold uppercase text-blueGray-600"
                                        for="email"
                                    >
                                        {{ __('global.login_email') }}
                                    </label>
                                    <input
                                        class="{{ $errors->has('email') ? ' ring ring-red-300' : '' }} w-full rounded border-0 bg-white px-3 py-3 text-sm text-blueGray-600 placeholder-blueGray-300 shadow focus:outline-none focus:ring"
                                        id="email"
                                        name="email"
                                        type="email"
                                        value="{{ old('email') }}"
                                        placeholder="{{ __('global.login_email') }}"
                                        required
                                        autocomplete="email"
                                    />
                                    @error('email')
                                        <div class="text-red-500">
                                            <small>{{ $message }}</small>
                                        </div>
                                    @enderror
                                </div>
                                <div class="relative mb-3 w-full">
                                    <label
                                        class="mb-2 block text-xs font-bold uppercase text-blueGray-600"
                                        for="password"
                                    >
                                        {{ __('global.login_password') }}
                                    </label>
                                    <input
                                        class="{{ $errors->has('password') ? ' ring ring-red-300' : '' }} w-full rounded border-0 bg-white px-3 py-3 text-sm text-blueGray-600 placeholder-blueGray-300 shadow focus:outline-none focus:ring"
                                        id="password"
                                        name="password"
                                        type="password"
                                        placeholder="{{ __('global.login_password') }}"
                                        required
                                        autocomplete="new-password"
                                    />
                                    @error('password')
                                        <div class="text-red-500">
                                            <small>{{ $message }}</small>
                                        </div>
                                    @enderror
                                </div>
                                <div class="relative mb-3 w-full">
                                    <label
                                        class="mb-2 block text-xs font-bold uppercase text-blueGray-600"
                                        for="password_confirmation"
                                    >
                                        {{ __('global.confirm_password') }}
                                    </label>
                                    <input
                                        class="w-full rounded border-0 bg-white px-3 py-3 text-sm text-blueGray-600 placeholder-blueGray-300 shadow focus:outline-none focus:ring"
                                        id="password_confirmation"
                                        name="password_confirmation"
                                        type="password"
                                        placeholder="{{ __('global.confirm_password') }}"
                                        required
                                        autocomplete="new-password"
                                    />
                                </div>
                                <div class="mt-6 text-center">
                                    <button
                                        class="mb-1 mr-1 w-full rounded bg-blueGray-800 px-6 py-3 text-sm font-bold uppercase text-white shadow outline-none transition-all duration-150 ease-linear hover:shadow-lg focus:outline-none active:bg-blueGray-600"
                                    >
                                        {{ __('global.register') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
