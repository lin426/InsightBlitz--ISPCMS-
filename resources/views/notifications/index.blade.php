@extends('layouts.app')
@section('content')
@php
  $q   = $q   ?? request('q', '');
  $dir = $dir ?? request('dir','desc');
  $nextDir = $dir === 'asc' ? 'desc' : 'asc';
@endphp
<div class="container mt-4" style="max-width:900px;">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="text-white m-0">Notifications</h2>
    <form method="GET" class="d-flex gap-2" style="max-width:380px; width:100%;">
      <input type="hidden" name="dir" value="{{ $dir }}">
      <input type="text" name="q" value="{{ $q }}" class="form-control form-control-sm" placeholder="Search completed tasks…">
      @if($q) <a href="{{ request()->fullUrlWithQuery(['q'=>null]) }}" class="btn btn-secondary btn-sm">Clear</a> @endif
      <button type="submit" class="btn btn-outline-light btn-sm">Search</button>
    </form>
  </div>
  <div class="table-responsive" style="max-height:700px; overflow-y:auto;">
    <table class="table table-bordered custom-table">
      <thead><tr>
        <th>Message</th>
        <th style="width:170px;"><a class="link-light text-decoration-none" href="{{ request()->fullUrlWithQuery(['dir' => $nextDir]) }}">Created {{ $dir === 'asc' ? '↑' : '↓' }}</a></th>
        <th style="width:100px;">Action</th>
      </tr></thead>
      <tbody>
        @forelse($rows as $n)
          <tr><td>{{ $n->message }}</td><td>{{ \Illuminate\Support\Carbon::parse($n->created_at)->format('Y-m-d H:i') }}</td>
          <td><a href="{{ $n->link }}" class="btn btn-sm btn-primary">View</a></td></tr>
        @empty <tr><td colspan="3" class="text-center" style="color:rgba(255,255,255,0.3);">No notifications.</td></tr> @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
