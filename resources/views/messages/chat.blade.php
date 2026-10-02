@extends('layouts.app')

@section('content')
<style>
  .chat-container {
    display: flex;
    flex-direction: column;
    height: calc(100vh - 120px);
    max-width: 900px;
    margin: 0 auto;
  }

  .chat-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem 1.25rem;
    background: linear-gradient(145deg, rgba(15,15,22,0.95), rgba(8,8,12,0.98));
    border: 1px solid rgba(255,255,255,0.06);
    border-radius: 16px 16px 0 0;
  }

  .chat-back {
    color: var(--green-400);
    font-size: 1.2rem;
    text-decoration: none;
    transition: color 0.2s;
  }
  .chat-back:hover { color: var(--green-500); }

  .chat-header-avatar {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--green-600), var(--green-400));
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 1rem;
    color: #fff;
    text-transform: uppercase;
    box-shadow: 0 0 10px rgba(34,197,94,0.2);
  }

  .chat-header-info h5 {
    margin: 0;
    font-weight: 700;
    color: #fff;
    font-size: 1rem;
  }
  .chat-header-info small {
    color: rgba(255,255,255,0.4);
    font-size: 0.75rem;
    text-transform: capitalize;
  }

  .chat-messages {
    flex: 1;
    overflow-y: auto;
    padding: 1.5rem 1.25rem;
    background: rgba(5,5,10,0.6);
    border-left: 1px solid rgba(255,255,255,0.06);
    border-right: 1px solid rgba(255,255,255,0.06);
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
  }
  .chat-messages::-webkit-scrollbar { width: 6px; }
  .chat-messages::-webkit-scrollbar-thumb {
    background: rgba(34,197,94,0.2);
    border-radius: 3px;
  }

  .msg-row {
    display: flex;
    max-width: 75%;
  }
  .msg-row.sent {
    margin-left: auto;
    justify-content: flex-end;
  }
  .msg-row.received {
    margin-right: auto;
    justify-content: flex-start;
  }

  .msg-bubble {
    padding: 0.7rem 1rem;
    border-radius: 18px;
    font-size: 0.88rem;
    line-height: 1.5;
    position: relative;
    word-wrap: break-word;
  }
  .msg-row.sent .msg-bubble {
    background: linear-gradient(135deg, var(--green-600), var(--green-500));
    color: #fff;
    border-bottom-right-radius: 4px;
    box-shadow: 0 2px 12px rgba(34,197,94,0.2);
  }
  .msg-row.received .msg-bubble {
    background: rgba(30,30,42,0.9);
    color: #e2e8f0;
    border: 1px solid rgba(255,255,255,0.08);
    border-bottom-left-radius: 4px;
  }

  .msg-time {
    font-size: 0.65rem;
    color: rgba(255,255,255,0.3);
    margin-top: 4px;
    display: block;
  }
  .msg-row.sent .msg-time { text-align: right; color: rgba(255,255,255,0.5); }
  .msg-row.received .msg-time { text-align: left; }

  .msg-read-tick {
    font-size: 0.6rem;
    color: rgba(255,255,255,0.5);
    margin-left: 4px;
  }

  .date-divider {
    text-align: center;
    font-size: 0.72rem;
    color: rgba(255,255,255,0.25);
    padding: 0.5rem 0;
    position: relative;
  }
  .date-divider::before,
  .date-divider::after {
    content: '';
    position: absolute;
    top: 50%;
    width: 30%;
    height: 1px;
    background: rgba(255,255,255,0.06);
  }
  .date-divider::before { left: 5%; }
  .date-divider::after { right: 5%; }

  .chat-composer {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem 1.25rem;
    background: linear-gradient(145deg, rgba(15,15,22,0.95), rgba(8,8,12,0.98));
    border: 1px solid rgba(255,255,255,0.06);
    border-radius: 0 0 16px 16px;
  }

  .chat-input {
    flex: 1;
    background: rgba(30,30,42,0.6);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 24px;
    padding: 0.65rem 1.2rem;
    color: #e2e8f0;
    font-size: 0.88rem;
    resize: none;
    max-height: 120px;
    line-height: 1.4;
    transition: border-color 0.3s;
  }
  .chat-input::placeholder { color: rgba(255,255,255,0.25); }
  .chat-input:focus {
    outline: none;
    border-color: rgba(34,197,94,0.4);
    box-shadow: 0 0 0 3px rgba(34,197,94,0.08);
  }

  .chat-send {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    border: none;
    background: linear-gradient(135deg, var(--green-600), var(--green-500));
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s;
    box-shadow: 0 2px 10px rgba(34,197,94,0.3);
    flex-shrink: 0;
  }
  .chat-send:hover {
    transform: scale(1.08);
    box-shadow: 0 4px 16px rgba(34,197,94,0.45);
  }
  .chat-send:disabled {
    opacity: 0.4;
    cursor: not-allowed;
    transform: none;
  }

  .empty-chat {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    flex: 1;
    color: rgba(255,255,255,0.2);
  }
  .empty-chat i { font-size: 3rem; margin-bottom: 1rem; color: rgba(34,197,94,0.15); }

  /* Sending animation */
  .msg-sending .msg-bubble {
    opacity: 0.6;
  }

  @keyframes fadeInUp {
    from { opacity: 0; transform: translateY(8px); }
    to { opacity: 1; transform: translateY(0); }
  }
  .msg-row.new-msg {
    animation: fadeInUp 0.25s ease-out;
  }
</style>

<div class="container py-3">
  <div class="chat-container">

    {{-- Header --}}
    <div class="chat-header">
      <a href="{{ route('messages.index') }}" class="chat-back" title="Back">
        <i class="fa-solid fa-arrow-left"></i>
      </a>
      @php
        $contactInitials = collect(explode(' ', $contact->name))->map(fn($w) => strtoupper(substr($w,0,1)))->take(2)->join('');
      @endphp
      <div class="chat-header-avatar">{{ $contactInitials ?: 'U' }}</div>
      <div class="chat-header-info">
        <h5>{{ $contact->name }}</h5>
        <small>{{ ucfirst($contact->role ?? 'User') }}</small>
      </div>
    </div>

    {{-- Messages --}}
    <div class="chat-messages" id="chatMessages">
      @if($messages->isEmpty())
        <div class="empty-chat" id="emptyChat">
          <i class="fa-regular fa-paper-plane"></i>
          <p>No messages yet.<br>Start the conversation!</p>
        </div>
      @else
        @php $lastDate = null; @endphp
        @foreach($messages as $msg)
          @php
            $msgDate = $msg->created_at->format('M d, Y');
            $isSent = $msg->sender_id === auth()->id();
          @endphp
          @if($msgDate !== $lastDate)
            <div class="date-divider">{{ $msg->created_at->isToday() ? 'Today' : ($msg->created_at->isYesterday() ? 'Yesterday' : $msgDate) }}</div>
            @php $lastDate = $msgDate; @endphp
          @endif
          <div class="msg-row {{ $isSent ? 'sent' : 'received' }}" data-msg-id="{{ $msg->id }}">
            <div>
              <div class="msg-bubble">{!! nl2br(e($msg->body)) !!}</div>
              <span class="msg-time">
                {{ $msg->created_at->format('g:i A') }}
                @if($isSent && $msg->read_at)
                  <span class="msg-read-tick" title="Read {{ $msg->read_at->diffForHumans() }}">
                    <i class="fa-solid fa-check-double"></i>
                  </span>
                @elseif($isSent)
                  <span class="msg-read-tick"><i class="fa-solid fa-check"></i></span>
                @endif
              </span>
            </div>
          </div>
        @endforeach
      @endif
    </div>

    {{-- Composer (no longer a <form> that submits) --}}
    <div class="chat-composer" id="chatComposer">
      <textarea class="chat-input" id="chatInput" rows="1"
                placeholder="Type a message…" maxlength="5000"></textarea>
      <button type="button" class="chat-send" id="sendBtn" title="Send" disabled>
        <i class="fa-solid fa-paper-plane"></i>
      </button>
    </div>

  </div>
</div>

<script>
(function() {
  const chatBox      = document.getElementById('chatMessages');
  const input        = document.getElementById('chatInput');
  const sendBtn      = document.getElementById('sendBtn');
  const emptyChat    = document.getElementById('emptyChat');
  const receiverId   = {{ $contact->id }};
  const csrfToken    = '{{ csrf_token() }}';
  const storeUrl     = '{{ route("messages.store") }}';
  const fetchUrl     = '{{ route("messages.fetch", $contact->id) }}';

  let lastMsgId      = {{ $messages->last()?->id ?? 0 }};
  let isSending      = false;

  // Auto-scroll to bottom on load
  scrollToBottom();

  // ── Auto-resize textarea ──
  input.addEventListener('input', function() {
    this.style.height = 'auto';
    this.style.height = Math.min(this.scrollHeight, 120) + 'px';
    sendBtn.disabled = !this.value.trim();
  });

  // ── Send on Enter (Shift+Enter = newline) ──
  input.addEventListener('keydown', function(e) {
    if (e.key === 'Enter' && !e.shiftKey) {
      e.preventDefault();
      if (this.value.trim() && !isSending) sendMessage();
    }
  });

  // ── Click send button ──
  sendBtn.addEventListener('click', function() {
    if (input.value.trim() && !isSending) sendMessage();
  });

  // ── AJAX Send ──
  function sendMessage() {
    const body = input.value.trim();
    if (!body) return;

    isSending = true;
    sendBtn.disabled = true;

    // Remove empty state if present
    if (emptyChat) emptyChat.remove();

    // Optimistically append the message
    const tempId = 'temp-' + Date.now();
    appendMessage({
      id: tempId,
      body: body,
      is_sent: true,
      created_at: new Date().toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true }),
      sending: true,
    });

    // Clear input
    input.value = '';
    input.style.height = 'auto';
    scrollToBottom();

    // Send via fetch
    fetch(storeUrl, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
      },
      body: JSON.stringify({ receiver_id: receiverId, body: body }),
    })
    .then(res => {
      if (!res.ok) throw new Error('Send failed');
      return res.json();
    })
    .then(data => {
      // Replace temp message with real one
      const tempEl = document.querySelector(`[data-msg-id="${tempId}"]`);
      if (tempEl) {
        tempEl.setAttribute('data-msg-id', data.message.id);
        tempEl.classList.remove('msg-sending');
        const bubble = tempEl.querySelector('.msg-bubble');
        if (bubble) bubble.style.opacity = '1';
        // Add single check
        const timeEl = tempEl.querySelector('.msg-time');
        if (timeEl && !timeEl.querySelector('.msg-read-tick')) {
          timeEl.innerHTML = data.message.created_at + ' <span class="msg-read-tick"><i class="fa-solid fa-check"></i></span>';
        }
      }
      lastMsgId = Math.max(lastMsgId, data.message.id);
    })
    .catch(err => {
      console.error('Message send error:', err);
      // Mark as failed
      const tempEl = document.querySelector(`[data-msg-id="${tempId}"]`);
      if (tempEl) {
        const bubble = tempEl.querySelector('.msg-bubble');
        if (bubble) {
          bubble.style.opacity = '1';
          bubble.style.borderColor = 'rgba(239,68,68,0.5)';
        }
        const timeEl = tempEl.querySelector('.msg-time');
        if (timeEl) timeEl.innerHTML += ' <span style="color:#f87171;">Failed</span>';
      }
    })
    .finally(() => {
      isSending = false;
      sendBtn.disabled = !input.value.trim();
    });
  }

  // ── Append a message bubble to the chat ──
  function appendMessage(msg) {
    const row = document.createElement('div');
    row.className = 'msg-row ' + (msg.is_sent ? 'sent' : 'received') + ' new-msg' + (msg.sending ? ' msg-sending' : '');
    row.setAttribute('data-msg-id', msg.id);

    const escapedBody = escapeHtml(msg.body).replace(/\n/g, '<br>');
    const tickHtml = msg.is_sent
      ? (msg.read_at
          ? ' <span class="msg-read-tick"><i class="fa-solid fa-check-double"></i></span>'
          : ' <span class="msg-read-tick"><i class="fa-solid fa-check"></i></span>')
      : '';

    row.innerHTML = `
      <div>
        <div class="msg-bubble">${escapedBody}</div>
        <span class="msg-time">${msg.created_at}${tickHtml}</span>
      </div>
    `;

    chatBox.appendChild(row);
  }

  // ── Poll for new messages every 3 seconds ──
  setInterval(pollMessages, 3000);

  function pollMessages() {
    if (isSending) return; // skip while sending to avoid duplicates

    fetch(fetchUrl + '?after=' + lastMsgId, {
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
      }
    })
    .then(res => res.json())
    .then(messages => {
      if (!messages.length) return;

      // Remove empty state
      if (emptyChat) emptyChat.remove();

      const wasAtBottom = isScrolledToBottom();

      messages.forEach(msg => {
        // Skip if already rendered (e.g. our own optimistic message)
        if (document.querySelector(`[data-msg-id="${msg.id}"]`)) {
          // Update read status if needed
          if (msg.read_at) {
            const el = document.querySelector(`[data-msg-id="${msg.id}"] .msg-read-tick`);
            if (el) el.innerHTML = '<i class="fa-solid fa-check-double"></i>';
          }
          return;
        }
        appendMessage(msg);
        lastMsgId = Math.max(lastMsgId, msg.id);
      });

      // Auto-scroll only if user was already at bottom
      if (wasAtBottom) scrollToBottom();
    })
    .catch(() => {}); // silently ignore poll errors
  }

  // ── Helpers ──
  function scrollToBottom() {
    if (chatBox) chatBox.scrollTop = chatBox.scrollHeight;
  }

  function isScrolledToBottom() {
    return chatBox.scrollHeight - chatBox.scrollTop - chatBox.clientHeight < 50;
  }

  function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
  }
})();
</script>
@endsection
