<?php
require '../vendor/autoload.php';
include '../functions.php';

ob_start();

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

// Ambil data dari DB
$query = mysqli_query($conn, "
    SELECT a.*, u.username
    FROM alumni a
    INNER JOIN user u ON a.kode_alumni = u.kode_pengguna
    ORDER BY a.kode_alumni ASC
");

// Buat Spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Header kolom
$headers = ['NISN', 'Nama', 'Jurusan', 'Alamat', 'Telepon', 'Tahun Lulus', 'Username'];
$sheet->fromArray($headers, NULL, 'A1');

// Format header
$headerStyle = [
    'font' => [
        'bold' => true,
        'color' => ['rgb' => '175A0F'],
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER,
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
        ],
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => [
            'rgb' => 'C6EFCE',
        ],
    ],
];
$sheet->getStyle('A1:G1')->applyFromArray($headerStyle);

// Isi data
$row = 2;
while ($data = mysqli_fetch_assoc($query)) {
    $sheet->setCellValue("A$row", $data['nisn']);
    $sheet->setCellValue("B$row", $data['nama']);
    $sheet->setCellValue("C$row", $data['jurusan']);
    $sheet->setCellValue("D$row", $data['alamat']);
    $sheet->setCellValue("E$row", $data['telepon']);
    $sheet->setCellValue("F$row", $data['tahun_lulus']);
    $sheet->setCellValue("G$row", $data['username']);

    // Tambahkan border untuk tiap baris data
    $sheet->getStyle("A1:G1")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

    $row++;
}

$lastRow = $row - 1;
$sheet->getStyle("A2:G{$lastRow}")->applyFromArray([
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER,
    ],
]);

// Otomatis atur lebar kolom
foreach (range('A', 'G') as $col) {
    $sheet->getColumnDimension($col)->setWidth(30);
}

$sheet->getRowDimension(1)->setRowHeight(22);

for ($i = 2; $i < $row; $i++) {
    $sheet->getRowDimension($i)->setRowHeight(20);
}

// Nama file
$filename = 'Data_Alumni_' . date('Ymd') . '.xlsx';

// Output file ke browser
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment;filename=\"$filename\"");
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
ob_end_clean();
$writer->save('php://output');
exit;