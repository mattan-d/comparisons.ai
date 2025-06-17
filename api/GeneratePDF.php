<?php
include __DIR__ . '/../classes/init.php';

// Set headers to download PDF
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="comparison_summary.pdf"');

// Get comparison ID from request
$comparison_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$comparison_id) {
    die("Error: No comparison ID provided");
}

// Fetch comparison data
$comparison = $DB->store('comparisons')
        ->where('id', $comparison_id)
        ->one()
        ->fetch();

if (!$comparison) {
    die("Error: Comparison not found");
}

// Fetch client data
$client = $DB->store('clients')
        ->where('id', $comparison['client_id'])
        ->one()
        ->fetch();

// Fetch travel insurance details
$travel_details = $DB->store('travel_insurance_details')
        ->where('comparison_id', $comparison_id)
        ->one()
        ->fetch();

// Fetch coverage options
$coverage_options = $DB->store('comparison_coverage')
        ->where('comparison_id', $comparison_id)
        ->one()
        ->fetch();

// Fetch companies
$companies = $DB->store('comparison_companies')
        ->where('comparison_id', $comparison_id)
        ->one()
        ->fetch();

// Fetch comparison results
$results = $DB->store('comparison_results')
        ->where('comparison_id', $comparison_id)
        ->one()
        ->fetch();

// Format dates
$created_date = date('d/m/Y H:i', $comparison['created_at']);
$departure_date = isset($travel_details['departureDate']) ? date('d/m/Y', strtotime($travel_details['departureDate'])) : 'N/A';
$return_date = isset($travel_details['returnDate']) ? date('d/m/Y', strtotime($travel_details['returnDate'])) : 'N/A';

// Calculate trip duration
$trip_duration = 'N/A';
if (isset($travel_details['departureDate']) && isset($travel_details['returnDate'])) {
    $departure = new DateTime($travel_details['departureDate']);
    $return = new DateTime($travel_details['returnDate']);
    $trip_duration = $departure->diff($return)->days . ' ימים';
}

// Get destination name
$destination = isset($travel_details['destinationName']) ? $travel_details['destinationName'] :
        (isset($travel_details['destination']) ? $travel_details['destination'] : 'N/A');

// Essential coverage types in Hebrew
$essential_coverage = [
        'medical-expenses' => 'הוצאות רפואיות',
        'third-party-liability' => 'חבות כלפי צד ג\'',
        'trip-cancellation' => 'ביטול נסיעה',
        'trip-shortening' => 'קיצור נסיעה',
        'luggage' => 'כבודה',
        'search-rescue' => 'איתור, חיפוש וחילוץ'
];

// Optional coverage types in Hebrew
$optional_coverage_types = [
        'dental-emergency' => 'טיפול חירום בשיניים',
        'medical-israel' => 'הוצאות רפואיות בישראל',
        'pregnancy' => 'הריון לא בסיכון',
        'rental-car' => 'ביטול השתתפות עצמית לרכב שכור',
        'extreme-sports' => 'פעילות אתגרית',
        'winter-sports' => 'ספורט חורף',
        'electronics' => 'מחשב נייד / טלפון / טאבלט',
        'camera' => 'מצלמה'
];

// Generate HTML content for PDF
$html = '
<!DOCTYPE html>
<html dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>סיכום השוואת פוליסות</title>
    <style>
        body {
            font-family: "DejaVu Sans", Arial, Helvetica, sans-serif;
            direction: rtl;
            text-align: right;
            line-height: 1.6;
            color: #333;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #FF1744;
            padding-bottom: 10px;
        }
        .header h1 {
            color: #FF1744;
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .section {
            margin-bottom: 20px;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }
        .section h2 {
            color: #FF1744;
            font-size: 16px;
            margin: 0 0 10px 0;
        }
        .info-grid {
            display: table;
            width: 100%;
            border-collapse: collapse;
        }
        .info-row {
            display: table-row;
        }
        .info-label {
            display: table-cell;
            font-weight: bold;
            padding: 5px 0;
            width: 30%;
        }
        .info-value {
            display: table-cell;
            padding: 5px 0;
            width: 70%;
        }
        .coverage-list {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }
        .coverage-list li {
            padding: 3px 0;
        }
        .coverage-list li:before {
            content: "✓";
            color: #FF1744;
            margin-left: 5px;
        }
        .companies-list {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }
        .companies-list li {
            padding: 3px 0;
        }
        .companies-list li:before {
            content: "•";
            color: #FF1744;
            margin-left: 5px;
        }
        .recommendation {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            padding: 10px;
            margin-top: 10px;
        }
        .recommendation h3 {
            color: #FF1744;
            margin: 0 0 10px 0;
            font-size: 14px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #999;
        }
        table.comparison-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table.comparison-table th {
            background-color: #f2f2f2;
            border: 1px solid #ddd;
            padding: 8px;
            text-align: right;
        }
        table.comparison-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>סיכום השוואת פוליסות ביטוח נסיעות לחו"ל</h1>
        <p>הופק בתאריך: ' . $created_date . '</p>
    </div>
    
    <div class="section">
        <h2>פרטי הלקוח</h2>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">שם הלקוח:</div>
                <div class="info-value">' . htmlspecialchars($client['name'] ?? 'לא צוין') . '</div>
            </div>
            <div class="info-row">
                <div class="info-label">תעודת זהות:</div>
                <div class="info-value">' . htmlspecialchars($client['id_number'] ?? 'לא צוין') . '</div>
            </div>
            <div class="info-row">
                <div class="info-label">טלפון:</div>
                <div class="info-value">' . htmlspecialchars($client['phone'] ?? 'לא צוין') . '</div>
            </div>
            <div class="info-row">
                <div class="info-label">אימייל:</div>
                <div class="info-value">' . htmlspecialchars($client['email'] ?? 'לא צוין') . '</div>
            </div>
        </div>
    </div>
    
    <div class="section">
        <h2>פרטי הנסיעה</h2>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">יעד:</div>
                <div class="info-value">' . htmlspecialchars($destination) . '</div>
            </div>
            <div class="info-row">
                <div class="info-label">תאריך יציאה:</div>
                <div class="info-value">' . $departure_date . '</div>
            </div>
            <div class="info-row">
                <div class="info-label">תאריך חזרה:</div>
                <div class="info-value">' . $return_date . '</div>
            </div>
            <div class="info-row">
                <div class="info-label">משך הנסיעה:</div>
                <div class="info-value">' . $trip_duration . '</div>
            </div>
            <div class="info-row">
                <div class="info-label">מספר נוסעים:</div>
                <div class="info-value">' . htmlspecialchars($travel_details['travelersCount'] ?? 'לא צוין') . '</div>
            </div>
            <div class="info-row">
                <div class="info-label">גיל הנוסע הבוגר ביותר:</div>
                <div class="info-value">' . htmlspecialchars($travel_details['oldestTravelerAge'] ?? 'לא צוין') . '</div>
            </div>
        </div>
    </div>
    
    <div class="section">
        <h2>כיסויים הכרחיים</h2>
        <ul class="coverage-list">';

foreach ($essential_coverage as $key => $name) {
    $html .= '<li>' . htmlspecialchars($name) . '</li>';
}

$html .= '
        </ul>
    </div>
    
    <div class="section">
        <h2>כיסויים נוספים</h2>';

if (count($coverage_options) > 0) {
    $html .= '<ul class="coverage-list">';
    foreach ($coverage_options as $option) {
        $coverage_name = $optional_coverage_types[$option['type']] ?? $option['name'] ?? $option['type'];
        $days_info = '';
        if (isset($option['days']) && ($option['type'] == 'extreme-sports' || $option['type'] == 'winter-sports')) {
            $days_info = ' (' . $option['days'] . ' ימים)';
        }
        $html .= '<li>' . htmlspecialchars($coverage_name) . $days_info . '</li>';
    }
    $html .= '</ul>';
} else {
    $html .= '<p>לא נבחרו כיסויים נוספים</p>';
}

$html .= '
    </div>
    
    <div class="section">
        <h2>חברות ביטוח להשוואה</h2>';

if (count($companies) > 0) {
    $html .= '<ul class="companies-list">';
    foreach ($companies as $company) {
        $html .= '<li>' . htmlspecialchars($company['name']) . '</li>';
    }
    $html .= '</ul>';
} else {
    $html .= '<p>לא נבחרו חברות ביטוח להשוואה</p>';
}

$html .= '
    </div>';

// Add recommendation section if available
if (!empty($results) && !empty($results['response'])) {
    $html .= '
    <div class="section">
        <h2>המלצת המערכת</h2>
        <div class="recommendation">
            <h3>המלצה מבוססת על הנתונים שהוזנו:</h3>
            <p>' . nl2br(htmlspecialchars($results['response'])) . '</p>
        </div>
    </div>';
}

$html .= '
    <div class="footer">
        <p>מסמך זה הופק באמצעות מערכת השוואת פוליסות ביטוח נסיעות לחו"ל</p>
        <p>© ' . date('Y') . ' כל הזכויות שמורות</p>
    </div>
</body>
</html>';

// Try to use mPDF if available
if (class_exists('Mpdf\Mpdf')) {
    try {
        require_once __DIR__ . '/../vendor/autoload.php';
        $mpdf = new \Mpdf\Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'margin_left' => 15,
                'margin_right' => 15,
                'margin_top' => 16,
                'margin_bottom' => 16,
                'margin_header' => 9,
                'margin_footer' => 9,
                'tempDir' => sys_get_temp_dir()
        ]);
        $mpdf->SetDirectionality('rtl');
        $mpdf->WriteHTML($html);
        $mpdf->Output('comparison_summary_' . $comparison_id . '.pdf', 'I');
        exit;
    } catch (Exception $e) {
        // Fall back to other methods
    }
}

// Try to use TCPDF if available
if (class_exists('TCPDF')) {
    try {
        require_once __DIR__ . '/../vendor/autoload.php';
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetCreator('Insurance Comparison System');
        $pdf->SetAuthor('Insurance Agent');
        $pdf->SetTitle('Comparison Summary');
        $pdf->SetSubject('Insurance Policy Comparison');
        $pdf->setRTL(true);
        $pdf->AddPage();
        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->Output('comparison_summary_' . $comparison_id . '.pdf', 'I');
        exit;
    } catch (Exception $e) {
        // Fall back to other methods
    }
}

// Try to use wkhtmltopdf if available
$wkhtmltopdf = '/usr/local/bin/wkhtmltopdf';
if (file_exists($wkhtmltopdf) || file_exists('/usr/bin/wkhtmltopdf')) {
    try {
        $tempHtmlFile = tempnam(sys_get_temp_dir(), 'pdf_') . '.html';
        $tempPdfFile = tempnam(sys_get_temp_dir(), 'pdf_') . '.pdf';

        file_put_contents($tempHtmlFile, $html);

        $wkhtmltopdf = file_exists($wkhtmltopdf) ? $wkhtmltopdf : '/usr/bin/wkhtmltopdf';
        $command = "$wkhtmltopdf --encoding utf-8 $tempHtmlFile $tempPdfFile";

        exec($command, $output, $returnVar);

        if ($returnVar === 0 && file_exists($tempPdfFile)) {
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="comparison_summary_' . $comparison_id . '.pdf"');
            readfile($tempPdfFile);

            // Clean up temporary files
            @unlink($tempHtmlFile);
            @unlink($tempPdfFile);
            exit;
        }
    } catch (Exception $e) {
        // Fall back to other methods
    }
}

// If all PDF generation methods fail, output HTML
header('Content-Type: text/html; charset=utf-8');
header('Content-Disposition: inline; filename="comparison_summary_' . $comparison_id . '.html"');
echo $html;
