@extends('layouts.app')
@section('content')
<div class="container mt-4" style="max-width:580px;">
  <div class="bubble p-4">
    <h2 class="text-white mb-3" style="font-size:1.4rem;">Edit Lead</h2>
    <form action="{{ route('leads.update', $lead->id) }}" method="POST">
      @csrf @method('PUT')
      <div class="mb-3"><label class="form-label">Name</label><input type="text" name="name" value="{{ $lead->name }}" class="form-control" required></div>
      <div class="mb-3"><label class="form-label">Contact (Gmail)</label><input type="email" name="contact" class="form-control" value="{{ old('contact', $lead->contact) }}" required></div>
      <div class="mb-3"><label class="form-label">Stage</label>
        <select name="stage" class="form-control" required>
          @foreach(['Prospect','Contacted','Proposal','Closed'] as $stage)
            <option value="{{ $stage }}" {{ $lead->stage == $stage ? 'selected' : '' }}>{{ $stage }}</option>
          @endforeach
        </select>
      </div>
      <div class="mb-3"><label class="form-label">Value</label><input type="number" step="0.01" name="value" value="{{ $lead->value }}" class="form-control"></div>
      <div class="mb-3"><label class="form-label">Status</label>
        <select name="status" class="form-control">
          @foreach(['open','won','lost'] as $status)
            <option value="{{ $status }}" {{ $lead->status == $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
          @endforeach
        </select>
      </div>
      <div class="d-flex gap-2"><button type="submit" class="btn btn-primary">Update Lead</button><a href="{{ route('leads.index') }}" class="btn btn-secondary">Cancel</a></div>
    </form>
  </div>
</div>
@endsection
