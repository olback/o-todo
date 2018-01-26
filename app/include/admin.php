<?php
    if(count(get_included_files()) == 1){
        http_response_code(404);
        die();
    }
?>

<!-- Admin page -->
<div class="modal" id="admin">
    <div class="inner">
        <i class="fa fa-close close-modal" aria-hidden="true"></i>
        <h1>Admin</h1><br>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <label for="rprivk">Recaptcha private key</label>
            <input type="text" id="rprivk" />
            <label for="rpubk">Recaptcha public key</label>
            <input type="text" id="rpubk" />
        </form>
    </div>
</div>

<script>
    document.getElementById('admin-button').onclick = () => {
        document.getElementById('admin').style.display = 'block';
    }
</script>
