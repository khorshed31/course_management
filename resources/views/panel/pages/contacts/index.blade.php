@extends('panel.layouts.app')

@section('title','Contacts')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
  <h1 class="h4 mb-0">Contact Inbox</h1>
  <form class="d-flex m-2 column-gap-4" method="get" action="{{ route('admin.contacts.index') }}">
      <input class="form-control" type="search" name="q" placeholder="Search name/email/message" value="{{ $filters['q'] ?? '' }}">
      <select name="status" class="form-select">
          <option value="">All statuses</option>
          @foreach(['open','replied','closed'] as $st)
            <option value="{{ $st }}" {{ ($filters['status']??'')===$st?'selected':'' }}>{{ ucfirst($st) }}</option>
          @endforeach
      </select>
      <div class="form-check">
        <input class="form-check-input" type="checkbox" name="star" value="1" id="star" {{ !empty($filters['star'])?'checked':'' }}>
        <label class="form-check-label" for="star">Starred</label>
      </div>
      <button class="btn btn-primary">Filter</button>
  </form>
</div>

<div class="card">
  <div class="table-responsive">
    <table class="table align-middle mb-0">
      <thead>
        <tr>
          <th></th>
          <th>From</th>
          <th>Email</th>
          <th>Snippet</th>
          <th>Status</th>
          <th>Replies</th>
          <th>Received</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        @foreach($messages as $m)
        <tr>
          <td>
            <form method="post" action="{{ route('admin.contacts.toggle-star',$m) }}">
              @csrf
              <button class="btn btn-sm {{ $m->is_starred?'btn-warning':'btn-outline-secondary' }}">
                <i class="bi bi-star{{ $m->is_starred?'-fill':'' }}"></i>
              </button>
            </form>
          </td>
          <td class="fw-semibold">{{ $m->name }}</td>
          <td><a href="mailto:{{ $m->email }}">{{ $m->email }}</a></td>
          <td class="text-muted">{{ \Illuminate\Support\Str::limit($m->message, 70) }}</td>
          <td><span class="badge bg-{{ $m->status==='open'?'secondary':($m->status==='replied'?'info':'success') }}">{{ ucfirst($m->status) }}</span></td>
          <td>{{ $m->reply_count }}</td>
          <td>{{ $m->created_at->diffForHumans() }}</td>
          <td>
            <a href="{{ route('admin.contacts.show',$m) }}" class="btn btn-sm btn-outline-primary">Open</a>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <div class="card-body">
    {{ $messages->links() }}
  </div>
</div>
@endsection

