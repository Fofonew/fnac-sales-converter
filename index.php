<?php

require './vendor/autoload.php';

use App\SQLiteInsert;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use App\SQLiteConnection;

try {
    $pdo = (new SQLiteConnection())->connect();
    $sqlite = new SQLiteInsert($pdo);
    $convertedFiles = $sqlite->getLast10ConvertedFiles();
} catch (PDOException $e) {
    echo $e->getMessage();
    $convertedFiles = null;
}


if (!empty($_FILES['uploadedFile']) && !empty($_FILES['uploadedFile']['tmp_name'])) {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $handle = fopen($_FILES['uploadedFile']['tmp_name'], 'rb');
    $i = 1;
    if ($handle) {
        while (($buffer = fgets($handle, 4096)) !== false) {
            $sheet->setCellValue('A' . $i, trim($buffer[8]));
            $sheet->setCellValue('B' . $i, trim(substr($buffer, 10, 14)));
            $sheet->setCellValue('C' . $i, trim(substr($buffer, 30, 6)));
            $sheet->setCellValue('D' . $i, trim(substr($buffer, 76, 18)));
            $sheet->setCellValue('E' . $i, trim(substr($buffer, 94, 5)));
            $sheet->setCellValue('F' . $i, substr($buffer, 99, 25));
            $sheet->setCellValue('G' . $i, (int)substr($buffer, 149, 13) / 100);
            $i++;
        }
        if (!feof($handle)) {
            echo "Erreur: fgets() a échoué\n";
        }
        fclose($handle);
    }
    $writer = new Xls($spreadsheet);
    $convertedFilePath = './uploaded_files/' . $_FILES['uploadedFile']['name'] . '_convert.xls';
    $writer->save($convertedFilePath);
    // Download file as XLS
    try {
        $pdo = (new SQLiteConnection())->connect();
        $sqlite = new SQLiteInsert($pdo);
        $sqlite->insertConvertedFileRecord($_FILES['uploadedFile']['name'], $convertedFilePath);
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
    header('Content-Type: application/xls');
    header('Content-Disposition: attachment; filename=export.xls');
    $writer->save('php://output');
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Fnac file converter</title>
  <!-- Bootstrap -->
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css' rel='stylesheet'
          integrity='sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi' crossorigin='anonymous'>
    <script
            src='https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js'
            integrity='sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3'
            crossorigin='anonymous'></script>
</head>
  <!--HTML body with a form to upload a file and a table to display the last 10 converted files using Bootstrap-->
<body>
<div class="container">
    <div class="row text-center">
        <div class="col-md-12">
            <h1 class="m-3">Fnac file converter</h1>
            <form action="index.php" method="post" enctype="multipart/form-data">
                <div class="form-group m-5
                ">
                    <label for="uploadedFile">Upload a file</label>
                    <input type="file" class="form-control-file" id="uploadedFile" name="uploadedFile">
                </div>
                <button type="submit" class="btn btn-primary">Convert</button>
            </form>
        </div>
    </div>
    <div class="row my-5">
        <div class="col-md-12">
            <h2>Last 10 converted files</h2>
            <table class="table">
                <thead>
                <tr>
                    <th scope="col">File name</th>
                    <th scope="col">Converted file path</th>
                    <th scope="col">Converted at</th>
                </tr>
                </thead>
                <tbody>
                <?php if (!empty($convertedFiles)): ?>
                    <?php foreach ($convertedFiles as $convertedFile): ?>
                    <tr>
                            <td><?= $convertedFile['original_file_path'] ?></td>
                            <td><a href="<?= $convertedFile['converted_file_path'] ?>"><?= basename($convertedFile['converted_file_path']) ?></a></td>
                            <td><?= $convertedFile['created_at'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
