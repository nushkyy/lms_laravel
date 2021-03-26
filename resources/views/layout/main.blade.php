@include('layout.header')
@include('layout.navigation')
<div class="content-wrapper">
    <div class="container-fluid-no" style="overflow: hidden;">

        @yield('content')

    </div>
</div>
@include('layout.footer')