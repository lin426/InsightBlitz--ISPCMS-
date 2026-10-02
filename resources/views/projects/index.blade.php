@extends('layouts.app')
@section('content')
@php
  $sort = $sort ?? request('sort', 'created_at');
  $dir  = $dir  ?? request('dir',  'desc');
  $nextDir = function (string $col) use ($sort, $dir) { return ($sort === $col && $dir === 'asc') ? 'desc' : 'asc'; };
  $sortUrl = function (string $col) use ($nextDir) { return request()->fullUrlWithQuery(['sort' => $col, 'dir' => $nextDir($col)]); };
  $arrow = function (string $col) use ($sort, $dir) { if ($sort !== $col) return ''; return $dir === 'asc' ? ' ↑' : ' ↓'; };
@endphp

<div class="container mt-4" style="max-width:1000px;">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="text-white m-0">Projects</h2>
    <div class="d-flex align-items-center gap-2">
      <form method="GET" class="d-flex gap-2" style="max-width:340px;">
        <input type="hidden" name="sort" value="{{ $sort }}"><input type="hidden" name="dir" value="{{ $dir }}">
        <input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-sm" placeholder="Search name, client…">
        @if(request('q')) <a href="{{ request()->fullUrlWithQuery(['q'=>null]) }}" class="btn btn-secondary btn-sm">Clear</a> @endif
        <button type="submit" class="btn btn-outline-light btn-sm">Search</button>
      </form>
      <a href="{{ route('projects.create') }}" class="btn btn-primary btn-sm">+ Add Project</a>
    </div>
  </div>
  @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
  <div class="table-responsive" style="max-height:700px; overflow-y:auto;">
    <table class="table table-bordered custom-table">
      <thead><tr>
        <th><a class="link-light text-decoration-none" href="{{ $sortUrl('name') }}">Name{!! $arrow('name') !!}</a></th>
        <th><a class="link-light text-decoration-none" href="{{ $sortUrl('client_name') }}">Client{!! $arrow('client_name') !!}</a></th>
        <th style="width:120px;text-align:center;"><a class="link-light text-decoration-none" href="{{ $sortUrl('result') }}">Result{!! $arrow('result') !!}</a></th>
        <th style="width:140px;"><a class="link-light text-decoration-none" href="{{ $sortUrl('created_at') }}">Created{!! $arrow('created_at') !!}</a></th>
        <th style="width:140px;">Actions</th>
      </tr></thead>
      <tbody>
        @forelse ($projects as $project)
          <tr>
            <td>{{ $project->name }}</td><td>{{ $project->client_name ?: '—' }}</td>
            <td class="text-center">
              @php $r = strtolower((string) $project->result); @endphp
              @if($r === 'good') <span class="badge glass-badge bg-success">Good</span>
              @elseif($r === 'bad') <span class="badge glass-badge bg-danger">Bad</span>
              @else <span class="badge glass-badge bg-secondary">{{ $project->result ? ucfirst($project->result) : '—' }}</span> @endif
            </td>
            <td>{{ \Illuminate\Support\Carbon::parse($project->created_at)->format('Y-m-d') }}</td>
            <td class="text-nowrap">
              <a href="{{ route('projects.edit', $project->id) }}" class="btn btn-warning btn-sm">Edit</a>
              <form action="{{ route('projects.destroy', $project->id) }}" method="POST" style="display:inline;">@csrf @method('DELETE')<button class="btn btn-danger btn-sm" onclick="return confirm('Delete?')">Delete</button></form>
            </td>
          </tr>
        @empty <tr><td colspan="5" class="text-center" style="color:rgba(255,255,255,0.3);">No projects found.</td></tr> @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
