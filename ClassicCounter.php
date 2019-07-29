<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\ClassicCounter;

use Piwik\Access;

class ClassicCounter extends \Piwik\Plugin
{
    public static function getVisitorData($idSite) {
        $visitsCount = Access::getInstance()->doAsSuperUser(function () use ($idSite) {
            return \Piwik\API\Request::processRequest('VisitsSummary.get', array(
                'idSite' => $idSite,
                'period' => "range",
                'date' => "2000-01-01,2030-01-01",
            ))->getFirstRow()->export()[0];
        });
        return $visitsCount;
    }

}
