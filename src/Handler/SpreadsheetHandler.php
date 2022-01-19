<?php

namespace App\Handler;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class SpreadsheetHandler {
	
	private $em;
	private $params;

	public function __construct(EntityManagerInterface $em, ParameterBagInterface $params){
		$this->em = $em;
		$this->params = $params;
	}

	public function generateReportProduct($products){
		$spreadsheet = new Spreadsheet();		
		$sheet = $spreadsheet->getActiveSheet();

		$sheet->getStyle('A1:I1')->getFont()->setBold(true);

		$sheet->setCellValue('A1', 'Id');
		$sheet->setCellValue('B1', 'Codigo');
		$sheet->setCellValue('C1', 'Nombre');
		$sheet->setCellValue('D1', 'Descripción');
		$sheet->setCellValue('E1', 'Marca');
		$sheet->setCellValue('F1', 'Categoria');
		$sheet->setCellValue('G1', 'Precio');
		$sheet->setCellValue('H1', 'Fecha Creación');
		$sheet->setCellValue('I1', 'Fecha Ultima Modificación');

		$columnPosition = 2;
		foreach($products as $key => $product){
			$sheet->setCellValue('A'.$columnPosition, $product->getId());
			$sheet->setCellValue('B'.$columnPosition, $product->getCode());
			$sheet->setCellValue('C'.$columnPosition, $product->getName());
			$sheet->setCellValue('D'.$columnPosition, $product->getDescription());
			$sheet->setCellValue('E'.$columnPosition, $product->getBrand());
			$sheet->setCellValue('F'.$columnPosition, $product->getCategory()->getName());
			$sheet->setCellValue('G'.$columnPosition, $product->getPrice());
			$sheet->setCellValue('H'.$columnPosition, $product->getCreatedAt()->format('Y-m-d H:i:s'));
			$sheet->setCellValue('I'.$columnPosition, $product->getUpdatedAt()->format('Y-m-d H:i:s'));
			$columnPosition++;
		}

		$writer = new Xlsx($spreadsheet);
		$writer->save($pathReportProduct = $this->params->get('kernel.project_dir').'\\tmp\\productReport.xlsx');
		return $pathReportProduct;
	}
}
?>