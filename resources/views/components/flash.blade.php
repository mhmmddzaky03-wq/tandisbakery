@if (session('success'))
    <div class="sr-only" role="status" data-flash-success>{{ session('success') }}</div>
@endif

@if (session('error'))
    <div class="sr-only" role="alert" data-flash-error>{{ session('error') }}</div>
@endif

@if ($errors->any())
    @php
        $flashError = $errors->first('materials')
            ?? collect($errors->getMessages())
                ->filter(fn ($_, $key) => str_starts_with($key, 'materials.'))
                ->flatten()
                ->first()
            ?? $errors->first();
    @endphp
    <div class="sr-only" role="alert" data-flash-error>{{ $flashError }}</div>
@endif
