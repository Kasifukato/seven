<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
$page_title = 'Admin Home Page';
require_once('includes/load.php');
page_require_level(1);

$last_user_update = get_last_user_update_time();
$last_product_update = get_last_product_update_time();
$last_sale_update = get_last_sale_update_time();
$last_category_update = get_last_categories_update_time(); 

  $c_categorie     = count_by_id('categories');
  $c_product       = count_by_id('products');
  $c_sale          = count_by_id('sales');
  $c_user          = count_by_id('users');
  $products_sold   = find_higest_saleing_product('10');
  $recent_products = find_recent_product_added('5');
$recent_sales    = find_recent_sale_added('5');
?>
<?php include_once('layouts/header.php'); ?>
<div class="adm-dashboard__main">
  <?php echo display_msg($msg); ?>
  <div class="workboard__heading">
    <h1 class="workboard__title">Dashboard</h1>
  </div>
  <div class="workpanel">
    <div class="row">
      <div class="col xs-12 sx-8">
        <div class="row">
          <div class="col xs-12">
            <div class="date">
              <input type="date" value="<?php echo date('Y-m-d'); ?>" />
            </div>
          </div>
        </div>
        <div class="insights">
          <div class="row">
            <div class="col xs-12 sm-3">
              <div class="panel">
                <div class="sales">
                  <div class="middle">
                    <div class="left">
                      <h3>Users</h3>
                      <h1><?php echo $c_user['total']; ?></h1>
                    </div>
                    <div class="progress">
                      <span class="icon-user"></span>
                    </div>
                  </div>
                  <small class="text-muted"><?php echo date('M d, Y ', strtotime($last_user_update)); ?></small>
                </div>
              </div>
            </div>
            <div class="col xs-12 sm-3">
              <div class="panel">
                <div class="sales">
                  <div class="middle">
                    <div class="left">
                      <h3>Categories</h3>
                      <h1><?php echo $c_categorie['total']; ?></h1>
                    </div>
                    <div class="progress">
                      <span class="icon-product"></span>
                    </div>
                  </div>
                  <small class="text-muted"><?php echo date('M d, Y ', strtotime($last_category_update)); ?></small>
                </div>
              </div>
            </div>
            <div class="col xs-12 sm-3">
              <div class="panel">
                <div class="expenses">
                  <div class="middle">
                    <div class="left">
                      <h3>Products</h3>
                      <h1><?php echo $c_product['total']; ?></h1>
                    </div>
                    <div class="progress">
                      <span class="icon-order"></span>
                    </div>
                  </div>
                  <small class="text-muted"><?php echo date('M d, Y ', strtotime($last_product_update)); ?></small>
                </div>
              </div>
            </div>
            <div class="col xs-12 sm-3">
              <div class="panel">
                <div class="income">
                  <div class="middle">
                    <div class="left">
                      <h3>Sales</h3>
                      <h1><?php echo $c_sale['total']; ?></h1>
                    </div>
                    <div class="progress">
                      <span class="icon-receipt"></span>
                    </div>
                  </div>
                  <small class="text-muted"><?php echo date('M d, Y ', strtotime($last_sale_update)); ?></small>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="recent-orders">
          <div class="row">
            <div class="col xs-12">
              <h2 class="subheading">Selling Products</h2>
              <div class="tbl-wrap">
                <table>
                  <thead>
                    <tr>
                      <th>Title</th>
                      <th>Total Sold</th>
                      <th>Total Quantity</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($products_sold as  $product_sold): ?>
                      <tr>
                        <td><?php echo remove_junk(first_character($product_sold['name'])); ?></td>
                        <td><?php echo (int)$product_sold['totalSold']; ?></td>
                        <td><?php echo (int)$product_sold['totalQty']; ?></td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col xs-12 sx-6 sm-4">
        <div class="recent-updates">
          <h2 class="subheading">Recent Updates</h2>
          <div class="updates">
            <?php
            $counter = 0; // Initialize counter
            foreach ($recent_products as $recent_product):
              if ($counter >= 3) break; // Stop after 3 products
              ?>
              <a href="edit_product.php?id=<?php echo (int)$recent_product['id']; ?>" class="update-link">
                <div class="update">
                  <div class="message">
                    <div class="profile-photo">
                      <?php if ($recent_product['media_id'] === '0'): ?>
                        <img src="uploads/products/no_image.png" alt="No Image" />
                      <?php else: ?>
                        <img src="uploads/products/<?php echo $recent_product['image']; ?>" alt="<?php echo remove_junk($recent_product['name']); ?>" />
                      <?php endif; ?>
                    </div>
                    <div class="msg--info">
                      <p>
                        <span class="product"><b><?php echo remove_junk(first_character($recent_product['name'])); ?></b></span>
                      </p>
                      <small class="details"><?php echo remove_junk(first_character($recent_product['categorie'])); ?></small>
                    </div>
                  </div>
                  <div class="sales">
                    <div class="amunt">
                      <span class="amount">Rs.<?php echo (int)$recent_product['sale_price']; ?></span>
                    </div>
                  </div>
                </div>
              </a>
              <?php
              $counter++; 
            endforeach;
            ?>
          </div>
        </div>
        <div class="sales-analytics">
          <h2 class="subheading">Sales Analytics</h2>
          <?php foreach ($recent_sales as $recent_sale): ?>
            <a href="edit_sale.php?id=<?php echo (int)$recent_sale['id']; ?>" class="analytics-link" style="text-decoration: none; color: inherit;">
              <div class="item online" style="text-align: justify;">
                <div class="right" style="text-align: justify;">
                  <div class="info" style="text-align: justify;">
                    <h3 class="productname" style="text-align: justify;"><?php echo remove_junk(first_character($recent_sale['name'])); ?></h3>
                    <small class="text-muted" style="text-align: justify; color: #2baa3d;"><?php echo remove_junk(ucfirst($recent_sale['date'])); ?></small>
                  </div>

                  <span class="total sales" style="text-align: justify;"><b>Rs. <?php echo remove_junk(first_character($recent_sale['price'])); ?></b></span>
                </div>
              </div>
            </a>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>

  <?php include_once('layouts/footer.php'); ?>