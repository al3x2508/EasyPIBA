<?php
namespace Module\JSON;
use Controller\AdminController;
class Admin {
	public $columnsMap;
	public $instanceName;
	public $permission;
	public $countTotal;
	public $countFiltered;
	public $entity;
	public $fields = array();
	protected $data = array();

	public function __construct() {
		$adminController = new AdminController();
		if($adminController->checkPermission($this->permission) == false) die(__('You do not have permissions for this'));
		if(!arrayKeyExists('export', $_REQUEST)) header('Content-Type: application/json');
	}
	public function output() {
		if(arrayKeyExists('export', $_REQUEST)) {
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
						header('Content-Disposition: attachment;filename="' . $this->instanceName . '.pdf"');
						echo file_get_contents($pdfFile);
						unlink($excelFile);
						unlink($pdfFile);
					}
					else echo __('Error processing');
					break;
				case 'excel':
				default:
					header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
					header('Content-Disposition: attachment;filename="' . $this->instanceName . '.xlsx"');
					$objWriter = self::excelExport();
					$objWriter->save('php://output');
					break;
			}
			exit;
		}
		else {
			if(arrayKeyExists('secho', $_REQUEST)) {
				$entities = array();
				foreach($this->data AS $entity) {
					if(arrayKeyExists('password', $entity)) unset($entity->password);
					$entities[] = $entity;
				}
				$response = json_encode(array(
					'sEcho'                => $_REQUEST['secho'],
					'iTotalRecords'        => $this->countTotal,
					'iTotalDisplayRecords' => $this->countFiltered,
					'aaData'               => $entities
				));
			}
			else $response = json_encode($this->data);
		}
		if(arrayKeyExists('callback', $_REQUEST)) $response = $_REQUEST['callback'] . '(' . $response . ')';
		echo $response;
	}
	public function excelExport() {
		$data = array();
		$columnNames = array();
		foreach($this->data AS $entity) {
			foreach(get_object_vars($entity) AS $key => $value) {
				if(!is_array($value) && !is_object($value)) {
					if((!isset($this->columnsMap) || !is_array($this->columnsMap) || (count($this->columnsMap) == 0)) || arrayKeyExists($key, $this->columnsMap)) {
						$data[$key] = $value;
						if(!in_array($key, $columnNames)) $columnNames[] = $key;
					}
				}
				elseif(is_object($value)) foreach(get_object_vars($value) AS $key2 => $value2) {
					if(!is_array($value2) && !is_object($value2)) {
						$columnName = $key . '.' . $key2;
						if((!isset($this->columnsMap) || !is_array($this->columnsMap) || (count($this->columnsMap) == 0)) || arrayKeyExists($columnName, $this->columnsMap)) {
							$data[$columnName] = $value2;
							if(!in_array($columnName, $columnNames)) $columnNames[] = $columnName;
						}
					}
				}
			}
		}
		$objPHPExcel = new \PHPExcel();
		//Set document properties
		$objPHPExcel->getProperties()->setCreator(_APP_NAME_)->setLastModifiedBy(_APP_NAME_)->setTitle("Export " . $this->instanceName)->setSubject("Export " . $this->instanceName)->setDescription("Export " . $this->instanceName)->setKeywords(_APP_NAME_ . "export data " . $this->instanceName)->setCategory(_APP_NAME_ . " " . $this->instanceName);
		//Add header line
		foreach($columnNames AS $index => $columnName) {
			if((!isset($this->columnsMap) || !is_array($this->columnsMap) || (count($this->columnsMap) == 0)) || arrayKeyExists($columnName, $this->columnsMap)) {
				$columnName = (is_array($this->columnsMap) && arrayKeyExists($columnName, $this->columnsMap))?$this->columnsMap[$columnName]:$columnName;
				$colString = \PHPExcel_Cell::stringFromColumnIndex($index);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue($colString . '1', $columnName);
			}
		}
		//Set bold the header line
		$objPHPExcel->getActiveSheet()->getStyle('A1:' . chr(count($columnNames) + 64) . '1')->getFont()->setBold(true);
		//Set filters
		$objPHPExcel->getActiveSheet()->setAutoFilter($objPHPExcel->getActiveSheet()->calculateWorksheetDimension());
		$objPHPExcel->getActiveSheet()->fromArray((array) $data, NULL, 'A2');
		//Set to repeat the first row and the page in landscape mode
		$objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 1)->setOrientation(\PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		//Rename the worksheet
		$objPHPExcel->getActiveSheet()->setTitle('Export ' . $this->instanceName);
		//Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);
		//Redirect to browser
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