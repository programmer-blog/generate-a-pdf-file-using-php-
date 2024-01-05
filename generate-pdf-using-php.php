<?php

require_once('vendor/autoload.php');
// Include TCPDF (if using Composer) 

use TCPDF as TCPDF;
// Alias for TCPDF class 
// Create a PDF instance 
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8');
$pdf->SetCreator('Your Name');
$pdf->SetAuthor('Your Name');
$pdf->SetTitle('Student Exam Results');
$pdf->SetSubject('Student exam results report');
$pdf->SetKeywords('TCPDF, PDF, PHP, example, tutorial');

// Add a page 
$pdf->AddPage();
// Connect to the MySQL database (replace with your credentials) 
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'exam_results'; // Your database name 

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}

// Retrieve student exam results from the database 
$query = "SELECT name, subject, score FROM students";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    // Create a table for the exam results
    $html = "<h1 style=\"text-align: center; font-size: 18px;\">Students Exam Results Report</h1>";

    $html .= '<table style="width: 100%; border: none; border-radius: 5px; border-color: #007bff;">
        <tr style="background-color: #f2f2f2; color: #444; text-align: center; font-weight: bold; border-bottom: 2px solid #007bff;
            box-shadow: 0 1px 1px rgba(0, 0, 0, 0.05);">
            <th style="text-align: left;  width: 30%; flex: 1 0;">
                <h2 style="font-size: 15px; margin-bottom: 0;">Name</h2>
            </th>
            <th style="text-align: left;  width: 35%; flex: 1 0;">
                <h2 style="font-size: 15px; margin-bottom: 0;">Subject</h2>
            </th>
            <th style="text-align: left; width: 25%; flex: 1 0;">
                <h2 style="font-size: 15px; margin-bottom: 0;">Score</h2>
            </th>
        </tr>';

    $i = 0;
    while ($row = $result->fetch_assoc()) {
        // Add data to the table
        $row_class = ($i % 2 == 0) ? "even" : "odd";
        $html .= '<tr class="' . $row_class . '" style="background-color: ';
        if ($row_class == "even") {
            $html .= '#c3c3c3'; // Lighter tint of main background color
        } else {
            $html .= '#fff'; // White background color for odd rows
        }
        $html .= '; color: #333; text-align: center;">
        <td style="text-align: left; padding: 10px;"><p style="font-size: 14px; margin-bottom: 0;">' . $row['name'] . '</p></td>
        <td style="text-align: left; padding: 10px;"><p style="font-size: 14px; margin-bottom: 0;">' . $row['subject'] . '</p></td>';

        // Apply higher line-height to cells with longer text
        if (strlen($row['score']) > 7) {
            $html .= '<td style="padding: 10px;"><p style="font-size: 14px; margin-bottom: 0;">' . $row['score'] . '</p></td>';
        } else {
            $html .= '<td style="text-align: left; padding: 10px;"><p style="font-size: 14px; margin-bottom: 0;">' . $row['score'] . '</p></td>';
        }

        $html .= '</tr>';
        $i++;
    }
    $html .= '</table>';

    // Output the HTML table to the PDF
    $pdf->writeHTML($html, true, false, true, false, '');
} else {
    echo 'No records found.';
}

// Output the PDF to the browser
$pdf->Output('exam_results.pdf', 'I');





// Close the MySQL connection 
$conn->close();
