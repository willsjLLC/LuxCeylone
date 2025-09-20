@php
    $socialElement = getContent('social_icon.element', orderById: true);
    $policyPages = getContent('policy_pages.element', orderById: true);
    $footerContent = getContent('footer.content', true);
    $pages = App\Models\Page::where('tempname', $activeTemplate)->where('is_default', Status::NO)->get();
@endphp

<footer class="footer-section bg_img bg_fixed">
    <div class="footer-top">
        <div class="container">
            <div class="row justify-content-between gy-2">
                <div class="text-center col-xl-5 col-lg-6 col-md-12 col-sm-12 text-md-start">
                    <div class="  widget-about">
                        <div class="logo">
                            <a href="{{ route('home') }}">
                                <img src="{{ siteLogo() }}" alt="@lang('logo')" class="img-fluid"
                                    style="max-height: 50px;">
                            </a>
                        </div>
                        <ul class="flex-wrap mt-2 social-links d-flex justify-content-center justify-content-md-start">
                            <div class="mb-2 separator"></div>
                            @foreach ($socialElement as $social)
                                <li>
                                    <a href="{{ $social->data_values->url }}" target="__blank" style="color:#fff">
                                        @php echo $social->data_values->icon @endphp
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="mt-2 text-center col-xl-3 col-lg-3 col-md-6 col-sm-6 mt-md-0 text-md-start">
                    <div class=" ">
                        <h4 class="widget-title">@lang('About Company')</h4>
                        <div class="separator red-line"></div>
                        <ul class="footer-links">
                            <li><a href="{{ route('ads.index') }}">@lang('Advertisements')</a></li>
                            @foreach ($pages as $page)
                                <li>
                                    <a href="{{ route('pages', [$page->slug]) }}">
                                        {{ __($page->name) }}
                                    </a>
                                </li>
                            @endforeach
                            <li><a href="{{ route('contact') }}">@lang('Contact Us')</a></li>
                        </ul>
                    </div>
                </div>
                <div class="mt-2 text-center col-xl-3 col-lg-3 col-md-6 col-sm-6 mt-md-0 text-md-start">
                    <div class=" ">
                        <h4 class="widget-title">@lang('Policy Pages')</h4>
                        <div class="separator red-line"></div>
                        <ul class="footer-links">
                            @foreach ($policyPages as $page)
                                <li>
                                    <a href="{{ route('policy.pages', $page->slug) }}">
                                        {{ $page->data_values->title }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</footer>

<style>
    /* General Footer Styling */
    .footer-section {
        background: #0e1a2b; /* dark elegant bg */
        color: #fff;
        position: relative;
    }
    
    .footer-top {
        padding: 40px 0 20px;
    }
    
    .footer-bottom {
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        padding: 15px 0;
        background: #000000;
    }
    
    /* Widget Title and Underline */
    .widget-title {
        font-size: 18px;
        font-weight: 600;
        color: #ffffff;
        margin-bottom: 10px;
        position: relative;
    }
    
    .separator.red-line {
        width: 50px;
        height: 3px;
        background-color: #e74c3c; /* vivid red */
        margin: 0 auto 15px;
        border-radius: 2px;
    }
    
    @media (min-width: 768px) {
        .separator.red-line {
            margin-left: 0;
            margin-right: 0;
        }
    }
    
    /* Links */
    .footer-links {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .footer-links li {
        margin-bottom: 8px;
    }
    
    .footer-links a {
        color: #bbb;
        text-decoration: none;
        transition: color 0.3s ease;
    }
    
    .footer-links a:hover {
        color: #fff;
    }
    
    /* Social Icons */
    .social-links {
        padding: 0;
        margin: 15px 0;
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        justify-content: center;
    }
    
    .social-links li {
        list-style: none;
    }
    
    .social-links a {
        color: #fff;
        font-size: 18px;
        transition: transform 0.3s ease, color 0.3s ease;
    }
    
    .social-links a:hover {
        transform: scale(1.1);
        color: #e74c3c;
    }
    
    /* Logo */
    .footer-section .logo img {
        max-height: 60px;
    }
    
    /* Responsive tweaks */
    @media (max-width: 767px) {
        .widget-title {
            font-size: 16px;
        }
    
        .footer-section .footer-top {
            padding: 20px 0 10px;
        }
    
        .social-links {
            justify-content: center;
        }
    
        .footer__bottom__wrapper {
            flex-direction: column;
            text-align: center;
        }
    }

    .company-text-color{
        color: rgb(99, 99, 99) !important;
    }

    .company-link-color{
        text-decoration: underline;
        color: rgb(51, 51, 51) !important;
    }

    .company-link-color:hover{
        color: rgb(146, 146, 146) !important;
    }
    </style>
    
