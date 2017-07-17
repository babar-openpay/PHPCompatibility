<?php
/**
 * Classname determination test file
 *
 * @package PHPCompatibility
 */

namespace PHPCompatibility\Tests\BaseClass;

/**
 * Classname determination function tests
 *
 * @group utilityGetFQClassNameFromNewToken
 * @group utilityFunctions
 *
 * @uses    \PHPCompatibility\Tests\BaseClass\MethodTestFrame
 * @package PHPCompatibility
 * @author  Juliette Reinders Folmer <phpcompatibility_nospam@adviesenzo.nl>
 */
class GetFQClassNameFromNewTokenTest extends MethodTestFrame
{

    /**
     * The file name for the file containing the test cases within the
     * `sniff-examples/utility-functions/` directory.
     *
     * @var string
     */
    protected $filename = 'get_fqclassname_from_new_token.php';

    /**
     * testGetFQClassNameFromNewToken
     *
     * @requires PHP 5.3
     *
     * @dataProvider dataGetFQClassNameFromNewToken
     *
     * @covers \PHPCompatibility\Sniff::getFQClassNameFromNewToken
     *
     * @param string $commentString The comment which prefaces the T_NEW token in the test file.
     * @param string $expected      The expected fully qualified class name.
     *
     * @return void
     */
    public function testGetFQClassNameFromNewToken($commentString, $expected)
    {
        $stackPtr = $this->getTargetToken($commentString, T_NEW);
        $result   = $this->helperClass->getFQClassNameFromNewToken($this->phpcsFile, $stackPtr);
        $this->assertSame($expected, $result);
    }

    /**
     * dataGetFQClassNameFromNewToken
     *
     * @see testGetFQClassNameFromNewToken()
     *
     * @return array
     */
    public function dataGetFQClassNameFromNewToken()
    {
        return array(
            array('/* Case 1 */', '\DateTime'),
            array('/* Case 2 */', '\MyTesting\DateTime'),
            array('/* Case 3 */', '\MyTesting\DateTime'),
            array('/* Case 4 */', '\DateTime'),
            array('/* Case 5 */', '\MyTesting\anotherNS\DateTime'),
            array('/* Case 6 */', '\FQNS\DateTime'),
            array('/* Case 7 */', '\AnotherTesting\DateTime'),
            array('/* Case 8 */', '\AnotherTesting\DateTime'),
            array('/* Case 9 */', '\DateTime'),
            array('/* Case 10 */', '\AnotherTesting\anotherNS\DateTime'),
            array('/* Case 11 */', '\FQNS\DateTime'),
            array('/* Case 12 */', '\DateTime'),
            array('/* Case 13 */', '\DateTime'),
            array('/* Case 14 */', '\AnotherTesting\DateTime'),
            array('/* Case 15 */', ''),
            array('/* Case 16 */', ''),
        );
    }

}
