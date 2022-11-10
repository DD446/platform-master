<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd" xmlns:fn="http://www.w3.org/2005/02/xpath-functions" version="2.1">
	<xsl:output method="html" indent="yes" version="5.0"/>
	<xsl:variable name="podcast_img" select=".//itunes:image/@href"/>
	<xsl:template match="/">
		<html>
			<head>
				<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-15"/>
				<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
				<title><xsl:value-of select=".//title"/></title>
				<link rel="stylesheet" type="text/css" href="/assets/css/black.css"/>
			</head>
			<body>
				<div class="main-content">
					<div class="upper-content px-0 pb-lg-2 position-relative podcast">
						<svg class="d-none">
						  	<defs>
						      	<filter id="blurFilter">
						          	<feGaussianBlur stdDeviation="14" result="blurred"/>
						          	<feMorphology in="blurred" operator="dilate" radius="14" result="expanded"/>
									<feMerge>
										<feMergeNode in="expanded"/>
										<feMergeNode in="blurred"/>
									</feMerge>
						      	</filter>
						  	</defs>
						</svg>
						<div class="d-none d-md-block bg-cover position-absolute">
							<div class="d-none bg-cover-black position-absolute"/>
						</div>
						<header class="container-fluid container-lg mx-auto mb-5 mb-md-0 px-0 py-3 py-lg-4 text-center text-lg-left position-relative">
							<a class="logo d-inline-block" href="#"><img class="d-none" src="" data-msrc="{$podcast_img}"/></a>
						</header>
						<div class="d-flex flex-column flex-md-row container mx-auto px-3 px-md-0 pt-5 py-md-5 position-relative bottom-part">
							<div class="col-auto cover align-self-center align-self-md-start px-0 position-relative">
								<img src="{$podcast_img}"/>
								<div class="circle" data-x="0" data-angle="0">
									 <div class="clip1">
			       		 				<div class="slice1"/>
									</div>
								    <div class="clip2">
								        <div class="slice2"/>
								    </div>
								    <div class="inner" style="background-image: url('{$podcast_img}');"/>
								</div>
							</div>
							<div class="description pt-4 pt-lg-5 pl-0 pl-md-5 text-center text-md-left">
								<h1 class="mb-2 mb-lg-0 podcast-title"><xsl:value-of select=".//title"/></h1>
								<p class="subtitle mb-4 mb-lg-3">von <xsl:value-of select=".//itunes:author"/></p>
								<div class="description-content text-left px-3 py-4 p-md-0 ml-n3 ml-md-0">
									<div class="swipe-point d-block d-md-none"/>
									<div class="d-flex d-md-none align-items-center mobile-subtitle pb-4">
										<div class="text align-self-start overflow-hidden"/>
										<a href="#" class="col-auto px-0 ml-auto mr-3 about i-about"/>
										<div class="col-auto px-0 btn-show-list-episodes i-list"/>
									</div>
									<p><xsl:value-of select=".//description"/></p>
								</div>
<!--								<p class="follow-list-title mt-4 mt-md-0 mb-2 text-left">Folge Podcast auf:</p>
								<ul class="follow-list d-flex list-unstyled pb-3 pb-md-0 m-0">
									<li class="mr-2"><a href="#" class="d-block i-si1"/></li>
									<li class="mr-2"><a href="#" class="d-block i-spotify"/></li>
									<li class="mr-2"><a href="#" class="d-block i-rss"/></li>
									<li class="d-block d-md-none ml-auto bg-transparent"><a href="#" class="d-block i-download"/></li>
								</ul>-->
							</div>
						</div>
					</div>
					<div class="middle-content bg-light-grey podcast position-relative">
						<div class="container mx-auto px-0">
							<div class="d-block d-md-none px-3 px-sm-0 pt-4">
								<div class="btn-hide-list-episodes i-left"/>
							</div>
							<h2 class="hv-header px-3 px-sm-0 pt-3 pt-md-5 pb-md-3 py-lg-5 m-0">Episoden</h2>
							<div class="list-episodes">
								<xsl:for-each select=".//item">
									<xsl:choose>
										<xsl:when test="enclosure and starts-with(enclosure/@type, 'audio/') and enclosure/@url">
											<div class="d-inline-flex episode amplitude-song-container justify-content-between justify-content-md-start px-0 pt-4 pb-2 p-md-4 my-md-4 mb-lg-5">
												<div class="d-none d-md-block col-auto cover align-self-start px-0">
                                                    <xsl:variable name="title">
                                                        <xsl:value-of select="title"/>
                                                    </xsl:variable>
													<xsl:choose>
											         	<xsl:when test="itunes:image/@href">
											           		<xsl:variable name="src">
											           			<xsl:value-of select="itunes:image/@href"/>
															</xsl:variable>
											         		<img src="{$src}" alt="Episoden-Cover: {$title}"/>
											         	</xsl:when>
											         	<xsl:otherwise>
                                                            <img src="{$podcast_img}" alt="Podcast-Cover: {$title}"/>
                                                        </xsl:otherwise>
											       	</xsl:choose>
												</div>
												<div class="col-10 col-md-9 d-flex flex-column justify-content-between flex-fill description pl-3 pr-2 px-md-4">
													<h3 class="mb-2 mb-md-3 text-truncate">
                                                        <xsl:value-of select="title"/>
                                                    </h3>
													<div class="d-flex meta mt-0 mt-lg-3">
														<div class="date mr-5">
                                                            <xsl:variable name="raw_date">
                                                                <xsl:value-of select="pubDate"/>
                                                            </xsl:variable>
                                                            <xsl:value-of select="substring( $raw_date, 5, 12 )" />
                                                        </div>
<!--														<a class="about d-flex align-middle text-decoration-none" href="#">
															<div class="i-about d-inline-block mr-2"/>
															Über diese Episode
														</a>-->
													</div>
												</div>
												<div class="d-flex flex-column justify-content-between pr-3 pr-md-0 ml-auto">
													<xsl:variable name="url">
								    					<xsl:value-of select="enclosure/@url"/>
													</xsl:variable>
													<div class="d-flex btn-play amplitude-play-pause amplitude-paused ml-auto" data-amplitude-song-index="0" data-song-url="{$url}"><div class="i-play m-auto"/></div>
													<div class="timing text-right"><xsl:value-of select="itunes:duration"/></div>
												</div>
											</div>
										</xsl:when>
									</xsl:choose>
						        </xsl:for-each>
							</div>
<!--							<div class="text-center py-4 pt-md-0 pb-md-4 pb-lg-5">
								<a class="d-inline-block btn-load-more px-5 py-3 text-decoration-none" href="#">Weitere Episoden</a>
							</div>-->
						</div>
					</div>
					<div class="player-panel d-none">
						<div class="container px-0 mx-auto">
							<div class="d-none d-md-block slider-range-container soundtrack-line position-absolute">
								<div class="placeholder"/>
								<div class="progress-line">
									<div class="timing"><span class="amplitude-current-minutes"/>:<span class="amplitude-current-seconds"/></div>
								</div>
								<input type="range" class="soundtrack-line-slider amplitude-song-slider" value="0"/>
							</div>
							<div class="d-flex justify-content-between py-0 py-md-3">
								<div class="d-none d-md-flex align-items-center">
									<div class="cover m-auto">
										<img data-amplitude-song-info="cover_art_url"/>
									</div>
									<div class="description ml-2">
										<h3 class="overflow-hidden mb-1 episode-title" data-amplitude-song-info="name"/>
										<div class="meta" data-amplitude-song-info="date"/>
									</div>
								</div>
								<div class="col-12 col-md-auto d-flex align-items-center justify-content-around justify-content-md-start px-0 mx-auto ml-md-auto ">
									<div class="pp-btn-prev amplitude-prev mr-md-4"/>
									<div class="pp-btn-prev-15s mr-md-1"/>
									<div class="pp-btn-play amplitude-play-pause amplitude-paused mx-md-4"/>
									<div class="pp-btn-next-15s ml-md-1"/>
									<div class="pp-btn-next amplitude-next ml-md-4"/>
								</div>
								<div class="d-none d-md-flex align-items-center justify-content-end ml-auto mr-5 w-228">
									<div class="i-volume amplitude-mute mr-2"/>
									<div class="slider-range-container sound-volume-container position-relative">
										<div class="placeholder"/>
										<div class="progress-line"/>
										<input type="range" class="sound-volume-slider amplitude-volume-slider" value="0"/>
									</div>
								</div>
<!--								<div class="d-none d-md-flex">
									<a href="#" id="download-button-player" class="i-dwnl m-auto"/>
								</div>-->
							</div>
						</div>
					</div>
				</div>
				<footer class="d-none d-md-block py-2 text-white">
					<div class="container d-flex flex-column-reverse flex-md-row justify-content-between mx-auto px-3 px-sm-0 py-4">
						<div class="text-center">
                            <a class="text-white" href="https://www.podcaster.de">
                                Podcast-Hosting von podcaster.de
                            </a>
                        </div>
						<div class="d-flex justify-content-center mb-3 mb-md-0">
							<a class="text-white" href="https://www.podcaster.de/impressum" title="Impressum des Hosters">Impressum</a>
							<span class="mx-2">|</span>
							<a class="text-white" href="https://www.podcaster.de/privacy" title="Datenschutzerklärung des Hosters">Datenschutz</a>
						</div>
					</div>
				</footer>
				<script type="text/javascript">
					let episodes = [
						<xsl:for-each select=".//item">
                            <xsl:variable name="cover">
                                <xsl:choose>
                                    <xsl:when test="itunes:image/@href">
                                        <xsl:value-of select="itunes:image/@href"/>
                                    </xsl:when>
                                    <xsl:otherwise>
                                        <xsl:value-of select="$podcast_img"/>
                                    </xsl:otherwise>
                                </xsl:choose>
                            </xsl:variable>
							<xsl:choose>
								<xsl:when test="enclosure and enclosure/@type='audio/mpeg' and enclosure/@url">
									{
										"name": "<xsl:value-of select="title"/>",
										"url": "<xsl:value-of select="enclosure/@url"/>",
										"cover_art_url": "<xsl:value-of select="$cover"/>",
                                        "date": "<xsl:value-of select="pubDate"/>"
									},
								</xsl:when>
							</xsl:choose>
				        </xsl:for-each>
					];
					let rss_episodes_ajax_url = 'https://www.podcaster.de/api/feed//shows';
				</script>
				<script type="text/javascript" src="/assets/js/main.js"/>
			</body>
		</html>
	</xsl:template>
</xsl:stylesheet>

