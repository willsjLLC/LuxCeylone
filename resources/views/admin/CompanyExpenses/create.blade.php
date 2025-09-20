@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    {{-- Form action conditional on whether $companyExpense exists --}}
                    <form action="{{ route('admin.expenses.store', @$companyExpense->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        {{-- If updating, include a method spoofing for PUT/PATCH --}}
                        @if(@$companyExpense->id)
                            @method('PUT')
                        @endif

                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label> @lang('Date')</label>
                                    <input type="date" name="date" class="form-control"
                                        value="{{ old('date', @$companyExpense->date ? date('Y-m-d', strtotime(@$companyExpense->date)) : date('Y-m-d')) }}" required>
                                </div>
                            </div>
                        </div>

                        <div id="expense-items-container">
                            @if(@$companyExpenseItems && $companyExpenseItems->count() > 0)
                                {{-- Loop through existing items for editing --}}
                                @foreach($companyExpenseItems as $index => $item)
                                    <div class="row expense-item-row border-bottom pb-3 mb-3 align-items-end" data-item-id="{{ $item->id }}">
                                        <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id }}">
                                        
                                        {{-- Image Preview Column --}}
                                        <div class="col-md-1">
                                            <div class="form-group">
                                                {{-- <label> @lang('Preview')</label> --}}
                                                <div class="image-preview-container">
                                                    @if($item->image)
                                                        <img src="{{ getImage(getFilePath('expenseItems') . '/' . $item->image) }}" 
                                                             class="image-thumbnail" 
                                                             data-bs-toggle="modal" 
                                                             data-bs-target="#imageModal"
                                                             data-image-src="{{ getImage(getFilePath('expenseItems') . '/' . $item->image) }}"
                                                             alt="Expense Image">
                                                    @else
                                                        <div class="no-image-placeholder">
                                                            <i class="fa fa-image"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-1">
                                            <div class="form-group">
                                                <label> @lang('Item No.')</label>
                                                {{-- <p class="form-control-static item-number-display">{{ $item->item_no }}</p> --}}
                                                <input type="text" readonly name="items[{{ $index }}][item_no]" class="item-number-hidden-input form-control" value="{{ $item->item_no }}">
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label> @lang('Description')</label>
                                                {{-- <textarea name="items[{{ $index }}][description]" class="form-control" rows="1" required>{{ old('items.'.$index.'.description', $item->description) }}</textarea> --}}
                                                <input type="text" name="items[{{ $index }}][description]" class="form-control" value="{{ old('items.'.$index.'.description', $item->description) }}">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label> @lang('Debit')</label>
                                                <input type="number" step="0.01" name="items[{{ $index }}][debit]" class="form-control item-debit-input" value="{{ old('items.'.$index.'.debit', $item->debit ?? 0.00) }}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label> @lang('Image')</label>
                                                <input type="file" name="items[{{ $index }}][image]" class="form-control image-input" accept="image/*">
                                                {{-- @if($item->image)
                                                    <small class="text-muted mt-1">
                                                        <a href="{{ getImage(getFilePath('expenseItems') . '/' . $item->image) }}" target="_blank">@lang('View Current Image')</a>
                                                    </small>
                                                    <input type="hidden" name="items[{{ $index }}][old_image]" value="{{ $item->image }}">
                                                @endif --}}
                                            </div>
                                        </div>
                                        <div class="col-md-1">
                                            <div class="form-group">
                                                {{-- <label> @lang('Image')</label> --}}
                                                <button type="button" class="btn btn--danger remove-item-btn">
                                                <i class="fa fa-times"></i>
                                            </button>
                                            </div>
                                        </div>
                                        {{-- <div class="col-md-1 d-flex align-items-end">
                                            <button type="button" class="btn btn--danger remove-item-btn">
                                                <i class="fa fa-times"></i>
                                            </button>
                                        </div> --}}
                                    </div>
                                @endforeach
                            @else
                                {{-- Default single item row for creation --}}
                                <div class="row expense-item-row border-bottom pb-3 mb-3 align-items-end">
                                    {{-- Image Preview Column --}}
                                    <div class="col-md-1">
                                        <div class="form-group">
                                            <label> @lang('Preview')</label>
                                            <div class="image-preview-container">
                                                <div class="no-image-placeholder">
                                                    <i class="fa fa-image"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label> @lang('Item No.')</label>
                                            <p class="form-control-static item-number-display">1</p>
                                            <input type="hidden" name="items[0][item_no]" class="item-number-hidden-input" value="1">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label> @lang('Description')</label>
                                            <textarea name="items[0][description]" class="form-control" rows="1" required>{{ old('items.0.description') }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label> @lang('Debit')</label>
                                            <input type="number" step="0.01" name="items[0][debit]" class="form-control item-debit-input" value="{{ old('items.0.debit', '0.00') }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label> @lang('Image')</label>
                                            <input type="file" name="items[0][image]" class="form-control image-input" accept="image/*">
                                        </div>
                                    </div>
                                    <div class="col-md-1 d-flex align-items-end">
                                        <button type="button" class="btn btn--danger remove-item-btn" style="display: none;">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12 text-end">
                                <button type="button" class="btn btn--primary add-item-btn">
                                    <i class="fa fa-plus"></i> @lang('Add New Item')
                                </button>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-12 text-end">
                                <div class="form-group">
                                    <label class="font-weight-bold"> @lang('Total Debit')</label>
                                    <span class="form-control-static total-debit-display" id="total-debit-display">
                                        {{ showAmount(@$companyExpense->total_debit ?? 0.00) }}
                                    </span>
                                    <input type="hidden" name="total_debit" id="total-debit-hidden-input" class="text-dark" value="{{ @$companyExpense->total_debit ?? 0.00 }}">
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit Expenses')</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Image Modal --}}
    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageModalLabel">@lang('Expense Image')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="modalImage" src="" alt="Expense Image" class="img-fluid">
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('admin.expenses.index') }}" />
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";

            // Initialize itemIndex based on existing items if in edit mode, otherwise 0
            let itemIndex = {{ @$companyExpenseItems ? $companyExpenseItems->count() - 1 : 0 }};
            // Array to keep track of IDs of items that are currently on the form (for update/delete logic)
            let existingItemIds = [];
            @if(@$companyExpenseItems)
                @foreach($companyExpenseItems as $item)
                    existingItemIds.push({{ $item->id }});
                @endforeach
            @endif

            // Function to calculate and update total debit
            function calculateTotalDebit() {
                let total = 0;
                $('#expense-items-container').find('.item-debit-input').each(function() {
                    let debitValue = parseFloat($(this).val()) || 0;
                    total += debitValue;
                });
                $('#total-debit-display').text(total.toFixed(2));
                $('#total-debit-hidden-input').val(total.toFixed(2));
            }

            // Function to add a new expense item row
            function addNewExpenseItem() {
                itemIndex++;
                const container = $('#expense-items-container');
                const firstRow = container.find('.expense-item-row').first();
                const newRow = firstRow.clone();

                // Reset values and update names for the new row
                newRow.find('input, textarea').each(function() {
                    const originalName = $(this).attr('name');
                    if (originalName) {
                        $(this).attr('name', originalName.replace(/items\[\d+\]/, `items[${itemIndex}]`));
                    }
                    if ($(this).attr('type') === 'file') {
                        $(this).val('');
                    } else if ($(this).attr('type') === 'number') {
                        $(this).val('0.00');
                    } else {
                        $(this).val('');
                    }
                });

                // Clear any old image display/hidden input for the new row
                newRow.find('small.text-muted').remove();
                newRow.find('input[name*="[old_image]"]').remove();
                newRow.find('input[name*="[id]"]').remove();

                // Reset image preview to placeholder
                newRow.find('.image-preview-container').html('<div class="no-image-placeholder"><i class="fa fa-image"></i></div>');

                // Update the displayed item number and the hidden input's value
                newRow.find('.item-number-display').text(itemIndex + 1);
                newRow.find('.item-number-hidden-input').val(itemIndex + 1);

                // Show the remove button for the new row
                newRow.find('.remove-item-btn').show();

                container.append(newRow);
                calculateTotalDebit();
            }

            // Function to handle image preview
            function handleImagePreview(input, previewContainer) {
                if (input.files && input.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewContainer.html(`<img src="${e.target.result}" class="image-thumbnail" data-bs-toggle="modal" data-bs-target="#imageModal" data-image-src="${e.target.result}" alt="Preview">`);
                    };
                    reader.readAsDataURL(input.files[0]);
                }
            }

            // Event listener for "Add New Item" button
            $('.add-item-btn').on('click', function() {
                addNewExpenseItem();
            });

            // Event listener for "Remove Item" buttons (delegated)
            $('#expense-items-container').on('click', '.remove-item-btn', function() {
                const rowToRemove = $(this).closest('.expense-item-row');
                const itemId = rowToRemove.data('item-id');

                if ($('.expense-item-row').length > 1) {
                    rowToRemove.remove();
                    calculateTotalDebit();

                    if (itemId) {
                        $('<input>').attr({
                            type: 'hidden',
                            name: 'deleted_items[]',
                            value: itemId
                        }).appendTo('form');
                    }

                    // Re-index item numbers after removal
                    $('#expense-items-container').find('.expense-item-row').each(function(idx) {
                        $(this).find('.item-number-display').text(idx + 1);
                        $(this).find('.item-number-hidden-input').val(idx + 1);
                        $(this).find('input, textarea').each(function() {
                            const originalName = $(this).attr('name');
                            if (originalName && originalName.startsWith('items[')) {
                                $(this).attr('name', originalName.replace(/items\[\d+\]/, `items[${idx}]`));
                            }
                        });
                    });
                    itemIndex = $('.expense-item-row').length - 1;

                } else {
                    console.log('You must have at least one expense item.');
                }
            });

            // Event listener for changes in debit input fields
            $('#expense-items-container').on('input', '.item-debit-input', function() {
                calculateTotalDebit();
            });

            // Event listener for file input changes (delegated)
            $('#expense-items-container').on('change', '.image-input', function() {
                const previewContainer = $(this).closest('.expense-item-row').find('.image-preview-container');
                handleImagePreview(this, previewContainer);
            });

            // Event listener for image modal
            $(document).on('click', '.image-thumbnail', function() {
                const imageSrc = $(this).data('image-src');
                $('#modalImage').attr('src', imageSrc);
            });

            // Initial setup on page load
            $(document).ready(function() {
                // If there's only one row (for creation mode), hide its remove button
                if ($('.expense-item-row').length === 1 && !{{ @$companyExpense->id ? 'true' : 'false' }}) {
                    $('.expense-item-row').first().find('.remove-item-btn').hide();
                }

                // If in edit mode and no items, ensure at least one empty row is shown
                @if(@$companyExpense->id && (!$companyExpenseItems || $companyExpenseItems->isEmpty()))
                    addNewExpenseItem();
                    $('.expense-item-row').first().find('.remove-item-btn').hide();
                @endif

                // Calculate total debit on initial page load
                calculateTotalDebit();
            });

        })(jQuery);
    </script>
@endpush

@push('style')
    <style>
        .expense-item-row {
            padding-top: 15px;
            margin-bottom: 15px;
        }
        .form-control-static {
            display: block;
            width: 100%;
            padding: .375rem .75rem;
            font-size: 1rem;
            line-height: 1.5;
            color: #495057;
            background-color: #e9ecef;
            border: 1px solid #ced4da;
            border-radius: .25rem;
            box-sizing: border-box;
        }
        .total-debit-display {
            font-weight: bold;
            color:rgb(255, 0, 0);
        }
        
        /* Image Preview Styles */
        .image-preview-container {
            height: 60px;
            width: 60px;
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }
        
        .image-thumbnail {
            width: 100%;
            height: 100%;
            object-fit: cover;
            cursor: pointer;
            border-radius: 6px;
            transition: all 0.3s ease;
        }
        
        .image-thumbnail:hover {
            transform: scale(1.05);
            opacity: 0.8;
        }
        
        .no-image-placeholder {
            color: #6c757d;
            font-size: 20px;
            text-align: center;
        }
        
        .no-image-placeholder i {
            opacity: 0.5;
        }
        
        /* Modal Styles */
        .modal-body img {
            max-width: 100%;
            max-height: 70vh;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .image-preview-container {
                height: 45px;
                width: 45px;
            }
            
            .no-image-placeholder {
                font-size: 16px;
            }
        }
    </style>
@endpush