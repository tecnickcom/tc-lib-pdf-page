<?php
/**
 * BoxTest.php
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
 * Box Test
 *
 * @since       2011-05-23
 * @category    Library
 * @package     PdfPage
 * @author      Nicola Asuni <info@tecnick.com>
 * @copyright   2011-2015 Nicola Asuni - Tecnick.com LTD
 * @license     http://www.gnu.org/copyleft/lesser.html GNU-LGPL v3 (see LICENSE.TXT)
 * @link        https://github.com/tecnickcom/tc-lib-pdf-page
 */
class BoxTest extends \PHPUnit_Framework_TestCase
{
    protected $obj = null;

    public function setUp()
    {
        //$this->markTestSkipped(); // skip this test
        $this->obj = new \Com\Tecnick\Pdf\Page\Box;
    }
    
    public function testSetCoordinates()
    {
        $dims = $this->obj->setCoordinates(array(), 'CropBox', 2, 4, 6, 8);
        $this->assertEquals(array('CropBox'=>array('llx'=>2, 'lly'=>4, 'urx'=>6, 'ury'=>8)), $dims);

        $this->setExpectedException('\Com\Tecnick\Pdf\Page\Exception');
        $this->obj->setCoordinates(array(), 'ERROR', 1, 2, 3, 4);
    }

    public function testSwapCoordinates()
    {
        $dims = array('CropBox'=>array('llx'=>2, 'lly'=>4, 'urx'=>6, 'ury'=>8));
        $newpagedim = $this->obj->swapCoordinates($dims);
        $this->assertEquals(array('CropBox'=>array('llx'=>4, 'lly'=>2, 'urx'=>8, 'ury'=>6)), $newpagedim);
    }

    public function testSetPageBoxes()
    {
        $dims = $this->obj->setPageBoxes(100, 200);
        $this->assertEquals(
            array(
                'MediaBox' => array('llx' => 0, 'lly' => 0, 'urx' => 100, 'ury' => 200),
                'CropBox'  => array('llx' => 0, 'lly' => 0, 'urx' => 100, 'ury' => 200),
                'BleedBox' => array('llx' => 0, 'lly' => 0, 'urx' => 100, 'ury' => 200),
                'TrimBox'  => array('llx' => 0, 'lly' => 0, 'urx' => 100, 'ury' => 200),
                'ArtBox'   => array('llx' => 0, 'lly' => 0, 'urx' => 100, 'ury' => 200),
            ),
            $dims
        );
    }
}
