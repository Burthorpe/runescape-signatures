<?php

class SignatureTests extends PHPUnit_Framework_TestCase
{
    public function testGetImage()
    {
        $signature = new \Burthorpe\Runescape\Signature\Signature('iWader');

        $this->assertInstanceOf(\Intervention\Image\Image::class, $signature->getImage());
    }

    public function testPsrHttpResponse()
    {
        $signature = new Burthorpe\Runescape\Signature\Signature('iWader');
        $response = $signature->psrHttpResponse();

        $this->assertInstanceOf(\GuzzleHttp\Psr7\Response::class, $response);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame([
            0 => 'image/png'
        ], $response->getHeader('Content-Type'));
    }
}