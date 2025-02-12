<?php
$page_title = 'Edit Category';
require_once('includes/load.php');

// Check if the user has the required permission level
page_require_level(1);
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID is missing or empty. Debug info: " . print_r($_GET, true));
}

// Check if the ID parameter exists
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $session->msg("d", "Missing category ID.");
    redirect('categorie.php');
}

$category_id = (int)$_GET['id'];
$category = find_by_id('categories', $category_id);

if (!$category) {
    $session->msg("d", "Category not found.");
    redirect('categorie.php');
}

if (isset($_POST['update_cat'])) {
    $req_fields = array('categorie-name', 'threshold');
    validate_fields($req_fields);

    $cat_name = remove_junk($db->escape($_POST['categorie-name']));
    $threshold = (int)$_POST['threshold'];

    if (empty($errors)) {
        $sql  = "UPDATE categories SET ";
        $sql .= "name = '{$cat_name}', ";
        $sql .= "threshold = '{$threshold}' ";
        $sql .= "WHERE id = '{$category_id}'";

        if ($db->query($sql)) {
            $session->msg("s", "Category updated successfully.");
            redirect('categorie.php', false);
        } else {
            $session->msg("d", "Failed to update category.");
            redirect("edit_categorie.php?id={$category_id}", false);
        }
    } else {
        $session->msg("d", $errors);
        redirect("edit_categorie.php?id={$category_id}", false);
    }
}
?>
<?php include_once('layouts/header.php'); ?>
<div class="col-md-12">
     <?php echo display_msg($msg); ?>
   </div>
					<div class="workboard__heading">
						<h1 class="workboard__title">Edit Category</h1>
					</div>
					<div class="workpanel">
						<form class="general--form access__form" method="post" action="edit_categorie.php?id=<?php echo $category['id']; ?>">
							<div class="overall-info">
								<div class="row">
									<div class="col xs-12">
										<div class="info">
											<div class="row">
												<div class="col xs-12 sx-6">
                        <span>Editing</span>
												</div>
												<div class="col xs-12 sx-6">
													<!-- <form action="get"> -->
														<div class="site-panel">
															<div class="form__action">
																<input type="submit" name="update_cat" class="button primary-tint" value="Save">
															</div>
														</div>
													<!-- </form> -->
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col xs-12 sm-3">
												<div class="form__module">
												<label for="categorie-name" class="form__label">Category Name</label>
													<div class="form__set">
													<input type="text" class="form-control" name="categorie-name" value="<?php echo remove_junk($category['name']); ?>" required>
													</div>
												</div>
											</div>
											<div class="col xs-12 sm-3">
												<div class="form__module">
													<label class="form__label" for="threshold">Threshold Value</label>
													<div class="form__set">
													<input type="number" class="form-control" name="threshold" value="<?php echo (int)$category['threshold']; ?>" required>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</form>
			</div>
      <?php include_once('layouts/footer.php'); ?>