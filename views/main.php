<?php 
  use Data\DataManager, Slacklight\AuthenticationManager, Slacklight\Util;;
  $channels = DataManager::getChannels();
  $channelId = isset($_REQUEST['channelId']) ? (int) $_REQUEST['channelId'] : null;
  $selectedChannel =  null;
  $messages = array();
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
    
    

    <?php foreach ($messages as $message) : ?>
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">
            <?php if ($message->getSeen() == 0): ?>
              <strong><?php echo $message->getUsername(); ?></strong>
              <?php else: ?>
              <?php echo $message->getUsername(); ?>
            <?php endif; ?>
          </h3>
            <?php echo $message->getCreated(); ?>
        </div>
        <div class="panel-body">
          <?php echo $message->getContent(); ?>
          <!-- if seen from others? O.o-->
          <br>         
          <form class="form-horizontal" method="post" action="<?php echo Util::action(Slacklight\Controller::ACTION_DELETEMESSAGE, array('view' => $view, "messageId" => $message->getId(), "channelId" => $channelId)); ?>">
            <button class="btn btn-default" type="submit"><span class="glyphicon glyphicon-trash"></span></button>
          </form>

          <?php if ($message->getFavourite() == 1): ?>
            <form class="form-horizontal" method="post" action="<?php echo Util::action(Slacklight\Controller::ACTION_UNSTARMESSAGE, array('view' => $view, "messageId" => $message->getId(), "channelId" => $channelId)); ?>">
              <button class="btn btn-default" type="submit"><span class="glyphicon glyphicon-star"></span></button>
            </form>
          <?php else: ?>
            <form class="form-horizontal" method="post" action="<?php echo Util::action(Slacklight\Controller::ACTION_STARMESSAGE, array('view' => $view, "messageId" => $message->getId(), "channelId" => $channelId)); ?>">
              <button class="btn btn-default" type="submit"><span class="glyphicon glyphicon-star-empty"></span></button>
            </form>
            <?php endif; ?>

          
        </div>

      </div>
    <?php endforeach; ?>

    <form class="form-horizontal" method="post" action="<?php echo Util::action(Slacklight\Controller::ACTION_SENDMESSAGE, array('view' => $view, "channelId" => $channelId)); ?>">
    <div class="input-group">
      <input type="text" required class="form-control" id="sendMessageField" name="<?php print Slacklight\Controller::SEND_MESSAGE_FIELD; ?>" placeholder="Jot your message here">
      <span class="input-group-btn">
        <button class="btn btn-default" type="submit">Send Message!</button>
      </span>
    </div>
    </form>
    <?php endif;?>
    <?php if($channelId === null):?>
    <h4><-- Please select a channel on the left in the sidebar</h4>
    <?php endif;?>

</div>


<?php require_once('views/partials/footer.php'); ?>