<?php
/**
 * Region.php
 *
 * @since       2011-05-23
 * @category    Library
 * @package     PdfPage
 * @author      Nicola Asuni <info@tecnick.com>
 * @copyright   2011-2015 Nicola Asuni - Tecnick.com LTD
 * @license     http://www.gnu.org/copyleft/lesser.html GNU-LGPL v3 (see LICENSE.TXT)
 * @link        https://github.com/tecnickcom/tc-lib-pdf-page
 *
 * This file is part of tc-lib-pdf-page software library.
 */

namespace Com\Tecnick\Pdf\Page;

use \Com\Tecnick\Color\Pdf as Color;
use \Com\Tecnick\Pdf\Page\Exception as PageException;

/**
 * Com\Tecnick\Pdf\Page\Region
 *
 * @since       2011-05-23
 * @category    Library
 * @package     PdfPage
 * @author      Nicola Asuni <info@tecnick.com>
 * @copyright   2011-2015 Nicola Asuni - Tecnick.com LTD
 * @license     http://www.gnu.org/copyleft/lesser.html GNU-LGPL v3 (see LICENSE.TXT)
 * @link        https://github.com/tecnickcom/tc-lib-pdf-page
 */
abstract class Region extends \Com\Tecnick\Pdf\Page\Settings
{
    /**
     * Select the specified page region.
     *
     * @return array Selected region data
     */
    public function selectRegion($idr)
    {
        $this->page[$this->pageid]['currentRegion'] = min(max(0, intval($idr)), $this->page[$this->pageid]['columns']);
        return $this->getCurrentRegion();
    }

    /**
     * Returns the current region data
     *
     * @return array
     */
    public function getCurrentRegion()
    {
        return $this->page[$this->pageid]['region'][$this->page[$this->pageid]['currentRegion']];
    }

    /**
     * Returns the page data with the next selected region.
     * If there are no more regions available in the current page, then a new page is added.
     *
     * @return array Current page data
     */
    public function getNextRegion()
    {
        $nextid = ($this->page[$this->pageid]['currentRegion'] + 1);
        if (isset($this->page[$this->pageid]['region'][$nextid])) {
            $this->page[$this->pageid]['currentRegion'] = $nextid;
            return $this->page[$this->pageid];
        }
        return $this->add();
    }

    /**
     * Check if the specified position is outside the region.
     *
     * @param float  $posy Position
     * @param string $min  ID of the min region value to check
     * @param string $max  ID of the max region value to check
     *
     * @return boolean
     */
    private function isOutRegion($pos, $min, $max)
    {
        $eps = 0.0001;
        $region = $this->getCurrentRegion();
        if (($pos < ($region[$min] - $eps)) || ($pos > ($region[$max] + $eps))) {
            return true;
        }
        return false;
    }

    /**
     * Check if the specified vertical position is outside the region.
     *
     * @param float $posy Y position
     *
     * @return boolean
     */
    public function isYOutRegion($posy)
    {
        return $this->isOutRegion($posy, 'Y', 'T');
    }

    /**
     * Check if the specified horizontal position is outside the region
     *
     * @param float $posx X position
     *
     * @return boolean
     */
    public function isXOutRegion($posx)
    {
        return $this->isOutRegion($posx, 'X', 'L');
    }
}
