{literal}

function openModal() {
    document.getElementById("backdrop").style.display = "block"
    document.getElementById("Modal").style.display = "block"
    document.getElementById("Modal").classList.add("show")
}
function closeModal() {
    document.getElementById("backdrop").style.display = "none"
    document.getElementById("Modal").style.display = "none"
    document.getElementById("Modal").classList.remove("show")
}
// Get the modal
var modal = document.getElementById('Modal');

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
  if (event.target == modal) {
    closeModal()
  }
}


function changeSingleValue(table,field,dataid,refresh){
      openModal()
      document.getElementById("UpdateID").value = dataid
      document.getElementById("newval").value = GetFieldData(table,field,dataid)

      document.getElementById("UpdateField").value = field
      document.getElementById("UpdateTable").value = table
      if(refresh){
        document.getElementById("refresh").value = "1"
      }else{
        document.getElementById("refresh").value = "0"
      }

}




function saveModal(){
      var id = document.getElementById("UpdateID").value
      var field = document.getElementById("UpdateField").value
      var table = document.getElementById("UpdateTable").value
      var newval = document.getElementById("newval").value
      var refresh = false;
      if(document.getElementById("refresh").value == "1"){refresh = true}
      SetFieldData(table,field,id,newval)
      closeModal()
      if(refresh){location.reload()}
}




{/literal}
