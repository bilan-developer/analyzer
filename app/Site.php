<?php

namespace App;

use App\Exceptions\ModelException;
use App\Http\Core\Constants;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Facades\Excel;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Fill;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


class Site extends Model
{
    protected $fillable = ['url'];

    protected $statusError = array(
        'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array (
                'rgb' => 'DE101D'
            )
        ),
        'alignment' => array (
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        ),
    );

    protected $statusOk = array(
        'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array (
                'rgb' => '00C220'
            )
        ),
        'alignment' => array (
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        )
    );

    const searchFile = 'robots.txt';
    /**
     * Парсинг url.
     *
     * @return string
     *
     * @throws ModelException
     */
    public function parsingUrl()
    {
        $arUrl = parse_url($this->url);

        if (!array_key_exists("scheme", $arUrl) || !in_array($arUrl["scheme"], array("http", "https"))){
            $arUrl["scheme"] = "http";
        }
        if ($arUrl['scheme'] && isset($arUrl["host"])){
            if(substr($arUrl["host"], 0, 3) !== 'www'){
                $arUrl["host"] = sprintf("www.%s", $arUrl["host"]);
            }
            $this->url = sprintf("%s://%s", $arUrl["scheme"], $arUrl["host"]);
            return true;
        }

        if (preg_match("/^\w+\.[\w\.]+(\/.*)?$/", $arUrl["path"])){
            if(substr($arUrl["path"], 0, 3) !== 'www'){
                $arUrl["path"] = sprintf("www.%s", $arUrl["path"]);
            }
            $this->url = sprintf("%s://%s", $arUrl["scheme"], $arUrl["path"]);
            return true;
        }

        $this->notValid();
    }

    /**
     * Генерация исключения Сайт не валиден.
     *
     * @throws ModelException
     */
    public function notValid()
    {
        throw new ModelException([
            'url' => ['Сайт не валиден']
        ]);
    }

    /**
     * Поиск файла.
     *
     * @return array
     */
    public function searchFile()
    {
        $fileName = sprintf("%s.txt", md5(microtime()));
        $filePatch = sprintf("files/%s",  $fileName);
        $this->getFileCurl($filePatch);

        $report = $this->parsingFile($filePatch);
        unlink($filePatch);

        return $report;
    }

    /**
     * Парсинг файла.
     *
     * @param string $filePatch
     *
     * @return array
     */
    public function parsingFile($filePatch)
    {
        $report = [Constants::okFilePresence];

        $texFile = file_get_contents($filePatch);
        $countDirectiveHost = substr_count($texFile, "Host: ");
        if($countDirectiveHost){
            $report[] = Constants::okIsDirectiveHost;
            $report[] = $countDirectiveHost === 1 ? Constants::okCountDirectiveHost : Constants::errorCountDirectiveHost;
        }else{
            $report[] = Constants::errorIsDirectiveHost;
        }

        $fileSize = (int)filesize($filePatch);
        $report[] = $fileSize < 32768 ? Constants::okSizeFile($fileSize) : Constants::errorSizeFile($fileSize);

        $countDirectiveSitemap = substr_count($texFile, "Sitemap: ");
        $report[] = $countDirectiveSitemap ? Constants::okIsDirectiveSitemap : Constants::errorIsDirectiveSitemap;

        $report[] = Constants::okCodeAnswer;

        return $report;
    }

    /**
     * Считываение файла curl.
     *
     * @param string $filePatch
     */
    public function getFileCurl($filePatch)
    {
        $file = fopen($filePatch, 'w');

        $ch = curl_init();
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_URL, sprintf("%s/%s", $this->url, self::searchFile));
        curl_setopt($ch, CURLOPT_FILE, $file);
        curl_exec($ch);
        fclose($file);
        curl_close($ch);
    }

    /**
     * Проверка заголовков.
     *
     * @return array
     */
    public function checkHeaders()
    {
        $headers = @get_headers(sprintf("%s/%s", $this->url, self::searchFile));
        if(!$headers || ($headers[0] && $headers[0] !== 'HTTP/1.1 200 OK')){
            preg_match('/\d{3}/', $headers[0], $matches);
            $codeAnswer =  $matches ? $matches[0] : 0;

            return ['status' => false, 'report' => [Constants::errorFilePresence, Constants::errorCodeAnswer($codeAnswer)]];
        }

        return ['status' => true];
    }

    /**
     * Сохранение отчёта.
     *
     * @param $report
     * @return string
     */
    public function saveReport($report)
    {
        $fileName = sprintf("%s.txt", md5(microtime()));
        $report = json_encode($report);
        file_put_contents($this->getFileReportPatch($fileName), $report);

        return $fileName;
    }

    /**
     * Создание Excel документа.
     * @param $fileName
     */
    public function createdExcel($fileName)
    {
        $data = file_get_contents($this->getFileReportPatch($fileName));
        $report = json_decode($data, TRUE);

        Excel::create('Report', function($excel) use ($report) {
            $excel->sheet('Лист 1', function($sheet) use($report){
                $sheet->getCell('A1')->setValue('№');
                $sheet->getCell('B1')->setValue('Название проверки');
                $sheet->getCell('C1')->setValue('Статус');
                $sheet->getCell('D1')->setValue(' ');
                $sheet->getCell('E1')->setValue('Текущее состояние');
                $sheet->getStyleByColumnAndRow(0, 1)->getAlignment()
                    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $sheet->getStyleByColumnAndRow(2, 1)->getAlignment()
                    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $this->sheet = $sheet;
                $kol = 3;
                foreach ($report as $check){
                    $status =  $check['status'] ? 'ОК' : 'Ошибка';
                    $sheet->mergeCellsByColumnAndRow(0, $kol, 0, $kol+1);
                    $sheet->mergeCellsByColumnAndRow(1, $kol, 1, $kol+1);
                    $sheet->mergeCellsByColumnAndRow(2, $kol, 2, $kol+1);

                    $sheet->getStyleByColumnAndRow(0, $kol)->getAlignment()
                        ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                        ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $sheet->getStyleByColumnAndRow(1, $kol)->getAlignment()->
                    setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

                    $sheet->row($kol, [$check['number'], $check['name'], $status]);
                    if($check['status']){
                        $sheet->getStyleByColumnAndRow(2, $kol)->applyFromArray($this->statusOk);
                    }else{
                        $sheet->getStyleByColumnAndRow(2, $kol)->applyFromArray($this->statusError);
                    }
                    $sheet->setCellValueByColumnAndRow(3, $kol, 'Состояние');
                    $sheet->setCellValueByColumnAndRow(3, $kol+1, 'Рекомендации');

                    $sheet->setCellValueByColumnAndRow(4, $kol, $check['condition']);
                    $sheet->setCellValueByColumnAndRow(4, $kol+1, $check['recommendations']);

                    $kol +=3;
                }
            });

            $excel->getActiveSheet()->getColumnDimension('A')->setAutoSize(false);
            $excel->getActiveSheet()->getColumnDimension('A')->setWidth(5);

            $excel->getActiveSheet()->getColumnDimension('B')->setAutoSize(false);
            $excel->getActiveSheet()->getColumnDimension('B')->setWidth(55);

            $excel->getActiveSheet()->getColumnDimension('C')->setAutoSize(false);
            $excel->getActiveSheet()->getColumnDimension('C')->setWidth(10);

            $excel->getActiveSheet()->getColumnDimension('D')->setAutoSize(false);
            $excel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
        })->export('xls');
    }

    /**
     * Путь к файлу с отчётом.
     *
     * @param $fileName
     * @return string
     */
    public function getFileReportPatch($fileName)
    {
        return sprintf("files/report%s",  $fileName);
    }
}
