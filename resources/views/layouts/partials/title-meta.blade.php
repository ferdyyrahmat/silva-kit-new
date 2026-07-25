@php
    $appName = \App\Models\SystemSetting::getByKey('app_name', config('app.name', 'Silva Kit'));
    $metaDesc = \App\Models\SystemSetting::getByKey('meta_description', 'Silva Kit Enterprise Admin Management Platform');
    $metaKeywords = \App\Models\SystemSetting::getByKey('meta_keywords', 'silva kit, admin dashboard, enterprise');
    $metaAuthor = \App\Models\SystemSetting::getByKey('meta_author', 'Silva Team');
    $appFavicon = \App\Models\SystemSetting::getByKey('app_favicon', '/images/favicon.ico');
@endphp
<meta charset="utf-8" />
<title>{{ $title ?? 'Silva' }} | {{ $appName }}</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="description" content="{{ $metaDesc }}" />
<meta name="keywords" content="{{ $metaKeywords }}" />
<meta name="author" content="{{ $metaAuthor }}" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />

<!-- App favicon -->
<link rel="shortcut icon" href="{{ $appFavicon }}">