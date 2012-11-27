<?php

namespace GetId3\Tests\Modules;

use GetId3\GetId3Core;

class AudioVideoTest extends \PHPUnit_Framework_TestCase
{
    protected static $quicktimeFile;
    protected static $class;

    protected function setUp()
    {
        self::$quicktimeFile = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Fixtures' . DIRECTORY_SEPARATOR . 'sample_iTunes.mov';
        self::$class = 'GetId3\\GetId3Core';
    }

    public function testClass()
    {
        if (!class_exists(self::$class)) {
            $this->markTestSkipped(self::$class . ' is not available.');
        }
        $this->assertTrue(class_exists(self::$class));
        $this->assertClassHasAttribute('option_md5_data', self::$class);
        $this->assertClassHasAttribute('option_md5_data_source', self::$class);
        $this->assertClassHasAttribute('encoding', self::$class);
        $rc = new \ReflectionClass(self::$class);
        $this->assertTrue($rc->hasMethod('analyze'));
        $rm = new \ReflectionMethod(self::$class, 'analyze');
        $this->assertTrue($rm->isPublic());
    }

    public function testQuicktimeFile()
    {
        $this->assertFileExists(self::$quicktimeFile);
        $this->assertTrue(is_readable(self::$quicktimeFile));
    }

    /**
     * @depends testClass
     * @depends testQuicktimeFile
     */
    public function testReadQuicktime()
    {
        $getId3 = new GetId3Core();
        $getId3->option_md5_data        = true;
        $getId3->option_md5_data_source = true;
        $getId3->encoding               = 'UTF-8';

        $properties = $getId3->analyze(self::$quicktimeFile);

        $this->assertArrayNotHasKey('error', $properties);
        $this->assertArrayHasKey('mime_type', $properties);
        $this->assertEquals('video/quicktime', $properties['mime_type']);
        $this->assertArrayHasKey('encoding', $properties);
        $this->assertEquals('UTF-8', $properties['encoding']);
        $this->assertArrayHasKey('filesize', $properties);
        $this->assertSame(3284257, $properties['filesize']);
        $this->assertArrayHasKey('fileformat', $properties);
        $this->assertEquals('quicktime', $properties['fileformat']);
        $this->assertArrayHasKey('audio', $properties);
        $this->assertArrayHasKey('dataformat', $properties['audio']);
        $this->assertEquals('mp4', $properties['audio']['dataformat']);
        $this->assertArrayHasKey('video', $properties);
        $this->assertArrayHasKey('dataformat', $properties['video']);
        $this->assertEquals('mpeg4', $properties['video']['dataformat']);
    }
}