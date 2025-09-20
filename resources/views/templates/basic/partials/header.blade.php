@php
    $pages = App\Models\Page::where('tempname', $activeTemplate)->where('is_default', Status::NO)->get();
@endphp
<header class="header-bottom fixed-top" style="background-color: #17433c">
    <div class="container ">
        <div class="header-bottom-area">

            <div class="flex-row d-flex flex-start align-items-center position-relative">

                <!-- Logo -->
                <div class="logo position-absolute">
                    <a href="{{ route('home') }}">
                        <img src="{{ siteLogo() }}" alt="@lang('logo')" class="img-fluid">
                    </a>
                </div>


            </div>
            <ul class="menu">
                <li><a href="{{ route('home') }}"
                        class="{{ request()->routeIs('home') ? 'active' : '' }}">@lang('Home')</a></li>
                @foreach ($pages as $page)
                    <li>
                        <a href="{{ route('pages', [$page->slug]) }}"
                            class="{{ request()->routeIs('pages') ? 'active' : '' }}">{{ __($page->name) }}</a>
                    </li>
                @endforeach
                <li><a href="{{ route('ads.index') }}"
                        class="{{ request()->routeIs('ads.index') ? 'active' : '' }}">@lang('Ads')</a></li>
                <li><a href="{{ route('contact') }}"
                        class="{{ request()->routeIs('contact') ? 'active' : '' }}">@lang('Contact Us')</a></li>
                <li class="p-0">
                    @guest
                        <a class="my-2 text-white btn btn--base btn--round btn--md ms-2 my-lg-0"
                            href="{{ route('user.login') }}">@lang('Login')
                        </a>
                        <a class="text-white btn btn--base btn--round btn--md ms-2"
                            href="{{ route('user.register') }}">@lang('Register')
                        </a>
                    @else
                        <a class="my-2 text-white btn btn--base btn--round btn--md ms-2 my-lg-0"
                            href="{{ route('user.product.index') }}">@lang('Dashboard')
                        </a>
                        <a class="text-white btn btn--base btn--round btn--md ms-2"
                            href="{{ route('user.logout') }}">@lang('Logout')
                        </a>
                    @endguest

                </li>
            </ul>
            <div class="header-trigger-wrapper d-flex d-lg-none align-items-center">
                <div class="header-trigger d-block d--none">
                    <span></span>
                </div>
            </div>

        </div>
    </div>
</header>

@push('style')
    <style>
        .img-fluid {
            height: 55px;
        }
        @media(min-width: 1200px) {
            .img-fluid {
                height: 80px;
            }
            .container {
                max-width: 1250px !important;
            }
        }

        .menu li a {
            color: #d9e4d4;
        }

        .menu li a:hover {
            color: #fff !important;
        }

        .menu li a.active {
            color: #fff !important;
        }

        .menu.active {
            background-color: rgb(0, 88, 77);
        }
    </style>
@endpush
