{{-- @extends('admin.layouts.app')
@section('panel')
    <div class="row mb-none-30 justify-content-center">
        <div class="col-xl-4 col-md-6 mb-30">
            <div class="card overflow-hidden box--shadow1">
                <div class="card-body">
                    <h5 class="mb-20 text-muted">@lang('Deposit Via') @if($deposit->method_code < 5000) {{ __(@$deposit->gateway->name) }} @else @lang('Google Pay') @endif</h5>
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Date')
                            <span class="fw-bold">{{ showDateTime($deposit->created_at) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Transaction Number')
                            <span class="fw-bold">{{ $deposit->trx }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Username')
                            <span class="fw-bold">
                                <a href="{{ route('admin.users.detail', $deposit->user_id) }}"><span>@</span>{{ @$deposit->user->username }}</a>
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Method')
                            <span class="fw-bold">
                                @if($deposit->method_code < 5000)
                                    {{ __(@$deposit->gateway->name) }}
                                @else
                                    @lang('Google Pay')
                                @endif
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Amount')
                            <span class="fw-bold">{{ showAmount($deposit->amount) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Charge')
                            <span class="fw-bold">{{ showAmount($deposit->charge) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('After Charge')
                            <span class="fw-bold">{{ showAmount($deposit->amount+$deposit->charge) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Rate')
                            <span class="fw-bold">1 {{__(gs('cur_text'))}}
                                = {{ showAmount($deposit->rate,currencyFormat:false) }} {{__($deposit->baseCurrency())}}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('After Rate Conversion')
                            <span class="fw-bold">{{ showAmount($deposit->final_amount,currencyFormat:false) }} {{__($deposit->method_currency)}}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Status')
                            @php echo $deposit->statusBadge @endphp
                        </li>
                        @if($deposit->admin_feedback)
                            <li class="list-group-item">
                                <strong>@lang('Admin Response')</strong>
                                <br>
                                <p>{{__($deposit->admin_feedback)}}</p>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
        @if($details || $deposit->status == Status::PAYMENT_PENDING)
        <div class="col-xl-8 col-md-6 mb-30">
            <div class="card overflow-hidden box--shadow1">
                <div class="card-body">
                    <h5 class="card-title border-bottom pb-2">@lang('User Deposit Information')</h5>
                    @if($details != null)
                        @foreach(json_decode($details) as $val)
                            @if($deposit->method_code >= 1000)
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <h6>{{__($val->name)}}</h6>
                                    @if($val->type == 'checkbox')
                                        {{ implode(',',$val->value) }}
                                    @elseif($val->type == 'file')
                                        @if($val->value)
                                            <a href="{{ route('admin.download.attachment',encrypt(getFilePath('verify').'/'.$val->value)) }}"><i class="fa-regular fa-file"></i>  @lang('Attachment') </a>
                                        @else
                                            @lang('No File')
                                        @endif
                                    @else
                                    <p>{{__($val->value)}}</p>
                                    @endif
                                </div>
                            </div>
                            @endif
                        @endforeach
                        @if($deposit->method_code < 1000)
                            @include('admin.deposit.gateway_data',['details'=>json_decode($details)])
                        @endif
                    @endif
                    @if($deposit->status == Status::PAYMENT_PENDING)
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <button class="btn btn-outline--success btn-sm ms-1 confirmationBtn"
                                data-action="{{ route('admin.deposit.approve', $deposit->id) }}"
                                data-question="@lang('Are you sure to approve this transaction?')"
                                ><i class="las la-check"></i>
                                    @lang('Approve')
                                </button>

                                <button class="btn btn-outline--danger btn-sm ms-1" data-bs-toggle="modal" data-bs-target="#rejectModal"><i class="las la-ban"></i> @lang('Reject')
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>

   
    <div id="rejectModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Reject Deposit Confirmation')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="{{ route('admin.deposit.reject')}}" method="POST">
                    @csrf
                    <input type="hidden" name="id" value="{{ $deposit->id }}">
                    <div class="modal-body">
                        <p>@lang('Are you sure to') <span class="fw-bold">@lang('reject')</span> <span class="fw-bold text--success">{{ showAmount($deposit->amount)}}</span> @lang('deposit of') <span class="fw-bold">{{ @$deposit->user->username }}</span>?</p>

                        <div class="form-group">
                            <label class="mt-2">@lang('Reason for Rejection')</label>
                            <textarea name="message" maxlength="255" class="form-control" rows="5" required>{{ old('message') }}</textarea>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection --}}


@extends('admin.layouts.app')
@section('panel')
    <div class="row mb-none-30 justify-content-center">
        <div class="col-xl-4 col-md-6 mb-30">
            <div class="card overflow-hidden box--shadow1">
                <div class="card-body">
                    <h5 class="mb-20 text-muted">@lang('Deposit Via') @if($deposit->method_code < 5000) {{ __(@$deposit->gateway->name) }} @else @lang('Google Pay') @endif</h5>
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Date')
                            <span class="fw-bold">{{ showDateTime($deposit->created_at) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Transaction Number')
                            <span class="fw-bold">{{ $deposit->trx }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Username')
                            <span class="fw-bold">
                                <a href="{{ route('admin.users.detail', $deposit->user_id) }}"><span>@</span>{{ @$deposit->user->username }}</a>
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Method')
                            <span class="fw-bold">
                                @if($deposit->method_code < 5000)
                                    {{ __(@$deposit->gateway->name) }}
                                @else
                                    @lang('Google Pay')
                                @endif
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Amount')
                            <span class="fw-bold">{{ showAmount($deposit->amount) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Charge')
                            <span class="fw-bold">{{ showAmount($deposit->charge) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('After Charge')
                            <span class="fw-bold">{{ showAmount($deposit->amount+$deposit->charge) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Rate')
                            <span class="fw-bold">1 {{__(gs('cur_text'))}}
                                = {{ showAmount($deposit->rate,currencyFormat:false) }} {{__($deposit->baseCurrency())}}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('After Rate Conversion')
                            <span class="fw-bold">{{ showAmount($deposit->final_amount,currencyFormat:false) }} {{__($deposit->method_currency)}}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Status')
                            @php echo $deposit->statusBadge @endphp
                        </li>
                        @if($deposit->admin_feedback)
                            <li class="list-group-item">
                                <strong>@lang('Admin Response')</strong>
                                <br>
                                <p>{{__($deposit->admin_feedback)}}</p>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
        @if($details || $deposit->status == Status::PAYMENT_PENDING)
        <div class="col-xl-8 col-md-6 mb-30">
            <div class="card overflow-hidden box--shadow1">
                <div class="card-body">
                    <h5 class="card-title border-bottom pb-2">@lang('User Deposit Information')</h5>
                    @if($details != null)
                        @foreach(json_decode($details) as $val)
                            @if($deposit->method_code >= 1000)
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <h6>{{__($val->name)}}</h6>
                                    @if($val->type == 'checkbox')
                                        {{ implode(',',$val->value) }}
                                    @elseif($val->type == 'file')
                                        @if($val->value)
                                            @php
                                                $filePath = getFilePath('verify').'/'.$val->value;
                                                $extension = strtolower(pathinfo($val->value, PATHINFO_EXTENSION));
                                                $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg'];
                                                $isImage = in_array($extension, $imageExtensions);
                                            @endphp
                                            
                                            <div class="deposit-file-container">
                                                @if($isImage)
                                                    <div class="deposit-image-preview mb-2">
                                                        <img src="{{ getImage($filePath) }}" 
                                                             alt="{{ $val->name }}" 
                                                             class="deposit-image"
                                                             onclick="openDepositImageModal('{{ getImage($filePath) }}', '{{ $val->name }}')">
                                                    </div>
                                                @else
                                                    <div class="deposit-file-icon mb-2">
                                                        <i class="fas fa-file-{{ $extension == 'pdf' ? 'pdf' : 'alt' }} fa-2x text-primary"></i>
                                                        <p class="small text-muted mt-1">{{ strtoupper($extension) }} @lang('File')</p>
                                                    </div>
                                                @endif
                                                
                                                <div class="file-actions">
                                                    @if(!$isImage)
                                                        <button type="button" class="btn btn-outline-primary btn-sm me-2" 
                                                                onclick="previewDepositFile('{{ $filePath }}', '{{ $val->value }}')">
                                                            <i class="fa-regular fa-eye"></i> @lang('Preview')
                                                        </button>
                                                    @endif
                                                    <a href="{{ route('admin.download.attachment',encrypt($filePath)) }}" 
                                                       class="btn btn-outline-secondary btn-sm">
                                                        <i class="fa-regular fa-download"></i> @lang('Download')
                                                    </a>
                                                </div>
                                            </div>
                                        @else
                                            @lang('No File')
                                        @endif
                                    @else
                                    <p>{{__($val->value)}}</p>
                                    @endif
                                </div>
                            </div>
                            @endif
                        @endforeach
                        @if($deposit->method_code < 1000)
                            @include('admin.deposit.gateway_data',['details'=>json_decode($details)])
                        @endif
                    @endif
                    @if($deposit->status == Status::PAYMENT_PENDING)
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <button class="btn btn-outline--success btn-sm ms-1 confirmationBtn"
                                data-action="{{ route('admin.deposit.approve', $deposit->id) }}"
                                data-question="@lang('Are you sure to approve this transaction?')"
                                ><i class="las la-check"></i>
                                    @lang('Approve')
                                </button>

                                <button class="btn btn-outline--danger btn-sm ms-1" data-bs-toggle="modal" data-bs-target="#rejectModal"><i class="las la-ban"></i> @lang('Reject')
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- REJECT MODAL --}}
    <div id="rejectModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Reject Deposit Confirmation')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="{{ route('admin.deposit.reject')}}" method="POST">
                    @csrf
                    <input type="hidden" name="id" value="{{ $deposit->id }}">
                    <div class="modal-body">
                        <p>@lang('Are you sure to') <span class="fw-bold">@lang('reject')</span> <span class="fw-bold text--success">{{ showAmount($deposit->amount)}}</span> @lang('deposit of') <span class="fw-bold">{{ @$deposit->user->username }}</span>?</p>

                        <div class="form-group">
                            <label class="mt-2">@lang('Reason for Rejection')</label>
                            <textarea name="message" maxlength="255" class="form-control" rows="5" required>{{ old('message') }}</textarea>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Deposit Image Full View Modal -->
    <div id="depositImageViewModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="depositImageModalTitle">@lang('Image View')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <div class="modal-body text-center p-2">
                    <img id="depositFullSizeImage" src="" alt="" class="full-size-image">
                </div>
            </div>
        </div>
    </div>

    <!-- Deposit File Preview Modal -->
    <div id="depositFilePreviewModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="depositPreviewModalTitle">@lang('File Preview')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <div id="depositPreviewContent">
                        <!-- Preview content will be loaded here -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('Close')</button>
                    <a id="depositDownloadFromPreview" href="#" class="btn btn-primary">
                        <i class="fa-regular fa-download"></i> @lang('Download')
                    </a>
                </div>
            </div>
        </div>
    </div>

    <x-confirmation-modal />

    <style>
        .deposit-file-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            margin-top: 10px;
        }
        
        .deposit-image-preview {
            position: relative;
            border: 2px solid #dee2e6;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: transform 0.2s ease;
        }
        
        .deposit-image-preview:hover {
            transform: scale(1.02);
            cursor: pointer;
            border-color: #007bff;
        }
        
        .deposit-image {
            width: 200px;
            height: 150px;
            object-fit: cover;
            display: block;
        }
        
        .deposit-file-icon {
            padding: 20px;
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            text-align: center;
            width: 200px;
        }
        
        .file-actions {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
            justify-content: center;
        }
        
        .full-size-image {
            max-width: 100%;
            max-height: 80vh;
            object-fit: contain;
            border-radius: 8px;
        }
        
        .preview-image {
            max-width: 100%;
            max-height: 70vh;
            object-fit: contain;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .preview-document {
            width: 100%;
            height: 70vh;
            border: none;
            border-radius: 8px;
        }
        
        .preview-error {
            padding: 2rem;
            color: #6c757d;
        }
        
        .preview-loading {
            padding: 2rem;
            color: #6c757d;
        }
        
        .preview-unsupported {
            padding: 2rem;
            text-align: center;
            color: #6c757d;
        }
        
        .preview-unsupported i {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: #dee2e6;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .deposit-image {
                width: 150px;
                height: 112px;
            }
            
            .deposit-file-icon {
                width: 150px;
                padding: 15px;
            }
            
            .file-actions {
                flex-direction: column;
                width: 100%;
            }
        }
    </style>

    <script>
        function openDepositImageModal(imageSrc, imageName) {
            const modal = new bootstrap.Modal(document.getElementById('depositImageViewModal'));
            const fullSizeImage = document.getElementById('depositFullSizeImage');
            const modalTitle = document.getElementById('depositImageModalTitle');
            
            fullSizeImage.src = imageSrc;
            fullSizeImage.alt = imageName;
            modalTitle.textContent = imageName;
            
            modal.show();
        }
        
        function previewDepositFile(filePath, fileName) {
            const modal = new bootstrap.Modal(document.getElementById('depositFilePreviewModal'));
            const previewContent = document.getElementById('depositPreviewContent');
            const modalTitle = document.getElementById('depositPreviewModalTitle');
            const downloadBtn = document.getElementById('depositDownloadFromPreview');
            
            // Set modal title
            modalTitle.textContent = fileName;
            
            // Set download link
            downloadBtn.href = "{{ route('admin.download.attachment', '') }}/" + btoa(filePath).replace(/\+/g, '-').replace(/\//g, '_').replace(/=/g, '');
            
            // Show loading
            previewContent.innerHTML = '<div class="preview-loading"><i class="fas fa-spinner fa-spin fa-2x"></i><p class="mt-3">@lang("Loading preview...")</p></div>';
            
            // Get file extension
            const extension = fileName.split('.').pop().toLowerCase();
            const documentExtensions = ['pdf'];
            
            // Generate preview URL
            const previewUrl = "{{ asset('') }}" + filePath;
            
            if (documentExtensions.includes(extension)) {
                // Preview PDF
                previewContent.innerHTML = `
                    <iframe src="${previewUrl}" class="preview-document" type="application/pdf">
                        <div class="preview-error">
                            <i class="fas fa-file-pdf fa-2x"></i>
                            <p class="mt-3">@lang("PDF preview not supported in this browser")</p>
                            <small class="text-muted">@lang("Please download the file to view it")</small>
                        </div>
                    </iframe>
                `;
                
            } else {
                // Unsupported file type
                previewContent.innerHTML = `
                    <div class="preview-unsupported">
                        <i class="fas fa-file-alt"></i>
                        <h5>@lang("Preview not available")</h5>
                        <p class="text-muted">@lang("Preview is not supported for this file type") (${extension.toUpperCase()})</p>
                        <small class="text-muted">@lang("Please download the file to view its contents")</small>
                    </div>
                `;
            }
            
            // Show modal
            modal.show();
        }
    </script>
@endsection
