<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Validate
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */
/**
 * @see Zend_Validate_Abstract
 */
require_once 'Zend/Validate/Abstract.php';

/**
 * @category   Zend
 * @package    Zend_Validate
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class AppCms2_Validate_Nip extends Zend_Validate_Abstract
{
  const LENGTH = 'numLength';
  const CHECKSUM = 'numChecksum';
  protected static $_filter = null;
  protected $_messageTemplates = array(
    self::LENGTH => "'%value%' must contain 10 digits",
    self::CHECKSUM => "Luhn algorithm (mod-11 checksum) failed on '%value%'"
  );

  public function isValid($sValue)
  {
    $this->_setValue($sValue);
    if (null === self::$_filter) {
      require_once 'Zend/Filter/Digits.php';
      self::$_filter = new Zend_Filter_Digits();
    }
    $sValueFiltered = self::$_filter->filter($sValue);
    $nLength = strlen($sValueFiltered);
    if ($nLength != 10) {
      $this->_error(self::LENGTH);
      return false;
    }
    $nMod = 11;
    $nSum = 0;
    $aWeights = array(6, 5, 7, 2, 3, 4, 5, 6, 7);
    preg_match_all("/\d/", $sValueFiltered, $aDigits);
    $aValueFiltered = $aDigits[0];
    foreach ($aValueFiltered as $nDigit) {
      $nWeight = current($aWeights);
      $nSum += $nDigit * $nWeight;
      next($aWeights);
    }
    if ((($nSum % $nMod == 10) ? 0 : $nSum % $nMod) != $aValueFiltered[$nLength - 1]) {
      $this->_error(self::CHECKSUM, $sValueFiltered);
      return false;
    }
    return true;
  }
}

?>
