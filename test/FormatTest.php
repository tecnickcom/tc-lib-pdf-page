<?php
/**
 * FormatTest.php
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
 * Format Test
 *
 * @since       2011-05-23
 * @category    Library
 * @package     PdfPage
 * @author      Nicola Asuni <info@tecnick.com>
 * @copyright   2011-2015 Nicola Asuni - Tecnick.com LTD
 * @license     http://www.gnu.org/copyleft/lesser.html GNU-LGPL v3 (see LICENSE.TXT)
 * @link        https://github.com/tecnickcom/tc-lib-pdf-page
 */
class FormatTest extends \PHPUnit_Framework_TestCase
{
    protected $obj = null;

    public function setUp()
    {
        //$this->markTestSkipped(); // skip this test
        $this->obj = new \Com\Tecnick\Pdf\Page\Format;
    }
    
    public function testGetPageSize()
    {
        $dims = $this->obj->getPageSize('A0');
        $this->assertEquals(array(2383.937, 3370.394), $dims);

        $dims = $this->obj->getPageSize('A4', 'in', 2);
        $this->assertEquals(array(8.27, 11.69), $dims);

        $dims = $this->obj->getPageSize('LEGAL', 'mm', 0);
        $this->assertEquals(array(216, 356), $dims);

        $this->setExpectedException('\Com\Tecnick\Pdf\Page\Exception');
        $dims = $this->obj->getPageSize('*ERROR*');
    }
}
