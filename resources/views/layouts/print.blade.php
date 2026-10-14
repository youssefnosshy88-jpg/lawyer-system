<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - {{ config('firm.name') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Cairo', system-ui, sans-serif; color: #111827; margin: 0; padding: 32px; font-size: 14px; background: #fff; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #4f46e5; padding-bottom: 16px; margin-bottom: 24px; }
        .firm { font-size: 22px; font-weight: 700; color: #4f46e5; }
        .muted { color: #6b7280; font-size: 12px; }
        .title { font-size: 20px; font-weight: 700; margin: 0 0 4px; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 24px; }
        .box { border: 1px solid #e5e7eb; border-radius: 8px; padding: 12px 16px; }
        .box h4 { margin: 0 0 8px; font-size: 13px; color: #6b7280; text-transform: uppercase; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        th, td { padding: 10px 12px; border-bottom: 1px solid #e5e7eb; text-align: start; }
        th { background: #f3f4f6; font-weight: 600; font-size: 13px; }
        .num { text-align: end; white-space: nowrap; }
        .totals { width: 320px; margin-inline-start: auto; }
        .totals td { border: none; padding: 6px 12px; }
        .totals .grand td { border-top: 2px solid #111827; font-weight: 700; font-size: 16px; }
        .badge { display: inline-block; padding: 2px 10px; border-radius: 999px; font-size: 12px; background: #eef2ff; color: #4f46e5; font-weight: 600; }
        .footer { margin-top: 40px; padding-top: 12px; border-top: 1px solid #e5e7eb; font-size: 12px; color: #6b7280; display: flex; justify-content: space-between; }
        .sign { margin-top: 48px; display: flex; justify-content: space-between; }
        .sign div { width: 40%; border-top: 1px dashed #9ca3af; padding-top: 8px; text-align: center; }
        @media print { body { padding: 0; } .no-print { display: none; } }
        .no-print { margin-bottom: 16px; }
        .btn { background: #4f46e5; color: #fff; border: 0; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-family: inherit; }
    </style>
</head>
<body>
    <div class="no-print"><button class="btn" onclick="window.print()">{{ __('app.actions.print') }}</button></div>
    <div class="header">
        <div>
            <div class="firm">{{ config('firm.name') }}</div>
            <div class="muted">{{ config('mail.from.address') }}</div>
        </div>
        <div style="text-align: end">
            <p class="title">@yield('title')</p>
            @yield('header-meta')
        </div>
    </div>
    @yield('content')
    <div class="footer">
        <span>{{ config('firm.name') }}</span>
        <span>{{ now()->format('Y-m-d H:i') }}</span>
    </div>
</body>
</html>
