@extends('admin.layouts.app')
@section('panel')
    <div class="container">

        {{-- Form for Uploading Images --}}
        <div class="col">
            <div class="col-md-12">
                <form id="imageUploadForm" method="POST" enctype="multipart/form-data"
                    action="{{ route('admin.banner.image') }}">
                    @csrf
                    <div class="mb-3">
                        <input type="file" name="image" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-outline--primary text-right">Upload</button>
                </form>
            </div>

            {{-- Images Table Display --}}
            <div class="col-md-12 mt-3">
                <table class="table table--light style--two">
                    <thead>
                        <tr>
                            <th>Images</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach (@$bannerImage as $imageSet)
                            <tr>
                                <td>
                                    @if (!empty($imageSet->images))
                                        <div class="p-3 ">
                                            <div class="">
                                                @if ($imageSet->images)
                                                    <img src="{{ getImage(getFilePath('bannerImage') . '/' . $imageSet->images) }}"
                                                        width="80" height="80" >
                                                @else
                                                    <img src="{{ asset('assets/admin/images/empty.png') }}"
                                                        class="b-radius--10  ">
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <a href="javascript:void(0)" class="btn btn-danger confirmationBtn"
                                       data-action="{{ route('admin.banner.delete', $imageSet->id) }}"
                                       data-question="@lang('Are you sure to delete this image?')">
                                       @lang('Delete')
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection


@push('script')
@endpush
