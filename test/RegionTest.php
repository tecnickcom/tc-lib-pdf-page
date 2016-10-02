<?php
/**
 * RegionTest.php
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
 * Page Test
 *
 * @since       2011-05-23
 * @category    Library
 * @package     PdfPage
 * @author      Nicola Asuni <info@tecnick.com>
 * @copyright   2011-2015 Nicola Asuni - Tecnick.com LTD
 * @license     http://www.gnu.org/copyleft/lesser.html GNU-LGPL v3 (see LICENSE.TXT)
 * @link        https://github.com/tecnickcom/tc-lib-pdf-page
 */
class RegionTest extends \PHPUnit_Framework_TestCase
{
    protected $obj = null;

    public function setUp()
    {
        //$this->markTestSkipped(); // skip this test

        $col = new \Com\Tecnick\Color\Pdf;
        $enc = new \Com\Tecnick\Pdf\Encrypt\Encrypt(false);
        $this->obj = new \Com\Tecnick\Pdf\Page\Page('mm', $col, $enc, false, false);
    }

    public function testRegion()
    {
        $this->obj->add(array('columns' => 3));
        $res = $this->obj->selectRegion(1);
        $exp = array(
            'X' => 70,
            'Y' => 0,
            'W' => 70,
            'H' => 297,
            'L' => 140,
            'R' => 70,
            'T' => 297,
            'B' => 0,
        );
        $this->assertEquals($exp, $res, '', 0.01);

        $res = $this->obj->getCurrentRegion();
        $this->assertEquals($exp, $res, '', 0.01);

        $res = $this->obj->getNextRegion();
        $this->assertEquals(2, $res['currentRegion'], '', 0.01);

        $res = $this->obj->getNextRegion();
        $this->assertEquals(0, $res['currentRegion'], '', 0.01);
    }

    public function testRegionBoundaries()
    {
        $this->obj->add(array('columns' => 3));
        $region = $this->obj->getCurrentRegion();

        $res = $this->obj->isYOutRegion(-1);
        $this->assertTrue($res);
        $res = $this->obj->isYOutRegion($region['Y']);
        $this->assertFalse($res);
        $res = $this->obj->isYOutRegion(0);
        $this->assertFalse($res);
        $res = $this->obj->isYOutRegion(100);
        $this->assertFalse($res);
        $res = $this->obj->isYOutRegion(297);
        $this->assertFalse($res);
        $res = $this->obj->isYOutRegion($region['T']);
        $this->assertFalse($res);
        $res = $this->obj->isYOutRegion(298);
        $this->assertTrue($res);

        $this->obj->getNextRegion();
        $region = $this->obj->getCurrentRegion();

        $res = $this->obj->isXOutRegion(69);
        $this->assertTrue($res);
        $res = $this->obj->isXOutRegion($region['X']);
        $this->assertFalse($res);
        $res = $this->obj->isXOutRegion(70);
        $this->assertFalse($res);
        $res = $this->obj->isXOutRegion(90);
        $this->assertFalse($res);
        $res = $this->obj->isXOutRegion(140);
        $this->assertFalse($res);
        $res = $this->obj->isXOutRegion($region['L']);
        $this->assertFalse($res);
        $res = $this->obj->isXOutRegion(141);
        $this->assertTrue($res);
    }
}
