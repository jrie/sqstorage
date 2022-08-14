{literal}

decodeBase64 = function(s) {
    var e={},i,b=0,c,x,l=0,a,r='',w=String.fromCharCode,L=s.length;
    var A="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";
    for(i=0;i<64;i++){e[A.charAt(i)]=i;}
    for(x=0;x<L;x++){
        c=e[s.charAt(x)];b=(b<<6)+c;l+=6;
        while(l>=8){((a=(b>>>(l-=8))&0xff)||(x<(L-2)))&&(r+=w(a));}
    }
    return r;
};


function GetItemImage(itemId){
  let ret_value = null
  let API_URL = 'api/records/images?filter=itemId,eq,:itemId&include=thumb&size=1'

  let URL = API_URL.replace(':itemId', itemId)
  const xmlhttp = new XMLHttpRequest()
  xmlhttp.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      const newCharacterJSON = JSON.parse(xmlhttp.responseText)
      ret_value = decodeBase64(newCharacterJSON["records"][0]['thumb']);
    }
  }

  xmlhttp.open('GET', URL, false)
  xmlhttp.setRequestHeader('X-Requested-With', 'XMLHttpRequest')
  xmlhttp.send()
  return ret_value


}



function GetFieldData(table, field, id) {
  let ret_value = null
  let API_URL = 'api/records/:table/:id?include=:field'
  let URL = API_URL.replace(':id', id)
  URL = URL.replace(':table', table)
  URL = URL.replace(':field', field)
  const xmlhttp = new XMLHttpRequest()
  xmlhttp.onreadystatechange = function () {
    if (this.readyState == 4 && this.status == 200) {
      const newCharacterJSON = JSON.parse(xmlhttp.responseText)
      ret_value = newCharacterJSON[field]
    }
  }

  xmlhttp.open('GET', URL, false)
  xmlhttp.setRequestHeader('X-Requested-With', 'XMLHttpRequest')
  xmlhttp.send()
  return ret_value
}

function SetFieldData (table, field, id, data) {
  let ret_value = null
  let API_URL = 'api/records/:table/:id'
  let URL = API_URL.replace(':id', id)
  URL = URL.replace(':table', table)

  let body = '{ ":field" :  ":data" }'
  body = body.replace(':field', field)
  body = body.replace(':data', data)

  const xmlhttp = new XMLHttpRequest()
  xmlhttp.onreadystatechange = function () {
    if (this.readyState == 4 && (this.status == 200 || this.status == 422)) {
      // var newCharacterJSON = JSON.parse(xmlhttp.responseText)
      // ret_value  = newCharacterJSON[field]
      ret_value = GetFieldData(table, field, id)
    }
  }

  xmlhttp.open('PUT', URL, false)
  xmlhttp.setRequestHeader('X-Requested-With', 'XMLHttpRequest')

  xmlhttp.send(body)
  return ret_value
}
{/literal}
