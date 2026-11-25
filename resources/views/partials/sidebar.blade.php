<aside class="ehr-sidebar offcanvas-lg offcanvas-start"
       tabindex="-1"
       id="appSidebar"
       aria-labelledby="sidebarTitle">

    <div class="offcanvas-header">
        <h2 class="offcanvas-title h6" id="sidebarTitle">
            Navigation
        </h2>

        <button type="button"
                class="btn-close btn-close-white"
                data-bs-dismiss="offcanvas"
                data-bs-target="#appSidebar"
                aria-label="Close navigation"></button>
    </div>

    <div class="offcanvas-body">
        <a href="{{ route('dashboard') }}" class="sidebar-brand">
            <span class="brand-symbol">
                <i class="bi bi-heart-pulse" aria-hidden="true"></i>
            </span>

            <span>
                <strong>Fatty Liver</strong>
                <small>Research &amp; Management</small>
            </span>
        </a>

        <p class="sidebar-caption">Workspaces</p>

        <nav class="sidebar-nav" aria-label="Main navigation">
            <a href="{{ route('dashboard') }}"
               class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
               @if(request()->routeIs('dashboard')) aria-current="page" @endif>
                <i class="bi bi-grid" aria-hidden="true"></i>
                Clinical Dashboard
            </a>

            <a href="{{ route('dashboard.research') }}"
               class="sidebar-link {{ request()->routeIs('dashboard.research') ? 'active' : '' }}"
               @if(request()->routeIs('dashboard.research')) aria-current="page" @endif>
                <i class="bi bi-bar-chart" aria-hidden="true"></i>
                Research Dashboard
            </a>

            <p class="sidebar-caption">Patient records</p>

            <a href="{{ route('patients.index') }}"
               class="sidebar-link {{ request()->routeIs('patients.index', 'patients.show', 'patients.edit', 'patients.visits.*', 'patients.medical-history.*', 'patients.lifestyle.*') ? 'active' : '' }}">
                <i class="bi bi-people" aria-hidden="true"></i>
                Patient List
            </a>

            <a href="{{ route('patients.create') }}"
               class="sidebar-link {{ request()->routeIs('patients.create') ? 'active' : '' }}">
                <i class="bi bi-person-plus" aria-hidden="true"></i>
                Add Patient
            </a>

            @can('archive-patients')
                <a href="{{ route('patients.archived') }}"
                   class="sidebar-link {{ request()->routeIs('patients.archived') ? 'active' : '' }}">
                    <i class="bi bi-archive" aria-hidden="true"></i>
                    Archived Patients
                </a>
            @endcan

            <p class="sidebar-caption">Research</p>

            <a href="{{ route('dashboard.research.patients') }}"
               class="sidebar-link {{ request()->routeIs('dashboard.research.patients') ? 'active' : '' }}">
                <i class="bi bi-collection" aria-hidden="true"></i>
                Research Patient List
            </a>

            @can('manage-users')
                <p class="sidebar-caption">Administration</p>

                <a href="{{ route('admin.users.index') }}"
                   class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="bi bi-person-gear" aria-hidden="true"></i>
                    User Management
                </a>

                <a href="{{ route('admin.consent-versions.index') }}"
                   class="sidebar-link {{ request()->routeIs('admin.consent-versions.*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-text" aria-hidden="true"></i>
                    Consent Versions
                </a>
            @endcan
        </nav>

        <div class="sidebar-bottom">
            <i class="bi bi-shield-check" aria-hidden="true"></i>

            <div>
                <strong>Protected workspace</strong>
                <small>{{ auth()->user()->role?->name ?? 'Clinical team' }}</small>
            </div>
        </div>
    </div>
</aside>