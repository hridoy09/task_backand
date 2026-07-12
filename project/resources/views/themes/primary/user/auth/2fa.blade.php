@extends('theme::user.layouts.main')

@section('content')
<div class="container" style="max-width:760px">
  <h3 class="mb-4">Two-Factor Authentication</h3>

  @if (session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
  @endif
  @error('code') <div class="alert alert-danger">{{ $message }}</div> @enderror

  @if (! $user->two_factor_secret)
    @if (! $pendingSecret)
      <form method="POST" action="{{ route('user.2fa.start') }}" class="mb-4">
        @csrf
        <div class="mb-3">
          <label class="form-label">Confirm Password</label>
          <input type="password" class="form-control" name="confirm_password" required>
        </div>
        <button class="btn btn-primary">Enable 2FA</button>
      </form>
      <p class="text-muted">এক্টিভ করলে লগইনে এক্সট্রা ৬-ডিজিট কোড লাগবে।</p>
    @else
      <div class="mb-3">
        <h6>Step 1: Scan QR</h6>
        <div class="border rounded p-3">{!! $qrSvg !!}</div>
        <p class="mt-2 small text-muted">If needed, manual key: <code>{{ $pendingSecret }}</code></p>
      </div>
      <form method="POST" action="{{ route('user.2fa.confirm') }}" class="mb-4">
        @csrf
        <div class="mb-3">
          <label class="form-label">Enter 6-digit Code</label>
          <input class="form-control" name="code" inputmode="numeric" autocomplete="one-time-code" required>
        </div>
        <button class="btn btn-success">Confirm & Activate</button>
      </form>
    @endif
  @else
    <div class="alert alert-success">
      2FA is <strong>enabled</strong> {{ $user->two_factor_confirmed_at ? 'since '.$user->two_factor_confirmed_at->format('Y-m-d H:i') : '' }}.
    </div>

    <h6 class="mt-4">Recovery Codes</h6>
    <ul class="small">
      @foreach ($recovery as $c)
        <li><code>{{ $c }}</code></li>
      @endforeach
    </ul>
    <form method="POST" action="{{ route('user.2fa.recovery.regen') }}" class="mb-3">
      @csrf
      <div class="mb-2">
        <label class="form-label">Confirm Password</label>
        <input type="password" class="form-control" name="confirm_password" required>
      </div>
      <button class="btn btn-outline-secondary">Regenerate Recovery Codes</button>
    </form>

    <form method="POST" action="{{ route('user.2fa.disable') }}">
      @csrf @method('DELETE')
      <div class="mb-2">
        <label class="form-label">Confirm Password</label>
        <input type="password" class="form-control" name="confirm_password" required>
      </div>
      <button class="btn btn-danger">Disable 2FA</button>
    </form>
  @endif
</div>
@endsection
