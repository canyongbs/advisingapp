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

    <form action="{{ $route }}" method="POST">
        @csrf
        @method('PUT')
        <input type="hidden" name="filename" value={{ $filename }}>
        <input type="hidden" name="redirectTo" value={{ $redirectTo }}>
        <input type="hidden" name="has_header" value={{ $csvHasHeader }}>

        <div class="overflow-hidden">
            <div class="overflow-x-auto">
                <table class="table table-index w-full">
                    @if($csvHasHeader)
                        <thead>
                            <tr>
                                @foreach($csvHeader as $header)
                                    <th>{{ $header }}</th>
                                @endforeach
                            </tr>
                        </thead>
                    @endif
                    <tbody>
                        @foreach($csvPreviewLines as $entries)
                            <tr>
                                @foreach($entries as $entry)
                                    <td>{{ $entry }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                        <tr>
                            @foreach($csvHeader as $key => $entry)
                                <td>
                                    <select name="fields[{{ $key }}]" id="{{ $key }}">
                                        <option value="">Please select</option>
                                        @foreach($fillables as $field)
                                            <option value="{{ $field }}" {{ strtolower($entry) === strtolower($field) ? 'selected' : '' }}>
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
                        <button type="submit" class="btn btn-indigo mr-2">
                            import
                        </button>
                        <a href="{{ url()->previous() }}" class="btn btn-secondary">
                            {{ trans('global.cancel') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>

</div>

@endsection