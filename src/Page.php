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
     * Alias for total number of pages in a group
     *
     * @var string
     */
    const PAGE_TOT = '~#PT';
    
    /**
     * Alias for page number
     *
     * @var string
     */
    const PAGE_NUM = '~#PN';

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
     * Count pages in each group
     *
     * @var array
     */
    protected $group = array(0 => 0);
    
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
     *     num         : if set overwrites the page number;
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
     *     margin      : page margins:
     *                   PL : page left
     *                   PR : page right
     *                   PT : page top (header top)
     *                   HB : header bottom
     *                   CT : content top
     *                   CB : content bottom (breaking point)
     *                   FT : footer top
     *                   PB : page bottom (footer bottom)
     */
    public function add(array $data = array())
    {
        if (empty($data) && ($this->pageid >= 0)) {
            // clone last page data
            $data = $this->page[$this->pageid];
            unset($data['time'], $data['content'], $data['annotrefs'], $data['pagenum']);
        } else {
            $this->sanitizeGroup($data);
            $this->sanitizeRotation($data);
            $this->sanitizeZoom($data);
            $this->sanitizePageFormat($data);
            $this->sanitizeBoxData($data);
            $this->sanitizeTransitions($data);
            $this->sanitizeMargins($data);
        }

        $this->sanitizeTime($data);
        $this->sanitizeContent($data);
        $this->sanitizeAnnotRefs($data);
        $this->sanitizePageNumber($data);
        $data['content_mark'] = array(0);

        $this->page[++$this->pageid] = $data;
        if (isset($this->group[$data['group']])) {
            $this->group[$data['group']] += 1;
        } else {
            $this->group[$data['group']] = 1;
        }
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
        $this->group[$this->page['group']] -= 1;
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
     * Add page content mark
     */
    public function addContentMark()
    {
        $this->page[$this->pageid]['content_mark'][] = count($this->page[$this->pageid]['content']);
    }

    /**
     * Remove the last marked page content
     */
    public function popContentToLastMark()
    {
        $mark = array_pop($this->page[$this->pageid]['content_mark']);
        $this->page[$this->pageid]['content'] = array_slice($this->page[$this->pageid]['content'], 0, $mark, true);
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

        foreach ($this->page as $num => $page) {
            if (!isset($page['num'])) {
                if ($num > 0) {
                    if ($page['group'] == $this->page[($num - 1)]['group']) {
                        $page['num'] = (1 + $this->page[($num - 1)]['num']);
                    } else {
                        // new page group
                        $page['num'] = 1;
                    }
                } else {
                    $page['num'] = (1 + $num);
                }
            }
            
            $content = $this->replacePageTemplates($page);
            $out .= $this->getPageContentObj($pon, $content);
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
     * Replace page templates and numbers
     *
     * @param array $data Page data
     */
    protected function replacePageTemplates(array $data)
    {
        return implode(
            '',
            str_replace(
                array(PAGE_TOT, PAGE_NUM),
                array($this->group[$data['group']], $data['num']),
                $data['content']
            )
        );
    }
}
