<?php
namespace json;
abstract class json {
	public static $columnsMap;
	public static $instanceName;
	public static $permission;
	public static $countTotal;
	public static $countFiltered;
	public $entity;
	public $fields = array();
	protected static $data = array();

	public function __construct() {
		$adminController = new \Controller\AdminController();
		if($adminController->checkPermission(self::$permission) == false) die(__('You do not have permissions for this'));
	}
	public function output() {
		if(array_key_exists('export', $_REQUEST)) {
			ini_set('memory_limit', -1);
			error_reporting(E_ALL);
			ini_set('display_errors', true);
			ini_set('display_startup_errors', true);
			date_default_timezone_set('Europe/London');

			if(PHP_SAPI == 'cli') die(__('This example should only be run from a Web Browser'));

			switch($_REQUEST['export']) {
				case 'pdf':
					$objWriter = self::excelExport();
					$excelFile = tempnam(sys_get_temp_dir(), 'Export');
					$pdfFile = $excelFile . '.pdf';
					$excelFile .= '.xlsx';
					$objWriter->save($excelFile);
					chdir(sys_get_temp_dir());
					try {
						exec('soffice --headless --convert-to pdf ' . $excelFile . ' 2>&1', $output, $return);
						echo $return . '<br />' . PHP_EOL;
						print_r($output);
					}
					catch(\Exception $e) {
						die($e);
					}
					if(file_exists($pdfFile)) {
						header('Content-Type: application/pdf');
						header('Content-Disposition: attachment;filename="' . self::$instanceName . '.pdf"');
						echo file_get_contents($pdfFile);
						unlink($excelFile);
						unlink($pdfFile);
					}
					else {
						echo 'Eroare la procesare';
						//unlink($excelFile);
					}
					break;
				case 'excel':
				default:
					header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
					header('Content-Disposition: attachment;filename="' . self::$instanceName . '.xlsx"');
					$objWriter = self::excelExport();
					$objWriter->save('php://output');
					break;
			}
			exit;
		}
		else {
			if(array_key_exists('secho', $_REQUEST)) {
				$entitati = array();
				foreach(self::$data AS $entitate) {
					if(array_key_exists('parola', $entitate)) unset($entitate['parola']);
					$entitati[] = $entitate;
				}
				$raspuns = json_encode(array(
					'sEcho'                => $_REQUEST['secho'],
					'iTotalRecords'        => self::$countTotal,
					'iTotalDisplayRecords' => self::$countFiltered,
					'aaData'               => $entitati
				));
			}
			else $raspuns = json_encode(self::$data);
		}
		if(array_key_exists('callback', $_REQUEST)) $raspuns = $_REQUEST['callback'] . '(' . $raspuns . ')';
		echo $raspuns;
	}
	public static function excelExport() {
		/** Include PHPExcel */
		require_once(dirname(dirname(dirname(__FILE__))) . '/Utils/PHPExcel.php');
		$objPHPExcel = new \PHPExcel();
		//Seteaza proprietatile documentului
		$objPHPExcel->getProperties()->setCreator("MyKoolio")->setLastModifiedBy("MyKoolio")->setTitle("Export " . self::$instanceName)->setSubject("Export " . self::$instanceName)->setDescription("Export " . self::$instanceName)->setKeywords("export date my koolio " . self::$instanceName)->setCategory("MyKoolio " . self::$instanceName);
		//Adauga linia de antet cu numele coloanelor
		$coloane = array_keys(self::$data[0]);
		foreach($coloane AS $index => $numeColoana) {
			if(array_key_exists($numeColoana, self::$columnsMap)) $numeColoana = self::$columnsMap[$numeColoana];
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue(chr($index + 65) . '1', $numeColoana);
		}
		//Punem bold pe prima linie
		$objPHPExcel->getActiveSheet()->getStyle('A1:' . chr(count($coloane) + 64) . '1')->getFont()->setBold(true);
		//Punem filtru
		$objPHPExcel->getActiveSheet()->setAutoFilter($objPHPExcel->getActiveSheet()->calculateWorksheetDimension());
		$objPHPExcel->getActiveSheet()->fromArray(self::$data, NULL, 'A2');
		//Punem repeat pe primul rand si pagina in mod landscape
		$objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 1)->setOrientation(\PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		//Redenumim worksheetul
		$objPHPExcel->getActiveSheet()->setTitle('Export ' . self::$instanceName);
		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);
		//Redirectioneaza catre browser
		header('Cache-Control: max-age=0');
		//If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');
		//If you're serving to IE over SSL, then the following may be needed
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
		header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header('Pragma: public'); // HTTP/1.0
		$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		return $objWriter;
	}
}
?>