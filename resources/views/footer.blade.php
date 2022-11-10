<footer class="bg-gray text-light footer-long">
    <div class="container">
        <div class="row">
            <div class="col-12 col-lg-3 col-md-12 col-sm-12 d-none d-md-block">
                <img alt="podcaster" src="{{ asset('images1/podcaster_logo_weiss.svg') }}" class="mb-4 img-fluid" style="width:85%;max-width:350px;height:auto" />
                <p class="text-muted">
                    <?php
                    $fromYear = 2007;
                    $thisYear = (int)date('Y');
                    echo $fromYear . (($fromYear != $thisYear) ? '-' . $thisYear : '');
                    ?>
                     podcaster
                    <br />Podcast Hosting aus Deutschland
                </p>
            </div>
            <!--end of col-->
            <div class="col-12 col-lg-9 col-md-12 col-sm-12">
                <span class="h5">{{ trans('main.footer_slogan')  }}</span>
                <div class="row no-gutters">
                    <div class="col-6 col-lg-3 col-md-3 col-sm-6">
                        <h6>{{ trans('main.footer_header_company')  }}</h6>
                        <ul class="list-unstyled">
                            <li><a href="{{ route('page.team') }}">{{ trans('main.footer_link_team') }}</a></li>
                            <li><a href="{{ route('page.imprint') }}">{{ trans('main.footer_link_imprint') }}</a></li>
                            <li><a href="{{ route('page.press') }}">{{ trans('main.footer_link_press') }}</a></li>
                            <li><a href="https://www.podcast.de/jobs" target="_blank">{{ trans('main.footer_link_jobs') }}</a></li>
                            <li><a href="{{ route('lp.sitemap') }}">{{ trans('main.footer_link_sitemap') }}</a></li>
                            <li>
                                <a href="{{ route('page.privacy') }}">Datenschutz</a>
                            </li>
                        </ul>
                    </div>
                    <!--end of col-->
                    <div class="col-6 col-lg-3 col-md-3 col-sm-6">
                        <h6>Support</h6>
                        <ul class="list-unstyled">
                            <li>
                                <a href="{{ route('lp.help') }}">{{ trans('nav.help') }}</a>
                            </li>
                            <li>
                                <a href="{{ route('faq.index') }}">{{ trans('nav.faq') }}</a>
                            </li>
                            <li>
                                <a href="{{ route('lp.videos') }}" title="Videos zum Dienst" rel="nofollow noopener">Videos</a>
                            </li>
                            <li>
                                <a href="https://www.facebook.com/groups/169168417223173" target="_blank" title="Facebook Gruppe für Kunden" rel="nofollow noopener noreferrer">FB Kundengruppe</a>
                            </li>
                            <li>
                                <a href="https://podster.de/c/podcast-hosting/6?mtm_campaign=nav&mtm_source=podcaster&mtm_medium=website&mtm_content=link&mtm_placement=footer" target="_blank" title="Diskussionforum zum Dienst auf podster">Forum</a>
                            </li>
                            <li>
                                <a href="{{ route('contactus.create') }}">{{ trans('nav.contactus') }}</a>
                            </li>
                        </ul>
                    </div>
                    <!--end of col-->
                    <div class="col-6 col-lg-3 col-md-3 col-sm-6 pt-md-0 pt-3">
                        <h6>Dienst</h6>
                        <ul class="list-unstyled">
                            <li>
                                <a href="{{ route('login') }}">Anmeldung</a>
                            </li>
                            <li>
                                <a href="{{ route('lp.tour') }}">Tour</a>
                            </li>
                            <li>
                                <a href="{{ route('packages') }}">Pakete & Preise</a>
                            </li>
                            <li><a href="{{ route('lp.reviews') }}">{{ trans('main.footer_link_reviews') }}</a></li>
                            <li>
                                <a href="{{ route('news.index') }}">News</a>
                            </li>
                            <li>
                                <a href="{{ route('page.terms') }}">Nutzungsbedingungen</a>
                            </li>
                        </ul>
                    </div>
                    <!--end of col-->
                    <div class="col-6 col-lg-3 col-md-3 col-sm-6 pt-md-0 pt-3">
                        <h6>Produkte</h6>
                        <ul class="list-unstyled">
                            <li>
                                <a href="https://www.podcast.de/podcast-beratung?mtm_campaign=nav&mtm_source=podcaster&mtm_medium=website&mtm_content=link&mtm_placement=footer" target="_blank">Podcast-Beratung</a>
                            </li>
                            <li>
                                <a href="https://www.podcast.de/podcast-jobs?mtm_campaign=nav&mtm_source=podcaster&mtm_medium=website&mtm_content=link&mtm_placement=footer" target="_blank">Podcast-Jobs</a>
                            </li>
                            <li>
                                <a href="https://www.podcast.de/podcast-push?mtm_campaign=nav&mtm_source=podcaster&mtm_medium=website&mtm_content=link&mtm_placement=footer" target="_blank">Podcast-Push</a>
                            </li>
                            <li>
                                <a href="https://podspace.de?mtm_campaign=nav&mtm_source=podcaster&mtm_medium=website&mtm_content=link&mtm_placement=footer" target="_blank">Podcast-Studio</a>
                            </li>
                            <li>
                                <a href="https://www.podcast.de/podcast-werbung?mtm_campaign=nav&mtm_source=podcaster&mtm_medium=website&mtm_content=link&mtm_placement=footer" target="_blank">Podcast-Werbung</a>
                            </li>
                        </ul>
                    </div>
                    <!--end of col-->
                </div>
                <!--end of row-->
            </div>
            <!--end of col-->
        </div>
        <!--end of row-->
        <div class="row pt-4">
            <div class="col-12 justify-content-center text-center">
                <a href="https://twitter.com/podcasterDE" rel="nofollow noopener noreferrer" target="_blank" title="podcaster@Twitter" class="m-2"><i class="socicon-twitter"></i></a>
                <a href="https://www.facebook.com/podcast.hosting" rel="nofollow noopener noreferrer" target="_blank" title="podcaster@Facebook" class="m-2"><i class="socicon-facebook"></i></a>
                <a href="https://www.youtube.com/channel/UC45hgxNKdFWbIrsDojsfchQ" rel="nofollow noopener noreferrer" target="_blank" title="podcaster@YouTube" class="m-2"><i class="socicon-youtube"></i></a>
                <a href="https://www.linkedin.com/company/podcasthosting/" rel="nofollow noopener noreferrer" target="_blank" title="podcaster@LinkedIn" class="m-2"><i class="socicon-linkedin"></i></a>
                <a href="https://instagram.com/podcastplattform" rel="nofollow noopener noreferrer" target="_blank" title="PodcastPlattform@Instagram" class="m-2"><i class="socicon-instagram"></i></a>
                <a href="https://www.pinterest.de/PodcastPlattform/" rel="nofollow noopener noreferrer" target="_blank" title="PodcastPlattform@Pinterest" class="m-2"><i class="socicon-pinterest"></i></a>
                <a href="https://t.me/podcastplattform" rel="nofollow noopener noreferrer" target="_blank" title="PodcastPlattform@Telegram" class="m-2"><i class="socicon-telegram"></i></a>
            </div>
        </div>
        <div class="row pt-4">
            <div class="col-12 justify-content-center text-center">
                <a href="https://www.podcastplattform.de?mtm_campaign=nav&mtm_source=podcaster&mtm_medium=website&mtm_content=logo&mtm_placement=footer" target="_blank">
                    <img src="{{ asset('images1/teil-der-podcast-plattform-logo.png') }}" alt="Podcast Plattform" style="width: 167px;height: 167px">
                </a>
            </div>
        </div>
    </div>
    <!--end of container-->
</footer>

