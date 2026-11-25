@php
    $isPreview = request()->routeIs('interface.*');

    $researchWorkspace = request()->routeIs(
        'dashboard.research*',
        'interface.research*',
        'patients.eligibility.*',
        'patients.consents.*',
        'admin.consent-versions.*'
    );

    $successMessages = collect([
        session('success'),
        session('visit_success'),
    ])->filter()->unique();
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') | Fatty Liver Research</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap"
          rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.9/dist/css/bootstrap.min.css"
          rel="stylesheet"
          integrity="sha384-yEPZ7jP9S0QKiUQ0m3jjsO4ProIW7wvR9gXkI7JOGa2XVsw53Yke2bsdkBbjmmiX"
          crossorigin="anonymous">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
          rel="stylesheet">

    <link href="{{ asset('css/base.css') }}" rel="stylesheet">

    @if ($researchWorkspace)
        <link href="{{ asset('css/research.css') }}" rel="stylesheet">
    @else
        <link href="{{ asset('css/clinical.css') }}" rel="stylesheet">
    @endif

    @stack('styles')
</head>

<body class="{{ $researchWorkspace ? 'workspace-research' : 'workspace-clinical' }} {{ $isPreview ? '' : 'live-interface' }}">
    <a href="#main-content" class="skip-link">
        Skip to content
    </a>

    @include('partials.sidebar')

    <div class="ehr-shell">
        @include('partials.navbar')

        <main id="main-content" class="ehr-main" tabindex="-1">
            @include('partials.breadcrumbs')

            @if ($isPreview)
                <div class="preview-label mb-3">
                    <i class="bi bi-info-circle" aria-hidden="true"></i>
                    Interface preview · synthetic sample data
                </div>
            @endif

            @foreach ($successMessages as $message)
                <div class="alert alert-success" role="status">
                    {{ $message }}
                </div>
            @endforeach

            @if (session('error'))
                <div class="alert alert-danger" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger" role="alert">
                    <strong>Please check the following:</strong>

                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>

        <footer class="ehr-footer">
            <span>
                <i class="bi bi-shield-lock" aria-hidden="true"></i>
                Confidential – for medical use only
            </span>

            <span>Fatty Liver Research &amp; Management System</span>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.9/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-WyOxWvTyr1QaMPfiPJSv76iReVthorl5uWlFtZPyK0IEB0hGzo9y5Bp9mYanr8Bu"
            crossorigin="anonymous"></script>

    @stack('scripts')
</body>
</html>