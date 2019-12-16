<nav class="navbar navbar-light bg-light">
    <a href="index.php"><img class="logo" src="./img/sqstorage.png" /></a>
    <ul class="nav">
        <li class="nav-item"><a href="index.php" class="nav-link"><?php echo gettext('Eintragen') ?></a></li>
        <li class="nav-item"><a href="inventory.php" class="nav-link"><?php echo gettext('Inventar') ?></a></li>
        <li class="nav-item"><a href="categories.php" class="nav-link"><?php echo gettext('Kategorien') ?></a></li>
        <li class="nav-item"><a href="transfer.php" class="nav-link"><?php echo gettext('Transferieren') ?></a></li>
        <li class="nav-item"><a href="datafields.php" class="nav-link"><?php echo gettext('Datenfelder') ?></a></li>
    </ul>

    <form class="form-inline searchArea" method="GET" action="inventory.php">
        <input class="form-control mr-sm-2" name="searchValue" type="search" placeholder="<?php echo gettext('Suche') ?>" aria-label="<?php echo gettext('Suche') ?>">
        <button class="btn btn-outline-success my-2 my-sm-0" type="submit"><?php echo gettext('Suchen') ?></button>
    </form>
</nav>