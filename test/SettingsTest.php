<?php

/**
 * SettingsTest.php
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
 * Settings class test
 *
 * @since     2011-05-23
 * @category  Library
 * @package   PdfPage
 * @author    Nicola Asuni <info@tecnick.com>
 * @copyright 2011-2026 Nicola Asuni - Tecnick.com LTD
 * @license   https://www.gnu.org/copyleft/lesser.html GNU-LGPL v3 (see LICENSE)
 * @link      https://github.com/tecnickcom/tc-lib-pdf-page
 */
class SettingsTest extends TestUtil
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
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    public function testGetPageID(): void
    {
        $page = $this->getTestObject();

        $pid = $page->getPageID();
        $this->assertEquals(-1, $pid);
    }

    /**
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    public function testSanitizePageNumber(): void
    {
        $page = $this->getTestObject();
        $data = [];
        $page->sanitizePageNumber($data);
        $this->assertEquals([], $data);

        $data = [
            'num' => -1,
        ];
        $page->sanitizePageNumber($data);
        $this->assertEquals(
            [
                'num' => 0,
            ],
            $data,
        );

        $data = [
            'num' => 0,
        ];
        $page->sanitizePageNumber($data);
        $this->assertEquals(
            [
                'num' => 0,
            ],
            $data,
        );

        $data = [
            'num' => 1,
        ];
        $page->sanitizePageNumber($data);
        $this->assertEquals(
            [
                'num' => 1,
            ],
            $data,
        );
    }

    /**
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    public function testSanitizeTime(): void
    {
        $page = $this->getTestObject();
        $data = [];
        $page->sanitizeTime($data);
        $this->assertArrayHasKey('time', $data);
        $this->assertNotEmpty($data['time'] ?? null);

        $data = [
            'time' => -1,
        ];
        $page->sanitizeTime($data);
        $this->assertEquals(
            [
                'time' => 0,
            ],
            $data,
        );

        $data = [
            'time' => 0,
        ];
        $page->sanitizeTime($data);
        $this->assertArrayHasKey('time', $data);
        $this->assertNotEmpty($data['time'] ?? null);

        $data = [
            'time' => 1,
        ];
        $page->sanitizeTime($data);
        $this->assertEquals(
            [
                'time' => 1,
            ],
            $data,
        );
    }

    /**
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    public function testSanitizeGroup(): void
    {
        $page = $this->getTestObject();
        $data = [];
        $page->sanitizeGroup($data);
        $this->assertEquals(
            [
                'group' => 0,
            ],
            $data,
        );

        $data = [
            'group' => -1,
        ];
        $page->sanitizeGroup($data);
        $this->assertEquals(
            [
                'group' => 0,
            ],
            $data,
        );

        $data = [
            'group' => 0,
        ];
        $page->sanitizeGroup($data);
        $this->assertEquals(
            [
                'group' => 0,
            ],
            $data,
        );

        $data = [
            'group' => 1,
        ];
        $page->sanitizeGroup($data);
        $this->assertEquals(
            [
                'group' => 1,
            ],
            $data,
        );
    }

    /**
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    public function testSanitizeContent(): void
    {
        $page = $this->getTestObject();
        $data = [];
        $page->sanitizeContent($data);
        $this->assertEquals(
            [
                'content' => [''],
            ],
            $data,
        );

        $data = [
            'content' => 'test',
        ];
        $page->sanitizeContent($data); // @phpstan-ignore argument.type
        $this->assertEquals(
            [
                'content' => ['test'],
            ],
            $data,
        );

        $data = [
            'content' => [
                2 => 'a',
                5 => 'b',
            ],
        ];
        $page->sanitizeContent($data);
        $this->assertEquals(
            [
                'content' => ['a', 'b'],
            ],
            $data,
        );
    }

    /**
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    public function testSanitizeAnnotRefs(): void
    {
        $page = $this->getTestObject();
        $data = [];
        $page->sanitizeAnnotRefs($data);
        $this->assertEquals(
            [
                'annotrefs' => [],
            ],
            $data,
        );
    }

    /**
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    public function testSanitizeRotation(): void
    {
        $page = $this->getTestObject();
        $data = [];
        $page->sanitizeRotation($data);
        $this->assertEquals(
            [
                'rotation' => 0,
            ],
            $data,
        );

        $data = [
            'rotation' => 0,
        ];
        $page->sanitizeRotation($data);
        $this->assertEquals(
            [
                'rotation' => 0,
            ],
            $data,
        );

        $data = [
            'rotation' => 100,
        ];
        $page->sanitizeRotation($data);
        $this->assertEquals(
            [
                'rotation' => 0,
            ],
            $data,
        );

        $data = [
            'rotation' => 90,
        ];
        $page->sanitizeRotation($data);
        $this->assertEquals(
            [
                'rotation' => 90,
            ],
            $data,
        );

        $data = [
            'rotation' => 180,
        ];
        $page->sanitizeRotation($data);
        $this->assertEquals(
            [
                'rotation' => 180,
            ],
            $data,
        );

        $data = [
            'rotation' => 270,
        ];
        $page->sanitizeRotation($data);
        $this->assertEquals(
            [
                'rotation' => 270,
            ],
            $data,
        );

        // Multiples of 90 outside [0, 360) are normalized into the range.
        $data = [
            'rotation' => 360,
        ];
        $page->sanitizeRotation($data);
        $this->assertEquals(
            [
                'rotation' => 0,
            ],
            $data,
        );

        $data = [
            'rotation' => 450,
        ];
        $page->sanitizeRotation($data);
        $this->assertEquals(
            [
                'rotation' => 90,
            ],
            $data,
        );

        $data = [
            'rotation' => -90,
        ];
        $page->sanitizeRotation($data);
        $this->assertEquals(
            [
                'rotation' => 270,
            ],
            $data,
        );
    }

    /**
     * The preferred zoom is a magnification factor, so a non-positive value falls
     * back to 1.0.
     *
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    public function testSanitizeZoomRejectsNonPositiveValues(): void
    {
        $page = $this->getTestObject();

        foreach ([0.0, -5.0] as $zoom) {
            $data = [
                'zoom' => $zoom,
            ];
            $page->sanitizeZoom($data);
            $this->bcAssertEqualsWithDelta(1.0, $data['zoom'] ?? null);
        }

        $data = [
            'zoom' => 2.5,
        ];
        $page->sanitizeZoom($data);
        $this->bcAssertEqualsWithDelta(2.5, $data['zoom'] ?? null);
    }

    /**
     * The default page size is applied whenever the dimensions are missing, also
     * when margins are given.
     *
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    public function testSanitizeMarginsAppliesTheDefaultPageSize(): void
    {
        $page = $this->getTestObject();
        $data = [
            'margin' => [
                'PL' => 10.0,
                'PR' => 10.0,
            ],
        ];
        $page->sanitizeMargins($data);

        $this->bcAssertEqualsWithDelta(210, $data['width'] ?? null);
        $this->bcAssertEqualsWithDelta(297, $data['height'] ?? null);
        $this->bcAssertEqualsWithDelta(190, $data['ContentWidth'] ?? null);
        $this->bcAssertEqualsWithDelta(297, $data['ContentHeight'] ?? null);
        $this->bcAssertEqualsWithDelta([
            'booklet' => false,
            'CB' => 0,
            'CT' => 0,
            'FT' => 0,
            'HB' => 0,
            'PB' => 0,
            'PL' => 10,
            'PR' => 10,
            'PT' => 0,
        ], $data['margin'] ?? null);
    }

    /**
     * A region larger than the page is clamped without producing negative
     * coordinates, and the derived edges stay consistent with the clamped rectangle.
     *
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    public function testSanitizeRegionsClampsOversizedRegions(): void
    {
        $page = $this->getTestObject();
        $res = $page->add([
            'format' => 'A4',
            'margin' => [
                'PL' => 10,
                'PR' => 10,
                'PT' => 10,
                'PB' => 10,
            ],
            'region' => [[
                'RX' => 10,
                'RY' => 10,
                'RW' => 1000,
                'RH' => 1000,
            ]],
        ]);

        // The region is clamped to the content area; RL = RX + RW and RT = RY + RH,
        // and no coordinate goes negative.
        $this->bcAssertEqualsWithDelta([[
            'RW' => 190,
            'RX' => 10,
            'RL' => 200,
            'RR' => 10,
            'RH' => 277,
            'RY' => 10,
            'RT' => 287,
            'RB' => 10,
            'x' => 10,
            'y' => 10,
        ]], $res['region']);
    }

    /**
     * The number of equal vertical columns is capped.
     *
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    public function testSanitizeRegionsCapsTheColumnCount(): void
    {
        $page = $this->getTestObject();
        $res = $page->add([
            'format' => 'A4',
            'columns' => 500000,
        ]);

        $this->assertSame(\Com\Tecnick\Pdf\Page\Settings::MAX_COLUMNS, $res['columns']);
    }

    /**
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    public function testSanitizeZoom(): void
    {
        $page = $this->getTestObject();
        $data = [];
        $page->sanitizeZoom($data);
        $this->assertEquals(
            [
                'zoom' => 1,
            ],
            $data,
        );

        $data = [
            'zoom' => 1.2,
        ];
        $page->sanitizeZoom($data);
        $this->assertEquals(
            [
                'zoom' => 1.2,
            ],
            $data,
        );
    }

    /**
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    public function testSanitizeTransitions(): void
    {
        $page = $this->getTestObject();
        $data = [];
        $page->sanitizeTransitions($data);
        $this->assertEquals([], $data);

        $data = [
            'transition' => [
                'Dur' => 0,
            ],
        ];
        $page->sanitizeTransitions($data);
        $exp = [
            'transition' => [
                'S' => 'R',
                'D' => 1,
            ],
        ];
        $this->assertEquals($exp, $data);

        $data = [
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
        ];
        $page->sanitizeTransitions($data);
        $exp = [
            'transition' => [
                'Dur' => 2,
                'D' => 3,
                'S' => 'Glitter',
                'Di' => 315,
            ],
        ];
        $this->assertEquals($exp, $data);
    }

    /**
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    public function testSanitizeMargins(): void
    {
        $page = $this->getTestObject();
        $data = [];
        $page->sanitizeMargins($data);
        $exp = [
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
            'orientation' => 'P',
            'height' => 297,
            'width' => 210,
            'ContentWidth' => 210,
            'ContentHeight' => 297,
            'HeaderHeight' => 0,
            'FooterHeight' => 0,
        ];
        $this->bcAssertEqualsWithDelta($exp, $data);

        $data = [
            'margin' => [
                'booklet' => false,
                'PL' => 11,
                'PR' => 12,
                'PT' => 13,
                'HB' => 14,
                'CT' => 15,
                'CB' => 15,
                'FT' => 13,
                'PB' => 11,
            ],
            'orientation' => 'P',
            'height' => 297,
            'width' => 210,
        ];
        $page->sanitizeMargins($data);
        $exp = [
            'margin' => [
                'booklet' => false,
                'PL' => 11,
                'PR' => 12,
                'PT' => 13,
                'HB' => 14,
                'CT' => 15,
                'CB' => 15,
                'FT' => 13,
                'PB' => 11,
            ],
            'orientation' => 'P',
            'height' => 297,
            'width' => 210,
            'ContentWidth' => 187,
            'ContentHeight' => 267,
            'HeaderHeight' => 1,
            'FooterHeight' => 2,
        ];
        $this->bcAssertEqualsWithDelta($exp, $data);
    }

    /**
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    public function testSanitizeBoxData(): void
    {
        $page = $this->getTestObject();
        $data = [];
        $page->sanitizeBoxData($data);
        $exp = [
            'orientation' => 'P',
            'pheight' => 841.890,
            'pwidth' => 595.276,
            'box' => [
                'MediaBox' => [
                    'llx' => 0,
                    'lly' => 0,
                    'urx' => 595.276,
                    'ury' => 841.890,
                    'bci' => [
                        'color' => '#000000',
                        'width' => 0.353,
                        'style' => 'S',
                        'dash' => [
                            0 => 3,
                        ],
                    ],
                ],
                'CropBox' => [
                    'llx' => 0,
                    'lly' => 0,
                    'urx' => 595.276,
                    'ury' => 841.890,
                    'bci' => [
                        'color' => '#000000',
                        'width' => 0.353,
                        'style' => 'S',
                        'dash' => [
                            0 => 3,
                        ],
                    ],
                ],
                'BleedBox' => [
                    'llx' => 0,
                    'lly' => 0,
                    'urx' => 595.276,
                    'ury' => 841.890,
                    'bci' => [
                        'color' => '#000000',
                        'width' => 0.353,
                        'style' => 'S',
                        'dash' => [
                            0 => 3,
                        ],
                    ],
                ],
                'TrimBox' => [
                    'llx' => 0,
                    'lly' => 0,
                    'urx' => 595.276,
                    'ury' => 841.890,
                    'bci' => [
                        'color' => '#000000',
                        'width' => 0.353,
                        'style' => 'S',
                        'dash' => [
                            0 => 3,
                        ],
                    ],
                ],
                'ArtBox' => [
                    'llx' => 0,
                    'lly' => 0,
                    'urx' => 595.276,
                    'ury' => 841.890,
                    'bci' => [
                        'color' => '#000000',
                        'width' => 0.353,
                        'style' => 'S',
                        'dash' => [
                            0 => 3,
                        ],
                    ],
                ],
            ],
        ];
        $this->bcAssertEqualsWithDelta($exp, $data);

        $data = [
            'format' => 'MediaBox',
            'orientation' => 'L',
            'box' => [
                'MediaBox' => [
                    'llx' => 0,
                    'lly' => 0,
                    'urx' => 595.276,
                    'ury' => 841.890,
                    'bci' => [
                        'color' => '#000000',
                        'width' => 0.353,
                        'style' => 'S',
                        'dash' => [
                            0 => 3,
                        ],
                    ],
                ],
            ],
        ];
        $page->sanitizeBoxData($data);
        $exp = [
            'format' => 'CUSTOM',
            'orientation' => 'L',
            'box' => [
                'MediaBox' => [
                    'llx' => 0,
                    'lly' => 0,
                    'urx' => 841.890,
                    'ury' => 595.276,
                    'bci' => [
                        'color' => '#000000',
                        'width' => 0.353,
                        'style' => 'S',
                        'dash' => [
                            0 => 3,
                        ],
                    ],
                ],
                'CropBox' => [
                    'llx' => 0,
                    'lly' => 0,
                    'urx' => 841.890,
                    'ury' => 595.276,
                    'bci' => [
                        'color' => '#000000',
                        'width' => 0.353,
                        'style' => 'S',
                        'dash' => [
                            0 => 3,
                        ],
                    ],
                ],
                'BleedBox' => [
                    'llx' => 0,
                    'lly' => 0,
                    'urx' => 841.890,
                    'ury' => 595.276,
                    'bci' => [
                        'color' => '#000000',
                        'width' => 0.353,
                        'style' => 'S',
                        'dash' => [
                            0 => 3,
                        ],
                    ],
                ],
                'TrimBox' => [
                    'llx' => 0,
                    'lly' => 0,
                    'urx' => 841.890,
                    'ury' => 595.276,
                    'bci' => [
                        'color' => '#000000',
                        'width' => 0.353,
                        'style' => 'S',
                        'dash' => [
                            0 => 3,
                        ],
                    ],
                ],
                'ArtBox' => [
                    'llx' => 0,
                    'lly' => 0,
                    'urx' => 841.890,
                    'ury' => 595.276,
                    'bci' => [
                        'color' => '#000000',
                        'width' => 0.353,
                        'style' => 'S',
                        'dash' => [
                            0 => 3,
                        ],
                    ],
                ],
            ],
            'width' => 297,
            'height' => 210,
            'pwidth' => 841.890,
            'pheight' => 595.276,
        ];
        $this->bcAssertEqualsWithDelta($exp, $data);

        $data = [
            'width' => 210,
            'height' => 297,
            'pwidth' => 595.276,
            'pheight' => 841.890,
            'box' => [
                'CropBox' => [
                    'llx' => 0,
                    'lly' => 0,
                    'urx' => 595.276,
                    'ury' => 841.890,
                    'bci' => [
                        'color' => '#000000',
                        'width' => 0.353,
                        'style' => 'S',
                        'dash' => [
                            0 => 3,
                        ],
                    ],
                ],
            ],
        ];
        $page->sanitizeBoxData($data);
        $exp = [
            'width' => 210,
            'height' => 297,
            'pwidth' => 595.276,
            'pheight' => 841.890,
            'box' => [
                'CropBox' => [
                    'llx' => 0,
                    'lly' => 0,
                    'urx' => 595.276,
                    'ury' => 841.890,
                    'bci' => [
                        'color' => '#000000',
                        'width' => 0.353,
                        'style' => 'S',
                        'dash' => [
                            0 => 3,
                        ],
                    ],
                ],
                'MediaBox' => [
                    'llx' => 0,
                    'lly' => 0,
                    'urx' => 595.276,
                    'ury' => 841.890,
                    'bci' => [
                        'color' => '#000000',
                        'width' => 0.353,
                        'style' => 'S',
                        'dash' => [
                            0 => 3,
                        ],
                    ],
                ],
                'BleedBox' => [
                    'llx' => 0,
                    'lly' => 0,
                    'urx' => 595.276,
                    'ury' => 841.890,
                    'bci' => [
                        'color' => '#000000',
                        'width' => 0.353,
                        'style' => 'S',
                        'dash' => [
                            0 => 3,
                        ],
                    ],
                ],
                'TrimBox' => [
                    'llx' => 0,
                    'lly' => 0,
                    'urx' => 595.276,
                    'ury' => 841.890,
                    'bci' => [
                        'color' => '#000000',
                        'width' => 0.353,
                        'style' => 'S',
                        'dash' => [
                            0 => 3,
                        ],
                    ],
                ],
                'ArtBox' => [
                    'llx' => 0,
                    'lly' => 0,
                    'urx' => 595.276,
                    'ury' => 841.890,
                    'bci' => [
                        'color' => '#000000',
                        'width' => 0.353,
                        'style' => 'S',
                        'dash' => [
                            0 => 3,
                        ],
                    ],
                ],
            ],
            'orientation' => 'P',
        ];
        $this->bcAssertEqualsWithDelta($exp, $data);
    }

    /**
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    public function testSanitizePageFormat(): void
    {
        $page = $this->getTestObject();
        $data = [];
        $page->sanitizePageFormat($data);
        $exp = [
            'orientation' => 'P',
            'format' => 'A4',
            'pheight' => 841.890,
            'pwidth' => 595.276,
            'width' => 210,
            'height' => 297,
        ];
        $this->bcAssertEqualsWithDelta($exp, $data);

        $data = [
            'box' => [
                'MediaBox' => [
                    'llx' => 0,
                    'lly' => 0,
                    'urx' => 595.276,
                    'ury' => 841.890,
                    'bci' => [
                        'color' => '#000000',
                        'width' => 0.353,
                        'style' => 'S',
                        'dash' => [
                            0 => 3,
                        ],
                    ],
                ],
            ],
        ];
        $page->sanitizePageFormat($data);
        $exp = [
            'box' => [
                'MediaBox' => [
                    'llx' => 0,
                    'lly' => 0,
                    'urx' => 595.276,
                    'ury' => 841.890,
                    'bci' => [
                        'color' => '#000000',
                        'width' => 0.353,
                        'style' => 'S',
                        'dash' => [
                            0 => 3,
                        ],
                    ],
                ],
            ],
            'orientation' => 'P',
            'format' => 'A4',
            'pwidth' => 595.276,
            'pheight' => 841.890,
            'width' => 210.000,
            'height' => 297.000,
        ];
        $this->bcAssertEqualsWithDelta($exp, $data);
    }

    /**
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    public function testSanitizeMarginsWithoutBookletKey(): void
    {
        $page = $this->getTestObject();
        $data = [
            'margin' => [
                'PL' => 10,
                'PR' => 5,
                'PT' => 0,
                'HB' => 0,
                'CT' => 0,
                'CB' => 0,
                'FT' => 0,
                'PB' => 0,
            ],
            'width' => 210,
            'height' => 297,
        ];
        $page->sanitizeMargins($data);
        $exp = [
            'margin' => [
                'booklet' => false,
                'PL' => 10,
                'PR' => 5,
                'PT' => 0,
                'HB' => 0,
                'CT' => 0,
                'CB' => 0,
                'FT' => 0,
                'PB' => 0,
            ],
            'width' => 210,
            'height' => 297,
            'ContentWidth' => 195,
            'ContentHeight' => 297,
            'HeaderHeight' => 0,
            'FooterHeight' => 0,
        ];
        $this->bcAssertEqualsWithDelta($exp, $data);
    }

    /**
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    public function testSanitizeMarginsBookletSwap(): void
    {
        $page = $this->getTestObject();
        $page->add();
        $data = [
            'margin' => [
                'booklet' => true,
                'PL' => 10,
                'PR' => 5,
                'PT' => 0,
                'HB' => 0,
                'CT' => 0,
                'CB' => 0,
                'FT' => 0,
                'PB' => 0,
            ],
            'width' => 210,
            'height' => 297,
        ];
        $page->sanitizeMargins($data);
        $exp = [
            'margin' => [
                'booklet' => true,
                'PL' => 5,
                'PR' => 10,
                'PT' => 0,
                'HB' => 0,
                'CT' => 0,
                'CB' => 0,
                'FT' => 0,
                'PB' => 0,
            ],
            'width' => 210,
            'height' => 297,
            'ContentWidth' => 195,
            'ContentHeight' => 297,
            'HeaderHeight' => 0,
            'FooterHeight' => 0,
        ];
        $this->bcAssertEqualsWithDelta($exp, $data);
    }

    /**
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    public function testSanitizeMarginsPartialInputPreservesBottomMargins(): void
    {
        $page = $this->getTestObject();
        $data = [
            'margin' => [
                'PL' => 10,
                'PR' => 12,
                'PT' => 13,
                'HB' => 14,
                'FT' => 13,
                'PB' => 11,
            ],
            'width' => 210,
            'height' => 297,
        ];

        $page->sanitizeMargins($data);
        $margin = is_array($data['margin'] ?? null) ? $data['margin'] : [];

        $this->assertSame(14.0, $margin['CT'] ?? null);
        $this->assertSame(13.0, $margin['CB'] ?? null);
        $this->assertSame(13.0, $margin['FT'] ?? null);
        $this->assertSame(11.0, $margin['PB'] ?? null);
        $this->assertSame(2.0, $data['FooterHeight'] ?? null);
    }

    /**
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    public function testSanitizeMarginsExplicitCtCbRemainAuthoritative(): void
    {
        $page = $this->getTestObject();
        $data = [
            'margin' => [
                'PL' => 10,
                'PR' => 12,
                'PT' => 13,
                'HB' => 14,
                'CT' => 15,
                'CB' => 16,
                'FT' => 13,
                'PB' => 11,
            ],
            'width' => 210,
            'height' => 297,
        ];

        $page->sanitizeMargins($data);
        $margin = is_array($data['margin'] ?? null) ? $data['margin'] : [];

        $this->assertSame(15.0, $margin['CT'] ?? null);
        $this->assertSame(16.0, $margin['CB'] ?? null);
        $this->assertSame(13.0, $margin['FT'] ?? null);
        $this->assertSame(11.0, $margin['PB'] ?? null);
        $this->assertSame(266.0, $data['ContentHeight'] ?? null);
    }

    /**
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    public function testSanitizeMarginsPathologicalValuesAreClampedSafely(): void
    {
        $page = $this->getTestObject();
        $data = [
            'margin' => [
                'PL' => -3,
                'PR' => 500,
                'PT' => -5,
                'HB' => 350,
                'FT' => 500,
                'PB' => -4,
            ],
            'width' => 210,
            'height' => 297,
        ];

        $page->sanitizeMargins($data);
        $margin = is_array($data['margin'] ?? null) ? $data['margin'] : [];

        $this->assertSame(0.0, $margin['PL'] ?? null);
        $this->assertSame(210.0, $margin['PR'] ?? null);
        $this->assertSame(0.0, $margin['PT'] ?? null);
        $this->assertSame(297.0, $margin['HB'] ?? null);
        $this->assertSame(297.0, $margin['CT'] ?? null);
        $this->assertSame(0.0, $margin['CB'] ?? null);
        $this->assertSame(0.0, $margin['FT'] ?? null);
        $this->assertSame(0.0, $margin['PB'] ?? null);
        $this->assertSame(0.0, $data['ContentWidth'] ?? null);
        $this->assertSame(0.0, $data['ContentHeight'] ?? null);
        $this->assertGreaterThanOrEqual(0, $data['FooterHeight'] ?? 0.0);
    }

    /**
     * The 'MediaBox' pseudo-format takes the page size from the supplied MediaBox
     * and must be usable through add(), not only through sanitizeBoxData().
     *
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    public function testAddAcceptsTheMediaBoxPseudoFormat(): void
    {
        $page = $this->getTestObject();
        $data = $page->add([
            'format' => 'MediaBox',
            'box' => [
                'MediaBox' => [
                    'llx' => 0.0,
                    'lly' => 0.0,
                    'urx' => 1000.0,
                    'ury' => 500.0,
                ],
            ],
        ]);

        $this->assertSame('CUSTOM', $data['format']);
        $this->assertSame('L', $data['orientation']);
        $this->bcAssertEqualsWithDelta(1000.0, $data['pwidth']);
        $this->bcAssertEqualsWithDelta(500.0, $data['pheight']);
        $mediabox = $data['box']['MediaBox'] ?? [];
        $this->bcAssertEqualsWithDelta(1000.0, $mediabox['urx'] ?? null);
        $this->bcAssertEqualsWithDelta(500.0, $mediabox['ury'] ?? null);
    }

    /**
     * The regions are addressed by an index in the [0, columns - 1] range, so a
     * caller-supplied array with arbitrary keys must be reindexed.
     *
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    public function testSanitizeRegionsReindexesTheRegionList(): void
    {
        $page = $this->getTestObject();
        $data = [
            'width' => 210.0,
            'height' => 297.0,
            'ContentWidth' => 190.0,
            'ContentHeight' => 277.0,
            'margin' => [
                'PL' => 10.0,
                'PR' => 10.0,
                'CT' => 10.0,
                'CB' => 10.0,
            ],
            'region' => [
                3 => [
                    'RX' => 10.0,
                    'RY' => 10.0,
                    'RW' => 80.0,
                    'RH' => 100.0,
                ],
                7 => [
                    'RX' => 100.0,
                    'RY' => 10.0,
                    'RW' => 80.0,
                    'RH' => 100.0,
                ],
            ],
        ];

        $page->sanitizeRegions($data);

        $this->assertSame([0, 1], array_keys($data['region'] ?? []));
        $this->assertSame(2, $data['columns'] ?? null);
        $this->bcAssertEqualsWithDelta(10.0, ($data['region'][0] ?? [])['RX'] ?? null);
        $this->bcAssertEqualsWithDelta(100.0, ($data['region'][1] ?? [])['RX'] ?? null);
    }

    /**
     * Called on its own, sanitizeRegions() derives the content area from the page
     * size and the margins instead of collapsing every region to zero.
     *
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    public function testSanitizeRegionsDerivesTheContentArea(): void
    {
        $page = $this->getTestObject();
        $data = [
            'width' => 210.0,
            'height' => 297.0,
            'margin' => [
                'PL' => 10.0,
                'PR' => 10.0,
                'CT' => 10.0,
                'CB' => 10.0,
            ],
            'region' => [
                [
                    'RX' => 10.0,
                    'RY' => 10.0,
                    'RW' => 100.0,
                    'RH' => 100.0,
                ],
            ],
        ];

        $page->sanitizeRegions($data);
        $region = $data['region'][0] ?? [];

        $this->bcAssertEqualsWithDelta(100.0, $region['RW'] ?? null);
        $this->bcAssertEqualsWithDelta(100.0, $region['RH'] ?? null);
        $this->bcAssertEqualsWithDelta(10.0, $region['RX'] ?? null);
        $this->bcAssertEqualsWithDelta(110.0, $region['RL'] ?? null);
    }

    /**
     * Every entry of a caller-supplied page box is optional, so the missing
     * coordinates and BoxColorInfo entries must be completed with the defaults.
     *
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    public function testSanitizeBoxDataCompletesPartialBoxes(): void
    {
        $page = $this->getTestObject();
        $data = [
            'format' => 'MediaBox',
            'box' => [
                'MediaBox' => [
                    'urx' => 800.0,
                    'ury' => 400.0,
                    'bci' => [
                        'color' => '#FF0000',
                    ],
                ],
            ],
        ];

        $page->sanitizeBoxData($data);
        $mediabox = $data['box']['MediaBox'] ?? [];

        $this->bcAssertEqualsWithDelta(0.0, $mediabox['llx'] ?? null);
        $this->bcAssertEqualsWithDelta(0.0, $mediabox['lly'] ?? null);
        $this->bcAssertEqualsWithDelta(800.0, $mediabox['urx'] ?? null);
        $this->bcAssertEqualsWithDelta(400.0, $mediabox['ury'] ?? null);
        $this->assertSame('#FF0000', $mediabox['bci']['color'] ?? null);
        $this->bcAssertEqualsWithDelta(0.353, $mediabox['bci']['width'] ?? null);
        $this->assertSame('S', $mediabox['bci']['style'] ?? null);
        $this->assertSame([3], $mediabox['bci']['dash'] ?? null);
    }

    /**
     * setBox() rejects an unknown page box name, so the same name must not be
     * accepted through the 'box' page data either.
     *
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    public function testSanitizeBoxDataRejectsUnknownBoxType(): void
    {
        $this->bcExpectException(\Com\Tecnick\Pdf\Page\Exception::class);
        $page = $this->getTestObject();
        $data = [
            'box' => [
                'Mediabox' => [
                    'llx' => 0.0,
                    'lly' => 0.0,
                    'urx' => 800.0,
                    'ury' => 400.0,
                ],
            ],
        ];
        $page->sanitizeBoxData($data);
    }

    /**
     * /Dur is kept only when it makes the reader auto-advance the page, and the
     * transition duration /D cannot be negative.
     *
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    public function testSanitizeTransitionsClampsDurationEntries(): void
    {
        $page = $this->getTestObject();
        $data = [
            'transition' => [
                'S' => 'Wipe',
                'D' => -5,
                'Dur' => -2.0,
            ],
        ];

        $page->sanitizeTransitions($data);

        $this->assertSame(
            [
                'S' => 'Wipe',
                'D' => 0,
            ],
            $data['transition'] ?? null,
        );
    }

    /**
     * /SS and /B apply to the Fly style only, and /SS must be greater than 0.
     *
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    public function testSanitizeTransitionsPrunesFlyOnlyEntries(): void
    {
        $page = $this->getTestObject();

        $data = [
            'transition' => [
                'S' => 'Wipe',
                'SS' => 3.0,
                'B' => true,
            ],
        ];
        $page->sanitizeTransitions($data);
        $this->assertSame(['S' => 'Wipe', 'D' => 1], $data['transition'] ?? null);

        $data = [
            'transition' => [
                'S' => 'Fly',
                'SS' => -2.5,
            ],
        ];
        $page->sanitizeTransitions($data);
        $this->assertSame(['S' => 'Fly', 'D' => 1, 'B' => false], $data['transition'] ?? null);

        $data = [
            'transition' => [
                'S' => 'Fly',
                'SS' => 0.5,
                'B' => true,
            ],
        ];
        $page->sanitizeTransitions($data);
        $this->assertSame(['S' => 'Fly', 'SS' => 0.5, 'B' => true, 'D' => 1], $data['transition'] ?? null);
    }

    /**
     * A page has a positive area, so a non-positive dimension falls back to the
     * default format instead of reaching the page boxes and the margin clamps.
     *
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    public function testSanitizeNonPositivePageSize(): void
    {
        $page = $this->getTestObject();

        $data = [
            'width' => -100.0,
            'height' => 200.0,
        ];
        $page->sanitizePageFormat($data);
        $page->sanitizeBoxData($data);
        $page->sanitizeMargins($data);

        $this->assertSame('A4', $data['format'] ?? null);
        $this->bcAssertEqualsWithDelta(595.276, $data['pwidth'] ?? null);
        $this->bcAssertEqualsWithDelta(841.890, $data['pheight'] ?? null);
        $this->bcAssertEqualsWithDelta(0.0, $data['box']['MediaBox']['llx'] ?? null);
        $this->bcAssertEqualsWithDelta(595.276, $data['box']['MediaBox']['urx'] ?? null);

        foreach ($data['margin'] ?? [] as $key => $value) {
            if ($key === 'booklet') {
                continue;
            }

            $this->assertGreaterThanOrEqual(0.0, $value, 'negative margin ' . $key);
        }

        // Ordering a pair of negative dimensions would otherwise put the larger
        // magnitude in the height slot.
        $data = [
            'width' => -300.0,
            'height' => -100.0,
            'orientation' => 'L',
        ];
        $page->sanitizePageFormat($data);
        $this->assertSame('A4', $data['format'] ?? null);
        $this->assertSame('P', $data['orientation'] ?? null);
    }

    /**
     * A caller-supplied page box that extends past the MediaBox describes an area the
     * page cannot have, so it is clamped exactly as a page resize clamps it.
     *
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    public function testSanitizeBoxDataClampsBoxesToMediaBox(): void
    {
        $page = $this->getTestObject();
        $data = [
            'format' => 'A4',
            'box' => [
                'CropBox' => [
                    'llx' => -100.0,
                    'lly' => -100.0,
                    'urx' => 2000.0,
                    'ury' => 3000.0,
                ],
            ],
        ];
        $page->sanitizePageFormat($data);
        $page->sanitizeBoxData($data);

        foreach (\Com\Tecnick\Pdf\Page\Page::BOX as $type) {
            $this->bcAssertEqualsWithDelta(0.0, $data['box'][$type]['llx'] ?? null, 0.01, $type);
            $this->bcAssertEqualsWithDelta(0.0, $data['box'][$type]['lly'] ?? null, 0.01, $type);
            $this->bcAssertEqualsWithDelta(595.276, $data['box'][$type]['urx'] ?? null, 0.01, $type);
            $this->bcAssertEqualsWithDelta(841.890, $data['box'][$type]['ury'] ?? null, 0.01, $type);
        }
    }

    /**
     * A page box given with the corners reversed is stored ordered, so the clamping
     * arithmetic that assumes llx <= urx keeps working.
     *
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    public function testSanitizeBoxDataOrdersReversedCorners(): void
    {
        $page = $this->getTestObject();
        $data = [
            'format' => 'MediaBox',
            'box' => [
                'MediaBox' => [
                    'llx' => 500.0,
                    'lly' => 700.0,
                    'urx' => 0.0,
                    'ury' => 0.0,
                ],
            ],
        ];
        $page->sanitizePageFormat($data);
        $page->sanitizeBoxData($data);

        $this->bcAssertEqualsWithDelta(0.0, $data['box']['MediaBox']['llx'] ?? null);
        $this->bcAssertEqualsWithDelta(0.0, $data['box']['MediaBox']['lly'] ?? null);
        $this->bcAssertEqualsWithDelta(500.0, $data['box']['MediaBox']['urx'] ?? null);
        $this->bcAssertEqualsWithDelta(700.0, $data['box']['MediaBox']['ury'] ?? null);
    }

    /**
     * The multiple-of-90 test accepts a loose scalar: a fractional or non-numeric
     * rotation is rejected without converting it to int first.
     *
     * @throws \Com\Tecnick\Pdf\Page\Exception
     */
    public function testSanitizeRotationWithLooseValues(): void
    {
        $page = $this->getTestObject();

        foreach ([45, -1] as $rotation) {
            $data = ['rotation' => $rotation];
            $page->sanitizeRotation($data);
            $this->assertSame(0, $data['rotation'] ?? null, 'rotation ' . $rotation);
        }

        foreach ([90 => 90, -90 => 270, 450 => 90, 180 => 180] as $rotation => $expected) {
            $data = ['rotation' => $rotation];
            $page->sanitizeRotation($data);
            $this->assertSame($expected, $data['rotation'] ?? null, 'rotation ' . $rotation);
        }

        // A fractional value is outside the declared int contract: it must be rejected
        // instead of being converted to int first.
        $fractional = ['rotation' => 90.5];
        // @mago-ignore analysis:possibly-invalid-argument
        $page->sanitizeRotation($fractional);
        $this->assertSame(0, $fractional['rotation'] ?? null);

        $integral = ['rotation' => 90.0];
        // @mago-ignore analysis:possibly-invalid-argument
        $page->sanitizeRotation($integral);
        $this->assertSame(90, $integral['rotation'] ?? null);
    }
}
