@extends('layouts.auth')

@section('title', 'Authentication — Learning Curve')

@section('content')
<div class="mt-2">
    <!-- Container where active form is rendered -->
    <div class="w-full">
        <div id="login-tab-content">
            @include('auth.login-content')
        </div>
        <div id="register-tab-content" class="hidden">
            @include('auth.register-content')
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto-switch to register tab if coming from /register route or if register fields have old input
    @if(request()->is('register') || old('name'))
        document.addEventListener('DOMContentLoaded', function() {
            switchAuthTab('register');
        });
    @endif
</script>
@endpush
