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
                                    <th>@lang('Image')</th>
                                    <th class="text-start">@lang('Rank')</th>
                                    <th class="text-start">@lang('Reward')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($rankRewards as $rankReward)
                                    <tr>
                                        <td>
                                            <div class="reward-image">
                                                <img src="{{ getImage(getFilePath('rankReward') . '/' . $rankReward->image, getFileSize('rankReward')) }}"
                                                    class="b-radius--10 withdraw-detailImage">
                                            </div>
                                        </td>

                                        <td class="text-start">
                                            <span class="name">{{ $rankReward->rank->name }}</span>
                                        </td>

                                        <td class="text-start">
                                            <span class="name">{{ $rankReward->reward }}</span>
                                        </td>

                                        <td>
                                            <div class="d-flex gap-2 flex-wrap justify-content-end">
                                                <button type="button"
                                                    class="btn btn-sm btn-outline--danger confirmationBtn"
                                                    data-action="{{ route('admin.ranks.rewards.delete', $rankReward->id) }}"
                                                    data-question="@lang('Are you sure to disable this rank reward ?')">
                                                    @lang('Delete')
                                                </button>
                                                <button class="btn btn-outline--primary btn-sm updateFile"
                                                    data-rank="{{ $rankReward }}"
                                                    data-image-url="{{ getImage(getFilePath('rankReward') . '/' . $rankReward->image, getFileSize('rankReward')) }}">
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
                @if ($rankRewards->hasPages())
                    <div class="card-footer py-4">
                        @php echo paginateLinks($rankRewards) @endphp
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Updated Modal -->
    <div class="modal fade addFileType w-100" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"></h4>
                    <button type="button" class="close" data-bs-dismiss="modal">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form class="resetFormData" method="post" action="{{ route('admin.ranks.rewards.store') }}"
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
                            <select class="form-control select2" name="rank_id" required>
                                <option value="">@lang('Select one')</option>
                                @foreach ($ranks as $rank)
                                    <option value="{{ $rank->id }}" @selected(old('rank_id', @$rankRequirement->rank_id) == $rank->id)>
                                        {{ __($rank->name) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>@lang('Reward')</label>
                            <textarea id="reward" name="reward" class="form-control" cols="30" rows="5">{{ old('reward', @$product->reward) }}</textarea>
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

        .reward-image {
            width: 80px;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border-radius: 8px;
            background-color: #f9f9f9;
        }

        .reward-image img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
    </style>
    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <x-search-form placeholder="Rank Rewards" />
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

                imageInput.off('change').on('change', function(e) {
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

                removeImageBtn.off('click').on('click', function() {
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

                if (imageUrl && imageUrl !== '{{ getImage(getFilePath('rankReward')) . '/' }}') {
                    imagePreview.attr('src', imageUrl);
                    imagePreviewContainer.show();
                    uploadWrapper.addClass('has-image');
                } else {
                    resetImageUploader();
                }
            }

            // Add New Button
            $('.addfile').on('click', function() {
                let modal = $('.addFileType');
                let title = "@lang('Add New Rank Reward')";
                let action = "{{ route('admin.ranks.rewards.store') }}";

                // Reset form
                modal.find('form')[0].reset();
                resetImageUploader();

                // Clear reward textarea
                modal.find('#reward').val('');

                // Reset rank dropdown
                modal.find('select[name="rank_id"]').val('').trigger('change');

                // Set form action and title
                modal.find('form').attr('action', action);
                modal.find('.modal-title').text(title);
                modal.modal('show');
            });

            // Edit Button - FIXED VERSION
            $('.updateFile').on('click', function() {
                let modal = $('.addFileType');
                let title = "@lang('Update Rank Reward')";
                let rankReward = $(this).data('rank');
                let imageUrl = $(this).data('image-url');

                console.log('Edit data:', rankReward); // Debug line - remove in production

                // Set form action for update
                let action = "{{ route('admin.ranks.rewards.store', ':id') }}";
                modal.find('form').attr('action', action.replace(':id', rankReward.id));
                modal.find('.modal-title').text(title);

                // Set the rank dropdown
                modal.find('select[name="rank_id"]').val(rankReward.rank_id).trigger('change');

                // Set the reward textarea - THIS WAS THE MISSING PART!
                modal.find('#reward').val(rankReward.reward);

                // Load existing image
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
