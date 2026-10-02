@extends('layouts.app')
@section('content')
<div class="container mt-4" style="max-width:580px;">
  <div class="bubble p-4">
    <h2 class="text-white mb-3" style="font-size:1.4rem;">Add New Lead</h2>
    <form action="{{ route('leads.store') }}" method="POST">
      @csrf
      <div class="mb-3"><label class="form-label">Name</label><input type="text" name="name" class="form-control" required></div>
      <div class="mb-3"><label class="form-label">Contact</label><input type="email" name="contact" class="form-control" placeholder="example@gmail.com" required></div>
      <div class="mb-3"><label class="form-label">Stage</label>
        <select name="stage" class="form-control" required>
          <option value="Prospect">Prospect</option><option value="Contacted">Contacted</option><option value="Proposal">Proposal</option><option value="Closed">Closed</option>
        </select>
      </div>
      <div class="mb-3"><label class="form-label">Value</label><input type="number" step="0.01" name="value" class="form-control"></div>
      <div class="mb-3"><label class="form-label">Status</label>
        <select name="status" class="form-control"><option value="open">Open</option><option value="won">Won</option><option value="lost">Lost</option></select>
      </div>
      <div class="d-flex gap-2"><button type="submit" class="btn btn-primary">Save Lead</button><a href="{{ route('leads.index') }}" class="btn btn-secondary">Back</a></div>
    </form>
  </div>
</div>
@endsection
