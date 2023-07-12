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
                                    {{ __('Verify Your Email Address') }}
                                </h6>
                            </div>
                            <hr class="border-b-1 mt-6 border-blueGray-300" />
                        </div>
                        <div class="flex-auto px-4 py-10 pt-0 lg:px-10">
                            @if (session('resent'))
                                <div class="relative mb-4 rounded border-0 bg-green-500 px-6 py-4 text-white">
                                    <span class="mr-8 inline-block align-middle">
                                        {{ __('A fresh verification link has been sent to your email address.') }}
                                    </span>
                                </div>
                            @endif
                            {{ __('Before proceeding, please check your email for a verification link.') }}
                            {{ __('If you did not receive the email') }},
                            <form
                                class="inline"
                                method="POST"
                                action="{{ route('verification.resend') }}"
                            >
                                @csrf
                                <button class="m-0 inline-block p-0 align-baseline text-lightBlue-500 hover:underline">
                                    {{ __('click here to request another') }}
                                </button>.
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
