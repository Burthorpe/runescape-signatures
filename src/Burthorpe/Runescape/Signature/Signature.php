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

        $image->insert($icon, 'top-left', 10, 10);
        $image->text($level, 30, 22, $this->fontCallback());
    }

    /**
     * Render the image
     *
     * @return
     */
    protected function draw()
    {
        foreach($this->api->)
        $this->drawSkill('attack');
        $this->drawSkill('strength');

        return;

        $this->api->calculateCombatLevel(
            $stats->get('attack')->get('level'),
            $stats->get('strength')->get('level'),
            $stats->get('magic')->get('level'),
            $stats->get('ranged')->get('level'),
            $stats->get('defence')->get('level'),
            $stats->get('constitution')->get('level'),
            $stats->get('prayer')->get('level'),
            $stats->get('summoning')->get('level')
        );

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

        $image->insert($this->getSkillIconFile('prayer'), 'top-left', 32, 121);
        $image->text($stats->get('prayer')->get('level'), 10, 134, $this->fontCallback());

        $image->insert($this->getSkillIconFile('magic'), 'top-left', 80, 10);
        $image->text($stats->get('magic')->get('level'), 60, 22, $this->fontCallback());

        $image->insert($this->getSkillIconFile('summoning'), 'top-left', 80, 31);
        $image->text($stats->get('summoning')->get('level'), 60, 44, $this->fontCallback());

        $image->insert($this->getSkillIconFile('cooking'), 'top-left', 80, 53);
        $image->text($stats->get('cooking')->get('level'), 60, 66, $this->fontCallback());

        $image->insert($this->getSkillIconFile('fishing'), 'top-left', 80, 77);
        $image->text($stats->get('fishing')->get('level'), 60, 89, $this->fontCallback());

        $image->insert($this->getSkillIconFile('firemaking'), 'top-left', 80, 100);
        $image->text($stats->get('firemaking')->get('level'), 60, 111, $this->fontCallback());

        $image->insert($this->getSkillIconFile('crafting'), 'top-left', 80, 121);
        $image->text($stats->get('crafting')->get('level'), 60, 134, $this->fontCallback());

        $image->insert($this->getSkillIconFile('woodcutting'), 'top-left', 130, 10);
        $image->text($stats->get('woodcutting')->get('level'), 110, 22, $this->fontCallback());

        $image->insert($this->getSkillIconFile('fletching'), 'top-left', 130, 31);
        $image->text($stats->get('fletching')->get('level'), 110, 44, $this->fontCallback());

        $image->insert($this->getSkillIconFile('smithing'), 'top-left', 130, 53);
        $image->text($stats->get('smithing')->get('level'), 110, 66, $this->fontCallback());

        $image->insert($this->getSkillIconFile('mining'), 'top-left', 130, 77);
        $image->text($stats->get('mining')->get('level'), 110, 89, $this->fontCallback());

        $image->insert($this->getSkillIconFile('herblore'), 'top-left', 130, 100);
        $image->text($stats->get('herblore')->get('level'), 110, 111, $this->fontCallback());

        $image->insert($this->getSkillIconFile('agility'), 'top-left', 130, 121);
        $image->text($stats->get('agility')->get('level'), 110, 134, $this->fontCallback());

        $image->insert($this->getSkillIconFile('thieving'), 'top-left', 180, 10);
        $image->text($stats->get('thieving')->get('level'), 160, 22, $this->fontCallback());

        $image->insert($this->getSkillIconFile('runecrafting'), 'top-left', 180, 31);
        $image->text($stats->get('runecrafting')->get('level'), 160, 44, $this->fontCallback());

        $image->insert($this->getSkillIconFile('slayer'), 'top-left', 180, 53);
        $image->text($stats->get('slayer')->get('level'), 160, 66, $this->fontCallback());

        $image->insert($this->getSkillIconFile('farming'), 'top-left', 180, 77);
        $image->text($stats->get('farming')->get('level'), 160, 89, $this->fontCallback());

        $image->insert($this->getSkillIconFile('construction'), 'top-left', 180, 100);
        $image->text($stats->get('construction')->get('level'), 160, 111, $this->fontCallback());

        $image->insert($this->getSkillIconFile('hunter'), 'top-left', 180, 121);
        $image->text($stats->get('hunter')->get('level'), 160, 134, $this->fontCallback());

        $image->insert($this->getSkillIconFile('dungeoneering'), 'top-left', 230, 10);
        $image->text($stats->get('dungeoneering')->get('level'), $this->negateThreeDigits($stats->get('dungeoneering')->get('level'), 210), 22, $this->fontCallback());

        $image->insert($this->getSkillIconFile('divination'), 'top-left', 230, 31);
        $image->text($stats->get('divination')->get('level'), 210, 44, $this->fontCallback());


    }

    protected function negateThreeDigits($level, $xAxis)
    {
        if (strlen($level) === 3)
        {
            return $xAxis - 8;
        }

        return $xAxis;
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

        return (! is_null($skill) ? $stats->get($skill) : $stats);
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