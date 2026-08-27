<?php

/**
 * TestUtil.php
 *
 * @since     2020-12-19
 * @category  Library
 * @package   PdfPage
 * @author    Nicola Asuni <info@tecnick.com>
 * @copyright 2015-2026 Nicola Asuni - Tecnick.com LTD
 * @license   https://www.gnu.org/copyleft/lesser.html GNU-LGPL v3 (see LICENSE)
 * @link      https://github.com/tecnickcom/tc-lib-pdf-page
 *
 * This file is part of tc-lib-pdf-page software library.
 */

namespace Test;

use PHPUnit\Framework\TestCase;

/**
 * Shared base class for the test suite: encryption stub and assertion helpers.
 *
 * @since     2020-12-19
 * @category  Library
 * @package   PdfPage
 * @author    Nicola Asuni <info@tecnick.com>
 * @copyright 2015-2026 Nicola Asuni - Tecnick.com LTD
 * @license   https://www.gnu.org/copyleft/lesser.html GNU-LGPL v3 (see LICENSE)
 * @link      https://github.com/tecnickcom/tc-lib-pdf-page
 */
class TestUtil extends TestCase
{
    /**
     * Returns an Encrypt object with encryption disabled: encryptString() and
     * getFormattedDate() pass the data through. The constructor is overridden so
     * that the helper does not inherit the `@throws` contract of
     * Encrypt::__construct().
     */
    protected function getEncryptObject(): \Com\Tecnick\Pdf\Encrypt\Encrypt
    {
        return new class() extends \Com\Tecnick\Pdf\Encrypt\Encrypt {
            public function __construct() {}
        };
    }

    public function bcAssertEqualsWithDelta(
        mixed $expected,
        mixed $actual,
        float $delta = 0.01,
        string $message = '',
    ): void {
        parent::assertEqualsWithDelta($expected, $actual, $delta, $message);
    }

    /**
     * @param class-string<\Throwable> $exception
     */
    public function bcExpectException(string $exception): void
    {
        parent::expectException($exception);
    }

    public function bcAssertStringContainsString(string $needle, string $haystack): void
    {
        parent::assertStringContainsString($needle, $haystack);
    }
}
