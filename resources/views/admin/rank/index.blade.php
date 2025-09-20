@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Name')</th>
                                    <th class="text-start">@lang('Rank')</th>
                                    <th class="text-start">@lang('Stars')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($ranks as $rankDetail)
                                    <tr>
                                        <td>
                                            <div class="user">
                                                <div class="thumb">
                                                    <img src="{{ getImage(getFilePath('rank') . '/' . $rankDetail->image, getFileSize('rank')) }}"
                                                        class="plugin_bg">
                                                </div>
                                                <span class="name">{{ __($rankDetail->name) }}</span>
                                            </div>
                                        </td>

                                         <td class="text-start">
                                                <span class="name">{{ ($rankDetail->rank) }}</span>
                                        </td>

                                        <td class="text-start">
                                            @for ($i = 0; $i < $rankDetail->no_of_stars; $i++)
                                                <i class="fas fa-star text-warning me-2"></i>
                                            @endfor
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2 flex-wrap justify-content-end">
                                                <button type="button"
                                                    class="btn btn-sm btn-outline--danger confirmationBtn"
                                                    data-action="{{ route('admin.ranks.delete', $rankDetail->id) }}"
                                                    data-question="@lang('Are you sure to disable this rank type?')">
                                                    @lang('Delete')
                                                </button>
                                                <button class="btn btn-outline--primary btn-sm updateFile"
                                                    data-rank="{{ $rankDetail }}"
                                                    data-image-url="{{ getImage(getFilePath('rank') . '/' . $rankDetail->image, getFileSize('rank')) }}">
                                                    <i class="las la-pen"></i>
                                                    @lang('Edit')
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($ranks->hasPages())
                    <div class="card-footer py-4">
                        @php echo paginateLinks($ranks) @endphp
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Updated Modal -->
    <div class="modal fade addFileType" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"></h4>
                    <button type="button" class="close" data-bs-dismiss="modal">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form class="resetFormData" method="post" action="{{ route('admin.ranks.store') }}"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">

                        <div class="form-group">
                            <label>@lang('Image')</label>
                            <div id="imageUploaderContainer">
                                <div class="image-upload-wrapper">
                                    <!-- Image Preview -->
                                    <div class="image-preview-container mb-3" id="imagePreviewContainer"
                                        style="display: none;">
                                        <img id="imagePreview" src="" alt="Image Preview"
                                            style="max-width: 200px; max-height: 200px; border: 1px solid #ddd; border-radius: 8px; object-fit: cover;">
                                        <br>
                                        <button type="button" class="btn btn-sm btn-danger mt-2" id="removeImageBtn">
                                            <i class="las la-times"></i> Remove Image
                                        </button>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="file-input-container">
                            <input type="file" name="image" id="imageInput" class="form-control" accept="image/*">
                            <small class="text-muted mt-1">Select an image file (JPG, PNG, GIF)</small>
                        </div>

                        <div class="form-group">
                            <label>@lang('Rank')</label>
                            <div class="col-sm-12">
                                <input type="number" min="1" class="form-control" name="rank" id="rankNumber"
                                    required placeholder="1">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>@lang('Name')</label>
                            <div class="col-sm-12">
                                <input type="text" class="form-control" name="name" required placeholder="One Star">
                            </div>
                        </div>

                        <div class="form-group" >
                            <label>@lang('Number Of Stars')</label>
                            <div class="col-sm-12">
                                <input type="number" class="form-control" name="no_of_stars" min="1" required>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary h-45 w-100">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .image-upload-wrapper {
            border: 2px dashed #ddd;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            transition: border-color 0.3s ease;
        }

        .image-upload-wrapper:hover {
            border-color: #007bff;
        }

        .image-upload-wrapper.has-image {
            border-color: #28a745;
            background-color: #f8f9fa;
        }

        .image-preview-container {
            text-align: center;
        }

        #imagePreview {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .file-input-container {
            margin-top: 10px;
        }
    </style>
    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <x-search-form placeholder="File type name" />
    <button class="btn btn-outline--primary h-45 addfile">
        <i class="las la-plus"></i>@lang('Add New')
    </button>
@endpush

@push('script')
    <script>
        (function($) {

            function initializeImageUpload() {
                const imageInput = $('#imageInput');
                const imagePreview = $('#imagePreview');
                const imagePreviewContainer = $('#imagePreviewContainer');
                const removeImageBtn = $('#removeImageBtn');
                const uploadWrapper = $('.image-upload-wrapper');

                imageInput.on('change', function(e) {
                    const file = e.target.files[0];

                    if (file) {

                        if (!file.type.match('image.*')) {
                            alert('Please select a valid image file.');
                            $(this).val('');
                            return;
                        }

                        if (file.size > 5 * 1024 * 1024) {
                            alert('Please select an image smaller than 5MB.');
                            $(this).val('');
                            return;
                        }

                        const reader = new FileReader();
                        reader.onload = function(e) {
                            imagePreview.attr('src', e.target.result);
                            imagePreviewContainer.show();
                            uploadWrapper.addClass('has-image');
                        };
                        reader.readAsDataURL(file);
                    }
                });

                removeImageBtn.on('click', function() {
                    imageInput.val('');
                    imagePreview.attr('src', '');
                    imagePreviewContainer.hide();
                    uploadWrapper.removeClass('has-image');
                });
            }


            function resetImageUploader() {
                const imageInput = $('#imageInput');
                const imagePreview = $('#imagePreview');
                const imagePreviewContainer = $('#imagePreviewContainer');
                const uploadWrapper = $('.image-upload-wrapper');
                imageInput.val('');
                imagePreview.attr('src', '');
                imagePreviewContainer.hide();
                uploadWrapper.removeClass('has-image');
            }

            function loadExistingImage(imageUrl) {
                const imagePreview = $('#imagePreview');
                const imagePreviewContainer = $('#imagePreviewContainer');
                const uploadWrapper = $('.image-upload-wrapper');

                if (imageUrl && imageUrl !== '') {
                    imagePreview.attr('src', imageUrl);
                    imagePreviewContainer.show();
                    uploadWrapper.addClass('has-image');
                } else {
                    resetImageUploader();
                }
            }

            $('.addfile').on('click', function() {
                let modal = $('.addFileType');
                let title = "@lang('Add New Rank')";
                let action = "{{ route('admin.ranks.store') }}";
                $('.resetFormData')[0].reset();
                resetImageUploader();
                modal.find('form').attr('action', action);
                modal.find('.modal-title').text(title);
                modal.modal('show');
            });

            $('.updateFile').on('click', function() {
                let modal = $('.addFileType');
                let title = "@lang('Update Rank')";
                let rank = $(this).data('rank');
                let imageUrl = $(this).data('image-url');
                let action = "{{ route('admin.ranks.store', ':id') }}";
                modal.find('form').attr('action', action.replace(':id', rank.id));
                modal.find('.modal-title').text(title);
                modal.find('input[name=rank]').val(rank.rank);
                modal.find('input[name=name]').val(rank.name);
                modal.find('input[name=no_of_stars]').val(rank.no_of_stars);
                loadExistingImage(imageUrl);
                modal.modal('show');
            });

            $(document).ready(function() {
                initializeImageUpload();
            });

            $('.addFileType').on('shown.bs.modal', function() {
                initializeImageUpload();
            });

        })(jQuery);
    </script>
@endpush
