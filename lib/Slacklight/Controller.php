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
    const USER_NAME = 'userName';
    const USER_PASSWORD = 'password';
    const ACTION_ORDER = 'placeOrder';
    const CC_NAME = 'nameOnCard';
    const CC_NUMBER = 'cardNumber';


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



  protected function processCheckout(string $nameOnCard = null, string $cardNumber =  null) : bool {

    $errors = array();
    $nameOnCard = trim($nameOnCard);

    if ($nameOnCard == null || strlen($nameOnCard) == 0) {
        $errors[] = 'invalid name on card';
    }


    if ($cardNumber == null || strlen($cardNumber) != 16 || !ctype_digit($cardNumber)) {
        $errors[] = 'invalid credit card number';
    }


    if (count($errors) > 0) {
        $this->forwardRequest($errors);
        return false;
    }

    if (\Slacklight\ShoppingCart::size() == 0) {
        $this->forwardRequest(array('no items in cart'));
        return false;
    }

    // place order
    $user = \Slacklight\AuthenticationManager::getAuthenticatedUser();
    $orderId = \Data\DataManager::createOrder($user->getId(), 
            \Slacklight\ShoppingCart::getAll(), $nameOnCard, $cardNumber);

    if (!$orderId) {
        $this->forwardRequest(array('could not create order'));
        return false;
    }    

    \Slacklight\ShoppingCart::clear();
    Util::redirect('index.php?view=success&orderId=' . rawurlencode($orderId));

    return true;


  }

}