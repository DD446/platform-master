<?php
/**
 * User: fabio
 * Date: 26.08.20
 * Time: 21:25
 */

namespace App\Classes;


use JamesHeinrich\GetID3\GetID3;
use JamesHeinrich\GetID3\WriteTags;

/**
 * Class FileTagManager
 * @package App\Classes
 * @see https://github.com/JamesHeinrich/getID3/blob/2.0/demos/demo.simple.write.php
 */
class FileTagManager
{
    const TEXT_ENCODING_UTF8 = 'UTF-8';

    public const DATA_TYPE_COMMON = 'meta';
    public const DATA_TYPE_CHAPTERS = 'chapters';

    /**
     * @var array
     */
    private array $errors = [];

    /**
     * @var array
     */
    private array $warnings = [];

    /**
     * FileTagManager constructor.
     *
     * @param  array|null  $file
     */
    public function __construct(?array $file)
    {
        $this->file = $file;
    }

    /**
     * @param  array  $data
     * @param  bool  $overwriteTags
     * @param  bool  $removeOtherTags
     * @return bool
     * @throws \Exception
     */
    public function write(array $data, bool $overwriteTags = false, bool $removeOtherTags = false): bool
    {
        // Initialize getID3 tag-writing module
        $tagwriter = new WriteTags;

        $tagwriter->filename = $this->file['path'];

        // ['id3v1', 'id3v2.3'];
        $tagwriter->tagformats = ['id3v2.4'];

        // set various options (optional)
        // if true will erase existing tag data and write only passed data;
        // if false will merge passed data with existing tag data (experimental)
        $tagwriter->overwrite_tags = $overwriteTags;

        // if true removes other tag formats (e.g. ID3v1, ID3v2, APE, Lyrics3, etc)
        // that may be present in the file and only write the specified tag format(s).
        // If false leaves any unspecified tag formats as-is.
        $tagwriter->remove_other_tags = $removeOtherTags;

        $tagwriter->tag_encoding = self::TEXT_ENCODING_UTF8;

        // populate data array
/*        $data = [
            'title'                  => ['My Song'],
            'artist'                 => ['The Artist'],
            'album'                  => ['Greatest Hits'],
            'year'                   => ['2004'],
            'genre'                  => ['Rock'],
            'comment'                => ['excellent!'],
            'track_number'           => ['04/16'],
            'popularimeter'          => ['email'=>'user@example.net', 'rating'=>128, 'data'=>0],
            'unique_file_identifier' => ['ownerid'=>'user@example.net', 'data'=>md5(time())],
        ];*/

        $tagwriter->tag_data = $data;

        // write tags
        if (!$tagwriter->WriteTags()) {
            $this->errors = $tagwriter->errors;
            throw new \Exception();
        }

        $this->warnings = $tagwriter->warnings;

        return true;
    }

    /**
     * @param  string|null  $type
     * @return array
     */
    public function getData(?string $type = null): ?array
    {
        // Initialize getID3 engine
        $getID3 = new GetID3;
        $getID3->setOption(['encoding' => self::TEXT_ENCODING_UTF8]);

        // Analyze file and store returned data in $info
        $info = $getID3->analyze($this->file['path'], $this->file['byte']);

        if ($type == self::DATA_TYPE_CHAPTERS) {
            return $info['id3v2']['chapters'] ?? [];
        } else {
            /** Copies data from all subarrays of [tags] into [comments] so
             * metadata is all available in one location for all tag formats
             * metainformation is always available under [tags] even if this is not called
             */
            $getID3->CopyTagsToComments($info);
        }

        return $info['comments_html'] ?? [];
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getWarnings()
    {
        return $this->warnings;
    }

    public function hasWarnings()
    {
        return count($this->warnings) > 0;
    }
}
