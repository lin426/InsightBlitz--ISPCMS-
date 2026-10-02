@extends('layouts.app')
@section('content')
<div class="container mt-4" style="max-width:580px;">
  <div class="bubble p-4">
    <h2 class="text-white mb-3" style="font-size:1.4rem;">Edit Staff</h2>
    @if ($errors->any()) <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div> @endif
    <form method="POST" action="{{ route('staff.update', $staff->id) }}">
      @csrf @method('PUT')
      <div class="mb-3"><label class="form-label">Name</label><input name="name" class="form-control" value="{{ old('name', $staff->name) }}" required></div>
      <div class="mb-3"><label class="form-label">Position</label><input name="position" class="form-control" value="{{ old('position', $staff->position) }}"></div>
      <div class="mb-3"><label class="form-label">Rating (1–5)</label>
        <select name="rating" class="form-control" required><option value="">Select</option>@for ($i=1;$i<=5;$i++)<option value="{{ $i }}" {{ old('rating', $staff->rating)==$i ? 'selected' : '' }}>{{ $i }}</option>@endfor</select>
      </div>
      <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="{{ old('email', $staff->user->email ?? '') }}" required>
        <small style="color:rgba(255,255,255,0.4);">Used as staff login email.</small></div>
      <div class="mb-3"><label class="form-label">Reset Password <small style="color:rgba(255,255,255,0.4);">(leave blank to keep current)</small></label>
        <input type="password" name="password" class="form-control" minlength="6" autocomplete="new-password" placeholder="New password or leave blank"></div>
      <div class="d-flex gap-2"><button class="btn btn-primary">Update</button><a href="{{ route('staff.index') }}" class="btn btn-secondary">Cancel</a></div>
    </form>
  </div>
</div>
@endsection
