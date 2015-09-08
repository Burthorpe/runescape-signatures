<?php namespace Burthorpe\Runescape\Signature;

use Burthorpe\Runescape\EvolutionOfCombat;
use Intervention\Image\AbstractFont;
use Intervention\Image\ImageManager;

class Signature {

    /**
     * @var \Burthorpe\Runescape\EvolutionOfCombat
     */
    protected $api;

    /**
     * @var \Intervention\Image\ImageManager
     */
    protected $imageManager;

    /**
     * @var \Intervention\Image\Image
     */
    protected $image;

    /**
     * @var string
     */
    protected $username;

    /**
     * @var null|array
     */
    protected $stats = null;

    /**
     * @param string $username RuneScape display name
     */
    public function __construct($username)
    {
        $this->api = new EvolutionOfCombat();
        $this->imageManager = new ImageManager([
            'driver' => 'imagick'
        ]);

        $this->username = $username;
    }

    /**
     * Bootstrap the rendering process
     */
    public function run()
    {
        $this->draw();
    }

    /**
     * Serve the image via. HTTP
     *
     * @return mixed
     */
    public function httpResponse()
    {
        return $this->getImage()->response('png', 90);
    }

    /**
     * Return a PSR-7 compatible HTTP response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function psrHttpResponse()
    {
        return $this->getImage()->psrResponse('png', 90);
    }

    /**
     * Render the image
     *
     * @return
     */
    protected function draw()
    {
        $image = $this->getImage();

        $stats = $this->getStats();

        $image->text('Hello World', 10, 20, $this->fontCallback());
    }

    /**
     * Get the callback to be used when rendering the text in the image
     *
     * @return \Closure
     */
    protected function fontCallback()
    {
        return function (AbstractFont $font)
        {
            $font->file(
                $this->getFontFile()
            );

            $font->color(
                $this->getFontColour()
            );
        };
    }

    /**
     * Get the image resource being used
     *
     * @return \Intervention\Image\Image
     */
    protected function getImage()
    {
        if ($this->image) return $this->image;

        return $this->image = $this->imageManager->canvas(
            350,
            150
        );
    }

    /**
     * Get the users display name
     *
     * @return null|string
     */
    protected function getUsername()
    {
        return $this->username;
    }

    /**
     * Get the users skills stats
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getStats()
    {
        if ($this->stats) return $this->stats;

        return $this->stats = $this->api->stats(
            $this->getUsername()
        );
    }

    /**
     * Get the path to the font file
     *
     * @return string
     */
    protected function getFontFile()
    {
        return __DIR__ . '/Fonts/OpenSans/OpenSans-Regular.ttf';
    }

    /**
     * Get the colour to be used for the font
     *
     * @return string
     */
    protected function getFontColour()
    {
        return '111111';
    }

}