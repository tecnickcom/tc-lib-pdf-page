<?php
/**
 * Unit.php
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
 * Com\Tecnick\Pdf\Page\Unit
 *
 * @since       2011-05-23
 * @category    Library
 * @package     PdfPage
 * @author      Nicola Asuni <info@tecnick.com>
 * @copyright   2011-2015 Nicola Asuni - Tecnick.com LTD
 * @license     http://www.gnu.org/copyleft/lesser.html GNU-LGPL v3 (see LICENSE.TXT)
 * @link        https://github.com/tecnickcom/tc-lib-pdf-page
 */
abstract class Unit
{
    /**
     * Array of conversion ratios relative to points
     * 72 is the default DPI (Dot Per Inch) document resolution
     *
     * @var array
     */
    public static $unitratio = array(
        ''            => 1,                // default to points
        'px'          => 1,
        'pt'          => 1,
        'points'      => 1,
        'millimeters' => 2.83464566929134, // (72 / 25.4)
        'mm'          => 2.83464566929134, // (72 / 25.4)
        'centimeters' => 28.3464566929134, // (72 / 2.54)
        'cm'          => 28.3464566929134, // (72 / 2.54)
        'inches'      => 72,
        'in'          => 72
    );

    /**
     * Get the unit ratio for the specified unit of measure
     *
     * @param string $unit   Name of the unit of measure
     *
     * @return float
     */
    public function getUnitRatio($unit)
    {
        $unit = strtolower($unit);
        if (!isset(self::$unitratio[$unit])) {
            throw new PageException('unknown unit: '.$unit);
        }
        return self::$unitratio[$unit];
    }

    /**
     * Convert Points to another unit
     *
     * @param float  $points Value to convert
     * @param string $unit   Name of the unit to convert to
     * @param int    $dec    Number of decimals to return
     *
     * @return float
     */
    public function convertPoints($points, $unit, $dec = 6)
    {
        return round(($points / $this->getUnitRatio($unit)), $dec);
    }
}
