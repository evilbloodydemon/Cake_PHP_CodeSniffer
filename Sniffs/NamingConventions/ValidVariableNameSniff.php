<?php
/**
 * Cake_Sniffs_NamingConventions_ValidVariableNameSniff.
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

if (class_exists('PHP_CodeSniffer_Standards_AbstractVariableSniff', true) === false) {
    throw new PHP_CodeSniffer_Exception('Class PHP_CodeSniffer_Standards_AbstractVariableSniff not found');
}

/**
 * Cake_Sniffs_NamingConventions_ValidVariableNameSniff.
 *
 * Checks the naming of variables and member variables.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Tarique Sani <tarique@sanisoft.com>
 * @copyright 2008 SANIsoft Technologies Pvt Ltd http://sanisoft.com/
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @link      http://sanisoft.com/downloads/cakephp_sniffs/
 */
class Cake_Sniffs_NamingConventions_ValidVariableNameSniff extends PHP_CodeSniffer_Standards_AbstractVariableSniff
{
    //todo implement camelBack checking

    /**
     * Tokens to ignore so that we can find a DOUBLE_COLON.
     *
     * @var array
     */
    private $_ignore = array(
                        T_WHITESPACE,
                        T_COMMENT,
                       );


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token in the
     *                                        stack passed in $tokens.
     *
     * @return void
     */
    protected function processVariable(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens  = $phpcsFile->getTokens();
        $varName = ltrim($tokens[$stackPtr]['content'], '$');

        $phpReservedVars = array(
                            '_SERVER',
                            '_GET',
                            '_POST',
                            '_REQUEST',
                            '_SESSION',
                            '_ENV',
                            '_COOKIE',
                            '_FILES',
                            'GLOBALS',
                           );

        // If it's a php reserved var, then its ok.
        if (in_array($varName, $phpReservedVars) === true) {
            return;
        }

        $objOperator = $phpcsFile->findNext(array(T_WHITESPACE), ($stackPtr + 1), null, true);
        if ($tokens[$objOperator]['code'] === T_OBJECT_OPERATOR) {
            // Check to see if we are using a variable from an object.
            $var     = $phpcsFile->findNext(array(T_STRING), ($objOperator + 1));
            $bracket = $objOperator = $phpcsFile->findNext(array(T_WHITESPACE), ($var + 1), null, true);

            // This Object variable is not a method ;)
            if ($tokens[$bracket]['code'] !== T_OPEN_PARENTHESIS) {

                $objVarName = $tokens[$var]['content'];

                // There is no way for us to know if the var is public or private,
                // so we have to ignore a leading underscore if there is one and just
                // check the main part of the variable name.
                $originalVarName = $objVarName;
                $objVarName = ltrim($objVarName, '_');

                if (strpos($objVarName, '_')) {
                    $error = "Variable \"$originalVarName\" is not in valid camel caps format";
                    $phpcsFile->addError($error, $var);
                }
            }
        }//end if

        // There is no way for us to know if the var is public or private,
        // so we have to ignore a leading underscore if there is one and just
        // check the main part of the variable name.
        $originalVarName = $varName;
        $varName = ltrim($varName, '_');

        if (strpos($varName, '_')) {
            $error = "Variable \"$originalVarName\" is not in valid camel caps format";
            $phpcsFile->addError($error, $stackPtr);
        }

    }//end processVariable()


    /**
     * Processes class member variables.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token in the
     *                                        stack passed in $tokens.
     *
     * @return void
     */
    protected function processMemberVar(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $varName     = ltrim($tokens[$stackPtr]['content'], '$');
        $memberProps = $phpcsFile->getMemberProperties($stackPtr);
        $public      = ($memberProps['scope'] === 'public');

        if ($public === true) {
            if (substr($varName, 0, 1) === '_') {
                $error = "Public member variable \"$varName\" must not contain a leading underscore";
                $phpcsFile->addError($error, $stackPtr);
                return;
            }
        }

        if ($this->isCamelBack($varName, $public) === false) {
            $error = "Variable \"$varName\" is not in valid camelBack format.";
            $phpcsFile->addError($error, $stackPtr);
        }

    }//end processMemberVar()


    /**
     * Processes the variable found within a double quoted string.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the double quoted
     *                                        string.
     *
     * @return void
     */
    protected function processVariableInString(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $phpReservedVars = array(
                            '_SERVER',
                            '_GET',
                            '_POST',
                            '_REQUEST',
                            '_SESSION',
                            '_ENV',
                            '_COOKIE',
                            '_FILES',
                            'GLOBALS',
                           );

        if (preg_match_all('|[^\\\]\$([a-zA-Z0-9_]+)|', $tokens[$stackPtr]['content'], $matches) !== 0) {
            foreach ($matches[1] as $varName) {
                // If it's a php reserved var, then its ok.
                if (in_array($varName, $phpReservedVars) === true) {
                    continue;
                }

                // There is no way for us to know if the var is public or private,
                // so we have to ignore a leading underscore if there is one and just
                // check the main part of the variable name.
                $originalVarName = $varName;
                if (substr($varName, 0, 1) === '_') {
                    if ($phpcsFile->hasCondition($stackPtr, array(T_CLASS, T_INTERFACE)) === true) {
                        $varName = substr($varName, 1);
                    }
                }

                if ($this->isCamelBack($varName) === false) {
                    $error   = "Variable \"$originalVarName\" is not in valid camelBack format";
                    $phpcsFile->addError($error, $stackPtr);
                }
            }
        }//end if

    }//end processVariableInString()

    protected function isCamelBack($var, $public = true) {
        $regex = '/^[' . ($public ? '' : '_') . 'a-z]+[a-zA-Z0-9]*$/';

        return preg_match($regex, $var);
    }
}//end class

?>
