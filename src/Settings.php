<?php
/**
 * Settings.php
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
use \Com\Tecnick\Pdf\Page\Exception as PageException;

/**
 * Com\Tecnick\Pdf\Page\Settings
 *
 * @since       2011-05-23
 * @category    Library
 * @package     PdfPage
 * @author      Nicola Asuni <info@tecnick.com>
 * @copyright   2011-2015 Nicola Asuni - Tecnick.com LTD
 * @license     http://www.gnu.org/copyleft/lesser.html GNU-LGPL v3 (see LICENSE.TXT)
 * @link        https://github.com/tecnickcom/tc-lib-pdf-page
 */
abstract class Settings extends \Com\Tecnick\Pdf\Page\Box
{
    /**
     * Sanitize or set the page modification time.
     *
     * @param array $data Page data
     */
    public function sanitizeTime(array &$data)
    {
        if (empty($data['time'])) {
            $data['time'] = time();
        } else {
            $data['time'] = max(0, intval($data['time']));
        }
    }

    /**
     * Sanitize or set the page group
     *
     * @param array $data Page data
     */
    public function sanitizeGroup(array &$data)
    {
        if (empty($data['group'])) {
            unset($data['group']);
        } else {
            $data['group'] = intval($data['group']);
        }
    }

    /**
     * Sanitize or set the page content.
     *
     * @param array $data Page data
     */
    public function sanitizeContent(array &$data)
    {
        if (empty($data['content'])) {
            $data['content'] = array('');
        } else {
            $data['content'] = array((string)$data['content']);
        }
    }

    /**
     * Sanitize or set the annotation references
     *
     * @param array $data Page data
     */
    public function sanitizeAnnotRefs(array &$data)
    {
        if (empty($data['annotrefs'])) {
            $data['annotrefs'] = array();
        }
    }

    /**
     * Sanitize or set the page rotation.
     * The number of degrees by which the page shall be rotated clockwise when displayed or printed.
     * The value shall be a multiple of 90.
     *
     * @param array $data Page data
     */
    public function sanitizeRotation(array &$data)
    {
        if (empty($data['rotation']) || (($data['rotation'] % 90) != 0)) {
            $data['rotation'] = 0;
        } else {
            $data['rotation'] = intval($data['rotation']);
        }
    }

    /**
     * Sanitize or set the page preferred zoom (magnification) factor.
     *
     * @param array $data Page data
     */
    public function sanitizeZoom(array &$data)
    {
        if (empty($data['zoom'])) {
            $data['zoom'] = 1;
        } else {
            $data['zoom'] = floatval($data['zoom']);
        }
    }

    /**
     * Sanitize or set the page transitions.
     *
     * @param array $data Page data
     *
     * @SuppressWarnings(PHPMD)
     */
    public function sanitizeTransitions(array &$data)
    {
        if (empty($data['transition'])) {
            return;
        }
        // display duration before advancing page
        if (empty($data['transition']['Dur'])) {
            unset($data['transition']['Dur']);
        } else {
            $data['transition']['Dur'] = floatval($data['transition']['Dur']);
        }
        // transition style
        $styles = array(
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
            'Fade'
        );
        if (empty($data['transition']['S']) || !in_array($data['transition']['S'], $styles)) {
            $data['transition']['S'] = 'R';
        }
        // duration of the transition effect, in seconds
        if (!isset($data['transition']['D'])) {
            $data['transition']['D'] = 1;
        } else {
            $data['transition']['D'] = intval($data['transition']['D']);
        }
        // dimension in which the specified transition effect shall occur
        if (empty($data['transition']['Dm'])
            || !in_array($data['transition']['S'], array('Split', 'Blinds'))
            || !in_array($data['transition']['Dm'], array('H', 'V'))
        ) {
            unset($data['transition']['Dm']);
        }
        // direction of motion for the specified transition effect
        if (empty($data['transition']['M'])
            || !in_array($data['transition']['S'], array('Split', 'Box', 'Fly'))
            || !in_array($data['transition']['M'], array('I', 'O'))
        ) {
            unset($data['transition']['M']);
        }
        // direction in which the specified transition effect shall moves
        if (empty($data['transition']['Di'])
            || !in_array($data['transition']['S'], array('Wipe', 'Glitter', 'Fly', 'Cover', 'Uncover', 'Push'))
            || !in_array($data['transition']['Di'], array('None', 0, 90, 180, 270, 315))
            || (in_array($data['transition']['Di'], array(90, 180)) && ($data['transition']['S'] != 'Wipe'))
            || (($data['transition']['Di'] == 315) && ($data['transition']['S'] != 'Glitter'))
            || (($data['transition']['Di'] == 'None') && ($data['transition']['S'] != 'Fly'))
        ) {
            unset($data['transition']['Di']);
        }
        // starting or ending scale at which the changes shall be drawn
        if (isset($data['transition']['SS'])) {
            $data['transition']['SS'] = floatval($data['transition']['SS']);
        }
        // If true, the area that shall be flown in is rectangular and opaque
        if (empty($data['transition']['B'])) {
            $data['transition']['B'] = false;
        } else {
            $data['transition']['B'] = true;
        }
    }
}
