@extends('layouts.branch.app')

@section('title', translate('Add new notification'))

@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="text-capitalize mb-0 d-flex align-items-center gap-2">
                <img width="20" src="{{ asset('assets/admin/img/icons/notification.png') }}"
                    alt="{{ translate('notification') }}">
                {{ translate('notification') }}
            </h2>
        </div>

        {{-- Notification Form --}}
        <div class="card">
            <div class="card-body">
                <form action="{{ route('branch.notification.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="input-label">{{ translate('title') }}</label>
                                <input type="text" name="title" class="form-control"
                                    placeholder="{{ translate('New notification') }}" required maxlength="255">
                            </div>
                            <div class="form-group">
                                <label class="input-label">{{ translate('description') }}</label>
                                <textarea name="description" class="form-control" required maxlength="255"></textarea>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <div class="d-flex align-items-center justify-content-center gap-1">
                                    <label class="mb-0">{{ translate('Image') }}</label>
                                    <small class="text-danger">* ( {{ translate('ratio') }} 1:1 )</small>
                                </div>
                                <div class="d-flex justify-content-center mt-4">
                                    <div class="upload-file">
                                        <input type="file" name="image" id="customFileEg1"
                                            accept=".jpg,.png,.jpeg,.gif,.bmp,.tif,.tiff|image/*" class="upload-file__input"
                                            required>
                                        <div class="upload-file__img">
                                            <img width="150" id="viewer"
                                                src="{{ asset('assets/admin/img/icons/upload_img.png') }}"
                                                alt="{{ translate('notification') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-3">
                        <button type="reset" class="btn btn-secondary">{{ translate('reset') }}</button>
                        <button type="{{ env('APP_MODE') != 'demo' ? 'submit' : 'button' }}"
                            class="btn btn-primary demo-form-submit">{{ translate('send_notification') }}</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Notification Table --}}
        <div class="card mt-3">
            <div class="px-20 py-3">
                <div class="row gy-2 align-items-center">
                    <div class="col-sm-4">
                        <h5 class="text-capitalize d-flex align-items-center gap-2 mb-0">
                            {{ translate('notification_table') }}
                            <span class="badge badge-soft-dark rounded-50 fz-12">{{ $notifications->total() }}</span>
                        </h5>
                    </div>
                    <div class="col-sm-8">
                        <div class="d-flex flex-wrap justify-content-sm-end gap-2">
                            <form action="#" method="GET">
                                <div class="input-group">
                                    <input type="search" name="search" class="form-control"
                                        placeholder="{{ translate('Search by Title') }}" aria-label="Search"
                                        value="{{ $search ?? '' }}" required autocomplete="off">
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
                            <th>{{ translate('image') }}</th>
                            <th>{{ translate('title') }}</th>
                            <th>{{ translate('description') }}</th>
                            <th>{{ translate('status') }}</th>
                            <th class="text-center">{{ translate('action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($notifications as $key => $notification)
                            <tr>
                                <td>{{ $notifications->firstItem() + $key }}</td>
                                <td>
                                    @php
                                        // Use the image_url accessor from the model
                                        $imageUrl = $notification->image_url ?? asset('assets/admin/img/icons/upload_img.png');
                                    @endphp

                                    <div class="avatar-lg border rounded">
                                        <img class="img-fit rounded" src="{{ $imageUrl }}"
                                            onerror="this.src='{{ asset('assets/admin/img/icons/upload_img.png') }}'"
                                            alt="{{ translate('image') }}">
                                    </div>
                                </td>
                                <td>{{ Str::limit($notification->title, 25) }}</td>
                                <td>{{ Str::limit($notification->description, 25) }}</td>
                                <td>
                                    <label class="switcher">
                                        <input type="checkbox" class="switcher_input change-status"
                                            data-route="{{ route('branch.notification.status', [$notification->id, $notification->status == 1 ? 0 : 1]) }}"
                                            {{ $notification->status == 1 ? 'checked' : '' }}>
                                        <span class="switcher_control"></span>
                                    </label>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        <a class="btn btn-outline-info square-btn"
                                            href="{{ route('branch.notification.edit', [$notification->id]) }}">
                                            <i class="tio tio-edit"></i>
                                        </a>
                                        <a class="btn btn-outline-danger square-btn form-alert" href="javascript:"
                                            data-id="notification-{{ $notification->id }}"
                                            data-message="{{ translate('Want to delete this notification?') }}">
                                            <i class="tio tio-delete"></i>
                                        </a>
                                    </div>
                                    <form action="{{ route('branch.notification.delete', [$notification->id]) }}" method="post"
                                        id="notification-{{ $notification->id }}">
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
                    {!! $notifications->links() !!}
                </div>
            </div>

            @if(count($notifications) == 0)
                <div class="text-center p-4">
                    <img class="mb-3 width-7rem" src="{{ asset('assets/admin/svg/illustrations/sorry.svg') }}"
                        alt="{{ translate('image') }}">
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

        // STATUS toggle
        $(document).on('change', '.change-status', function () {
            let url = $(this).data('route');
            if (url) window.location.href = url;
        });

        // DELETE confirmation using SweetAlert2
        $(document).on('click', '.form-alert', function () {
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

        // IMAGE preview for notification upload
        $(document).on('change', '#customFileEg1', function () {
            let input = this;
            if (input.files && input.files[0]) {
                let reader = new FileReader();
                reader.onload = function (e) {
                    $('#viewer').attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
            } else {
                $('#viewer').attr('src', '{{ asset("assets/admin/img/icons/upload_img.png") }}');
            }
        });
    </script>
@endpush