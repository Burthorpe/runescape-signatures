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

        $image->insert($this->getSkillIconFile('attack'), 'top-left', 30, 10);
        $image->text($stats->get('attack')->get('level'), 10, 22, $this->fontCallback());

        $image->insert($this->getSkillIconFile('strength'), 'top-left', 30, 31);
        $image->text($stats->get('strength')->get('level'), 10, 44, $this->fontCallback());

        $image->insert($this->getSkillIconFile('defence'), 'top-left', 30, 53);
        $image->text($stats->get('defence')->get('level'), 10, 66, $this->fontCallback());

        $image->insert($this->getSkillIconFile('constitution'), 'top-left', 30, 77);
        $image->text($stats->get('constitution')->get('level'), 10, 89, $this->fontCallback());

        $image->insert($this->getSkillIconFile('ranged'), 'top-left', 30, 100);
        $image->text($stats->get('ranged')->get('level'), 10, 111, $this->fontCallback());

        $image->insert($this->getSkillIconFile('prayer'), 'top-left', 30, 121);
        $image->text($stats->get('prayer')->get('level'), 10, 134, $this->fontCallback());
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

            $font->size(
                $this->getFontSize()
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
            150,
            '888888'
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
     * Get the path to the resources directory
     *
     * @param string $path
     * @return string
     */
    protected function getResourcesPath($path = '')
    {
        return __DIR__ . '/Resources/' . $path;
    }

    /**
     * Get the path to the font file
     *
     * @return string
     */
    protected function getFontFile()
    {
        return $this->getResourcesPath('/Fonts/OpenSans/OpenSans-Bold.ttf');
    }

    /**
     * Get the path to the given skill icon
     *
     * @param string $skill
     * @return string
     */
    protected function getSkillIconFile($skill)
    {
        return $this->getResourcesPath(
            sprintf('/Images/Skills/%s.png', strtolower($skill))
        );
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

    /**
     * Get the font size to be rendered
     *
     * @return int
     */
    protected function getFontSize()
    {
        return 14;
    }

}