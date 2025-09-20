{{-- @php
    $contacElement = getContent('contact_us.element', false, 2, true);
    $socialElement = getContent('social_icon.element', orderById: true);
    $language = \App\Models\Language::all();
    $selectedLang = $language->where('code', session('lang'))->first();
@endphp
<div class="header-top">
    <div class="container">
        <div
            class="header__top__wrapper d-flex flex-wrap justify-content-center justify-content-lg-between align-items-center">
            <div class="header__top__wrapper-left">
                <ul class="contacts  d-flex flex-wrap justify-content-center m-0">
                    @foreach ($contacElement as $contact)
                        <li>
                            <a href="{{ @$contact->data_values->attribute }}{{ $contact->data_values->content }}">
                                @php echo $contact->data_values->icon @endphp
                                {{ __($contact->data_values->content) }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            @auth
                <a class="btn btn--base btn--round btn--sm me-3 text-white d-none d-sm-block" data-bs-toggle="tooltip"
                    data-bs-placement="bottom" title="Click to Switch User Type" href="{{ route('user.switchrole') }}">Switch
                    to
                    @if (Auth::user()->role == 1)
                        Employer
                    @else
                        Employee
                    @endif
                </a>
            @endauth

            <div class="header__top__wrapper-right d-flex align-items-center">
                
                <ul class="social-links m-0 me-3">
                    @foreach ($socialElement as $social)
                        <li>
                            <a href="{{ @$social->data_values->url }}" target="__blank">
                                @php echo @$social->data_values->icon @endphp
                            </a>
                        </li>
                    @endforeach
                </ul>
                @if (gs('multi_language'))
                    <div class="language dropdown">
                        <button class="language-wrapper" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="language-content">
                                <div class="language_flag">
                                    <img src="{{ getImage(getFilePath('language') . '/' . @$selectedLang->image, getFileSize('language')) }}"
                                        alt="flag">
                                </div>
                                <p class="language_text_select">{{ __(@$selectedLang->name) }}</p>
                            </div>
                            <span class="collapse-icon"><i class="las la-angle-down"></i></span>
                        </button>
                        <div class="dropdown-menu langList_dropdow py-2">
                            <ul class="langList">
                                @foreach ($language as $item)
                                    <li class="language-list langSel" data-code="{{ $item->code }}">
                                        <div class="language_flag">
                                            <img src="{{ getImage(getFilePath('language') . '/' . $item->image, getFileSize('language')) }}"
                                                alt="flag">
                                        </div>
                                        <p class="language_text">{{ $item->name }}</p>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('script')
    <script>
        (function($) {
            "use strict";
            $(".langSel").on("click", function() {
                window.location.href = "{{ route('home') }}/change/" + $(this).data('code');
            });
        })(jQuery);
    </script>
@endpush --}}
