<?php 
  use Data\DataManager;
  $channels = DataManager::getChannels();
  $channelId = isset($_REQUEST['channelId']) ? (int) $_REQUEST['channelId'] : null;
  $selectedChannel =  DataManager::getChannelByChannelId($channelId);
  $messages = array();
  if($channelId !== null){
    $messages = DataManager::getMessagesByChannel($channelId);
  }
  //var_dump($messages);
  //var_dump($selectedChannel);
  
?>

<?php require_once('views/partials/header.php'); ?>



<div class="container-fluid">
  <div class="row">
    <?php require_once('views/partials/sidebar.php'); ?>
    <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">

    <h2 class="page-header">#general</h2>
       
    <h4 class="sub-header">Company-wide announcements and work-based matters</h4>
    <br/>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title">Admin</h3>
        19:16 on 2018-05-14
      </div>
      <div class="panel-body">
        läuft bei euch das feedback. hab jz zum zweiten mal keins bekommen und vom tutor sowieso noch nie eins.
      </div>
    </div>

    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title">Admin</h3>
        19:16 on 2018-05-14
      </div>
      <div class="panel-body">
        läuft bei euch das feedback. hab jz zum zweiten mal keins bekommen und vom tutor sowieso noch nie eins.
      </div>
    </div>

    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title">Admin</h3>
        19:16 on 2018-05-14
      </div>
      <div class="panel-body">
        läuft bei euch das feedback. hab jz zum zweiten mal keins bekommen und vom tutor sowieso noch nie eins.
      </div>
    </div>

  
  

    <div class="input-group">
      <input type="text" class="form-control" placeholder="Jot your message down here">
      <span class="input-group-btn">
        <button class="btn btn-default" type="button">Send Message!</button>
      </span>
    </div><!-- /input-group -->
  
</div>


<?php require_once('views/partials/footer.php'); ?>