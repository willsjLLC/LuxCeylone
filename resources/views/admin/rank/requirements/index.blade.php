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
                                    <th>@lang('Rank')</th>
                                    <th class="text-start">@lang('Minimum Required Rank')</th>
                                    <th class="text-end">@lang('No Of Users Require In Level One')</th>
                                    <th class="text-end">@lang('No Of Users Require In Level Two')</th>
                                    <th class="text-end">@lang('No Of Users Require In Level Three')</th>
                                    <th class="text-end">@lang('No Of Users Require In Level Four')</th>
                                    <th class="text-end">@lang('At Least One Product Purchase')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($rankRequirements as $rankDetail)
                                    <tr>
                                        <td>
                                            <div class="user">
                                                <div class="thumb">
                                                    <img src="{{ getImage(getFilePath('rank') . '/' . $rankDetail->rank->image, getFileSize('rank')) }}"
                                                        class="plugin_bg">
                                                </div>
                                                <span class="name">{{ __($rankDetail->rank->name) }}</span>
                                            </div>
                                        </td>

                                        <td class="text-start">
                                            <span class="name">{{ $rankDetail->minRank?->name ?? 'Not Required' }}</span>
                                        </td>

                                        <td class="text-end">
                                            <span class="name">{{ $rankDetail->level_one_user_count }}</span>
                                        </td>

                                        <td class="text-end">
                                            <span class="name">{{ $rankDetail->level_two_user_count }}</span>
                                        </td>

                                        <td class="text-end">
                                            <span class="name">{{ $rankDetail->level_three_user_count }}</span>
                                        </td>

                                        <td class="text-end">
                                            <span class="name">{{ $rankDetail->level_four_user_count }}</span>
                                        </td>

                                        <td class="text-center">
                                            <span
                                                class="name">{{ $rankDetail->required_at_least_one_product_purchase == 1 ? 'YES' : 'NO' }}</span>
                                        </td>

                                        <td>
                                            <div class="d-flex gap-2 flex-wrap justify-content-end">
                                                <button type="button"
                                                    class="btn btn-sm btn-outline--danger confirmationBtn"
                                                    data-action="{{ route('admin.ranks.requirements.delete', $rankDetail->id) }}"
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
                @if ($rankRequirements->hasPages())
                    <div class="card-footer py-4">
                        @php echo paginateLinks($ranks) @endphp
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade addFileType" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"></h4>
                    <button type="button" class="close" data-bs-dismiss="modal">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form class="resetFormData" method="post" action="{{ route('admin.ranks.requirements.store') }}"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">

                        <div class="form-group">
                            <label>@lang('Min Required Rank')</label>
                            <select class="form-control select2" name="min_rank_id">
                                <option value="">@lang('Select one')</option>
                                @foreach ($ranks as $rank)
                                    <option value="{{ $rank->id }}" @selected(old('rank_id', @$rankRequirement->rank_id) == $rank->id)>
                                        {{ __($rank->name) }}
                                    </option>
                                @endforeach
                            </select>
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
                            <label>@lang('No Of Users Require In Level One')</label>
                            <div class="col-sm-12">
                                <input type="number" class="form-control" name="level_one_user_count" min="0"
                                    required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>@lang('No Of Users Require In Level Two')</label>
                            <div class="col-sm-12">
                                <input type="number" class="form-control" name="level_two_user_count" min="0"
                                    required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>@lang('No Of Users Require In Level Three')</label>
                            <div class="col-sm-12">
                                <input type="number" class="form-control" name="level_three_user_count" min="0"
                                    required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>@lang('No Of Users Require In Level Four')</label>
                            <div class="col-sm-12">
                                <input type="number" class="form-control" name="level_four_user_count" min="0"
                                    required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>@lang('At Least One Product Purchase')</label>
                            <div
                                class="form-switch border rounded p-3 shadow-sm h-100 d-flex justify-content-between align-items-center">
                                <label for="required_at_least_one_product_purchase" class="form-check-label fw-semibold">

                                </label>
                                <input type="checkbox" data-width="100%" data-height="50" data-onstyle="-success"
                                    data-offstyle="-danger" name="required_at_least_one_product_purchase"
                                    id="required_at_least_one_product_purchase" data-off="@lang('Disabled')"
                                    class="form-check-input" data-bs-toggle="toggle" data-on="@lang('Enable')"
                                    @if(isset($rankDetail))
                                    {{ $rankDetail->required_at_least_one_product_purchase == 1 ? 'checked' : '' }}>
                                    @endif
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
                let title = "@lang('Add New Rank Requirement')";
                let action = "{{ route('admin.ranks.requirements.store') }}";
                $('.resetFormData')[0].reset();

                // Remove the hidden _method field if it exists
                modal.find('input[name="_method"]').remove();

                modal.find('form').attr('action', action);
                modal.find('.modal-title').text(title);
                modal.modal('show');
            });


            $('.updateFile').on('click', function() {
                let modal = $('.addFileType');
                let title = "@lang('Update Rank Requirement')";
                let rank = $(this).data('rank');
                let imageUrl = $(this).data('image-url');
                let action = "{{ route('admin.ranks.requirements.update', ':id') }}";

                // Add the hidden _method field
                if (!modal.find('input[name="_method"]').length) {
                    modal.find('form').prepend('<input type="hidden" name="_method" value="PUT">');
                }

                modal.find('form').attr('action', action.replace(':id', rank.id));
                modal.find('.modal-title').text(title);
                modal.find('select[name=min_rank_id]').val(rank.min_rank_id).trigger('change');
                modal.find('select[name=rank_id]').val(rank.rank_id).trigger('change');
                modal.find('input[name=level_one_user_count]').val(rank.level_one_user_count);
                modal.find('input[name=level_two_user_count]').val(rank.level_two_user_count);
                modal.find('input[name=level_three_user_count]').val(rank.level_three_user_count);
                modal.find('input[name=level_four_user_count]').val(rank.level_four_user_count);
                modal.find('input[name=required_at_least_one_product_purchase]')
                    .prop('checked', rank.required_at_least_one_product_purchase == 1)
                    .change();
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
