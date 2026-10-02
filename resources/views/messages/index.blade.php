@extends('layouts.app')

@section('content')
<style>
  /* ── Conversations List ── */
  .msg-page-title {
    font-size: 2rem;
    font-weight: 800;
    background: linear-gradient(135deg, #22c55e, #4ade80, #86efac);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
  }
  .msg-subtitle {
    color: rgba(255,255,255,0.45);
    font-size: 0.92rem;
    margin-bottom: 1.5rem;
  }

  .conversations-list {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
  }

  .conv-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem 1.25rem;
    background: linear-gradient(145deg, rgba(15,15,22,0.95), rgba(8,8,12,0.98));
    border: 1px solid rgba(255,255,255,0.06);
    border-radius: 16px;
    text-decoration: none;
    color: #e2e8f0;
    transition: all 0.3s ease;
    position: relative;
  }
  .conv-item:hover {
    border-color: rgba(34,197,94,0.3);
    background: linear-gradient(145deg, rgba(20,20,30,0.95), rgba(12,12,18,0.98));
    transform: translateY(-1px);
    color: #fff;
  }
  .conv-item.has-unread {
    border-color: rgba(34,197,94,0.25);
    background: linear-gradient(145deg, rgba(34,197,94,0.06), rgba(8,8,12,0.98));
  }

  .conv-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--green-600), var(--green-400));
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 1.1rem;
    color: #fff;
    flex-shrink: 0;
    text-transform: uppercase;
    box-shadow: 0 0 12px rgba(34,197,94,0.2);
  }

  .conv-body {
    flex: 1;
    min-width: 0;
  }
  .conv-name {
    font-weight: 600;
    font-size: 0.95rem;
    color: #fff;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }
  .conv-role-tag {
    font-size: 0.65rem;
    padding: 2px 8px;
    border-radius: 20px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }
  .conv-role-tag.admin { background: rgba(239,68,68,0.15); color: #f87171; }
  .conv-role-tag.staff { background: rgba(59,130,246,0.15); color: #60a5fa; }
  .conv-role-tag.client { background: rgba(34,197,94,0.15); color: #4ade80; }

  .conv-preview {
    font-size: 0.82rem;
    color: rgba(255,255,255,0.4);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 400px;
    margin-top: 2px;
  }
  .conv-item.has-unread .conv-preview { color: rgba(255,255,255,0.7); }

  .conv-meta {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 4px;
    flex-shrink: 0;
  }
  .conv-time {
    font-size: 0.72rem;
    color: rgba(255,255,255,0.3);
  }
  .conv-item.has-unread .conv-time { color: var(--green-400); }

  .unread-badge {
    width: 22px;
    height: 22px;
    border-radius: 50%;
    background: var(--green-500);
    color: #000;
    font-size: 0.68rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 0 8px rgba(34,197,94,0.4);
  }

  .no-conversations {
    text-align: center;
    padding: 4rem 2rem;
    color: rgba(255,255,255,0.3);
  }
  .no-conversations i {
    font-size: 3rem;
    margin-bottom: 1rem;
    color: rgba(34,197,94,0.2);
  }

  .search-box {
    background: rgba(15,15,22,0.8);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 12px;
    padding: 0.6rem 1rem;
    color: #e2e8f0;
    font-size: 0.88rem;
    width: 100%;
    max-width: 350px;
    transition: border-color 0.3s;
  }
  .search-box::placeholder { color: rgba(255,255,255,0.25); }
  .search-box:focus {
    outline: none;
    border-color: rgba(34,197,94,0.4);
    box-shadow: 0 0 0 3px rgba(34,197,94,0.08);
  }
</style>

<div class="container py-4">
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
    <div>
      <h1 class="msg-page-title m-0">
        <i class="fa-solid fa-comments me-2" style="font-size:1.6rem;"></i>Messages
      </h1>
      <p class="msg-subtitle m-0 mt-1">
        @if(auth()->user()->isClient())
          Chat with administrators
        @elseif(auth()->user()->isStaff())
          Chat with admins &amp; team members
        @else
          Chat with staff &amp; clients
        @endif
      </p>
    </div>
    <div>
      <input type="text" class="search-box" id="searchConversations" placeholder="Search conversations…">
    </div>
  </div>

  <div class="conversations-list" id="convList">
    @forelse($conversations as $conv)
      @php
        $c = $conv->contact;
        $initials = collect(explode(' ', $c->name))->map(fn($w) => strtoupper(substr($w,0,1)))->take(2)->join('');
        $roleClass = strtolower($c->role ?? 'user');
        $hasUnread = $conv->unread > 0;
      @endphp
      <a href="{{ route('messages.chat', $c->id) }}"
         class="conv-item {{ $hasUnread ? 'has-unread' : '' }}"
         data-name="{{ strtolower($c->name) }}">
        <div class="conv-avatar">{{ $initials ?: 'U' }}</div>
        <div class="conv-body">
          <div class="conv-name">
            {{ $c->name }}
            <span class="conv-role-tag {{ $roleClass }}">{{ ucfirst($c->role ?? 'User') }}</span>
          </div>
          <div class="conv-preview">
            @if($conv->last_message)
              @if($conv->last_message->sender_id === auth()->id())
                <span style="color:rgba(255,255,255,0.55)">You:</span>
              @endif
              {{ Str::limit($conv->last_message->body, 60) }}
            @else
              <em>No messages yet — say hello!</em>
            @endif
          </div>
        </div>
        <div class="conv-meta">
          @if($conv->last_message)
            <span class="conv-time">{{ $conv->last_message->created_at->diffForHumans(null, true, true) }}</span>
          @endif
          @if($hasUnread)
            <span class="unread-badge">{{ $conv->unread > 9 ? '9+' : $conv->unread }}</span>
          @endif
        </div>
      </a>
    @empty
      <div class="no-conversations">
        <i class="fa-regular fa-comment-dots d-block"></i>
        <p class="mb-0">No conversations yet</p>
      </div>
    @endforelse
  </div>
</div>

<script>
  // Search filter
  document.getElementById('searchConversations')?.addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('.conv-item').forEach(el => {
      el.style.display = el.dataset.name.includes(q) ? '' : 'none';
    });
  });
</script>
@endsection
