<?php

namespace Burthorpe\Runescape\Signature;

use Burthorpe\Runescape\RS3\API as RS3;
use Burthorpe\Runescape\RS3\Skills\Attack;
use Burthorpe\Runescape\RS3\Skills\Constitution;
use Burthorpe\Runescape\RS3\Skills\Defence;
use Burthorpe\Runescape\RS3\Skills\Dungeoneering;
use Burthorpe\Runescape\RS3\Skills\Magic;
use Burthorpe\Runescape\RS3\Skills\Overall;
use Burthorpe\Runescape\RS3\Skills\Prayer;
use Burthorpe\Runescape\RS3\Skills\Ranged;
use Burthorpe\Runescape\RS3\Skills\Skill;
use Burthorpe\Runescape\RS3\Skills\Strength;
use Burthorpe\Runescape\RS3\Skills\Summoning;
use Intervention\Image\AbstractFont;
use Intervention\Image\ImageManager;
use Burthorpe\Runescape\RS3\Skills\Contract as SkillContract;

class Signature
{
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
            'driver' => 'imagick',
        ]);

        $this->username = $username;
    }

    /**
     * Bootstrap the rendering process.
     */
    public function run()
    {
        $this->draw();
    }

    /**
     * Serve the image via. HTTP.
     *
     * @return mixed
     */
    public function httpResponse()
    {
        return $this->getImage()->response('png', 90);
    }

    /**
     * Return a PSR-7 compatible HTTP response.
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function psrHttpResponse()
    {
        return $this->getImage()->psrResponse('png', 90);
    }

    /**
     * Render the given skill onto the image.
     *
     * @param string $skill
     * @param int    $x
     * @param int    $y
     *
     * @return void
     */
    protected function drawSkill(SkillContract $skill, $x = 0, $y = 0)
    {
        $image = $this->getImage();
        $icon = $this->getSkillIconFile($skill);
        $level = $this->getStats($skill)->get('level');

        $image->insert($icon, 'top-left', $x, ($y - 12)); // -12 to offset icon height
        $image->text($level, ($x + 20), $y, $this->fontCallback()); // +20 to align text to the side of the icon
    }

    /**
     * Get the x-axis coordinate to start drawing from depending on the skill given.
     *
     * @param $skillId
     *
     * @return int
     */
    protected function getDrawLocationX(SkillContract $skill)
    {
        return 10 + (floor(max($skill->getId() - 1, 0) / 6) * 50);
    }

    /**
     * Get the y-axis coordinate to start drawing from depending on the skill given.
     *
     * @param $skillId
     *
     * @return int
     */
    protected function getDrawLocationY(SkillContract $skill)
    {
        return 22 + (22 * (max($skill->getId() - 1, 0) % 6));
    }

    /**
     * Render the image.
     *
     * @return void
     */
    protected function draw()
    {
        $this->api->getSkills()->each(function (SkillContract $skill) {
            switch ($skill->getName()) {
                case 'overall';
                    break; // Skip
                default:
                    $this->drawSkill(
                        $skill,
                        $this->getDrawLocationX($skill),
                        $this->getDrawLocationY($skill)
                    );
            }
        });

        $this->drawUserArea(
            $this->getDrawLocationX(new Dungeoneering())
        );

        $this->drawWatermark();
    }

    /**
     * Draw the users name, overall level, rank and combat level.
     *
     * @return void
     */
    protected function drawUserArea($x)
    {
        $this->drawDisplayName($this->username, $x, 70);

        $this->drawCombatLevel(
            $this->api->calculateCombatLevel(
                $this->getStats(new Attack())->get('level'),
                $this->getStats(new Strength())->get('level'),
                $this->getStats(new Magic())->get('level'),
                $this->getStats(new Ranged())->get('level'),
                $this->getStats(new Defence())->get('level'),
                $this->getStats(new Constitution())->get('level'),
                $this->getStats(new Prayer())->get('level'),
                $this->getStats(new Summoning())->get('level')
            ),
            $x,
            85
        );

        $overall = $this->getStats(new Overall());

        $this->drawOverallLevel($overall->get('level'), $x, 100);
        $this->drawOverallRank($overall->get('rank'), $x, 115);
        $this->drawOverallXp($overall->get('xp'), $x, 130);
    }

    /**
     * Draw the users display name.
     *
     * @param $name
     * @param $x
     * @param $y
     *
     * @return void
     */
    protected function drawDisplayName($name, $x, $y)
    {
        $this->getImage()->text(
            $name,
            $x,
            $y,
            $this->fontCallback(null, null, 18)
        );
    }

    /**
     * Draw the users combat level.
     *
     * @param $level
     * @param $x
     * @param $y
     *
     * @return void
     */
    protected function drawCombatLevel($level, $x, $y)
    {
        $this->getImage()->text(
            sprintf('Combat: %s', $level),
            $x,
            $y,
            $this->fontCallback(
                $this->getResourcesPath('Fonts/OpenSans/OpenSans-Regular.ttf')
            )
        );
    }

    /**
     * Draw the users overall level.
     *
     * @param $level
     * @param $x
     * @param $y
     *
     * @return void
     */
    protected function drawOverallLevel($level, $x, $y)
    {
        $level = number_format($level, 0);

        $this->getImage()->text(
            sprintf('Overall: %s', $level),
            $x,
            $y,
            $this->fontCallback(
                $this->getResourcesPath('Fonts/OpenSans/OpenSans-Regular.ttf')
            )
        );
    }

    /**
     * Draw the users overall rank.
     *
     * @param $rank
     * @param $x
     * @param $y
     *
     * @return void
     */
    protected function drawOverallRank($rank, $x, $y)
    {
        $rank = number_format($rank, 0);

        $this->getImage()->text(
            sprintf('Rank: %s', $rank),
            $x,
            $y,
            $this->fontCallback(
                $this->getResourcesPath('Fonts/OpenSans/OpenSans-Regular.ttf')
            )
        );
    }

    /**
     * Draw the users overall experience.
     *
     * @param $xp
     * @param $x
     * @param $y
     *
     * @return void
     */
    protected function drawOverallXp($xp, $x, $y)
    {
        $xp = number_format($xp, 0);

        $this->getImage()->text(
            sprintf('XP: %s', $xp),
            $x,
            $y,
            $this->fontCallback(
                $this->getResourcesPath('Fonts/OpenSans/OpenSans-Regular.ttf')
            )
        );
    }

    /**
     * Draw the URL watermark on the image.
     *
     * @return void
     */
    protected function drawWatermark()
    {
        $this->getImage()->text('burthorpe.com', 250, 145, $this->fontCallback(
            $this->getResourcesPath('Fonts/OpenSans/OpenSans-Light.ttf')
        ));
    }

    /**
     * Get the callback to be used when rendering the text in the image.
     *
     * @return \Closure
     */
    protected function fontCallback($fontFile = null, $colour = null, $size = null)
    {
        return function (AbstractFont $font) use ($fontFile, $colour, $size) {
            $font->file(
                (!is_null($fontFile) ? $fontFile : $this->getFontFile())
            );

            $font->color(
                (!is_null($colour) ? $colour : $this->getFontColour())
            );

            $font->size(
                (!is_null($size) ? $size : $this->getFontSize())
            );
        };
    }

    /**
     * Get the image resource being used.
     *
     * @return \Intervention\Image\Image
     */
    public function getImage()
    {
        if ($this->image) {
            return $this->image;
        }

        return $this->image = $this->imageManager->canvas(
            350,
            150,
            '888888'
        );
    }

    /**
     * Get the users display name.
     *
     * @return null|string
     */
    protected function getUsername()
    {
        return $this->username;
    }

    /**
     * Get the users skills stats.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getStats(SkillContract $skill = null)
    {
        if ($this->stats) {
            $stats = $this->stats;
        } else {
            $stats = $this->api->stats(
                $this->getUsername()
            );
        }

        return (! is_null($skill) ? $stats->get($skill->getName()) : $stats);
    }

    /**
     * Return the users level for the given skill.
     *
     * @param string $skill
     *
     * @return int
     */
    protected function getLevel(SkillContract $skill)
    {
        return $this->getStats()->get($skill->getName())->get('level');
    }

    /**
     * Get the path to the resources directory.
     *
     * @param string $path
     *
     * @return string
     */
    protected function getResourcesPath($path = '')
    {
        return __DIR__.'/Resources'.($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    /**
     * Get the path to the font file.
     *
     * @return string
     */
    protected function getFontFile()
    {
        return $this->getResourcesPath('Fonts/OpenSans/OpenSans-Bold.ttf');
    }

    /**
     * Get the path to the given skill icon.
     *
     * @param string $skill
     *
     * @return string
     */
    protected function getSkillIconFile(SkillContract $skill)
    {
        return $this->getResourcesPath(
            sprintf('Images/Skills/%s.png', strtolower($skill->getName()))
        );
    }

    /**
     * Get the colour to be used for the font.
     *
     * @return string
     */
    protected function getFontColour()
    {
        return '111111';
    }

    /**
     * Get the font size to be rendered.
     *
     * @return int
     */
    protected function getFontSize()
    {
        return 14;
    }
}
