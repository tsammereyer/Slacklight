<?php 

namespace Slacklight;

class Controller extends BaseObject {

    const ACTION = 'action';
    const PAGE = 'page';
    const ACTION_ADD = 'addToCart';
    const ACTION_REMOVE = 'removeFromCart';
    const ACTION_LOGIN = 'login';
    const ACTION_REGISTER = 'register';
    const ACTION_LOGOUT = 'logout';
    const ACTION_DELETEMESSAGE = 'deletemessage';
    const ACTION_STARMESSAGE = 'starmessage';
    const ACTION_UNSTARMESSAGE = 'unstarmessage';
    //const ACTION_EDITMESSAGE = 'editmessage';
    const ACTION_UPDATEMESSAGE = 'updatemessage';
    //DELETE FROM message WHERE id=11 
    const USER_NAME = 'userName';
    const USER_PASSWORD = 'password';
    const SELECTED_CHANNELS_REGISTER = 'check_list';
    const ACTION_ORDER = 'placeOrder';
    const ACTION_SENDMESSAGE = "sendMessage";
    const SEND_MESSAGE_FIELD = 'sendMessageField';
    const UPDATE_MESSAGE_FIELD = 'updateMessageField';



    private static $instance = false;
    private function __construct() {}

    public static function getInstance() : Controller {
        if (!self::$instance) {
            self::$instance = new \Slacklight\Controller();
        }
        return self::$instance;
    }

    public function invokePostAction() : bool {
        
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            throw new Exception ('POST requests only!!!');
            return null;
        }
        elseif (!isset($_REQUEST[self::ACTION])) {
            throw new Exception(self::ACTION .' is not defined');
            return null;
        }

        $action = $_REQUEST[self::ACTION];

        switch ($action) {

            case self::ACTION_ADD :
                \Slacklight\ShoppingCart::add((int) $_REQUEST['bookId']);
                Util::redirect();
                break;

            case self::ACTION_REMOVE :
                \Slacklight\ShoppingCart::remove((int) $_REQUEST['bookId']);
                Util::redirect();
                break;

            case self::ACTION_LOGIN : 
                if (!\Slacklight\AuthenticationManager::authenticate(
                    $_REQUEST[self::USER_NAME], $_REQUEST[self::USER_PASSWORD])) {
                        $this->forwardRequest(array('Invalid credentials'));
                    }
                Util::redirect();
                break;

            case self::ACTION_REGISTER : 
                //var_dump($_REQUEST[self::SELECTED_CHANNELS_REGISTER]);
                //die();
                if (!AuthenticationManager::registerUser($_REQUEST[self::USER_NAME], $_REQUEST[self::USER_PASSWORD], $_REQUEST[self::SELECTED_CHANNELS_REGISTER]))
                    self::forwardRequest(['User already exists.']);
                Util::redirect();
                break;
                default :
                    throw new \Exception('Unknown controller action: ' . $action);
                    break;

            case self::ACTION_LOGOUT : 
                    \Slacklight\AuthenticationManager::signOUt();
                    Util::redirect();
                    break;

            case self::ACTION_ORDER : 
                $user = \Slacklight\AuthenticationManager::getAuthenticatedUser();
                if ($user == null) {
                    $this->forwardRequest(array('Please login'));
                    break;
                }        
                if ($this->processCheckout($_POST[self::CC_NAME], $_POST[self::CC_NUMBER]))
                    break;
                else 
                    return null;

            case self::ACTION_SENDMESSAGE :
                $user = \Slacklight\AuthenticationManager::getAuthenticatedUser();
                $channelId = isset($_REQUEST['channelId']) ? (int) $_REQUEST['channelId'] : null;
                //var_dump($channelId);
                //var_dump($_POST[self::SEND_MESSAGE_FIELD]);
                //die();
                if ($user == null) {
                    $this->forwardRequest(array('Please login'));
                    break;
                }        
                if ($this->sendMessage($channelId, $_POST[self::SEND_MESSAGE_FIELD]))
                    break;
                else 
                    return null;
            
            case self::ACTION_DELETEMESSAGE :
                $user = \Slacklight\AuthenticationManager::getAuthenticatedUser();
                $messageId = isset($_REQUEST['messageId']) ? (int) $_REQUEST['messageId'] : null;
                $channelId = isset($_REQUEST['channelId']) ? (int) $_REQUEST['channelId'] : null;
                //var_dump($messageId);
                //die();
                if ($user == null) {
                    $this->forwardRequest(array('Please login'));
                    break;
                }
                if ($this->deleteMessage($messageId, $channelId))
                    break;
                else 
                    return null;

            case self::ACTION_STARMESSAGE :
                $user = \Slacklight\AuthenticationManager::getAuthenticatedUser();
                $messageId = isset($_REQUEST['messageId']) ? (int) $_REQUEST['messageId'] : null;
                $channelId = isset($_REQUEST['channelId']) ? (int) $_REQUEST['channelId'] : null;
                //var_dump($messageId);
                //die();
                if ($user == null) {
                    $this->forwardRequest(array('Please login'));
                    break;
                }
                if ($this->starMessage($messageId, $channelId))
                    break;
                else 
                    return null;
            
            case self::ACTION_UNSTARMESSAGE :
                $user = \Slacklight\AuthenticationManager::getAuthenticatedUser();
                $messageId = isset($_REQUEST['messageId']) ? (int) $_REQUEST['messageId'] : null;
                $channelId = isset($_REQUEST['channelId']) ? (int) $_REQUEST['channelId'] : null;
                //var_dump($messageId);
                //die();
                if ($user == null) {
                    $this->forwardRequest(array('Please login'));
                    break;
                }
                if ($this->unStarMessage($messageId, $channelId))
                    break;
                else 
                    return null;
            case self::ACTION_UPDATEMESSAGE :
                $user = \Slacklight\AuthenticationManager::getAuthenticatedUser();
                $messageId = isset($_REQUEST['messageId']) ? (int) $_REQUEST['messageId'] : null;
                $channelId = isset($_REQUEST['channelId']) ? (int) $_REQUEST['channelId'] : null;

                //var_dump($messageId);
                //var_dump($channelId);
                //var_dump($_POST[self::UPDATE_MESSAGE_FIELD]);
                //die();

                if ($user == null) {
                    $this->forwardRequest(array('Please login'));
                        break;
                }
                if ($this->updateMessage($channelId, $messageId, $_POST[self::UPDATE_MESSAGE_FIELD]))
                    break;
                else 
                    return null;
        }
    }


        /**
   * 
   * @param array $errors : optional assign it to 
   * @param string $target : url for redirect of the request
   */
  protected function forwardRequest(array $errors = null, $target = null) {
    //check for given target and try to fall back to previous page if needed
    if ($target == null) {
      if (!isset($_REQUEST[self::PAGE])) {
        throw new Exception('Missing target for forward.');
      }
      $target = $_REQUEST[self::PAGE];
    }
    //forward request to target
    // optional - add errors to redirect and process them in view
    if (count($errors) > 0)
      $target .= '&errors=' . urlencode(serialize($errors));
    header('location: ' . $target);
    exit();
  }

  protected function starMessage(int $messageId = null, int $channelId = null) : bool {
    //var_dump($channelId);
    //var_dump($messageId);
    //var_dump($content);
    //die();

    // delete message

    $user = \Slacklight\AuthenticationManager::getAuthenticatedUser();
    $result = \Data\DataManager::starMessage($messageId);

    if (!$result) {
        $this->forwardRequest(array('could not delete message'));
        return false;
    }    

    Util::redirect('index.php?view=main&channelId=' . $channelId);

    return true;

  }

  protected function unStarMessage(int $messageId = null, int $channelId = null) : bool {
    //var_dump($channelId);
    //var_dump($messageId);
    //echo("unstar");
    //var_dump($content);
    //die();

    // delete message

    $user = \Slacklight\AuthenticationManager::getAuthenticatedUser();
    $result = \Data\DataManager::unStarMessage($messageId);

    if (!$result) {
        $this->forwardRequest(array('could not delete message'));
        return false;
    }    

    Util::redirect('index.php?view=main&channelId=' . $channelId);

    return true;
    
  }

  protected function deleteMessage(int $messageId = null, int $channelId = null) : bool {
    //var_dump($channelId);
    //var_dump($messageId);
    //var_dump($content);
    //die();

    // delete message

    $user = \Slacklight\AuthenticationManager::getAuthenticatedUser();
    $result = \Data\DataManager::deleteMessage($messageId);

    if (!$result) {
        $this->forwardRequest(array('could not delete message'));
        return false;
    }    

    Util::redirect('index.php?view=main&channelId=' . $channelId);

    return true;


  }


  protected function sendMessage(int $channelId = null, string $content =  null) : bool {
    //var_dump($channelId);
    //var_dump($content);
    //die();

    // send message

    $user = \Slacklight\AuthenticationManager::getAuthenticatedUser();
    $result = \Data\DataManager::sendMessage($user->getId(), $channelId, $content);

    if (!$result) {
        $this->forwardRequest(array('could not create message'));
        return false;
    }    

    Util::redirect('index.php?view=main&channelId=' . $channelId);

    return true;


  }

  protected function updateMessage(int $channelId = null, int $messageId = null, string $content = null) : bool {
    //var_dump($channelId);
    //var_dump($content);
    //die();

    // send message

    $user = \Slacklight\AuthenticationManager::getAuthenticatedUser();
    $result = \Data\DataManager::updateMessage($user->getId(), $messageId, $content);

    if (!$result) {
        $this->forwardRequest(array('could not create message'));
        return false;
    }    

    Util::redirect('index.php?view=main&channelId=' . $channelId);

    return true;


  }

}