<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use App\Classes\Activity;
use App\Models\Package;
use App\Models\Space;
use App\Models\User;
use App\Models\UserAccounting;
use App\Models\UserData;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use DatabaseMigrations;

    const TEST_USERNAME = 'testABC123';
    const TEST_FILENAME = 'test.txt';
    const TEST_FILENAME_SECOND = 'test(1).txt';
    const TEST_FILENAME_COPY = 'test_copy.txt';
    const TEST_FILENAME_RENAME = 'test_rename.txt';
    const TEST_FILENAME_RENAME_NOT_AVAILABLE = 'test(1).txt';
    const TEST_FILEID = 1234567890;
    const TEST_FILEID_SECOND = 1234567891;
    const TEST_GROUP = 'listen';
    const TEST_FEED_ID = 'feed123';
    const TEST_SPACE_AVAILABLE = 1000000;

    protected function setUp(): void
    {
        parent::setUp();

        // seed the database
        $this->seed('PackageSeeder');
        $this->seed('PackageFeatureSeeder');
        $this->seed('PackageFeatureMappingSeeder');

        $storagePath = $this->getStoragePath() . self::TEST_FILEID . '/';

        if (!File::isDirectory($storagePath)) {
            File::makeDirectory($storagePath, 0755, true);
        }

        if (!File::isFile($storagePath . self::TEST_FILENAME)) {
            file_put_contents($storagePath . self::TEST_FILENAME, 'TEST');
        }

        $storagePath = $this->getStoragePath() . self::TEST_FILEID_SECOND . '/';

        if (!File::isDirectory($storagePath)) {
            File::makeDirectory($storagePath, 0755, true);
        }

        if (!File::isFile($storagePath . self::TEST_FILENAME_SECOND)) {
            file_put_contents($storagePath . self::TEST_FILENAME_SECOND, 'TEST 1');
        }
    }

    /**
     * @return void
     */
    protected function tearDown(): void
    {
        /** @var User $user */
        $user = User::whereUsername(self::TEST_USERNAME)->first();

        if ($user) {
            File::deleteDirectory($this->getStoragePath());
            $user->forceDelete();
        }

        $config = app('config');
        parent::tearDown();
        app()->instance('config', $config);
    }

    public function testCreateUser(): void
    {
        $this->withoutNotifications();

        User::whereUsername(self::TEST_USERNAME)->forceDelete();

        $user = new User();

        $this->assertIsObject($user);
        $this->assertInstanceOf(User::class, $user);

        $user->username = self::TEST_USERNAME;
        $user->passwd = md5('testtest');
        $user->package_id = 1;
        $user->first_name = 'Test';
        $user->last_name = 'Last';
        $user->funds = 0;

        $ret = $user->save();
        $this->assertEquals(true, $ret);

        $ua = UserAccounting::create([
            'usr_id' => $user->user_id,
            'activity_type' => Activity::PACKAGE,
            'activity_characteristic' => 3,
            'activity_description' => 'Buchung Paket Profi',
            'amount' => -10.0,
            'currency' => 'EUR',
            'date_created' => Carbon::now(),
            'date_start' => Carbon::now(),
            'date_end' => Carbon::now()->addDays(30),
            'procedure' => 2,
            'status' => 1,
        ]);

        $this->assertIsObject($ua);
        $this->assertInstanceOf(UserAccounting::class, $ua);

        $space = Space::where('user_accounting_id', '=', $ua->accounting_id)->firstOrFail();

        $this->assertEquals(true, $ret);

        $this->assertIsObject($space);
        $this->assertInstanceOf(Space::class, $space);

        $this->be($user);

        $userPathExpected = '/t/e/s/testabc123';
        $userPath = $user->getUserPath();
        $this->assertEquals($userPathExpected, $userPath);

        $aFile = $user->getFile(1234567890);
        $this->assertIsArray($aFile);
        $this->assertArrayHasKey('id', $aFile);
        $this->assertArrayHasKey('name', $aFile);
        $this->assertArrayHasKey('path', $aFile);
        $this->assertEquals(self::TEST_FILENAME, $aFile['name']);
        $this->assertDirectoryExists(File::dirname($aFile['path']));
        $this->assertNull($aFile['cat']);

        $expectedStoragePath = $this->getStoragePath();
        $storagePath = $user->getStoragePath();
        $this->assertEquals($expectedStoragePath, $storagePath);

        $files = $user->getFiles();
        $this->assertEquals(2, $files['count']);
        $this->assertArrayHasKey('items', $files);

        $firstCopy = $user->getUniqueFilename(self::TEST_FILENAME);
        // This is correct because file test(1).txt actually exists through setUp method
        $this->assertEquals('test(2).txt', $firstCopy);

        $secondCopy = $user->getUniqueFilename(self::TEST_FILENAME_SECOND);
        $this->assertEquals('test(2).txt', $secondCopy);

        $file = $user->getStoragePath() . DIRECTORY_SEPARATOR . self::TEST_FILEID . DIRECTORY_SEPARATOR . self::TEST_FILENAME;
        // Link download dir
        $ret = $user->link($file, UserData::MEDIA_DIRECT_DIR);
        $this->assertEquals(true, $ret);
        $publicPath = $this->getPublicStoragePath();
        $link = $publicPath . UserData::MEDIA_DIRECT_DIR . DIRECTORY_SEPARATOR . self::TEST_FILENAME;
        $this->assertEquals(true, is_link($link));
        $this->assertEquals(true, (readlink($link) == $file));
        // Link fictive feed dir
        $ret = $user->link($file, 'media', self::TEST_FEED_ID);
        $this->assertEquals(true, $ret);
        $link = $publicPath . DIRECTORY_SEPARATOR . self::TEST_FEED_ID . DIRECTORY_SEPARATOR. 'media' . DIRECTORY_SEPARATOR . self::TEST_FILENAME;
        $this->assertEquals(true, is_link($link));
        $this->assertEquals(true, (readlink($link) == $file));

        $aCopiedFile = $user->copyFile(self::TEST_FILEID, self::TEST_FILENAME_COPY);
        $this->assertArrayHasKey('id', $aCopiedFile);
        $this->assertArrayHasKey('name', $aCopiedFile);
        $this->assertArrayHasKey('path', $aCopiedFile);
        $this->assertDirectoryExists(File::dirname($aCopiedFile['path']));
        $this->assertFileExists($aCopiedFile['path']);
        $this->assertEquals(self::TEST_FILENAME_COPY, $aCopiedFile['name']);
        $this->assertNull($aCopiedFile['cat']);
        $ret = $user->deleteFile($aCopiedFile['id']);
        $this->assertEquals(true, $ret);
        $this->assertFileDoesNotExist($this->getStoragePath() . DIRECTORY_SEPARATOR . $aCopiedFile['id'] . DIRECTORY_SEPARATOR . $aCopiedFile['name']);
        $this->assertDirectoryDoesNotExist($this->getStoragePath() . DIRECTORY_SEPARATOR . $aCopiedFile['id']);

        // This should not rename the file because the name is same
        $ret = $user->renameFile(self::TEST_FILEID, self::TEST_FILENAME);
        $this->assertEquals(true, $ret);
        $file = $this->getStoragePath() . DIRECTORY_SEPARATOR . self::TEST_FILEID . DIRECTORY_SEPARATOR . self::TEST_FILENAME;
        $link = $this->getPublicStoragePath() . DIRECTORY_SEPARATOR . UserData::MEDIA_DIRECT_DIR . DIRECTORY_SEPARATOR . self::TEST_FILENAME;
        $this->assertFileExists($link);
        $this->assertEquals(true, (readlink($link) == $file));

        // Give the file a new name and relink
        $ret = $user->renameFile(self::TEST_FILEID, self::TEST_FILENAME_RENAME);
        $this->assertEquals(true, $ret);
        $aFile = $user->getFile(self::TEST_FILEID);
        $this->assertEquals(self::TEST_FILENAME_RENAME, $aFile['name']);
        $renameFile = $this->getStoragePath() . self::TEST_FILEID . DIRECTORY_SEPARATOR . self::TEST_FILENAME_RENAME;
        $this->assertFileExists($renameFile);
        $renameLink = $this->getPublicStoragePath() . DIRECTORY_SEPARATOR . UserData::MEDIA_DIRECT_DIR . DIRECTORY_SEPARATOR . self::TEST_FILENAME_RENAME;
        $this->assertFileExists($renameLink);
        $goal = readlink($renameLink);
        $this->assertEquals(true, ($goal == $renameFile));

        // Move to group
        $ret = $user->renameFile(self::TEST_FILEID, self::TEST_FILENAME_RENAME, self::TEST_GROUP);
        $this->assertEquals(true, $ret);
        $aFile = $user->getFile(self::TEST_FILEID);
        $this->assertEquals(self::TEST_FILENAME_RENAME, $aFile['name']);
        $this->assertEquals(self::TEST_GROUP, $aFile['cat']);
        $renameFile = $this->getStoragePath() . self::TEST_FILEID . DIRECTORY_SEPARATOR . self::TEST_GROUP . DIRECTORY_SEPARATOR . self::TEST_FILENAME_RENAME;
        $this->assertFileExists($renameFile);
        $renameLink = $this->getPublicStoragePath() . DIRECTORY_SEPARATOR . UserData::MEDIA_DIRECT_DIR . DIRECTORY_SEPARATOR . self::TEST_FILENAME_RENAME;
        $this->assertFileExists($renameLink);
        $this->assertEquals(true, (readlink($renameLink) == $renameFile));

        // Remove group
        $ret = $user->renameFile(self::TEST_FILEID, self::TEST_FILENAME_RENAME);
        $this->assertEquals(true, $ret);
        $aFile = $user->getFile(self::TEST_FILEID);
        $this->assertEquals(self::TEST_FILENAME_RENAME, $aFile['name']);
        $renameFile = $this->getStoragePath() . self::TEST_FILEID . DIRECTORY_SEPARATOR . self::TEST_FILENAME_RENAME;
        $this->assertFileExists($renameFile);
        $renameLink = $this->getPublicStoragePath() . DIRECTORY_SEPARATOR . UserData::MEDIA_DIRECT_DIR . DIRECTORY_SEPARATOR . self::TEST_FILENAME_RENAME;
        $this->assertFileExists($renameLink);
        $goal = readlink($renameLink);
        $this->assertEquals(true, ($goal == $renameFile));
        // Test if link for feed is changed as well
        $feedLink = $this->getPublicStoragePath() . DIRECTORY_SEPARATOR . self::TEST_FEED_ID . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR . self::TEST_FILENAME_RENAME;
        $this->assertFileExists($feedLink);
        $this->assertEquals(true, (readlink($feedLink) == $renameFile));

        $ret = $user->deleteFile(self::TEST_FILEID);
        $this->assertEquals(true, $ret);
        $this->assertFileDoesNotExist($this->getStoragePath() . DIRECTORY_SEPARATOR . self::TEST_FILEID . DIRECTORY_SEPARATOR . self::TEST_FILENAME);
        $this->assertFileDoesNotExist($this->getStoragePath() . DIRECTORY_SEPARATOR . self::TEST_FILEID . DIRECTORY_SEPARATOR . self::TEST_FILENAME_RENAME);
        $this->assertDirectoryDoesNotExist($this->getStoragePath() . DIRECTORY_SEPARATOR . self::TEST_FILEID);
        $ret = $user->deleteFile(self::TEST_FILEID_SECOND);
        $this->assertEquals(true, $ret);
        $this->assertFileDoesNotExist($this->getStoragePath() . DIRECTORY_SEPARATOR . self::TEST_FILEID_SECOND . DIRECTORY_SEPARATOR . self::TEST_FILENAME_SECOND);
        $this->assertDirectoryDoesNotExist($this->getStoragePath() . DIRECTORY_SEPARATOR . self::TEST_FILEID_SECOND);

        $ret = User::whereUsername(self::TEST_USERNAME)->forceDelete();
        $this->assertEquals(true, $ret);

        // Get space by user id
        $ret = Space::whereUserId($user->user_id)->first();
        $this->assertIsObject($ret);
    }

/*    public function testRenameNAN()
    {
        $this->expectException(\Exception::class);
        $user->renameFile(self::TEST_FILEID, self::TEST_FILENAME_RENAME_NOT_AVAILABLE);
    }*/

    private function getStoragePath()
    {
        $username = strtolower(self::TEST_USERNAME);
        $storagePath = File::dirname(File::dirname(File::dirname(__FILE__))) . '/storage/hostingstorage/mediafiles/' . $username[0]. '/' . $username[1]. '/' . $username[2]. '/' . $username . '/';

        return $storagePath;
    }

    private function getPublicStoragePath()
    {
        $username = strtolower(self::TEST_USERNAME);
        $storagePath = File::dirname(File::dirname(File::dirname(__FILE__))) . '/storage/publicstorage/' . $username[0]. '/' . $username[1]. '/' . $username[2]. '/' . $username . '/';

        return $storagePath;
    }
}
