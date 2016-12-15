<?php
namespace AppBundle\Common;

use PHPExcel;
use PHPExcel_IOFactory;

class PHPExcelToolkit
{
    //TO DO 异常处理
    public static function export($datas, $info)
    {
        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();
        // Set document properties
        $objPHPExcel->getProperties()
            ->setCreator($info['creator'])
            ->setLastModifiedBy($info['creator'])
            ->setTitle('Office 2007 XLSX Document')
            ->setSubject('Office 2007 XLSX Document')
            ->setDescription('Document for Office 2007 XLSX, generated using PHP classes.')
            ->setKeywords('office 2007 openxml php')
            ->setCategory('Export file');
        $activieSheet = $objPHPExcel->setActiveSheetIndex(0);
        $index = 1;

        if (!empty($datas)) {
            foreach ($datas as $data) {
                $i = 0;
                foreach ($data as $value) {
                    if ($i >= 26) {
                        $char = chr(39+$i);
                        $i++;
                        $activieSheet->setCellValue("A{$char}{$index}", $value);
                        if ($value === '手机号') {
                            $activieSheet->getColumnDimension("A{$char}")->setWidth(15);
                        }
                        if ($value === '身份证') {
                            $activieSheet->getColumnDimension("{$char}")->setWidth(25);
                        }
                    } else {
                        $char = chr(65+$i);
                        $i++;
                        $activieSheet->setCellValue("{$char}{$index}", $value);
                        if ($value === '手机号') {
                            $activieSheet->getColumnDimension("{$char}")->setWidth(15);
                        }
                        if ($value === '身份证') {
                            $activieSheet->getColumnDimension("{$char}")->setWidth(25);
                        }
                    }
                }
                $activieSheet->getRowDimension($index)->setRowHeight(18);
                $index++;
            }
        }
        
        // Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle($info['sheetName']);
        $objPHPExcel->setActiveSheetIndex(0);

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

        return $objWriter;
    }
}
