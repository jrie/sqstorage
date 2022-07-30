{include file="head.tpl" title="{t}Transferieren{/t}"}
{include file="nav.tpl" target="transfer.php" request=$REQUEST}

    <div class="content">

        <div class="dropdown float-left storeSrcDiv">
            <select value="-1" autocomplete="off" class="btn dropdown-toggle switchStorage" id="storeSrc" type="button" tabindex="-1" data-type="storeSrc" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <option selected="selected" value="-1">{t}Quelle{/t}</option>';
            {foreach $storages as $storage}
                <option value="{$storage.id}">{$storage.label}</option>
            {/foreach}
            </select>
        </div>

        <div class="dropdown float-left storeDestDiv">
            <select value="-1" autocomplete="off" class="btn dropdown-toggle switchStorage" id="storeDest" type="button" tabindex="-1" data-type="storeDest" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <option selected="selected" value="-1">{t}Ziel{/t}</option>

            {foreach $storages as $storage}
                <option value="{$storage.id}">{$storage.label}</option>
            {/foreach}
            </select>
        </div>

        <div class="clearfix storageListing storeSrc">
            <h2>{t}Quelle{/t}</h2>
            <div class="data" data-type="src">{t}Quelle wählen.{/t}</div>
        </div>
        <div class="float-left storageListing storeDest">
            <h2>{t}Ziel{/t}</h2>
            <div class="data" data-type="dest">{t}Ziel wählen.{/t}</div><button id="transferButton" class="btn btn-primary float-right">{t}Transferieren{/t}</button>
        </div>
        
        <div class="clearfix"></div>
        </div>

{$target="transfer.php"}

{include file="footer.tpl"}
{literal}
        <script type="text/javascript">
            let transferItemIds = []
            let transferItemAmounts = []

            function NumSelect(maxAmount) {
                while (true) {
                    let transferAmount = parseInt(prompt("{/literal}{t}Von diesem Artikel sind mehrere Stück am Lagerplatz. Wie viele sollen zum neuen Lagerort transferiert werden?{/t}{literal}", maxAmount))
                    if (isNaN(transferAmount)) return -1
                    if (transferAmount > maxAmount) alert("{/literal}{t}Von diesem Artikel sind nicht genug Einheiten vorhanden.{/t}{literal}")
                    else if (transferAmount < 0) alert("{/literal}{t}Anzahl kann nicht negativ sein.{/t}{literal}")
                    else return transferAmount
                }
            }

            function transferItem(evt) {
                evt.preventDefault()
                if (evt.target.dataset['sid'] === document.querySelector('.switchStorage[data-type="storeDest"]').value || parseInt(document.querySelector('.switchStorage[data-type="storeDest"]').value) === -1) return
                if (evt.target.parentNode.dataset['type'] === 'src') {
                    let target = document.querySelector('.storageListing.storeDest .data')
                    let targetId = parseInt(evt.target.dataset['id'])
                    let index = transferItemIds.indexOf(targetId)
                    let amount = parseInt(evt.target.dataset['amount'])

                    if (index === -1) {
                        let transferAmount = amount
                        if (amount > 1) transferAmount = NumSelect(amount)
                        if (transferAmount <= 0) return

                        if (transferAmount === amount) transferTarget = target.appendChild(evt.target.cloneNode(true))
                        else {
                            transferTarget = target.appendChild(evt.target.cloneNode(true))
                            transferTarget.dataset['amount'] = transferAmount
                            transferTarget.textContent = transferTarget.textContent.replace(amount, transferAmount, 1)
                            evt.target.textContent = evt.target.textContent.replace(amount, amount - transferAmount, 1)
                        }

                        transferItemIds.push(targetId)
                        transferItemAmounts.push(transferAmount)

                        evt.target.classList.add('moving')
                        transferTarget.classList.add('moving')
                        target.lastChild.addEventListener('click', transferItem)
                        evt.target.addEventListener('click', transferItem)
                    } else {
                        let srcTarget = document.querySelector('.storageListing.storeSrc .data a[data-id="' + evt.target.dataset['id'] + '"]')
                        if (srcTarget !== null) srcTarget.classList.remove('moving')

                        target = document.querySelector('.storageListing.storeDest .data a[data-id="' + evt.target.dataset['id'] + '"]')
                        target.parentNode.removeChild(target)

                        let amount = parseInt(srcTarget.textContent.match(/[0-9,\.]*/)[0])
                        if (amount < parseInt(srcTarget.dataset['amount'])) srcTarget.textContent = srcTarget.textContent.replace(amount, amount + transferItemAmounts[index], 1)

                        transferItemIds.splice(index, 1)
                        transferItemAmounts.splice(index, 1)
                    }
                } else if (evt.target.parentNode.dataset['type'] === 'dest') {
                    let targetId = parseInt(evt.target.dataset['id'])
                    let index = transferItemIds.indexOf(targetId);
                    if (index !== -1) {
                        evt.target.parentNode.removeChild(evt.target)

                        let srcTarget = document.querySelector('.storageListing.storeSrc .data a[data-id="' + evt.target.dataset['id'] + '"]')
                        if (srcTarget !== null) {
                            srcTarget.classList.remove('moving')
                            let amount = parseInt(srcTarget.textContent.match(/[0-9]*/)[0])
                            if (amount < parseInt(srcTarget.dataset['amount'])) srcTarget.textContent = srcTarget.textContent.replace(amount, amount + transferItemAmounts[index], 1)
                        }

                        transferItemIds.splice(index, 1)
                        transferItemAmounts.splice(index, 1)
                    }
                }
            }

            function transferItems(evt) {
                evt.preventDefault()
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
                        transferItemAmounts.length = 0

                    }
                })


                request.open('GET', 'transfer.php?transferTarget="' + targetId.toString() + '"&transferIds="' + transferItemIds.toString() + '"' + '"&transferAmounts="' + transferItemAmounts.toString() + '"')
                request.send()

            }

            function loadChange(evt) {
                if (evt.target.dataset['type'] === 'storeDest') {
                    transferItemIds.length = 0
                    transferItemAmounts.length = 0
                    for (let childNode of document.querySelectorAll('.storeSrc .data a')) childNode.classList.remove('moving')
                }

                let root = document.querySelector('.' + evt.target.dataset['type'] + ' .data')
                for (let x = 0; x < root.childNodes.length; ++x) {
                    root.removeChild(root.childNodes[x])
                        --x
                }

                if (parseInt(evt.target.value) === -1) {
                    let noItems = document.createElement('p');
                    if (evt.target.dataset['type'] === 'storeSrc') noItems.appendChild(document.createTextNode('{/literal}{t}Quelle wählen{/t}{literal}'))
                    else if (evt.target.dataset['type'] === 'storeDest') noItems.appendChild(document.createTextNode('{/literal}{t}Ziel wählen{/t}{literal}'))
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
                            noItems.appendChild(document.createTextNode('{/literal}{t}Keine Gegenstände gefunden.{/t}{literal}'))
                            root.appendChild(noItems)
                            return
                        }

                        let useMove = false
                        if (evt.target.dataset['type'] === 'storeSrc') useMove = true;

                        for (let item of items) {
                            let itemLink = document.createElement('a');
                            itemLink.href = '#'
                            itemLink.dataset['amount'] = item['amount']
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
{/literal}
{include file="bodyend.tpl"}
