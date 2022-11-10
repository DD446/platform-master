<?php
/**
 * User: fabio
 * Date: 13.11.18
 * Time: 22:32
 */

namespace App\Classes;


class Datacenter
{
    public static function getItunesCategoriesAsJson()
    {
        return file_get_contents(resource_path('js/itunes_categories.de.json'));
    }

    public static function getItunesCategoriesAsObjectList()
    {
        return json_decode(self::getItunesCategoriesAsJson());
    }

    public static function getItunesCategoriesAsArray()
    {
        return json_decode(self::getItunesCategoriesAsJson(), true);
    }

    public static function getItunesCategories()
    {
        $collection = collect(self::getItunesCategoriesAsArray());

        return $collection->mapWithKeys(function ($item) {
            return [$item['value'] => $item['text']];
        })->toArray();

/*        return [
            'Arts'                         => 'Kunst',
            'Arts:Design'                  => 'Kunst: Design',
            'Arts:Fashion & Beauty'    => 'Kunst: Mode und Schönheit',
            'Arts:Food'                    => 'Kunst: Essen',
            'Arts:Literature'              => 'Kunst: Literatur',
            'Arts:Performing Arts'         => 'Kunst: Darstellende Kunst',
            'Arts:Visual Arts'             => 'Kunst: Visuelle Kunst',

            'Business' => 'Wirtschaft',
            'Business:Business News' => 'Wirtschaft: Wirtschaftsnachrichten',
            'Business:Careers' => 'Wirtschaft: Karriere',
            'Business:Investing' => 'Wirtschaft: Investitionen',
            'Business:Management & Marketing' => 'Wirtschaft: Management und Marketing',
            'Business:Shopping' => 'Wirtschaft: Einkaufen',

            'Comedy' => 'Comedy',

            'Education' => 'Bildung',
            'Education:Education Technology' => 'Bildung: Bildungstechnologien',
            'Education:Higher Education' => 'Bildung: Höhere Bildung',
            'Education:K-12' => 'Bildung: Mittelschule',
            'Education:Language Courses' => 'Bildung: Sprachkurse',
            'Education:Training' => 'Bildung: Training',

            'Games & Hobbies' => 'Bildung: Hobbys',
            'Games & Hobbies:Automotive' => 'Bildung: Hobbys: Fahrzeuge',
            'Games & Hobbies:Aviation' => 'Bildung: Hobbys: Luftfahrt',
            'Games & Hobbies:Hobbies' => 'Bildung: Hobbys: Hobbies',
            'Games & Hobbies:Other Games' => 'Bildung: Hobbys: Andere Spiele',
            'Games & Hobbies:Video Games' => 'Bildung: Hobbys: Videospiele',

            'Government & Organizations' => 'Regierung und Organisationen',
            'Government & Organizations:Local' => 'Regierung und Organisationen: Lokal',
            'Government & Organizations:National' => 'Regierung und Organisationen: National',
            'Government & Organizations:Non-Profit' => 'Regierung und Organisationen: Gemeinnützig',
            'Government & Organizations:Regional' => 'Regierung und Organisationen: Regional',

            'Health' => 'Gesundheit',
            'Health:Alternative Health' => 'Gesundheit: Alternative Medizin',
            'Health:Fitness & Nutrition' => 'Gesundheit: Fitness und Ernährung',
            'Health:Self-Help' => 'Gesundheit: Selbsthilfe',
            'Health:Sexuality' => 'Gesundheit: Sexualität',

            'Kids & Family' => 'Kinder und Familie',
            'Music' => 'Musik',
            'News & Politics' => 'Nachrichten und Politik',

            'Religion & Spirituality' => 'Religion und Spiritualität',
            'Religion & Spirituality:Buddhism' => 'Religion und Spiritualität: Buddismus',
            'Religion & Spirituality:Christianity' => 'Religion und Spiritualität: Christentum',
            'Religion & Spirituality:Hinduism' => 'Religion und Spiritualität: Hinduismus',
            'Religion & Spirituality:Islam' => 'Religion und Spiritualität: Islam',
            'Religion & Spirituality:Judaism' => 'Religion und Spiritualität: Judentum',
            'Religion & Spirituality:Other' => 'Religion und Spiritualität: Andere',
            'Religion & Spirituality:Spirituality' => 'Religion und Spiritualität: Spiritualität',

            'Science & Medicine' => 'Wissenschaft und Medizin',
            'Science & Medicine:Medicine' => 'Wissenschaft und Medizin: Medizin',
            'Science & Medicine:Natural Sciences' => 'Wissenschaft und Medizin: Naturwissenschaften',
            'Science & Medicine:Social Sciences' => 'Wissenschaft und Medizin: Sozialwissenschaften',

            'Society & Culture' => 'Gesellschaft und Kultur',
            'Society & Culture:History' => 'Gesellschaft und Kultur: Geschichte',
            'Society & Culture:Personal Journals' => 'Gesellschaft und Kultur: Tagebücher',
            'Society & Culture:Philosophy' => 'Gesellschaft und Kultur: Philosophie',
            'Society & Culture:Places & Travel' => 'Gesellschaft und Kultur: Orte und Reisen',

            'Sports & Recreation' => 'Sport und Erholung',
            'Sports & Recreation:Amateur' => 'Sport und Erholung: Amateure',
            'Sports & Recreation:College & High School' => 'Sport und Erholung: Universität und Schule',
            'Sports & Recreation:Outdoor' => 'Sport und Erholung: Draußen',
            'Sports & Recreation:Professional' => 'Sport und Erholung: Profis',

            'Technology' => 'Technologie',
            'Technology:Gadgets' => 'Technologie: Gadgets',
            'Technology:Tech News' => 'Technologie: Nachrichten',
            'Technology:Podcasting' => 'Technologie: Podcasting',
            'Technology:Software How-To' => 'Technologie: Software How-To',

            'TV & Film' => 'TV und Film'
        ];*/
    }

    public static function getGooglePlayCategoriesAsJson()
    {
        return file_get_contents(resource_path('js/googleplay_categories.de.json'));
    }

    public static function getGooglePlayCategoriesAsObjectList()
    {
        return json_decode(self::getGooglePlayCategoriesAsJson());
    }

    public static function getGooglePlayCategoriesAsArray()
    {
        return json_decode(self::getGooglePlayCategoriesAsJson(), true);
    }

    public static function getGooglePlayCategories()
    {
        $collection = collect(self::getGooglePlayCategoriesAsArray());

        return $collection->mapWithKeys(function ($item) {
            return [$item['value'] => $item['text']];
        })->toArray();

/*        return [
            'Arts' => 'Kunst',
            'Business' => 'Wirtschaft',
            'Comedy' => 'Comedy',
            'Education' => 'Bildung',
            'Games &amp; Hobbies' => 'Spiele und Hobbys',
            'Government &amp; Organizations' => 'Regierung und Organisationen',
            'Health' => 'Gesundheit',
            'Kids &amp; Family' => 'Kinder und Familie',
            'Music' => 'Musik',
            'News &amp; Politics' => 'Nachrichten und Politik',
            'Religion &amp; Spirituality' => 'Religion und Spiritualität',
            'Science &amp; Medicine' => 'Wissenschaft und Medizin',
            'Society &amp; Culture' => 'Gesellschaft und Kultur',
            'Sports &amp; Recreation' => 'Sport und Erholung',
            'TV &amp; Film' => 'Fernsehen und Film',
            'Technology' => 'Technologie',
        ];*/
    }
}
