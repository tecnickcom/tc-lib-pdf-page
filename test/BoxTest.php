<?php

/**
 * BoxTest.php
 *
 * @since     2011-05-23
 * @category  Library
 * @package   PdfPage
 * @author    Nicola Asuni <info@tecnick.com>
 * @copyright 2011-2026 Nicola Asuni - Tecnick.com LTD
 * @license   https://www.gnu.org/copyleft/lesser.html GNU-LGPL v3 (see LICENSE)
 * @link      https://github.com/tecnickcom/tc-lib-pdf-page
 *
 * This file is part of tc-lib-pdf-page software library.
 */

namespace Test;

/**
 * Box class test
 *
 * @since     2011-05-23
 * @category  Library
 * @package   PdfPage
 * @author    Nicola Asuni <info@tecnick.com>
 * @copyright 2011-2026 Nicola Asuni - Tecnick.com LTD
 * @license   https://www.gnu.org/copyleft/lesser.html GNU-LGPL v3 (see LICENSE)
 * @link      https://github.com/tecnickcom/tc-lib-pdf-page
 */
class BoxTest extends TestUtil
{
    /**
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    protected function getTestObject(): \Com\Tecnick\Pdf\Page\Page
    {
        $pdf = new \Com\Tecnick\Color\Pdf();
        $encrypt = $this->getEncryptObject();
        return new \Com\Tecnick\Pdf\Page\Page('mm', $pdf, $encrypt, false, false);
    }

    /**
     * Build a page carrying the given BoxColorInfo color on every page box.
     *
     * @throws \Com\Tecnick\Pdf\Page\Exception
     * @throws \Com\Tecnick\Pdf\Encrypt\Exception
     */
    protected function getBoxColorInfoOutput(\Com\Tecnick\Color\Pdf $color, string $boxcolor): string
    {
        $page = new \Com\Tecnick\Pdf\Page\Page('mm', $color, $this->getEncryptObject(), false, false);
        $bci = [
            'color' => $boxcolor,
            'width' => 1.0,
            'style' => 'S',
            'dash' => [3],
        ];
        $box = [];
        foreach (\Com\Tecnick\Pdf\Page\Page::BOX as $type) {
            $box = $page->setBox($box, $type, 0, 0, 595, 842, $bci);
        }

        $page->add(['box' => $box]);
        $pon = 0;

        return $page->getPdfPages($pon);
    }

    /**
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    public function testSetBox(): void
    {
        $page = $this->getTestObject();
        $dims = $page->setBox([], 'CropBox', 2, 4, 6, 8);
        $this->bcAssertEqualsWithDelta([
            'CropBox' => [
                'llx' => 2,
                'lly' => 4,
                'urx' => 6,
                'ury' => 8,
                'bci' => [
                    'color' => '#000000',
                    'width' => 0.353,
                    'style' => 'S',
                    'dash' => [3],
                ],
            ],
        ], $dims);

        $dims = $page->setBox([], 'TrimBox', 3, 5, 7, 11, [
            'color' => 'aquamarine',
            'width' => 2,
            'style' => 'D',
            'dash' => [2, 3, 5, 7],
        ]);
        $this->bcAssertEqualsWithDelta([
            'TrimBox' => [
                'llx' => 3,
                'lly' => 5,
                'urx' => 7,
                'ury' => 11,
                'bci' => [
                    'color' => 'aquamarine',
                    'width' => 2,
                    'style' => 'D',
                    'dash' => [2, 3, 5, 7],
                ],
            ],
        ], $dims);
    }

    /**
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    public function testSetBoxEx(): void
    {
        $this->bcExpectException(\Com\Tecnick\Pdf\Page\Exception::class);
        $page = $this->getTestObject();
        $page->setBox([], 'ERROR', 1, 2, 3, 4);
    }

    /**
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    public function testSwapCoordinates(): void
    {
        $page = $this->getTestObject();
        $dims = [
            'CropBox' => [
                'llx' => 2,
                'lly' => 4,
                'urx' => 6,
                'ury' => 8,
            ],
        ];
        $newpagedim = $page->swapCoordinates($dims);
        $this->assertEquals(
            [
                'CropBox' => [
                    'llx' => 4,
                    'lly' => 2,
                    'urx' => 8,
                    'ury' => 6,
                ],
            ],
            $newpagedim,
        );
    }

    /**
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    public function testSetPageBoxes(): void
    {
        $page = $this->getTestObject();
        $dims = $page->setPageBoxes(100, 200);
        $exp = [
            'llx' => 0,
            'lly' => 0,
            'urx' => 100,
            'ury' => 200,
            'bci' => [
                'color' => '#000000',
                'width' => 0.353,
                'style' => 'S',
                'dash' => [3],
            ],
        ];
        $this->bcAssertEqualsWithDelta([
            'MediaBox' => $exp,
            'CropBox' => $exp,
            'BleedBox' => $exp,
            'TrimBox' => $exp,
            'ArtBox' => $exp,
        ], $dims);
    }

    /**
     * @throws \Com\Tecnick\Pdf\Page\Exception
     * @throws \Com\Tecnick\Pdf\Encrypt\Exception
     */
    public function testGetBoxColorInfo(): void
    {
        $out = $this->getBoxColorInfoOutput(new \Com\Tecnick\Color\Pdf(), '#331133');
        $this->bcAssertStringContainsString('/C [0.200000 0.066667 0.200000]', $out);
    }

    /**
     * An unresolvable color yields no /C entry, as an empty array is not a valid
     * BoxColorInfo color.
     *
     * @throws \Com\Tecnick\Pdf\Page\Exception
     * @throws \Com\Tecnick\Pdf\Encrypt\Exception
     */
    public function testGetBoxColorInfoUnresolvedColor(): void
    {
        $out = $this->getBoxColorInfoOutput(new \Com\Tecnick\Color\Pdf(), 'ERROR');
        $this->assertStringNotContainsString('/C [', $out);
        $this->bcAssertStringContainsString('/BoxColorInfo <<', $out);
    }

    /**
     * A guideline width of 0 means "do not display the guideline", so it must be
     * emitted: omitting /W would let the reader apply the default width of 1.
     *
     * @throws \Com\Tecnick\Pdf\Page\Exception
     * @throws \Com\Tecnick\Pdf\Encrypt\Exception
     */
    public function testGetBoxColorInfoZeroGuidelineWidth(): void
    {
        $page = $this->getTestObject();
        $bci = [
            'color' => '#000000',
            'width' => 0.0,
            'style' => 'S',
            'dash' => [],
        ];
        $box = [];
        foreach (\Com\Tecnick\Pdf\Page\Page::BOX as $type) {
            $box = $page->setBox($box, $type, 0, 0, 595, 842, $bci);
        }

        $page->add(['box' => $box, 'format' => 'MediaBox']);
        $pon = 0;
        $out = $page->getPdfPages($pon);

        $this->assertSame(5, \substr_count($out, '/W 0.000000' . "\n"));
        $this->assertStringNotContainsString('/D [', $out);
    }

    /**
     * A spot color name is resolved to its RGB components without being
     * registered, so it adds no Separation object to the document resources.
     *
     * @throws \Com\Tecnick\Pdf\Page\Exception
     * @throws \Com\Tecnick\Pdf\Encrypt\Exception
     * @throws \Com\Tecnick\Color\Exception
     */
    public function testGetBoxColorInfoSpotColorIsNotRegistered(): void
    {
        $color = new \Com\Tecnick\Color\Pdf();
        $out = $this->getBoxColorInfoOutput($color, 'red');
        $this->bcAssertStringContainsString('/C [1.000000 0.000000 0.000000]', $out);

        $pon = 0;
        $this->assertSame('', $color->getPdfSpotObjects($pon));
    }

    /**
     * llx/lly is the lower-left corner and urx/ury the upper-right one, so a box
     * given with the corners reversed is stored with them ordered.
     *
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    public function testSetBoxOrdersReversedCorners(): void
    {
        $page = $this->getTestObject();

        $dims = $page->setBox([], 'MediaBox', 100.0, 200.0, 50.0, 100.0);
        $box = $dims['MediaBox'] ?? [];

        $this->assertSame(50.0, $box['llx'] ?? null);
        $this->assertSame(100.0, $box['lly'] ?? null);
        $this->assertSame(100.0, $box['urx'] ?? null);
        $this->assertSame(200.0, $box['ury'] ?? null);
    }
}
