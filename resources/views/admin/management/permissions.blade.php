@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">

                <form action="{{ route('admin.management.permission.update', $admin->id) }}" method="POST">
                    @csrf
                    @method('POST')
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <h4 class="mb-3">Manage Permissions for: <strong>{{ $admin->name }}</strong></h4>
                            </div>
                            <div class="col-6">
                                <div class="mb-4">
                                    <input type="text" class="form-control" id="permissionSearch"
                                        placeholder="Search permission...">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            @php
                                $groupedPermissions = $allPermissions->groupBy('category');
                            @endphp

                            @foreach ($groupedPermissions as $category => $permissions)
                                <div class="category-block mb-4" data-category="{{ strtolower($category) }}">
                                    <h5 class="category-title">{{ ucwords(str_replace('_', ' ', $category)) }} Management
                                    </h5>
                                    <div class="row mt-2">
                                        @foreach ($permissions as $permission)
                                            <div class="col-md-4 mb-3 permission-block"
                                                data-permission="{{ strtolower(str_replace('.', ' ', $permission->description)) }}"
                                                data-category="{{ strtolower($category) }}">
                                                <div
                                                    class="form-switch border rounded p-3 shadow-sm h-100 d-flex justify-content-between align-items-center">
                                                    <label for="perm_{{ $permission->id }}"
                                                        class="form-check-label fw-semibold">
                                                        {{ ucwords(str_replace('.', ' ', $permission->description)) }}
                                                    </label>
                                                    <input type="checkbox" data-width="20%" data-height="50"
                                                        data-onstyle="-success" data-offstyle="-danger" name="permissions[]"
                                                        id="perm_{{ $permission->id }}" value="{{ $permission->name }}"
                                                        data-off="@lang('Disabled')" class="form-check-input"
                                                        data-bs-toggle="toggle" data-on="@lang('Enable')"
                                                        {{ $admin->hasPermissionTo($permission->name) ? 'checked' : '' }}>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach

                        </div>

                        <div class="text-end mt-4">
                            <button type="submit" class="btn btn--primary w-100 h-45">@lang('Save')</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('admin.management.index') }}" />
@endpush

@push('style')
    <style>
        .form-switch .form-check-input {
            width: 45px;
            height: 24px;
            background-color: #ccc;
            border-radius: 50px;
            position: relative;
            appearance: none;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .form-switch .form-check-input:checked {
            background-color: #3b82f6;
        }

        .form-switch .form-check-input::before {
            content: "";
            position: absolute;
            top: 2px;
            left: 2px;
            width: 20px;
            height: 20px;
            background-color: white;
            border-radius: 50%;
            transition: transform 0.2s;
        }

        .form-switch .form-check-input:checked::before {
            transform: translateX(21px);
        }
    </style>
@endpush

@push('script')
    <script>
        document.getElementById('permissionSearch').addEventListener('input', function() {
            const query = this.value.toLowerCase();

            const categoryBlocks = document.querySelectorAll('.category-block');

            categoryBlocks.forEach(category => {
                const categoryName = category.getAttribute('data-category');
                const permissions = category.querySelectorAll('.permission-block');
                let matchFound = false;

                permissions.forEach(permission => {
                    const permissionText = permission.getAttribute('data-permission');
                    const permissionCategory = permission.getAttribute('data-category');

                    const matches = permissionText.includes(query) || permissionCategory.includes(
                        query);

                    if (matches) {
                        permission.style.display = 'block';
                        matchFound = true;
                    } else {
                        permission.style.display = 'none';
                    }
                });

                category.style.display = matchFound ? 'block' : 'none';
            });
        });
    </script>
@endpush
