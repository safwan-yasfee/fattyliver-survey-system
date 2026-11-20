@extends('layouts.patients')

@section('content')
    <nav class="d-flex flex-wrap gap-2 mb-4"
         aria-label="Dashboard navigation">
        <a
            href="{{ route('dashboard') }}"
            class="btn {{ request()->routeIs('dashboard') ? 'btn-primary' : 'btn-outline-secondary' }}"
        >
            Clinical Dashboard
        </a>

        <a
            href="{{ route('dashboard.research') }}"
            class="btn {{ request()->routeIs('dashboard.research') ? 'btn-primary' : 'btn-outline-secondary' }}"
        >
            Research Dashboard
        </a>
    </nav>

    <header class="mb-4">
        <h1 class="h2 fw-bold">@yield('dashboard-title')</h1>

        <p class="text-secondary mb-0">
            Welcome, {{ auth()->user()->name }}.
        </p>
    </header>

    @yield('dashboard-content')
@endsection