{{-- Attachments / Proof --}}
<div class="mt-2">
  @php $attachments = $task->attachments ?? collect(); @endphp
  @forelse($attachments as $att)
    <div class="mb-1">
      <a href="{{ route('attachments.show', $att->id) }}" target="_blank" style="color:#4ade80;">
        {{ $att->original_name ?? (isset($att->path) ? basename($att->path) : 'proof') }}
      </a>
      @if(!empty($att->size)) <span style="color:rgba(255,255,255,0.3); font-size:0.78rem;">({{ number_format($att->size / 1024, 1) }} KB)</span> @endif
    </div>
  @empty
    <span style="color:rgba(255,255,255,0.3);">No attachments.</span>
  @endforelse
</div>
