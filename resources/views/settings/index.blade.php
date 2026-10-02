@extends('layouts.app')
@section('content')
<div class="container mt-4" style="max-width:700px;">
  <h2 class="text-white mb-3">Settings</h2>

  @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
  @if($errors->any()) <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div> @endif

  <div class="bubble mb-3">
    <div class="p-4" style="border-bottom:1px solid rgba(255,255,255,0.06);">
      <h5 class="text-white mb-3" style="font-weight:700;">My Password</h5>
      <form method="POST" action="{{ route('settings.password.update.own') }}">
        @csrf
        <div class="row g-3">
          <div class="col-md-6"><label class="form-label">Current Password</label><input type="password" name="current_password" class="form-control" required></div>
          <div class="col-md-6"><label class="form-label">New Password</label><input type="password" name="password" class="form-control" required><small style="color:rgba(255,255,255,0.35);">Minimum 8 characters.</small></div>
          <div class="col-md-6"><label class="form-label">Confirm New Password</label><input type="password" name="password_confirmation" class="form-control" required></div>
        </div>
        <div class="mt-3"><button type="submit" class="btn btn-primary">Update Password</button></div>
      </form>
    </div>

    @if(auth()->user() && strtolower(auth()->user()->role) === 'admin')
    <div class="p-4">
      <h5 class="text-white mb-3" style="font-weight:700;">Admin Reset: User Password</h5>
      <form method="POST" action="{{ route('settings.password.update.admin') }}">
        @csrf
        <div class="row g-3">
          <div class="col-md-8"><label class="form-label">Select User</label>
            <select id="userSelect" name="user_id" class="form-select" required><option value="">Loading users…</option></select>
            <small style="color:rgba(255,255,255,0.35);">Includes Admin, Staff, and Client roles.</small>
          </div>
          <div class="col-md-6"><label class="form-label">New Password</label><input type="password" name="password" class="form-control" required></div>
          <div class="col-md-6"><label class="form-label">Confirm Password</label><input type="password" name="password_confirmation" class="form-control" required></div>
        </div>
        <div class="mt-3"><button type="submit" class="btn btn-outline-light">Reset Password</button></div>
      </form>
    </div>
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        fetch('{{ route("settings.users") }}', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
          .then(r => r.json()).then(users => {
            const sel = document.getElementById('userSelect');
            sel.innerHTML = '<option value="">Select a user…</option>';
            users.forEach(u => { const o = document.createElement('option'); o.value = u.id; o.textContent = u.name+' ('+u.email+') — '+u.role; sel.appendChild(o); });
          }).catch(() => { document.getElementById('userSelect').innerHTML = '<option value="">Could not load users</option>'; });
      });
    </script>
    @endif
  </div>
</div>
@endsection
