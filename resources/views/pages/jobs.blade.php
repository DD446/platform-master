@extends('main')

@section('content')

    <section>
        <div class="container">
            <div class="row justify-content-center text-center section-intro">
                <div class="col-12 col-md-9 col-lg-8">
                    <span class="title-decorative aos-init aos-animate" data-aos="fade-up" data-aos-delay="10">{{trans('pages.subheader_jobs')}}</span>
                    <h2 class="display-4 aos-init aos-animate" data-aos="fade-up" data-aos-delay="100">{{trans('pages.header_jobs')}}</h2>
                    <span class="lead aos-init aos-animate" data-aos="fade-up" data-aos-delay="200">{{trans('pages.description_jobs')}}</span>
                </div>
                <!--end of col-->
            </div>
            <!--end of row-->

            <div class="row justify-content-center">
                <div class="col-12 col-md-10">

                    @collection('jobs')

                    <h4 class="mb-4">{{ $entry['department'] }}</h4>
                    <div class="card mb-5">
                        <ul class="list-group list-group-lg list-group-flush">

                            <li class="list-group-item d-flex justify-content-between">
                                <a href="{{ url('/jobs/' . $entry['slug']) }}">{{ $entry['title'] }}</a>
                                <span>{{ $entry['contract_type'] }}</span>
                            </li>

                        </ul>
                    </div>
                    @endcollection

                </div>
                <!--end of col-->
            </div>
            <!--end of row-->
            <div class="row justify-content-center text-center section-outro">
                <div class="col-lg-4 col-md-5">
                    <h6>Didn't see your job?</h6>
                    <p>We're always on the hunt for talented designers and developers to join our team</p>
                    <a href="{{ route('contactus.create') }}">Get in touch ›</a>
                </div>
                <!--end of col-->
            </div>
            <!--end of row-->
        </div>
        <!--end of container-->
    </section>

    <section>
        <div class="container">
            <div class="row text-center section-intro">
                <div class="col">
                    <h2 class="h1">Benefits &amp; Incentives</h2>
                </div>
                <!--end of col-->
            </div>
            <!--end of row-->
            <ul class="row feature-list justify-content-center">
                <li class="col-12 col-md-6 col-lg-5">
                    <i class="icon-star h1 text-teal"></i>
                    <h5>Inclusive Environment</h5>
                    <p>
                        A self-contained unit of a discourse in writing dealing with a particular point or idea. A paragraph consists of one or more sentences.
                    </p>
                    <a href="#">Read our diversity policy</a>
                </li>
                <li class="col-12 col-md-6 col-lg-5">
                    <i class="icon-cloud h1 text-purple"></i>
                    <h5>Remote Opportunities</h5>
                    <p>
                        A paragraph, from the Greek paragraphos is a self-contained unit of a discourse in writing dealing with a particular point or idea.
                    </p>
                </li>
                <li class="col-12 col-md-6 col-lg-5">
                    <i class="icon-wallet h1 text-orange"></i>
                    <h5>Competetive Salary</h5>
                    <p>
                        A paragraph, from the Greek paragraphos is a self-contained unit of a discourse in writing dealing with a particular point or idea.
                    </p>
                </li>
                <li class="col-12 col-md-6 col-lg-5">
                    <i class="icon-heart h1 text-red"></i>
                    <h5>Healthcare Benefits</h5>
                    <p>
                        A self-contained unit of a discourse in writing dealing with a particular point or idea. A paragraph consists of one or more sentences.
                    </p>
                    <a href="#">Read our healthcare policy</a>
                </li>
            </ul>
        </div>
        <!--end of container-->

    </section>
@endsection
