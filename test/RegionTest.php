<?php

/**
 * RegionTest.php
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

use Com\Tecnick\Color\Pdf;
use Com\Tecnick\Pdf\Page\Page;

/**
 * Region class test
 *
 * @since     2011-05-23
 * @category  Library
 * @package   PdfPage
 * @author    Nicola Asuni <info@tecnick.com>
 * @copyright 2011-2026 Nicola Asuni - Tecnick.com LTD
 * @license   https://www.gnu.org/copyleft/lesser.html GNU-LGPL v3 (see LICENSE)
 * @link      https://github.com/tecnickcom/tc-lib-pdf-page
 */
class RegionTest extends TestUtil
{
    /**
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    protected function getTestObject(): \Com\Tecnick\Pdf\Page\Page
    {
        $pdf = new Pdf();
        $encrypt = $this->getEncryptObject();
        return new Page('mm', $pdf, $encrypt, false, false);
    }

    /**
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    public function testRegion(): void
    {
        $page = $this->getTestObject();
        $page->add([
            'columns' => 3,
        ]);

        $res = $page->selectRegion(1);
        $exp = [
            'RX' => 70,
            'RY' => 0,
            'RW' => 70,
            'RH' => 297,
            'RL' => 140,
            'RR' => 70,
            'RT' => 297,
            'RB' => 0,
            'x' => 70,
            'y' => 0,
        ];
        $this->bcAssertEqualsWithDelta($exp, $res);

        $res = $page->getRegion();
        $this->bcAssertEqualsWithDelta($exp, $res);

        $res = $page->getNextRegion();
        $this->bcAssertEqualsWithDelta(2, $res['currentRegion']);

        $res = $page->getNextRegion();
        $this->bcAssertEqualsWithDelta(0, $res['currentRegion']);

        $page->setCurrentPage(0);
        $res = $page->getNextRegion();
        $this->bcAssertEqualsWithDelta(0, $res['currentRegion']);

        $res = $page->checkRegionBreak(1000);
        $this->bcAssertEqualsWithDelta(1, $res['currentRegion']);

        $res = $page->checkRegionBreak();
        $this->bcAssertEqualsWithDelta(1, $res['currentRegion']);

        $page->setX(13)->setY(17);
        $this->bcAssertEqualsWithDelta(13, $page->getX());
        $this->bcAssertEqualsWithDelta(17, $page->getY());
    }

    /**
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    public function testRegionBoundaries(): void
    {
        $page = $this->getTestObject();
        $page->add([
            'columns' => 3,
        ]);

        $region = $page->getRegion();

        $res = $page->isYOutRegion(null, 1);
        $this->assertFalse($res);
        $res = $page->isYOutRegion(-1);
        $this->assertTrue($res);
        $res = $page->isYOutRegion($region['RY']);
        $this->assertFalse($res);
        $res = $page->isYOutRegion(0);
        $this->assertFalse($res);
        $res = $page->isYOutRegion(100);
        $this->assertFalse($res);
        $res = $page->isYOutRegion(297);
        $this->assertFalse($res);
        $res = $page->isYOutRegion($region['RT']);
        $this->assertFalse($res);
        $res = $page->isYOutRegion(298);
        $this->assertTrue($res);

        $page->getNextRegion();
        $region = $page->getRegion();

        $res = $page->isXOutRegion(null, 1);
        $this->assertFalse($res);
        $res = $page->isXOutRegion(69);
        $this->assertTrue($res);
        $res = $page->isXOutRegion($region['RX']);
        $this->assertFalse($res);
        $res = $page->isXOutRegion(70);
        $this->assertFalse($res);
        $res = $page->isXOutRegion(90);
        $this->assertFalse($res);
        $res = $page->isXOutRegion(140);
        $this->assertFalse($res);
        $res = $page->isXOutRegion($region['RL']);
        $this->assertFalse($res);
        $res = $page->isXOutRegion(141);
        $this->assertTrue($res);

        $pid = $page->getPageID();
        $this->assertEquals(0, $pid);
    }

    /**
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    public function testSetNoWriteRegionsSideRectangle(): void
    {
        $page = $this->getTestObject();
        $page->add();

        // Obstacle on the right side blocking x in [150, 210] for y in [100, 200].
        $res = $page->setNoWriteRegions([
            ['xt' => 150, 'yt' => 100, 'xb' => 150, 'yb' => 200, 'side' => 'R'],
        ], 50.0);

        $this->bcAssertEqualsWithDelta(3, $res['columns']);

        $exp = [
            [
                'RW' => 210,
                'RX' => 0,
                'RL' => 210,
                'RR' => 0,
                'RH' => 100,
                'RY' => 0,
                'RT' => 100,
                'RB' => 197,
                'x' => 0,
                'y' => 0,
            ],
            [
                'RW' => 150,
                'RX' => 0,
                'RL' => 150,
                'RR' => 60,
                'RH' => 100,
                'RY' => 100,
                'RT' => 200,
                'RB' => 97,
                'x' => 0,
                'y' => 100,
            ],
            [
                'RW' => 210,
                'RX' => 0,
                'RL' => 210,
                'RR' => 0,
                'RH' => 97,
                'RY' => 200,
                'RT' => 297,
                'RB' => 0,
                'x' => 0,
                'y' => 200,
            ],
        ];
        $this->bcAssertEqualsWithDelta($exp, $res['region']);
    }

    /**
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    public function testBuildWritableRegionsSlantedSegment(): void
    {
        $page = $this->getTestObject();
        $page->add();

        // Slanted right-side segment from (100,100) to (150,200): a staircase approximation.
        $res = $page->buildWritableRegions([
            ['xt' => 100, 'yt' => 100, 'xb' => 150, 'yb' => 200, 'side' => 'R'],
        ], 50.0);

        $exp = [
            ['RX' => 0, 'RY' => 0, 'RW' => 210, 'RH' => 100],
            ['RX' => 0, 'RY' => 100, 'RW' => 100, 'RH' => 50],
            ['RX' => 0, 'RY' => 150, 'RW' => 125, 'RH' => 50],
            ['RX' => 0, 'RY' => 200, 'RW' => 210, 'RH' => 97],
        ];
        $this->bcAssertEqualsWithDelta($exp, $res);
    }

    /**
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    public function testBuildWritableRegionsFloatingBoxKeepsWidestSide(): void
    {
        $page = $this->getTestObject();
        $page->add();

        // Floating obstacle blocking x in [60, 100] for y in [100, 150]:
        // the band splits into [0,60] (w=60) and [100,210] (w=110); the widest is kept.
        $res = $page->buildWritableRegions([
            ['x' => 60, 'y' => 100, 'w' => 40, 'h' => 50],
        ], 50.0);

        $exp = [
            ['RX' => 0, 'RY' => 0, 'RW' => 210, 'RH' => 100],
            ['RX' => 100, 'RY' => 100, 'RW' => 110, 'RH' => 50],
            ['RX' => 0, 'RY' => 150, 'RW' => 210, 'RH' => 147],
        ];
        $this->bcAssertEqualsWithDelta($exp, $res);
    }

    /**
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    public function testNoWriteRegionsAddGetRemove(): void
    {
        $page = $this->getTestObject();
        $page->add();

        $page->setNoWriteRegions([
            ['xt' => 150, 'yt' => 100, 'xb' => 150, 'yb' => 200, 'side' => 'R'],
        ], 50.0);
        $this->assertCount(1, $page->getNoWriteRegions());

        // The added left-side obstacle pushes the bottom region's left edge to x=60.
        $res = $page->addNoWriteRegion(['xt' => 60, 'yt' => 230, 'xb' => 60, 'yb' => 270, 'side' => 'L']);
        $this->assertCount(2, $page->getNoWriteRegions());
        $this->bcAssertEqualsWithDelta(3, $res['columns']);
        $expAdded = [
            [
                'RW' => 210,
                'RX' => 0,
                'RL' => 210,
                'RR' => 0,
                'RH' => 100,
                'RY' => 0,
                'RT' => 100,
                'RB' => 197,
                'x' => 0,
                'y' => 0,
            ],
            [
                'RW' => 150,
                'RX' => 0,
                'RL' => 150,
                'RR' => 60,
                'RH' => 100,
                'RY' => 100,
                'RT' => 200,
                'RB' => 97,
                'x' => 0,
                'y' => 100,
            ],
            [
                'RW' => 150,
                'RX' => 60,
                'RL' => 210,
                'RR' => 0,
                'RH' => 97,
                'RY' => 200,
                'RT' => 297,
                'RB' => 0,
                'x' => 60,
                'y' => 200,
            ],
        ];
        $this->bcAssertEqualsWithDelta($expAdded, $res['region']);

        // Removing the first (right) area leaves only the left-side area.
        $page->removeNoWriteRegion(0);
        $this->bcAssertEqualsWithDelta([[
            'xt' => 60,
            'yt' => 230,
            'xb' => 60,
            'yb' => 270,
            'side' => 'L',
        ]], $page->getNoWriteRegions());

        // Removing the last area leaves the page with a single full content region.
        $res = $page->removeNoWriteRegion(0);
        $this->assertCount(0, $page->getNoWriteRegions());
        $this->bcAssertEqualsWithDelta(1, $res['columns']);
        $this->bcAssertEqualsWithDelta([[
            'RW' => 210,
            'RX' => 0,
            'RL' => 210,
            'RR' => 0,
            'RH' => 297,
            'RY' => 0,
            'RT' => 297,
            'RB' => 0,
            'x' => 0,
            'y' => 0,
        ]], $res['region']);
    }

    /**
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    public function testNoWriteRegionsResetOnClonedPage(): void
    {
        $page = $this->getTestObject();
        $page->add();

        $res = $page->setNoWriteRegions([
            ['xt' => 150, 'yt' => 100, 'xb' => 150, 'yb' => 200, 'side' => 'R'],
        ], 50.0);
        $this->assertGreaterThan(1, $res['columns']);

        // Cloning the page (e.g. an automatic page break) must NOT inherit the no-write bands:
        // the new page starts with a single default full-content region.
        $cloned = $page->add();
        $this->bcAssertEqualsWithDelta(1, $cloned['columns']);
        $this->assertCount(0, $page->getNoWriteRegions($cloned['pid']));
        $this->bcAssertEqualsWithDelta([[
            'RW' => 210,
            'RX' => 0,
            'RL' => 210,
            'RR' => 0,
            'RH' => 297,
            'RY' => 0,
            'RT' => 297,
            'RB' => 0,
            'x' => 0,
            'y' => 0,
        ]], $cloned['region']);
    }

    /**
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    public function testNoWriteRegionsValidation(): void
    {
        $page = $this->getTestObject();
        $page->add();

        try {
            $page->setNoWriteRegions([], 0.0);
            $this->fail('Expected exception was not thrown.');
        } catch (\Com\Tecnick\Pdf\Page\Exception $e) {
            $this->assertStringContainsString('band height', $e->getMessage());
        }

        try {
            $page->buildWritableRegions([], -5.0);
            $this->fail('Expected exception was not thrown.');
        } catch (\Com\Tecnick\Pdf\Page\Exception $e) {
            $this->assertStringContainsString('band height', $e->getMessage());
        }

        try {
            $page->addNoWriteRegion(['x' => 10, 'y' => 10, 'w' => 10, 'h' => 10]);
            $this->fail('Expected exception was not thrown.');
        } catch (\Com\Tecnick\Pdf\Page\Exception $e) {
            $this->assertStringContainsString('setNoWriteRegions', $e->getMessage());
        }

        try {
            $page->removeNoWriteRegion(7);
            $this->fail('Expected exception was not thrown.');
        } catch (\Com\Tecnick\Pdf\Page\Exception $e) {
            $this->assertStringContainsString('index 7', $e->getMessage());
        }
    }

    /**
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    public function testDefensiveChecksOnMissingPageAfterForcedSanitizeId(): void
    {
        $pdf = new Pdf();
        $encrypt = $this->getEncryptObject();
        $page = new class('mm', $pdf, $encrypt, false, false) extends Page {
            public bool $forceSanitizePageId = false;
            public int $forcedPid = 0;

            protected function sanitizePageID(int $pid = -1): int
            {
                if ($this->forceSanitizePageId) {
                    return $this->forcedPid;
                }

                return parent::sanitizePageID($pid);
            }
        };
        $page->add();
        $page->forceSanitizePageId = true;
        $page->forcedPid = 99;

        try {
            $page->getPage(99);
            $this->fail('Expected exception was not thrown.');
        } catch (\Com\Tecnick\Pdf\Page\Exception $e) {
            $this->assertStringContainsString('index 99', $e->getMessage());
        }

        try {
            $page->setPagePHeight(10, 99);
            $this->fail('Expected exception was not thrown.');
        } catch (\Com\Tecnick\Pdf\Page\Exception $e) {
            $this->assertStringContainsString('index 99', $e->getMessage());
        }

        try {
            $page->setPagePWidth(10, 99);
            $this->fail('Expected exception was not thrown.');
        } catch (\Com\Tecnick\Pdf\Page\Exception $e) {
            $this->assertStringContainsString('index 99', $e->getMessage());
        }

        try {
            $page->selectRegion(0, 99);
            $this->fail('Expected exception was not thrown.');
        } catch (\Com\Tecnick\Pdf\Page\Exception $e) {
            $this->assertStringContainsString('index 99', $e->getMessage());
        }

        try {
            $page->getNextRegion(99);
            $this->fail('Expected exception was not thrown.');
        } catch (\Com\Tecnick\Pdf\Page\Exception $e) {
            $this->assertStringContainsString('index 99', $e->getMessage());
        }

        try {
            $page->isAutoPageBreakEnabled(99);
            $this->fail('Expected exception was not thrown.');
        } catch (\Com\Tecnick\Pdf\Page\Exception $e) {
            $this->assertStringContainsString('index 99', $e->getMessage());
        }

        try {
            $page->enableAutoPageBreak(true, 99);
            $this->fail('Expected exception was not thrown.');
        } catch (\Com\Tecnick\Pdf\Page\Exception $e) {
            $this->assertStringContainsString('index 99', $e->getMessage());
        }

        try {
            $page->setX(1, 99);
            $this->fail('Expected exception was not thrown.');
        } catch (\Com\Tecnick\Pdf\Page\Exception $e) {
            $this->assertStringContainsString('index 99', $e->getMessage());
        }

        try {
            $page->setY(1, 99);
            $this->fail('Expected exception was not thrown.');
        } catch (\Com\Tecnick\Pdf\Page\Exception $e) {
            $this->assertStringContainsString('index 99', $e->getMessage());
        }
    }

    /**
     * An out-of-range region index clamps to the nearest valid region.
     *
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    public function testSelectRegionClampsOutOfRangeIndex(): void
    {
        $page = $this->getTestObject();
        $page->add([
            'columns' => 3,
        ]);

        // Index beyond the last region (valid: 0..2) clamps to the last region.
        $res = $page->selectRegion(99);
        $this->bcAssertEqualsWithDelta(2, $page->getPage()['currentRegion']);
        $this->bcAssertEqualsWithDelta($res, $page->getRegion());

        // Negative index clamps to the first region.
        $page->selectRegion(-5);
        $this->bcAssertEqualsWithDelta(0, $page->getPage()['currentRegion']);
    }

    /**
     * A cloned page is a fresh page: its region cursors must start at the region
     * origin instead of inheriting the write position of the source page.
     *
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    public function testClonedPageResetsRegionCursors(): void
    {
        $page = $this->getTestObject();
        $page->add([
            'format' => 'A4',
            'margin' => [
                'PL' => 10,
                'PR' => 10,
                'PT' => 10,
                'PB' => 10,
            ],
        ]);
        $page->setX(120.0)->setY(250.0);

        $res = $page->add();
        $region = $res['region'][0] ?? [];

        $this->bcAssertEqualsWithDelta($region['RX'] ?? null, $region['x'] ?? null);
        $this->bcAssertEqualsWithDelta($region['RY'] ?? null, $region['y'] ?? null);
        $this->bcAssertEqualsWithDelta(10, $page->getX());
        $this->bcAssertEqualsWithDelta(10, $page->getY());
    }

    /**
     * Booklet margins alternate on every page, including the pages created by
     * cloning (the path taken by the automatic page break).
     *
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    public function testClonedBookletPageMirrorsMargins(): void
    {
        $page = $this->getTestObject();
        $page->add([
            'format' => 'A4',
            'margin' => [
                'booklet' => true,
                'PL' => 30,
                'PR' => 10,
                'PT' => 10,
                'PB' => 10,
            ],
        ]);

        $second = $page->add();
        $this->bcAssertEqualsWithDelta(10, $second['margin']['PL']);
        $this->bcAssertEqualsWithDelta(30, $second['margin']['PR']);
        $this->bcAssertEqualsWithDelta(10, ($second['region'][0] ?? [])['RX'] ?? null);

        $third = $page->add();
        $this->bcAssertEqualsWithDelta(30, $third['margin']['PL']);
        $this->bcAssertEqualsWithDelta(10, $third['margin']['PR']);
        $this->bcAssertEqualsWithDelta(30, ($third['region'][0] ?? [])['RX'] ?? null);
    }

    /**
     * isYOutRegion() and isXOutRegion() must read the implicit cursor from the
     * queried page, not from the current one.
     *
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    public function testIsOutRegionUsesTheQueriedPageCursor(): void
    {
        $page = $this->getTestObject();
        $page->add();
        $page->add();

        $page->setY(10.0, 0);
        $page->setY(400.0, 1);
        $page->setX(10.0, 0);
        $page->setX(400.0, 1);
        $page->setCurrentPage(0);

        $this->assertTrue($page->isYOutRegion(null, 0.0, 1));
        $this->assertTrue($page->isXOutRegion(null, 0.0, 1));
        $this->assertFalse($page->isYOutRegion(null, 0.0, 0));
        $this->assertFalse($page->isXOutRegion(null, 0.0, 0));
    }

    /**
     * Moving to an already existing page enters it from its first region.
     *
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    public function testGetNextRegionEntersTheNextPageFromItsFirstRegion(): void
    {
        $page = $this->getTestObject();
        $page->add([
            'columns' => 3,
        ]);
        $page->add([
            'columns' => 3,
        ]);

        // Leave page 1 pointing at its last region, then leave page 0 the same way.
        $page->selectRegion(2, 1);
        $page->selectRegion(2, 0);

        $res = $page->getNextRegion(0);

        $this->bcAssertEqualsWithDelta(1, $res['pid']);
        $this->bcAssertEqualsWithDelta(0, $res['currentRegion']);
    }

    /**
     * A band height that would slice the content area into more than MAX_BANDS
     * bands is rejected instead of looping for an unbounded amount of time.
     *
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    public function testBuildWritableRegionsRejectsTooManyBands(): void
    {
        $page = $this->getTestObject();
        $page->add();

        try {
            $page->buildWritableRegions([], 0.0001001);
            $this->fail('Expected exception was not thrown.');
        } catch (\Com\Tecnick\Pdf\Page\Exception $e) {
            $this->assertStringContainsString('band height is too small', $e->getMessage());
        }

        // A sane band height still works.
        $this->assertNotEmpty($page->buildWritableRegions([], 10.0));
    }

    /**
     * Overriding the page size must resize the MediaBox and leave the declared
     * coordinates of the other boxes untouched.
     *
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    public function testSetPageSizeResizesTheMediaBox(): void
    {
        $page = $this->getTestObject();
        $page->add([
            'format' => 'A4',
        ]);

        $page->setPagePHeight(300.0);
        $page->setPagePWidth(200.0);

        $corners = [];
        foreach ($page->getPage(0)['box'] as $type => $box) {
            $corners[$type] = [$box['llx'], $box['lly'], $box['urx'], $box['ury']];
        }

        $this->bcAssertEqualsWithDelta([
            'MediaBox' => [0.0, 0.0, 200.0, 300.0],
            'CropBox' => [0.0, 0.0, 595.276, 841.89],
            'BleedBox' => [0.0, 0.0, 595.276, 841.89],
            'TrimBox' => [0.0, 0.0, 595.276, 841.89],
            'ArtBox' => [0.0, 0.0, 595.276, 841.89],
        ], $corners);
    }

    /**
     * Restoring the page size returned by the setters must restore the original
     * page geometry.
     *
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    public function testSetPageSizeRoundTripRestoresTheBoxes(): void
    {
        $page = $this->getTestObject();
        $page->add([
            'format' => 'A4',
        ]);

        $expected = $page->getPage(0)['box'];

        $pheight = $page->setPagePHeight(28.35);
        $pwidth = $page->setPagePWidth(56.7);
        $page->setPagePWidth($pwidth);
        $page->setPagePHeight($pheight);

        $this->bcAssertEqualsWithDelta($expected, $page->getPage(0)['box']);
    }

    /**
     * Overriding the page size must keep the orientation consistent with it.
     *
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    public function testSetPageSizeUpdatesTheOrientation(): void
    {
        $page = $this->getTestObject();
        $page->add([
            'format' => 'A4',
        ]);
        $this->assertSame('P', $page->getPage(0)['orientation']);

        $page->setPagePWidth(1000.0);
        $this->assertSame('L', $page->getPage(0)['orientation']);

        $page->setPagePHeight(2000.0);
        $this->assertSame('P', $page->getPage(0)['orientation']);
    }

    /**
     * A block ending above the region top does not need more room, so it must
     * neither consume a region nor add a page.
     *
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    public function testCheckRegionBreakIgnoresPositionsAboveTheRegion(): void
    {
        $page = $this->getTestObject();
        $page->add([
            'format' => 'A4',
            'margin' => [
                'PL' => 10,
                'PR' => 10,
                'PT' => 10,
                'PB' => 10,
            ],
        ]);

        // The cursor sits in the header area, above the region top (RY = 10).
        $page->setY(0.0);
        $res = $page->checkRegionBreak(5.0);

        $this->bcAssertEqualsWithDelta(0, $res['pid']);
        $this->bcAssertEqualsWithDelta(0, $res['currentRegion']);
        $this->assertCount(1, $page->getPages());

        // A real overflow still breaks.
        $res = $page->checkRegionBreak(1000.0);
        $this->assertCount(2, $page->getPages());
        $this->bcAssertEqualsWithDelta(1, $res['pid']);
    }

    /**
     * When the automatic page break is disabled on the last page there is nowhere
     * to go, so the selected region must be kept instead of rewinding to the first
     * one and cycling forever.
     *
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    public function testGetNextRegionKeepsTheRegionWhenItCannotLeaveThePage(): void
    {
        $page = $this->getTestObject();
        $page->add([
            'columns' => 3,
            'autobreak' => false,
        ]);
        $page->selectRegion(2);

        for ($idx = 0; $idx < 3; ++$idx) {
            $res = $page->getNextRegion();
            $this->bcAssertEqualsWithDelta(0, $res['pid']);
            $this->bcAssertEqualsWithDelta(2, $res['currentRegion']);
        }

        $this->assertCount(1, $page->getPages());
    }

    /**
     * The booklet gutter depends on the position of the new page in the stack, so
     * it must not follow the current page pointer.
     *
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    public function testBookletMarginsFollowTheNewPageIndex(): void
    {
        $page = $this->getTestObject();
        $margin = [
            'booklet' => true,
            'PL' => 30,
            'PR' => 10,
            'PT' => 10,
            'PB' => 10,
        ];

        $page->add([
            'format' => 'A4',
            'margin' => $margin,
        ]);
        $page->add([
            'format' => 'A4',
            'margin' => $margin,
        ]);

        // Inspecting an earlier page must not change the gutter of the next one.
        $page->setCurrentPage(0);

        $third = $page->add([
            'format' => 'A4',
            'margin' => $margin,
        ]);

        $this->bcAssertEqualsWithDelta(2, $third['pid']);
        $this->bcAssertEqualsWithDelta(30, $third['margin']['PL']);
        $this->bcAssertEqualsWithDelta(10, $third['margin']['PR']);
    }

    /**
     * A page is a rectangle with a positive area, so a non-positive override is rejected
     * instead of inverting the MediaBox.
     *
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    public function testSetPagePSizeRejectsNonPositiveValues(): void
    {
        $page = $this->getTestObject();
        $page->add([
            'format' => 'A4',
        ]);

        $thrown = 0;
        foreach ([0.0, -1.0] as $value) {
            try {
                $page->setPagePWidth($value);
            } catch (\Com\Tecnick\Pdf\Page\Exception) {
                ++$thrown;
            }

            try {
                $page->setPagePHeight($value);
            } catch (\Com\Tecnick\Pdf\Page\Exception) {
                ++$thrown;
            }
        }

        $this->assertSame(4, $thrown);

        // The page is left untouched by the rejected calls.
        $this->bcAssertEqualsWithDelta(595.276, $page->getPage(0)['pwidth']);
        $this->bcAssertEqualsWithDelta(841.890, $page->getPage(0)['pheight']);
    }

    /**
     * A cloned booklet page mirrors the gutter, and its regions follow it by being
     * translated: they must not be replaced by that many equal columns.
     *
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    public function testClonedBookletPageKeepsCustomRegions(): void
    {
        $page = $this->getTestObject();
        $page->add([
            'format' => 'A4',
            'margin' => [
                'booklet' => true,
                'PL' => 30.0,
                'PR' => 10.0,
            ],
            'region' => [
                [
                    'RX' => 30.0,
                    'RY' => 0.0,
                    'RW' => 40.0,
                    'RH' => 100.0,
                ],
                [
                    'RX' => 90.0,
                    'RY' => 0.0,
                    'RW' => 80.0,
                    'RH' => 250.0,
                ],
            ],
        ]);

        $cloned = $page->add();

        // The gutter moved from PL = 30 to PL = 10, so every region shifts left by 20.
        $this->bcAssertEqualsWithDelta([
            [10.0, 40.0, 100.0],
            [70.0, 80.0, 250.0],
        ], $this->getRegionExtents($cloned['region']));
    }

    /**
     * Reduce a region list to the [RX, RW, RH] triplet of each region.
     *
     * @param array<int, array<string, float>> $regions Page regions.
     *
     * @return array<int, array{0: float, 1: float, 2: float}> Region extents.
     */
    private function getRegionExtents(array $regions): array
    {
        $extents = [];
        foreach ($regions as $region) {
            $extents[] = [$region['RX'] ?? 0.0, $region['RW'] ?? 0.0, $region['RH'] ?? 0.0];
        }

        return $extents;
    }

    /**
     * A page really split into equal columns keeps producing the same columns when
     * cloned: translating them by the gutter delta is the same as rebuilding them.
     *
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    public function testClonedBookletPageKeepsEqualColumns(): void
    {
        $page = $this->getTestObject();
        $first = $page->add([
            'format' => 'A4',
            'columns' => 3,
            'margin' => [
                'booklet' => true,
                'PL' => 30.0,
                'PR' => 10.0,
            ],
        ]);

        $cloned = $page->add();

        $expected = [];
        foreach ($this->getRegionExtents($first['region']) as $extent) {
            $expected[] = [$extent[0] - 20.0, $extent[1], $extent[2]];
        }

        $this->assertCount(3, $expected);
        $this->bcAssertEqualsWithDelta($expected, $this->getRegionExtents($cloned['region']));
    }
}
