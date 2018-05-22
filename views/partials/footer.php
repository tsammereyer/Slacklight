<?php

use Slacklight\Util;

if (isset($errors) && is_array($errors)): ?>
    <div class="errors alert alert-danger">
      <ul>
        <?php foreach ($errors as $errMsg): ?>
          <li><?php echo(Util::escape($errMsg)); ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
<?php endif;  ?>

<!--/display error messages-->

</div><!--  container -->

    <script src="assets/jquery/jquery-3.3.1.js"></script>
    <script src="assets/bootstrap/js/bootstrap.js"></script>

  </body>
</html>