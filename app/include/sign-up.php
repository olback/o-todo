<?php
    if(count(get_included_files()) == 1){
        http_response_code(404);
        die();
    }
?>

<div class="modal" id="account-modal">
    <div class="inner">
        <i id="close-create-account" class="fa fa-close close-modal" aria-hidden="true"></i>
        <h1>Create new account</h1>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <label for="new-username">Username</label>
            <input type="text" name="new-username" id="new-username" required="required" />
            <label for="new-password">Password (min 8 chars)</label>
            <input type="password" name="new-password" id="new-password" required="required" min="8"/>
            <label for="repeat-password">Repeat password</label>
            <input type="password" name="repeat-password" id="repeat-password" required="required" />
            <?php if(!empty($settings['captcha-private']) && !empty($settings['captcha-public'])) { echo '<div class="recap"><div class="g-recaptcha" data-sitekey="'.$settings['captcha-public'].'"></div></div>'; } ?>
            <input type="submit" name="sign-up" value="Sign up" style="margin:auto;display:block;margin-top:10px;" />
            <p class="message"></p>
        </form>
    </div>
</div>

<script>

    document.getElementById('create-account').onclick = () => {
        document.getElementById('account-modal').style.display = 'block';
    }

    document.getElementById('close-create-account').onclick = () => {
        document.getElementById('account-modal').style.display = 'none';
    }

    if(getParameterByName('modal')) {
        document.getElementById('account-modal').style.display = 'block';
    }

</script>