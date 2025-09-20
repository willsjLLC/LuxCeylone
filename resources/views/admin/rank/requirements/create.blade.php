@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <form id="rankRequirementForm"
                        action="{{ isset($rankRequirement) ? route('admin.ranks.requirements.update', $rankRequirement->id) : route('admin.ranks.requirements.store') }}"
                        method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-12">
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
                                    </div>

                                    <div class="col-md-12">
                                        <hr>
                                        <h5>@lang('Rank Requirements')</h5>
                                    </div>

                                    <div class="col-md-12">
                                        <h5>@lang('Option One - Current User Personal Level')</h5>
                                        <div class="row option-group" id="optionOneGroup">

                                            <div class="row">
                                                <div class="row">
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label>@lang('Option One Status')</label>
                                                            <input type="checkbox" data-width="100%" data-onstyle="-success"
                                                                id="op_one_status" data-offstyle="-danger"
                                                                data-bs-toggle="toggle" data-on="@lang('Enable')"
                                                                data-off="@lang('Disable')" name="op_one_status"
                                                                for="op_one_status" @checked(old('op_one_status', @$rankRequirement->op_one_status))>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label>@lang('Option One Condition')</label>
                                                            <select class="form-control select2" name="op_one_condition">
                                                                <option value="">@lang('Select Condition')</option>
                                                                <option value="1" @selected(old('op_one_condition', @$rankRequirement->op_one_condition) == 1)>
                                                                    AND
                                                                </option>
                                                                <option value="2" @selected(old('op_one_condition', @$rankRequirement->op_one_condition) == 2)>
                                                                    OR
                                                                </option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label>@lang('Golden User Requirement')</label>
                                                            <input type="checkbox" data-width="100%" data-onstyle="-success"
                                                                id="golden_user_status" data-offstyle="-danger"
                                                                data-bs-toggle="toggle" data-on="@lang('Enable')"
                                                                data-off="@lang('Disable')" name="golden_user_status"
                                                                for="golden_user_status" @checked(old('golden_user_status', @$rankRequirement->golden_user_status))>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label>@lang('Minimum User Rank Requirement')</label>
                                                            <input type="checkbox" data-width="100%" data-onstyle="-success"
                                                                id="min_gd_rank_status" data-offstyle="-danger"
                                                                data-bs-toggle="toggle" data-on="@lang('Enable')"
                                                                data-off="@lang('Disable')" name="min_gd_rank_status"
                                                                for="min_gd_rank_status" @checked(old('min_gd_rank_status', @$rankRequirement->min_gd_rank_status))>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label>@lang('Minimum User Rank')</label>
                                                            <select class="form-control select2" name="min_gd_rank">
                                                                <option value="">@lang('Select rank')</option>
                                                                @foreach ($ranks as $rank)
                                                                    <option value="{{ $rank->id }}"
                                                                        @selected(old('min_gd_rank', @$rankRequirement->min_gd_rank) == $rank->id)>
                                                                        {{ __($rank->name) }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <h5>@lang('Option Two - Down line Golden Distributors')</h5>
                                            <div class="row option-group" id="optionTwoGroup">

                                                <div class="row">
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label>@lang('Option Two Status')</label>
                                                            <input type="checkbox" data-width="100%" data-onstyle="-success"
                                                                id="op_two_status" data-offstyle="-danger"
                                                                data-bs-toggle="toggle" data-on="@lang('Enable')"
                                                                data-off="@lang('Disable')" name="op_two_status"
                                                                for="op_two_status" @checked(old('op_two_status', @$rankRequirement->op_two_status))>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label>@lang('Option Two Condition')</label>
                                                            <select class="form-control select2" name="op_two_condition">
                                                                <option value="">@lang('Select Condition')</option>
                                                                <option value="1" @selected(old('op_two_condition', @$rankRequirement->op_two_condition) == 1)>
                                                                    AND
                                                                </option>
                                                                <option value="2" @selected(old('op_two_condition', @$rankRequirement->op_two_condition) == 2)>
                                                                    OR
                                                                </option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label>@lang('GD Count Requirement')</label>
                                                            <input type="checkbox" data-width="100%"
                                                                data-onstyle="-success" id="gd_count_status"
                                                                data-offstyle="-danger" data-bs-toggle="toggle"
                                                                data-on="@lang('Enable')" data-off="@lang('Disable')"
                                                                name="gd_count_status" for="gd_count_status"
                                                                @checked(old('gd_count_status', @$rankRequirement->gd_count_status))>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label>@lang('GD Count')</label>
                                                            <input type="number" name="gd_count" class="form-control"
                                                                value="{{ old('gd_count', @$rankRequirement->gd_count) }}"
                                                                placeholder="@lang('Enter required GD count')">
                                                        </div>
                                                    </div>

                                                    {{-- <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label>@lang('GD Required Rank')</label>
                                                            <input type="checkbox" data-width="100%"
                                                                data-onstyle="-success" id="gd_req_rank_status"
                                                                data-offstyle="-danger" data-bs-toggle="toggle"
                                                                data-on="@lang('Enable')" data-off="@lang('Disable')"
                                                                name="gd_req_rank_status" for="gd_req_rank_status"
                                                                @checked(old('gd_req_rank_status', @$rankRequirement->gd_req_rank_status))>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label>@lang('GD Required Rank')</label>
                                                            <select class="form-control select2" name="gd_req_rank_id">
                                                                <option value="">@lang('Select rank')</option>
                                                                @foreach ($ranks as $rank)
                                                                    <option value="{{ $rank->id }}"
                                                                        @selected(old('gd_req_rank_id', @$rankRequirement->gd_req_rank_id) == $rank->id)>
                                                                        {{ __($rank->name) }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div> --}}

                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label>@lang('GD Level Status')</label>
                                                            <input type="checkbox" data-width="100%"
                                                                data-onstyle="-success" id="gd_level_require_status"
                                                                data-offstyle="-danger" data-bs-toggle="toggle"
                                                                data-on="@lang('Enable')" data-off="@lang('Disable')"
                                                                name="gd_level_require_status"
                                                                for="gd_level_require_status"
                                                                @checked(old('gd_level_require_status', @$rankRequirement->gd_level_require_status))>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label>@lang('GD Required Level')</label>
                                                            <select class="form-control select2" name="gd_level">
                                                                <option value="">@lang('Select REF Level')</option>
                                                                <option value="1" @selected(old('gd_level', @$rankRequirement->gd_level) == 1)>
                                                                    Level 1
                                                                </option>
                                                                <option value="2" @selected(old('gd_level', @$rankRequirement->gd_level) == 2)>
                                                                    Level 2
                                                                </option>
                                                                <option value="3" @selected(old('gd_level', @$rankRequirement->gd_level) == 3)>
                                                                    Level 3
                                                                </option>
                                                                <option value="4" @selected(old('gd_level', @$rankRequirement->gd_level) == 4)>
                                                                    Level 4
                                                                </option>
                                                                <option value="5" @selected(old('gd_level', @$rankRequirement->gd_level) == 5)>
                                                                    Level 5
                                                                </option>
                                                                <option value="6" @selected(old('gd_level', @$rankRequirement->gd_level) == 6)>
                                                                    Level 6
                                                                </option>
                                                                <option value="7" @selected(old('gd_level', @$rankRequirement->gd_level) == 7)>
                                                                    Level 7
                                                                </option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <h5>@lang('Option Three - Down line Star Distributors')</h5>
                                            <div class="row option-group" id="optionThreeGroup">
                                                <div class="row">
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label>@lang('Option Three Status')</label>
                                                            <input type="checkbox" data-width="100%"
                                                                data-onstyle="-success" id="op_three_status"
                                                                data-offstyle="-danger" data-bs-toggle="toggle"
                                                                data-on="@lang('Enable')" data-off="@lang('Disable')"
                                                                name="op_three_status" for="op_three_status"
                                                                @checked(old('op_three_status', @$rankRequirement->op_three_status))>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label>@lang('Option Three Condition')</label>
                                                            <select class="form-control select2"
                                                                name="op_three_condition">
                                                                <option value="">@lang('Select Condition')</option>
                                                                <option value="1" @selected(old('op_three_condition', @$rankRequirement->op_three_condition) == 1)>
                                                                    AND
                                                                </option>
                                                                <option value="2" @selected(old('op_three_condition', @$rankRequirement->op_three_condition) == 2)>
                                                                    OR
                                                                </option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label>@lang('SD Count Requirement')</label>
                                                            <input type="checkbox" data-width="100%"
                                                                data-onstyle="-success" id="sd_count_status"
                                                                data-offstyle="-danger" data-bs-toggle="toggle"
                                                                data-on="@lang('Enable')" data-off="@lang('Disable')"
                                                                name="sd_count_status" for="sd_count_status"
                                                                @checked(old('sd_count_status', @$rankRequirement->sd_count_status))>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label>@lang('Required User SD Count')</label>
                                                            <input type="number" name="sd_count" class="form-control"
                                                                value="{{ old('sd_count', @$rankRequirement->sd_count) }}"
                                                                placeholder="@lang('Enter required SD count')">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label>@lang('SD Required Rank')</label>
                                                            <input type="checkbox" data-width="100%"
                                                                data-onstyle="-success" id="sd_req_rank_status"
                                                                data-offstyle="-danger" data-bs-toggle="toggle"
                                                                data-on="@lang('Enable')" data-off="@lang('Disable')"
                                                                name="sd_req_rank_status" for="sd_req_rank_status"
                                                                @checked(old('sd_req_rank_status', @$rankRequirement->sd_req_rank_status))>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label>@lang('SD Required Rank')</label>
                                                            <select class="form-control select2" name="sd_req_rank_id">
                                                                <option value="">@lang('Select rank')</option>
                                                                @foreach ($ranks as $rank)
                                                                    <option value="{{ $rank->id }}"
                                                                        @selected(old('sd_req_rank_id', @$rankRequirement->sd_req_rank_id) == $rank->id)>
                                                                        {{ __($rank->name) }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                    {{-- <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label>@lang('SD Required Level')</label>
                                                            <input type="checkbox" data-width="100%"
                                                                data-onstyle="-success" id="sd_level_require_status"
                                                                data-offstyle="-danger" data-bs-toggle="toggle"
                                                                data-on="@lang('Enable')" data-off="@lang('Disable')"
                                                                name="sd_level_require_status"
                                                                for="sd_level_require_status"
                                                                @checked(old('sd_level_require_status', @$rankRequirement->sd_level_require_status))>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label>@lang('SD Required REF Level')</label>
                                                            <select class="form-control select2" name="sd_level">
                                                                <option value="">@lang('Select REF Level')</option>
                                                                <option value="1" @selected(old('sd_level', @$rankRequirement->sd_level) == 1)>
                                                                    Level 1
                                                                </option>
                                                                <option value="2" @selected(old('sd_level', @$rankRequirement->sd_level) == 2)>
                                                                    Level 2
                                                                </option>
                                                                <option value="3" @selected(old('sd_level', @$rankRequirement->sd_level) == 3)>
                                                                    Level 3
                                                                </option>
                                                                <option value="4" @selected(old('sd_level', @$rankRequirement->sd_level) == 4)>
                                                                    Level 4
                                                                </option>
                                                                <option value="5" @selected(old('sd_level', @$rankRequirement->sd_level) == 5)>
                                                                    Level 5
                                                                </option>
                                                                <option value="6" @selected(old('sd_level', @$rankRequirement->sd_level) == 6)>
                                                                    Level 6
                                                                </option>
                                                                <option value="7" @selected(old('sd_level', @$rankRequirement->sd_level) == 7)>
                                                                    Level 7
                                                                </option>
                                                            </select>
                                                        </div>
                                                    </div> --}}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')</button>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('admin.ranks.requirements') }}" />
@endpush

@push('script')
    <script>
        bkLib.onDomLoaded(function() {

            document.getElementById('rankRequirementForm').addEventListener('submit', function(event) {});

            function toggleFormGroup(toggleId, containerId) {
                const toggle = document.getElementById(toggleId);
                const container = document.getElementById(containerId);

                if (toggle && container) {
                    function updateGroupState() {
                        const formElements = container.querySelectorAll('input, select');
                        formElements.forEach(element => {
                            if (element.id !== toggleId) {
                                if (toggle.checked) {
                                    element.removeAttribute('disabled');
                                } else {
                                    element.setAttribute('disabled', 'disabled');
                                    if (element.tagName === 'SELECT') {
                                        element.value = '';
                                    } else if (element.type === 'number' || element.type === 'text') {
                                        element.value = '';
                                    }

                                    if (element.type === 'checkbox' && element.getAttribute(
                                            'data-bs-toggle') === 'toggle') {
                                        $(element).bootstrapToggle('off');
                                    }
                                }
                            }
                        });
                    }

                    $(toggle).on('change', updateGroupState);
                    updateGroupState(); // Initial state setup
                }
            }
            toggleFormGroup('op_one_status', 'optionOneGroup');
            toggleFormGroup('op_two_status', 'optionTwoGroup');
            toggleFormGroup('op_three_status', 'optionThreeGroup');

            setupToggle('golden_user_status',
                'select[name="op_one_condition"]');
            setupToggle('min_gd_rank_status', 'select[name="min_gd_rank"]');
            setupToggle('gd_count_status', 'input[name="gd_count"]');
            setupToggle('gd_level_require_status', 'select[name="gd_level"]');
            setupToggle('gd_req_rank_status', 'select[name="gd_req_rank_id"]');
            setupToggle('sd_count_status', 'input[name="sd_count"]');
            setupToggle('sd_req_rank_status', 'select[name="sd_req_rank_id"]');
            setupToggle('sd_level_require_status', 'select[name="sd_level"]');
        });


        function setupToggle(toggleId, inputSelector) {
            const toggle = document.getElementById(toggleId);
            const input = document.querySelector(inputSelector);

            if (toggle && input) {
                function updateInputState() {
                    if (toggle.checked) {
                        input.removeAttribute('disabled');
                    } else {
                        input.setAttribute('disabled', 'disabled');
                        if (input.tagName === 'SELECT') {
                            input.value = '';
                        } else {
                            input.value = '';
                        }
                    }
                }
                $(toggle).on('change', updateInputState);
                updateInputState();
            }
        }
    </script>
@endpush

@push('style')
    <style>
        .profilePicUpload {
            margin-top: -20px;
        }

        .form-check {
            margin-bottom: 10px;
        }

        .form-check-input {
            margin-right: 8px;
        }

        hr {
            margin: 20px 0;
            border-color: #e9ecef;
        }

        h5 {
            color: #495057;
            font-weight: 600;
            margin-bottom: 15px;
        }

        /* Add a class to dim disabled sections for better UX */
        .option-group.disabled-section {
            opacity: 0.6;
            pointer-events: none;
            /* Prevent clicks on disabled elements */
        }
    </style>
@endpush
