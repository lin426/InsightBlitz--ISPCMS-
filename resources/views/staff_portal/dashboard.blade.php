@extends('layouts.app')

@section('content')
<style>
  .staff-header {
    font-size: 2rem;
    font-weight: 800;
    background: linear-gradient(135deg, #22c55e, #4ade80, #86efac);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
  }
  .welcome-card {
    background: linear-gradient(145deg, rgba(15,15,22,0.95), rgba(8,8,12,0.98));
    border: 1px solid rgba(34,197,94,0.2);
    border-radius: 20px;
    padding: 1.5rem 2rem;
    position: relative;
    overflow: hidden;
  }
  .welcome-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0;
    width: 100%; height: 3px;
    background: linear-gradient(90deg, #22c55e, #4ade80, transparent);
  }
  .welcome-card .welcome-name {
    font-size: 1.3rem;
    font-weight: 700;
    color: #fff;
  }
  .welcome-card .welcome-detail {
    color: rgba(255,255,255,0.5);
    font-size: 0.9rem;
  }
  .welcome-card .welcome-detail strong { color: #4ade80; }

  .stat-card {
    background: linear-gradient(145deg, rgba(20,20,30,0.9), rgba(10,10,15,0.95));
    border: 1px solid rgba(34,197,94,0.12);
    border-radius: 18px;
    padding: 1.25rem 1.5rem;
    text-align: center;
    transition: all 0.3s;
    position: relative;
    overflow: hidden;
  }
  .stat-card:hover {
    border-color: rgba(34,197,94,0.4);
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(34,197,94,0.12);
  }
  .stat-card::after {
    content: '';
    position: absolute;
    bottom: 0; left: 0;
    width: 100%; height: 3px;
    background: linear-gradient(90deg, #22c55e, transparent);
  }
  .stat-card .stat-number {
    font-size: 2rem;
    font-weight: 800;
    color: #4ade80;
  }
  .stat-card .stat-label {
    font-size: 0.8rem;
    color: rgba(255,255,255,0.5);
    text-transform: uppercase;
    letter-spacing: 1px;
    font-weight: 600;
    margin-top: 4px;
  }

  .staff-glass {
    background: linear-gradient(145deg, rgba(15,15,22,0.95), rgba(8,8,12,0.98));
    border: 1px solid rgba(255,255,255,0.06);
    border-radius: 20px;
    padding: 1.5rem;
  }
  .staff-glass h5 {
    font-weight: 700;
    color: #fff;
    display: flex; align-items: center; gap: 8px;
  }
  .staff-glass h5::before {
    content: '';
    width: 4px; height: 20px;
    background: #22c55e;
    border-radius: 2px;
  }

  .staff-table {
    background: transparent;
    border-radius: 16px;
    overflow: hidden;
  }
  .staff-table thead th {
    background: rgba(34,197,94,0.08) !important;
    color: #4ade80 !important;
    border-color: rgba(255,255,255,0.06) !important;
    font-weight: 700;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.8px;
    padding: 12px 14px;
  }
  .staff-table tbody td {
    background: transparent !important;
    color: #fff !important;
    border-color: rgba(255,255,255,0.04) !important;
    padding: 14px;
    vertical-align: middle;
  }
  .staff-table tbody tr {
    background: transparent !important;
    transition: all 0.2s;
  }
  .staff-table tbody tr:hover {
    background: rgba(34,197,94,0.05) !important;
  }

  .progress {
    background: rgba(255,255,255,0.08);
    border-radius: 10px;
    overflow: hidden;
  }
  .progress-bar {
    background: linear-gradient(90deg, #22c55e, #4ade80);
    font-size: 0.7rem;
    font-weight: 700;
    color: #000;
  }

  .status-badge {
    display: inline-block;
    padding: 3px 12px;
    border-radius: 8px;
    font-size: 0.78rem;
    font-weight: 600;
  }
  .status-open { background: rgba(59,130,246,0.15); color: #60a5fa; }
  .status-in_progress { background: rgba(234,179,8,0.15); color: #facc15; }
  .status-done, .status-completed { background: rgba(34,197,94,0.15); color: #4ade80; }

  .priority-badge {
    display: inline-block;
    padding: 3px 10px;
    border-radius: 8px;
    font-size: 0.75rem;
    font-weight: 600;
  }
  .priority-high, .priority-urgent { background: rgba(239,68,68,0.15); color: #f87171; }
  .priority-medium, .priority-normal { background: rgba(234,179,8,0.15); color: #facc15; }
  .priority-low { background: rgba(34,197,94,0.15); color: #4ade80; }

  .attachment-link {
    color: #4ade80;
    text-decoration: none;
    font-size: 0.85rem;
    transition: color 0.2s;
  }
  .attachment-link:hover { color: #86efac; text-decoration: underline; }
</style>

<div class="container mt-4" style="max-width: 1100px;">
  <h2 class="staff-header mb-4">Staff Dashboard</h2>

  @if(session('warning'))
    <div class="alert alert-warning" style="background: rgba(234,179,8,0.15); border: 1px solid rgba(234,179,8,0.3); color: #facc15; border-radius: 12px;">{{ session('warning') }}</div>
  @endif
  @if(session('success'))
    <div class="alert alert-success" style="background: rgba(34,197,94,0.15); border: 1px solid rgba(34,197,94,0.3); color: #4ade80; border-radius: 12px;">{{ session('success') }}</div>
  @endif
  @if($errors->any())
    <div class="alert alert-danger" style="background: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.3); color: #f87171; border-radius: 12px;">
      <ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
  @endif

  @php use Illuminate\Support\Facades\Storage; @endphp

  {{-- Welcome Card --}}
  <div class="welcome-card mb-4">
    <div class="welcome-name mb-2">👋 Welcome, {{ $user->name ?? auth()->user()->name }}</div>
    <div class="welcome-detail"><strong>Role:</strong> {{ auth()->user()->role }}</div>
    @if(isset($staff) && $staff)
      <div class="welcome-detail"><strong>Position:</strong> {{ $staff->position ?? 'N/A' }}</div>
      <div class="welcome-detail"><strong>Rating:</strong> <span style="color: #facc15;">{{ $staff->rating }} / 5 ⭐</span></div>
    @else
      <div class="welcome-detail" style="color: #facc15;">No Staff profile linked to this account yet.</div>
    @endif
  </div>

  {{-- Stats Cards --}}
  @if(isset($stats))
  <div class="row g-3 mb-4">
    <div class="col-md-4">
      <div class="stat-card">
        <div class="stat-number">{{ $stats['total'] }}</div>
        <div class="stat-label">Total Tasks</div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="stat-card">
        <div class="stat-number" style="color: #60a5fa;">{{ $stats['open'] }}</div>
        <div class="stat-label">Open</div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="stat-card">
        <div class="stat-number">{{ $stats['done'] }}</div>
        <div class="stat-label">Done</div>
      </div>
    </div>
  </div>
  @endif

  {{-- Tasks Table --}}
  <div class="staff-glass">
    <h5 class="mb-3">My Recent Tasks</h5>

    @if(isset($tasks) && $tasks->count())
      <div class="table-responsive">
        <table class="table table-bordered staff-table mb-0">
          <thead>
            <tr>
              <th style="min-width: 220px;">Title</th>
              <th>Status</th>
              <th>Priority</th>
              <th>Due</th>
              <th style="width: 280px;">Progress</th>
              <th style="min-width: 240px;">Attachments</th>
            </tr>
          </thead>
          <tbody>
            @foreach($tasks as $t)
              @php
                $canEdit = isset($staff) && $staff && (int)$t->assigned_staff_id === (int)$staff->id;
                $progress = (int)($t->progress ?? 0);
                $statusClass = 'status-' . strtolower($t->status ?? 'open');
                $priorityClass = 'priority-' . strtolower($t->priority ?? 'normal');
              @endphp
              <tr>
                <td>
                  <div class="fw-semibold">{{ $t->title ?? ('Task #'.$t->id) }}</div>
                  <div style="color: rgba(255,255,255,0.4); font-size: 0.8rem;">{{ \Illuminate\Support\Str::limit($t->description ?? '', 120) }}</div>
                </td>
                <td><span class="status-badge {{ $statusClass }}">{{ ucfirst($t->status ?? 'n/a') }}</span></td>
                <td><span class="priority-badge {{ $priorityClass }}">{{ ucfirst($t->priority ?? 'normal') }}</span></td>
                <td style="color: rgba(255,255,255,0.6) !important;">
                  @if(!empty($t->due_date))
                    {{ \Illuminate\Support\Carbon::parse($t->due_date)->format('M d, Y') }}
                  @else —
                  @endif
                </td>
                <td>
                  <div class="progress" style="height: 16px;">
                    <div class="progress-bar" role="progressbar" style="width: {{ $progress }}%;"
                         aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100">
                      {{ $progress }}%
                    </div>
                  </div>
                  @if(!empty($t->progress_note))
                    <small style="color: rgba(255,255,255,0.4); display: block; margin-top: 4px;">Note: {{ $t->progress_note }}</small>
                  @endif
                  @if($canEdit)
                    <form class="mt-2" method="POST" action="{{ route('staff.portal.tasks.progress', $t) }}">
                      @csrf
                      <div class="d-flex align-items-center gap-2">
                        <input type="range" min="0" max="100" name="progress" value="{{ $progress }}"
                               oninput="this.nextElementSibling.value=this.value+'%'"
                               class="form-range" style="width: 140px; accent-color: #22c55e;">
                        <output style="color: #4ade80; font-weight: 600; font-size: 0.85rem;">{{ $progress }}%</output>
                        <label class="ms-2 mb-0 d-flex align-items-center gap-1" style="color: rgba(255,255,255,0.5); font-size: 0.8rem;">
                          <input type="checkbox" name="complete" value="1" {{ $progress >= 100 ? 'checked disabled' : '' }} style="accent-color: #22c55e;">
                          <span>Mark complete</span>
                        </label>
                      </div>
                      <textarea name="note" class="form-control form-control-sm mt-2" rows="2" placeholder="Optional progress note..."
                                style="background: rgba(0,0,0,0.4); border: 1px solid rgba(255,255,255,0.08); color: #fff; border-radius: 10px; font-size: 0.85rem;">{{ old('note', $t->progress_note) }}</textarea>
                      <button class="btn btn-primary btn-sm mt-2" style="padding: 6px 20px;">Save Progress</button>
                    </form>
                  @endif
                </td>
                <td>
                  @if($t->attachments && $t->attachments->count())
                    <ul class="mb-2" style="padding-left: 0; list-style: none;">
                      @foreach($t->attachments as $att)
                        <li class="mb-1">
                          <a href="{{ route('staff.portal.attachments.show', $att) }}" class="attachment-link">
                            📎 {{ $att->original_name ?? basename($att->path) }}
                          </a>
                          @if($att->size)
                            <span style="color: rgba(255,255,255,0.3); font-size: 0.75rem;">({{ number_format($att->size / 1024, 1) }} KB)</span>
                          @endif
                        </li>
                      @endforeach
                    </ul>
                  @else
                    <span style="color: rgba(255,255,255,0.3); font-size: 0.85rem;">No attachments</span>
                  @endif
                  @if($canEdit)
                    <form method="POST" action="{{ route('staff.portal.tasks.attachments.upload', $t) }}" enctype="multipart/form-data" class="mt-2">
                      @csrf
                      <input type="file" name="attachments[]" class="form-control form-control-sm" multiple
                             style="background: rgba(0,0,0,0.4); border: 1px solid rgba(255,255,255,0.08); color: #fff; border-radius: 10px; font-size: 0.8rem;">
                      <button class="btn btn-sm mt-2" style="background: rgba(255,255,255,0.08); color: #fff; border: 1px solid rgba(255,255,255,0.1); border-radius: 10px; padding: 4px 16px; font-size: 0.8rem;">Upload</button>
                    </form>
                  @endif
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <div class="mt-3">{{ $tasks->links() }}</div>
    @else
      <p style="color: rgba(255,255,255,0.4);">No tasks assigned yet.</p>
    @endif
  </div>
</div>
@endsection
