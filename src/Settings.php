<?php

/**
 * Settings.php
 *
 * @since     2011-05-23
 * @category  Library
 * @package   PdfPage
 * @author    Nicola Asuni <info@tecnick.com>
 * @copyright 2011-2024 Nicola Asuni - Tecnick.com LTD
 * @license   http://www.gnu.org/copyleft/lesser.html GNU-LGPL v3 (see LICENSE.TXT)
 * @link      https://github.com/tecnickcom/tc-lib-pdf-page
 *
 * This file is part of tc-lib-pdf-page software library.
 */

namespace Com\Tecnick\Pdf\Page;

use Com\Tecnick\Pdf\Encrypt\Encrypt;

/**
 * Com\Tecnick\Pdf\Page\Settings
 *
 * @since     2011-05-23
 * @category  Library
 * @package   PdfPage
 * @author    Nicola Asuni <info@tecnick.com>
 * @copyright 2011-2024 Nicola Asuni - Tecnick.com LTD
 * @license   http://www.gnu.org/copyleft/lesser.html GNU-LGPL v3 (see LICENSE.TXT)
 * @link      https://github.com/tecnickcom/tc-lib-pdf-page
 *
 * @phpstan-import-type PageBci from \Com\Tecnick\Pdf\Page\Box
 * @phpstan-import-type PageBox from \Com\Tecnick\Pdf\Page\Box
 * @phpstan-import-type MarginData from \Com\Tecnick\Pdf\Page\Box
 * @phpstan-import-type RegionData from \Com\Tecnick\Pdf\Page\Box
 * @phpstan-import-type TransitionData from \Com\Tecnick\Pdf\Page\Box
 * @phpstan-import-type PageData from \Com\Tecnick\Pdf\Page\Box
 * @phpstan-import-type PageInputData from \Com\Tecnick\Pdf\Page\Box
 *
 * @SuppressWarnings("PHPMD.TooManyPublicMethods")
 * @SuppressWarnings("PHPMD.ExcessiveClassComplexity")
 */
abstract class Settings extends \Com\Tecnick\Pdf\Page\Box
{
    /**
     * Epsilon precision used to compare floating point values.
     */
    public const EPS = 0.0001;

    /**
     * Alias for total number of pages in a group.
     *
     * @var string
     */
    public const PAGE_TOT = '~#PT';

    /**
     * Alias for page number.
     *
     * @var string
     */
    public const PAGE_NUM = '~#PN';

    /**
     * Array of pages (stack).
     *
     * @var array<int, PageData>
     */
    protected array $page = [];

    /**
     * Current page ID.
     */
    protected int $pid = -1;

    /**
     * Maximum page ID.
     */
    protected int $pmaxid = -1;

    /**
     * Count pages in each group.
     *
     * @var array<int, int>
     */
    protected array $group = [
        0 => 0,
    ];

    /**
     * Encrypt object.
     */
    protected Encrypt $enc;

    /**
     * True if we are in PDF/A mode.
     */
    protected bool $pdfa = false;

    /**
     * Enable stream compression.
     */
    protected bool $compress = true;

    /**
     * True if the signature approval is enabled (for incremental updates).
     */
    protected bool $sigapp = false;

    /**
     * Reserved Object ID for the resource dictionary.
     */
    protected int $rdoid = 1;

    /**
     * Root object ID.
     */
    protected int $rootoid = 0;

    /**
     * Return the current page ID.
     *
     * @return int Page ID.
     */
    public function getPageID(): int
    {
        return $this->pid;
    }

    /**
     * Sanitize or set the page modification time.
     *
     * @param PageInputData $data Page data.
     */
    public function sanitizePageNumber(array &$data): void
    {
        if (! empty($data['num'])) {
            $data['num'] = max(0, (int) $data['num']);
        }
    }

    /**
     * Sanitize or set the page modification time.
     *
     * @param PageInputData $data Page data.
     */
    public function sanitizeTime(array &$data): void
    {
        $data['time'] = empty($data['time']) ? time() : max(0, (int) $data['time']);
    }

    /**
     * Sanitize or set the page group.
     *
     * @param PageInputData $data Page data.
     */
    public function sanitizeGroup(array &$data): void
    {
        $data['group'] = empty($data['group']) ? 0 : max(0, $data['group']);
    }

    /**
     * Sanitize or set the page content.
     *
     * @param PageInputData $data Page data.
     */
    public function sanitizeContent(array &$data): void
    {
        if (empty($data['content'])) {
            $data['content'] = [''];
            return;
        }

        if (is_string($data['content'])) {
            $data['content'] = [(string) $data['content']]; // @phpstan-ignore parameterByRef.type
        }
    }

    /**
     * Sanitize or set the annotation references.
     *
     * @param PageInputData $data Page data.
     */
    public function sanitizeAnnotRefs(array &$data): void
    {
        if (empty($data['annotrefs'])) {
            $data['annotrefs'] = [];
        }
    }

    /**
     * Sanitize or set the page rotation.
     * The number of degrees by which the page shall be rotated clockwise when displayed or printed.
     * The value shall be a multiple of 90.
     *
     * @param PageInputData $data Page data.
     */
    public function sanitizeRotation(array &$data): void
    {
        $data['rotation'] = empty($data['rotation']) || ($data['rotation'] % 90 != 0) ? 0 : (int) $data['rotation'];
    }

    /**
     * Sanitize or set the page preferred zoom (magnification) factor.
     *
     * @param PageInputData $data Page data.
     */
    public function sanitizeZoom(array &$data): void
    {
        $data['zoom'] = empty($data['zoom']) ? 1 : $data['zoom']; // @phpstan-ignore parameterByRef.type
    }

    /**
     * Sanitize or set the page transitions.
     *
     * @param PageInputData $data Page data.
     *
     * @SuppressWarnings("PHPMD.CyclomaticComplexity")
     * @SuppressWarnings("PHPMD.NPathComplexity")
     */
    public function sanitizeTransitions(array &$data): void
    {
        if (empty($data['transition'])) {
            return;
        }

        // display duration before advancing page
        if (empty($data['transition']['Dur'])) {
            unset($data['transition']['Dur']);
        } else {
            $data['transition']['Dur'] = max(0, $data['transition']['Dur']); // @phpstan-ignore parameterByRef.type
        }

        // transition style
        $styles = [
            'Split',
            'Blinds',
            'Box',
            'Wipe',
            'Dissolve',
            'Glitter',
            'R',
            'Fly',
            'Push',
            'Cover',
            'Uncover',
            'Fade',
        ];
        if (empty($data['transition']['S']) || ! in_array($data['transition']['S'], $styles)) {
            $data['transition']['S'] = 'R';  // @phpstan-ignore parameterByRef.type
        }

        // duration of the transition effect, in seconds
        $data['transition']['D'] ??= 1; // @phpstan-ignore parameterByRef.type

        // dimension in which the specified transition effect shall occur
        if (
            empty($data['transition']['Dm'])
            || ! in_array($data['transition']['S'], ['Split', 'Blinds'])
            || ! in_array($data['transition']['Dm'], ['H', 'V'])
        ) {
            unset($data['transition']['Dm']); // @phpstan-ignore parameterByRef.type
        }

        // direction of motion for the specified transition effect
        if (
            empty($data['transition']['M'])
            || ! in_array($data['transition']['S'], ['Split', 'Box', 'Fly'])
            || ! in_array($data['transition']['M'], ['I', 'O'])
        ) {
            unset($data['transition']['M']); // @phpstan-ignore parameterByRef.type
        }

        // direction in which the specified transition effect shall moves
        if (
            empty($data['transition']['Di'])
            || ! in_array($data['transition']['S'], ['Wipe', 'Glitter', 'Fly', 'Cover', 'Uncover', 'Push'])
            || ! in_array($data['transition']['Di'], ['None', 0, 90, 180, 270, 315])
            || (in_array($data['transition']['Di'], [90, 180]) && ($data['transition']['S'] != 'Wipe'))
            || (($data['transition']['Di'] == 315) && ($data['transition']['S'] != 'Glitter'))
            || (($data['transition']['Di'] == 'None') && ($data['transition']['S'] != 'Fly'))
        ) {
            unset($data['transition']['Di']); // @phpstan-ignore parameterByRef.type
        }

        // If true, the area that shall be flown in is rectangular and opaque
        $data['transition']['B'] = ! empty($data['transition']['B']); // @phpstan-ignore parameterByRef.type
    }

    /**
     * Sanitize or set the page margins.
     *
     * @param PageInputData $data Page data.
     *
     * @SuppressWarnings("PHPMD.CyclomaticComplexity")
     * @SuppressWarnings("PHPMD.NPathComplexity")
     */
    public function sanitizeMargins(array &$data): void
    {
        if (empty($data['margin'])) {
            $data['margin'] = ['booklet' => false];
            if (empty($data['width']) || empty($data['height'])) {
                [$data['width'], $data['height'], $data['orientation']] = $this->getPageFormatSize('A4', 'P');
                $data['width'] /= $this->kunit;
                $data['height'] /= $this->kunit;
            }
        }

        if (!isset($data['margin']['booklet'])) {
            $data['margin']['booklet'] = false;
        }

        $dataWidth = $data['width'] ?? 0;
        $dataHeight = $data['height'] ?? 0;

        $margins = [
            'PL' => $dataWidth,
            'PR' => $dataWidth,
            'PT' => $dataHeight,
            'HB' => $dataHeight,
            'CT' => $dataHeight,
            'CB' => $dataHeight,
            'FT' => $dataHeight,
            'PB' => $dataHeight,
        ];

        if (empty($data['margin'])) {
            $data['margin'] = [];
        }

        foreach ($margins as $type => $max) {
            $data['margin'][$type] = ( // @phpstan-ignore parameterByRef.type
                empty($data['margin'][$type])
            ) ? 0 : min(max(0, $data['margin'][$type]), $max);
        }

        if ($data['margin']['booklet'] && ($this->pid % 2 == 0)) {
            // swap margins on odd pages
            // NOTE: $this->pid is the previous page (0 indexed).
            $mtmp = $data['margin']['PL'];
            $data['margin']['PL'] = $data['margin']['PR']; // @phpstan-ignore parameterByRef.type
            $data['margin']['PR'] = $mtmp; // @phpstan-ignore parameterByRef.type
        }

        $data['margin']['PR'] = min( // @phpstan-ignore parameterByRef.type
            $data['margin']['PR'],
            ($dataWidth - $data['margin']['PL'])
        );
        $data['margin']['HB'] = max( // @phpstan-ignore parameterByRef.type
            $data['margin']['HB'],
            $data['margin']['PT']
        );
        $data['margin']['CT'] = max( // @phpstan-ignore parameterByRef.type
            $data['margin']['CT'],
            $data['margin']['HB']
        );
        $data['margin']['CB'] = min( // @phpstan-ignore parameterByRef.type
            $data['margin']['CB'],
            ($dataHeight - $data['margin']['CT'])
        );
        $data['margin']['FT'] = min( // @phpstan-ignore parameterByRef.type
            $data['margin']['FT'],
            $data['margin']['CB']
        );
        $data['margin']['PB'] = min( // @phpstan-ignore parameterByRef.type
            $data['margin']['PB'],
            $data['margin']['FT']
        );

        $data['ContentWidth'] = ( // @phpstan-ignore parameterByRef.type
            $dataWidth - $data['margin']['PL'] - $data['margin']['PR']);
        $data['ContentHeight'] = ( // @phpstan-ignore parameterByRef.type
            $dataHeight - $data['margin']['CT'] - $data['margin']['CB']);
        $data['HeaderHeight'] = ( // @phpstan-ignore parameterByRef.type
            $data['margin']['HB'] - $data['margin']['PT']);
        $data['FooterHeight'] = ( // @phpstan-ignore parameterByRef.type
            $data['margin']['FT'] - $data['margin']['PB']);
    }

    /**
     * Sanitize or set the page regions (columns).
     *
     * @param PageInputData $data Page data.
     */
    public function sanitizeRegions(array &$data): void
    {
        if (! empty($data['columns'])) {
            // set eaual columns
            $data['region'] = [];
            $width = (($data['ContentWidth'] ?? 0) / ($data['columns'] ?? 1));
            for ($idx = 0; $idx < $data['columns']; ++$idx) {
                $data['region'][] = [ // @phpstan-ignore parameterByRef.type
                    'RX' => (($data['margin']['PL'] ?? 0) + ($idx * $width)),
                    'RY' => ($data['margin']['CT'] ?? 0),
                    'RW' => $width,
                    'RH' => ($data['ContentHeight'] ?? 0),
                ];
            }
        }

        if (empty($data['region'])) {
            // default single region
            $data['region'] = [[ // @phpstan-ignore parameterByRef.type
                'RX' => ($data['margin']['PL'] ?? 0),
                'RY' => ($data['margin']['CT'] ?? 0),
                'RW' => ($data['ContentWidth'] ?? 0),
                'RH' => ($data['ContentHeight'] ?? 0),
            ]];
        }

        $data['columns'] = 0; // @phpstan-ignore parameterByRef.type
        foreach ($data['region'] as $key => $val) {
            // region width
            $data['region'][$key]['RW'] = min( // @phpstan-ignore parameterByRef.type
                max(
                    0,
                    ($val['RW'] ?? 0),
                ),
                ($data['ContentWidth'] ?? 0)
            );
            // horizontal coordinate of the top-left corner
            $data['region'][$key]['RX'] = min( // @phpstan-ignore parameterByRef.type
                max(0, ($val['RX'] ?? 0)),
                (($data['width'] ?? 0) - ($data['margin']['PR'] ?? 0) - ($val['RW'] ?? 0))
            );
            // distance of the region right side from the left page edge
            $data['region'][$key]['RL'] = ( // @phpstan-ignore parameterByRef.type
                ($val['RX'] ?? 0) + ($val['RW'] ?? 0));
            // distance of the region right side from the right page edge
            $data['region'][$key]['RR'] = ( // @phpstan-ignore parameterByRef.type
                ($data['width'] ?? 0) - ($val['RX'] ?? 0) - ($val['RW'] ?? 0));
            // region height
            $data['region'][$key]['RH'] = min( // @phpstan-ignore parameterByRef.type
                max(
                    0,
                    ($val['RH'] ?? 0)
                ),
                ($data['ContentHeight'] ?? 0)
            );
            // vertical coordinate of the top-left corner
            $data['region'][$key]['RY'] = min( // @phpstan-ignore parameterByRef.type
                max(0, ($val['RY'] ?? 0)),
                (($data['height'] ?? 0) - ($data['margin']['CB'] ?? 0) - ($val['RH'] ?? 0))
            );
            // distance of the region bottom side from the top page edge
            $data['region'][$key]['RT'] = ( // @phpstan-ignore parameterByRef.type
                ($val['RY'] ?? 0) + ($val['RH'] ?? 0));
            // distance of the region bottom side from the bottom page edge
            $data['region'][$key]['RB'] = ( // @phpstan-ignore parameterByRef.type
                ($data['height'] ?? 0) - ($val['RY'] ?? 0) - ($val['RH'] ?? 0));

            // initialize cursor position inside the region
            $data['region'][$key]['x'] = $data['region'][$key]['RX']; // @phpstan-ignore parameterByRef.type
            $data['region'][$key]['y'] = $data['region'][$key]['RY']; // @phpstan-ignore parameterByRef.type

            ++$data['columns']; // @phpstan-ignore parameterByRef.type
        }

        if (! isset($data['autobreak'])) {
            $data['autobreak'] = true; // @phpstan-ignore parameterByRef.type
        }
    }

    /**
     * Sanitize or set the page boxes containing the page boundaries.
     *
     * @param PageInputData $data Page data.
     *
     * @SuppressWarnings("PHPMD.CyclomaticComplexity")
     * @SuppressWarnings("PHPMD.NPathComplexity")
     */
    public function sanitizeBoxData(array &$data): void
    {
        if (empty($data['box'])) {
            if (empty($data['pwidth']) || empty($data['pheight'])) {
                [$data['pwidth'], $data['pheight'], $data['orientation']] = $this->getPageFormatSize('A4', 'P');
            }

            $data['box'] = $this->setPageBoxes(($data['pwidth'] ?? 0), ($data['pheight'] ?? 0));
        } else {
            if (isset($data['format']) && $data['format'] !== '' && ($data['format'] == 'MediaBox')) {
                $data['format'] = '';
                $data['width'] = abs(
                    ($data['box']['MediaBox']['urx'] ?? 0) - ($data['box']['MediaBox']['llx'] ?? 0)
                ) / $this->kunit;
                $data['height'] = abs(
                    ($data['box']['MediaBox']['ury'] ?? 0) - ($data['box']['MediaBox']['lly'] ?? 0)
                ) / $this->kunit;
                $this->sanitizePageFormat($data);
            }

            if (empty($data['box']['MediaBox'])) {
                $data['box'] = $this->setBox(
                    $data['box'] ?? $this->setPageBoxes( // @phpstan-ignore argument.type
                        ($data['pwidth'] ?? 0),
                        ($data['pheight'] ?? 0)
                    ),
                    'MediaBox',
                    0,
                    0,
                    ($data['pwidth'] ?? 0),
                    ($data['pheight'] ?? 0),
                );
            }

            if (empty($data['box']['CropBox'])) {
                $data['box'] = $this->setBox(
                    $data['box'], // @phpstan-ignore argument.type
                    'CropBox',
                    ($data['box']['MediaBox']['llx'] ?? 0),
                    ($data['box']['MediaBox']['lly'] ?? 0),
                    ($data['box']['MediaBox']['urx'] ?? 0),
                    ($data['box']['MediaBox']['ury'] ?? 0),
                );
            }

            if (empty($data['box']['BleedBox'])) {
                $data['box'] = $this->setBox(
                    $data['box'], // @phpstan-ignore argument.type
                    'BleedBox',
                    ($data['box']['CropBox']['llx'] ?? 0),
                    ($data['box']['CropBox']['lly'] ?? 0),
                    ($data['box']['CropBox']['urx'] ?? 0),
                    ($data['box']['CropBox']['ury'] ?? 0),
                );
            }

            if (empty($data['box']['TrimBox'])) {
                $data['box'] = $this->setBox(
                    $data['box'], // @phpstan-ignore argument.type
                    'TrimBox',
                    ($data['box']['CropBox']['llx'] ?? 0),
                    ($data['box']['CropBox']['lly'] ?? 0),
                    ($data['box']['CropBox']['urx'] ?? 0),
                    ($data['box']['CropBox']['ury'] ?? 0),
                );
            }

            if (empty($data['box']['ArtBox'])) {
                $data['box'] = $this->setBox(
                    $data['box'], // @phpstan-ignore argument.type
                    'ArtBox',
                    ($data['box']['CropBox']['llx'] ?? 0),
                    ($data['box']['CropBox']['lly'] ?? 0),
                    ($data['box']['CropBox']['urx'] ?? 0),
                    ($data['box']['CropBox']['ury'] ?? 0),
                );
            }
        }

        $orientation = $this->getPageOrientation(
            abs(
                ($data['box']['MediaBox']['urx'] ?? 0) - ($data['box']['MediaBox']['llx'] ?? 0)
            ),
            abs(
                ($data['box']['MediaBox']['ury'] ?? 0) - ($data['box']['MediaBox']['lly'] ?? 0)
            )
        );
        if (empty($data['orientation'])) {
            $data['orientation'] = $orientation;
        } elseif ($data['orientation'] != $orientation) {
            $data['box'] = $this->swapCoordinates($data['box']); // @phpstan-ignore argument.type
        }
    }

    /**
     * Sanitize or set the page format.
     *
     * @param PageInputData $data Page data.
     */
    public function sanitizePageFormat(array &$data): void
    {
        if (empty($data['orientation'])) {
            $data['orientation'] = '';
        }

        if (! empty($data['format'])) {
            [$data['pwidth'], $data['pheight'], $data['orientation']] = $this->getPageFormatSize(
                $data['format'],
                $data['orientation']
            );
            $data['width'] = ($data['pwidth'] / $this->kunit);
            $data['height'] = ($data['pheight'] / $this->kunit);
        } else {
            $data['format'] = 'CUSTOM';
            if (empty($data['width']) || empty($data['height'])) {
                // default page format
                $data['format'] = 'A4';
                $data['orientation'] = 'P';
                $this->sanitizePageFormat($data);
                return;
            }

            [$data['width'], $data['height'], $data['orientation']] = $this->getPageOrientedSize(
                $data['width'],
                $data['height'],
                $data['orientation']
            );
        }

        // convert values in points
        $data['pwidth'] = ($data['width'] * $this->kunit);
        $data['pheight'] = ($data['height'] * $this->kunit);
    }
}
