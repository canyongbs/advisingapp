@extends('layouts.admin')
@section('content')

    <div class="card bg-white">
        <div class="card-header">
            <div class="card-header-container">
                <h6 class="card-title">
                    CSV Import
                </h6>
            </div>
        </div>

        <form
            action="{{ $route }}"
            method="POST"
        >
            @csrf
            @method('PUT')
            <input
                name="filename"
                type="hidden"
                value={{ $filename }}
            >
            <input
                name="redirectTo"
                type="hidden"
                value={{ $redirectTo }}
            >
            <input
                name="has_header"
                type="hidden"
                value={{ $csvHasHeader }}
            >

            <div class="overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="table-index table w-full">
                        @if ($csvHasHeader)
                            <thead>
                                <tr>
                                    @foreach ($csvHeader as $header)
                                        <th>{{ $header }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                        @endif
                        <tbody>
                            @foreach ($csvPreviewLines as $entries)
                                <tr>
                                    @foreach ($entries as $entry)
                                        <td>{{ $entry }}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                            <tr>
                                @foreach ($csvHeader as $key => $entry)
                                    <td>
                                        <select
                                            id="{{ $key }}"
                                            name="fields[{{ $key }}]"
                                        >
                                            <option value="">Please select</option>
                                            @foreach ($fillables as $field)
                                                <option
                                                    value="{{ $field }}"
                                                    {{ strtolower($entry) === strtolower($field) ? 'selected' : '' }}
                                                >
                                                    {{ $field }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                @endforeach
                            </tr>
                        </tbody>
                    </table>

                    <div class="card-body mt-3">
                        <div class="form-group">
                            <button
                                class="btn btn-indigo mr-2"
                                type="submit"
                            >
                                import
                            </button>
                            <a
                                class="btn btn-secondary"
                                href="{{ url()->previous() }}"
                            >
                                {{ trans('global.cancel') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>

    </div>

@endsection
