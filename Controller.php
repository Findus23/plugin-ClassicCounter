<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\ClassicCounter;

use Piwik\Cache;
use Piwik\Common;
use Piwik\View;


class Controller extends \Piwik\Plugin\Controller
{
    private $sevenSegment = [
        "0" => [true, true, true, true, true, true, false],
        "1" => [false, true, true, false, false, false, false],
        "2" => [true, true, false, true, true, false, true],
        "3" => [true, true, true, true, false, false, true],
        "4" => [false, true, true, false, false, true, true],
        "5" => [true, false, true, true, false, true, true],
        "6" => [true, false, true, true, true, true, true],
        "7" => [true, true, true, false, false, false, false],
        "8" => [true, true, true, true, true, true, true],
        "9" => [true, true, true, true, false, true, true],
        "E" => [true, false, false, true, true, true, true],
        "r" => [false, false, false, false, true, false, true],
        "o" => [false, false, true, true, true, false, true],
    ];

    private $modes = ["nb_visits", "nb_actions", "nb_visits_converted", "bounce_count", "sum_visit_length", "max_actions", "bounce_rate", "nb_actions_per_visit", "avg_time_on_site"];

    private $colorRegex = '/^(?:[0-9a-fA-F]{3}){1,2}$/m';

    public function svg() {
        $request = \Piwik\Request::fromRequest();
        $idSite = $request->getIntegerParameter('idSite', $this->idSite);
        $mode = $request->getStringParameter('mode', "nb_visits");
        $colors = [
            "backgroundColor" => $request->getStringParameter('backgroundColor', "000"),
            "foregroundColor" => $request->getStringParameter('foregroundColor', "f00"),
            "lightColor" => $request->getStringParameter('lightColor', "222"),
        ];
        $historicValue = $request->getIntegerParameter('historicValue', 0);
        try {
            if (!in_array($mode, $this->modes)) {
                $modestring = implode(", ", $this->modes);
                throw new \Exception("mode can only be one of $modestring");
            }
            foreach ($colors as $name => $color) {
                if (!preg_match($this->colorRegex, $color)) {
                    $colors = [
                        "backgroundColor" => "000",
                        "foregroundColor" => "f00",
                        "lightColor" => "222",
                    ];
                    throw new \Exception("$name has to be a valid hex color (without the #)");
                }
            }
            $cache = Cache::getLazyCache();
            $cacheKey = "ClassicCounter_Data_" . $idSite;
            if ($cache->contains($cacheKey)) {
                $visitData = $cache->fetch($cacheKey);
            } else {
                $visitData = ClassicCounter::getVisitorData($idSite);
                $cache->save($cacheKey, $visitData, 60);
            }
            $text = $visitData[$mode];
            $text = (int)$text + $historicValue;
            $errorMessage = false;
        } catch (\Exception $e) {
            $text = "Error";
            $errorMessage = $e->getMessage();
        }

        $view = new View("@ClassicCounter/svg");
        $view->setContentType("image/svg+xml");
        $view->number = $text;
        $view->errorMessage = $errorMessage;
        $view->colors = $colors;
        $chars = str_split($text);
        foreach ($chars as $char) {
            if (empty($this->sevenSegment[$char])) {
                throw new \Exception("character '$char' can't be shown on seven segment display");
            }
            $view->digits[] = $this->sevenSegment[$char];
        }
        $view->length = strlen($text);
        return $view->render();
    }
}
