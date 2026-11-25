@php
    $viewer = auth()->user();
@endphp

<header class="ehr-topbar">
    <button class="btn icon-button d-lg-none"
            type="button"
            data-bs-toggle="offcanvas"
            data-bs-target="#appSidebar"
            aria-controls="appSidebar"
            aria-label="Open navigation">
        <i class="bi bi-list" aria-hidden="true"></i>
    </button>

    <form class="global-search"
          method="GET"
          action="{{ route('patients.index') }}"
          role="search">
        <i class="bi bi-search" aria-hidden="true"></i>

        <label for="global-search" class="visually-hidden">
            Search patients by code, name or phone
        </label>

        <input id="global-search"
               name="search"
               type="search"
               maxlength="255"
               value="{{ request()->routeIs('patients.index') ? request('search') : '' }}"
               placeholder="Search patients by code, name or phone">

        <button type="submit" class="search-submit">
            Search
        </button>
    </form>

    <div class="topbar-tools">
        <span class="date-stamp">
            <i class="bi bi-calendar3" aria-hidden="true"></i>
            {{ now()->format('d M Y') }}
        </span>

        <div class="dropdown">
            <button class="btn icon-button notification-button"
                    type="button"
                    data-bs-toggle="dropdown"
                    aria-expanded="false"
                    aria-label="Clinical alerts">
                <i class="bi bi-bell" aria-hidden="true"></i>

                @if (isset($openAlertCount) && $openAlertCount > 0)
                    <span class="notification-dot"></span>
                @endif
            </button>

            <div class="dropdown-menu dropdown-menu-end notification-menu">
                <h2 class="dropdown-header">Clinical alerts</h2>

                @if (isset($openAlertCount))
                    <p class="px-3 small text-secondary">
                        {{ $openAlertCount }} open alerts
                    </p>
                @endif

                <a class="dropdown-item" href="{{ route('dashboard') }}">
                    <i class="bi bi-clipboard2-pulse me-2"
                       aria-hidden="true"></i>
                    Open Clinical Dashboard
                </a>
            </div>
        </div>

        <div class="dropdown">
            <button class="doctor-menu dropdown-toggle"
                    type="button"
                    data-bs-toggle="dropdown"
                    aria-expanded="false">
                <span class="doctor-avatar">
                    <i class="bi bi-person" aria-hidden="true"></i>
                </span>

                <span class="doctor-summary">
                    <strong>{{ $viewer->name }}</strong>
                    <small>{{ $viewer->role?->name ?? 'Clinical team' }}</small>
                </span>
            </button>

            <div class="dropdown-menu dropdown-menu-end">
                <div class="px-3 py-2">
                    <strong class="d-block">{{ $viewer->name }}</strong>

                    <small class="text-secondary">
                        {{ $viewer->email }}
                    </small>

                    <small class="d-block text-secondary">
                        {{ $viewer->role?->name ?? 'Clinical team' }}
                    </small>
                </div>

                <hr class="dropdown-divider">

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button class="dropdown-item" type="submit">
                        <i class="bi bi-box-arrow-right me-2"
                           aria-hidden="true"></i>
                        Sign out
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>