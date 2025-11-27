<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('admin_panel/assets/img/apple-icon.png') }}">
    <link rel="icon" type="image/png" href="{{ asset('admin_panel/assets/img/favicon.png') }}">
    <title>
        @yield('title') || Mriduukriti
    </title>
    @include('admin_panel.layouts.css_link')

</head>

<body class="g-sidenav-show  bg-gray-100">

    @include('admin_panel.layouts.sidebar')
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        @include('admin_panel.layouts.navbar')
        <div class="container-fluid py-2">
            @yield('content')
            @include('admin_panel.layouts.footer')
        </div>
    </main>
    @include('admin_panel.layouts.script_link')
    @stack('scripts')

    <script>
        $(document).ready(function() {
            @if (session('success'))
                toastr.success(@json(session('success')));
            @endif

            @if (session('error'))
                toastr.error(@json(session('error')));
            @endif

            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    toastr.error(@json($error));
                @endforeach
            @endif

        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>
</body>

</html>
