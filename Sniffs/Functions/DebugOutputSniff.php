<?php
/**
 * Cake_Sniffs_Functions_FunctionLineCountSniff
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Tarique Sani <tarique@sanisoft.com>
 * @copyright 2008 SANIsoft Technologies Pvt Ltd http://sanisoft.com/
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @link      http://sanisoft.com/downloads/cakephp_sniffs/
 */

/**
 */
class Cake_Sniffs_Functions_DebugOutputSniff implements PHP_CodeSniffer_Sniff {

    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register() {
        return array(
            T_STRING,
        );

    }

    public $debugOutput = array(
        'var_dump',
        'pr',
    );

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token in the
     *                                        stack passed in $tokens..
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr) {
        $tokens = $phpcsFile->getTokens();

        $currentToken = $tokens[$stackPtr];
        if ($currentToken['code'] === T_STRING) {
            $content = $currentToken['content'];
            if (in_array($content, $this->debugOutput)) {
                $phpcsFile->addError("Active debug output: {$content}", $stackPtr);
            }
        }
    }

}