<?php
/**
 * PHPCompatibility, an external standard for PHP_CodeSniffer.
 *
 * @package   PHPCompatibility
 * @copyright 2012-2020 PHPCompatibility Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCompatibility/PHPCompatibility
 */

namespace PHPCompatibility\Tests\FunctionDeclarations;

use PHPCompatibility\Tests\BaseSniffTest;

/**
 * Test the NewParamTypeDeclarations sniff.
 *
 * @group newParamTypeDeclarations
 * @group functionDeclarations
 * @group typeDeclarations
 *
 * @covers \PHPCompatibility\Sniffs\FunctionDeclarations\NewParamTypeDeclarationsSniff
 *
 * @since 7.0.0
 */
class NewParamTypeDeclarationsUnitTest extends BaseSniffTest
{

    /**
     * testNewTypeDeclaration
     *
     * @dataProvider dataNewTypeDeclaration
     *
     * @param string $type              The scalar type.
     * @param string $lastVersionBefore The PHP version just *before* the type hint was introduced.
     * @param array  $line              The line number where the error is expected.
     * @param string $okVersion         A PHP version in which the type hint was ok to be used.
     * @param bool   $testNoViolation   Whether or not to test noViolation.
     *                                  Defaults to true.
     *
     * @return void
     */
    public function testNewTypeDeclaration($type, $lastVersionBefore, $line, $okVersion, $testNoViolation = true)
    {
        $file = $this->sniffFile(__FILE__, $lastVersionBefore);
        $this->assertError($file, $line, "'{$type}' type declaration is not present in PHP version {$lastVersionBefore} or earlier");

        if ($testNoViolation === true) {
            $file = $this->sniffFile(__FILE__, $okVersion);
            $this->assertNoViolation($file, $line);
        }
    }

    /**
     * Data provider.
     *
     * @see testNewTypeDeclaration()
     *
     * @return array
     */
    public function dataNewTypeDeclaration()
    {
        return [
            ['array', '5.0', 4, '5.1'],
            ['array', '5.0', 5, '5.1'],
            ['callable', '5.3', 8, '5.4'],
            ['bool', '5.6', 11, '7.0'],
            ['int', '5.6', 12, '7.0'],
            ['float', '5.6', 13, '7.0'],
            ['string', '5.6', 14, '7.0'],
            ['iterable', '7.0', 17, '7.1'],
            ['parent', '5.1', 24, '5.2'],
            ['self', '5.1', 34, '5.2'],
            ['self', '5.1', 37, '5.2', false],
            ['self', '5.1', 41, '5.2'],
            ['self', '5.1', 44, '5.2', false],
            ['callable', '5.3', 52, '5.4'],
            ['int', '5.6', 53, '7.0'],
            ['callable', '5.3', 56, '5.4'],
            ['int', '5.6', 57, '7.0'],
            ['object', '7.1', 60, '7.2'],
            ['parent', '5.1', 63, '5.2', false],
            ['parent', '5.1', 66, '5.2', false],
            ['self', '5.1', 71, '5.2'],
            ['self', '5.1', 72, '5.2', false],
            ['parent', '5.1', 73, '5.2', false],
            ['int', '5.6', 78, '7.0'],
            ['callable', '5.3', 80, '5.4'],
            ['mixed', '7.4', 85, '8.0'],
            ['mixed', '7.4', 88, '8.0', false],
        ];
    }


    /**
     * testInvalidTypeDeclaration
     *
     * @dataProvider dataInvalidTypeDeclaration
     *
     * @param string $type        The scalar type.
     * @param string $alternative Alternative for the invalid type hint.
     * @param int    $line        Line number on which to expect an error.
     *
     * @return void
     */
    public function testInvalidTypeDeclaration($type, $alternative, $line)
    {
        $file = $this->sniffFile(__FILE__, '5.0'); // Lowest version in which this message will show.
        $this->assertError($file, $line, "'{$type}' is not a valid type declaration. Did you mean {$alternative} ?");
    }

    /**
     * Data provider.
     *
     * @see testInvalidTypeDeclaration()
     *
     * @return array
     */
    public function dataInvalidTypeDeclaration()
    {
        return [
            ['boolean', 'bool', 20],
            ['integer', 'int', 21],
            ['static', 'self', 25],
            ['integer', 'int', 81],
        ];
    }


    /**
     * testInvalidSelfTypeDeclaration
     *
     * @dataProvider dataInvalidSelfTypeDeclaration
     *
     * @param int    $line Line number on which to expect an error.
     * @param string $type The invalid type which should be expected.
     *
     * @return void
     */
    public function testInvalidSelfTypeDeclaration($line, $type)
    {
        $file = $this->sniffFile(__FILE__, '5.2'); // Lowest version in which this message will show.
        $this->assertError($file, $line, "'$type' type cannot be used outside of class scope");
    }

    /**
     * Data provider.
     *
     * @see testInvalidSelfTypeDeclaration()
     *
     * @return array
     */
    public function dataInvalidSelfTypeDeclaration()
    {
        return [
            [37, 'self'],
            [44, 'self'],
            [63, 'parent'],
            [66, 'parent'],
            [72, 'self'],
        ];
    }


    /**
     * testInvalidSelfTypeDeclaration
     *
     * @dataProvider dataInvalidSelfTypeDeclarationNoFalsePositives
     *
     * @param int $line Line number on which to expect an error.
     *
     * @return void
     */
    public function testInvalidSelfTypeDeclarationNoFalsePositives($line)
    {
        $file = $this->sniffFile(__FILE__, '5.2');
        $this->assertNoViolation($file, $line);
    }

    /**
     * Data provider.
     *
     * @see testInvalidSelfTypeDeclarationNoFalsePositives()
     *
     * @return array
     */
    public function dataInvalidSelfTypeDeclarationNoFalsePositives()
    {
        return [
            [71],
            [73],
        ];
    }


    /**
     * Verify an error is thrown for nullable mixed types.
     *
     * @return void
     */
    public function testInvalidNullableMixed()
    {
        $file = $this->sniffFile(__FILE__, '8.0');
        $this->assertError($file, 88, 'Mixed types cannot be nullable, null is already part of the mixed type');
    }


    /**
     * Test no false positives for non-nullable "mixed" type.
     *
     * @return void
     */
    public function testInvalidNullableMixedNoFalsePositives()
    {
        $file = $this->sniffFile(__FILE__, '8.0');
        $this->assertNoViolation($file, 85);
    }


    /**
     * testTypeDeclaration
     *
     * @dataProvider dataTypeDeclaration
     *
     * @param int  $line            Line number on which to expect an error.
     * @param bool $testNoViolation Whether or not to test noViolation for PHP 5.0.
     *                              This covers the remaining few cases not covered
     *                              by the above tests.
     *
     * @return void
     */
    public function testTypeDeclaration($line, $testNoViolation = false)
    {
        $file = $this->sniffFile(__FILE__, '4.4');
        $this->assertError($file, $line, 'Type declarations were not present in PHP 4.4 or earlier');

        if ($testNoViolation === true) {
            $file = $this->sniffFile(__FILE__, '5.0');
            $this->assertNoViolation($file, $line);
        }
    }

    /**
     * Data provider.
     *
     * @see testTypeDeclaration()
     *
     * @return array
     */
    public function dataTypeDeclaration()
    {
        return [
            [4],
            [5],
            [8],
            [11],
            [12],
            [13],
            [14],
            [17],
            [20],
            [21],
            [24],
            [25],
            [29, true],
            [30, true],
            [34],
            [37],
            [41],
            [44],
            [52],
            [53],
            [56],
            [57],
            [60],
            [63],
            [66],
            [71],
            [72],
            [73],
            [78],
            [79],
            [80],
            [81],
            [85],
            [88],
        ];
    }


    /**
     * testNoFalsePositives
     *
     * @dataProvider dataNoFalsePositives
     *
     * @param int $line The line number.
     *
     * @return void
     */
    public function testNoFalsePositives($line)
    {
        $file = $this->sniffFile(__FILE__, '4.4'); // Low version below the first addition.
        $this->assertNoViolation($file, $line);
    }

    /**
     * Data provider.
     *
     * @see testNoFalsePositives()
     *
     * @return array
     */
    public function dataNoFalsePositives()
    {
        return [
            [48],
            [49],
            [82],
        ];
    }


    /*
     * `testNoViolationsInFileOnValidVersion` test omitted as this sniff will throw errors
     * for invalid type hints and incorrect usage.
     */
}
