{literal}

function GetItemThumb(itemId, filterType) {
  let ret_value = null;
  let API_URL = 'api.php/records/images?filter=' + filterType + ',eq,:itemId&include=thumb&size=1';

  let URL = API_URL.replace(':itemId', itemId);
  const xmlhttp = new XMLHttpRequest();
  xmlhttp.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      const newCharacterJSON = JSON.parse(xmlhttp.responseText);
      ret_value = atob(newCharacterJSON["records"][0]['thumb']);
    }
  };

  xmlhttp.open('GET', URL, false);
  xmlhttp.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
  xmlhttp.send();
  return ret_value;
}

function GetItemFullimage(itemId, filterType) {
  let ret_value = null;
  let API_URL = 'api.php/records/images?filter=' + filterType + ',eq,:itemId&include=imageData&size=1';

  let URL = API_URL.replace(':itemId', itemId);
  const xmlhttp = new XMLHttpRequest();
  xmlhttp.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      const newCharacterJSON = JSON.parse(xmlhttp.responseText);
      ret_value = atob(newCharacterJSON["records"][0]['imageData']);
    }
  };

  xmlhttp.open('GET', URL, false);
  xmlhttp.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
  xmlhttp.send();
  return ret_value;
}

function GetItemFullimages(itemId, filterType, coverId) {
  let ret_value = [];
  let API_URL = 'api.php/records/images?filter=itemId,eq,:itemId&include=imageData,id';

  let URL = API_URL.replace(':itemId', itemId);
  const xmlhttp = new XMLHttpRequest();
  xmlhttp.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      const newCharacterJSON = JSON.parse(xmlhttp.responseText);
      let ArrLength = Object.keys(newCharacterJSON["records"]).length;

      for (let x = 0; x < ArrLength; x++) {
        let imageData = 'data:image/*;charset=utf-8;base64,' + atob(newCharacterJSON["records"][x]['imageData'])
        if (newCharacterJSON["records"][x]['id'] != coverId) {
          ret_value.push(imageData);
        } else {
          ret_value.splice(0, 0, imageData);
        }
        //ret_value.push('data:image/*;charset=utf-8;base64,' + atob(newCharacterJSON["records"][x]['imageData']));
      }
    }
  };

  xmlhttp.open('GET', URL, false);
  xmlhttp.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
  xmlhttp.send();
  return ret_value;
}


function GetFieldData(table, field, id) {
  let ret_value = null;
  let API_URL = 'api.php/records/:table/:id?include=:field';
  let URL = API_URL.replace(':id', id);
  URL = URL.replace(':table', table);
  URL = URL.replace(':field', field);
  const xmlhttp = new XMLHttpRequest();
  xmlhttp.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      const newCharacterJSON = JSON.parse(xmlhttp.responseText);
      ret_value = newCharacterJSON[field];
    }
  };

  xmlhttp.open('GET', URL, false);
  xmlhttp.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
  xmlhttp.send();
  return ret_value;
}

function SetFieldData(table, field, id, data) {
  let ret_value = null;
  let API_URL = 'api.php/records/:table/:id';
  let URL = API_URL.replace(':id', id);
  URL = URL.replace(':table', table);

  let body = '{ ":field" :  ":data" }';
  body = body.replace(':field', field);
  body = body.replace(':data', data);

  const xmlhttp = new XMLHttpRequest();
  xmlhttp.onreadystatechange = function () {
    if (this.readyState == 4 && (this.status == 200 || this.status == 422)) {
      // var newCharacterJSON = JSON.parse(xmlhttp.responseText)
      // ret_value  = newCharacterJSON[field]
      ret_value = GetFieldData(table, field, id);
    }
  };

  xmlhttp.open('PUT', URL, false);
  xmlhttp.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

  xmlhttp.send(body);
  return ret_value;
}

{/literal}