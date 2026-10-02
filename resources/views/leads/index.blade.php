@extends('layouts.app')
@section('content')
@php
  $sort = $sort ?? request('sort', 'created_at');
  $dir  = $dir  ?? request('dir',  'desc');
  $nextDir = function (string $col) use ($sort, $dir) { return ($sort === $col && $dir === 'asc') ? 'desc' : 'asc'; };
  $sortUrl = function (string $col) use ($nextDir) { return request()->fullUrlWithQuery(['sort' => $col, 'dir' => $nextDir($col)]); };
  $arrow = function (string $col) use ($sort, $dir) { if ($sort !== $col) return ''; return $dir === 'asc' ? ' ↑' : ' ↓'; };
@endphp

<div class="container mt-4" style="max-width:1050px;">
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h2 class="text-white mb-0">Sales Pipeline</h2>
    <div class="d-flex align-items-center gap-2">
      <form method="GET" class="d-flex gap-2" style="max-width:380px;">
        <input type="hidden" name="sort" value="{{ $sort }}">
        <input type="hidden" name="dir"  value="{{ $dir }}">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Search name or email…" class="form-control form-control-sm">
        @if(request('q')) <a href="{{ request()->fullUrlWithQuery(['q'=>null]) }}" class="btn btn-secondary btn-sm">Clear</a> @endif
        <button class="btn btn-outline-light btn-sm">Search</button>
      </form>
      <a href="{{ route('leads.create') }}" class="btn btn-primary btn-sm">+ Add Lead</a>
    </div>
  </div>

  <div class="table-responsive" style="max-height:700px; overflow-y:auto;">
    <table class="table table-bordered custom-table">
      <thead>
        <tr>
          <th><a class="link-light text-decoration-none" href="{{ $sortUrl('name') }}">Name{!! $arrow('name') !!}</a></th>
          <th><a class="link-light text-decoration-none" href="{{ $sortUrl('email') }}">Contact{!! $arrow('email') !!}</a></th>
          <th><a class="link-light text-decoration-none" href="{{ $sortUrl('stage') }}">Stage{!! $arrow('stage') !!}</a></th>
          <th><a class="link-light text-decoration-none" href="{{ $sortUrl('value') }}">Value{!! $arrow('value') !!}</a></th>
          <th><a class="link-light text-decoration-none" href="{{ $sortUrl('status') }}">Status{!! $arrow('status') !!}</a></th>
          <th><a class="link-light text-decoration-none" href="{{ $sortUrl('created_at') }}">Created{!! $arrow('created_at') !!}</a></th>
          <th style="width:140px;">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($leads as $lead)
          <tr>
            <td>{{ $lead->name }}</td>
            <td>{{ $lead->email ?? $lead->contact }}</td>
            <td><span class="badge glass-badge bg-secondary">{{ $lead->stage }}</span></td>
            <td>{{ number_format((float)$lead->value, 0) }}</td>
            <td>
              @php $badge = match($lead->status){ 'won'=>'bg-success','lost'=>'bg-danger','open'=>'bg-secondary', default=>'bg-info' }; @endphp
              <span class="badge glass-badge {{ $badge }}">{{ ucfirst($lead->status) }}</span>
            </td>
            <td>{{ optional($lead->created_at)->format('Y-m-d') }}</td>
            <td class="text-nowrap">
              <a href="{{ route('leads.edit', $lead->id) }}" class="btn btn-sm btn-warning">Edit</a>
              <form action="{{ route('leads.destroy', $lead->id) }}" method="POST" style="display:inline;">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this lead?')">Delete</button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="7" class="text-center" style="color:rgba(255,255,255,0.3);">No leads found.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if(method_exists($leads, 'links')) <div class="mt-2">{{ $leads->withQueryString()->links() }}</div> @endif
</div>
@endsection
