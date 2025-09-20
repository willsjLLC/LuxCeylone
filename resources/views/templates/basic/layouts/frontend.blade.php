@extends($activeTemplate . 'layouts.app')
@section('app')
    @include($activeTemplate . 'partials.topbar')

    @if (request()->routeIs('user.product.*')  || request()->routeIs('user.order.*') )
        @include($activeTemplate . 'partials.product_header')
    @elseif (request()->routeIs('user.*') || request()->routeIs('ticket.*')|| request()->routeIs('user.cart.*') || request()->routeIs('user.advertisement.*'))
        @include($activeTemplate . 'partials.user_header')
    @else
        @include($activeTemplate . 'partials.header')
    @endif
    {{-- @if (!request()->routeIs('home'))
        @include($activeTemplate . 'partials.breadcrumb')
    @endif --}}
    @yield('content')
    {{-- @if (!request()->routeIs('user.*'))
        @include($activeTemplate . 'partials.get_started')
    @endif --}}
   {{-- @if (request()->routeIs('user.*') || request()->routeIs('ticket.*') || request()->routeIs('advertisement.*'))
        @include($activeTemplate . 'partials.footer')
    @endif --}}

@endsection
