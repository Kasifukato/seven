<?php
$page_title = 'All Image';
require_once('includes/load.php');
page_require_level(2);

$media_files = find_all('media');

// Handle File Upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
  $photo = new Media();
  $photo->upload($_FILES['file_upload']);
  if ($photo->process_media()) {
    $session->msg('s', 'Photo has been uploaded.');
    redirect('media.php');
  } else {
    $session->msg('d', join($photo->errors));
    redirect('media.php');
  }
}
?>

<?php include_once('layouts/header.php'); ?>
<div class="workboard__heading">
  <h1 class="workboard__title">Media</h1>
</div>
<div class="workpanel media__main">
  <?php echo display_msg($msg); ?>
  <div class="row">
    <div class="col xs-12">
     <form action="media.php" method="POST" enctype="multipart/form-data">
      <div class="profile__img">
        <div class="upload">
          <label for="file-upload" class="custom-photo-upload  primary-line">Choose File</label>
          <input type="file" name="file_upload" id="file-upload" class="btn btn-primary btn-file">
          <input type="submit" name="submit" class="custom-photo-upload primary-tint" value="Upload">
        </div>
      </div>
    </form>
  </div>
</div>
<div class="meta-info">
  <div class="row">
    <div class="col xs-12">
      <form method="POST" action="ajax.php" autocomplete="off" id="sug-form">
        <div class="site-panel">
          <div class="form__module">
            <div class="form__set ">
              <button type="submit" class="icon-search"></button>
              <input class="search-input" type="text" name="title" placeholder="Search">
            </div>
            <div id="result" class="list-group"></div>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
<div class="row">
  <div class="col xs-12">
    <div class="questionaries__showcase" id="question_popup" style="display: flex;">
      <div class="tbl-wrap">
        <table id="tracking__table">
          <thead>
            <tr>
              <th class="dtser">S.N</th>
              <th>Photo</th>
              <th>Photo Name</th>
              <th>Photo Type</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($media_files as $media_file): ?>
              <tr class="list-inline">
                <td class="text-center"><?php echo count_id(); ?></td>
                <td class="text-center">
                  <div class="profile--img">
                    <img src="uploads/products/<?php echo $media_file['file_name']; ?>" class="img-thumbnail" />
                  </div>
                </td>
                <td class="text-center">
                  <?php echo $media_file['file_name']; ?>
                </td>
                <td class="text-center">
                  <?php echo $media_file['file_type']; ?>
                </td>

                <td data-label="Action"><a href="delete_media.php?id=<?php echo (int) $media_file['id']; ?>">
                  <span class="icon-trash"></span></a></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include_once('layouts/footer.php'); ?>