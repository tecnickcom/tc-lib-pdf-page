<?php
/**
 * Page.php
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

use \Com\Tecnick\Color\Pdf as Color;
use \Com\Tecnick\Pdf\Encrypt\Encrypt;
use \Com\Tecnick\Pdf\Page\Exception as PageException;

/**
 * Com\Tecnick\Pdf\Page\Page
 *
 * @since       2011-05-23
 * @category    Library
 * @package     PdfPage
 * @author      Nicola Asuni <info@tecnick.com>
 * @copyright   2011-2015 Nicola Asuni - Tecnick.com LTD
 * @license     http://www.gnu.org/copyleft/lesser.html GNU-LGPL v3 (see LICENSE.TXT)
 * @link        https://github.com/tecnickcom/tc-lib-pdf-page
 */
class Page extends \Com\Tecnick\Pdf\Page\Settings
{
    /**
     * Array of pages (stack).
     *
     * @var array
     */
    protected $page = array();

    /**
     * Current page ID.
     * NOTE: page 0 is the default page (to not be printed).
     *
     * @var int
     */
    protected $pageid = -1;
    
    /**
     * Unit of measure conversion ratio
     *
     * @var float
     */
    protected $kunit = 1.0;

    /**
     * Color object
     *
     * @var Color
     */
    protected $col;

    /**
     * Encrypt object
     *
     * @var Encrypt
     */
    protected $enc;

    /**
     * True if we are in PDF/A mode.
     *
     * @var bool
     */
    protected $pdfa = false;

    /**
     * True if the signature approval is enabled (for incremental updates).
     *
     * @var bool
     */
    protected $sigapp = false;

    /**
     * Reserved Object ID for the resource dictionary.
     *
     * @var int
     */
    protected $rdoid = 1;

    /**
     * Initialize page data
     *
     * @param float   $kunit  Unit of measure conversion ratio
     * @param Color   $col    Color object
     * @param Encrypt $enc    Encrypt object
     * @param bool    $pdfa   True if we are in PDF/A mode.
     * @param bool    $sigapp True if the signature approval is enabled (for incremental updates).
     */
    public function __construct($kunit, Color $col, Encrypt $enc, $pdfa = false, $sigapp = false)
    {
        $this->kunit = (float) $kunit;
        $this->col = $col;
        $this->enc = $enc;
        $this->pdfa = (bool) $pdfa;
        $this->sigapp = (bool) $sigapp;
    }

    /**
     * Add a new page
     *
     * @param array $data Page data:
     *     time        : UTC page modification time in seconds;
     *     group       : page group number;
     *     content     : string containing the raw page content;
     *     annotrefs   : array containing the annotation object references;
     *     format      : page format name, or alternatively you can set width and height as below;
     *     width       : page width;
     *     height      : page height;
     *     orientation : page orientation ('P' or 'L');
     *     rotation    : the number of degrees by which the page shall be rotated clockwise when displayed or printed;
     *     box         : array containing page box boundaries and settings (@see setBox);
     *     transition  : array containing page transition data (@see getPageTransition);
     *     zoom        : preferred zoom (magnification) factor;
     */
    public function add(array $data = array())
    {
        $this->sanitizeTime($data);
        $this->sanitizeGroup($data);
        $this->sanitizeContent($data);
        $this->sanitizeAnnotRefs($data);
        $this->sanitizeRotation($data);
        $this->sanitizeZoom($data);
        $this->sanitizePageFormat($data);
        $this->sanitizeBoxData($data);
        $this->sanitizeTransitions($data);

        $this->page[++$this->pageid] = $data;
    }

    /**
     * Remove and return last page
     *
     * @return string PDF page string
     */
    public function pop()
    {
        if ($this->pageid <= 0) {
            throw new GraphException('The page stack is empty');
        }
        --$this->pageid;
        return array_pop($this->page);
    }

    /**
     * Returns the array (stack) containing all pages data.
     *
     * return array
     */
    public function getPages()
    {
        return $this->page;
    }

    /**
     * Returns the reserved Object ID for the Resource dictionary.
     *
     * return int
     */
    public function getResourceDictObjID()
    {
        return $this->rdoid;
    }

    /**
     * Returns the specified page data.
     *
     * @param int $idx Page ID (page_number - 1).
     *
     * return array
     */
    public function getPage($idx)
    {
        if (!isset($this->page[$idx])) {
            throw new \Com\Tecnick\Pdf\Page\Exception('The page '.$idx.' do not exist.');
        }
        return $this->page[$idx];
    }

    /**
     * Returns the last page array
     *
     * @return array
     */
    public function getCurrentPage()
    {
        return $this->page[$this->pageid];
    }

    /**
     * Add page content
     *
     * @param array $data Page data
     */
    public function addContent($content)
    {
        $this->page[$this->pageid]['content'][] = (string) $content;
    }

    /**
     * Remove and return last page content
     *
     * @param array $data Page data
     *
     * @param string content
     */
    public function popContent()
    {
        return array_pop($this->page[$this->pageid]['content']);
    }

    /**
     * Returns the PDF command to output all page sections
     *
     * @param int $pon Current PDF object number
     *
     * @return string PDF command
     */
    public function getPdfPages(&$pon)
    {
        $out = $this->getPageRootObj($pon);
        $rootobjid = $pon;

        foreach ($this->page as $page) {
            $out .= $this->getPageContentObj($pon, implode('', $page['content']));
            $contentobjid = $pon;

            $out .= $page['n'].' 0 obj'."\n"
                .'<<'."\n"
                .'/Type /Page'."\n"
                .'/Parent '.$rootobjid.' 0 R'."\n";
            if (!$this->pdfa) {
                $out .= '/Group << /Type /Group /S /Transparency /CS /DeviceRGB >>'."\n";
            }
            if (!$this->sigapp) {
                $out .= '/LastModified '.$this->enc->getFormattedDate($page['time'], $pon)."\n";
            }
            $out .= '/Resources '.$this->rdoid.' 0 R'."\n"
                .$this->getBox($page['box'])
                .$this->getBoxColorInfo($page['box'])
                .'/Contents '.$contentobjid.' 0 R'."\n"
                .'/Rotate '.$page['rotation']."\n"
                .'/PZ '.sprintf('%F', $page['zoom'])."\n"
                .$this->getPageTransition($page)
                .$this->getAnnotationRef($page)
                .'>>'."\n"
                .'endobj'."\n";
        }

        return $out;
    }

    /**
     * Returns the PDF command to output the page content.
     *
     * @param int    $pon     Current PDF object number.
     * @param string $content Page content.
     *
     * @return string PDF command
     */
    protected function getPageTransition($page)
    {
        if (empty($page['transition'])) {
            return '';
        }
        $entries = array('S', 'D', 'Dm', 'M', 'Di', 'SS', 'B');
        $out = '';
        if (isset($page['transition']['Dur'])) {
            $out .= '/Dur '.sprintf('%F', $page['transition']['Dur'])."\n";
        }
        $out .= '/Trans <<'."\n"
            .'/Type /Trans'."\n";
        foreach ($page['transition'] as $key => $val) {
            if (in_array($key, $entries)) {
                if (is_float($val)) {
                    $val = sprintf('%F', $val);
                }
                $out .= '/'.$key.' /'.$val."\n";
            }
        }
        $out .= '>>'."\n";
        return $out;
    }

    /**
     * Get references to page annotations.
     *
     * @param array $page Page data
     *
     * @return string PDF command
     */
    protected function getAnnotationRef($page)
    {
        if (empty($page['annotrefs'])) {
            return '';
        }
        $out = '/Annots [ ';
        foreach ($page['annotrefs'] as $val) {
            $out .= intval($val).' 0 R ';
        }
        $out .= ']'."\n";
        return $out;
    }

    /**
     * Returns the PDF command to output the page content.
     *
     * @param int    $pon     Current PDF object number.
     * @param string $content Page content.
     *
     * @return string PDF command
     */
    protected function getPageContentObj(&$pon, $content = '')
    {
        $content = $this->replacePageTemplates($content);
        $stream = $this->enc->encryptString(gzcompress($content), ++$pon);
        $out = $pon.' 0 obj'."\n"
            .'<</Filter /FlateDecode /Length '.strlen($stream).'>>'."\n"
            .'stream'."\n"
            .$stream."\n"
            .'endstream'."\n"
            .'endobj'."\n";
        return $out;
    }

    /**
     * Returns the PDF command to output the page root object.
     *
     * @param int $pon Current PDF object number
     *
     * @return string PDF command
     */
    protected function getPageRootObj(&$pon)
    {
        $out = (++$pon).' 0 obj'."\n";
        $this->rdoid = ++$pon; // reserve object ID for the resource dictionary
        $out .= '<< /Type /Pages /Kids [ ';
        $numpages = count($this->page);
        for ($idx = 0; $idx < $numpages; ++$idx) {
            $this->page[$idx]['n'] = ++$pon;
            $out .= $this->page['n'].' 0 R ';
        }
        $out .= '] /Count '.$numpages.' >>'."\n"
            .'endobj'."\n";
        return $out;
    }

    /**
     * Sanitize or set the page format
     *
     * @param array $data Page data
     */
    public function sanitizePageFormat(array &$data)
    {
        if (empty($data['orientation'])) {
            $data['orientation'] = '';
        }
        if (!empty($data['format'])) {
            list($data['width'], $data['height'], $data['orientation']) = $this->getPageFormatSize(
                $data['format'],
                $data['orientation'],
                $this->kunit
            );
        } else {
            $data['format'] = 'CUSTOM';
            if (empty($data['width']) || empty($data['height'])) {
                if (empty($data['box']['MediaBox'])) {
                    // default page format
                    $data['format'] = 'A4';
                    $data['orientation'] = 'P';
                    return $this->sanitizePageFormat($data);
                }
                $data['format'] = 'MediaBox';
                return;
            } else {
                list($data['width'], $data['height'], $data['orientation']) = $this->getPageOrientedSize(
                    $data['width'],
                    $data['height'],
                    $data['orientation']
                );
            }
        }
        // convert values in points
        $data['pwidth'] = ($data['width'] * $this->kunit);
        $data['pheight'] = ($data['height'] * $this->kunit);
    }

    /**
     * Sanitize or set the page boxes containing the page boundaries.
     *
     * @param array $data Page data
     */
    public function sanitizeBoxData(array &$data)
    {
        if (empty($data['box'])) {
            $data['box'] = $this->setPageBoxes($data['pwidth'], $data['pheight']);
        } else {
            if ($data['format'] == 'MediaBox') {
                $data['format'] = '';
                $data['width'] = abs($data['box']['MediaBox']['urx'] - $data['box']['MediaBox']['llx']) / $this->kunit;
                $data['height'] = abs($data['box']['MediaBox']['ury'] - $data['box']['MediaBox']['lly']) / $this->kunit;
                $this->setPageFormat($data);
            }
            if (empty($data['box']['MediaBox'])) {
                $data['box'] = $this->setBox($data['box'], 'MediaBox', 0, 0, $data['pwidth'], $data['pheight']);
            }
            if (empty($data['box']['CropBox'])) {
                $data['box'] = $this->setBox(
                    $data['box'],
                    'CropBox',
                    $data['box']['MediaBox']['llx'],
                    $data['box']['MediaBox']['lly'],
                    $data['box']['MediaBox']['urx'],
                    $data['box']['MediaBox']['ury']
                );
            }
            if (empty($data['box']['BleedBox'])) {
                $data['box'] = $this->setBox(
                    $data['box'],
                    'BleedBox',
                    $data['box']['CropBox']['llx'],
                    $data['box']['CropBox']['lly'],
                    $data['box']['CropBox']['urx'],
                    $data['box']['CropBox']['ury']
                );
            }
            if (empty($data['box']['TrimBox'])) {
                $data['box'] = $this->setBox(
                    $data['box'],
                    'TrimBox',
                    $data['box']['CropBox']['llx'],
                    $data['box']['CropBox']['lly'],
                    $data['box']['CropBox']['urx'],
                    $data['box']['CropBox']['ury']
                );
            }
            if (empty($data['box']['ArtBox'])) {
                $data['box'] = $this->setBox(
                    $data['box'],
                    'ArtBox',
                    $data['box']['CropBox']['llx'],
                    $data['box']['CropBox']['lly'],
                    $data['box']['CropBox']['urx'],
                    $data['box']['CropBox']['ury']
                );
            }
        }
    }

    /**
     * Replace page templates and numbers
     *
     * @param string $content Page content.
     *
     * @return string page content
     */
    protected function replacePageTemplates($content)
    {
        // @TODO: implement number replacement
        return $content;
    }
}
