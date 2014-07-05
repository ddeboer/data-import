<?php

namespace Ddeboer\DataImport\Tests\Writer;

class AbstractStreamWriterTest extends StreamWriterTest
{
    protected function setUp()
    {
        $this->writer = $this->getMockForAbstractClass('Ddeboer\\DataImport\\Writer\\AbstractStreamWriter');
    }

    public function testItImplementsWriterInterface()
    {
        $this->assertInstanceOf('Ddeboer\\DataImport\\Writer\\WriterInterface', $this->writer);
    }

    public function testItThrowsInvalidArgumentExceptionOnInvalidStream()
    {
        $invalidStreams = array(0, 1, null, 'stream', new \stdClass());
        foreach ($invalidStreams as $invalidStream) {
            try {
                $this->writer->setStream($invalidStream);
                $this->fail('Above call should throw exception');
            } catch (\InvalidArgumentException $exception) {
                $this->assertContains('Expects argument to be a stream resource', $exception->getMessage());
            }
        }
    }

    public function testGetStreamReturnsAStreamResource()
    {
        $this->assertTrue('resource' == gettype($stream = $this->writer->getStream()), 'getStream should return a resource');
        $this->assertEquals('stream', get_resource_type($stream));
    }

    public function testSetStream()
    {
        $this->assertSame($this->writer, $this->writer->setStream($this->getStream()));
        $this->assertSame($this->getStream(), $this->writer->getStream());
    }

    public function testEol()
    {
        $this->assertSame("\n", $this->writer->getEol());
        $this->assertSame($this->writer, $this->writer->setEol("\r\n"));
        $this->assertSame("\r\n", $this->writer->getEol());
    }

    public function testCloseOnFinishIsInhibitable()
    {
        $this->assertTrue($this->writer->closeStreamOnFinish());
        $this->assertFalse($this->writer->closeStreamOnFinish(false));
        $this->assertFalse($this->writer->closeStreamOnFinish());
        $this->assertTrue($this->writer->closeStreamOnFinish(true));
        $this->assertTrue($this->writer->closeStreamOnFinish());
    }

    public function testFinishCloseStreamAccordingToCloseOnFinishState()
    {
        $stream = $this->getStream();
        $this->writer->setStream($stream);
        $this->writer->prepare();

        $this->writer->closeStreamOnFinish(false);
        $this->writer->finish();
        $this->assertTrue(is_resource($stream));

        $this->writer->closeStreamOnFinish(true);
        $this->writer->finish();
        $this->assertFalse(is_resource($stream));
    }
}
