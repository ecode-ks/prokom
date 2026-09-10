@extends('template.template')

{{-- Page Title --}}
@section('title', isset($editCompanyNews) ? __('pages.company_news_update_title') : __('pages.company_news_title_full'))

{{-- Page Content --}}
@section('content')
    @if(isset($editCompanyNews))
        <small class="d-block txt-lightblue fw-semibold text-uppercase"><a href="{{ route('company.news') }}" class="text-decoration-none txt-lightblue fw-semibold"><i class="fa-solid fa-circle-chevron-left"></i> {{ __('app.back_full') }}</a></small>
        <h3 class="fw-semibold pt-2">{{ __('pages.company_news_update_title') }}</h3>
        <p class="text-muted">{{ __('pages.company_news_update_lead') }}</p>
        <hr class="mb-4">
    @else
        <small class="d-block txt-lightblue fw-semibold text-uppercase">{{ __('app.nav_overview') }}</small>
        <h3 class="fw-semibold pt-2">{{ __('pages.company_news_title') }} <span class="txt-lightblue fw-semibold">{{ __('pages.company_news_title_highlight') }}</span></h3>
        <p class="text-muted">{{ __('pages.company_news_lead') }}</p>
        <hr class="mb-4">
    @endif

    @if(isset($editCompanyNews))

        {{-- Update News --}}
        <div class="card mb-4">
            <div class="card-header rounded-top-4 bg-white">
                <span><i class="fa-regular fa-bell txt-lightblue py-2"></i> {{ __('pages.company_news_update_card') }}</span>
            </div>
            <div class="card-body">
                <form action="{{ route('update.company.news') }}" method="post">
                    @csrf
                    <input type="hidden" name="id" value="{{ $editCompanyNews->id }}">
                    <div class="row g-3">
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="project_id" class="form-label">{{ __('pages.company_news_project_optional') }}</label>
                            <select name="project_id" id="project_id" class="form-control shadow-sm rounded-3">
                                <option value="">{{ __('pages.company_news_all_projects') }}</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}" {{ old('project_id', $editCompanyNews->project_id) == $project->id ? 'selected' : '' }}>{{ $project->project_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label for="info" class="form-label">{{ __('pages.company_news_info_label') }}</label>
                            <textarea name="info" id="info" class="form-control shadow-sm rounded-3" rows="5" placeholder="{{ __('pages.company_news_info_placeholder') }}">{{ old('info', $editCompanyNews->info) }}</textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-darkblue rounded-3 px-4 shadow-sm"><i class="fa-solid fa-check"></i> {{ __('pages.company_news_update_btn') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @else

        {{-- Create News --}}
        <div class="card mb-4">
            <div class="card-header rounded-top-4 bg-white">
                <span><i class="fa-regular fa-bell txt-lightblue py-2"></i> {{ __('pages.company_news_publish') }}</span>
            </div>
            <div class="card-body">
                <form action="{{ route('create.company.news') }}" method="post">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12 col-md-4 col-lg-4">
                            <label for="project_id" class="form-label">{{ __('pages.company_news_project_optional') }}</label>
                            <select name="project_id" id="project_id" class="form-control shadow-sm rounded-3">
                                <option value="">{{ __('pages.company_news_all_projects') }}</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>{{ $project->project_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label for="info" class="form-label">{{ __('pages.company_news_info_label') }}</label>
                            <textarea name="info" id="info" class="form-control shadow-sm rounded-3" rows="5" placeholder="{{ __('pages.company_news_info_placeholder') }}">{{ old('info') }}</textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-darkblue rounded-3 px-4 shadow-sm"><i class="fa-solid fa-plus"></i> {{ __('pages.company_news_publish_btn') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- News List --}}
        <div class="card">
            <div class="card-header rounded-top-4 bg-white">
                <span><i class="fa-solid fa-list-ol txt-lightblue py-2"></i> {{ __('pages.company_news_list') }}</span>
            </div>
            <div class="card-body">
                <form action="{{ route('company.news') }}" method="get" class="input-group mb-3">
                    <input type="text" name="search" id="search" value="{{ request('search') }}" class="form-control shadow-sm rounded-start-3" placeholder="{{ __('app.search') }}" aria-label="{{ __('app.search') }}" aria-describedby="basic-addon2">
                    <button type="submit" class="btn btn-darkblue shadow-sm rounded-end-3" id="basic-addon2"><i class="fa-solid fa-magnifying-glass"></i></button>
                </form>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th scope="col" class="text-nowrap">#</th>
                                <th scope="col" class="text-nowrap">{{ __('app.date') }}</th>
                                <th scope="col" class="text-nowrap">{{ __('pages.company_news_project') }}</th>
                                <th scope="col" class="text-nowrap text-end">{{ __('app.operations') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($companyNews) === 0)
                                <tr>
                                    <td colspan="4" class="text-center"><i class="fa-regular fa-face-frown"></i> {{ __('app.no_data') }}</td>
                                </tr>
                            @else
                                @foreach ($companyNews as $news)
                                    <tr>
                                        <th scope="row" class="text-nowrap">{{ $news->id }}</th>
                                        <td class="text-nowrap">{{ \Carbon\Carbon::parse($news->date)->format('d/m/Y') }}</td>
                                        <td class="text-nowrap">{{ $news->project_name ?? '-' }}</td>
                                        <td class="text-nowrap text-end">
                                            <a href="{{ route('edit.company.news', $news->id) }}" class="btn btn-outline-success btn-sm rounded-3 shadow-sm"><i class="fa-solid fa-pen"></i></a>
                                            <button class="btn btn-outline-danger btn-sm rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $news->id }}"><i class="fa-solid fa-trash"></i></button>
                                            <button class="btn btn-outline-dark btn-sm rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#infoModal{{ $news->id }}"><i class="fa-solid fa-eye"></i></button>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
                @if ($companyNews->hasPages())
                    <div class="mt-3">
                        {{ $companyNews->links('vendor.pagination.custom') }}
                    </div>
                @endif
            </div>
        </div>

        @if(count($companyNews) !== 0)

            <!-- Info Modal -->
            @foreach ($companyNews as $news)
                <div class="modal modal-lg fade" id="infoModal{{ $news->id }}" tabindex="-1" aria-labelledby="infoModal{{ $news->id }}Label" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-scrollable">
                        <div class="modal-content rounded-4">
                            <div class="modal-header justify-content-between align-items-center px-4">
                                <h1 class="modal-title fs-5 fw-semibold" id="infoModal{{ $news->id }}Label">{{ __('pages.company_news_details') }} <strong>#CNID-{{ $news->id }}</strong></h1>
                                <button type="button" class="btn btn-darkblue btn-sm" data-bs-dismiss="modal" aria-label="{{ __('app.close') }}"><i class="fa-solid fa-xmark"></i></button>
                            </div>
                            <div class="modal-body bg-custom px-4 py-5">
                                <span class="d-block mb-2"><span class="text-muted"><i class="fa-regular fa-calendar"></i> {{ __('app.date') }} : </span>{{ \Carbon\Carbon::parse($news->date)->format('d/m/Y') }}</span>
                                <span class="d-block mb-2"><span class="text-muted"><i class="fa-solid fa-diagram-project"></i> {{ __('pages.company_news_project') }} : </span>{{ $news->project_name ?? '-' }}</span>
                                <span class="d-block mt-4 mb-3 text-muted"><i class="fa-regular fa-newspaper"></i> {{ __('pages.company_news_info') }}</span>
                                <div class="card card-body rounded-4">
                                    <span>{!! nl2br(e($news->info)) !!}</span>
                                </div>
                                <div class="text-end mt-3">
                                    <small class="text-muted"><i class="fa-solid fa-pen"></i> {{ __('pages.company_news_published') }} {{ \Carbon\Carbon::parse($news->date)->format('d/m/Y') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            <!-- Delete Modal -->
            @foreach ($companyNews as $news)
                <div class="modal fade" id="deleteModal{{ $news->id }}" tabindex="-1" aria-labelledby="deleteModal{{ $news->id }}Label" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-body text-center">
                                <p>{{ __('pages.company_news_confirm_delete') }}</p>
                                <div class="mt-3">
                                    <a href="{{ route('delete.company.news', $news->id) }}" class="btn btn-danger btn-sm fw-semibold"><i class="fa-solid fa-check"></i> {{ __('app.confirm') }}</a>
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
