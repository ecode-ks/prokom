@extends('template.template')

@section('title', 'Task Reports')

@section('content')
    <small class="d-block txt-lightblue fw-semibold text-uppercase">Tasks</small>
    <h3 class="fw-semibold pt-2">Task Reports</h3>
    <p class="text-muted">Create and track reports related to task execution.</p>
    <hr class="mb-4">

    <div class="card mb-4">
        <div class="card-header rounded-top-4 bg-white">
            <span><i class="fa-solid fa-file-lines txt-lightblue py-2"></i> Create Task Report</span>
        </div>
        <div class="card-body">
            <form action="{{ route('create.task.report') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="row g-3">
                    <div class="col-12 col-md-4">
                        <label for="task_id" class="form-label">Task Title</label>
                        <select name="task_id" id="task_id" class="form-control shadow-sm rounded-3" required>
                            <option value="">-- Select Task Title --</option>
                            @foreach($tasks as $task)
                                <option value="{{ $task->id }}" {{ old('task_id') == $task->id ? 'selected' : '' }}>
                                    #{{ $task->id }} - {{ \Illuminate\Support\Str::limit($task->title, 45) }} ({{ $task->project_name }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-8">
                        <label for="images" class="form-label">Images / Facts (optional)</label>
                        <input type="file" name="images[]" id="images" class="form-control shadow-sm rounded-3" multiple accept="image/*">
                    </div>
                    <div class="col-12">
                        <label for="report" class="form-label">Report</label>
                        <textarea name="report" id="report" class="form-control shadow-sm rounded-3" rows="5" placeholder="Write report details here..." required>{{ old('report') }}</textarea>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-darkblue rounded-3 px-4 shadow-sm"><i class="fa-solid fa-plus"></i> Create Report</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header rounded-top-4 bg-white">
            <span><i class="fa-solid fa-list-ol txt-lightblue py-2"></i> Task Reports List</span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th scope="col" class="text-nowrap">#</th>
                            <th scope="col" class="text-nowrap">Date</th>
                            <th scope="col" class="text-nowrap">Task Title</th>
                            <th scope="col" class="text-nowrap">Status</th>
                            <th scope="col" class="text-nowrap">Reported By</th>
                            <th scope="col" class="text-nowrap text-end">Operations</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($reports) === 0)
                            <tr>
                                <td colspan="6" class="text-center"><i class="fa-regular fa-face-frown"></i> No data found...</td>
                            </tr>
                        @else
                            @foreach($reports as $report)
                                <tr>
                                    <th scope="row" class="text-nowrap">{{ $report->id }}</th>
                                    <td class="text-nowrap">{{ \Carbon\Carbon::parse($report->date)->format('d/m/Y') }}</td>
                                    <td class="text-truncate" style="max-width: 340px;">{{ $report->title }}</td>
                                    <td class="text-nowrap">
                                        @if($report->status === 'active' || $report->status === 'new')
                                            <span class="badge rounded-pill text-primary text-bg-primary bg-opacity-25 border border-primary">Active</span>
                                        @elseif($report->status === 'under_review')
                                            <span class="badge rounded-pill text-dark text-bg-warning bg-opacity-25 border border-warning">Under Review</span>
                                        @elseif($report->status === 'resolved')
                                            <span class="badge rounded-pill text-success text-bg-success bg-opacity-25 border border-success">Resolved</span>
                                        @else
                                            <span class="badge rounded-pill text-secondary text-bg-secondary bg-opacity-25 border border-secondary">Closed</span>
                                        @endif
                                    </td>
                                    <td class="text-nowrap">{{ $report->reporter_name ?? '-' }}</td>
                                    <td class="text-nowrap text-end">
                                        <a href="{{ route('task.report.details', $report->id) }}" class="btn btn-outline-dark btn-sm rounded-3 shadow-sm"><i class="fa-solid fa-eye"></i></a>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>

            @if(method_exists($reports, 'hasPages') && $reports->hasPages())
                <div class="mt-3">
                    {{ $reports->links('vendor.pagination.custom') }}
                </div>
            @endif
        </div>
    </div>
@endsection
