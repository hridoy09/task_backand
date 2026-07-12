@extends('theme::user.layouts.main')

@section('content')
<div class="container" style="max-width:480px">
  <h3 class="mb-3">Two-Factor Challenge</h3>

  @if ($errors->any())
    <div class="alert alert-danger">{{ $errors->first() }}</div>
  @endif

  <form method="POST" action="{{ route('2fa.verify') }}" class="mb-3">
    @csrf
    <div class="mb-3">
      <label class="form-label">Authenticator Code</label>
      <input class="form-control" name="code" inputmode="numeric" autocomplete="one-time-code">
    </div>
    <div class="form-check mb-3">
      <input class="form-check-input" type="checkbox" id="remember_device" name="remember_device" value="1">
      <label class="form-check-label" for="remember_device">Remember this device for 30 days</label>
    </div>
    <button class="btn btn-primary w-100">Verify</button>
  </form>

  <div class="text-center text-muted my-2">— OR —</div>

  <form method="POST" action="{{ route('2fa.verify') }}">
    @csrf
    <div class="mb-3">
      <label class="form-label">Recovery Code</label>
      <input class="form-control" name="recovery_code">
    </div>
    <button class="btn btn-outline-secondary w-100">Use Recovery Code</button>
  </form>
</div>
@endsection
