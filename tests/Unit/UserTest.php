<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use App\Models\Package;
use App\Models\Space;
use App\Models\User;
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

    protected function setUp(): void
    {
        parent::setUp();

        // seed the database
        $this->seed('PackageSeeder');

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

    /**
     *
     *
     * @return void
     */
    public function testNewUsernameNotEmpty(): void
    {
        $this->assertNotEmpty(User::getNewUsername());
    }

    public function testNewUsernameIsString()
    {
        $this->assertIsString(User::getNewUsername());
    }

    public function testNewUsernameLength(): void
    {
        $this->assertEquals(6, strlen(User::getNewUsername()));
    }

    public function testUsernameIsAvailable(): void
    {
        $this->assertIsBool(User::isUsernameAvailable('abcdefg'));
        $this->assertIsBool(User::isUsernameAvailable('admin'));
    }

    public function testUsernameIsUnavailable(): void
    {
        // This works because default length is 6 and there should be no username like this
        $this->assertEquals(true, User::isUsernameAvailable('abcdefg'));
        $this->assertEquals(false, User::isUsernameAvailable('admin'));
        $this->assertEquals(false, User::isUsernameAvailable('login'));
    }

    public function testFundsChanges(): void
    {
        $user = User::create(['username' => Str::random(6)]);
        $user->increment('funds', 5);
        $this->assertEquals('5', $user->funds);
        $user->decrement('funds', 15);
        $this->assertEquals('-10', $user->funds);
        $user->increment('funds', 10);
        $this->assertEquals(0, $user->funds);
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
