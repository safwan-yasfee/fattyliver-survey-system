@extends('layouts.patients')

@section('content')
    <header class="page-heading">
        <div>
            <p class="eyebrow">
                {{ request()->routeIs('dashboard.research*')
                    ? 'Research workspace'
                    : 'Clinical workspace' }}
            </p>

            <h1>@yield('dashboard-title', 'Dashboard')</h1>

            <p class="page-description">
                Welcome, {{ auth()->user()->name }}.
            </p>
        </div>

        <nav class="d-flex flex-wrap gap-2"
             aria-label="Dashboard navigation">
            <a href="{{ route('dashboard') }}"
               class="btn {{ request()->routeIs('dashboard') ? 'btn-primary' : 'btn-outline-secondary' }}"
               @if(request()->routeIs('dashboard')) aria-current="page" @endif>
                Clinical Dashboard
            </a>

            <a href="{{ route('dashboard.research') }}"
               class="btn {{ request()->routeIs('dashboard.research*') ? 'btn-research' : 'btn-outline-secondary' }}"
               @if(request()->routeIs('dashboard.research*')) aria-current="page" @endif>
                Research Dashboard
            </a>
        </nav>
    </header>

    @yield('dashboard-content')
@endsection