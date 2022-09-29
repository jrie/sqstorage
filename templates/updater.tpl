{include file="head.tpl" title="{t}Aktualisierungs-Manager{/t}"}
{include file="nav.tpl" target="updater.php" request=$REQUEST}

<div class="content">



{if $install_allowed}
    <div id="updateprogress"></div>

        <ul class="update list-group" id="installupdate">
            <li class="alert alert-info">
                <span class="list-span">{t}Aktualisierung der Installation{/t}</span>
            </li>
            <li class="list-group-item">
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                      <span><h2>{t}Hier kannst Du Deine Installation von der aktuell gewählten Quelle aktualisieren{/t}</h2></span>
                    </div>
                </div>
                <button class="btn btn-primary float-right" id="updaterbutton" onclick="RunTheUpdate();return false;">{t}Aktualisierung durchführen{/t}</button>
            </li>
        </ul>

    <hr />
    <form accept-charset="utf-8" id="dbupdate" method="POST" action="install.php">
        <ul class="dbupdate list-group">
            <li class="alert alert-info">
                <span class="list-span">{t}Aktualisierung der Datenbank{/t}</span>
            </li>
            <li class="list-group-item">
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                      <span><h2>{t}Datenbank-Aktualisierung{/t}</h2></span>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary float-right" >{t}Zur Datenbank-Aktualisierung{/t}</button>
            </li>
        </ul>
    </form>


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
      let DoCheck = false;
      let inProgress = false;

      function updateProgress(){
        if(DoCheck){
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
      }

      function RunTheUpdate(){
            let int = self.setInterval("updateProgress()", 200);
/////'updater.php target=installupdate'
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
              if (this.readyState == 4 && this.status == 200) {
                setTimeout(function(){
                  DoCheck = false;
                  window.stop();
                }, 1000);



              }
            };
            xhttp.open("GET", "updater.php?target=installupdate", true);
            document.getElementById('installupdate').style.display = 'none';
            DoCheck = true;
            xhttp.send();

      }

      </script>
{/literal}
{include file="bodyend.tpl"}
