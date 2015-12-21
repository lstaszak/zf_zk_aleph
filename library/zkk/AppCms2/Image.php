<?php

class AppCms2_Image
{
  private $rRes = false;
  private $nWidth = 0;
  private $nHeight = 0;
  private $sMimeType = '';
  private $sImageType = '';
  private $aImageTypes = array('jpg', 'jpeg', 'gif', 'png');
  private $aMimeTypes = array(
    'image/gif' => 'gif',
    'image/jpeg' => 'jpg',
    'image/pjpeg' => 'jpg',
    'image/jpg' => 'jpg',
    'image/x-png' => 'png',
    'image/png' => 'png',
    'application/zip' => 'zip'
  );
  private $nJpegQuality = 100;
  private $sError = '';

  public function __construct()
  {
  }

  public function getImageType()
  {
    return $this->sImageType;
  }

  public function setImageType($sMimeType)
  {
    return $this->aMimeTypes[$sMimeType];
  }

  public function load($sFileName)
  {
    if (!file_exists($sFileName)) {
      return false;
    }
    $aImageData = @getimagesize($sFileName);
    $this->sMimeType = $aImageData['mime'];
    $this->sImageType = $this->aMimeTypes[$aImageData['mime']];
    switch ($this->sImageType) {
      case 'jpg':
      case 'jpeg':
        $this->rRes = @imagecreatefromjpeg($sFileName);
        break;
      case 'gif':
        $this->rRes = @imagecreatefromgif($sFileName);
        break;
      case 'png':
        $this->rRes = @imagecreatefrompng($sFileName);
        imagealphablending($this->rRes, false);
        imagesavealpha($this->rRes, true);
        break;
    }
    if (!$this->rRes) {
      return false;
    }
    $this->update();
    return true;
  }

  public function save($sFileName)
  {
    $sExt = strtolower(array_pop(explode('.', $sFileName)));
    if (!$sExt) {
      return false;
    }
    if (!in_array($sExt, $this->aImageTypes)) {
      return false;
    }
    $bResult = false;
    switch ($sExt) {
      case 'jpg':
      case 'jpeg':
        $bResult = imagejpeg($this->rRes, $sFileName, $this->nJpegQuality);
        break;
      case 'gif':
        $bResult = imagegif($this->rRes, $sFileName);
        break;
      case 'png':
        imagealphablending($this->rRes, false);
        imagesavealpha($this->rRes, true);
        $bResult = imagepng($this->rRes, $sFileName);
        break;
    }
    if (!$bResult) {
      return false;
    }
    return true;
  }

  private function update()
  {
    $this->nWidth = imagesx($this->rRes);
    $this->nHeight = imagesy($this->rRes);
  }

  public function zoom($nNewWidth, $nNewHeight)
  {
    $nCurrWidth = $this->nWidth;
    $nCurrHeight = $this->nHeight;
    if (($nCurrWidth < $nNewWidth) || ($nCurrHeight < $nNewHeight))
      return $this->resize($nNewWidth, $nNewHeight);
    $nX = $nCurrWidth / $nNewWidth;
    $nY = $nCurrHeight / $nNewHeight;
    if ($nX > $nY) {
      $bResult = $this->height($nNewHeight);
      if (!$bResult) {
        return false;
      }
      $nTempWidth = round($nCurrWidth * $nNewHeight / $nCurrHeight);
      $src_x = round(($nTempWidth - $nNewWidth) / 2);
      return $this->crop($src_x, 0, $nNewWidth, $nNewHeight);
    } elseif ($nY > $nX) {
      $bResult = $this->width($nNewWidth);
      if (!$bResult) {
        return false;
      }
      $nTempHeight = round($nCurrHeight * $nNewWidth / $nCurrWidth);
      $src_y = round(($nTempHeight - $nNewHeight) / 2);
      return $this->crop(0, $src_y, $nNewWidth, $nNewHeight);
    } else
      return $this->resize($nNewWidth, $nNewHeight);
  }

  private function resize($nNewWidth, $nNewHeight)
  {
    $nCurrWidth = $this->nWidth;
    $nCurrHeight = $this->nHeight;
    $nX = $nCurrWidth / $nNewWidth;
    $nY = $nCurrHeight / $nNewHeight;
    if ($nX < $nY) {
      if ($nCurrHeight <= $nNewHeight)
        return true;
      return $this->height($nNewHeight);
    } else {
      if ($nCurrWidth <= $nNewWidth)
        return true;
      return $this->width($nNewWidth);
    }
  }

  private function width($nWidth)
  {
    if ($this->nWidth <= $nWidth)
      return true;
    $nHeight = round($nWidth / $this->nWidth * $this->nHeight);
    $rImage = $this->createBackground($nWidth, $nHeight);
    if (!$rImage) {
      return false;
    }
    $bResult = imagecopyresampled($rImage, $this->rRes, 0, 0, 0, 0, $nWidth, $nHeight, $this->nWidth, $this->nHeight);
    if (!$bResult) {
      return false;
    }
    $this->rRes = $rImage;
    $this->update();
    return true;
  }

  private function height($nHeight)
  {
    if ($this->nHeight <= $nHeight)
      return true;
    $nWidth = round($nHeight / $this->nHeight * $this->nWidth);
    $rImage = $this->createBackground($nWidth, $nHeight);
    if (!$rImage) {
      return false;
    }
    $bResult = imagecopyresampled($rImage, $this->rRes, 0, 0, 0, 0, $nWidth, $nHeight, $this->nWidth, $this->nHeight);
    if (!$bResult) {
      return false;
    }
    $this->rRes = $rImage;
    $this->update();
    return true;
  }

  private function createBackground($nWidth, $nHeight)
  {
    $rImage = imagecreatetruecolor($nWidth, $nHeight);
    if (!$rImage) {
      return false;
    }
    imagealphablending($rImage, false);
    imagesavealpha($rImage, true);
    $nTransparent = imagecolorallocatealpha($rImage, 255, 255, 255, 127);
    imagefilledrectangle($rImage, 0, 0, $nWidth, $nHeight, $nTransparent);
    return $rImage;
  }

  private function crop($nX, $nY, $nWidth, $nHeight)
  {
    $rImage = $this->createBackground($nWidth, $nHeight);
    if (!$rImage) {
      return false;
    }
    $bResult = imagecopyresampled($rImage, $this->rRes, 0, 0, $nX, $nY, $nWidth, $nHeight, $nWidth, $nHeight);
    if (!$bResult) {
      return false;
    }
    $this->rRes = $rImage;
    $this->update();
    return true;
  }

  public function makeMiniature($nNewWidth, $nNewHeight)
  {
    $nWidth = $this->nWidth;
    $nHeight = $this->nHeight;
    $nXScale = $nWidth / $nNewWidth;
    $nYScale = $nHeight / $nNewHeight;
    if ($nYScale > $nXScale) {
      $nTempWidth = round($nWidth * (1 / $nYScale));
      $nTempHeight = round($nHeight * (1 / $nYScale));
    } else {
      $nTempWidth = round($nWidth * (1 / $nXScale));
      $nTempHeight = round($nHeight * (1 / $nXScale));
    }
    $rImage = imagecreatetruecolor($nTempWidth, $nTempHeight);
    imagecopyresampled($rImage, $this->rRes, 0, 0, 0, 0, $nTempWidth, $nTempHeight, $nWidth, $nHeight);
    $this->rRes = $rImage;
    $this->update();
    return true;
  }

  public function makeCrop($nX, $nY, $nNewWidth, $nNewHeight)
  {
    $rImage = imagecreatetruecolor($nNewWidth, $nNewHeight);
    imagecopyresampled($rImage, $this->rRes, 0, 0, $nX, $nY, $nNewWidth, $nNewHeight, $nNewWidth, $nNewHeight);
    $this->rRes = $rImage;
    $this->update();
    return true;
  }

  public function getWidth()
  {
    return $this->nWidth;
  }

  public function getHeight()
  {
    return $this->nHeight;
  }

  public function px2cm($image, $dpi)
  {
    $rImg = ImageCreateFromJpeg($image);
    $nX = ImageSX($rImg);
    $nY = ImageSY($rImg);
    $nH = $nX * 2.54 / $dpi;
    $nL = $nY * 2.54 / $dpi;
    //$nH = number_format($nH, 2, ',', ' ');
    //$nL = number_format($nL, 2, ',', ' ');
    $aResult[] = $nH;
    $aResult[] = $nL;
    return $aResult;
  }

  public function cm2px($aHL, $nDpi)
  {
    $nX = $aHL[0] * $nDpi / 2.54;
    $nY = $aHL[1] * $nDpi / 2.54;
    $aResult[] = $nX;
    $aResult[] = $nY;
    return $aResult;
  }
}

?>
