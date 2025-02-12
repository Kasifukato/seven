<?php
$page_title = 'Sale Report';
require_once('includes/load.php');
// Checkin What level user has permission to view this page
page_require_level(3);
?>
<?php include_once('layouts/header.php'); ?>




<div class="row">
  <div class="col-xs-6">
    <?php echo display_msg($msg); ?>
  </div>
</div>
<div class="workboard__heading">
  <h1 class="workboard__title">Sales</h1>
</div>
<div class="workpanel report__main">
  <div class="overall-info">
    <form class="general--form access__form clearfix" method="post" action="sale_report_process.php">
      <div class="row">
        <div class="col xs-12">
          <div class="info">
            <div class="row">
              <div class="col xs-12 sx-6">
                <span>Date Range</span>
              </div>
              <div class="col xs-12 sx-6">
                <form action="get">
                  <div class="site-panel">
                    <div class="form__action">
                      <input type="submit" name="submit" class="button primary-tint" value="Generate">
                    </div>
                  </div>
                </form>
              </div>
            </div>

            <div class="row">
              <div class="col xs-12 sm-3">
                <div class="form__module">
                  <div class="form__set">
                    <input type="date" id="startdate" class="datepicker form-control" name="start-date" placeholder="From">
                    <input type="date" id="enddate" class="datepicker form-control" name="end-date" placeholder="To">
                  </div>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>
    </form>
  </div>
 </div>

<?php include_once('layouts/footer.php'); ?>