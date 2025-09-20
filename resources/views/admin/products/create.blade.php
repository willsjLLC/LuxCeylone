@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <form id="productForm"
                        action="{{ isset($product) ? route('admin.products.store', $product->id) : route('admin.products.store') }}"
                        method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-md-5">
                                <div class="">

                                    <div class="product-images-container">
                                        <!-- Primary Image (Required) -->
                                        <div class="mb-3">
                                            <label class="form-label">@lang('Primary Image (Required)')</label>
                                            <x-image-uploader name="images[0]" :imagePath="getImage(
                                                getFilePath('product') . '/' . @$product->image_url,
                                                getFileSize('product'),
                                            )" :size="false"
                                                class="w-100" id="primaryImageUpload" :required="!isset($product) || !$product->image_url"
                                                value="{{ old('images.0', @$primary_image ? $primary_image->image : @$product->image_url) }}" />
                                            <input type="hidden" name="is_primary[0]" value="1">
                                            <input type="hidden" name="sort_order[0]" value="1">
                                        </div>

                                        <!-- Additional Images (Optional) -->
                                        @for ($i = 1; $i < 5; $i++)
                                            <div class="mb-3">
                                                <x-image-uploader name="images[{{ $i }}]" :imagePath="getImage(
                                                    getFilePath('product') . '/' . @$additional_images[$i - 1]->image ??
                                                        '',
                                                    getFileSize('product'),
                                                )"
                                                    :size="false" class="w-100"
                                                    id="additionalImageUpload{{ $i }}" :required="false"
                                                    value="{{ old('images.' . $i, @$additional_images[$i - 1]->image ?? '') }}" />
                                                <input type="hidden" name="is_primary[{{ $i }}]" value="0">
                                                <input type="hidden" name="sort_order[{{ $i }}]"
                                                    value="{{ $i + 1 }}">
                                            </div>
                                        @endfor
                                    </div>


                                </div>
                            </div>
                            <div class="col-md-7">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>@lang('Name')</label>
                                            <input type="text" name="name" class="form-control"
                                                value="{{ old('name', @$product->name) }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>@lang('Category')</label>
                                            <select class="form-control select2" name="category_id" id="category_id"
                                                required>
                                                <option value="">@lang('Select one')</option>
                                                @foreach ($product_categories as $product_category)
                                                    <option value="{{ $product_category->id }}"
                                                        @selected(old('category_id', @$product->category_id) == $product_category->id)>
                                                        {{ __($product_category->name) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>@lang('Sub Category')</label>
                                            <select class="form-control select2" name="sub_category_id" id="sub_category_id"
                                                required>
                                                <option value="">@lang('Select one')</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>@lang('Original Price')</label>
                                            <input type="text" name="original_price" id="original_price"
                                                class="form-control"
                                                value="{{ old('original_price', @$product->original_price) }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>@lang('Cost')</label>
                                            <input type="number" name="cost" id="cost" class="form-control"
                                                value="{{ old('cost', @$product->cost) }}" step="0.01" required>

                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>@lang('Discount')</label>
                                            <input type="number" name="discount" class="form-control" step="0.01"
                                                value="{{ old('discount', @$product->discount) }}">
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>@lang('Quantity')</label>
                                            <input type="number" name="quantity" class="form-control"
                                                value="{{ old('quantity', @$product->quantity) }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>@lang('Profit')</label>
                                            <input type="number" name="profit" id="profit" class="form-control"
                                                value="{{ old('profit', @$product->profit) }}" step="0.01" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>@lang('Selling Price')</label>
                                            <input type="number" name="selling_price" id="selling_price"
                                                class="form-control"
                                                value="{{ old('selling_price', @$product->selling_price) }}" step="0.01"
                                                readonly>
                                        </div>
                                    </div>

                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label>@lang('Weight')</label>
                                            <input type="number" name="weight" class="form-control"
                                                value="{{ old('weight', @$product->weight) }}" step="0.01" required>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>@lang('Unit')</label>
                                            <select class="form-control select2" name="unit" required>
                                                <option value="">@lang('Select one')</option>
                                                <option value="mg" @selected(old('unit', @$product->unit) == 'mg')>@lang('mg')
                                                </option>
                                                <option value="ml" @selected(old('unit', @$product->unit) == 'ml')>@lang('ml')
                                                </option>
                                                <option value="g" @selected(old('unit', @$product->unit) == 'g')>@lang('g')
                                                </option>
                                                <option value="bags" @selected(old('unit', @$product->unit) == 'bags')>@lang('bags')
                                                </option>
                                                <option value="package" @selected(old('unit', @$product->unit) == 'package')>@lang('package')
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>@lang('Status')</label>
                                            <select class="form-control select2" name="status" required>
                                                <option value="">@lang('Select one')</option>
                                                <option value="active" @selected(old('status', @$product->status) == 'active')>@lang('Active')
                                                </option>
                                                <option value="inactive" @selected(old('status', @$product->status) == 'inactive')>@lang('Inactive')
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>@lang('Description')</label>
                                            <textarea id="description" name="description" class="form-control" cols="30" rows="20">{{ old('description', @$product->description) }}</textarea>
                                        </div>
                                    </div>

                                    <!-- Add Watermark Option -->
                                    <div class="mt-3 form-group">
                                        <label>@lang('Apply Watermark')</label>
                                        <input type="checkbox" name="watermark" id="apply_watermark"
                                            class="form-check-input" data-bs-toggle="toggle" data-on="@lang('Yes')"
                                            data-off="@lang('No')" data-onstyle="-success" data-offstyle="-danger"
                                            data-width="100%" data-height="50" value="1"
                                            {{ old('watermark', @$product->watermark) ? 'checked' : '' }}>
                                        <small class="form-text text-muted">
                                            @lang('Add "luxceylone.com" watermark to product image')
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <hr>
                            <h4 class="mb-3">Single Product Commissions</h4>

                            <div class="mt-3 col-md-3">
                                <div class="form-group">
                                    <label>Total Commission</label>
                                    <input type="number" id="total_commission" name="total_commission"
                                        value="total_commission" class="form-control" readonly>
                                </div>
                            </div>

                            <h5 class="mb-2">Company Commissions</h5>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('Company Commission')</label>
                                    <input type="number" name="company_commission" class="form-control" min="0.0"
                                        step="any"
                                        value="{{ old('company_commission', @$commissions->company_commission) }}"
                                        required>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('Company Expenses')</label>
                                    <input type="number" name="company_expenses" class="form-control" min="0.0"
                                        step="any"
                                        value="{{ old('company_expenses', @$commissions->company_expenses) }}" required>
                                </div>
                            </div>

                            <h5 class="mb-2">Customers Commissions</h5>
                            <br>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('Customers Commission')</label>
                                    <input type="number" name="customers_commission" class="form-control"
                                        value="{{ old('customers_commission', @$commissions->customers_commission) }}"
                                        min="0.0" step="any" required>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('Customers Voucher')</label>
                                    <input type="number" name="customers_voucher" class="form-control"
                                        value="{{ old('customers_voucher', @$commissions->customers_voucher) }}"
                                        min="0.0" step="any" required>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('Customers Festival')</label>
                                    <input type="number" name="customers_festival" class="form-control" min="0.0"
                                        step="any"
                                        value="{{ old('customers_festival', @$commissions->customers_festival) }}"
                                        required>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('Customers Saving')</label>
                                    <input type="number" name="customers_saving" class="form-control" min="0.0"
                                        step="any"
                                        value="{{ old('customers_saving', @$commissions->customers_saving) }}" required>
                                </div>
                            </div>

                            <h5 class="mt-2 mb-2">Leader Commissions</h5>
                            <br>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('Leader Bonus')</label>
                                    <input type="number" name="leader_bonus" class="form-control" min="0.0"
                                        step="any" value="{{ old('leader_bonus', @$commissions->leader_bonus) }}"
                                        required>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('Leader Vehicle Lease')</label>
                                    <input type="number" name="leader_vehicle_lease" class="form-control"
                                        min="0.0" step="any"
                                        value="{{ old('leader_vehicle_lease', @$commissions->leader_vehicle_lease) }}"
                                        required>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('Leader Petrol')</label>
                                    <input type="number" name="leader_petrol" class="form-control" min="0.0"
                                        step="any" value="{{ old('leader_petrol', @$commissions->leader_petrol) }}"
                                        required>
                                </div>
                            </div>



                            <h5 class="mt-2 mb-2">Top 10 Leaders Commissions</h5>
                            <br>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('Top Leader Car')</label>
                                    <input type="number" name="top_leader_car" class="form-control" min="0.0"
                                        step="any" value="{{ old('top_leader_car', @$commissions->top_leader_car) }}"
                                        required>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('Top Leader House')</label>
                                    <input type="number" name="top_leader_house" class="form-control" min="0.0"
                                        step="any"
                                        value="{{ old('top_leader_house', @$commissions->top_leader_house) }}" required>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>@lang('Top Leader Expenses')</label>
                                    <input type="number" name="top_leader_expenses" class="form-control" min="0.0"
                                        step="any"
                                        value="{{ old('top_leader_expenses', @$commissions->top_leader_expenses) }}"
                                        required>
                                </div>
                            </div>

                        </div>
                        <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('admin.products.index') }}" />
@endpush

@push('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/nicedit/0.9/nicEdit.min.js"></script>
    <script>
        bkLib.onDomLoaded(function() {
            // Initialize nicEdit
            new nicEditor({
                fullPanel: true
            }).panelInstance('description');

            // Set up form validation
            document.getElementById('productForm').addEventListener('submit', function(event) {
                let nicInstance = nicEditors.findEditor('description');
                let content = nicInstance.getContent();

                if (!content.trim()) {
                    alert('Description is required!');
                    event.preventDefault();
                    return false;
                } else {
                    document.getElementById('description').value = content;
                }

                // Calculate before submit
                calculateProfit();
                calculateSellingPrice();
            });

            function calculateProfit() {
                let cost = parseFloat(document.getElementById('cost').value) || 0;
                let discount = parseFloat(document.getElementById('discount').value) || 0;
                let original_price = parseFloat(document.getElementById('original_price').value) || 0;
                let profit = original_price - (cost + discount);
                document.getElementById('profit').value = (original_price - (cost + discount)).toFixed(2);
            }

            function calculateSellingPrice() {
                let original_price = parseFloat(document.getElementById('original_price').value) || 0;
                let discount = parseFloat(document.getElementById('discount').value) || 0;
                let selling_price = original_price - discount;
                document.getElementById('selling_price').value = (original_price - discount).toFixed(
                    2);
            }

            // Add event listeners
            ['cost', 'discount', 'original_price'].forEach(id => {
                document.getElementById(id).addEventListener('input', () => {
                    calculateProfit();
                    calculateSellingPrice();
                });
            });

            // Initial calculation
            calculateProfit();
            calculateSellingPrice();
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const commissionInputNames = [
                'company_commission',
                'company_expenses',
                'customers_commission',
                'customers_voucher',
                'customers_festival',
                'customers_saving',
                'leader_bonus',
                'leader_vehicle_lease',
                'leader_petrol',
                'top_leader_car',
                'top_leader_house',
                'top_leader_expenses'
            ];

            const totalCommissionField = document.getElementById("total_commission");

            function calculateTotalCommission() {
                let total = 0;
                commissionInputNames.forEach(name => {
                    const inputField = document.querySelector(`input[name='${name}']`);
                    if (inputField) {
                        const value = parseFloat(inputField.value);
                        if (!isNaN(value)) {
                            total += value;
                        }
                    }
                });
                totalCommissionField.value = total.toFixed(2);
            }

            commissionInputNames.forEach(name => {
                const inputField = document.querySelector(`input[name='${name}']`);
                if (inputField) {
                    inputField.addEventListener("input", calculateTotalCommission);
                }
            });

            calculateTotalCommission();
        });
    </script>

    <script>
        $(document).ready(function() {
            const categoryId = "{{ old('category_id', @$product->category_id) }}";
            const selectedSubcategoryId = "{{ old('sub_category_id', @$product->sub_category_id) }}";

            function loadSubcategories(categoryId, selectedId = null) {
                $('#sub_category_id').empty().append('<option value="">Select one</option>');

                if (categoryId) {
                    $.ajax({
                        url: '/admin/product-sub-category/related-category/' + categoryId,
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            $.each(data, function(key, value) {
                                let selected = (value.id == selectedId) ? 'selected' : '';
                                $('#sub_category_id').append('<option value="' + value.id +
                                    '" ' + selected + '>' + value.name + '</option>');
                            });
                        }
                    });
                }
            }

            // Load on category change
            $('#category_id').on('change', function() {
                let selectedCategoryId = $(this).val();
                loadSubcategories(selectedCategoryId);
            });

            // Preload on edit if category and subcategory exist
            if (categoryId) {
                loadSubcategories(categoryId, selectedSubcategoryId);
            }
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
