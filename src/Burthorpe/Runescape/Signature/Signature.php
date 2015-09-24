<?php namespace Burthorpe\Runescape\Signature;

use Burthorpe\Runescape\RS3\API as RS3;
use Intervention\Image\AbstractFont;
use Intervention\Image\ImageManager;

class Signature {

    /**
     * @var \Burthorpe\Runescape\RS3\API
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
        $this->api = new RS3();
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

    protected function drawOverall()
    {

    }

    /**
     * Render the given skill onto the image
     *
     * @param string $skill
     * @param int $x
     * @param int $y
     * @return void
     */
    protected function drawSkill($skill, $x = 0, $y = 0)
    {
        $image = $this->getImage();
        $icon = $this->getSkillIconFile($skill);
        $level = $this->getStats($skill)->get('level');

        $image->insert($icon, 'top-left', $x, ($y - 12)); // -12 to offset icon height
        $image->text($level, ($x + 20), $y, $this->fontCallback()); // +20 to align text to the side of the icon
    }

    /**
     * Get the x-axis coordinate to start drawing from depending on the skill given
     *
     * @param $skillId
     * @return int
     */
    protected function getDrawLocationX($skillId)
    {
        return 10 + (floor(max($skillId - 1, 0) / 6) * 50);
    }

    /**
     * Get the y-axis coordinate to start drawing from depending on the skill given
     *
     * @param $skillId
     * @return int
     */
    protected function getDrawLocationY($skillId)
    {
        return 22 + (22 * (max($skillId - 1, 0) % 6));
    }

    /**
     * Render the image
     *
     * @return void
     */
    protected function draw()
    {
        $this->api->getSkills()->each(function($skill)
        {
            $id = $skill->get('id');

            switch(mb_strtolower($skill->get('name')))
            {
                case 'overall';
                    $this->drawOverall();
                    break;
                default:
                    $this->drawSkill(
                        $skill,
                        $this->getDrawLocationX($id),
                        $this->getDrawLocationY($id)
                    );
            }
        });

        $this->drawWatermark();
    }

    /**
     * Draw the URL watermark on the image
     *
     * @return void
     */
    protected function drawWatermark()
    {
        $this->getImage()->text('burthorpe.com', 240, 145, $this->fontCallback());
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
    protected function getStats($skill = null)
    {
        if ($this->stats)
        {
            $stats = $this->stats;
        }
        else
        {
            $stats = $this->api->stats(
                $this->getUsername()
            );
        }

        return (! is_null($skill) ? $stats->get($skill->get('name')) : $stats);
    }

    /**
     * Return the users level for the given skill
     *
     * @param string $skill
     * @return int
     */
    protected function getLevel($skill)
    {
        return $this->getStats()->get($skill)->get('level');
    }

    /**
     * Get the path to the resources directory
     *
     * @param string $path
     * @return string
     */
    protected function getResourcesPath($path = '')
    {
        return __DIR__ . '/Resources' . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }

    /**
     * Get the path to the font file
     *
     * @return string
     */
    protected function getFontFile()
    {
        return $this->getResourcesPath('Fonts/OpenSans/OpenSans-Bold.ttf');
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
            sprintf('Images/Skills/%s.png', strtolower($skill->get('name')))
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