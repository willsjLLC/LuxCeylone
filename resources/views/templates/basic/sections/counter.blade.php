@php
    $counterElement = getContent('counter.element', orderById: true);
@endphp
<section class="counter-section padding-top padding-bottom">
    <div class="container">
        <div class="mb-5 popular__tags">
            <h3 class="title d-inline-block">@lang('Featured Categories')</h3>
            <div class="flex-wrap gap-4 category-listgrid d-flex">
                @php $visibleCategories = $categories->slice(1, 8); @endphp

                @foreach ($visibleCategories as $category)
                    <div class="category-item visible-category">
                        <a href="{{ route('ads.index') }}"
                            class="text-decoration-none">
                            <div class="text-center category-icon-box ">
                                @if ($category->image)
                                    <img src="{{ asset('assets/images/category/' . $category->image)  }}"
                                        alt="{{ $category->name }}" class="category-icon">
                                @else
                                    <div class="placeholder-icon">
                                        <i class="fa fa-box"></i>
                                    </div>
                                @endif
                                <p class="mt-2 mb-0 category-name">{{ $category->name }}</p>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
            <ul class="tags-list">
                @foreach ($keywords as $keyword)
                    <li>
                        <a
                            href="{{ route('category.jobs', [@$keyword->category->id, slug(@$keyword->category->name)]) }}">
                            {{ __(@$keyword->category->name) }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="row g-4 justify-content-center">
            @foreach ($counterElement as $counter)
                <div class="col-lg-4 col-md-6 col-sm-10">
                    <div class="counter__item">
                        <div class="counter__item-icon">
                            @php echo @$counter->data_values->icon @endphp
                        </div>
                        <div class="counter__item-content">
                            <h2 class="title"><span class="odometer" data-odometer-final="{{@$counter->data_values->digit }}"></span>{{ @$counter->data_values->digit_postfix }}
                            </h2>
                            <p class="info">{{ __(@$counter->data_values->title) }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

@push('style')
    <style>
        .counter-section{
            background: white !important;
        }
    </style>
@endpush
