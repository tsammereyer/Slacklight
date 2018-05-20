<?php 
  use Data\DataManager;
  $channels = DataManager::getChannels();
  $channelId = isset($_REQUEST['channelId']) ? (int) $_REQUEST['channelId'] : null;
?>
<div class="col-sm-3 col-md-2 sidebar">
  <ul class="nav nav-sidebar">
    <?php foreach ($channels as $channel) : ?>
      <li <?php if ($channel->getId() === $channelId) { ?> class="active" <?php } ?>>
        <a href="<?php echo $_SERVER['PHP_SELF']; ?>?view=main&channelId=<?php echo urlencode($channel->getId()); ?>">#
          <?php echo $channel->getName(); ?>
        </a>
    </li>
    <?php endforeach; ?>
  </ul>
</div>
