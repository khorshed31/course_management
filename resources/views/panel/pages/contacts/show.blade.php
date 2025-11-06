@extends('panel.layouts.app')

@section('title','Conversation #'.$contact->id)

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
  <div>
    <h1 class="h4 mb-1">Conversation #{{ $contact->id }}</h1>
    <div class="text-muted small">{{ $contact->email }} • {{ $contact->created_at->toDayDateTimeString() }}</div>
  </div>
  <div class="d-flex column-gap-3">

    <a href="{{ route('admin.contacts.index') }}" class="btn btn-sm btn-outline-secondary">
        <em class="ni ni-back-alt"></em> Back
    </a>

    <form method="post" action="{{ route('admin.contacts.set-status',$contact) }}">
      @csrf
      <select name="status" class="form-select form-select-sm d-inline w-auto" onchange="this.form.submit()">
        @foreach(['open','replied','closed'] as $st)
          <option value="{{ $st }}" {{ $contact->status===$st?'selected':'' }}>{{ ucfirst($st) }}</option>
        @endforeach
      </select>
    </form>

    <form method="post" action="{{ route('admin.contacts.toggle-star',$contact) }}">
      @csrf
      <button class="btn btn-sm {{ $contact->is_starred?'btn-warning':'btn-outline-secondary' }}">
        <i class="bi bi-star{{ $contact->is_starred?'-fill':'' }}"></i>
      </button>
    </form>

    <form method="post" action="{{ route('admin.contacts.destroy',$contact) }}" onsubmit="return confirm('Move to trash?')">
      @csrf @method('DELETE')
      <button class="btn btn-sm btn-outline-danger">Trash</button>
    </form>
  </div>
</div>

<div class="row g-4">
  <div class="col-lg-7">
    <div class="card mb-3">
      <div class="card-header fw-semibold">Original Message</div>
      <div class="card-body">
        <div class="mb-2"><strong>Name:</strong> {{ $contact->name }}</div>
        <div class="mb-2"><strong>Phone:</strong> {{ $contact->phone ?? '—' }}</div>
        <div class="mb-2"><strong>Social:</strong> {{ $contact->social ?? '—' }}</div>
        <hr>
        <div class="lh-base" style="white-space:pre-wrap">{{ $contact->message }}</div>
      </div>
    </div>

    <div class="card">
      <div class="card-header fw-semibold">Replies ({{ $contact->reply_count }})</div>
      <div class="list-group list-group-flush">
        @forelse($contact->replies as $r)
          <div class="list-group-item">
            <div class="d-flex justify-content-between">
              <div>
                <div class="fw-semibold">{{ $r->subject }}</div>
                <div class="small text-muted">to {{ $r->to_email }} • {{ $r->created_at->toDayDateTimeString() }} • by {{ optional($r->admin)->name ?? 'System' }}</div>
              </div>
            </div>
            <hr class="my-2">
            <div class="reply-body">{!! $r->body !!}</div>
          </div>
        @empty
          <div class="list-group-item text-muted">No replies yet.</div>
        @endforelse
      </div>
    </div>
  </div>

  <div class="col-lg-5">
    <div class="card">
      <div class="card-header fw-semibold">Send a Reply</div>
      <div class="card-body">
        @if ($errors->any())
          <div class="alert alert-danger">
            <ul class="mb-0">
              @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
          </div>
        @endif
        @if (session('status'))
          <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        <form method="post" id="admin-reply-form" action="{{ route('admin.contacts.reply',$contact) }}">
          @csrf
          <div class="mb-3">
            <label class="form-label">To</label>
            <input class="form-control" value="{{ $contact->email }}" disabled>
          </div>
          <div class="mb-3">
            <label class="form-label">Subject</label>
            <input name="subject" class="form-control" required placeholder="Re: Message from {{ $contact->name }}" value="Re: Message from {{ $contact->name }}">
          </div>
          <div class="mb-3">
            <label class="form-label">Message (HTML allowed)</label>
            <textarea name="body_html" class="form-control" rows="8" placeholder="Write your reply..." required></textarea>
          </div>
          <button type="submit" id="reply-submit"
                class="btn btn-primary d-flex align-items-center justify-content-center">
            <span class="btn-text">Send Reply</span>
            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
         </button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('admin-reply-form');
  if (!form) return;

  const submitBtn = document.getElementById('reply-submit');
  const btnText = submitBtn.querySelector('.btn-text');
  const spinner = submitBtn.querySelector('.spinner-border');

  form.addEventListener('submit', function (e) {
    // built-in HTML5 validation first
    if (!form.checkValidity()) {
      form.classList.add('was-validated');
      return;
    }

    // prevent double-submits
    if (submitBtn.dataset.submitted === '1') {
      e.preventDefault();
      return;
    }
    submitBtn.dataset.submitted = '1';

    // show preloader
    submitBtn.disabled = true;
    btnText.textContent = 'Sending…';
    spinner.classList.remove('d-none');
  });
});
</script>
@endpush

@push('styles')
<style>
    #reply-submit .spinner-border{ animation-duration:.7s; }
</style>
@endpush
