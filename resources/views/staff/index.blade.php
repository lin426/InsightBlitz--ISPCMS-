@extends('layouts.app')
@section('content')
@php
  $sort = $sort ?? request('sort', 'name');
  $dir  = $dir  ?? request('dir',  'asc');
  $q    = $q    ?? request('q',    '');
  $nextDir = function (string $col) use ($sort, $dir) { return ($sort === $col && $dir === 'asc') ? 'desc' : 'asc'; };
  $sortUrl = function (string $col) use ($nextDir) { return request()->fullUrlWithQuery(['sort' => $col, 'dir' => $nextDir($col)]); };
  $arrow = function (string $col) use ($sort, $dir) { if ($sort !== $col) return ''; return $dir === 'asc' ? ' ↑' : ' ↓'; };
@endphp

<div class="container mt-4" style="max-width:900px;">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="text-white m-0">Staff List</h2>
    <div class="d-flex gap-2">
      <form method="GET" class="d-flex gap-2" role="search" style="max-width:360px;">
        @foreach(request()->except('q') as $k => $v)<input type="hidden" name="{{ $k }}" value="{{ $v }}">@endforeach
        <input name="q" value="{{ $q }}" class="form-control form-control-sm" placeholder="Search name, position…">
        <button class="btn btn-outline-light btn-sm">Search</button>
      </form>
      <a href="{{ route('staff.create') }}" class="btn btn-primary btn-sm">+ Add Staff</a>
    </div>
  </div>
  @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
  @if(session('generated_password')) <div class="alert alert-info">Temporary password: <strong>{{ session('generated_password') }}</strong></div> @endif
  <div class="table-responsive" style="max-height:700px; overflow-y:auto;">
    <table class="table custom-table table-bordered">
      <thead><tr>
        <th><a class="link-light text-decoration-none" href="{{ $sortUrl('name') }}">Name{!! $arrow('name') !!}</a></th>
        <th><a class="link-light text-decoration-none" href="{{ $sortUrl('position') }}">Position{!! $arrow('position') !!}</a></th>
        <th style="width:130px;"><a class="link-light text-decoration-none" href="{{ $sortUrl('rating') }}">Rating{!! $arrow('rating') !!}</a></th>
        <th style="width:140px;">Actions</th>
      </tr></thead>
      <tbody>
        @forelse($staff as $member)
          <tr><td>{{ $member->name }}</td><td>{{ $member->position ?? 'N/A' }}</td><td>{{ is_null($member->rating) ? '—' : $member->rating.' / 5' }}</td>
          <td class="text-nowrap"><a href="{{ route('staff.edit', $member->id) }}" class="btn btn-warning btn-sm">Edit</a>
          <form action="{{ route('staff.destroy', $member->id) }}" method="POST" class="d-inline">@csrf @method('DELETE')<button class="btn btn-danger btn-sm" onclick="return confirm('Delete?')">Delete</button></form></td></tr>
        @empty <tr><td colspan="4" class="text-center" style="color:rgba(255,255,255,0.3);">No staff found.</td></tr> @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
