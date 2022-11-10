<?php
/**
 * User: fabio
 * Date: 02.04.20
 * Time: 11:09
 */

namespace App\Classes;


use Illuminate\Support\Str;
use Laminas\Feed\Reader\Feed\AbstractFeed;

class FeedReader
{
    /**
     * @param  \Laminas\Feed\Reader\Feed\AbstractFeed  $feed
     * @return string
     */
    public function getName(\Laminas\Feed\Reader\Feed\AbstractFeed $feed): string
    {
        $name = substr($feed->getXpath()->evaluate('string(' . $feed->getXpathPrefix() . '/itunes:owner/itunes:name)'), 0, 100);

        if ($name) {
            return $name;
        }

        $name = $feed->getCastAuthor();

        if ($name) {
            return $name;
        }

        $name = $this->getNameFromAuthor($feed->getAuthor());

        if ($name) {
            return $name;
        }

        $name = $this->getNameFromAuthor($feed->getAuthor(1));

        return $name;
    }

    private function getNameFromAuthor($author): string
    {
        if (is_array($author) && array_key_exists('name', $author)) {
            $name = Str::between($author['name'], '(', ')');

            if ($name) {
                return $name;
            }
        }

        if (is_string($author) && strpos($author, '@') === false) {
            $name = Str::between($author, '(', ')');

            if ($name) {
                return $name;
            }
        }

        return '';
    }

    /**
     * @param  \Laminas\Feed\Reader\Feed\AbstractFeed  $feed
     * @return string
     */
    public function getEmail(\Laminas\Feed\Reader\Feed\AbstractFeed $feed): string
    {
        $email = substr($feed->getXpath()->evaluate('string(' . $feed->getXpathPrefix() . '/itunes:owner/itunes:email)'), 0, 100);

        if ($email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $email;
        }

        $author = $feed->getAuthor();

        if (is_array($author) && array_key_exists('name', $author)) {
            $email = trim(Str::before($author['name'], '('));

            if ($email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return $email;
            }
        }

        if (is_string($author) && strpos($author, '@') != false) {
            $email = trim(Str::before($author. '('));

            if ($email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return $email;
            }
        }

        return '';
    }

    /**
     * @param  string  $url
     * @return AbstractFeed
     */
    public static function getCachedFeed(string $url): AbstractFeed
    {
        $cache = \Laminas\Cache\StorageFactory::adapterFactory('Redis',
            [
                'server' => [
                    'host' => config('database.redis.cache.host'),
                    'port' => config('database.redis.cache.port')
                ],
                'database' => config('database.redis.cache.database'),
            ]);
        \Laminas\Feed\Reader\Reader::setCache($cache);
        \Laminas\Feed\Reader\Reader::useHttpConditionalGet();
        \Laminas\Feed\Reader\Reader::setHttpClient(
            new \Laminas\Http\Client(
                null,
                ['useragent' => 'podcaster.de FeedImporter/2.0 (+https://www.podcaster.de)']
            )
        );
        $feed = \Laminas\Feed\Reader\Reader::import($url);

        return $feed;
    }

    public static function cleanExplicit($status)
    {
        $status = Str::lower($status);

        switch($status) {
            case 'yes':
            case 'true':
            case true:
                $status = 'yes';
                //$status = 'true'; # Should be
                break;
            case 'clean':
            case 'false':
            case false:
            default:
                $status = 'clean';
            //$status = 'false'; # TODO: Should be
        }

        return $status;
    }

    public static function cleanBlock($status)
    {
        $status = Str::lower($status);

        switch($status) {
            case 'yes':
                return 'yes';
            default:
                return 'no';
        }
    }

    public static function cleanEpisodeType($type)
    {
        $type = Str::lower($type);

        switch($type) {
            case 'full':
            case 'trailer':
            case 'bonus':
                break;
            default:
                $type = 'full';
        }

        return $type;
    }
}
