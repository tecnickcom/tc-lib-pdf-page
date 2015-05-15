<?php
/**
 * Box.php
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

use \Com\Tecnick\Pdf\Page\Exception as PageException;

/**
 * Com\Tecnick\Pdf\Page\Box
 *
 * @since       2011-05-23
 * @category    Library
 * @package     PdfPage
 * @author      Nicola Asuni <info@tecnick.com>
 * @copyright   2011-2015 Nicola Asuni - Tecnick.com LTD
 * @license     http://www.gnu.org/copyleft/lesser.html GNU-LGPL v3 (see LICENSE.TXT)
 * @link        https://github.com/tecnickcom/tc-lib-pdf-page
 */
class Box
{
    /**
     * Array pf page box names
     *
     * @var array
     */
    public static $box = array(
        'MediaBox',
        'CropBox',
        'BleedBox',
        'TrimBox',
        'ArtBox'
    );

    /**
     * Swap X and Y coordinates of page boxes (change page boxes orientation).
     *
     * @param array $dims Array of page dimensions.
     *
     * @return array Page dimensions.
     *
     */
    public function swapCoordinates(array $dims)
    {
        foreach (self::$box as $type) {
            // swap X and Y coordinates
            if (isset($dims[$type])) {
                $tmp = $dims[$type]['llx'];
                $dims[$type]['llx'] = $dims[$type]['lly'];
                $dims[$type]['lly'] = $tmp;
                $tmp = $dims[$type]['urx'];
                $dims[$type]['urx'] = $dims[$type]['ury'];
                $dims[$type]['ury'] = $tmp;
            }
        }
        return $dims;
    }

    /**
     * Set page boundaries.
     *
     * @param array  $dims    Array of page dimensions to modify
     * @param string $type    Box type: MediaBox, CropBox, BleedBox, TrimBox, ArtBox.
     * @param float  $llx     Lower-left x coordinate in user units.
     * @param float  $lly     Lower-left y coordinate in user units.
     * @param float  $urx     Upper-right x coordinate in user units.
     * @param float  $ury     Upper-right y coordinate in user units.
     *
     * @return array Page dimensions.
     */
    public function setCoordinates($dims, $type, $llx, $lly, $urx, $ury)
    {
        if (empty($dims)) {
            // initialize array
            $dims = array();
        }
        if (!in_array($type, self::$box)) {
            throw new PageException('unknown page box type: '.$type);
        }
        $dims[$type]['llx'] = $llx;
        $dims[$type]['lly'] = $lly;
        $dims[$type]['urx'] = $urx;
        $dims[$type]['ury'] = $ury;
        return $dims;
    }

    /**
     * Initialize page boxes
     *
     * @param float $width  Page width in points
     * @param float $height Page height in points
     *
     * @return array Page boxes
     */
    public function setPageBoxes($width, $height)
    {
        $dims = array();
        foreach (self::$box as $type) {
            $dims = $this->setCoordinates($dims, $type, 0, 0, $width, $height);
        }
        return $dims;
    }
}
