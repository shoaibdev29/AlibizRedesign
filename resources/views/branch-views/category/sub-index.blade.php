@extends('layouts.branch.app')

@section('title', translate('Add new sub category'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
<div class="content container-fluid">
    <div class="mb-3">
        <h2 class="text-capitalize mb-0 d-flex align-items-center gap-2">
            <img width="20" src="{{ asset('assets/admin/img/icons/brand-setup.png') }}" alt="{{ translate('image') }}">
            {{ translate('Sub Category Setup') }}
        </h2>
    </div>

    {{-- Add Sub-Category Form --}}
    <div class="card mb-3">
        <div class="card-body">
            <form action="{{ route('branch.category.store') }}" method="post">
                @csrf
                @php
                    $language = \App\Models\BusinessSetting::where('key','language')->first();
                    $language = $language->value ?? null;
                    $default_lang = 'en';
                @endphp

                @if($language)
                    @php($default_lang = json_decode($language)[0])
                    <ul class="nav nav-tabs mb-4 max-content">
                        @foreach(json_decode($language) as $lang)
                            <li class="nav-item">
                                <a class="nav-link lang_link {{ $lang == $default_lang ? 'active' : '' }}" href="#" id="{{ $lang }}-link">
                                    {{ Helpers::get_language_name($lang) . '(' . strtoupper($lang) . ')' }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                    <div class="row">
                        <div class="col-sm-6">
                            @foreach(json_decode($language) as $lang)
                                <div class="form-group {{ $lang != $default_lang ? 'd-none' : '' }} lang_form" id="{{ $lang }}-form">
                                    <label class="input-label">{{ translate('Sub Category') }} {{ translate('name') }} ({{ strtoupper($lang) }})</label>
                                    <input type="text" name="name[]" class="form-control" maxlength="255"
                                           placeholder="{{ translate('New Sub Category') }}"
                                           {{ $lang == $default_lang ? 'required' : '' }}
                                           oninvalid="document.getElementById('en-link').click()">
                                </div>
                                <input type="hidden" name="lang[]" value="{{ $lang }}">
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group lang_form" id="{{ $default_lang }}-form">
                                <label class="input-label">{{ translate('Sub Category') }} {{ translate('name') }} ({{ strtoupper($default_lang) }})</label>
                                <input type="text" name="name[]" class="form-control" maxlength="255"
                                       placeholder="{{ translate('New Sub Category') }}" required>
                            </div>
                            <input type="hidden" name="lang[]" value="{{ $default_lang }}">
                        </div>
                    </div>
                @endif

                <input name="position" value="1" class="d-none">

                {{-- Parent Category --}}
                <div class="row mt-3">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="input-label">{{ translate('Main Category') }} <span class="input-label-secondary">*</span></label>
                            <select name="parent_id" class="form-control" required>
                                @foreach(\App\Models\Category::where('position', 0)->get() as $category)
                                    <option value="{{ $category['id'] }}">{{ $category['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Submit Buttons --}}
                <div class="row mt-3">
                    <div class="col-12 d-flex justify-content-end gap-3">
                        <button type="reset" class="btn btn-secondary">{{ translate('reset') }}</button>
                        <button type="submit" class="btn btn-primary">{{ translate('submit') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Sub-Category Table --}}
    <div class="card">
        <div class="px-20 py-3">
            <div class="row gy-2 align-items-center">
                <div class="col-lg-8 col-sm-4 col-md-6">
                    <h5 class="text-capitalize d-flex align-items-center gap-2 mb-0">
                        {{ translate('Sub Category Table') }}
                        <span class="badge badge-soft-dark rounded-50 fz-12">{{ $categories->total() }}</span>
                    </h5>
                </div>
                <div class="col-lg-4 col-sm-8 col-md-6">
                    <form action="{{ url()->current() }}" method="GET">
                        <div class="input-group">
                            <input type="search" name="search" class="form-control"
                                   placeholder="{{ translate('Search by Sub Category') }}" aria-label="Search"
                                   value="{{ $search ?? '' }}" autocomplete="off">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-primary">{{ translate('search') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="table-responsive datatable-custom">
            <table class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                <thead class="thead-light">
                    <tr>
                        <th>{{ translate('SL') }}</th>
                        <th>{{ translate('Main Category') }}</th>
                        <th>{{ translate('Sub Category') }}</th>
                        <th>{{ translate('Status') }}</th>
                        <th class="text-center">{{ translate('Action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categories as $key => $category)
                        <tr>
                            <td>{{ $categories->firstItem() + $key }}</td>
                            <td>{{ $category->parent['name'] ?? '-' }}</td>
                            <td>{{ $category['name'] }}</td>
                            <td>
                                <label class="switcher">
                                    <input type="checkbox" class="switcher_input change-status"
                                           {{ $category['status'] == 1 ? 'checked' : '' }}
                                           data-route="{{ route('branch.category.status', [$category['id'], $category['status'] == 1 ? 0 : 1]) }}">
                                    <span class="switcher_control"></span>
                                </label>
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    <a class="btn btn-outline-info square-btn" href="{{ route('branch.category.edit', [$category['id']]) }}">
                                        <i class="tio tio-edit"></i>
                                    </a>
                                    <a class="btn btn-outline-danger square-btn form-alert" href="javascript:"
                                       data-id="category-{{ $category['id'] }}"
                                       data-message="{{ translate('Want to delete this sub category?') }}">
                                       <i class="tio tio-delete"></i>
                                    </a>
                                </div>
                                <form action="{{ route('branch.category.delete', [$category['id']]) }}" method="post" id="category-{{ $category['id'] }}">
                                    @csrf @method('delete')
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="table-responsive mt-4 px-3">
            <div class="d-flex justify-content-end">
                {!! $categories->links() !!}
            </div>
        </div>

        @if(count($categories) == 0)
            <div class="text-center p-4">
                <img class="mb-3 width-7rem" src="{{ asset('assets/admin/svg/illustrations/sorry.svg') }}" alt="{{ translate('Image') }}">
                <p class="mb-0">{{ translate('No data to show') }}</p>
            </div>
        @endif
    </div>
</div>
@endsection

@push('script_2')
<script src="{{ asset('assets/admin/js/category.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
"use strict";

// STATUS toggle - same as notification
$(document).on('change', '.change-status', function() {
    let url = $(this).data('route');
    if(url) window.location.href = url;
});

// DELETE confirmation - same as notification
$(document).on('click', '.form-alert', function() {
    let form_id = $(this).data('id');
    let message = $(this).data('message') || '{{ translate("Are you sure?") }}';

    Swal.fire({
        title: '{{ translate("Are you sure?") }}',
        text: message,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: '{{ translate("Yes, delete it!") }}',
        cancelButtonText: '{{ translate("Cancel") }}'
    }).then((result) => {
        if (result.isConfirmed) {
            $("#" + form_id).submit();
        }
    });
});

// Optional: Toastr success
@if(Session::has('success'))
    toastr.success("{{ Session::get('success') }}");
@endif
</script>
@endpush