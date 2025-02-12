<?php
require_once 'vendor/autoload.php';
use Dompdf\Dompdf;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get HTML content from POST request
    $htmlContent = $_POST['htmlContent'] ?? '';
    $title = $_POST['title'] ?? '';

    // Build the complete HTML content
    $completeHtml = "<!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>IMS-Dashboard</title>
        <link rel='preconnect' href='https://fonts.googleapis.com'>
        <link rel='preconnect' href='https://fonts.gstatic.com' crossorigin>
        <link href='https://fonts.googleapis.com/css2?family=Noto+Sans:ital,wght@0,100..900;1,100..900&display=swap' rel='stylesheet'>
        <style>
            body { font-family: 'Noto Sans', sans-serif; }
            .workbord { margin-top: 20px; padding-right: 30px; border-left: 100px; height: 100vh; margin: 0 auto; padding: 80px; }
            table { width: 100%; border-collapse: collapse; }
            table, th, td { border: 1px solid #f1f1f1; }
            th, td { padding: 8px; text-align: left; }
        ";

    // Check the referer to add dynamic styles
    if (isset($_SERVER['HTTP_REFERER']) && basename($_SERVER['HTTP_REFERER']) === 'product.php') {
        $completeHtml .= "table, th, td { border: 1px solid #f1f1f1; }
            th, td { padding: 2px; text-align: left; }";
    }

    $completeHtml .= "</style>
    </head>
    <body>
        <div id='wrapper'>
            <main id='site__main' class='site__main'>
                <section class='workbord'>
                    <div class='workboard__heading'>
                        <h1 class='workboard__title'>Inventory Management System</h1>
                        <h3>" . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . "</h3>
                    </div>
                    <div class='workpanel salesreport__main'>
                        <div class='row'>
                            <div class='col xs-12'>
                                <div class='questionaries__showcase' id='question_popup' style='display: flex;'>
                                    <div class='tbl-wrap'>
                                        <table id='table'>
                                            " . $htmlContent . "
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </main>
        </div>
    </body>
    </html>";

    // Initialize Dompdf
    $dompdf = new Dompdf();

    // Load HTML content
    $dompdf->loadHtml($completeHtml);

    // (Optional) Set paper size and orientation
    $dompdf->setPaper('A4', 'portrait');

    try {
        // Render the HTML as PDF
        $dompdf->render();

        // Stream the generated PDF to the browser
        $dompdf->stream("document.pdf", ["Attachment" => true]);
    } catch (Exception $e) {
        error_log("Dompdf error: " . $e->getMessage());
        echo "An error occurred while generating the PDF.";
    }
} else {
    echo "Invalid request method.";
}
?>
