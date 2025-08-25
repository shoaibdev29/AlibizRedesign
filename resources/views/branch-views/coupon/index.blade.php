@extends('layouts.branch.app')

@section('title', translate('Add new coupon'))

@section('content')
<div class="content container-fluid">
    <div class="mb-3">
        <h2 class="text-capitalize mb-0 d-flex align-items-center gap-2">
            <img width="20" src="{{ asset('assets/admin/img/icons/coupon.png') }}" alt="{{ translate('coupon') }}">
            {{ translate('add_new_coupon') }}
        </h2>
    </div>

    {{-- Coupon Form --}}
    <div class="card">
        <div class="card-body">
            <form action="{{ route('branch.coupon.store') }}" method="post">
                @csrf
                <div class="row">
                    {{-- Title --}}
                    <div class="col-lg-4 col-sm-6">
                        <div class="form-group">
                            <label class="input-label">{{ translate('title') }}</label>
                            <input type="text" name="title" value="{{ old('title') }}" class="form-control" placeholder="{{ translate('New coupon') }}" required maxlength="100">
                        </div>
                    </div>

                    {{-- Coupon Type --}}
                    <div class="col-lg-4 col-sm-6">
                        <div class="form-group">
                            <label class="input-label">{{ translate('coupon') }} {{ translate('type') }}</label>
                            <select name="coupon_type" class="form-control coupon-type">
                                <option value="default">{{ translate('default') }}</option>
                                <option value="first_order">{{ translate('first order') }}</option>
                            </select>
                        </div>
                    </div>

                    {{-- Limit for user --}}
                    <div class="col-lg-4 col-sm-6" id="limit-for-user">
                        <div class="form-group">
                            <label class="input-label">{{ translate('limit') }} {{ translate('for') }} {{ translate('same') }} {{ translate('user') }}</label>
                            <input type="number" name="limit" id="user-limit" value="{{ old('limit') }}" class="form-control" max="100000" placeholder="{{ translate('EX: 10') }}" required min="1">
                        </div>
                    </div>

                    {{-- Code --}}
                    <div class="col-lg-4 col-sm-6">
                        <div class="form-group">
                            <div class="d-flex justify-content-between">
                                <label class="input-label">{{ translate('code') }}</label>
                                <a href="javascript:" class="float-right c1 fz-12 generate-code">{{ translate('generate_code') }}</a>
                            </div>
                            <input type="text" name="code" class="form-control" maxlength="15" id="code" value="{{ old('code') }}"
                                   placeholder="{{ \Illuminate\Support\Str::random(8) }}" required>
                        </div>
                    </div>

                    {{-- Start Date --}}
                    <div class="col-lg-4 col-sm-6">
                        <div class="form-group">
                            <label class="input-label">{{ translate('start') }} {{ translate('date') }}</label>
                            <input type="text" name="start_date" id="start_date" class="js-flatpickr form-control flatpickr-custom" placeholder="{{ translate('Select date') }}" data-hs-flatpickr-options='{ "dateFormat": "Y/m/d", "minDate": "today" }'>
                        </div>
                    </div>

                    {{-- Expire Date --}}
                    <div class="col-lg-4 col-sm-6">
                        <div class="form-group">
                            <label class="input-label">{{ translate('expire') }} {{ translate('date') }}</label>
                            <input type="text" name="expire_date" id="expire_date" class="js-flatpickr form-control flatpickr-custom" placeholder="{{ translate('Select date') }}" data-hs-flatpickr-options='{ "dateFormat": "Y/m/d", "minDate": "today" }'>
                        </div>
                    </div>

                    {{-- Min Purchase --}}
                    <div class="col-lg-4 col-sm-6">
                        <div class="form-group">
                            <label class="input-label">{{ translate('min') }} {{ translate('purchase') }}</label>
                            <input type="number" step="0.01" name="min_purchase" value="{{ old('min_purchase') }}" min="0" max="100000" class="form-control" placeholder="{{ translate('100') }}">
                        </div>
                    </div>

                    {{-- Discount Type --}}
                    <div class="col-lg-4 col-sm-6">
                        <div class="form-group">
                            <label class="input-label">{{ translate('discount') }} {{ translate('type') }}</label>
                            <select name="discount_type" id="discount_type" class="form-control">
                                <option value="percent">{{ translate('percent') }}</option>
                                <option value="amount">{{ translate('amount') }}</option>
                            </select>
                        </div>
                    </div>

                    {{-- Discount --}}
                    <div class="col-lg-4 col-sm-6">
                        <div class="form-group">
                            <label class="input-label">{{ translate('discount') }}</label>
                            <input type="number" step="0.01" min="1" max="100000" name="discount" value="{{ old('discount') }}" class="form-control" required>
                        </div>
                    </div>

                    {{-- Max Discount --}}
                    <div class="col-lg-4 col-sm-6" id="max_discount_div">
                        <div class="form-group">
                            <label class="input-label">{{ translate('max') }} {{ translate('discount') }}</label>
                            <input type="number" step="0.01" min="0" value="{{ old('max_discount') }}" max="100000" name="max_discount" class="form-control">
                        </div>
                    </div>
                </div>

                {{-- Form Buttons --}}
                <div class="d-flex justify-content-end gap-3">
                    <button type="reset" class="btn btn-secondary">{{ translate('Reset') }}</button>
                    <button type="submit" class="btn btn-primary">{{ translate('submit') }}</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Coupon Table --}}
    <div class="card mt-3">
        <div class="px-20 py-3">
            <div class="row gy-2 align-items-center">
                <div class="col-sm-4">
                    <h5 class="text-capitalize d-flex align-items-center gap-2 mb-0">
                        {{ translate('coupon_table') }}
                        <span class="badge badge-soft-dark rounded-50 fz-12">{{ $coupons->total() }}</span>
                    </h5>
                </div>
                <div class="col-sm-8">
                    <div class="d-flex flex-wrap justify-content-sm-end gap-2">
                        <form action="#" method="GET">
                            <div class="input-group">
                                <input id="datatableSearch_" type="search" name="search" class="form-control" placeholder="{{ translate('Search by Title') }}" aria-label="Search" value="{{ $search }}" required autocomplete="off">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-primary">{{ translate('search') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="table-responsive datatable-custom">
            <table class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                <thead class="thead-light">
                    <tr>
                        <th>{{ translate('SL') }}</th>
                        <th>{{ translate('title') }}</th>
                        <th>{{ translate('code') }}</th>
                        <th>{{ translate('min') }} {{ translate('purchase') }}</th>
                        <th>{{ translate('max') }} {{ translate('discount') }}</th>
                        <th>{{ translate('discount') }}</th>
                        <th>{{ translate('discount') }} {{ translate('type') }}</th>
                        <th>{{ translate('start') }} {{ translate('date') }}</th>
                        <th>{{ translate('expire') }} {{ translate('date') }}</th>
                        <th>{{ translate('status') }}</th>
                        <th class="text-center">{{ translate('action') }}</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($coupons as $key => $coupon)
                        <tr>
                            <td>{{ $coupons->firstItem() + $key }}</td>
                            <td>{{ $coupon['title'] }}</td>
                            <td>{{ $coupon['code'] }}</td>
                            <td>{{ Helpers::set_symbol($coupon['min_purchase']) }}</td>
                            <td>{{ $coupon['discount_type'] == 'percent' ? Helpers::set_symbol($coupon['max_discount']) : '-' }}</td>
                            <td>{{ $coupon['discount'] }}</td>
                            <td>{{ translate($coupon['discount_type']) }}</td>
                            <td>{{ date('d-m-Y', strtotime($coupon['start_date'])) }}</td>
                            <td>{{ date('d-m-Y', strtotime($coupon['expire_date'])) }}</td>
                            <td>
                                <label class="switcher">
                                    <input type="checkbox" class="switcher_input change-status" {{ $coupon['status'] == 1 ? 'checked' : '' }} data-route="{{ route('branch.coupon.status', [$coupon['id'], $coupon['status'] == 1 ? 0 : 1]) }}">
                                    <span class="switcher_control"></span>
                                </label>
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    <a class="btn btn-outline-warning square-btn coupon_details" href="#" data-id="{{ $coupon['id'] }}">
                                        <i class="tio-invisible"></i>
                                    </a>
                                    <a class="btn btn-outline-info square-btn" href="{{ route('branch.coupon.update', [$coupon['id']]) }}">
                                        <i class="tio tio-edit"></i>
                                    </a>
                                    <a class="btn btn-outline-danger square-btn form-alert" href="javascript:" data-id="coupon-{{ $coupon['id'] }}" data-message="{{ translate('Want to delete this coupon?') }}">
                                        <i class="tio tio-delete"></i>
                                    </a>
                                </div>
                                <form action="{{ route('branch.coupon.delete', [$coupon['id']]) }}" method="post" id="coupon-{{ $coupon['id'] }}">
                                    @csrf @method('delete')
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="table-responsive mt-4 px-3">
            <div class="d-flex justify-content-end">
                {!! $coupons->links() !!}
            </div>
        </div>

        @if(count($coupons) == 0)
            <div class="text-center p-4">
                <img class="mb-3 width-7rem" src="{{ asset('assets/admin/svg/illustrations/sorry.svg') }}" alt="{{ translate('Image') }}">
                <p class="mb-0">{{ translate('No data to show') }}</p>
            </div>
        @endif
    </div>
</div>

{{-- Coupon Details Modal --}}
<div class="modal fade" id="quick-view" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered coupon-details" role="document">
        <div class="modal-content" id="quick-view-modal"></div>
    </div>
</div>
@endsection

@push('script_2')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
"use strict";

flatpickr("#start_date", {
    dateFormat: "Y-m-d",
    minDate: "today"
});
flatpickr("#expire_date", {
    dateFormat: "Y-m-d",
    minDate: "today"
});

// Coupon code generator
$(document).on('click', '.generate-code', function() {
    const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    let result = '';
    for (let i = 0; i < 8; i++) {
        result += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    $('#code').val(result);
    toastr.success('{{ translate("New coupon code generated") }}');
});


// Initialize toastr
toastr.options = {
    "closeButton": true,
    "progressBar": true,
    "positionClass": "toast-top-right",
    "timeOut": "3000",
};

// Coupon code generator
$(document).on('click', '.generate-code', function() {
    // Generate a random string with numbers and uppercase letters
    const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789'; // Excluded easily confused chars
    let result = '';
    const length = 8; // Coupon code length
    
    for (let i = 0; i < length; i++) {
        result += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    
    // Set the generated code in the input field
    $('#code').val(result);
    
    // Show success notification
    toastr.success('{{ translate("New coupon code generated") }}');
});

// Coupon details modal
$(document).on('click', '.coupon_details', function(e) {
    e.preventDefault();
    let id = $(this).data('id');
    $.ajax({
        type: 'GET',
        url: '{{ route("branch.coupon.details") }}',
        data: { id: id },
        beforeSend: function() { $('#loading').show(); },
        success: function(data) {
            $('#loading').hide();
            $('#quick-view').modal('show');
            $('#quick-view-modal').empty().html(data.view);
        },
        error: function() {
            $('#loading').hide();
            toastr.error('{{ translate("Failed to load coupon details") }}');
        }
    });
});

// Show/Hide Max Discount based on Discount Type
function toggleMaxDiscount() {
    if ($('#discount_type').val() === 'percent') {
        $('#max_discount_div').show();
    } else {
        $('#max_discount_div').hide();
    }
}

// Initial check on page load
$(document).ready(function() {
    toggleMaxDiscount();
});

// On change event
$(document).on('change', '#discount_type', function() {
    toggleMaxDiscount();
});



// STATUS toggle
$(".change-status").change(function(e){
    e.preventDefault();
    e.stopImmediatePropagation();
    var url = $(this).data('route');
    if (url) window.location.href = url;
});

// DELETE confirmation
$(".form-alert").click(function(e){
    e.preventDefault();
    let form_id = $(this).data('id');
    let message = $(this).data('message') || '{{ translate("Are you sure you want to delete this coupon?") }}';

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

// Show success message if exists
@if(session()->has('success'))
    toastr.success("{{ session('success') }}");
@endif

// Show error message if exists
@if(session()->has('error'))
    toastr.error("{{ session('error') }}");
@endif
</script>
@endpush