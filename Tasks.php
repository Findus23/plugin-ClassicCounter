<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\ClassicCounter;

use Piwik\Cache;
use Piwik\FrontController;
use Piwik\Plugins\ClassicCounter\Controller;
use Piwik\Plugins\SitesManager\API as SitesManagerApi;

class Tasks extends \Piwik\Plugin\Tasks
{
    public function schedule() {
        $this->hourly('updateCache', null, self::LOW_PRIORITY);
    }

    public function updateCache() {
        $cache = Cache::getLazyCache();
        $bla=new Controller();

        $siteIds = SitesManagerApi::getInstance()->getAllSitesId();
        foreach ($siteIds as $idSite) {
            $cacheKey = "ClassicCounter_Visits_" . $idSite;

            $visitCount = ClassicCounter::getVisitorCount($idSite);
            $cache->save($cacheKey, $visitCount, 60);

        }
    }

}
