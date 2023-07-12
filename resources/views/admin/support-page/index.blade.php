@extends('layouts.admin')
@section('content')
    <div class="row">
        <div class="card bg-white">
            <div class="card-header border-b border-blueGray-200">
                <div class="card-header-container">
                    <h6 class="card-title">
                        {{ trans('cruds.supportPage.title_singular') }}
                        {{ trans('global.list') }}
                    </h6>

                    @can('support_page_create')
                        <a
                            class="btn btn-indigo"
                            href="{{ route('admin.support-pages.create') }}"
                        >
                            {{ trans('global.add') }} {{ trans('cruds.supportPage.title_singular') }}
                        </a>
                    @endcan
                </div>
            </div>
            @livewire('support-page.index')

        </div>
    </div>
@endsection
