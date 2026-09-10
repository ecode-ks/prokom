@extends('template.template')

@section('title', isset($editReport) ? __('pages.update_project_report') : __('app.nav_project_reports'))

@section('content')
    @if(isset($editReport))
        <small class="d-block txt-lightblue fw-semibold text-uppercase"><a href="{{ route('project.reports') }}" class="text-decoration-none txt-lightblue fw-semibold"><i class="fa-solid fa-circle-chevron-left"></i> {{ __('app.back_full') }}</a></small>
        <h3 class="fw-semibold pt-2">{{ __('pages.update_project_report') }}</h3>
        <p class="text-muted">{{ __('pages.update_report_lead') }}</p>
        <hr class="mb-4">
    @else
        <small class="d-block txt-lightblue fw-semibold text-uppercase">{{ __('app.nav_projects') }}</small>
        <h3 class="fw-semibold pt-2">{{ __('pages.reports_of') }} <span class="txt-lightblue fw-semibold">{{ __('pages.projects_genitive') }}</span></h3>
        <p class="text-muted">{{ __('pages.project_reports_lead') }}</p>
        <hr class="mb-4">
    @endif

    @if(isset($editReport))

        <div class="card mb-4">
            <div class="card-header rounded-top-4 bg-white">
                <span><i class="fa-solid fa-file-lines txt-lightblue py-2"></i> {{ __('pages.update_project_report') }}</span>
            </div>
            <div class="card-body">
                <form action="{{ route('update.project.report') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" value="{{ $editReport->id }}">
                    <div class="row g-3">
                        <div class="col-12 col-md-4">
                            <label for="project_id" class="form-label">{{ __('pages.project') }}</label>
                            <select name="project_id" id="project_id" class="form-control shadow-sm rounded-3" required>
                                <option value="" disabled {{ old('project_id', $editReport->project_id) ? '' : 'selected' }}>{{ __('pages.choose_project') }}</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}" {{ old('project_id', $editReport->project_id) == $project->id ? 'selected' : '' }}>{{ $project->project_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-8">
                            <label for="images" class="form-label">{{ __('pages.images_facts') }} <small class="text-muted">{{ __('pages.images_keep_existing') }}</small></label>
                            <input type="file" name="images[]" id="images" class="form-control shadow-sm rounded-3" multiple accept="image/*">
                            @php
                                $existingImages = json_decode($editReport->images ?? '[]', true);
                            @endphp
                            @if(!empty($existingImages) && is_array($existingImages))
                                <small class="d-block text-muted mt-1">{{ __('pages.existing_images_count', ['count' => count($existingImages)]) }}</small>
                            @endif
                        </div>
                        <div class="col-12">
                            <label for="report" class="form-label">{{ __('pages.report') }}</label>
                            <textarea name="report" id="report" class="form-control shadow-sm rounded-3" rows="5" placeholder="{{ __('pages.placeholder_report') }}" required>{{ old('report', $editReport->report) }}</textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-darkblue rounded-3 px-4 shadow-sm"><i class="fa-solid fa-check"></i> {{ __('pages.update_report') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    @else

        {{-- Info Cards --}}
        <div class="row g-3 mb-4">
            <div class="col-12 col-md-6 col-lg-3">
                <div class="card h-100">
                    <div class="card-body">
                        <small class="text-uppercase opacity-75"><i class="fa-solid fa-list-check text-primary"></i> {{ __('app.active') }}</small>
                        <h4 class="fw-semibold mb-0">{{ $reportStats->active_count ?? 0 }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-3">
                <div class="card h-100">
                    <div class="card-body">
                        <small class="text-uppercase opacity-75"><i class="fa-solid fa-magnifying-glass text-warning"></i> {{ __('pages.status_review') }}</small>
                        <h4 class="fw-semibold mb-0">{{ $reportStats->under_review_count ?? 0 }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-3">
                <div class="card h-100">
                    <div class="card-body">
                        <small class="text-uppercase opacity-75"><i class="fa-solid fa-check text-success"></i> {{ __('pages.resolved_plural') }}</small>
                        <h4 class="fw-semibold mb-0">{{ $reportStats->resolved_count ?? 0 }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-3">
                <div class="card h-100">
                    <div class="card-body">
                        <small class="text-uppercase opacity-75"><i class="fa-solid fa-lock text-secondary"></i> {{ __('pages.closed_plural') }}</small>
                        <h4 class="fw-semibold mb-0">{{ $reportStats->closed_count ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header rounded-top-4 bg-white">
                <span><i class="fa-solid fa-file-lines txt-lightblue py-2"></i> {{ __('pages.create_project_report') }}</span>
            </div>
            <div class="card-body">
                <form action="{{ route('create.project.report') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12 col-md-4">
                            <label for="project_id" class="form-label">{{ __('pages.project') }}</label>
                            <select name="project_id" id="project_id" class="form-control shadow-sm rounded-3" required>
                                <option value="" disabled {{ old('project_id') ? '' : 'selected' }}>{{ __('pages.choose_project') }}</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>{{ $project->project_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-8">
                            <label for="images" class="form-label">{{ __('pages.images_facts_optional') }}</label>
                            <input type="file" name="images[]" id="images" class="form-control shadow-sm rounded-3" multiple accept="image/*">
                        </div>
                        <div class="col-12">
                            <label for="report" class="form-label">{{ __('pages.report') }}</label>
                            <textarea name="report" id="report" class="form-control shadow-sm rounded-3" rows="5" placeholder="{{ __('pages.placeholder_report') }}" required>{{ old('report') }}</textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-darkblue rounded-3 px-4 shadow-sm"><i class="fa-solid fa-plus"></i> {{ __('pages.create_report_btn') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header rounded-top-4 bg-white">
                <span><i class="fa-solid fa-list-ol txt-lightblue py-2"></i> {{ __('pages.project_reports_list') }}</span>
            </div>
            <div class="card-body">
                <form action="{{ route('project.reports') }}" method="get" class="input-group mb-3">
                    <input type="text" name="search" id="search" value="{{ request('search') }}" class="form-control shadow-sm rounded-start-3" placeholder="{{ __('app.search') }}" aria-label="{{ __('app.search') }}" aria-describedby="basic-addon2">
                    <button type="submit" class="btn btn-darkblue shadow-sm rounded-end-3" id="basic-addon2"><i class="fa-solid fa-magnifying-glass"></i></button>
                </form>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th scope="col" class="text-nowrap">#</th>
                                <th scope="col" class="text-nowrap">{{ __('app.date') }}</th>
                                <th scope="col" class="text-nowrap">{{ __('pages.project') }}</th>
                                <th scope="col" class="text-nowrap">{{ __('pages.manager_col') }}</th>
                                <th scope="col" class="text-nowrap">{{ __('pages.client') }}</th>
                                <th scope="col" class="text-nowrap">{{ __('pages.reported_by') }}</th>
                                <th scope="col" class="text-nowrap">{{ __('app.status') }}</th>
                                <th scope="col" class="text-nowrap text-end">{{ __('app.operations') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($reports) === 0)
                                <tr>
                                    <td colspan="8" class="text-center"><i class="fa-regular fa-face-frown"></i> {{ __('app.no_data') }}</td>
                                </tr>
                            @else
                                @foreach($reports as $report)
                                    <tr>
                                        <th scope="row" class="text-nowrap">{{ $report->id }}</th>
                                        <td class="text-nowrap">{{ \Carbon\Carbon::parse($report->date)->format('d/m/Y') }}</td>
                                        <td class="text-nowrap">{{ $report->project_name }}</td>
                                        <td class="text-nowrap">{{ $report->manager_name ?? '-' }}</td>
                                        <td class="text-nowrap">{{ $report->client_name ?? '-' }}</td>
                                        <td class="text-nowrap">{{ $report->reporter_name ?? '-' }}</td>
                                        <td class="text-nowrap">
                                            @if($report->status === 'active' || $report->status === 'new')
                                                <span class="badge rounded-pill text-primary text-bg-primary bg-opacity-25 border border-primary">{{ __('pages.status_active') }}</span>
                                            @elseif($report->status === 'under_review')
                                                <span class="badge rounded-pill text-dark text-bg-warning bg-opacity-25 border border-warning">{{ __('pages.status_review') }}</span>
                                            @elseif($report->status === 'resolved')
                                                <span class="badge rounded-pill text-success text-bg-success bg-opacity-25 border border-success">{{ __('pages.status_resolved') }}</span>
                                            @else
                                                <span class="badge rounded-pill text-secondary text-bg-secondary bg-opacity-25 border border-secondary">{{ __('pages.status_closed') }}</span>
                                            @endif
                                        </td>
                                        <td class="text-nowrap text-end">
                                            <a href="{{ route('edit.project.report', $report->id) }}" class="btn btn-outline-success btn-sm rounded-3 shadow-sm"><i class="fa-solid fa-pen"></i></a>
                                            <button class="btn btn-outline-danger btn-sm rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $report->id }}"><i class="fa-solid fa-trash"></i></button>
                                            <a href="{{ route('project.report.details', $report->id) }}" class="btn btn-outline-dark btn-sm rounded-3 shadow-sm"><i class="fa-solid fa-eye"></i></a>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>

                @if($reports->hasPages())
                    <div class="mt-3">
                        {{ $reports->links('vendor.pagination.custom') }}
                    </div>
                @endif
            </div>
        </div>

        @if(count($reports) !== 0)
            @foreach($reports as $report)
                <div class="modal fade" id="deleteModal{{ $report->id }}" tabindex="-1" aria-labelledby="deleteModal{{ $report->id }}Label" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-body text-center">
                                <p>{{ __('pages.confirm_delete_report') }}</p>
                                <div class="mt-3">
                                    <a href="{{ route('delete.project.report', $report->id) }}" class="btn btn-danger btn-sm fw-semibold"><i class="fa-solid fa-check"></i> {{ __('app.confirm') }}</a>
                                    <button class="btn btn-success btn-sm fw-semibold" data-bs-dismiss="modal"><i class="fa-solid fa-xmark"></i> {{ __('app.cancel') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    @endif
@endsection
