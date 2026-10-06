@php
    $manifestPath = public_path('build/manifest.json');
    $manifest = [];
    if (file_exists($manifestPath)) {
        $manifest = json_decode(file_get_contents($manifestPath), true) ?: [];
    }
    $cssFile = $manifest['resources/css/app.css']['file'] ?? null;
    $jsFile = $manifest['resources/js/app.js']['file'] ?? null;
@endphp

@if(file_exists(public_path('hot')))
    @vite(['resources/css/app.css', 'resources/js/app.js'])
@elseif($cssFile && $jsFile)
    <link rel="stylesheet" href="{{ asset('build/' . $cssFile) }}">
    <script type="module" src="{{ asset('build/' . $jsFile) }}"></script>
@else
    @vite(['resources/css/app.css', 'resources/js/app.js'])
@endif
