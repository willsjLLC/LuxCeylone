@extends('admin.layouts.app')
@section('panel')
    <div class="container">
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        <div>
            <div class="card p-5">
                <div class="col-md-12">
                    <form id="imageUploadForm" action="{{ route('admin.productPromotionBanners.store') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf

                        <x-image-uploader name="image" :imagePath="getImage(
                            getFilePath('promotionalBanner') . '/' . @$promotional_banner->image,
                            getFileSize('promotionalBanner'),
                        )" :size="false" class="w-100"
                            id="productImageUpload" :required="!isset($promotional_banner) || !$promotional_banner->image"
                            value="{{ old('image', @$promotional_banner->image) }}" />

                        <div class="row mt-4">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>@lang('Title')</label>
                                    <input type="text" name="title" class="form-control" placeholder="Black Friday"
                                        value="{{ old('title', @$promotional_banner->title) }}">

                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="status">@lang('Status')</label>
                                    <select class="form-control select2" name="status" id="status" required>
                                        <option value="">@lang('Select one')</option>
                                        <option value="1" @selected(old('status', @$promotional_banner->status) == 1)>@lang('Active')</option>
                                        <option value="0" @selected(old('status', @$promotional_banner->status) == 0)>@lang('Inactive')</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>@lang('Description')</label>
                                    <textarea id="description" name="description" class="form-control" cols="30" rows="5">{{ old('description', @$promotional_banner->description) }}</textarea>
                                </div>
                            </div>

                            <div class="col-md-12 text-end">
                                <div class="form-group mt-4">
                                    <button type="submit" class="btn btn-outline--primary w-100">Save</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('script')
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/nicedit/0.9/nicEdit.min.js"></script> --}}
    {{-- <script>
        bkLib.onDomLoaded(function() {
            new nicEditor({
                fullPanel: true
            }).panelInstance('description');

            document.getElementById('imageUploadForm').addEventListener('submit', function(event) {
                let nicInstance = nicEditors.findEditor('description');
                let content = nicInstance.getContent();

                if (!content.trim()) {
                    alert('Description is required!');
                    event.preventDefault();
                    return false;
                } else {
                    document.getElementById('description').value = content;
                }

            });

        });
    </script> --}}
@endpush
