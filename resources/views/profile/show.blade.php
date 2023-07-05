@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="card bg-blueGray-100 lg:w-6/12">
        <div class="card-header">
            <div class="card-header-container">
                <h6 class="card-title">
                    {{ __('global.my_profile') }}
                </h6>
            </div>
        </div>

        <div class="card-body">
            <div class="pt-3">
                @livewire('update-profile-information-form')

                    <hr class="mt-6 border-b-1 border-blueGray-300">

                    @livewire('update-password-form')
            </div>
        </div>
    </div>


</div>
@endsection