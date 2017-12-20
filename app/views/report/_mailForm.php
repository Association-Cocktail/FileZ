<form method="POST" class="send-email-form">

  <p>
    <label for="to"><?php echo __('your email') ?> :</label>
    <input type="text" class="email" name="email" value="<?php echo params ('email') ?>"/>
  </p>
  <p>
    <label for="msg"><?php echo __('your message')?> :</label>
    <textarea cols="80" rows="10" name="msg" value="<?php echo params ('msg') ?>"></textarea>
  </p>
  <p class="submit">
    <input type="submit" class="awesome blue large" value="<?php echo __('Send') ?>" />
  </p>

</form>
