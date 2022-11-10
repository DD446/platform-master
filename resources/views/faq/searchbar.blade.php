<div class="row justify-content-center" id="searchbar">
    <div class="col-12 col-md-10 col-lg-8">
        <form action="{{ route('faq.search') }}" method="get" class="card card-sm" v-if="false">
            <div class="card-body row no-gutters align-items-center">
                <div class="col-auto">
                    <i class="icon-magnifying-glass h4 text-body"></i>
                </div>
                <!--end of col-->
                <div class="col">
                    <input class="form-control form-control-lg form-control-borderless" type="search" name="q"
                           placeholder="{{trans('faq.placeholder_search')}}" required />
                </div>
                <!--end of col-->
                <div class="col-auto">
                    <button class="btn btn-lg btn-success" type="submit">{{trans('faq.button_search')}}</button>
                </div>
                <!--end of col-->
            </div>
        </form>

        <faq-search-bar :placeholder="'{{trans('faq.placeholder_search')}}'"></faq-search-bar>
    </div>
    <!--end of col-->
</div>
<!--end of row-->
