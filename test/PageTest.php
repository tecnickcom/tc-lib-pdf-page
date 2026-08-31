<?php

/**
 * PageTest.php
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
 * Page class test
 *
 * @since     2011-05-23
 * @category  Library
 * @package   PdfPage
 * @author    Nicola Asuni <info@tecnick.com>
 * @copyright 2011-2026 Nicola Asuni - Tecnick.com LTD
 * @license   https://www.gnu.org/copyleft/lesser.html GNU-LGPL v3 (see LICENSE)
 * @link      https://github.com/tecnickcom/tc-lib-pdf-page
 */
class PageTest extends TestUtil
{
    /**
     * PDF marker for the per-page transparency group.
     */
    private const TRANSPARENCY_GROUP = '/Group << /Type /Group /S /Transparency /CS /DeviceRGB >>';

    /**
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    protected function getTestObject(): \Com\Tecnick\Pdf\Page\Page
    {
        $pdf = new \Com\Tecnick\Color\Pdf();
        $encrypt = $this->getEncryptObject();
        return new \Com\Tecnick\Pdf\Page\Page('mm', $pdf, $encrypt, false, true, false);
    }

    /**
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    protected function getNoTransparencyTestObject(): \Com\Tecnick\Pdf\Page\Page
    {
        $pdf = new \Com\Tecnick\Color\Pdf();
        $encrypt = $this->getEncryptObject();
        return new \Com\Tecnick\Pdf\Page\Page('mm', $pdf, $encrypt, true, true, false);
    }

    /**
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    public function testGetKUnit(): void
    {
        $page = $this->getTestObject();
        $this->bcAssertEqualsWithDelta(2.83464566929134, $page->getKUnit(), 0.001);
    }

    /**
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    public function testEnableSignatureApproval(): void
    {
        $page = $this->getTestObject();
        $res = $page->enableSignatureApproval(true);
        $this->assertNotNull($res); // @phpstan-ignore method.alreadyNarrowedType
    }

    /**
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    public function testAdd(): void
    {
        $page = $this->getTestObject();
        // page 1
        $res = $page->add();

        $box = [
            'llx' => 0,
            'lly' => 0,
            'urx' => 595.2765,
            'ury' => 841.890,
            'bci' => [
                'color' => '#000000',
                'width' => 0.353,
                'style' => 'S',
                'dash' => [
                    0 => 3,
                ],
            ],
        ];

        $exp = [
            'group' => 0,
            'rotation' => 0,
            'zoom' => 1,
            'orientation' => 'P',
            'format' => 'A4',
            'pheight' => 841.890,
            'pwidth' => 595.2765,
            'width' => 210,
            'height' => 297,
            'box' => [
                'MediaBox' => $box,
                'CropBox' => $box,
                'BleedBox' => $box,
                'TrimBox' => $box,
                'ArtBox' => $box,
            ],
            'margin' => [
                'booklet' => false,
                'PL' => 0,
                'PR' => 0,
                'PT' => 0,
                'HB' => 0,
                'CT' => 0,
                'CB' => 0,
                'FT' => 0,
                'PB' => 0,
            ],
            'ContentWidth' => 210,
            'ContentHeight' => 297,
            'HeaderHeight' => 0,
            'FooterHeight' => 0,
            'region' => [[
                'RX' => 0,
                'RY' => 0,
                'RW' => 210,
                'RH' => 297,
                'RL' => 210,
                'RR' => 0.0,
                'RT' => 297,
                'RB' => 0.0,
                'x' => 0.0,
                'y' => 0.0,
            ]],
            'currentRegion' => 0,
            'columns' => 1,
            'content' => [
                0 => '',
            ],
            'annotrefs' => [],
            'content_mark' => [
                0 => 0,
            ],
            'autobreak' => true,
            'pagenum' => 0,
            'num' => 0,
            'n' => 0,
        ];

        unset($res['time']);
        $exp['pid'] = 0;
        $this->bcAssertEqualsWithDelta($exp, $res);

        // page 2
        $res = $page->add();
        unset($res['time']);
        $exp['pid'] = 1;
        $this->bcAssertEqualsWithDelta($exp, $res);

        // page 3
        $res = $page->add([
            'group' => 1,
        ]);
        unset($res['time']);
        $exp['pid'] = 2;
        $exp['group'] = 1;
        $this->bcAssertEqualsWithDelta($exp, $res);

        // page 4
        $res = $page->add([
            'columns' => 2,
        ]);
        unset($res['time']);
        $exp['pid'] = 3;
        $exp['group'] = 0;
        $exp['columns'] = 2;
        $exp['region'] = [
            0 => [
                'RX' => 0,
                'RY' => 0,
                'RW' => 105,
                'RH' => 297,
                'RL' => 105,
                'RR' => 105,
                'RT' => 297,
                'RB' => 0,
                'x' => 0,
                'y' => 0,
            ],
            1 => [
                'RX' => 105,
                'RY' => 0,
                'RW' => 105,
                'RH' => 297,
                'RL' => 210,
                'RR' => 0.0,
                'RT' => 297,
                'RB' => 0,
                'x' => 105,
                'y' => 0,
            ],
        ];
        $this->bcAssertEqualsWithDelta($exp, $res);

        $pid = $page->getPageID();
        $this->assertEquals(3, $pid);
    }

    /**
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    public function testGetNextPage(): void
    {
        $page = $this->getTestObject();
        $page->add();
        $page->add();
        $page->add();
        $page->add();

        $page->setCurrentPage(2);
        $page->getNextPage();
        $page->enableAutoPageBreak(false);
        $page->getNextPage();
        $page->enableAutoPageBreak(true);
        $page->getNextPage();
        $page->getNextPage();

        $this->assertCount(6, $page->getPages());
    }

    /**
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    public function testDelete(): void
    {
        $page = $this->getTestObject();
        $page->add();
        $page->add();
        $page->add();
        $this->assertCount(3, $page->getPages());
        $res = $page->delete(1);
        $this->assertCount(2, $page->getPages());
        $this->assertArrayHasKey('time', $res);
    }

    /**
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    public function testDeleteEx(): void
    {
        $this->bcExpectException(\Com\Tecnick\Pdf\Page\Exception::class);
        $page = $this->getTestObject();
        $page->delete(2);
    }

    /**
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    public function testPop(): void
    {
        $page = $this->getTestObject();
        $page->add();
        $page->add();
        $page->add();
        $this->assertCount(3, $page->getPages());
        $res = $page->pop();
        $this->assertCount(2, $page->getPages());
        $this->assertArrayHasKey('time', $res);
    }

    /**
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    public function testMove(): void
    {
        $page = $this->getTestObject();
        $page->add();
        $page->add([
            'group' => 1,
        ]);
        $page->add([
            'group' => 2,
        ]);
        $page->add([
            'group' => 3,
        ]);

        $this->assertEquals($page->getPage(3), $page->getPage());

        $page->move(3, 0);
        $this->assertCount(4, $page->getPages());

        $res = $page->getPage(0);
        $this->assertEquals(3, $res['group']);
    }

    /**
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    public function testMoveEx(): void
    {
        $this->bcExpectException(\Com\Tecnick\Pdf\Page\Exception::class);
        $page = $this->getTestObject();
        $page->move(1, 2);
    }

    /**
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    public function testGetPageEx(): void
    {
        $this->bcExpectException(\Com\Tecnick\Pdf\Page\Exception::class);
        $page = $this->getTestObject();
        $page->getPage(2);
    }

    /**
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    public function testContent(): void
    {
        $testObj = $this->getTestObject();
        $testObj->add();
        $testObj->addContent('Lorem');
        $testObj->addContent('ipsum');
        $testObj->addContentMark();
        $testObj->addContent('dolor');
        $testObj->addContent('sit');
        $testObj->addContent('amet');

        $this->assertEquals('amet', $testObj->popContent());

        $page = $testObj->getPage();
        $this->assertEquals([0, 3], $page['content_mark']);
        $this->assertEquals(['', 'Lorem', 'ipsum', 'dolor', 'sit'], $page['content']);

        $testObj->popContentToLastMark();
        $page = $testObj->getPage();
        $this->assertEquals([0], $page['content_mark']);
        $this->assertEquals(['', 'Lorem', 'ipsum'], $page['content']);
    }

    /**
     * The base mark seeded by add() marks the start of the page and must survive
     * every pop, so the mark stack never underflows.
     *
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    public function testPopContentToLastMarkKeepsTheBaseMark(): void
    {
        $testObj = $this->getTestObject();
        $testObj->add();
        $testObj->addContent('Lorem');
        $testObj->addContent('ipsum');

        $testObj->popContentToLastMark();
        $page = $testObj->getPage();
        $this->assertEquals([0], $page['content_mark']);
        $this->assertEmpty($page['content']);

        // The stack is still usable: a new mark can be pushed and popped again.
        $testObj->addContent('dolor');
        $testObj->addContentMark();
        $testObj->addContent('sit');

        $testObj->popContentToLastMark();
        $page = $testObj->getPage();
        $this->assertEquals([0], $page['content_mark']);
        $this->assertEquals(['dolor'], $page['content']);
    }

    /**
     * @throws \Com\Tecnick\Pdf\Page\Exception
     * @throws \Com\Tecnick\Pdf\Encrypt\Exception
     */
    public function testGetPdfPages(): void
    {
        $page = $this->getTestObject();
        $page->add();
        $page->addContent('TEST1');
        $page->add();
        $page->addContent('TEST2');
        $page->add([
            'group' => 1,
            'transition' => [
                'Dur' => 2,
                'D' => 3,
                'Dm' => 'V',
                'S' => 'Glitter',
                'M' => 'O',
                'Di' => 315,
                'SS' => 1.3,
                'B' => true,
            ],
            'annotrefs' => [10, 20],
        ]);
        $page->addContent('TEST2');

        $pon = 0;
        $out = $page->getPdfPages($pon);
        $this->assertEquals(1, $page->getResourceDictObjID());
        $this->assertEquals(2, $page->getRootObjID());
        $this->assertStringContainsString('<< /Type /Pages /Kids [ 3 0 R 4 0 R 5 0 R ] /Count 3 >>', $out);
    }

    /**
     * A page shrunk below its declared boxes emits them intersected with the
     * MediaBox, while the page data keeps the declared coordinates.
     *
     * @throws \Com\Tecnick\Pdf\Page\Exception
     * @throws \Com\Tecnick\Pdf\Encrypt\Exception
     */
    public function testGetPdfPagesClampsTheBoxesToTheMediaBox(): void
    {
        $page = $this->getTestObject();
        $page->add([
            'format' => 'A4',
        ]);
        $page->setPagePHeight(300.0);

        $pon = 0;
        $out = $page->getPdfPages($pon);

        $this->assertStringContainsString('/MediaBox [0.000000 0.000000 595.276000 300.000000]', $out);
        $this->assertStringContainsString('/CropBox [0.000000 0.000000 595.276000 300.000000]', $out);

        foreach ($page->getPage(0)['box'] as $type => $box) {
            $this->bcAssertEqualsWithDelta($type === 'MediaBox' ? 300.0 : 841.89, $box['ury']);
        }
    }

    /**
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    public function testaddAnnotRef(): void
    {
        $testObj = $this->getTestObject();
        $testObj->add();
        $testObj->addAnnotRef(13);
        $testObj->addAnnotRef(17);

        $page = $testObj->getPage();
        $this->assertEquals(13, $page['annotrefs'][0] ?? null);
        $this->assertEquals(17, $page['annotrefs'][1] ?? null);
    }

    /**
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    public function testSetPagePHeight(): void
    {
        $testObj = $this->getTestObject();
        $testObj->add();
        $page = $testObj->getPage();
        $this->assertEquals(841, (int) $page['pheight']);
        $testObj->setPagePHeight(123.4);
        $page = $testObj->getPage();
        $this->assertEquals(123.4, $page['pheight']);
    }

    /**
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    public function testSetPagePWidth(): void
    {
        $testObj = $this->getTestObject();
        $testObj->add();
        $page = $testObj->getPage();
        $this->assertEquals(595, (int) $page['pwidth']);
        $testObj->setPagePWidth(123.4);
        $page = $testObj->getPage();
        $this->assertEquals(123.4, $page['pwidth']);
    }

    /**
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    public function testAddAnnotRefDuplicate(): void
    {
        $testObj = $this->getTestObject();
        $testObj->add();
        $testObj->addAnnotRef(42);
        $testObj->addAnnotRef(42);

        $page = $testObj->getPage();
        $this->assertCount(1, $page['annotrefs']);
        $this->assertEquals(42, $page['annotrefs'][0] ?? null);
    }

    /**
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    public function testPopContentEmptyEx(): void
    {
        $this->bcExpectException(\Com\Tecnick\Pdf\Page\Exception::class);
        $testObj = $this->getTestObject();
        $testObj->add();
        $testObj->popContent();
        $testObj->popContent();
    }

    /**
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    public function testPopContentToLastMarkEmpty(): void
    {
        $testObj = $this->getTestObject();
        $testObj->add();
        $testObj->popContent();
        $testObj->popContentToLastMark();
        $page = $testObj->getPage();
        $this->assertEmpty($page['content']);
    }

    /**
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    public function testSetPageTransparencyReturnsStatic(): void
    {
        $page = $this->getTestObject();
        $page->add();
        $this->assertSame($page, $page->setPageTransparency(true, 0));
        $this->assertSame($page, $page->setPageTransparency(false, 0));
        // -1 targets the current page, mirroring the rest of the page API.
        $this->assertSame($page, $page->setPageTransparency(false, -1));
    }

    /**
     * setPageTransparency() validates the page index like every other
     * page-targeting method.
     *
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    public function testSetPageTransparencyInvalidPageThrows(): void
    {
        $this->bcExpectException(\Com\Tecnick\Pdf\Page\Exception::class);
        $page = $this->getTestObject();
        $page->add();
        $page->setPageTransparency(false, 99);
    }

    /**
     * The transparency mode is matched case-insensitively.
     *
     * @throws \Com\Tecnick\Pdf\Page\Exception
     * @throws \Com\Tecnick\Pdf\Encrypt\Exception
     */
    public function testSetPageTransparencyGroupModeIsCaseInsensitive(): void
    {
        $page = $this->getTestObject();
        $page->add();
        $page->add();
        $page->setPageTransparency(true, 0);
        $page->setPageTransparencyGroupMode('NEVER');

        $pon = 0;
        $out = $page->getPdfPages($pon);
        $this->assertStringNotContainsString(self::TRANSPARENCY_GROUP, $out);
    }

    /**
     * Per-page transparency flags stay aligned with the page they were set on
     * after a page is deleted and the stack is reindexed.
     *
     * @throws \Com\Tecnick\Pdf\Page\Exception
     * @throws \Com\Tecnick\Pdf\Encrypt\Exception
     */
    public function testSetPageTransparencyFollowsPageAfterDelete(): void
    {
        $page = $this->getTestObject();
        $page->add(); // 0
        $page->add(); // 1
        $page->add(); // 2
        // Flag the last page opaque, then remove the first page so it becomes index 1.
        $page->setPageTransparency(false, 2);
        $page->delete(0);

        $pon = 0;
        $out = $page->getPdfPages($pon);
        // Two pages remain; the opaque flag must still suppress exactly one group.
        $this->assertEquals(1, substr_count($out, self::TRANSPARENCY_GROUP));
    }

    /**
     * Deleting a page keeps the current-page pointer valid.
     *
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    public function testDeleteKeepsCurrentPageValid(): void
    {
        $page = $this->getTestObject();
        $page->add(); // 0
        $page->add(); // 1
        $page->add(); // 2 (current)
        $page->delete(0);

        // The current-page pointer must stay within the reindexed stack so that
        // getPage() resolves instead of throwing "page ... do not exist".
        $this->assertGreaterThanOrEqual(0, $page->getPageID());
        $this->assertLessThanOrEqual(1, $page->getPageID());
        $current = $page->getPage();
        $this->assertArrayHasKey('time', $current);
    }

    /**
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    public function testSetPageTransparencyGroupModeReturnsStatic(): void
    {
        $page = $this->getTestObject();
        $this->assertSame($page, $page->setPageTransparencyGroupMode('auto'));
    }

    /**
     * In auto mode, pages that are never flagged emit the transparency group.
     *
     * @throws \Com\Tecnick\Pdf\Page\Exception
     * @throws \Com\Tecnick\Pdf\Encrypt\Exception
     */
    public function testGetPdfPagesTransparencyAutoDefault(): void
    {
        $page = $this->getTestObject();
        $page->add();
        $page->add();

        $pon = 0;
        $out = $page->getPdfPages($pon);
        $this->assertEquals(2, substr_count($out, self::TRANSPARENCY_GROUP));
    }

    /**
     * In auto mode, a page flagged as not using transparency omits the group,
     * while unflagged pages keep emitting it.
     *
     * @throws \Com\Tecnick\Pdf\Page\Exception
     * @throws \Com\Tecnick\Pdf\Encrypt\Exception
     */
    public function testGetPdfPagesTransparencyAutoFlaggedFalse(): void
    {
        $page = $this->getTestObject();
        $page->add();
        $page->add();
        $page->setPageTransparency(false, 0);

        $pon = 0;
        $out = $page->getPdfPages($pon);
        // page 0 omits the group, page 1 (unflagged) keeps it.
        $this->assertEquals(1, substr_count($out, self::TRANSPARENCY_GROUP));
    }

    /**
     * In auto mode, a page explicitly flagged as using transparency emits the
     * group.
     *
     * @throws \Com\Tecnick\Pdf\Page\Exception
     * @throws \Com\Tecnick\Pdf\Encrypt\Exception
     */
    public function testGetPdfPagesTransparencyAutoFlaggedTrue(): void
    {
        $page = $this->getTestObject();
        $page->add();
        $page->add();
        $page->setPageTransparency(false, 0);
        $page->setPageTransparency(true, 1);

        $pon = 0;
        $out = $page->getPdfPages($pon);
        $this->assertEquals(1, substr_count($out, self::TRANSPARENCY_GROUP));
    }

    /**
     * The 'always' mode emits the group on every standard page, even those
     * flagged as not using transparency.
     *
     * @throws \Com\Tecnick\Pdf\Page\Exception
     * @throws \Com\Tecnick\Pdf\Encrypt\Exception
     */
    public function testGetPdfPagesTransparencyAlways(): void
    {
        $page = $this->getTestObject();
        $page->add();
        $page->add();
        $page->setPageTransparency(false, 0);
        $page->setPageTransparency(false, 1);
        $page->setPageTransparencyGroupMode('always');

        $pon = 0;
        $out = $page->getPdfPages($pon);
        $this->assertEquals(2, substr_count($out, self::TRANSPARENCY_GROUP));
    }

    /**
     * The 'never' mode never emits the group, even on pages flagged as using
     * transparency.
     *
     * @throws \Com\Tecnick\Pdf\Page\Exception
     * @throws \Com\Tecnick\Pdf\Encrypt\Exception
     */
    public function testGetPdfPagesTransparencyNever(): void
    {
        $page = $this->getTestObject();
        $page->add();
        $page->add();
        $page->setPageTransparency(true, 0);
        $page->setPageTransparencyGroupMode('never');

        $pon = 0;
        $out = $page->getPdfPages($pon);
        $this->assertStringNotContainsString(self::TRANSPARENCY_GROUP, $out);
    }

    /**
     * Unknown modes are treated as 'auto'.
     *
     * @throws \Com\Tecnick\Pdf\Page\Exception
     * @throws \Com\Tecnick\Pdf\Encrypt\Exception
     */
    public function testGetPdfPagesTransparencyUnknownModeIsAuto(): void
    {
        $page = $this->getTestObject();
        $page->add();
        $page->add();
        $page->setPageTransparency(false, 0);
        $page->setPageTransparencyGroupMode('bogus');

        $pon = 0;
        $out = $page->getPdfPages($pon);
        // behaves like auto: page 0 omits, page 1 keeps the group.
        $this->assertEquals(1, substr_count($out, self::TRANSPARENCY_GROUP));
    }

    /**
     * A document whose conformance mode forbids transparency never emits the
     * transparency group, regardless of the configured mode or per-page flags.
     *
     * @throws \Com\Tecnick\Pdf\Page\Exception
     * @throws \Com\Tecnick\Pdf\Encrypt\Exception
     */
    public function testGetPdfPagesTransparencyWithoutTransparency(): void
    {
        $page = $this->getNoTransparencyTestObject();
        $page->add();
        $page->add();
        $page->setPageTransparency(true, 0);
        $page->setPageTransparencyGroupMode('always');

        $pon = 0;
        $out = $page->getPdfPages($pon);
        $this->assertStringNotContainsString(self::TRANSPARENCY_GROUP, $out);
    }

    /**
     * Numeric transition entries (/D, /Di, /SS) must be emitted as PDF numbers,
     * not as name objects, while name entries (/S) stay names.
     *
     * @throws \Com\Tecnick\Pdf\Page\Exception
     * @throws \Com\Tecnick\Pdf\Encrypt\Exception
     */
    public function testGetPdfPagesTransitionNumericEntries(): void
    {
        $page = $this->getTestObject();
        $page->add([
            'transition' => [
                'Dur' => 2,
                'D' => 3,
                'S' => 'Fly',
                'Di' => 270,
                'SS' => 1.3,
                'B' => true,
            ],
        ]);
        $page->addContent('TEST');

        $pon = 0;
        $out = $page->getPdfPages($pon);

        $this->bcAssertStringContainsString('/D 3' . "\n", $out);
        $this->bcAssertStringContainsString('/Di 270' . "\n", $out);
        $this->bcAssertStringContainsString('/SS 1.300000' . "\n", $out);
        $this->bcAssertStringContainsString('/S /Fly' . "\n", $out);
        $this->bcAssertStringContainsString('/B true' . "\n", $out);

        // The numeric keys are not rendered as name objects.
        $this->assertStringNotContainsString('/D /3', $out);
        $this->assertStringNotContainsString('/Di /270', $out);
        $this->assertStringNotContainsString('/SS /', $out);
    }

    /**
     * A page built from a partially-filled box must still emit complete /MediaBox
     * and /BoxColorInfo entries, without reading any missing key.
     *
     * @throws \Com\Tecnick\Pdf\Page\Exception
     * @throws \Com\Tecnick\Pdf\Encrypt\Exception
     */
    public function testGetPdfPagesWithPartialBoxData(): void
    {
        $page = $this->getTestObject();
        $page->add([
            'format' => 'MediaBox',
            'box' => [
                'MediaBox' => [
                    'urx' => 800.0,
                    'ury' => 400.0,
                ],
            ],
        ]);
        $page->addContent('TEST');

        $pon = 0;
        $out = $page->getPdfPages($pon);

        $this->bcAssertStringContainsString('/MediaBox [0.000000 0.000000 800.000000 400.000000]', $out);
        $this->assertSame(5, \substr_count($out, '/C [0.000000 0.000000 0.000000]' . "\n"));
        $this->assertStringNotContainsString('<<' . "\n" . '>>', $out);
    }

    /**
     * The /Di direction may legitimately be the name /None (Fly transitions).
     *
     * @throws \Com\Tecnick\Pdf\Page\Exception
     * @throws \Com\Tecnick\Pdf\Encrypt\Exception
     */
    public function testGetPdfPagesTransitionDiNoneIsName(): void
    {
        $page = $this->getTestObject();
        $page->add([
            'transition' => [
                'S' => 'Fly',
                'Di' => 'None',
                'SS' => 0.5,
            ],
        ]);
        $page->addContent('TEST');

        $pon = 0;
        $out = $page->getPdfPages($pon);

        $this->bcAssertStringContainsString('/S /Fly' . "\n", $out);
        $this->bcAssertStringContainsString('/Di /None' . "\n", $out);
        $this->bcAssertStringContainsString('/SS 0.500000' . "\n", $out);
    }

    /**
     * delete() reindexes the stack, so the embedded 'pid' of every remaining page
     * must keep matching its array index.
     *
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    public function testDeleteReindexesPageIds(): void
    {
        $page = $this->getTestObject();
        $page->add();
        $page->add();
        $page->add();
        $page->delete(0);

        foreach ($page->getPages() as $idx => $data) {
            $this->assertSame($idx, $data['pid']);
        }
    }

    /**
     * move() reindexes the stack, so the embedded 'pid' of every page must keep
     * matching its array index.
     *
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    public function testMoveReindexesPageIds(): void
    {
        $page = $this->getTestObject();
        $page->add();
        $page->add();
        $page->add();
        $page->add();
        $page->move(3, 0);

        foreach ($page->getPages() as $idx => $data) {
            $this->assertSame($idx, $data['pid']);
        }
    }

    /**
     * getNextPage() must decide auto-break based on the queried page, not the
     * current-page pointer when they differ.
     *
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    public function testGetNextPageUsesQueriedPageAutoBreak(): void
    {
        $page = $this->getTestObject();
        $page->add();
        $page->add();
        $page->enableAutoPageBreak(true, 0);
        $page->enableAutoPageBreak(false, 1);

        // Current pointer is page 0 (auto-break on), but page 1 (auto-break off)
        // is queried: no new page must be appended.
        $page->setCurrentPage(0);
        $page->getNextRegion(1);

        $this->assertCount(2, $page->getPages());
    }

    /**
     * /LastModified must be encrypted with the key of the page object that carries it.
     *
     * @throws \Com\Tecnick\Pdf\Page\Exception
     * @throws \Com\Tecnick\Pdf\Encrypt\Exception
     */
    public function testLastModifiedIsEncryptedWithThePageObjectNumber(): void
    {
        $encrypt = new \Com\Tecnick\Pdf\Encrypt\Encrypt(true, md5('file_id'), 3, ['print']);
        $page = new \Com\Tecnick\Pdf\Page\Page('mm', new \Com\Tecnick\Color\Pdf(), $encrypt, false, true, false);
        $page->add();

        $pon = 0;
        $out = $page->getPdfPages($pon);

        $matches = [];
        $this->assertSame(1, preg_match(
            '/(\d+) 0 obj\n<<\n\/Type \/Page\n.*?\/LastModified \((.*?)\)\n/s',
            $out,
            $matches,
        ));

        $decrypt = new \Com\Tecnick\Pdf\Encrypt\Decrypt($encrypt->getEncryptionData());
        $this->assertTrue($decrypt->authenticate(''));
        $this->assertStringStartsWith('D:', $decrypt->decryptString(
            stripcslashes($matches[2] ?? ''),
            (int) ($matches[1] ?? 0),
        ));
    }

    /**
     * A content stream declaring /FlateDecode must carry deflated data.
     *
     * @throws \Com\Tecnick\Pdf\Page\Exception
     * @throws \Com\Tecnick\Pdf\Encrypt\Exception
     */
    public function testContentStreamFilterMatchesTheStreamData(): void
    {
        $page = $this->getTestObject();
        $page->add();
        $page->addContent('TEST CONTENT');

        $pon = 0;
        $out = $page->getPdfPages($pon);

        $matches = [];
        $this->assertSame(1, preg_match(
            '/<< \/Filter \/FlateDecode \/Length (\d+) >>\nstream\n(.*)\nendstream/s',
            $out,
            $matches,
        ));
        $this->assertSame((int) ($matches[1] ?? 0), strlen($matches[2] ?? ''));
        $this->assertSame("\nTEST CONTENT", gzuncompress($matches[2] ?? ''));
    }

    /**
     * /Dur makes a conforming reader auto-advance the page, so it must be emitted
     * only when a positive display duration was requested.
     *
     * @throws \Com\Tecnick\Pdf\Page\Exception
     * @throws \Com\Tecnick\Pdf\Encrypt\Exception
     */
    public function testGetPdfPagesTransitionOmitsUnsetDuration(): void
    {
        $page = $this->getTestObject();
        $page->add([
            'transition' => [
                'S' => 'Fade',
            ],
        ]);

        $pon = 0;
        $out = $page->getPdfPages($pon);

        $this->bcAssertStringContainsString('/S /Fade' . "\n", $out);
        $this->assertStringNotContainsString('/Dur', $out);

        $page = $this->getTestObject();
        $page->add([
            'transition' => [
                'S' => 'Fade',
                'Dur' => 2.5,
            ],
        ]);

        $pon = 0;
        $this->bcAssertStringContainsString('/Dur 2.500000' . "\n", $page->getPdfPages($pon));
    }

    /**
     * The page number is derived from the position of the page in its group, so it
     * must be recomputed after the stack is reordered or a page is cloned.
     *
     * @throws \Com\Tecnick\Pdf\Page\Exception
     * @throws \Com\Tecnick\Pdf\Encrypt\Exception
     */
    public function testPageNumberIsRecomputedAfterStackChanges(): void
    {
        $page = $this->getTestObject();
        $page->add();
        $page->add();
        $page->add();

        $pon = 0;
        $page->getPdfPages($pon);
        $this->assertSame([1, 2, 3], array_column($page->getPages(), 'num'));

        // A cloned page must not reuse the number of the page it was cloned from.
        $page->add();
        $pon = 0;
        $page->getPdfPages($pon);
        $this->assertSame([1, 2, 3, 4], array_column($page->getPages(), 'num'));

        $page->move(3, 0);
        $pon = 0;
        $page->getPdfPages($pon);
        $this->assertSame([1, 2, 3, 4], array_column($page->getPages(), 'num'));

        $page->delete(0);
        $pon = 0;
        $page->getPdfPages($pon);
        $this->assertSame([1, 2, 3], array_column($page->getPages(), 'num'));
    }

    /**
     * A caller-supplied page number survives the recomputation.
     *
     * @throws \Com\Tecnick\Pdf\Page\Exception
     * @throws \Com\Tecnick\Pdf\Encrypt\Exception
     */
    public function testExplicitPageNumberOverridesTheComputedOne(): void
    {
        $page = $this->getTestObject();
        $page->add([
            'num' => 7,
        ]);
        $page->addContent(\Com\Tecnick\Pdf\Page\Settings::PAGE_NUM);

        $pon = 0;
        $page->getPdfPages($pon);
        $this->assertSame(7, $page->getPage(0)['num']);

        // A page cloned from it gets its own number, continuing the group sequence.
        $page->add();
        $pon = 0;
        $page->getPdfPages($pon);
        $this->assertSame([7, 8], array_column($page->getPages(), 'num'));
    }

    /**
     * /SS and /B belong to the Fly style only, and /SS must be greater than 0.
     *
     * @throws \Com\Tecnick\Pdf\Page\Exception
     * @throws \Com\Tecnick\Pdf\Encrypt\Exception
     */
    public function testGetPdfPagesTransitionFlyOnlyEntries(): void
    {
        $page = $this->getTestObject();
        $page->add([
            'transition' => [
                'S' => 'Wipe',
                'SS' => 3.0,
                'B' => true,
            ],
        ]);
        $page->add([
            'transition' => [
                'S' => 'Fly',
                'Di' => 'None',
                'SS' => -2.5,
            ],
        ]);

        $pon = 0;
        $out = $page->getPdfPages($pon);

        $this->bcAssertStringContainsString('/S /Wipe' . "\n", $out);
        $this->bcAssertStringContainsString('/S /Fly' . "\n", $out);
        $this->assertStringNotContainsString('/SS', $out);
        $this->assertSame(1, \substr_count($out, '/B '));
    }

    /**
     * Every entry the PageData shape declares is present from the moment the page is
     * created, so a caller reading a page through it never hits a missing key.
     *
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    public function testAddSeedsDeclaredPageDataKeys(): void
    {
        $page = $this->getTestObject();
        $data = $page->add([
            'format' => 'A4',
        ]);

        $declared = [
            'annotrefs',
            'autobreak',
            'box',
            'columns',
            'content',
            'content_mark',
            'ContentHeight',
            'ContentWidth',
            'currentRegion',
            'FooterHeight',
            'format',
            'group',
            'HeaderHeight',
            'height',
            'margin',
            'n',
            'num',
            'orientation',
            'pagenum',
            'pheight',
            'pid',
            'pwidth',
            'region',
            'rotation',
            'time',
            'width',
            'zoom',
        ];

        $this->assertSame([], \array_values(\array_diff($declared, \array_keys($data))));
        $this->assertSame([], \array_values(\array_diff(\array_keys($data), $declared)));
        $this->assertSame(0, $data['n']);
        $this->assertArrayHasKey('booklet', $data['margin']);
    }

    /**
     * ISO 15930 requires a page to carry a trim box or an art box, but not both,
     * so a producer must be able to drop one of them.
     *
     * @throws \Com\Tecnick\Pdf\Page\Exception
     * @throws \Com\Tecnick\Pdf\Encrypt\Exception
     */
    public function testOmitPageBoxRemovesItFromThePageDictionary(): void
    {
        $page = $this->getTestObject();
        $page->omitPageBox('ArtBox');
        $page->add();

        $pon = 0;
        $out = $page->getPdfPages($pon);

        $this->assertStringNotContainsString('/ArtBox', $out);
        $this->assertStringContainsString('/TrimBox', $out);
        $this->assertStringContainsString('/MediaBox', $out);
    }

    /**
     * @throws \Com\Tecnick\Pdf\Page\Exception
     * @throws \Com\Tecnick\Pdf\Encrypt\Exception
     */
    public function testKeepPageBoxRestoresIt(): void
    {
        $page = $this->getTestObject();
        $page->omitPageBox(\Com\Tecnick\Pdf\Page\PageBoxType::ArtBox);
        $page->keepPageBox(\Com\Tecnick\Pdf\Page\PageBoxType::ArtBox);
        $page->add();

        $pon = 0;
        $out = $page->getPdfPages($pon);

        $this->assertStringContainsString('/ArtBox', $out);
    }

    /**
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    public function testOmitPageBoxRefusesTheMediaBox(): void
    {
        $page = $this->getTestObject();
        $this->bcExpectException(\Com\Tecnick\Pdf\Page\Exception::class);
        $page->omitPageBox('MediaBox');
    }

    /**
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    public function testOmitPageBoxRefusesAnUnknownBox(): void
    {
        $page = $this->getTestObject();
        $this->bcExpectException(\Com\Tecnick\Pdf\Page\Exception::class);
        $page->omitPageBox('FooBox');
    }
}
