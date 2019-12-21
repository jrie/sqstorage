<?php require('login.php'); ?>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['getId']) && !empty($_GET['getId'])) {
        //require_once('./support/meekrodb.2.3.class.php');
        require_once('./vendor/autoload.php');
        require_once('./support/dba.php');

        $storageId = intVal($_GET['getId']);
        $items = DB::query('SELECT id, label, amount FROM items WHERE storageid=%d', $storageId);
        echo json_encode($items);
        die();
    } else if (isset($_GET['transferTarget']) && !empty($_GET['transferTarget']) && isset($_GET['transferIds']) && !empty($_GET['transferIds'])) {
        //require_once('./support/meekrodb.2.3.class.php');
        require_once('./vendor/autoload.php');
        require_once('./support/dba.php');

        $targetStorageId = intVal(trim($_GET['transferTarget'], '"'));
        $transferIds = explode(',', trim($_GET['transferIds'], '"'));

        foreach ($transferIds as $itemId) {
            $item = DB::queryFirstRow('SELECT storageid, amount FROM items WHERE id=%d', $itemId);
            if ($item === NULL) continue;

            $srcStorage = DB::queryFirstRow('SELECT id, amount FROM storages WHERE id=%d', $item['storageid']);
            $destStorage = DB::queryFirstRow('SELECT id, amount FROM storages WHERE id=%d', $targetStorageId);

            DB::update('storages', array('amount' => intVal($srcStorage['amount']) - intVal($item['amount'])), 'id=%d', $srcStorage['id']);
            DB::update('storages', array('amount' => intVal($destStorage['amount']) + intVal($item['amount'])), 'id=%d', $destStorage['id']);
            DB::update('items', array('storageid' => $targetStorageId), 'id=%d', $itemId);
        }
        echo 'transferred';
        die();
    }
}


?>
<!DOCTYPE html>
<html>
<?php include_once('head.php'); ?>

<body>
    <?php include_once('nav.php'); ?>

    <div class="content">
        <?php

        $storages = DB::query('SELECT id,label FROM storages');

        printf('<div class="dropdown float-left"><select value="-1" autocomplete="off" class="btn btn-primary dropdown-toggle switchStorage" type="button" tabindex="-1" data-type="storeSrc" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">');
        echo '<option selected="selected" value="-1">' . gettext('Quelle') . '</option>';

        foreach ($storages as $storage) printf('<option value="%s">%s</option>', $storage['id'], $storage['label']);
        echo '</select></div>';

        printf('<div class="dropdown float-left"><select value="-1" autocomplete="off" class="btn btn-primary dropdown-toggle switchStorage" type="button" tabindex="-1" data-type="storeDest" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">');
        echo ('<option selected="selected" value="-1">' . gettext('Ziel') . '</option>');

        foreach ($storages as $storage) printf('<option value="%s">%s</option>', $storage['id'], $storage['label']);
        echo '</select></div>';

        ?>
        <div class="clearfix storageListing storeSrc">
            <h2><?php echo gettext('Quelle') ?></h2>
            <div class="data" data-type="src"><?php echo gettext('Quelle wählen.') ?></div>
        </div>
        <div class="float-left storageListing storeDest">
            <h2><?php echo gettext('Ziel') ?></h2>
            <div class="data" data-type="dest"><?php echo gettext('Ziel wählen.') ?></div><button id="transferButton" class="btn btn-primary float-right"><?php echo gettext('Transferieren') ?></button>
        </div>

        <?php include_once('footer.php'); ?>

        <script type="text/javascript">
            let transferItemIds = []

            function transferItem(evt) {
                if (evt.target.dataset['sid'] === document.querySelector('.switchStorage[data-type="storeDest"]').value || parseInt(document.querySelector('.switchStorage[data-type="storeDest"]').value) === -1) return
                if (evt.target.parentNode.dataset['type'] === 'src') {
                    let target = document.querySelector('.storageListing.storeDest .data')
                    let targetId = parseInt(evt.target.dataset['id'])
                    let index = transferItemIds.indexOf(targetId)

                    if (index == -1) {
                        transferItemIds.push(targetId)
                        evt.target.classList.add('moving')
                        target.appendChild(evt.target.cloneNode(true))
                        target.lastChild.addEventListener('click', transferItem)
                        evt.target.addEventListener('click', transferItem)
                    } else {
                        let srcTarget = document.querySelector('.storageListing.storeSrc .data a[data-id="' + evt.target.dataset['id'] + '"]')
                        if (srcTarget !== null) srcTarget.classList.remove('moving')
                        target = document.querySelector('.storageListing.storeDest .data a[data-id="' + evt.target.dataset['id'] + '"]')
                        target.parentNode.removeChild(target)
                        transferItemIds.splice(index, 1)
                    }
                } else if (evt.target.parentNode.dataset['type'] === 'dest') {
                    let targetId = parseInt(evt.target.dataset['id'])
                    let index = transferItemIds.indexOf(targetId);
                    if (index !== -1) {
                        evt.target.parentNode.removeChild(evt.target)
                        let srcTarget = document.querySelector('.storageListing.storeSrc .data a[data-id="' + evt.target.dataset['id'] + '"]')
                        if (srcTarget !== null) srcTarget.classList.remove('moving')
                        transferItemIds.splice(index, 1)
                    }
                }
            }

            function transferItems(evt) {
                if (transferItemIds.length === 0) return

                let targetId = parseInt(document.querySelector('.switchStorage[data-type="storeDest"]').value)
                if (targetId === -1) return

                let request = new XMLHttpRequest()
                request.addEventListener('readystatechange', function(requestEvt) {
                    if (requestEvt.target.readyState === 4 && requestEvt.target.status === 200) {
                        if (requestEvt.target.responseText.trim() !== 'transferred') return // TODO: Show error message
                        let changeEvent = new Event('change')
                        document.querySelector('.switchStorage[data-type="storeSrc"]').dispatchEvent(changeEvent)

                        changeEvent = new Event('change')
                        document.querySelector('.switchStorage[data-type="storeDest"]').dispatchEvent(changeEvent)
                        transferItemIds.length = 0

                    }
                })


                request.open('GET', 'transfer.php?transferTarget="' + targetId.toString() + '"&transferIds="' + transferItemIds.toString() + '"')
                request.send()

            }

            function loadChange(evt) {
                if (evt.target.dataset['type'] === 'storeDest') {
                    transferItemIds.length = 0
                    for (let childNode of document.querySelectorAll('.storeSrc .data a')) childNode.classList.remove('moving')
                }

                let root = document.querySelector('.' + evt.target.dataset['type'] + ' .data')
                for (let x = 0; x < root.childNodes.length; ++x) {
                    root.removeChild(root.childNodes[x])
                        --x
                }

                if (parseInt(evt.target.value) === -1) {
                    let noItems = document.createElement('p');
                    if (evt.target.dataset['type'] === 'storeSrc') noItems.appendChild(document.createTextNode('<?php echo gettext('Quelle wählen.') ?>'))
                    else if (evt.target.dataset['type'] === 'storeDest') noItems.appendChild(document.createTextNode('<?php echo gettext('Ziel wählen.') ?>'))
                    root.appendChild(noItems)
                    return
                }


                let request = new XMLHttpRequest()
                request.addEventListener('readystatechange', function(requestEvt) {
                    if (requestEvt.target.readyState === 4 && requestEvt.target.status === 200) {
                        let items = JSON.parse(requestEvt.target.responseText)
                        let root = document.querySelector('.' + evt.target.dataset['type'] + ' .data')

                        for (let x = 0; x < root.childNodes.length; ++x) {
                            root.removeChild(root.childNodes[x])
                                --x
                        }

                        if (items.length === 0) {
                            let noItems = document.createElement('p');
                            noItems.appendChild(document.createTextNode('<?php echo gettext('Keine Gegenstände gefunden.') ?>'))
                            root.appendChild(noItems)
                            return
                        }

                        let useMove = false
                        if (evt.target.dataset['type'] === 'storeSrc') useMove = true;

                        for (let item of items) {
                            let itemLink = document.createElement('a');
                            itemLink.href = '#'
                            itemLink.dataset['id'] = item['id']
                            itemLink.dataset['sid'] = evt.target.value
                            itemLink.appendChild(document.createTextNode(item['amount'] + ' x ' + item['label']));
                            root.appendChild(itemLink)
                            itemLink.addEventListener('click', transferItem)
                            let targetId = parseInt(item['id'])

                            if (useMove && transferItemIds.indexOf(targetId) !== -1) itemLink.classList.add('moving')
                        }

                    }
                })

                request.open('GET', 'transfer.php?getId=' + evt.target.value)
                request.send()
            }

            let dropdowns = document.querySelectorAll('select.switchStorage')
            for (let dropdown of dropdowns) dropdown.addEventListener('change', loadChange)
            document.querySelector('#transferButton').addEventListener('click', transferItems)
        </script>
</body>

</html>