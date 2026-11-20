<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') | Fatty Liver Research</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
    <style>
        body { background: #edf4f3; color: #123047; }
        .navbar { background: #123047; }
        .page-header { display: flex; justify-content: space-between;
            align-items: center; flex-wrap: wrap; gap: 16px; margin: 28px 0; }
        .card { padding: 24px; border: 1px solid #dce7e7; border-radius: 16px; }
        .button { display: inline-block; padding: 9px 16px; border-radius: 8px;
            background: #087c6c; border: 1px solid #087c6c; color: white;
            text-decoration: none; cursor: pointer; }
        .button-secondary { background: white; color: #087c6c; }
        .muted { color: #576d78; }
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 13px; border-bottom: 1px solid #dce7e7; }
        th { background: #f5f9f8; }
        a { color: #087c6c; }
        .pagination { display: flex; justify-content: space-between;
            align-items: center; gap: 12px; flex-wrap: wrap; margin-top: 20px; }
    </style>
</head>
<body>
<nav class="navbar navbar-dark">
    <div class="container d-flex justify-content-between gap-3">
        <a class="navbar-brand text-wrap" href="{{ route('dashboard') }}">
            Fatty Liver Research
        </a>
        <div class="d-flex align-items-center gap-3">
            <a class="text-white" href="{{ route('dashboard') }}">Dashboard</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="btn btn-outline-light btn-sm" type="submit">Logout</button>
            </form>
        </div>
    </div>
</nav>
<main class="container py-3 pb-5">
    @if (session('success'))
        <div class="alert alert-success" role="status">{{ session('success') }}</div>
    @endif
    @if (session('visit_success'))
        <div class="alert alert-success" role="status">{{ session('visit_success') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @yield('content')
</main>
</body>
</html>
