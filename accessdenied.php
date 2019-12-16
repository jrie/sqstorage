<?php
    header("HTTP/1.1 401 Unauthorized");
?>
<!DOCTYPE html>
<html>
    <?php include_once('head.php'); ?>
    <body>
        <?php include_once('nav.php'); ?>

        <div class="content">
             <?php if ($error): ?>
            <div class="alert alert-danger" role="alert">
                <h6><?php echo $error; ?></h6>
            </div>
            <?php endif; ?>
        </div>

        <?php include_once('footer.php'); ?>
    </body>
</html>