@extends('layouts.branch.app')

@section('title', translate('Banner Management'))

@section('content')
    <div class="content container-fluid">
        <!-- Add New Banner Form Section -->
        <div class="mb-3">
            <h2 class="text-capitalize mb-0 d-flex align-items-center gap-2">
                <img width="24" src="{{asset('assets/admin/img/icons/banner.png')}}" alt="{{ translate('banner') }}">
                {{translate('add_new_banner')}}
            </h2>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <form action="{{route('branch.banner.store')}}" method="post" enctype="multipart/form-data" id="banner-form">
                    @csrf
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="mb-4">
                                <label class="input-label">{{translate('title')}}</label>
                                <input type="text" name="title" class="form-control" placeholder="{{ translate('New banner') }}" required maxlength="255">
                            </div>
                            <div class="mb-4">
                                <label class="input-label">{{translate('Banner')}} {{translate('type')}}<span class="input-label-secondary text-danger">*</span></label>
                                <select name="banner_type" class="form-control" id="banner_type">
                                    <option value="primary">{{translate('Primary Banner')}}</option>
                                    <option value="secondary">{{translate('Secondary Banner')}}</option>
                                </select>
                            </div>
                            <div class="mb-4">
                                <label class="input-label">{{translate('Redirection')}} {{translate('type')}}<span class="input-label-secondary text-danger">*</span></label>
                                <select name="item_type" class="form-control" id="redirection_type">
                                    <option value="product">{{translate('product')}}</option>
                                    <option value="category">{{translate('category')}}</option>
                                </select>
                            </div>
                            <div class="mb-4 type-product" id="type-product">
                                <label class="input-label">{{translate('product')}}<span class="input-label-secondary text-danger">*</span></label>
                                <select name="product_id" class="form-control js-select2-custom">
                                    @foreach($products as $product)
                                        <option value="{{$product['id']}}">{{$product['name']}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-4 d-none type-category" id="type-category">
                                <label class="input-label">{{translate('category')}}<span class="input-label-secondary text-danger">*</span></label>
                                <select name="category_id" class="form-control js-select2-custom">
                                    @foreach($categories as $category)
                                        <option value="{{$category['id']}}">{{$category['name']}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group" id="primary_banner">
                                <label class="mb-2">{{translate('Image')}} <span class="text-danger">*</span></label>
                                <div class="branch-upload-area ratio-2-1" id="primary-upload-area">
                                    <input type="file" name="primary_image" id="primary-image" class="branch-file-input" accept=".jpg,.png,.jpeg,.gif,.bmp,.tif,.tiff|image/*" required>
                                    <label for="primary-image" class="branch-upload-label">
                                        <img id="primary-image-preview" src="{{ asset('assets/admin/img/icons/upload_img.png') }}" 
                                             class="branch-image-preview" alt="{{ translate('banner preview') }}">
                                        <div class="branch-upload-text">
                                            <h3>{{ translate('Drag & Drop here') }}</h3>
                                            <p>{{ translate('or click to browse') }}</p>
                                        </div>
                                    </label>
                                    <button type="button" class="branch-remove-btn btn btn-danger btn-sm d-none">
                                        <i class="tio-delete"></i>
                                    </button>
                                </div>
                                <p class="fs-16 mb-2 text-dark mt-2">
                                    <i class="tio-info-outlined cursor-pointer" data-toggle="tooltip"
                                       title="{{ translate('When do not have secondary banner than the primary banner ration will be 3:1') }}">
                                    </i>
                                    {{ translate('Images Ratio') }} 2:1
                                </p>
                                <p class="fs-14 text-muted mb-0">{{ translate('Image format : jpg, png, jpeg | Maximum Size') }} : 2 MB</p>
                            </div>

                            <div class="form-group d-none" id="secondary_banner">
                                <label class="mb-2">{{translate('Image')}}</label>
                                <div class="branch-upload-area ratio-1-1" id="secondary-upload-area">
                                    <input type="file" name="secondary_image" id="secondary-image" class="branch-file-input" accept=".jpg,.png,.jpeg,.gif,.bmp,.tif,.tiff|image/*">
                                    <label for="secondary-image" class="branch-upload-label">
                                        <img id="secondary-image-preview" src="{{ asset('assets/admin/img/icons/upload_img.png') }}" 
                                             class="branch-image-preview" alt="{{ translate('banner preview') }}">
                                        <div class="branch-upload-text">
                                            <h3>{{ translate('Drag & Drop here') }}</h3>
                                            <p>{{ translate('or click to browse') }}</p>
                                        </div>
                                    </label>
                                    <button type="button" class="branch-remove-btn btn btn-danger btn-sm d-none">
                                        <i class="tio-delete"></i>
                                    </button>
                                </div>
                                <p class="fs-16 mb-2 text-dark mt-2">{{ translate('Images Ratio') }} 1:1</p>
                                <p class="fs-14 text-muted mb-0">{{ translate('Image format : jpg, png, jpeg | Maximum Size') }} : 2 MB</p>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-3">
                        <button type="reset" class="btn btn-secondary px-5">{{translate('reset')}}</button>
                        <button type="submit" class="btn btn-primary px-5" id="submit-btn">{{translate('submit')}}</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Banner List Table Section -->
        <div class="card">
            <div class="px-20 py-3">
                <div class="row gy-2 align-items-center">
                    <div class="col-sm-4">
                        <h5 class="text-capitalize d-flex align-items-center gap-2 mb-0">
                            {{translate('banner_table')}}
                            <span class="badge badge-soft-dark rounded-50 fz-12">{{ $banners->total() }}</span>
                        </h5>
                    </div>
                    <div class="col-sm-8">
                        <div class="d-flex flex-wrap justify-content-sm-end gap-2">
                            <form action="{{url()->current()}}" method="GET">
                                <div class="input-group">
                                    <input id="datatableSearch_" type="search" name="search"
                                        class="form-control"
                                        placeholder="{{translate('Search by Title')}}" aria-label="Search"
                                           value="{{$search}}" required autocomplete="off">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-primary">{{translate('search')}}
                                        </button>
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
                            <th>{{translate('SL')}}</th>
                            <th>{{translate('banner_image')}}</th>
                            <th>{{translate('title')}}</th>
                            <th>{{translate('type')}}</th>
                            <th>{{translate('status')}}</th>
                            <th class="text-center">{{translate('action')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($banners as $key=>$banner)
                        <tr>
                            <td>{{$banners->firstItem()+$key}}</td>
                            <td>
                                <div class="banner-img-wrap rounded border">
                                    <img class="img-fluid" src="{{$banner['image_fullpath']}}" alt="{{ translate('banner') }}" style="max-height: 80px;">
                                </div>
                            </td>
                            <td>{{$banner['title']}}</td>
                            <td>{{translate($banner['banner_type'])}}</td>
                            <td>
                                <label class="switcher">
                                    <input type="checkbox" class="switcher_input branch-change-status" 
                                        {{$banner['status']==1? 'checked' : ''}}
                                        data-route="{{route('branch.banner.status',[$banner['id'], $banner->status == 1 ? 0 : 1])}}">
                                    <span class="switcher_control"></span>
                                </label>
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    <a class="btn btn-outline-info square-btn"
                                        href="{{route('branch.banner.edit',[$banner['id']])}}">
                                        <i class="tio tio-edit"></i>
                                    </a>
                                    <a class="btn btn-outline-danger square-btn branch-form-alert" href="javascript:"
                                       data-id="banner-{{$banner['id']}}"
                                       data-message="{{translate('Want to delete this banner?')}}">
                                        <i class="tio tio-delete"></i>
                                    </a>
                                </div>
                                <form action="{{route('branch.banner.delete',[$banner['id']])}}"
                                        method="post" id="banner-{{$banner['id']}}">
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
                    {!! $banners->links() !!}
                </div>
            </div>
            @if(count($banners)==0)
                <div class="text-center p-4">
                    <img class="mb-3 width-7rem" src="{{asset('assets/admin/svg/illustrations/sorry.svg')}}" alt="{{ translate('Image Description') }}">
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

document.addEventListener('DOMContentLoaded', function() {
    // Initialize image uploaders
    initImageUpload('primary-image', 'primary-image-preview', 'primary-upload-area');
    initImageUpload('secondary-image', 'secondary-image-preview', 'secondary-upload-area');

    // Banner type toggle
    document.getElementById('banner_type').addEventListener('change', function() {
        if(this.value === 'primary') {
            document.getElementById('primary_banner').classList.remove('d-none');
            document.getElementById('secondary_banner').classList.add('d-none');
            document.querySelector('input[name="primary_image"]').required = true;
            document.querySelector('input[name="secondary_image"]').required = false;
        } else {
            document.getElementById('primary_banner').classList.add('d-none');
            document.getElementById('secondary_banner').classList.remove('d-none');
            document.querySelector('input[name="primary_image"]').required = false;
            document.querySelector('input[name="secondary_image"]').required = true;
        }
    });

    // Redirection type toggle
    document.getElementById('redirection_type').addEventListener('change', function() {
        if(this.value === 'product') {
            document.getElementById('type-product').classList.remove('d-none');
            document.getElementById('type-category').classList.add('d-none');
        } else {
            document.getElementById('type-product').classList.add('d-none');
            document.getElementById('type-category').classList.remove('d-none');
        }
    });

    // Status Change Handler
    document.querySelectorAll('.branch-change-status').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const url = this.dataset.route;
            if(url) window.location.href = url;
        });
    });

    // Delete Confirmation
    document.querySelectorAll('.branch-form-alert').forEach(button => {
        button.addEventListener('click', function() {
            const formId = this.dataset.id;
            const message = this.dataset.message || '{{ translate("Are you sure?") }}';
            
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
                    document.getElementById(formId).submit();
                }
            });
        });
    });

    // Form submission handler
    document.getElementById('banner-form').addEventListener('submit', function(e) {
        const submitBtn = document.getElementById('submit-btn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> {{ translate("Processing") }}';
    });
});

function initImageUpload(inputId, previewId, areaId) {
    const input = document.getElementById(inputId);
    const preview = document.getElementById(previewId);
    const area = document.getElementById(areaId);
    const removeBtn = area.querySelector('.branch-remove-btn');

    // Click handler
    area.querySelector('label').addEventListener('click', function(e) {
        if (e.target.tagName !== 'IMG') {
            input.click();
        }
    });

    // File selection handler
    input.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const file = this.files[0];
            // Validate file size (2MB max)
            if (file.size > 2 * 1024 * 1024) {
                Swal.fire({
                    title: '{{ translate("Error") }}',
                    text: '{{ translate("File size exceeds 2MB limit") }}',
                    icon: 'error'
                });
                this.value = '';
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                area.classList.add('has-image');
                if(removeBtn) removeBtn.classList.remove('d-none');
            };
            reader.readAsDataURL(file);
        }
    });

    // Remove button handler
    if(removeBtn) {
        removeBtn.addEventListener('click', function() {
            input.value = '';
            preview.src = '{{ asset("assets/admin/img/icons/upload_img.png") }}';
            area.classList.remove('has-image');
            this.classList.add('d-none');
        });
    }

    // Drag & drop handlers
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        area.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        area.addEventListener(eventName, highlight, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        area.addEventListener(eventName, unhighlight, false);
    });

    function highlight() {
        area.classList.add('highlight');
    }

    function unhighlight() {
        area.classList.remove('highlight');
    }

    area.addEventListener('drop', handleDrop, false);

    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        if (files.length) {
            input.files = files;
            const event = new Event('change');
            input.dispatchEvent(event);
        }
    }
}

@if(Session::has('success'))
    toastr.success("{{ Session::get('success') }}");
@endif
</script>

<style>
/* Upload Area Styling */
.branch-upload-area {
    border: 2px dashed #ddd;
    border-radius: 8px;
    padding: 15px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s;
    position: relative;
    overflow: hidden;
    background-color: #f9f9f9;
    margin-bottom: 15px;
}

.branch-upload-area.highlight {
    border-color: #2962FF;
    background-color: #f0f7ff;
}

.branch-upload-area.has-image {
    padding: 5px;
    border-style: solid;
    border-color: #2962FF;
}

.branch-upload-label {
    display: block;
    width: 100%;
    height: 100%;
    cursor: pointer;
    margin: 0;
}

.branch-image-preview {
    max-width: 100%;
    max-height: 150px;
    display: block;
    margin: 0 auto 10px;
    transition: all 0.3s;
    border: 1px solid #eee;
    border-radius: 4px;
    padding: 3px;
    background: white;
}

.branch-upload-area.has-image .branch-image-preview {
    margin-bottom: 0;
}

.branch-upload-text {
    color: #666;
    transition: all 0.3s;
}

.branch-upload-area.has-image .branch-upload-text {
    opacity: 0;
    height: 0;
    margin: 0;
    padding: 0;
}

.branch-remove-btn {
    position: absolute;
    top: 5px;
    right: 5px;
    z-index: 10;
    opacity: 0.8;
}

.branch-remove-btn:hover {
    opacity: 1;
}

/* Aspect ratio classes */
.ratio-2-1 {
    aspect-ratio: 2/1;
}

.ratio-1-1 {
    aspect-ratio: 1/1;
}

/* Table image styling */
.banner-img-wrap {
    width: 120px;
    height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f5f5f5;
    padding: 5px;
    margin: 0 auto;
}

/* Responsive adjustments */
@media (max-width: 767px) {
    .branch-upload-area {
        padding: 10px;
    }
    
    .branch-upload-text h3 {
        font-size: 16px;
    }
    
    .branch-image-preview {
        max-height: 120px;
    }
}
</style>
@endpush