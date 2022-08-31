{include file="head.tpl" title="{t}Aktualisierungs-Manager{/t}"}
{include file="nav.tpl" target="updater.php" request=$REQUEST}

<div class="content">


{if $install_allowed}
  {if $updatework}


      <div id="updateprogress"></div>
      <script type="text/javascript">
      let inProgress = false;
      let int = self.setInterval("updateProgress()", 200);
      function updateProgress(){
        if(inProgress == false){
            const xhttp = new XMLHttpRequest();
            xhttp.onload = function() {
              inProgress = false;
              document.getElementById("updateprogress").innerHTML = this.responseText;
              }
            xhttp.open("GET", "returnstate.php?state=install", true);
            xhttp.send();
            inProgress = true;

        }
      }



      </script>
  {else}
     <form accept-charset="utf-8" id="installupdate" method="POST" action="">
        <input type="hidden" id="install" name="target" value="installupdate" />
        <ul class="update list-group">
            <li class="alert alert-info">
                <span class="list-span">{t}Aktualisierung der installation{/t}</span>
            </li>
            <li class="list-group-item">
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                      <span><h2>{t}Hier kannst Du Deine Installation von der aktuell gewählten Quelle aktualisieren{/t}</h2></span>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary float-right">{t}Aktualisierung durchführen{/t}</button>
            </li>
        </ul>
    </form>
  {/if}

{else}
        <div class="alert alert-danger" role="alert">
          <h6>{t}Die Installation ist momentan nicht erlaubt - bitte zuerst in den Einstellungen aktivieren{/t}</h6>
        </div>
{/if}






</div>



{$target = "transfer.php"}

{include file="footer.tpl"}
{literal}
<script type="text/javascript">

</script>
{/literal}
{include file="bodyend.tpl"}
