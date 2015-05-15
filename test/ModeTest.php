<?php
/**
 * ModeTest.php
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

namespace Test;

/**
 * Mode Test
 *
 * @since       2011-05-23
 * @category    Library
 * @package     PdfPage
 * @author      Nicola Asuni <info@tecnick.com>
 * @copyright   2011-2015 Nicola Asuni - Tecnick.com LTD
 * @license     http://www.gnu.org/copyleft/lesser.html GNU-LGPL v3 (see LICENSE.TXT)
 * @link        https://github.com/tecnickcom/tc-lib-pdf-page
 */
class ModeTest extends \PHPUnit_Framework_TestCase
{
    public function testGetLayout()
    {
        $obj = new \Com\Tecnick\Pdf\Page\Mode;
        $this->assertEquals('TwoColumnLeft', $obj->getLayout('two'));
        $this->assertEquals('SinglePage', $obj->getLayout(''));
        $this->assertEquals('SinglePage', $obj->getLayout());
    }

    public function testGetDisplay()
    {
        $obj = new \Com\Tecnick\Pdf\Page\Mode;
        $this->assertEquals('UseThumbs', $obj->getDisplay('usethumbs'));
        $this->assertEquals('UseAttachments', $obj->getDisplay(''));
        $this->assertEquals('UseNone', $obj->getDisplay('something'));
    }
}
