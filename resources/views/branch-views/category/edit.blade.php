@extends('layouts.branch.app')

@section('title', translate('Update category'))

@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="text-capitalize mb-0 d-flex align-items-center gap-2">
                <img width="20" src="{{asset('assets/admin/img/icons/brand-setup.png')}}" alt="{{ translate('image') }}">
                @if($category->parent_id == 0)
                    {{translate('category_update')}}
                @else
                    {{translate('sub_category_update')}}
                @endif
            </h2>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="{{route('branch.category.update',[$category['id']])}}" method="post" enctype="multipart/form-data" id="category-form">
                    @csrf
                    @method('POST') {{-- Changed from PUT to POST --}}
                    
                    @php($language=\App\Models\BusinessSetting::where('key','language')->first())
                    @php($language = $language->value ?? null)
                    @php($default_lang = 'en')
                    
                    @if($language)
                        @php($default_lang = json_decode($language)[0])
                        <ul class="nav nav-tabs mb-4 max-content">
                            @foreach(json_decode($language) as $lang)
                                <li class="nav-item">
                                    <a class="nav-link lang_link {{$lang == $default_lang? 'active':''}}" href="#"
                                       id="{{$lang}}-link">{{Helpers::get_language_name($lang).'('.strtoupper($lang).')'}}</a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                    
                    <div class="row">
                        <div class="col-12">
                            @if($language)
                                @foreach(json_decode($language) as $lang)
                                    <?php
                                    if (count($category['translations'])) {
                                        $translate = [];
                                        foreach ($category['translations'] as $t) {
                                            if ($t->locale == $lang && $t->key == "name") {
                                                $translate[$lang]['name'] = $t->value;
                                            }
                                        }
                                    }
                                    ?>
                                    <div class="form-group {{$lang != $default_lang ? 'd-none':''}} lang_form"
                                         id="{{$lang}}-form">
                                        <label class="input-label">{{translate('name')}} ({{strtoupper($lang)}})</label>
                                        <input type="text" name="name[]" maxlength="255"
                                               value="{{$lang==$default_lang?$category['name']:($translate[$lang]['name']??'')}}"
                                               class="form-control" oninvalid="document.getElementById('en-link').click()"
                                               placeholder="{{ translate('New Category') }}" {{$lang == $default_lang? 'required':''}}>
                                    </div>
                                    <input type="hidden" name="lang[]" value="{{$lang}}">
                                @endforeach
                            @else
                                <div class="form-group lang_form" id="{{$default_lang}}-form">
                                    <label class="input-label">{{translate('name')}} ({{strtoupper($default_lang)}})</label>
                                    <input type="text" name="name[]" value="{{$category['name']}}"
                                           class="form-control" placeholder="{{ translate('New Category') }}" required>
                                </div>
                                <input type="hidden" name="lang[]" value="{{$default_lang}}">
                            @endif
                            
                            <input name="position" value="0" type="hidden">
                        </div>
                        
                        @if($category->parent_id == 0)
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="mb-2">{{translate('Image')}}</label>
                                    <div class="branch-upload-area ratio-1-1" id="image-upload-area" style="max-width: 200px;">
                                        <input type="file" name="image" id="category-image" class="branch-file-input" accept=".jpg,.png,.jpeg,.gif,.bmp,.tif,.tiff|image/*">
                                        <label for="category-image" class="branch-upload-label">
                                            <img id="category-image-preview" src="{{ $category['image_fullpath'] }}" 
                                                 class="branch-image-preview" alt="{{ translate('category image') }}">
                                            <div class="branch-upload-text">
                                                <span class="d-block">{{ translate('Click to upload') }}</span>
                                            </div>
                                        </label>
                                        <button type="button" class="branch-remove-btn btn btn-danger btn-sm d-none">
                                            <i class="tio-delete"></i>
                                        </button>
                                    </div>
                                    <p class="fs-14 text-muted mb-0">{{ translate('Image format: jpg, png, jpeg | Max: 2MB') }}</p>
                                </div>
                            </div>
                            
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label class="mb-2">{{translate('Banner Image')}}</label>
                                    <div class="branch-upload-area ratio-8-1" id="banner-upload-area" style="max-width: 400px; height: 100px;">
                                        <input type="file" name="banner_image" id="banner-image" class="branch-file-input" accept=".jpg,.png,.jpeg,.gif,.bmp,.tif,.tiff|image/*">
                                        <label for="banner-image" class="branch-upload-label">
                                            <img id="banner-image-preview" src="{{ $category['banner_image_fullpath'] }}" 
                                                 class="branch-image-preview" alt="{{ translate('banner image') }}">
                                            <div class="branch-upload-text">
                                                <span class="d-block">{{ translate('Click to upload') }}</span>
                                            </div>
                                        </label>
                                        <button type="button" class="branch-remove-btn btn btn-danger btn-sm d-none">
                                            <i class="tio-delete"></i>
                                        </button>
                                    </div>
                                    <p class="fs-14 text-muted mb-0">{{ translate('Image format: jpg, png, jpeg | Max: 2MB') }}</p>
                                </div>
                            </div>
                        @else
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="input-label">{{translate('main')}} {{translate('category')}}</label>
                                    <select name="parent_id" class="form-control" required>
                                        @foreach(\App\Models\Category::where(['position'=>0])->get() as $main_category)
                                            <option value="{{$main_category['id']}}" {{ $main_category['id'] == $category['parent_id'] ? 'selected' : ''}}>{{$main_category['name']}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @endif
                    </div>
                    
                    <div class="d-flex justify-content-end gap-3 mt-4">
                        <button type="reset" class="btn btn-secondary">{{translate('reset')}}</button>
                        <button type="submit" class="btn btn-primary" id="submit-btn">{{translate('update')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script_2')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
"use strict";

document.addEventListener('DOMContentLoaded', function() {
    // Initialize image uploaders
    initImageUpload('category-image', 'category-image-preview', 'image-upload-area');
    initImageUpload('banner-image', 'banner-image-preview', 'banner-upload-area');

    // Language tab switching
    if(document.querySelector('.lang_link')) {
        document.querySelectorAll('.lang_link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                document.querySelectorAll('.lang_link').forEach(l => l.classList.remove('active'));
                this.classList.add('active');
                
                const lang = this.id.split('-')[0];
                document.querySelectorAll('.lang_form').forEach(form => form.classList.add('d-none'));
                document.getElementById(`${lang}-form`).classList.remove('d-none');
            });
        });
    }

    // Form submission handler
    document.getElementById('category-form').addEventListener('submit', function(e) {
        const submitBtn = document.getElementById('submit-btn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> {{ translate("Updating") }}';
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
            preview.src = '{{ $category->image_fullpath }}';
            area.classList.remove('has-image');
            this.classList.add('d-none');
        });
    }
}

@if(Session::has('success'))
    toastr.success("{{ Session::get('success') }}");
@endif
</script>

<style>
/* Upload Area Styling - Compact Version */
.branch-upload-area {
    border: 2px dashed #ddd;
    border-radius: 8px;
    padding: 10px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s;
    position: relative;
    overflow: hidden;
    background-color: #f9f9f9;
    margin-bottom: 10px;
}

.branch-upload-area.ratio-1-1 {
    width: 300px;
    height: 300px;
}

.branch-upload-area.ratio-8-1 {
    width: 100%;
    height: 100px;
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
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 100%;
    cursor: pointer;
    margin: 0;
}

.branch-image-preview {
    max-width: 100%;
    max-height: 100%;
    display: block;
    transition: all 0.3s;
    border: 1px solid #eee;
    border-radius: 4px;
    background: white;
}

.branch-upload-text {
    color: #666;
    font-size: 12px;
    margin-top: 5px;
}

.branch-upload-area.has-image .branch-upload-text {
    display: none;
}

.branch-remove-btn {
    position: absolute;
    top: 5px;
    right: 5px;
    z-index: 10;
    opacity: 0.8;
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
}

.branch-remove-btn:hover {
    opacity: 1;
}

/* Responsive adjustments */
@media (max-width: 767px) {
    .branch-upload-area.ratio-1-1 {
        width: 200px;
        height: 250px;
    }
    
    .branch-upload-area.ratio-8-1 {
        height: 120px;
    }
}
</style>
@endpush