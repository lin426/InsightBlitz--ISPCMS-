@extends('layouts.app')
@section('content')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const titleInput = document.querySelector('input[name="title"]');
    const staffSelect = document.querySelector('select[name="assigned_staff_id"]');
    const descriptionBox = document.querySelector('textarea[name="description"]');
    function generateSuggestion() {
        const title = titleInput.value.trim().toLowerCase();
        const staffText = staffSelect.options[staffSelect.selectedIndex]?.text.toLowerCase() || '';
        let suggestions = [];
        if (title.includes('report')) suggestions.push('Prepare a detailed report.');
        if (title.includes('design')) suggestions.push('Create or revise design materials.');
        if (title.includes('deploy')) suggestions.push('Coordinate deployment and test post-launch.');
        if (staffText.includes('developer')) suggestions.push('Ensure clean code and version control.');
        if (suggestions.length > 0 && descriptionBox.value.trim() === '') descriptionBox.value = suggestions.join(' ');
    }
    titleInput?.addEventListener('input', generateSuggestion);
    staffSelect?.addEventListener('change', generateSuggestion);
});
</script>
<div class="container mt-4" style="max-width:580px;">
  <div class="bubble p-4">
    <h2 class="text-white mb-3" style="font-size:1.4rem;">Edit Task</h2>
    @if ($errors->any()) <div class="alert alert-danger">{{ $errors->first() }}</div> @endif
    <form method="POST" action="{{ route('tasks.update', $task->id) }}">
      @csrf @method('PUT')
      <div class="mb-3"><label class="form-label">Title</label><input name="title" value="{{ old('title', $task->title) }}" class="form-control" required></div>
      <div class="mb-3"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="3">{{ old('description', $task->description) }}</textarea></div>
      <div class="mb-3"><label class="form-label">Assigned Project</label>
        <select name="project_id" class="form-control"><option value="">— Select —</option>@foreach ($projects as $project)<option value="{{ $project->id }}" @selected(old('project_id', $task->project_id)==$project->id)>{{ $project->name }}</option>@endforeach</select>
      </div>
      <div class="mb-3"><label class="form-label">Client (User)</label>
        <select name="client_user_id" class="form-control"><option value="">— None —</option>@foreach($clientUsers as $u)<option value="{{ $u->id }}" @selected(old('client_user_id', $task->client_user_id)==$u->id)>{{ $u->name }} ({{ $u->email }})</option>@endforeach</select>
        @error('client_user_id') <small class="text-danger d-block">{{ $message }}</small> @enderror
      </div>
      <div class="mb-3"><label class="form-label">Status</label>
        <select name="status" class="form-control">@foreach(['pending','in_progress','completed'] as $status)<option value="{{ $status }}" @selected(old('status', $task->status)===$status)>{{ ucfirst(str_replace('_',' ', $status)) }}</option>@endforeach</select>
      </div>
      <div class="mb-3"><label class="form-label">Due Date</label><input type="date" name="due_date" class="form-control" value="{{ old('due_date', optional($task->due_date)->format('Y-m-d')) }}">@error('due_date') <div class="text-danger mt-1">{{ $message }}</div> @enderror</div>
      @php $staffList = \App\Models\Staff::orderByDesc('rating')->get(); @endphp
      <div class="mb-3"><label class="form-label">Assign to Staff</label>
        <select name="assigned_staff_id" class="form-control"><option value="">Unassigned</option>@foreach($staffList as $staff)<option value="{{ $staff->id }}" @selected(old('assigned_staff_id', $task->assigned_staff_id)==$staff->id)>{{ $staff->name }} – {{ $staff->position ?? 'No position' }} ({{ $staff->rating }})</option>@endforeach</select>
      </div>
      <div class="d-flex gap-2"><button class="btn btn-primary">Update</button><a href="{{ route('tasks.index') }}" class="btn btn-secondary">Cancel</a></div>
    </form>
  </div>
</div>
@endsection
