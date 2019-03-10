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
        "9" => [true, true, true, true, false, true, true]
    ];


    public function svg() {
        $idSite = Common::getRequestVar('idSite', $this->idSite, 'int');

        $cache = Cache::getLazyCache();
        $cacheKey = "ClassicCounter_Visits_" . $idSite;
        if ($cache->contains($cacheKey)) {
            $visitCount = $cache->fetch($cacheKey);
        } else {
            $visitCount = ClassicCounter::getVisitorCount($idSite);
            $cache->save($cacheKey, $visitCount, 60);
        }

        $view = new View("@ClassicCounter/svg");
        $view->setContentType("image/svg+xml");
        $view->number = $visitCount;
        $chars = str_split($visitCount);
        foreach ($chars as $char) {
            if (empty($this->sevenSegment[$char])) {
                throw new \Exception("character can't be shown on seven segment display");
            }
            $view->digits[] = $this->sevenSegment[$char];
        }
        $view->length = strlen($visitCount);
        return $view->render();
    }
}
