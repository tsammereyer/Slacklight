<?php
use Bookshop\ShoppingCart;
use Bookshop\Util;
use Bookshop\AuthenticationManager;
$cartSize = ShoppingCart::size();

$nameOnCard = isset($_REQUEST['nameOnCard']) ? $_REQUEST['nameOnCard'] : null;
$cardNumber = $_REQUEST['cardNumber'] ?? null;

require_once('views/partials/header.php'); ?>


<div class="page-header">
    <h2>Checkout</h2>
</div>


<p>You have <span class="badge"><?php echo Util::escape($cartSize); ?></span> items in your cart.</p>

<?php if ($cartSize > 0): ?>

  <?php if (AuthenticationManager::isAuthenticated()): ?>


        <div class="panel panel-default">
            <div class="panel-heading">
        Please provide your credit card details for payment:
        </div>
       <div class="panel-body">



    <form class="form-horizontal" method="post" action="<?php echo Util::action(Bookshop\Controller::ACTION_ORDER); ?>">
        <div class="form-group">
            <label for="nameOnCard" class="col-sm-4 control-label">Name on card:</label>
            <div class="col-sm-8">
                <input type="text" required class="form-control" id="nameOnCard" name="nameOnCard" placeholder="Your name please!" value="<?php echo htmlentities($nameOnCard); ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="cardNumber" class="col-sm-4 control-label">Card Number:</label>
            <div class="col-sm-8">
                <input type="text" class="form-control" id="cardNumber" name="cardNumber" placeholder="try '1234567891234567'" value="<?php echo htmlentities($cardNumber); ?>">
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-4 col-sm-8">
                <button type="submit" class="btn btn-default">Place Order</button>
            </div>
        </div>
    </form>
    </div>
        </div>


  <?php  else: ?>
    <p class="errors alert alert-info">Please log in to place your order.</p>
    <?php endif  ?>

<?php else: ?>
    <p class="errors alert alert-info">Please add some items to your cart first.</p>
<?php endif; ?>


<?php require_once('views/partials/footer.php'); ?>