<?php
/**
 * User: fabio
 * Date: 09.03.18
 * Time: 10:30
 */

namespace App\Classes;


class HashManager extends \Illuminate\Hashing\HashManager
{
    public function createMD5Driver()
    {
        return new MD5Hasher;
    }
}
