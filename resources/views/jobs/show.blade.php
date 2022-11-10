@extends('main')

@section('content')
    <section class="bg-white space-sm">
        <div class="container">
            <div class="row">
                <div class="col">
                    <h1>{{ $title }}</h1>
                    <span class="lead">{{ $job_type }}</span>
                    <a href="{{ route('contactus.create') }}" class="btn btn-success">@lang('pages.link_apply_now')</a>
                </div>
                <!--end of col-->
            </div>
            <!--end of row-->
        </div>
        <!--end of container-->
    </section>
    <!--end of section-->



    <section>
        <div class="container">
            <div class="row justify-content-between">
                <div class="col-12 col-md-8 col-lg-7">
                    <article>
                        {!! $content !!}
                    </article>
                    <hr>
                    <h5 class="mb-4">@lang('pages.header_job_question')</h5>
                    <form class="d-flex justify-content-between align-items-center">
                        <div>
                            <a href="{{ route('contactus.create') }}" class="btn btn-success">@lang('pages.link_apply_now')</a>
                        </div>
                        <div>
                            <a href="{{ route('page.jobs') }}">@lang('pages.link_all_jobs')</a>
                        </div>
                    </form>
                </div>
                <!--end of col-->
                <div class="col-12 col-md-4">
                    <div class="card">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between">
                                    <div><i class="icon-tools mr-1"></i> @lang('pages.header_department')</div>
                                    <span>{{ $department }}</span>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between">
                                    <div><i class="icon-home mr-1"></i> @lang('pages.header_location')</div>
                                    <span>{{ $location }}</span>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between">
                                    <div><i class="icon-stopwatch mr-1"></i> @lang('pages.header_contract_type')</div>
                                    <span>{{ $contract_type }}</span>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <span class="h6">@lang('pages.header_tasks')</span>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled list-spacing-sm">
                                @foreach($tasks as $task)
                                    <li>
                                        <i class="icon-text-document text-muted mr-1"></i>
                                        <!--                                    <a href="#">Setting up API end-points</a>-->
                                        {{ $task }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <div>
                                <span class="h6">@lang('pages.header_work_areas')</span>
<!--                                <span class="badge badge-secondary">{{ $work_areas }}</span>-->
                            </div>
<!--                            <a href="#">View all &rsaquo;</a>-->
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled list-spacing-sm">
                                @foreach($work_areas as $area)
                                <li>
                                    <i class="icon-text-document text-muted mr-1"></i>
<!--                                    <a href="#">Setting up API end-points</a>-->
                                    {{ $area }}
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <!--end of card-->
                </div>
                <!--end of col-->
            </div>
            <!--end of row-->
        </div>
        <!--end of container-->
    </section>

@endsection
