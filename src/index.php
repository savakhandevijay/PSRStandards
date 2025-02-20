<?php

// include your composer dependencies
$spreadsheet_url="https://docs.google.com/spreadsheets/d/e/2PACX-1vQ1wlbcd3wuOe8lJnj1R60uUVrsuV899INNyOfh1HgJzKbcCeeJqjNmUIh7IlmcH19pNYGTTDFQNOCX/pub?output=csv";


if(!ini_set('default_socket_timeout', 20)) echo "<!-- unable to change socket timeout -->";
if (($handle = fopen($spreadsheet_url, 'r')) !== false) {
    $row = 0;
    while ($data = fgetcsv($handle, 1000, ',', '"', '\\')) {
        $row++;
        //skip header row
        if ($row == 1) {
            continue;
        }
        
        $spreadsheet_data = array_filter($data, function ($element) {
            if (preg_match('/^WSS-(\d)+/', $element)) {
                return true;
            }
        });
        break;
    }
    fclose($handle);
} else {
    die('Problem reading csv');
}
echo '<pre>';
print_r($spreadsheet_data); 
