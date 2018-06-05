<?php 
  use Data\DataManager, Slacklight\AuthenticationManager, Slacklight\Util;;
  $channels = DataManager::getChannels();
  $channelId = isset($_REQUEST['channelId']) ? (int) $_REQUEST['channelId'] : null;
  $messageId = isset($_REQUEST['messageId']) ? (int) $_REQUEST['messageId'] : null;
  $selectedChannel =  null;
  $message = DataManager::getMessageById($messageId); //Todo
  $topics = array();
  if($channelId !== null){
    $messages = DataManager::getMessagesByChannelId($channelId);
    $selectedChannel =  DataManager::getChannelByChannelId($channelId);
    $topics = DataManager::getTopicsByChannelId($channelId);
  }

  $user = AuthenticationManager::getAuthenticatedUser();
  if ($user == null){
    header('Location: ' . $_SERVER['PHP_SELF'] . '?view=login');
  }

  //var_dump($message);
  //die();
  //var_dump($messages);
  //var_dump($selectedChannel);
  //var_dump($topics);
  
?>

<?php require_once('views/partials/header.php'); ?>


<div class="container-fluid">
  <div class="row">
    <?php require_once('views/partials/sidebar.php'); ?>

    
    <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
    <?php if($channelId !== null):?>
    <h2 class="page-header">#<?php echo $selectedChannel; ?></h2>

    <?php foreach ($topics as $topic) : ?>
      <h4 class="sub-header"><?php echo $topic->getName(); ?></h4>
    <?php endforeach; ?>

    <br/>

    <form class="form-horizontal" method="post" action="<?php echo Util::action(Slacklight\Controller::ACTION_UPDATEMESSAGE, array('view' => $view, "channelId" => $channelId, "messageId" => $messageId)); ?>">
    <div class="input-group">
      <input type="text" required class="form-control" id="<?php echo Slacklight\Controller::UPDATE_MESSAGE_FIELD;?>" name="<?php print Slacklight\Controller::UPDATE_MESSAGE_FIELD; ?>" value="<?php echo $message->getContent(); ?>">
      <span class="input-group-btn">
        <button class="btn btn-default" type="submit">Update Message!</button>
      </span>
    </div>
    </form>
    <?php endif;?>

</div>


<?php require_once('views/partials/footer.php'); ?>