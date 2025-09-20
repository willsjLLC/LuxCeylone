@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <form id="categoryForm" action="{{ route('admin.productCategories.store', @$category->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-image-uploader name="image" :imagePath="getImage(getFilePath('productCategory') . '/' . @$category->image_url, getFileSize('productCategory'))" :size="false" class="w-100" id="categoryImageUpload" :required="false" />
                                </div>
                            </div>
                            <div class="col-md-9">
                                <div class="form-group">
                                    <label> @lang('Name')</label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name', @$category->name) }}" required>
                                </div>
                                <div class="form-group">
                                    <label> @lang('Description')</label>
                                    <!-- Normal textarea -->
                                    <textarea id="description" name="description" class="form-control" cols="30" rows="10">{{ old('description', @$category->description) }}</textarea>
                                </div>
                            </div>
                            <button type="submit" class="btn btn--primary w-100 h-45">
                                @lang('Submit')
                            </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('admin.productCategories.index') }}" />
@endpush

@push('script')
    <!-- Include nicEdit -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/nicedit/0.9/nicEdit.min.js"></script>

    <script>
        bkLib.onDomLoaded(function() {
            new nicEditor({ fullPanel: true }).panelInstance('description'); // Activate nicEdit

            document.getElementById('categoryForm').addEventListener('submit', function(event) {
                let nicInstance = nicEditors.findEditor('description');
                let content = nicInstance.getContent(); // Get nicEdit content

                if (!content.trim()) {
                    alert('Description is required!');
                    event.preventDefault(); // Stop form submission if empty
                } else {
                    document.getElementById('description').value = content; // Set the textarea value before submitting
                }
            });
        });
    </script>
@endpush

@push('style')
    <style>
        .profilePicUpload {
            margin-top: -20px;
        }
    </style>
@endpush
