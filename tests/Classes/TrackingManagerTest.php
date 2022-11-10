<?php
/**
 * User: fabio
 * Date: 27.08.20
 * Time: 12:39
 */

namespace Tests\Classes;

use App\Classes\TrackingManager;
use Tests\TestCase;

class TrackingManagerTest extends TestCase
{
    public function testCheckChartable()
    {
        $enclosureLink = 'https://1lzg0dn.podcaster.de/90s-Podcast-90er-Jahre-Nostalgie/media/90sPodcast_Ep35_EssenUndTrinken.m4a';
        $link = 'https://chtbl.com/track/47E472/https://1lzg0dn.podcaster.de/90s-Podcast-90er-Jahre-Nostalgie/media/90sPodcast_Ep35_EssenUndTrinken.m4a';

        $tm = new TrackingManager();
        $response = $tm->checkChartable($link, $enclosureLink);

        $this->assertIsBool($response);
        $this->assertEquals(true, $response);
    }

    public function testCheckRms()
    {
        $link = 'https://rmsi-podcast.de/comedyperiode/media/42_Folge.mp3?awCollectionId=STO0001';

        $tm = new TrackingManager();
        $response = $tm->checkRms($link);

        $this->assertIsBool($response);
        $this->assertEquals(true, $response);
    }

    public function testCheckPodtrac()
    {
        $link = 'https://dts.podtrac.com/redirect.m4a/api.spreaker.com/download/episode/17594717/pitchcast_001_hubcash_a_fintech_que_quer_bater_de_frente_com_o_pagseguro_1.mp3';

        $tm = new TrackingManager();
        $response = $tm->checkPodtrac($link);

        $this->assertIsBool($response);
        $this->assertEquals(true, $response);
    }

    public function testCheckAudiotakes()
    {
        $link = 'https://deliver.audiotakes.net/d/hga9ha.podcaster.de/p/BerlinCityWalks/m/004_berlin_City_Walks_Wedding_mixdown.mp3?awCollectionId=at-imqou';
        $enclosureLink = 'https://hga9ha.podcaster.de/BerlinCityWalks/media/004_berlin_City_Walks_Wedding_mixdown.mp3';

        $tm = new TrackingManager();
        $response = $tm->checkAudiotakes($link, $enclosureLink);

        $this->assertIsBool($response);
        $this->assertEquals(true, $response);
    }
}
