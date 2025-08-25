@extends('layouts.branch.app')

@section('title', translate('Update Banner'))

@section('content')
<div class="content container-fluid">
    <div class="mb-3">
        <h2 class="text-capitalize mb-0 d-flex align-items-center gap-2">
            <img width="20" src="{{ asset('assets/admin/img/icons/banner.png') }}" alt="{{ translate('banner') }}">
            {{ translate('update_banner') }}
        </h2>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('branch.banner.update', [$banner['id']]) }}" method="post" enctype="multipart/form-data">
                @csrf
                @method('put')

                <div class="row">
                    <!-- Left Column -->
                    <div class="col-md-6">
                        <div class="mb-5">
                            <label class="input-label">{{ translate('title') }}</label>
                            <input type="text" name="title" value="{{ $banner['title'] }}" class="form-control" placeholder="{{ translate('New banner') }}" required>
                        </div>

                        <div class="mb-5">
                            <label class="input-label">{{ translate('Banner type') }} <span class="text-danger">*</span></label>
                            <select name="banner_type" id="banner_type" class="form-control">
                                <option value="primary" {{ $banner['banner_type'] == 'primary' ? 'selected' : '' }}>{{ translate('Primary Banner') }}</option>
                                <option value="secondary" {{ $banner['banner_type'] == 'secondary' ? 'selected' : '' }}>{{ translate('Secondary Banner') }}</option>
                            </select>
                        </div>

                        <div class="mb-5">
                            <label class="input-label">{{ translate('Redirection') }} <span class="text-danger">*</span></label>
                            <select name="item_type" id="redirection_type" class="form-control">
                                <option value="product" {{ $banner['product_id'] ? 'selected' : '' }}>{{ translate('Product') }}</option>
                                <option value="category" {{ $banner['category_id'] ? 'selected' : '' }}>{{ translate('Category') }}</option>
                            </select>
                        </div>

                        <div class="mb-5 type-product {{ $banner['product_id'] ? 'd-block' : 'd-none' }}" id="type-product">
                            <label class="input-label">{{ translate('Select Product') }} <span class="text-danger">*</span></label>
                            <select name="product_id" class="form-control js-select2-custom">
                                @foreach($products as $product)
                                    <option value="{{ $product['id'] }}" {{ $banner['product_id'] == $product['id'] ? 'selected' : '' }}>{{ $product['name'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-5 type-category {{ $banner['category_id'] ? 'd-block' : 'd-none' }}" id="type-category">
                            <label class="input-label">{{ translate('Select Category') }} <span class="text-danger">*</span></label>
                            <select name="category_id" class="form-control js-select2-custom">
                                @foreach($categories as $category)
                                    <option value="{{ $category['id'] }}" {{ $banner['category_id'] == $category['id'] ? 'selected' : '' }}>{{ $category['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="col-md-6">
                        <!-- Primary Banner -->
                        <div class="form-group {{ $banner['banner_type'] != 'primary' ? 'd-none' : '' }}" id="primary_banner">
                            <label class="mb-2">{{ translate('Primary Image') }}</label>
                            <small class="text-danger d-block mb-2">* ({{ translate('ratio') }} 2:1)</small>
                            
                            <div class="custom_upload_input max-h200px ratio-2">
                                <input type="file" name="primary_image" id="primary_image_input" class="custom-upload-input-file" accept=".jpg,.png,.jpeg,.webp">
                                
                                <div class="img_area_with_preview position-absolute z-index-2">
                                    <img id="primary_image_preview" 
                                         src="{{ $banner->image_fullpath }}" 
                                         class="img-fluid rounded" 
                                         style="max-height:200px; object-fit:contain;" 
                                         alt="{{ translate('banner') }}">
                                </div>
                                
                                @error('primary_image')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Secondary Banner -->
                        <div class="form-group {{ $banner['banner_type'] != 'secondary' ? 'd-none' : '' }}" id="secondary_banner">
                            <label class="mb-2">{{ translate('Secondary Image') }}</label>
                            <small class="text-danger d-block mb-2">* ({{ translate('ratio') }} 1:1)</small>
                            
                            <div class="custom_upload_input max-h200px ratio-1">
                                <input type="file" name="secondary_image" id="secondary_image_input" class="custom-upload-input-file" accept=".jpg,.png,.jpeg,.webp">
                                
                                <div class="img_area_with_preview position-absolute z-index-2">
                                    <img id="secondary_image_preview" 
                                         src="{{ $banner->image_fullpath }}" 
                                         class="img-fluid rounded" 
                                         style="max-height:200px; object-fit:contain;" 
                                         alt="{{ translate('banner') }}">
                                </div>
                                
                                @error('secondary_image')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-3 mt-4">
                    <button type="reset" class="btn btn-secondary px-5">{{ translate('reset') }}</button>
                    <button type="submit" class="btn btn-primary px-5">{{ translate('update') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('script_2')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle image preview for both banner types
    function previewImage(inputId, previewId) {
        const input = document.getElementById(inputId);
        const preview = document.getElementById(previewId);

        if (input && preview) {
            input.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    reader.onload = (e) => preview.src = e.target.result;
                    reader.readAsDataURL(this.files[0]);
                }
            });
        }
    }

    previewImage('primary_image_input', 'primary_image_preview');
    previewImage('secondary_image_input', 'secondary_image_preview');

    // Toggle primary/secondary banner sections
    document.getElementById('banner_type').addEventListener('change', function() {
        document.getElementById('primary_banner').classList.toggle('d-none', this.value !== 'primary');
        document.getElementById('secondary_banner').classList.toggle('d-none', this.value !== 'secondary');
    });

    // Toggle product/category select
    document.getElementById('redirection_type').addEventListener('change', function() {
        document.getElementById('type-product').classList.toggle('d-none', this.value !== 'product');
        document.getElementById('type-category').classList.toggle('d-none', this.value !== 'category');
    });
});
</script>
@endpush
