<?php

use Slacklight\AuthenticationManager;
use Slacklight\Util;
use Data\DataManager;

if (AuthenticationManager::isAuthenticated()) {
    Util::redirect("index.php");
}
$userName = isset($_REQUEST['userName']) ? $_REQUEST['userName'] : null;

$channels = DataManager::getChannels();

?>

<?php
require_once('views/partials/header.php');
?>


    <div class="page-header">
        <h2>Register</h2>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            Please fill out the form below:
        </div>
        <div class="panel-body">

            <form class="form-horizontal" method="post" action="<?php echo Util::action(Slacklight\Controller::ACTION_REGISTER, array('view' => $view)); ?>">
                <div class="form-group">
                    <label for="inputName" class="col-sm-2 control-label">User name:</label>
                    <div class="col-sm-6">
                        <input type="text" class="form-control" id="inputName" name="<?php print Slacklight\Controller::USER_NAME; ?>" placeholder="username" value="<?php echo htmlentities($userName); ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputPassword" class="col-sm-2 control-label">Password</label>
                    <div class="col-sm-6">
                        <input type="password" class="form-control" id="inputPassword" name="<?php print Slacklight\Controller::USER_PASSWORD; ?>" placeholder="password">
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputName" class="col-sm-2 control-label">Which Channels to join?</label>
                    <div class="col-sm-6">
                        <?php foreach ($channels as $channel) : ?>    
                            <div class="form-check ">
                                <input class="form-check-input" type="checkbox" value="<?php print $channel->getId(); ?>" name="<?php print Slacklight\Controller::SELECTED_CHANNELS_REGISTER; ?>[]">
                                <label class="form-check-label" >
                                    #<?php echo $channel->getName(); ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-6">
                        <button type="submit" class="btn btn-default">Register</button>
                    </div>
                </div>
            </form>
            <a href="<?php echo $_SERVER['PHP_SELF']; ?>?view=login" > you have already an account? -> Login</a>
        </div>
    </div>

<?php
require_once('views/partials/footer.php');