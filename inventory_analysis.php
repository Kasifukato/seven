<?php
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(3);



// Debugging step
if (!isset($dbh)) {
    die("Database connection not initialized.");
}
?>

<?php include_once('layouts/header.php'); ?>
<!-- <?php echo display_msg($msg); ?> -->
<div class="workboard__heading">
	<h1 class="workboard__title">Inventory Analysis</h1>
</div>
<div class="workpanel report__main">
	<div class="row">
		<div class="col xs-12">
			<div class="row">
				<div class="col xs-12">
					<div class="meta-info">
						<div class="row">
							<div class="col xs-12 sm-6">
								<h2 class="subheading">EOQ Analysis</h2>
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
                                    <?php 
                                $sql = "SELECT p.*, 
                                        ia.eoq,
                                        ia.safety_stock,
                                        ia.reorder_point,
                                        ia.seasonal_index,
                                        ia.last_calculated,
                                        COUNT(s.id) as total_sales
                                    FROM products p 
                                    LEFT JOIN inventory_analysis ia ON p.id = ia.product_id
                                    LEFT JOIN sales s ON p.id = s.product_id
                                    WHERE p.status = 'active'
                                    GROUP BY p.id";
                                $query = $dbh->prepare($sql);
                                $query->execute();
                                $results = $query->fetchAll(PDO::FETCH_OBJ);
                                ?>
										<table id="sales__table">
											<thead>
                                                <tr>
                                                    <th>S.N</th>
                                                    <th>Product</th>
                                                    <th>Current Stock</th>
                                                    <th>EOQ</th>
                                                    <th>Reorder Point</th>
                                                    <th>Safety Stock</th>
                                                    <th>Total Sales</th>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                $cnt=1;
                                                if($query->rowCount() > 0) {
                                                    foreach($results as $result) {
                                                        if(!$result->eoq) {
                                                            $annual_demand = $result->total_sales;
                                                            $holding_cost = $result->holding_cost ? $result->holding_cost : ($result->buy_price * 0.2); // 20% of cost
                                                            $order_cost = 50; // default order cost
                                                    
                                                            $eoq = sqrt((2 * $annual_demand * $order_cost) / $holding_cost);
                                                            $safety_stock = sqrt($annual_demand) * 0.5; // simple safety stock calculation
                                                            $reorder_point = ($annual_demand/365) * 7 + $safety_stock; // 7 days lead time
                                                            $sql = "INSERT INTO inventory_analysis 
                                                            (product_id, eoq, safety_stock, reorder_point, seasonal_index, last_calculated)
                                                            VALUES (:pid, :eoq, :ss, :rop, 1, NOW())
                                                            ON DUPLICATE KEY UPDATE
                                                            eoq = :eoq, safety_stock = :ss, reorder_point = :rop, last_calculated = NOW()";
                                                            $query2 = $dbh->prepare($sql);
                                                            $query2->bindParam(':pid', $result->id, PDO::PARAM_INT);
                                                            $query2->bindParam(':eoq', $eoq, PDO::PARAM_STR);
                                                            $query2->bindParam(':ss', $safety_stock, PDO::PARAM_STR);
                                                            $query2->bindParam(':rop', $reorder_point, PDO::PARAM_STR);
                                                            $query2->execute();
                                                    
                                                            $result->eoq = $eoq;
                                                            $result->safety_stock = $safety_stock;
                                                            $result->reorder_point = $reorder_point;
                                                        }
                                                        ?>
                                                        <tr>
                                                            <td><?php echo htmlentities($cnt);?></td>
                                                            <td><?php echo htmlentities($result->name);?></td>
                                                            <td><?php echo htmlentities($result->quantity);?></td>
                                            <td><?php echo round($result->eoq, 2);?></td>
                                            <td><?php echo round($result->reorder_point, 2);?></td>
                                            <td><?php echo round($result->safety_stock, 2);?></td>
                                            <td><?php echo htmlentities($result->total_sales);?></td>
                                            <td><?php 
                                                if($result->quantity <= $result->reorder_point) {
                                                    echo '<span class="label label-danger">Reorder Needed</span>';
                                                } else {
                                                    echo '<span class="label label-success">OK</span>';
                                                }
                                            ?></td>
                                            <td>
                                                <?php if($result->quantity <= $result->reorder_point) { ?>
                                                    <a href="add_orders.php?pid=<?php echo $result->id;?>&qty=<?php echo round($result->eoq);?>" 
                                                       class="btn btn-primary btn-xs">Order <?php echo round($result->eoq);?> units</a>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                        <?php $cnt=$cnt+1; }} ?>

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

