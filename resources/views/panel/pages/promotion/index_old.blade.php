@extends('panel.layouts.app')
@section('title', 'Promotion Lists')
@section('content')
    {{-- <script src="{{ url('assets/admin/js/bootbox.js') }}"></script> --}}

    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0"></h4>
                <div class="page-title-right">
                    <a class="btn btn-primary" href="{{ route('admin.promotions.create') }}">
                        <em class="icon ni ni-plus"></em> Add Promotion
                    </a>
                </div>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Promotion Lists</h4>
                </div><!-- end card header -->

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <span class="text-muted pull-right show-count">Showing
                                {{ $records->currentPage() * $records->perPage() - $records->perPage() + 1 }} to
                                {{ $records->currentPage() * $records->perPage() > $records->total() ? $records->total() : $records->currentPage() * $records->perPage() }}
                                of {{ $records->total() }} data(s)</span>
                        </div>
                        <div class="col-md-8 mb-2">
                            <form method="GET" action="{{ url('admin/promotions') }}">
                                <div class="row mb-3">
                                    <!-- Course Name -->
                                    <div class="col-md-4">
                                        <input type="text" name="course_name" class="form-control" 
                                            placeholder="Search by Course Name" value="{{ request('course_name') }}">
                                    </div>
                            
                                    <!-- Discount Type -->
                                    <div class="col-md-4">
                                        <select name="discount_type" class="form-control">
                                            <option value="">Select Discount Type</option>
                                            <option value="timer" {{ request('discount_type') == 'timer' ? 'selected' : '' }}>Timer</option>
                                            <option value="first_some_student" {{ request('discount_type') == 'first_some_student' ? 'selected' : '' }}>First Some Student</option>
                                            <option value="special_day" {{ request('discount_type') == 'special_day' ? 'selected' : '' }}>Special Day</option>
                                        </select>
                                    </div>
                            
                                    <!-- Submit Button -->
                                    <div class="col-md-4">
                                        <button type="submit" class="btn btn-primary">Search</button>
                                        <a href="{{ url('admin/promotions') }}" class="btn btn-secondary">Reset</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="live-preview">
                        <div class="table-responsive">
                            <table class="table table-striped table-nowrap table-bordered align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>SL</th>
                                        <th>Course Name</th>
                                        <th>Discount Type</th>
                                        <th>Start Time</th>
                                        <th>End Time</th>
                                        <th>Student Limit</th>
                                        <th>Day Title</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Discount Value Type</th>
                                        <th>Discount Value</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $count = 1; @endphp
                                    @forelse($records as $val)
                                        <tr>
                                            <td>{{ $count }}</td>
                                            <td>{{ optional($val->get_course)->title ?? "All Courses" }}</td>
                                            <td>{{ $val->discount_type }}</td>
                                            <td>{{ $val->start_time ?? '' }}</td>
                                            <td>{{ $val->end_time ?? '' }}</td>
                                            <td>{{ $val->student_limit ?? '' }}</td>
                                            <td>{{ $val->day_title ?? '' }}</td>
                                            <td>{{ $val->start_date ?? '' }}</td>
                                            <td>{{ $val->end_date ?? '' }}</td>
                                            <td>{{ $val->discount_value_type }}</td>
                                            <td>
                                                {{ $val->discount_value_type == 'percentage' ? $val->discount_value . '%' : $val->discount_value }}
                                            </td>
                                            <td>
                                                <button class="btn btn-sm {{ $val->status == 1 ? 'btn-success' : 'btn-danger' }} toggle-status" 
                                                    data-id="{{ $val->id }}" 
                                                    data-status="{{ $val->status }}">
                                                {{ $val->status == 1 ? 'Active' : 'Inactive' }}
                                            </button>
                                            </td>
                                            <td>
                                                <a class="badge bg-success"
                                                    href="{{ url('admin/promotions/' . $val->id . '/edit') }}"><em
                                                        class="icon ni ni-edit"></em></a>

                                                <form action="{{ route('admin.promotions.destroy',$val->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete Promotion?')">
                                                    @csrf @method('POST')
                                                    <button class="badge bg-danger"><em class="icon ni ni-trash"></em></button>
                                                </form>

                                            </td>
                                        </tr>
                                        @php $count++ @endphp
                                    @empty
                                        <tr>
                                            <td colspan="7">No Data Found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="mt-3">
                        @if ($records->lastPage() > 1)

                            <nav aria-label="Page navigation example">
                                <ul class="pagination">

                                    @if ($records->currentPage() != 1 && $records->lastPage() >= 5)
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $records->url($records->url(1)) }}">
                                                ← &nbsp; Prev </a>
                                        </li>
                                    @endif

                                    @if ($records->currentPage() != 1)
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $records->url($records->currentPage() - 1) }}">
                                                < </a>
                                        </li>
                                    @endif

                                    @for ($i = max($records->currentPage() - 2, 1); $i <= min(max($records->currentPage() - 2, 1) + 4, $records->lastPage()); $i++)
                                        <li class="page-item {{ $records->currentPage() == $i ? ' active' : '' }}">
                                            <a class="page-link" href="{{ $records->url($i) }}">{{ $i }}</a>
                                        </li>
                                    @endfor

                                    @if ($records->currentPage() != $records->lastPage())
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $records->url($records->currentPage() + 1) }}">
                                                >
                                            </a>
                                        </li>
                                    @endif

                                    @if ($records->currentPage() != $records->lastPage() && $records->lastPage() >= 5)
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $records->url($records->lastPage()) }}">
                                                Next &nbsp; →
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </nav>
                        @endif
                    </div>
                </div><!-- end card-body -->
            </div><!-- end card -->
        </div>
        <!-- end col -->
    </div>
    <!-- end row -->

    <script>
        function deletePromotion(id) {
            bootbox.confirm({
                message: "Do you want to delete?",
                buttons: {
                    confirm: {
                        label: 'Yes',
                        className: 'btn-success'
                    },
                    cancel: {
                        label: 'No',
                        className: 'btn-danger'
                    }
                },
                callback: function(result) {
                    if (result == true) {
                        $.ajax({
                            type: 'POST',
                            url: '{{ url('admin/promotions/destroy') }}',
                            data: {
                                "id": id,
                                "_token": "{{ csrf_token() }}"
                            },
                            success: function(response) {
                                toastr.options = {
                                    "closeButton": true,
                                    "progressBar": true
                                }
                                if (response.status == 1) {
                                    toastr.success(response.text);
                                    location.reload();
                                } else if (response.status == 2) {
                                    toastr.error(response.text);
                                } else {
                                    toastr.error(response.text);
                                }
                            }
                        });
                    }
                }
            });
        }

    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.toggle-status').forEach(function (button) {
                button.addEventListener('click', function () {
                    const id = this.getAttribute('data-id');
                    const currentStatus = this.getAttribute('data-status');
                    const newStatus = currentStatus == 1 ? 0 : 1;

                    // Send AJAX request to update the status
                    fetch(`/admin/promotions/${id}/update-status`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ status: newStatus })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Update button status and appearance
                            this.setAttribute('data-status', newStatus);
                            this.textContent = newStatus == 1 ? 'Active' : 'Inactive';
                            this.className = `btn btn-sm ${newStatus == 1 ? 'btn-success' : 'btn-danger'} toggle-status`;
                            //alert('Status updated successfully!');
                        } else {
                            alert('Failed to update status. Please try again.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while updating the status.');
                    });
                });
            });
        });
    </script>


    <style>
        table.table td {
            vertical-align: middle;
        }
    </style>

@endsection
