@extends('admin.layouts.app')
@section('panel')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    @if($user->kyc_data)
                        <ul class="list-group">
                          @foreach($user->kyc_data as $val)
                          @continue(!$val->value)
                          <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{__($val->name)}}
                            <span>
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
                                        
                                        <div class="kyc-file-container">
                                            @if($isImage)
                                                <div class="kyc-image-preview mb-2">
                                                    <img src="{{ getImage($filePath) }}" 
                                                         alt="{{ $val->name }}" 
                                                         class="kyc-image"
                                                         onclick="openImageModal('{{ getImage($filePath) }}', '{{ $val->name }}')">
                                                </div>
                                            @else
                                                <div class="kyc-file-icon mb-2">
                                                    <i class="fas fa-file-{{ $extension == 'pdf' ? 'pdf' : 'alt' }} fa-2x text-primary"></i>
                                                    <p class="small text-muted mt-1">{{ strtoupper($extension) }} @lang('File')</p>
                                                </div>
                                            @endif
                                            
                                            <div class="file-actions">
                                                @if(!$isImage)
                                                    <button type="button" class="btn btn-outline-primary btn-sm me-2" 
                                                            onclick="previewFile('{{ $filePath }}', '{{ $val->value }}')">
                                                        <i class="fa-regular fa-eye"></i> @lang('Preview')
                                                    </button>
                                                @endif
                                                <a href="{{ route('admin.download.attachment',encrypt($filePath)) }}" 
                                                   class="btn btn-outline-secondary btn-sm">
                                                    <i class="las la-download"></i> @lang('Download')
                                                </a>
                                            </div>
                                        </div>
                                    @else
                                        @lang('No File')
                                    @endif
                                @else
                                <p>{{__($val->value)}}</p>
                                @endif
                            </span>
                          </li>
                          @endforeach
                        </ul>
                        @else
                        <h5 class="text-center">@lang('KYC data not found')</h5>
                    @endif

                    @if($user->kv == Status::KYC_UNVERIFIED)
                    <div class="my-3">
                        <h6>@lang('Rejection Reason')</h6>
                        <p>{{ $user->kyc_rejection_reason }}</p>
                    </div>
                    @endif

                    @if($user->kv == Status::KYC_PENDING)
                    <div class="d-flex flex-wrap justify-content-end mt-3">
                        <button class="btn btn-outline--danger me-3" data-bs-toggle="modal" data-bs-target="#kycRejectionModal"><i class="las la-ban"></i>@lang('Reject')</button>
                        <button class="btn btn-outline--success confirmationBtn" data-question="@lang('Are you sure to approve this documents?')" data-action="{{ route('admin.users.kyc.approve', $user->id) }}"><i class="las la-check"></i>@lang('Approve')</button>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Image Full View Modal -->
    <div id="imageViewModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageModalTitle">@lang('Image View')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <div class="modal-body text-center p-2">
                    <img id="fullSizeImage" src="" alt="" class="full-size-image">
                </div>
            </div>
        </div>
    </div>

    <!-- File Preview Modal -->
    <div id="filePreviewModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="previewModalTitle">@lang('File Preview')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <div id="previewContent">
                        <!-- Preview content will be loaded here -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('Close')</button>
                    <a id="downloadFromPreview" href="#" class="btn btn-primary">
                        <i class="las la-download"></i> @lang('Download')
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div id="kycRejectionModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Reject KYC Documents')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="{{ route('admin.users.kyc.reject', $user->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-primary p-3">
                            @lang('If you reject these documents, the user will be able to re-submit new documents and these documents will be replaced by new documents.')
                        </div>

                        <div class="form-group">
                            <label>@lang('Rejection Reason')</label>
                            <textarea class="form-control" name="reason" rows="4" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary h-45 w-100">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <x-confirmation-modal />

    <style>
        .kyc-file-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }
        
        .kyc-image-preview {
            position: relative;
            border: 2px solid #dee2e6;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: transform 0.2s ease;
        }
        
        .kyc-image-preview:hover {
            transform: scale(1.02);
            cursor: pointer;
            border-color: #007bff;
        }
        
        .kyc-image {
            width: 200px;
            height: 150px;
            object-fit: cover;
            display: block;
        }
        
        .kyc-file-icon {
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
            .kyc-image {
                width: 150px;
                height: 112px;
            }
            
            .kyc-file-icon {
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
        function openImageModal(imageSrc, imageName) {
            const modal = new bootstrap.Modal(document.getElementById('imageViewModal'));
            const fullSizeImage = document.getElementById('fullSizeImage');
            const modalTitle = document.getElementById('imageModalTitle');
            
            fullSizeImage.src = imageSrc;
            fullSizeImage.alt = imageName;
            modalTitle.textContent = imageName;
            
            modal.show();
        }
        
        function previewFile(filePath, fileName) {
            const modal = new bootstrap.Modal(document.getElementById('filePreviewModal'));
            const previewContent = document.getElementById('previewContent');
            const modalTitle = document.getElementById('previewModalTitle');
            const downloadBtn = document.getElementById('downloadFromPreview');
            
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