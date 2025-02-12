<?php
  $page_title = 'All categories';
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(1);
  
  $all_categories = find_all('categories')
?>
<?php
if (isset($_POST['add_cat'])) {
    $req_field = array('categorie-name', 'threshold');
    validate_fields($req_field);

    $cat_name = remove_junk($db->escape($_POST['categorie-name']));
    $threshold = remove_junk($db->escape($_POST['threshold']));

    // Validate threshold is a non-negative integer
    if (!ctype_digit($threshold) && $threshold !== '0') {
        $session->msg("d", "Threshold must be zero or a positive whole number.");
        redirect('categorie.php', false);
        exit;
    }

    // Get current date and time
    $current_date = date('Y-m-d H:i:s');

    if (empty($errors)) {
        $sql  = "INSERT INTO categories (name, threshold, date)";
        $sql .= " VALUES ('{$cat_name}', '{$threshold}', '{$current_date}')";

        if ($db->query($sql)) {
            $session->msg("s", "Successfully Added New Category");
            redirect('categorie.php', false);
        } else {
            $session->msg("d", "Sorry Failed to insert.");
            redirect('categorie.php', false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('categorie.php', false);
    }
}
?>
<?php include_once('layouts/header.php'); ?>
     <div class="col-md-12">
       <?php echo display_msg($msg); ?>
     </div>
					<div class="workboard__heading">
						<h1 class="workboard__title">Category</h1>
					</div>
					<div class="workpanel">
						<form class="general--form access__form" method="post" action="categorie.php">
							<div class="overall-info">
								<div class="row">
									<div class="col xs-12">
										<div class="info">
											<div class="row">
												<div class="col xs-12 sx-6">
													<span>New Category</span>
												</div>
												<div class="col xs-12 sx-6">
														<div class="site-panel">
															<div class="form__action">
																<input type="submit"  name="add_cat" class="button primary-tint" value="Save">
															</div>
														</div>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col xs-12 sm-3">
												<div class="form__module">
													<label for="name" class="form__label">Name</label>
													<div class="form__set">
														<input type="text" id="name" name="categorie-name" placeholder="Category Name" required>
													</div>
												</div>
											</div>
											<div class="col xs-12 sm-3">
												<div class="form__module">
													<label for="threshold" class="form__label">Threshold Value</label>
													<div class="form__set">
														<input 
                              type="number" 
                              id="threshold" 
                              name="threshold" 
                              placeholder="Threshold Value" 
                              min="0" 
                              step="1"
                              onkeydown="return event.keyCode !== 190"
                              oninput="this.value = Math.abs(this.value.replace(/[^\d]/g, ''))"
                              required>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</form>
						
						<div class="row">
							<div class="col xs-12">
								<div class="questionaries__showcase">
									<div class="tbl-wrap">
										<table id="tracking__table">
											<thead>
												<tr>
													<th class="dtser">S.N</th>
													<th class="categories">Categories</th>
													<th class="threshold">Threshold Value</th>
													<th class="action">Action</th>
												</tr>
											</thead>
											<tbody>
											<?php foreach ($all_categories as $cat): ?>
												<tr>     
													<td class="text-center"><?php echo count_id(); ?></td>
													<td><?php echo remove_junk(ucfirst($cat['name'])); ?></td>
													<td><?php echo (int)$cat['threshold']; ?></td>
													<td class="text-center">
														 <div class="btn-group">
															<a href="edit_categorie.php?id=<?php echo (int)$cat['id'];?>" class="btn btn-xs btn-warning" data-toggle="tooltip" title="Edit">
															<span class="icon-edit"></span>
														 </a>
														 <a href="delete_categorie.php?id=<?php echo (int)$cat['id'];?>" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Remove">
														 <span class="icon-trash"></span>
														</a>
													</div>
												</td></tr>
												<?php endforeach; ?>
											</tbody>
										</table>
									</div>
								</div>
							</div>
						</div>
					</div>
					<?php include_once('layouts/footer.php'); ?>