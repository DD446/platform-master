<?php
/**
 * User: fabio
 * Date: 29.08.20
 * Time: 10:15
 */

namespace Tests\Classes;

use App\Classes\NginxServerConfigWriter;
use App\Exceptions\ServerConfigWriteException;
use Tests\TestCase;

class NginxServerConfigWriterTest extends TestCase
{
    const SERVER_NAME = 'www.example.org';

    public function setUp(): void
    {
        register_shutdown_function(function() {
            if(file_exists('temp.file')) {
                unlink('temp.file');
            }
        });
    }

    public function testConstructorWithWrongHostname()
    {
        $this->expectException(ServerConfigWriteException::class);
        new NginxServerConfigWriter('example', '.');
    }

    public function testSetAndGet()
    {
        $nsw = new NginxServerConfigWriter('example', self::SERVER_NAME);

        $this->assertEquals(self::SERVER_NAME, $nsw->server_name);
    }

    public function testSetConfDir()
    {
        $nsw = new NginxServerConfigWriter('example', self::SERVER_NAME);
        $confDir = basename(__FILE__);
        $nsw->setConfDir($confDir);

        $this->assertEquals($confDir, $nsw->getConfDir());
    }

    public function testGetWebsiteType()
    {
        $nsw = new NginxServerConfigWriter('example', self::SERVER_NAME);
        $websiteType = 'wordpress';
        $nsw->setWebsiteType($websiteType);

        $this->assertEquals($websiteType, $nsw->getWebsiteType());
    }

    public function testCreate()
    {
        $nsw = new NginxServerConfigWriter('example', self::SERVER_NAME);
        $nsw->setBaseDir('/tmp');
        $nsw->setConfDir('');
        $filename = '/tmp/' . self::SERVER_NAME . '.conf';
        $result = $nsw->create();

        $this->assertIsInt($result);
        $this->assertEquals(true, $result);
        $this->assertFileExists($filename);
    }

    public function testExists()
    {
        $nsw = new NginxServerConfigWriter('example', self::SERVER_NAME);
        $nsw->setBaseDir('/tmp');
        $nsw->setConfDir('');
        $filename = '/tmp/' . self::SERVER_NAME . '.conf';
        $result = $nsw->create();

        $this->assertIsInt($result);
        $this->assertEquals(true, $result);
        $this->assertFileExists($filename);

        $result = $nsw->exists();
        $this->assertEquals(true, $result);
    }

    public function testDelete()
    {
        $nsw = new NginxServerConfigWriter('example', self::SERVER_NAME);
        $nsw->setBaseDir('/tmp');
        $nsw->setConfDir('');
        $filename = '/tmp/' . self::SERVER_NAME . '.conf';
        $result = $nsw->create();

        $this->assertIsInt($result);
        $this->assertEquals(true, $result);
        $this->assertFileExists($filename);

        $result = $nsw->delete();

        $this->assertEquals(true, $result);
        $this->assertFileDoesNotExist($filename);
    }

    public function testProtection()
    {
        $nsw = new NginxServerConfigWriter('example', self::SERVER_NAME);
        $nsw->setBaseDir('/tmp');
        $nsw->setConfDir('');
        $nsw->setUsesProtection(true);
        $nsw->create();
        $filename = '/tmp/' . self::SERVER_NAME . '.conf';
        $this->assertStringContainsString('auth_basic_user_file', file_get_contents($filename));
    }

    // TODO: Test
}
