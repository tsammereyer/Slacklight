<?php 
  use Data\DataManager;
  $channels = DataManager::getChannels();
  $channelId = isset($_REQUEST['channelId']) ? (int) $_REQUEST['channelId'] : null;
  $selectedChannel =  DataManager::getChannelByChannelId($channelId);
  $messages = array();
  $topics = array();
  if($channelId !== null){
    $messages = DataManager::getMessagesByChannelId($channelId);
    $selectedChannel =  DataManager::getChannelByChannelId($channelId);
    $topics = DataManager::getTopicsByChannelId($channelId);
  }
  //var_dump($messages);
  //var_dump($selectedChannel);
  //var_dump($topics);
  
?>

<?php require_once('views/partials/header.php'); ?>


<div class="container-fluid">
  <div class="row">
    <?php require_once('views/partials/sidebar.php'); ?>
    <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">

    <h2 class="page-header">#<?php echo $selectedChannel; ?></h2>

    <?php foreach ($topics as $topic) : ?>
      <h4 class="sub-header"><?php echo $topic->getName(); ?></h4>
    <?php endforeach; ?>

    <br/>

    <?php foreach ($messages as $message) : ?>
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title"><?php echo $message->getUsername(); ?></h3>
            <?php echo $message->getCreated(); ?>
        </div>
        <div class="panel-body">
          <?php echo $message->getContent(); ?>
        </div>
      </div>
    <?php endforeach; ?>

    <div class="input-group">
      <input type="text" class="form-control" placeholder="Jot your message down here">
      <span class="input-group-btn">
        <button class="btn btn-default" type="button">Send Message!</button>
      </span>
    </div>
  
</div>


<?php require_once('views/partials/footer.php'); ?>