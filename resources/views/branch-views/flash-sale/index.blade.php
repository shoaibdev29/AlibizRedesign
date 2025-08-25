@extends('layouts.branch.app')

@section('title', translate('Flash Sale'))

@section('content')
<div class="content container-fluid">
    <div class="mb-3">
        <h2 class="text-capitalize mb-0 d-flex align-items-center gap-2">
            <img width="16" src="{{ asset('assets/admin/img/icons/flash-sale.png') }}" alt="{{ translate('flash') }}">
            {{ translate('Flash Sale') }}
        </h2>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('branch.flash-sale.store') }}" method="post" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-4">
                            <label class="input-label">{{ translate('Title') }}</label>
                            <input type="text" name="title" class="form-control" placeholder="{{ translate('Ex : LUX') }}" required maxlength="255">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-4">
                            <label class="input-label">{{ translate('Start Date') }}</label>
                            <input type="datetime-local" name="start_date" id="start_date" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-4">
                            <label class="input-label">{{ translate('End Date') }}</label>
                            <input type="datetime-local" name="end_date" id="end_date" class="form-control" required>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-3">
                    <button type="reset" class="btn btn-secondary px-5">{{ translate('reset') }}</button>
                    <button type="submit" class="btn btn-primary px-5">{{ translate('submit') }}</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card mt-4">
        <div class="px-20 py-3">
            <div class="row gy-2 align-items-center">
                <div class="col-sm-4">
                    <h5 class="text-capitalize d-flex align-items-center gap-2 mb-0">
                        {{ translate('Flash Table') }}
                        <span class="badge badge-soft-dark rounded-50 fz-12">{{ $flashSales->total() }}</span>
                    </h5>
                </div>
                <div class="col-sm-8">
                    <div class="d-flex flex-wrap justify-content-sm-end gap-2">
                        <form action="{{ url()->current() }}" method="GET">
                            <div class="input-group">
                                <input id="datatableSearch_" type="search" name="search" class="form-control" value="{{ $search }}" placeholder="{{ translate('Search by Title') }}" required autocomplete="off">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-primary">{{ translate('search') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive datatable-custom">
            <table class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                <thead class="thead-light">
                    <tr>
                        <th>{{ translate('SL') }}</th>
                        <th>{{ translate('Title') }}</th>
                        <th>{{ translate('Duration') }}</th>
                        <th>{{ translate('Status') }}</th>
                        <th>{{ translate('Active Products') }}</th>
                        <th>{{ translate('Publish') }}</th>
                        <th class="text-center">{{ translate('Action') }}</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($flashSales as $key => $flash)
                    <tr>
                        <td>{{ $flashSales->firstItem() + $key }}</td>
                        <td>{{ $flash['title'] }}</td>
                        <td>{{ date('Y-m-d H:i A', strtotime($flash['start_date'])) }} - {{ date('Y-m-d H:i A', strtotime($flash['end_date'])) }}</td>
                        <td>
                            @if(\Carbon\Carbon::parse($flash['end_date'])->isPast())
                                <span class="badge badge-soft-danger">{{ translate('expired') }}</span>
                            @else
                                <span class="badge badge-soft-success">{{ translate('active') }}</span>
                            @endif
                        </td>
                        <td>{{ $flash->products_count }}</td>
                        <td>
                            <label class="switcher">
                                <input type="checkbox" class="switcher_input route-alert"
                                       data-route="{{ route('branch.flash-sale.status', [$flash->id, $flash->status ? 0 : 1]) }}"
                                       data-message="{{ $flash->status ? translate('you_want_to_disable_this') : translate('you_want_to_active_this') }}"
                                       {{ $flash->status ? 'checked' : '' }}>
                                <span class="switcher_control"></span>
                            </label>
                        </td>
                        <td>
                            <div class="d-flex justify-content-center gap-2">
                                <a class="btn btn-soft-primary btn-sm py-1" href="{{ route('branch.flash-sale.add-product', [$flash['id']]) }}">
                                    <i class="tio tio-add"></i> {{ translate('Add Product') }}
                                </a>
                                <a class="btn btn-outline-info square-btn" href="{{ route('branch.flash-sale.edit', [$flash['id']]) }}">
                                    <i class="tio tio-edit"></i>
                                </a>
                                <a class="btn btn-outline-danger square-btn form-alert" href="javascript:"
                                   data-id="deal-{{ $flash['id'] }}"
                                   data-message="{{ translate('Want to delete this') }}">
                                    <i class="tio tio-delete"></i>
                                </a>
                            </div>
                            <form action="{{ route('branch.flash-sale.delete', [$flash['id']]) }}" method="post" id="deal-{{ $flash['id'] }}">
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
                {!! $flashSales->links() !!}
            </div>
        </div>

        @if(count($flashSales) == 0)
        <div class="text-center p-4">
            <img class="mb-3 width-7rem" src="{{ asset('assets/admin/svg/illustrations/sorry.svg') }}" alt="Image">
            <p class="mb-0">{{ translate('No data to show') }}</p>
        </div>
        @endif
    </div>
</div>
@endsection

@push('script_2')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
"use strict";

// Date validation (already in your code)
$('#start_date, #end_date').change(function () {
    let from = $('#start_date').val();
    let to = $('#end_date').val();
    if (from && to && from > to) {
        $('#start_date').val('');
        $('#end_date').val('');
        toastr.error('{{ translate('Invalid date range!') }}');
    }
});

// ✅ Status toggle
$(document).on('change', '.route-alert', function() {
    let url = $(this).data('route');
    let message = $(this).data('message');
    let checkbox = $(this);

    Swal.fire({
        title: '{{ translate("Are you sure?") }}',
        text: message,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: '{{ translate("Yes") }}',
        cancelButtonText: '{{ translate("Cancel") }}'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = url;
        } else {
            // rollback checkbox state if cancelled
            checkbox.prop('checked', !checkbox.prop('checked'));
        }
    });
});

// ✅ Delete confirmation
$(document).on('click', '.form-alert', function() {
    let form_id = $(this).data('id');
    let message = $(this).data('message');

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
            $('#' + form_id).submit();
        }
    });
});

@if(Session::has('success'))
    toastr.success("{{ Session::get('success') }}");
@endif
@if(Session::has('error'))
    toastr.error("{{ Session::get('error') }}");
@endif
</script>
@endpush
