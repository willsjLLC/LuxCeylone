{{-- @php
    $freelancerContent = getContent('top_freelancer.content', true);
    $topFreelancer = \App\Models\JobProve::approve()
        ->groupBy('user_id')
        ->selectRaw('count(id) as count, user_id')
        ->with('user')
        ->orderBy('count', 'desc')
        ->take(5)
        ->get();
@endphp
<section class="freelancer-section padding-top padding-bottom overflow-hidden">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                <div class="section__header text-center">
                    <h2 class="section__header-title">{{ __($freelancerContent->data_values->heading) }}</h2>
                    <p>{{ __(@$freelancerContent->data_values->subheading) }}</p>
                </div>
            </div>
        </div>
        <div class="freelancer__slider">
            @foreach ($topFreelancer as $freelancer)
                <div class="single-slide">
                    <div class="freelancer__item">
                        <div class="freelancer__header">
                            <div class="thumb">
                                <img src="{{ getImage(getFilePath('userProfile') . '/' . $freelancer->user->image, getFileSize('userProfile'),true) }}" alt="@lang('User')">
                            </div>
                            <h5 class="name">{{ @$freelancer->user->fullname }}</h5>
                            <p class="designation">{{@$freelancer->user->email}}</p>
                        </div>
                        <div class="freelancer__footer">
                            <ul class="freelancer__info">
                                <li class="d-flex justify-content-between flex-wrap mb-2 me-0">
                                    <span>@lang('Country')</span>
                                    <span class="text-end">{{ @$freelancer->user->address->country }}</span>
                                </li>
                                <li class="d-flex justify-content-between flex-wrap">
                                    <span >@lang('Jobs Completed')</span>
                                    <span class="text-end">{{ $freelancer->count }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            @endforeach

        </div>
    </div>
</section> --}}
