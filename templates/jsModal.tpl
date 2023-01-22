{literal}

function openModal () {
  document.getElementById('backdrop').style.display = 'block'
  document.getElementById('Modal').style.display = 'block'
  document.getElementById('Modal').classList.add('show')
}

function closeModal () {
  document.getElementById('backdrop').style.display = 'none'
  document.getElementById('Modal').style.display = 'none'
  document.getElementById('Modal').classList.remove('show')
}

// Get the modal
const modal = document.getElementById('Modal')

// When the user clicks anywhere outside of the modal, close it
window.addEventListener('click', function (evt) {
  if (evt.target === modal) {
    closeModal()
  }
})

function changeSingleValue (table, field, dataid, refresh) {
  document.getElementById('UpdateID').value = dataid
  document.getElementById('newval').value = GetFieldData(table, field, dataid)
  document.getElementById('UpdateField').value = field
  document.getElementById('UpdateTable').value = table
  
  if (refresh) {
    document.getElementById('refresh').value = '1'
  } else {
    document.getElementById('refresh').value = '0'
  }

  openModal()
}

function saveModal () {
  const id = document.getElementById('UpdateID').value
  const field = document.getElementById('UpdateField').value
  const table = document.getElementById('UpdateTable').value
  const newval = document.getElementById('newval').value
  
  let refresh = false
  if (document.getElementById('refresh').value == '1') {
    refresh = true
  }

  SetFieldData(table, field, id, newval)
  closeModal()
  if (refresh) { location.reload() }
}

{/literal}