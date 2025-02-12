<?php
require_once('includes/load.php');

$servername = "localhost";
$username = "root";      // Default XAMPP username
$password = "";          // Default XAMPP password is empty
$dbname = "inventory_system";  // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
}
// Set the smoothing factor (alpha) for Exponential Smoothing
$alpha = 0.5; // Can be adjusted between 0 and 1

// Fetch sales data grouped by month and product
$sql = "SELECT product_id, DATE_FORMAT(date, '%Y-%m') AS month, SUM(qty) AS total_sales 
FROM sales 
GROUP BY product_id, month 
ORDER BY product_id, month";
$result = $conn->query($sql);

$sales_data = [];

if ($result->num_rows > 0) {
	while ($row = $result->fetch_assoc()) {
		$sales_data[$row['product_id']][] = [
			'month' => $row['month'],
			'sales' => $row['total_sales']
		];
	}
}

// Calculate forecast using Exponential Smoothing
$forecast_results = [];

foreach ($sales_data as $product_id => $data) {
    $previous_forecast = $data[0]['sales']; // Initialize forecast with the first actual sales
    foreach ($data as $index => $entry) {
    	$actual_sales = $entry['sales'];
    	
        // Apply Exponential Smoothing formula
    	$forecast = ($alpha * $actual_sales) + ((1 - $alpha) * $previous_forecast);
    	
        // Calculate percentage error if actual sales exist
    	$percentage_error = ($actual_sales != 0) ? (($actual_sales - $previous_forecast) / $actual_sales) * 100 : 0;
    	
    	$forecast_results[] = [
    		'product_id' => $product_id,
    		'month' => $entry['month'],
    		'actual_sales' => $actual_sales,
    		'forecasted_sales' => round($forecast, 2),
    		'percentage_error' => round($percentage_error, 2)
    	];

        $previous_forecast = $forecast; // Update forecast for next iteration
    }
}
?>

<?php include_once('layouts/header.php'); ?>
<!-- <?php echo display_msg($msg); ?> -->
<div class="workboard__heading">
	<h1 class="workboard__title">Sales Prediction</h1>
</div>
<div class="workpanel report__main">
	<div class="row">
		<div class="col xs-12">
			<div class="row">
				<div class="col xs-12">
					<div class="meta-info">
						<div class="row">
							<div class="col xs-12 sm-6">
								<h2 class="subheading"></h2>
							</div>
							<div class="col xs-12 sm-6">
								<form method="POST">
									<div class="site-panel">
										<div class="form__module">
											<div class="form__action">
												<span class="icon-add"></span>
												<input type="submit" class="button primary-tint" value="Add sales" name="add_sale">
											</div>
										</div>
										<div class="downoad">
											<a href="" id="download_btn"><span class="icon-download"></span>Download</a>
										</div>
									</div>
								</form>
							</div>
						</div>
						<div class="row">
							<div class="col xs-12">
								<div class="questionaries__showcase" id="question_popup" style="display: flex;">
									<div class="tbl-wrap">
										<table id="sales__table">
											<thead>
												<tr>
													<th>Product ID</th>
													<th>Month</th>
													<th>Actual Sales</th>
													<th>Forecasted Sales</th>
													<th>Percentage Error (%)</th>
												</tr>
											</thead>
											<tbody>

												<?php foreach ($forecast_results as $result): ?>
													<tr>
														<td><?= $result['product_id']; ?></td>
														<td><?= $result['month']; ?></td>
														<td><?= $result['actual_sales']; ?></td>
														<td><?= $result['forecasted_sales']; ?></td>
														<td><?= $result['percentage_error']; ?>%</td>
													</tr>
												<?php endforeach; ?>
											</tbody>
										</table>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php include_once('layouts/footer.php'); ?>

