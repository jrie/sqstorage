<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <img class="logo" src="./img/sqstorage.png"></img>
    <ul class="nav">
        <li class="nav-item"><a href="index.php" class="nav-link">Eintragen</li></a></li>
        <li class="nav-item"><a href="inventory.php" class="nav-link">Inventar</li></a></li>
        <li class="nav-item"><a href="categories.php" class="nav-link">Kategorien</a></li>
    </ul>

    <form class="form-inline my-2 " method="GET" action="inventory.php">
        <input class="form-control mr-sm-2" name="searchValue" type="search" placeholder="Suche" aria-label="Suche">
        <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Suchen</button>
    </form>
</nav>