<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

    require './vendor/autoload.php';
    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\Writer\Xls;

function array_to_csv_download($array, $filename = "export.csv", $delimiter=";") {
    $f = fopen('./uploaded_files/csvready.csv', 'w');

    foreach ($array as $line) {
        fputcsv($f, $line, $delimiter);
    }
    fclose($f);

$spreadsheet = new Spreadsheet();
$reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
/* Set CSV parsing options */
$reader->setDelimiter(';');
$reader->setEnclosure('"');
$reader->setSheetIndex(0);
/* Load a CSV file and save as a XLS */
$spreadsheet = $reader->load("./uploaded_files/csvready.csv");
$writer = new Xls($spreadsheet);

$writer->save('./uploaded_files/export.xls');
$spreadsheet->disconnectWorksheets();
unset($spreadsheet);
header("Content-Type: application/xls");
header("Content-Disposition: attachment; filename=export.xls");
readfile('./uploaded_files/export.xls');
    die();
}
if (!empty($_FILES['uploadedFile'])) {
  $filename = $_FILES['uploadedFile']['name'];
        //$ext = pathinfo($filename, PATHINFO_EXTENSION);
  $uploaddir = './uploaded_files/';
            $uploadfile = $uploaddir . basename($_FILES['uploadedFile']['name']);
            move_uploaded_file($_FILES['uploadedFile']['tmp_name'], $uploadfile);
            

  $handle = fopen('./uploaded_files/' .$_FILES['uploadedFile']['name'], "r");
  $i = 0;
  //$a[$i][0]   = "A";
  $a[$i][1]   = "B";
  //$a[$i][2]   = "C";
  $a[$i][3]   = "D";
  //$a[$i][4]   = "E";
  $a[$i][5]   = "F";
  //$a[$i][6]   = "G";
  $a[$i][7]   = "H";
  $a[$i][8]   = "I";
  $a[$i][9]   = "J";
  //$a[$i][10]   = "K";
  $a[$i++][11]   = "L";
  //$a[$i++][12]   = "M";
  if ($handle) {
      while (($buffer = fgets($handle, 4096)) !== false) {
          //$a[$i][0]   = trim(substr($buffer, 0, 8));
          $a[$i][1]   = trim(substr($buffer, 8, 1));
          //$a[$i][2]   = trim(substr($buffer, 9, 1));
          $a[$i][3]   = trim(substr($buffer, 10, 14));
          //$a[$i][4]   = trim(substr($buffer, 26, 4));
          $a[$i][5]   = trim(substr($buffer, 30, 6));
          //$a[$i][6]   = trim(substr($buffer, 36, 40));
          $a[$i][7]   = trim(substr($buffer, 76, 18));
          $a[$i][8]   = trim(substr($buffer, 94, 5));
          $a[$i][9]   = substr($buffer, 99, 25);
          //$a[$i][10]   = trim(substr($buffer, 124, 25));
          $a[$i++][11]   = str_replace('.', ',', intval(substr($buffer, 149, 13)) / 100);
          //$a[$i++][12]   = trim(substr($buffer, 162, -2));
          
      }
      if (!feof($handle)) {
          echo "Erreur: fgets() a échoué\n";
      }
      fclose($handle);
      
  }
  array_to_csv_download($a);
  
}

?>

<!DOCTYPE html>
<html>
<head>
  <title>PHP File Upload</title>
</head>
<body>
  <form method="POST" enctype="multipart/form-data">
    <div>
      <span>Upload a File:</span>
      <input type="file" name="uploadedFile" />
    </div>

    <input type="submit" name="uploadBtn" value="Upload" />
  </form>
</body>
</html>
