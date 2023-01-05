<?php
/**
 * User: fabio
 * Date: 04.07.19
 * Time: 11:07
 */

namespace Audiotakes\PioniereCheckManagement\Http\Controllers;

use App\Classes\Activity;
use App\Models\AudiotakesContract;
use App\Models\Feed;
use App\Models\User;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use function React\Promise\all;

class CheckController extends Controller
{
    use ValidatesRequests;

    public function index()
    {
        $contracts = AudiotakesContract::with('user')
            ->whereNotNull('feed_id')
            ->orderByDesc('id')
            ->paginate();
        $data = [
            'users' => [],
            'currentPage' => $contracts->currentPage(),
            'hasMorePages' => $contracts->hasMorePages(),
            'perPage' => $contracts->perPage(),
            'total' => $contracts->total(),
        ];
        $users = [];

        foreach ($contracts as $contract) {
            if (!$contract->user) {
                continue;
            }
            $feed = Feed::select(['rss.title', 'domain', 'settings', 'logo.itunes', 'submit_links'])
                ->whereUsername($contract->user->username)
                ->whereFeedId($contract->feed_id)
                ->where('settings.audiotakes_id', '=', $contract->identifier)
                ->first();
            if ($feed) {
                try {
                    $file = false;
                    if(isset($feed->logo) && isset($feed->logo['itunes'])) {
                        $file = get_file($contract->user->username, $feed->logo['itunes']);
                    }
                } catch (\Exception $e) {
                }
                $users[] = [
                    'contract' => $contract,
                    'isActive' => $feed->settings['audiotakes'],
                    'feedTitle' => $feed->rss['title'],
                    'feedLink' => get_feed_uri($contract->feed_id, $feed->domain),
                    'feedImage' => $file ? get_image_uri($contract->feed_id, $file['name'], $feed->domain) : null,
                    'podcastLink' => $feed->submit_links['podcast'] ?? 'https://www.podcast.de/suche?q=' . $feed->rss['title'],
                ];
            }
        }

        $users = User::all();
        $data['users'] = $users;

        return response()->json($data);
    }

    public function listing()
    {
        $headers = [
            "Content-type" => "application/ms-excel",
            "Content-Disposition" => "attachment; filename=audiotakes_publisher_listing_table.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];
        $columns = ['Collection ID', 'Podcast Title', 'Feed URL'];
        $data = [];
        $contracts = AudiotakesContract::with('user')
            ->whereNotNull('feed_id')
            ->orderByDesc('id')
            ->get();

        foreach ($contracts as $contract) {
            $feed = Feed::select(['rss.title', 'domain'])
                ->whereUsername($contract->user->username)
                ->whereFeedId($contract->feed_id)
                ->where('settings.audiotakes_id', '=', $contract->identifier)
                ->first();

            if ($feed) {
                $_data = [
                    'identifier' => $contract->identifier,
                    'title' => $feed->rss['title'],
                    'url' => get_feed_uri($contract->feed_id, $feed->domain),
                ];
                $data[] = $_data;
            }
        }

        $callback = function() use ($data, $columns)
        {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach($data as $item) {
                fputcsv($file, [$item['identifier'], $item['title'], $item['url']]);

            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers); // this is the solution
    }

    public function matching()
    {
        $headers = [
            "Content-type" => "application/ms-excel",
            "Content-Disposition" => "attachment; filename=audiotakes_publisher_id_matching_table.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];
        $columns = ['Collection ID', 'Category'];
        $data = [];
        $contracts = AudiotakesContract::with('user')
            ->whereNotNull('feed_id')
            ->orderByDesc('id')
            ->get();

        foreach ($contracts as $contract) {
            $feed = Feed::select(['rss.title', 'itunes.category'])
                ->whereUsername($contract->user->username)
                ->whereFeedId($contract->feed_id)
                ->where('settings.audiotakes_id', '=', $contract->identifier)
                ->first();

            if ($feed) {
                $_data = [];

                foreach($feed->itunes['category'] as $cat) {

                    if ($cat != 'Society &amp; Culture:Places &amp; Travel') {
                        $cat = Str::before($cat, ':');
                    }

                    if (!in_array($cat, $_data) && $cat) {
                        $_data[] = $cat;
                    }
                }

                $categoryMatches = $this->getCategoryMatches();

                foreach ($_data as $cat) {
                    if (array_key_exists($cat, $categoryMatches)) {
                        $_cat = $categoryMatches[$cat];
                        $data[] = [
                            'identifier' => $contract->identifier,
                            'cat' => $_cat,
                        ];
                    }
                }
            }
        }

        $callback = function() use ($data, $columns)
        {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach($data as $item) {
                fputcsv($file, [$item['identifier'], $item['cat']]);

            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers); // this is the solution
    }

    public function rms()
    {
        $headers = [
            "Content-type" => "application/ms-excel",
            "Content-Disposition" => "attachment; filename=audiotakes_publisher_id_rms_matching_table.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];
        $columns = ['Collection ID', 'Title', 'Category'];
        $data = [];
        $contracts = AudiotakesContract::with('user')
            ->whereNotNull('feed_id')
            ->orderByDesc('id')
            ->get();

        foreach ($contracts as $contract) {
            $feed = Feed::select(['rss.title', 'itunes.category'])
                ->whereUsername($contract->user->username)
                ->whereFeedId($contract->feed_id)
                ->where('settings.audiotakes_id', '=', $contract->identifier)
                ->first();

            if ($feed) {
                $_data = [];

                foreach($feed->itunes['category'] as $cat) {

                    if ($cat != 'Society &amp; Culture:Places &amp; Travel') {
                        $cat = Str::before($cat, ':');
                    }

                    if (!in_array($cat, $_data) && $cat) {
                        $_data[] = $cat;
                    }
                }

                $categoryMatches = $this->getCategoryMatches();

                foreach ($_data as $cat) {
                    if (array_key_exists($cat, $categoryMatches)) {
                        $_cat = $categoryMatches[$cat];
                        $data[] = [
                            'identifier' => $contract->identifier,
                            'title' => $feed->rss['title'],
                            'cat' => $_cat,
                        ];
                        continue 2;
                    }
                }
            }
        }

        $callback = function() use ($data, $columns)
        {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach($data as $item) {
                fputcsv($file, [$item['identifier'], $item['title'], $item['cat']]);

            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers); // this is the solution
    }

    public function spotify()
    {
        $headers = [
            "Content-type" => "application/ms-excel",
            "Content-Disposition" => "attachment; filename=audiotakes_publisher_id_spotify_matching_table.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];
        $columns = ['Name', 'URL', 'Spotify-Uri', 'Nur MP3s', 'Anzahl MP3s', 'Anzahl Andere', 'MP3 Überzahl', 'Keine Sendungen'];
        $data = [];
        $contracts = AudiotakesContract::with('user')
            ->whereNotNull('feed_id')
            ->orderByDesc('id')
            ->get();

        foreach ($contracts as $contract) {
            if (!$contract->user) {
                continue;
            }
            $feed = Feed::whereUsername($contract->user->username)
                ->whereFeedId($contract->feed_id)
                ->where('settings.audiotakes_id', '=', $contract->identifier)
                ->first();

            if ($feed) {
                $isPure = true;
                $countMp3 = 0;
                $countOther = 0;

                if ($feed->entries) {
                    foreach($feed->entries as $entry) {
                        try {
                            if ($entry['show_media']) {
                                $file = get_file($feed->username, $entry['show_media']);
                                if ($file) {
                                    if (Str::lower($file['extension']) != 'mp3') {
                                        $isPure = false;
                                        $countOther++;
                                    } else {
                                        $countMp3++;
                                    }
                                }
                            }
                        } catch (\Exception $e) {
                            Log::error($e->getMessage());
                        }
                    }
                    if (!$countMp3) {
                        $isPure = false;
                    }
                }

                $data[] = [
                    'name' => $feed->rss['title'],
                    'url' => get_feed_uri($feed->feed_id, $feed->domain),
                    'spotify_uri' => isset($feed->settings['spotify_uri']) && $feed->settings['spotify_uri'] ? 'https://open.spotify.com/show/' . Str::after($feed->settings['spotify_uri'], 'spotify:show:') : null,
                    'mp3_only' => $isPure ? 'Ja' : 'Nein',
                    'count_mp3' => $countMp3,
                    'count_other' => $countOther,
                    'mp3_majority' => $countMp3 > $countOther ? 'Ja' : 'Nein',
                    'no_shows' => ($countMp3 + $countOther) < 1 ? 'Ja' : 'Nein',
                ];
            }
        }

        $callback = function() use ($data, $columns)
        {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach($data as $item) {
                fputcsv($file, [$item['name'], $item['url'], $item['spotify_uri'], $item['mp3_only'], $item['count_mp3'], $item['count_other'], $item['mp3_majority'], $item['no_shows']]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers); // this is the solution
    }

    public function spotifyShort()
    {
        $headers = [
            "Content-type" => "application/ms-excel",
            "Content-Disposition" => "attachment; filename=audiotakes_spotify_table_" . date('Ymd') . ".csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];
        $columns = ['Name', 'Spotify-Uri'];
        $data = [];
        $contracts = AudiotakesContract::with('user')
            ->whereNotNull('feed_id')
            ->orderByDesc('id')
            ->get();

        foreach ($contracts as $contract) {
            if (!$contract->user) {
                continue;
            }
            $feed = Feed::whereUsername($contract->user->username)
                ->whereFeedId($contract->feed_id)
                ->where('settings.audiotakes_id', '=', $contract->identifier)
                ->first();

            if ($feed) {
                $countMp3 = 0;
                $countOther = 0;

                if ($feed->entries) {
                    foreach($feed->entries as $entry) {
                        try {
                            if ($entry['show_media']) {
                                $file = get_file($feed->username, $entry['show_media']);
                                if (Str::lower($file['extension']) != 'mp3') {
                                    $countOther++;
                                } else {
                                    $countMp3++;
                                }
                            }
                        } catch (\Exception $e) {
                            Log::error($e->getMessage());
                        }
                    }
                }

                if (isset($feed->settings['spotify_uri'])
                    && $feed->settings['spotify_uri']
                    && ($countMp3 > 0)) {
                    $data[] = [
                        'name' => $feed->rss['title'],
                        'spotify_uri' =>  'https://open.spotify.com/show/' . Str::after($feed->settings['spotify_uri'], 'spotify:show:'),
                    ];
                }
            }
        }

        $callback = function() use ($data, $columns)
        {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach($data as $item) {
                fputcsv($file, [$item['name'], $item['spotify_uri']]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers); // this is the solution
    }

    private function getCategoryMatches()
    {
        return [
            'Kids &amp; Family' => 'Eltern und Kind',
            'Games &amp; Hobby' => 'Gaming',
            'Technology' => 'Gaming',
            'Society &amp; Culture:Places &amp; Travel' => 'Reisen',
            'Society &amp; Culture' => 'Gesellschaft und Kultur',
            'Politics' => 'Gesellschaft und Kultur',
            'Government' => 'Gesellschaft und Kultur',
            'History' => 'Gesellschaft und Kultur',
            'Religion &amp; Spirituality' => 'Gesellschaft und Kultur',
            'Arts' => 'Gesellschaft und Kultur',
            'Science' => 'Gesellschaft und Kultur',
            'Business' => 'Gesellschaft und Kultur',
            'Health &amp; Fitness' => 'Health',
            'Education' => 'Health',
            'Leisure' => 'Lifestyle',
            'Music' => 'Musik',
            'News' => 'News',
            'Sports' => 'Sport',
            'True Crime' => 'TrueCrime',
            'TV &amp; Film' => 'TV und Film',
        ];
/*
    Eltern und Kind -> iTunes Kids & Family
    Gaming -> (google Games & Hobby) + iTunes Technologie
    Gesellschaft und Kultur -> iTunes Society & Culture + itunes Politcs + iTunes Gouverment+ iTues History + iTunes Releigion + Itunes Arts + iTunes Sience + iTuines Fiction + iTunes Business
    Health -> iTunes Health & Fitness + iTunes Education
    Lifestyle -> iTunes Leisure
    Musik -> iTunes Musik
    News -> iTunes News
    Reisen -> iTunes UNterkategorie ->
    Sport -> iTiunes Sports
    Talk-> Adserver Talk
    TrueCrime -> iTunes True Crime
    TV und Film -> ITunes TV und Film
 */
    }
}
