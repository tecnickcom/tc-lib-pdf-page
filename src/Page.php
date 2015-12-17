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
class Page extends \Com\Tecnick\Pdf\Page\Box
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
     * @return
     */
    public function add(array $data = array())
    {
        // 'n'
        // 'time'
        // 'content'
        // 'annotrefs'
        // 'width'
        // 'height'
        // 'box'
        // 'transition'
        // 'orientation'
        // 'rotation'
        // 'zoom'

        // @TODO: default page
        // @TODO: methods to add properties

        
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

            //@TODO: replace page numbers ....
            
            $out .= $this->getPageContentObj($pon, $page['content']);
            $contentobjid = $pon;

            $out .= (++$pon).' 0 obj'."\n"
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
                .'/PZ '.$page['zoom']."\n"
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
            $out .= '/Dur '.$page['transition']['Dur']."\n";
        }
        $out .= '/Trans <<'."\n"
            .'/Type /Trans'."\n";
        foreach ($page['transition'] as $key => $val) {
            if (in_array($key, $entries)) {
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
            $out .= $val.' 0 R ';
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
        $out = (++$pon).' 0 obj'."\n"
            .'<< /Type /Pages /Kids [ ';
        foreach ($this->page as $page) {
            $out .= $page['n'].' 0 R ';
        }
        $out .= '] /Count '.count($this->page).' >>'."\n"
            .'endobj'."\n";
        $this->rdoid = ++$pon; // reserve object ID for the resource dictionary
        return $out;
    }
}
