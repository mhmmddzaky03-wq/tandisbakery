@if (session('success'))
    <div class="sr-only" role="status" data-flash-success>{{ session('success') }}</div>
@endif

@if ($errors->any())
    <div class="sr-only" role="alert" data-flash-error>{{ $errors->first() }}</div>
@endif
