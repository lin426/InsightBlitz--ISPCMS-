@extends('layouts.app')
@section('content')
<div class="container mt-4" style="max-width:580px;">
  <div class="bubble p-4">
    <h2 class="text-white mb-3" style="font-size:1.4rem;">Add Staff</h2>
    @if ($errors->any()) <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div> @endif
    <form method="POST" action="{{ route('staff.store') }}">
      @csrf
      <div class="mb-3"><label class="form-label">Name</label><input name="name" class="form-control" value="{{ old('name') }}" required></div>
      <div class="mb-3"><label class="form-label">Position</label><input name="position" class="form-control" value="{{ old('position') }}"></div>
      <div class="mb-3"><label class="form-label">Rating (1–5)</label>
        <select name="rating" class="form-control" required><option value="">Select</option>@for ($i=1;$i<=5;$i++)<option value="{{ $i }}" {{ old('rating')==$i ? 'selected' : '' }}>{{ $i }}</option>@endfor</select>
      </div>
      <div class="mb-3"><label class="form-label">Email (login username)</label><input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
        <small style="color:rgba(255,255,255,0.4);">A user account will be created with role <code>staff</code>.</small></div>
      <div class="mb-3"><label class="form-label">Temporary Password <small style="color:rgba(255,255,255,0.4);">(leave blank for default <code>123456</code>)</small></label>
        <input type="password" name="password" class="form-control" minlength="6" autocomplete="new-password"></div>
      <div class="d-flex gap-2"><button class="btn btn-primary">Save</button><a href="{{ route('staff.index') }}" class="btn btn-secondary">Cancel</a></div>
    </form>
  </div>
</div>
@endsection
