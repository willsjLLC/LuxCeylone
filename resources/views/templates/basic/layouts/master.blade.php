@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="dashboard-section padding-bottom">
        <div class="container">
            <div class="row">
                {{-- @if (request()->routeIs('user.wallet')) --}}
                    {{-- @include($activeTemplate . 'partials.sidebar') --}}
                {{-- @endif --}}
                <div class="col-lg-12 col-xl-12">
                    @include($activeTemplate . 'partials.responsive_header')
                    @yield('panel')
                </div>
            </div>
        </div>
    </section>
@endsection
