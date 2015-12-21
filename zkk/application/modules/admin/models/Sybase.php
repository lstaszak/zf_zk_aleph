<?php

class Admin_Model_Sybase extends AppCms2_Controller_Plugin_TableAbstractSybase
{

  protected static $_instance = null;
  protected $_aLog;

  public function __construct($config = array())
  {
    $this->_aLog = array();
    parent::__construct($config);
  }

  public static function getInstance()
  {
    if (null === self::$_instance) {
      self::$_instance = new self();
    }
    return self::$_instance;
  }

  public function checkBorrowerLogin($nBBarcodeId, $sPesel)
  {
    $sQuery = "SELECT TOP 1 b.borrower# FROM borrower_barcode bb
    INNER JOIN borrower b ON (bb.borrower# = b.borrower#)
    WHERE bb.bbarcode = '$nBBarcodeId' AND b.second_id = '$sPesel'";
    $aRow = $this->_db->fetchRow($sQuery);
    return $aRow;
  }

  public function getBorrowerInfo($nBBarcodeId)
  {
    $sQuery = "SELECT TOP 1 b.borrower#, b.name_reconstructed, b.second_id, ba.email_address, bb.bbarcode, b.btype, b.expiration_date FROM borrower_barcode bb
    INNER JOIN borrower b ON (bb.borrower# = b.borrower#) 
    LEFT JOIN borrower_address ba ON (bb.borrower# = ba.borrower#)
    WHERE bb.bbarcode = '$nBBarcodeId'";
    $aRow = $this->_db->fetchRow($sQuery);
    return $aRow;
  }

  public function getBorrowerInfoImport($nBBarcodeId)
  {
    $sQuery = "SELECT TOP 1 b.borrower#, b.name_reconstructed, b.second_id, ba.email_address, bb.bbarcode, b.btype, b.expiration_date FROM borrower_barcode bb
    INNER JOIN borrower b ON (bb.borrower# = b.borrower#) 
    INNER JOIN borrower_address ba ON (bb.borrower# = ba.borrower#)
    WHERE bb.bbarcode = '$nBBarcodeId'";
    $aRow = $this->_db->fetchRow($sQuery);
    return $aRow;
  }

  public function getBorrowerRealBType($nBorrowerId)
  {
    $sQuery = "SELECT TOP 1 borrower_title FROM borrower WHERE borrower# = $nBorrowerId";
    $aRow = $this->_db->fetchRow($sQuery);
    return $aRow["borrower_title"];
  }

  public function getBorrowerName($nBorrowerId)
  {
    $sQuery = "SELECT name_reconstructed FROM borrower WHERE borrower# = $nBorrowerId";
    $aRow = $this->_db->fetchRow($sQuery);
    return $aRow;
  }

  public function getBorrowerBBarcode($nBBarcodeId)
  {
    $sQuery = "SELECT TOP 1 borrower# FROM borrower_barcode WHERE bbarcode = '$nBBarcodeId'";
    $aRow = $this->_db->fetchRow($sQuery);
    return $aRow;
  }

  public function findBorrowerBBarcode($nBBarcodeId)
  {
    $aRow = false;
    $sQuery = "SELECT TOP 1 borrower# FROM borrower_barcode WHERE bbarcode = '$nBBarcodeId'";
    $aRow = $this->_db->fetchRow($sQuery);
    if ($aRow) {
      return true;
    } else {
      return false;
    }
  }

  public function getBorrowerSecurityGroup($nBorrowerId)
  {
    $sQuery = "SELECT security_group FROM borrower_911 WHERE borrower# = $nBorrowerId ORDER BY security_group DESC";
    $aRow = $this->_db->fetchAll($sQuery);
    return $aRow;
  }

  public function getBorrowerBstat($nBorrowerId)
  {
    $sQuery = "SELECT TOP 1 bstat FROM borrower_bstat WHERE borrower# = $nBorrowerId";
    $aRow = $this->_db->fetchRow($sQuery);
    return $aRow["bstat"];
  }

  public function getDueDateName($sDueDate)
  {
    $sSelect = "SELECT dateadd (dd, $sDueDate, '1970-01-01')";
    $aRowset = $this->_db->fetchAll($sSelect);
    return $aRowset[0]['computed'];
  }

  public function getToday()
  {
    $sSelect = "SELECT DATEDIFF(day, '1970-01-01', getdate())";
    $aRowset = $this->_db->fetchAll($sSelect);
    return $aRowset[0]['computed'];
  }

  public function getBorrowerItems($sBorrowerId, $sToday, $bFindInOtherLocation = false)
  {
    $sCkoLocation = 'BU-UAM';
    $sSelect = "SELECT item#, ibarcode, (($sToday) - (SELECT due_date FROM item WHERE item# = i.item#) - (SELECT count(*) FROM calendar_exception WHERE location = '$sCkoLocation' AND date between (SELECT due_date FROM item WHERE item# = i.item#) AND $sToday)) days, due_date, (SELECT processed FROM item_with_title WHERE item# = i.item#) title, item#, cko_location, n_renewals, n_opac_renewals FROM item i WHERE cko_location = '$sCkoLocation' AND borrower# = $sBorrowerId AND due_date >= $sToday ORDER BY due_date";
    if (!$bFindInOtherLocation) {
      $sSelect = "SELECT item#, ibarcode, (($sToday) - (SELECT due_date FROM item WHERE item# = i.item#) - (SELECT count(*) FROM calendar_exception WHERE location = '$sCkoLocation' AND date between (SELECT due_date FROM item WHERE item# = i.item#) AND $sToday)) days, due_date, (SELECT processed FROM item_with_title WHERE item# = i.item#) title, item#, cko_location, n_renewals, n_opac_renewals FROM item i WHERE cko_location = '$sCkoLocation' AND borrower# = $sBorrowerId AND due_date >= $sToday ORDER BY due_date";
    } else {
      $sSelect = "SELECT item#, ibarcode, (($sToday) - (SELECT due_date FROM item WHERE item# = i.item#) - (SELECT count(*) FROM calendar_exception WHERE location = '$sCkoLocation' AND date between (SELECT due_date FROM item WHERE item# = i.item#) AND $sToday)) days, due_date, (SELECT processed FROM item_with_title WHERE item# = i.item#) title, item#, cko_location, n_renewals, n_opac_renewals FROM item i WHERE cko_location NOT IN ('BC_1', 'BC_2', 'BC_3') AND borrower# = $sBorrowerId AND due_date >= $sToday ORDER BY due_date";
    }
    $aRowset = $this->_db->fetchAll($sSelect);
    return $aRowset;
  }

  public function getCalculateFine($sBorrowerId, $sToday, $bFindInOtherLocation = false)
  {
    $sCkoLocation = 'BU-UAM';
    $sSelect = "SELECT item#, ibarcode, (($sToday) - (SELECT due_date FROM item WHERE item# = i.item#) - (SELECT count(*) FROM calendar_exception WHERE location = '$sCkoLocation' AND date between (SELECT due_date FROM item WHERE item# = i.item#) AND $sToday)) days, due_date, (SELECT processed FROM item_with_title WHERE item# = i.item#) title, item#, cko_location, n_renewals, n_opac_renewals FROM item i WHERE cko_location = '$sCkoLocation' AND borrower# = $sBorrowerId AND due_date < $sToday";
    if (!$bFindInOtherLocation) {
      $sSelect = "SELECT item#, ibarcode, (($sToday) - (SELECT due_date FROM item WHERE item# = i.item#) - (SELECT count(*) FROM calendar_exception WHERE location = '$sCkoLocation' AND date between (SELECT due_date FROM item WHERE item# = i.item#) AND $sToday)) days, due_date, (SELECT processed FROM item_with_title WHERE item# = i.item#) title, item#, cko_location, n_renewals, n_opac_renewals FROM item i WHERE cko_location = '$sCkoLocation' AND borrower# = $sBorrowerId AND due_date < $sToday";
    } else {
      $sSelect = "SELECT item#, ibarcode, (($sToday) - (SELECT due_date FROM item WHERE item# = i.item#) - (SELECT count(*) FROM calendar_exception WHERE location = (SELECT location FROM item WHERE item# = i.item#) AND date between (SELECT due_date FROM item WHERE item# = i.item#) AND $sToday)) days, due_date, (SELECT processed FROM item_with_title WHERE item# = i.item#) title, item#, cko_location, n_renewals, n_opac_renewals FROM item i WHERE cko_location NOT IN ('BC_1', 'BC_2', 'BC_3') AND borrower# = $sBorrowerId AND due_date < $sToday";
    }
    $aRowset = $this->_db->fetchAll($sSelect);
    return $aRowset;
  }

  public function getBurbFine($sBorrowerId, $bFindInOtherLocation = false)
  {
    $sCkoLocation = 'BU-UAM';
    if (!$bFindInOtherLocation) {
      $sSelect = "SELECT b.item#, i.ibarcode, (SELECT processed FROM item_with_title WHERE item# = b.item#) title, SUM(b.amount) burb_fine, b.item_cko_location FROM burb b INNER JOIN item i ON(b.item# = i.item#) WHERE b.item_cko_location = '$sCkoLocation' AND b.borrower# = $sBorrowerId AND b.block LIKE 'fine' GROUP BY b.item#, b.item_cko_location";
    } else {
      $sSelect = "SELECT b.item#, i.ibarcode, (SELECT processed FROM item_with_title WHERE item# = b.item#) title, SUM(b.amount) burb_fine, b.item_cko_location FROM burb b INNER JOIN item i ON(b.item# = i.item#) WHERE b.item_cko_location NOT IN ('BC_1', 'BC_2', 'BC_3') AND b.borrower# = $sBorrowerId AND b.block LIKE 'fine' GROUP BY b.item#, b.item_cko_location";
    }
    $aRowset = $this->_db->fetchAll($sSelect);
    return $aRowset;
  }

  public function getBurbFee($sBorrowerId, $bFindInOtherLocation = false)
  {
    $sCkoLocation = 'BU-UAM';
    if (!$bFindInOtherLocation) {
      $sSelect = "SELECT b.*, i.ibarcode FROM burb b INNER JOIN item i ON(b.item# = i.item#) WHERE b.borrower# = $sBorrowerId AND b.block LIKE 'fee' AND b.trans_location = '$sCkoLocation'";
    } else {
      $sSelect = "SELECT b.*, i.ibarcode FROM burb b INNER JOIN item i ON(b.item# = i.item#) WHERE b.borrower# = $sBorrowerId AND b.block LIKE 'fee' AND b.trans_location NOT IN ('BC_1', 'BC_2', 'BC_3')";
    }
    $aRowset = $this->_db->fetchAll($sSelect);
    return $aRowset;
  }

  public function getBurbAdjcr($sBorrowerId, $nItem, $bFindInOtherLocation = false)
  {
    $sCkoLocation = 'BU-UAM';
    if (!$bFindInOtherLocation) {
      $sSelect = "SELECT b.*, i.ibarcode FROM burb b INNER JOIN item i ON(b.item# = i.item#) WHERE b.borrower# = $sBorrowerId AND b.item# = $nItem AND (b.block LIKE 'adjcr' OR b.block LIKE 'adjdbt' OR b.block LIKE 'payment') AND b.trans_location = '$sCkoLocation'";
    } else {
      $sSelect = "SELECT b.*, i.ibarcode FROM burb b INNER JOIN item i ON(b.item# = i.item#) WHERE b.borrower# = $sBorrowerId AND b.item# = $nItem AND (b.block LIKE 'adjcr' OR b.block LIKE 'adjdbt' OR b.block LIKE 'payment') AND b.trans_location NOT IN ('BC_1', 'BC_2', 'BC_3')";
    }
    $aRowset = $this->_db->fetchAll($sSelect);
    return $aRowset;
  }

  public function getAll($sBorrowerId, $bFindInOtherLocation = false)
  {
    $aResult = array();
    $aResultBurbFee = array();
    $nUserFine = 0;
    $sToday = $this->getToday();
    $aBorrowerItems = $this->getBorrowerItems($sBorrowerId, $sToday, $bFindInOtherLocation);
    $aBurbFine = $this->getBurbFine($sBorrowerId, $bFindInOtherLocation);
    $aBurbFee = $this->getBurbFee($sBorrowerId, $bFindInOtherLocation);
    $aCalculatedFine = $this->getCalculateFine($sBorrowerId, $sToday, $bFindInOtherLocation);
    foreach ($aBorrowerItems as $nKey => $aValue) {
      $aResult["borrower_items"][$aValue['item#']]['item#'] = $aValue['item#'];
      $aResult["borrower_items"][$aValue['item#']]['ibarcode'] = $aValue['ibarcode'];
      $aResult["borrower_items"][$aValue['item#']]['title'] = $aValue['title'];
      $aResult["borrower_items"][$aValue['item#']]['due_date'] = $this->pldate("d F Y", (int)$aValue['due_date'] * 24 * 3600);
      $aResult["borrower_items"][$aValue['item#']]['days'] = $aValue['days'];
      $aResult["borrower_items"][$aValue['item#']]['loc'] = $aValue['cko_location'];
      $aResult["borrower_items"][$aValue['item#']]['n_renewals'] = $aValue['n_renewals'];
      $aResult["borrower_items"][$aValue['item#']]['n_opac_renewals'] = $aValue['n_opac_renewals'];
    }
    foreach ($aBurbFine as $nKey => $aValue) {
      $nBurbAdjcr = 0;
      $aBurbAdjcr = $this->getBurbAdjcr($sBorrowerId, $aValue['item#'], $bFindInOtherLocation);
      if ($aBurbAdjcr) {
        foreach ($aBurbAdjcr as $aValue1) {
          $nBurbAdjcr += (int)$aValue1["amount"];
        }
      }
      $aResult[$aValue['item#']]['item#'] = $aValue['item#'];
      $aResult[$aValue['item#']]['title'] = $aValue['title'];
      $aResult[$aValue['item#']]['due_date'] = (int)0;
      $aResult[$aValue['item#']]['burb_fine'] += (int)$aValue['burb_fine'] + (int)$nBurbAdjcr;
      $aResult[$aValue['item#']]['fine'] += (int)0;
      $aResult[$aValue['item#']]['loc'] = $aValue['item_cko_location'];
    }
    foreach ($aBurbFee as $nKey => $aValue) {
      $aResultBurbFee[$nKey]['amount'] = (int)$aValue['amount'];
      $aResultBurbFee[$nKey]['title'] = "Inne opłaty...";
      $aResultBurbFee[$nKey]['loc'] = $aValue['trans_location'];
    }
    foreach ($aCalculatedFine as $nKey => $aValue) {
      if ($aValue['days'] > 0) {
        $aResult[$aValue['item#']]['item#'] = $aValue['item#'];
        $aResult[$aValue['item#']]['ibarcode'] = $aValue['ibarcode'];
        $aResult[$aValue['item#']]['title'] = $aValue['title'];
        $aResult[$aValue['item#']]['due_date'] = $this->pldate("d F Y", (int)$aValue['due_date'] * 24 * 3600);
        $aResult[$aValue['item#']]['burb_fine'] += (int)0;
        $aResult[$aValue['item#']]['fine'] += (int)($aValue['days'] * 30);
        $aResult[$aValue['item#']]['loc'] = $aValue['cko_location'];
        $aResult[$aValue['item#']]['n_renewals'] = $aValue['n_renewals'];
        $aResult[$aValue['item#']]['n_opac_renewals'] = $aValue['n_opac_renewals'];
      }
    }
    foreach ($aResult as $nKey => $aValue) {
      $nUserFine += $aValue['burb_fine'];
      $nUserFine += $aValue['fine'];
    }
    foreach ($aResultBurbFee as $nKey => $aValue) {
      $nUserFine += $aValue['amount'];
    }

    $aResult['burb_fee'] = $aResultBurbFee;
    $aResult['total_fine'] = $nUserFine;
    return $aResult;
  }

  public function translateLoaction($sLocation)
  {
    $aLocation = array(
      'AFUAM' => 'Biblioteka Alliance Francaise ul. 28 Czerwca 1956 r. nr 198',
      'BAUAM' => 'Biblioteka Wydziału Biologii ul. Umultowska 89',
      'BC_1' => 'Czytelnia Ogólna',
      'BC_2' => 'Czytelnia Nauk Historycznych Społecznych i Gazet',
      'BC_3' => 'Czytelnia Nauk Prawnych i Ekonomicznych',
      'BNUAM' => 'Biblioteka Niemiecka',
      'BU-UAM' => 'Biblioteka Uniwersytecka ul. Ratajczaka 38/40',
      'CAUAM' => 'Biblioteka Wydziału Chemii ul. Umultowska 89b',
      'FAUAM' => 'Biblioteka Wydziału Fizyki ul. Umultowska 85',
      'FBUAM' => 'to zawsze lokalizacja egzemplarza',
      'FCUAM' => 'Biblioteka Obserwatorium Astronomicznego ul. Słoneczna 36',
      'GAUAM' => 'Biblioteka Wydziału Nauk Geograficznych i Geologicznych ul. Dzięgielowa 27',
      'GBUAM' => 'Biblioteka Instytutu Geologii ul. Maków Polnych 16',
      'HAUAM' => 'Biblioteka Instytutu Historii ul. Św. Marcin 78',
      'HBUAM' => 'Biblioteka Instytutu Etnologii i Antropologii Kulturowej ul. Św. Marcin 78',
      'HCUAM' => 'Biblioteka Instytutu Historii Sztuki Al. Niepodległości 4',
      'HDUAM' => 'Biblioteka Instytutu Prahistorii ul. Św. Marcin 78',
      'HMUAM' => 'Biblioteka Katedry Muzykologii ul. Słowackiego 20',
      'JCUAM' => 'Austriacki Ośrodek Kultury. Centrum Egzaminacyjne ÖSD ul. Zwierzyniecka 7',
      'JDUAM' => 'Biblioteka Ogrodu Botanicznego',
      'JFUAM' => 'Biblioteka Studium Języka Angielskiego UAM',
      'JGUAM' => 'Biblioteka Collegium Europeaum Gniezno, ul. Kostrzewskiego 5/7',
      'JHUAM' => 'Stacja Ekologiczna - Jeziorsko',
      'JKUAM' => 'Szkoła Tłumaczy i Języków Obcych ul. 28 Czerwca 1956 nr 198',
      'KAUAM' => 'Biblioteka Wydziału Pedagogiczno-Artystycznego Ul. Nowy Świat 28-30 Kalisz',
      'KJUAM' => 'Kolegium Języków Obcych UAM ul. Międzychodzka 5',
      'KRUAM' => 'Biblioteka UAM w Krotoszynie',
      'LBUAM' => 'Biblioteka Instytutu Filologii Germańskiej Al. Niepodległości 4',
      'LCUAM' => 'Biblioteka Instytutu Filologii Romańskiej',
      'LDUAM' => 'Biblioteka Instytutu Filologii Rosyjskiej Al. Niepodległości 4',
      'LEUAM' => 'Czytelnia Instytutu Lingwistyki Stosowanej ul. 28 Czerwca 1956 nr 198',
      'LFUAM' => 'Biblioteka Instytutu Językoznawstwa ul. Międzychodzka 5',
      'LGUAM' => 'Biblioteka Katedry Skandynawistyki Al. Niepodległości 4',
      'LHUAM' => 'Biblioteka Orientalistyki ul. Międzychodzka 5',
      'LKUAM' => 'Biblioteka Katedry Ekokomunikacji ul. 28 Czerwca 1956 nr 198',
      'LNUAM' => 'Biblioteka Filologiczna Novum Al. Niepodległości 4',
      'LXUAM' => 'Biblioteka Instytutu Filologii Angielskiej Al. Niepodległości 4',
      'MAUAM' => 'Biblioteka Wydziału Matematyki i Informatyki ul. Umultowska 87',
      'NAUAM' => 'Dwuwydziałowa Biblioteka Nauk Społecznych ul. Szamarzewskiego 91',
      'NPUAM' => 'Biblioteka Wydziału Nauk Politycznych i Dziennikarstwa ul.Umultowska 89 a',
      'OAUAM' => 'Biblioteka Studium Nauczania Języków Obcych ul. 28 Czerwca 1956 nr 198',
      'PAUAM' => 'Biblioteka Instytutu Filologii Polskiej Al. Niepodległości 4',
      'PBUAM' => 'Biblioteka Instytutu Filologii Klasycznej Al. Niepodległości 4',
      'PCUAM' => 'Biblioteka Katedry Filologii Słowiańskiej Al. Niepodległości 4',
      'PI' => 'Punkt Informacyjny',
      'PIUAM' => 'Biblioteka UAM w Pile ul. Kołobrzeska 15',
      'PWUAM' => 'Biblioteka Wydziału Filologii Polskiej i Klasycznej ul. Fredry 10',
      'RAUAM' => 'Biblioteka Wydziału Prawa i Administracji ul. Św. Marcin 90',
      'SRUAM' => 'Biblioteka UAM w Śremie',
      'TMHW' => 'Towarzystwo Muzyczne im.H.Wieniawskiego'
    );
    return $aLocation[$sLocation];
  }

  public function pldate($format = 'j F Y', $date = False)
  {
    $date = (is_numeric($date) ? $date : time());
    $m = array(1 => 'styczeń', 'luty', 'marzec', 'kwiecień', 'maj', 'czerwiec',
      'lipiec', 'sierpień', 'wrzesień', 'październik', 'listopad', 'grudzień');
    $ms = array(1 => 'sty', 'lut', 'mar', 'kwi', 'maj', 'cze',
      'lip', 'sie', 'wrz', 'paź', 'lis', 'gru');
    $md = array(1 => 'styczeń', 'luty', 'marzec', 'kwiecień', 'maj', 'czerwiec',
      'lipiec', 'sierpień', 'wrzesień', 'październik', 'listopad', 'grudzień');
    $d = array(0 => 'Niedziela', 'Poniedziałek', 'Wtorek', 'Środa', 'Czwartek', 'Piątek', 'Sobota');
    $ds = array(0 => 'Nd', 'Pn', 'Wt', 'Śr', 'Cz', 'Pt', 'So');
    $f = str_split($format);
    for ($i = 0; $i < count($f); $i++) {
      if ($f[$i] != "\\") {
        switch ($f[$i]) {
          case 'F':
            $f[$i] = $md[date('n', $date)];
            break;
          case 'f':
            $f[$i] = $m[date('n', $date)];
            break;
          case 'M':
            $f[$i] = $ms[date('n', $date)];
            break;
          case 'l':
            $f[$i] = $d[date('w', $date)];
            break;
          case 'D':
            $f[$i] = $ds[date('w', $date)];
            break;
          default:
            $f[$i] = date($f[$i], $date);
        }
      } else {
        $f[$i] = '';
        $i++;
      }
    }
    return implode('', $f);
  }

  public function changeAddressEmail($sBorrowerId, $sValue)
  {
    $sUpdate = "UPDATE borrower_address SET email_address = '$sValue' WHERE borrower# = $sBorrowerId";
    $this->_db->fetchAll($sUpdate);
    return true;
  }

  public function changeSecondId($sBorrowerId, $sValue)
  {
    $sUpdate = "UPDATE borrower SET second_id = '$sValue' WHERE borrower# = $sBorrowerId";
    $this->_db->fetchAll($sUpdate);
    return true;
  }

  public function getItemInfo($sIBarcodeId)
  {
    $sSelect = "SELECT TOP 1 i.item#, i.call, i.ibarcode, i.collection, i.itype, i.invno, iwt.processed FROM item i INNER JOIN item_with_title iwt ON (i.item# = iwt.item#) WHERE i.ibarcode LIKE '$sIBarcodeId'";
    $aRow = $this->_db->fetchRow($sSelect);
    return $aRow;
  }

  public function getCsaRequest($sBorrowerId)
  {
    $aResult = array();
    $sSelect = "SELECT cr.csa_request_id, cr.borrower#, cr.item#, cr.req_status, cr.pickup_area_id, convert(char(12), cr.avail_date, 4) avail_date, iwt.processed FROM csa_request cr INNER JOIN item_with_title iwt ON (cr.item# = iwt.item#) WHERE cr.borrower# = $sBorrowerId";
    $aCsaRequest = $this->_db->fetchAll($sSelect);
    foreach ($aCsaRequest as $nKey => $aValue) {
      $aResult[$aValue['item#']]['item#'] = $aValue['item#'];
      $aResult[$aValue['item#']]['csa_request_id'] = $aValue['csa_request_id'];
      $aResult[$aValue['item#']]['req_status_id'] = $aValue['req_status'];
      $aResult[$aValue['item#']]['req_status_name'] = $this->translateReqStatusId($aValue['req_status']);
      $aResult[$aValue['item#']]['pickup_area_id'] = (int)$aValue['pickup_area_id'];
      $aResult[$aValue['item#']]['pickup_area_name'] = $this->translatePickupAreaId((int)$aValue['pickup_area_id']);
      $aResult[$aValue['item#']]['title'] = iconv("CP852", "UTF-8//TRANSLIT", $aValue['processed']);
      $aResult[$aValue['item#']]['avail_date'] = $aValue['avail_date'];
    }
    return $aResult;
  }

  public function translatePickupAreaId($mPickupAreaId)
  {
    if (is_string($mPickupAreaId)) {
      switch ($mPickupAreaId) {
        case "BAUAM":
          $sLocation = "Bibliotece Wydziału Biologii";
          break;
        case "BU-UAM":
          $sLocation = "Wypożyczalni Biblioteki Uniwersyteckiej";
          break;
        case "CAUAM":
          $sLocation = "Bibliotece Wydziału Chemii";
          break;
        case "GAUAM":
          $sLocation = "Bibliotece Wydziału Geografii i Geologii";
          break;
        case "HAUAM":
          $sLocation = "Bibliotece Instytutu Historii";
          break;
        case "HDUAM":
          $sLocation = "Bibliotece Instytutu Prahistorii";
          break;
        case "HMUAM":
          $sLocation = "Bibliotece Katedry Muzykologii";
          break;
        case "JGUAM":
          $sLocation = "Collegium Europeaum";
          break;
        case "LNUAM":
          $sLocation = "Bibliotece Wydziału Neofilologii";
          break;
        case "MAUAM":
          $sLocation = "Bibliotece Wydziału Matematyki";
          break;
        case "NAUAM":
          $sLocation = "Dwuwydziałowej Bibliotece Nauk Społecznych";
          break;
        case "NPUAM":
          $sLocation = "Bibliotece Wydziału Nauk Politycznych i Dziennikarstwa";
          break;
        case "RAUAM":
          $sLocation = "Bibliotece Wydziału Prawa i Administracji";
          break;
        case "JDUAM":
          $sLocation = "Bibliotece Ogrodu Botanicznego";
          break;
        case "SRUAM":
          $sLocation = "Bibliotece UAM w Śremie";
          break;
        case "KAUAM":
          $sLocation = "Bibliotece Wydziału Pedagogiczno-Artystycznego w Kaliszu";
          break;
      }
    } else if (is_numeric($mPickupAreaId)) {
      switch ($mPickupAreaId) {
        case 3:
          $sLocation = "Wypożyczalnia Biblioteki Uniwersyteckiej";
          break;
        case 10:
          $sLocation = "Czytelnia Ogólnej i Nauk Filologicznych Biblioteki Uniwersyteckiej";
          break;
        case 11:
          $sLocation = "Czytelnia Nauk Historycznych, Społecznych i Gazet Biblioteki Uniwersyteckiej";
          break;
        case 12:
          $sLocation = "Czytelnia Nauk Prawnych i Ekonomicznych Biblioteki Uniwersyteckiej";
          break;
        case 4:
          $sLocation = "Dwuwydziałowa Biblioteka Nauk Społecznych";
          break;
        case 6:
          $sLocation = "Biblioteka Instytutu Historii";
          break;
        case 7:
          $sLocation = "Biblioteka Instytutu Prahistorii";
          break;
        case 8:
          $sLocation = "Biblioteka Wydziału Nauk Politycznych i Dziennikarstwa";
          break;
        case 9:
          $sLocation = "Biblioteka Wydziału Prawa i Administracji";
          break;
      }
    }
    return $sLocation;
  }

  public function translateReqStatusId($sReqStatusId)
  {
    if ($sReqStatusId == "1") {
      $sReqStatusName = "Zamówione";
    } else if ($sReqStatusId == "2") {
      $sReqStatusName = "Wypożyczone";
    } else if ($sReqStatusId == "3") {
      $sReqStatusName = "Zrealizowane";
    }
    return $sReqStatusName;
  }

}
