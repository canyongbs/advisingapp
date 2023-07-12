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
                                    {{ __('global.reset_password') }}
                                </h6>
                            </div>
                            <hr class="border-b-1 mt-6 border-blueGray-300" />
                        </div>
                        <div class="flex-auto px-4 py-10 pt-0 lg:px-10">
                            @if (session('status'))
                                <div class="relative mb-4 rounded border-0 bg-green-500 px-6 py-4 text-white">
                                    <span class="mr-8 inline-block align-middle">
                                        {{ session('status') }}
                                    </span>
                                </div>
                            @endif

                            <form
                                method="POST"
                                action="{{ route('password.email') }}"
                            >
                                @csrf
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
                                        autofocus
                                    />
                                    @error('email')
                                        <div class="text-red-500">
                                            <small>{{ $message }}</small>
                                        </div>
                                    @enderror
                                </div>
                                <div class="mt-6 text-center">
                                    <button
                                        class="mb-1 mr-1 w-full rounded bg-blueGray-800 px-6 py-3 text-sm font-bold uppercase text-white shadow outline-none transition-all duration-150 ease-linear hover:shadow-lg focus:outline-none active:bg-blueGray-600"
                                    >
                                        {{ __('global.send_password') }}
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
