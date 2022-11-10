<div class="row">
    <div class="col text-center">
        <video controls>
            <source src="https://tutorials.podcaster.de/download/podcaster_coach_01.mp4" type="video/mp4">
        </video>
    </div>
    <!--end of col-->
</div>
<!--end of row-->

<div class="row justify-content-center pt-3 pb-2">
    <div class="col-12 col-lg-6 border shadow p-5" id="app" data-type="order">
        <h4>Jetzt podcaster.de kostenlos und unverbindlich für 30 Tage testen</h4>
        <div class="text-center" v-if="false">
            <div class="spinner-border m-5" style="width: 3rem; height: 3rem;" role="status">
                <span class="sr-only">...</span>
            </div>
        </div>
        <order
            terms-url="{{ route('page.terms') }}"
            privacy="{{ route('page.privacy') }}"
            id="3"></order>
    </div>
</div>
