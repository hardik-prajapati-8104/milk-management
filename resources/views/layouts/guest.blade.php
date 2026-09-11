<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="{{ config('languages.supported.'.app()->getLocale().'.font_class', 'lang-latin') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Login') - {{ setting('company_name', config('app.name')) }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Noto+Sans+Gujarati:wght@400;500;600;700&family=Noto+Sans+Devanagari:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #1b5e20, #2e7d32 60%, #66bb6a);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }
        html.lang-gujarati body { font-family: 'Noto Sans Gujarati', 'Inter', sans-serif; }
        html.lang-devanagari body { font-family: 'Noto Sans Devanagari', 'Inter', sans-serif; }
        .auth-card { border: 0; border-radius: 1rem; max-width: 420px; width: 100%; }
        .lang-switch { position: fixed; top: 1rem; right: 1rem; }
    </style>
</head>
<body>
    <div class="dropdown lang-switch">
        <button class="btn btn-sm btn-light d-flex align-items-center gap-1" data-bs-toggle="dropdown">
            <i class="bi bi-translate"></i>
            <span>{{ config('languages.supported.'.app()->getLocale().'.native') }}</span>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
            @foreach(config('languages.supported') as $code => $lang)
                <li>
                    <a class="dropdown-item d-flex align-items-center gap-2 {{ app()->getLocale() === $code ? 'active' : '' }}"
                       href="{{ route('language.switch', $code) }}">
                        <span>{{ $lang['flag'] }}</span>
                        <span>{{ $lang['native'] }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
    <div class="card auth-card shadow-lg">
        <div class="card-body p-4 p-md-5">
            <div class="text-center mb-4">
                <i class="bi bi-droplet-half text-success" style="font-size:2.5rem"></i>
                <h4 class="mt-2 mb-0">{{ setting('company_name', 'Milk Dairy ERP') }}</h4>
                <p class="text-muted small">@yield('subtitle', 'Sign in to your account')</p>
            </div>
            @yield('content')
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
