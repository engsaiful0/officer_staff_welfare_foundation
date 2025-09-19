<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Http\Controllers\MonthlyFeeReportController;
use Illuminate\Http\Request;

echo "Testing Monthly Report API...\n";

// Create a test request
$request = new Request();
$request->merge([
    'month' => 9,
    'year' => 2025,
    'status' => 'all',
    'search' => ''
]);

// Test the controller
$controller = new MonthlyFeeReportController();

try {
    $response = $controller->getReportData($request);
    $data = $response->getData(true);
    
    echo "Success: " . ($data['success'] ? 'true' : 'false') . "\n";
    echo "Data count: " . count($data['data']['data'] ?? []) . "\n";
    echo "Stats: " . json_encode($data['stats'] ?? []) . "\n";
    
    // Check fee amounts directly from database
    $totalFeeAmount = \App\Models\MonthlyFeePayment::where('month', 9)->where('year', 2025)->sum('fee_amount');
    echo "Direct DB fee amount: " . $totalFeeAmount . "\n";
    
    $samplePayment = \App\Models\MonthlyFeePayment::where('month', 9)->where('year', 2025)->first();
    if ($samplePayment) {
        echo "Sample payment - Fee: " . $samplePayment->fee_amount . ", Total: " . $samplePayment->total_amount . "\n";
    }
    
    if (isset($data['data']['data'])) {
        echo "First few records:\n";
        foreach (array_slice($data['data']['data'], 0, 3) as $index => $payment) {
            echo "  " . ($index + 1) . ". Student: " . ($payment['student']['full_name_in_english_block_letter'] ?? 'N/A') . 
                 ", Paid: " . ($payment['is_paid'] ? 'Yes' : 'No') . 
                 ", Overdue: " . ($payment['is_overdue'] ? 'Yes' : 'No') . "\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
