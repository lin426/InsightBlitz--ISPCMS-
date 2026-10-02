@extends('layouts.app')
@php
  $sort = $sort ?? request('sort', 'due_date');
  $dir  = $dir  ?? request('dir',  'asc');
  $nextDir = function (string $col) use ($sort, $dir) { return ($sort === $col && $dir === 'asc') ? 'desc' : 'asc'; };
  $sortUrl = function (string $col) use ($nextDir) { return request()->fullUrlWithQuery(['sort' => $col, 'dir' => $nextDir($col)]); };
  $arrow = function (string $col) use ($sort, $dir) { if ($sort !== $col) return ''; return $dir === 'asc' ? ' ↑' : ' ↓'; };
@endphp

@section('content')
<div class="container mt-4 mx-auto" style="max-width:1150px;">
  <div class="d-flex align-items-center gap-3 mb-3">
    <h2 class="text-white mb-0">Task Management</h2>
    <a href="{{ route('tasks.create') }}" class="btn btn-primary btn-sm">+ Add Task</a>
    <form method="GET" class="ms-auto d-flex gap-2" role="search" style="max-width:420px; width:100%;">
      <input type="hidden" name="sort" value="{{ $sort }}">
      <input type="hidden" name="dir"  value="{{ $dir }}">
      <input type="text" name="q" value="{{ request('q') }}" placeholder="Search title, project, status…" class="form-control form-control-sm">
      @if(request('q')) <a href="{{ request()->fullUrlWithQuery(['q' => null]) }}" class="btn btn-secondary btn-sm">Clear</a> @endif
      <button class="btn btn-outline-light btn-sm">Search</button>
    </form>
  </div>

  @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif

  <div class="table-responsive" style="max-height:500px; overflow-y:auto;">
    <table class="table table-bordered custom-table">
      <thead>
        <tr>
          <th><a class="link-light text-decoration-none" href="{{ $sortUrl('title') }}">Title{!! $arrow('title') !!}</a></th>
          <th><a class="link-light text-decoration-none" href="{{ $sortUrl('status') }}">Status{!! $arrow('status') !!}</a></th>
          <th><a class="link-light text-decoration-none" href="{{ $sortUrl('project') }}">Project{!! $arrow('project') !!}</a></th>
          <th><a class="link-light text-decoration-none" href="{{ $sortUrl('due_date') }}">Due Date{!! $arrow('due_date') !!}</a></th>
          <th>Assigned To</th>
          <th><a class="link-light text-decoration-none" href="{{ $sortUrl('progress') }}">Progress{!! $arrow('progress') !!}</a></th>
          <th>Proof</th>
          <th><a class="link-light text-decoration-none" href="{{ $sortUrl('handler_rating') }}">Rating{!! $arrow('handler_rating') !!}</a></th>
          <th>Note</th>
          <th style="width:100px;">Actions</th>
        </tr>
      </thead>
      <tbody>
      @foreach($tasks as $task)
        @php
          $p = (int)($task->progress ?? 0);
          $atts = $task->attachments ?? collect();
        @endphp
        <tr>
          <td>{{ $task->title }}</td>
          <td>
            @if($task->status === 'completed') <span class="badge glass-badge bg-success">Completed</span>
            @elseif($task->status === 'pending') <span class="badge glass-badge bg-warning text-dark">Pending</span>
            @elseif($task->status === 'in_progress') <span class="badge glass-badge bg-primary">In Progress</span>
            @else <span class="badge glass-badge bg-secondary">{{ ucfirst(str_replace('_',' ', $task->status)) }}</span>
            @endif
          </td>
          <td>{{ $task->project->name ?? '—' }}</td>
          <td>@if(!empty($task->due_date)) {{ \Illuminate\Support\Carbon::parse($task->due_date)->format('Y-m-d H:i:s') }} @else — @endif</td>
          <td>@if($task->assignedStaff) {{ $task->assignedStaff->name }} — {{ $task->assignedStaff->position ?? 'No position' }} @else <em style="color:rgba(255,255,255,0.3);">Unassigned</em> @endif</td>
          <td style="min-width:140px;">
            <div class="progress" title="{{ $p }}%"><div class="progress-bar" style="width:{{ $p }}%;">{{ $p }}%</div></div>
            @if(!empty($task->progress_note)) <small class="d-block mt-1" style="color:rgba(255,255,255,0.4);">{{ $task->progress_note }}</small> @endif
          </td>
          <td>
            @php $attachments = $task->attachments ?? collect(); @endphp
            @forelse($attachments as $att)
              <div class="mb-1"><a href="{{ route('attachments.show', $att->id) }}" target="_blank" style="color:#4ade80;">{{ $att->original_name ?? basename($att->path ?? 'proof') }}</a>
              @if(!empty($att->size)) <span style="color:rgba(255,255,255,0.3); font-size:0.78rem;">({{ number_format($att->size / 1024, 1) }} KB)</span> @endif</div>
            @empty <span style="color:rgba(255,255,255,0.3);">—</span> @endforelse
          </td>
          <td>@if($task->handler) {{ $task->handler->rating }} / 5 @else — @endif</td>
          <td>@if($task->handler) {{ $task->handler->recommendation ?? 'None' }} @else — @endif</td>
          <td class="text-nowrap">
            <div class="d-flex gap-1">
              <a href="{{ route('tasks.edit', $task->id) }}" class="btn btn-warning btn-sm">Edit</a>
              <form action="{{ route('tasks.destroy', $task->id) }}" method="POST">@csrf @method('DELETE')<button class="btn btn-danger btn-sm">Del</button></form>
            </div>
          </td>
        </tr>
      @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection
