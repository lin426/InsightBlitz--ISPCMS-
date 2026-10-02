@extends('layouts.app')
@section('content')
<div class="container mt-4" style="max-width:800px;">
  <div class="bubble p-4">
    <h2 class="text-white mb-3" style="font-size:1.4rem;">Leads by Stage</h2>
    <canvas id="leadChart" width="400" height="200"></canvas>
  </div>
</div>
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  Chart.defaults.color = 'rgba(255,255,255,0.5)';
  Chart.defaults.borderColor = 'rgba(255,255,255,0.06)';
  new Chart(document.getElementById('leadChart'), {
    type: 'bar',
    data: {
      labels: {!! json_encode($labels) !!},
      datasets: [{ label: 'Number of Leads', data: {!! json_encode($totals) !!}, backgroundColor: 'rgba(34,197,94,0.5)', borderColor: '#22c55e', borderWidth: 1, borderRadius: 6 }]
    },
    options: { scales: { y: { beginAtZero: true } }, plugins: { legend: { display: false } } }
  });
</script>
@endpush
@endsection
