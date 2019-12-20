<nav class="navbar navbar-light bg-light">
    <a href="index.php"><img class="logo" src="./img/sqstorage.png" /></a>
    <ul class="nav">
        <li class="nav-item"><a href="index.php" class="nav-link"><?php echo gettext('Eintragen') ?></a></li>
        <li class="nav-item"><a href="inventory.php" class="nav-link"><?php echo gettext('Inventar') ?></a></li>
        <li class="nav-item"><a href="categories.php" class="nav-link"><?php echo gettext('Kategorien') ?></a></li>
        <li class="nav-item"><a href="transfer.php" class="nav-link"><?php echo gettext('Transferieren') ?></a></li>
        <?php if ($_SESSION['user']['usergroupid'] == 1) { ?><li class="nav-item"><a href="settings.php" class="nav-link"><?php echo gettext('Einstellungen') ?></a></li><?php } ?>

        <li class="nav-item"><a href="datafields.php" class="nav-link"><?php echo gettext('Datenfelder') ?></a></li>
    </ul>

    <form class="form-inline searchArea" method="GET" action="inventory.php">
        <input class="form-control mr-sm-2" name="searchValue" type="search" placeholder="<?php echo gettext('Suche') ?>" aria-label="<?php echo gettext('Suche') ?>">
        <button class="btn btn-outline-success my-2 my-sm-0" type="submit"><?php echo gettext('Suchen') ?></button>
    </form>

    <ul class="nav">
        <li class="nav-item"><a href="index.php?logout" class="nav-link"><i class="fas fa-sign-out-alt" alt="<?php echo gettext('Abmelden') ?>" title="<?php echo gettext('Abmelden') ?>"></i></a></li>
    </ul>
</nav>