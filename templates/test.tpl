{include file="head.tpl" title=foo}
{include file="nav.tpl" title=foo}

        <div class="content">


        <div class="dropdown float-left">
        <select value="-1" autocomplete="off" class="btn btn-primary dropdown-toggle switchStorage" type="button" tabindex="-1" data-type="storeSrc" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <option selected="selected" value="-1">{t}Quelle{/t}</option>

        {foreach $storages as $storage}
            <option value="{$storage.id}">{$storage.label}</option>', $storage['id'], $storage['label']);
        {/foreach}
        </select>
        </div>

        <div class="dropdown float-left">
            <select value="-1" autocomplete="off" class="btn btn-primary dropdown-toggle switchStorage" type="button" tabindex="-1" data-type="storeDest" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <option selected="selected" value="-1">{t}Ziel{/t}</option>

        {foreach $storages as $storage}
         <option value="{$storage.id}">{$storage.label}</option>
        {/foreach}
        </select>
        </div>
        <div class="clearfix storageListing storeSrc"><h2>{t}Quelle{/t}</h2><div class="data" data-type="src">{t}Quelle wählen.{/t}</div></div>
        <div class="float-left storageListing storeDest"><h2>{t}Ziel{/t}</h2><div class="data" data-type="dest">{t}Ziel wählen.{/t}</div><button id="transferButton" class="btn btn-primary float-right">{t}Transferieren{/t}</button></div>






        </div>

{$target = "transfer.php"}

{include file="footer.tpl"}
{literal}
        <script type="text/javascript">
            let transferItemIds = []

            function transferItem(evt) {
                if (evt.target.dataset['sid'] === document.querySelector('.switchStorage[data-type="storeDest"]').value || parseInt(document.querySelector('.switchStorage[data-type="storeDest"]').value) === -1) return
                if (evt.target.parentNode.dataset['type'] === 'src') {
                    let target = document.querySelector('.storageListing.storeDest .data')
                    let targetId = parseInt(evt.target.dataset['id'])

                    if (transferItemIds.indexOf(targetId) === -1) {
                        transferItemIds.push(targetId)
                        target.appendChild(evt.target.cloneNode(true))
                        target.lastChild.addEventListener('click', transferItem)
                        evt.target.classList.add('moving')
                    } else return
                } else if (evt.target.parentNode.dataset['type'] === 'dest') {
                    let targetId = parseInt(evt.target.dataset['id'])
                    let index = transferItemIds.indexOf(targetId);
                    if (index !== -1) {
                        let srcTarget = document.querySelector('.storageListing.storeSrc .data a[data-id="' + evt.target.dataset['id'] + '"]')
                        if (srcTarget !== null) srcTarget.classList.remove('moving')
                        evt.target.parentNode.removeChild(evt.target)
                        transferItemIds.splice(index, 1)
                    }
                }
            }

            function transferItems(evt) {
                if (transferItemIds.length === 0) return

                let targetId = parseInt(document.querySelector('.switchStorage[data-type="storeDest"]').value)
                if (targetId === -1) return

                let request = new XMLHttpRequest()
                request.addEventListener('readystatechange', function (requestEvt) {
                    if (requestEvt.target.readyState === 4 && requestEvt.target.status === 200) {
                        if (requestEvt.target.responseText.trim() !== 'transferred') return // TODO: Show error message
                        let changeEvent = new Event('change')
                        document.querySelector('.switchStorage[data-type="storeSrc"]').dispatchEvent(changeEvent)

                        changeEvent = new Event('change')
                        document.querySelector('.switchStorage[data-type="storeDest"]').dispatchEvent(changeEvent)
                        transferItemIds.length = 0

                    }
                })


                request.open('GET', '{/literal}{$target}{literal}?transferTarget="' + targetId.toString() + '"&transferIds="' + transferItemIds.toString() + '"')
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
                    if (evt.target.dataset['type'] === 'storeSrc') noItems.appendChild(document.createTextNode('{/literal}{t}Quelle wählen{/t}{literal}'))
                    else if (evt.target.dataset['type'] === 'storeDest') noItems.appendChild(document.createTextNode('{/literal}{t}Ziel wählen{/t}{literal}'))
                    root.appendChild(noItems)
                    return
                }


                let request = new XMLHttpRequest()
                request.addEventListener('readystatechange', function (requestEvt) {
                    if (requestEvt.target.readyState === 4 && requestEvt.target.status === 200) {
                        let items = JSON.parse(requestEvt.target.responseText)
                        let root = document.querySelector('.' + evt.target.dataset['type'] + ' .data')

                        for (let x = 0; x < root.childNodes.length; ++x) {
                            root.removeChild(root.childNodes[x])
                            --x
                        }

                        if (items.length === 0) {
                            let noItems = document.createElement('p');
                            noItems.appendChild(document.createTextNode('{/literal}{t}Keine Gegenstände gefunden{/t}{literal}'))
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
                            itemLink.appendChild(document.createTextNode(item['label']));
                            root.appendChild(itemLink)
                            itemLink.addEventListener('click', transferItem)
                            let targetId = parseInt(item['id'])

                            if (useMove && transferItemIds.indexOf(targetId) !== -1) itemLink.classList.add('moving')
                        }

                    }
                })

                request.open('GET', '{/literal}{$target}{literal}?getId=' + evt.target.value)
                request.send()
            }

            let dropdowns = document.querySelectorAll('select.switchStorage')
            for (let dropdown of dropdowns) dropdown.addEventListener('change', loadChange)
            document.querySelector('#transferButton').addEventListener('click', transferItems)
        </script>    
{/literal}
{include file="bodyend.tpl"}