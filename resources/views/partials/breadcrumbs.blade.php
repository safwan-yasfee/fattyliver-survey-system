<nav aria-label="Breadcrumb" class="page-breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item">
            <a href="{{ route('dashboard') }}">
                <i class="bi bi-house-door" aria-hidden="true"></i>
                <span class="visually-hidden">Home</span>
            </a>
        </li>

        @if (request()->routeIs('dashboard'))
            <li class="breadcrumb-item active" aria-current="page">
                Clinical Dashboard
            </li>

        @elseif (request()->routeIs('dashboard.research*'))
            @if (! request()->routeIs('dashboard.research'))
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard.research') }}">
                        Research Dashboard
                    </a>
                </li>
            @endif

            <li class="breadcrumb-item active" aria-current="page">
                @yield('title', 'Research')
            </li>

        @elseif (request()->routeIs('patients.*'))
            @if (! request()->routeIs('patients.index'))
                <li class="breadcrumb-item">
                    <a href="{{ route('patients.index') }}">
                        Patient List
                    </a>
                </li>
            @endif

            <li class="breadcrumb-item active" aria-current="page">
                @yield('title', 'Patient Record')
            </li>

        @else
            <li class="breadcrumb-item active" aria-current="page">
                @yield('title', 'Page')
            </li>
        @endif
    </ol>
</nav>