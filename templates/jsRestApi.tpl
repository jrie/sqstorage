{literal}



function GetFieldData(table,field,id){
    var ret_value = null;
    var API_URL = 'api/records/:table/:id?include=:field'
    var URL = API_URL.replace(':id', id)
        URL = URL.replace(':table', table)
        URL = URL.replace(':field', field)
    var xmlhttp=new XMLHttpRequest();
    xmlhttp.onreadystatechange=function() {
        if (this.readyState==4 && this.status==200) {
            var newCharacterJSON = JSON.parse(xmlhttp.responseText)
            ret_value  = newCharacterJSON[field]
        }
    }

    xmlhttp.open("GET",URL,false);
    xmlhttp.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xmlhttp.send();
    return ret_value;
}

function SetFieldData(table,field,id,data){
    var ret_value = null;
    var API_URL = 'api/records/:table/:id'
    var URL = API_URL.replace(':id', id)
        URL = URL.replace(':table', table)

    var body = '{ ":field" :  ":data" }'
        body = body.replace(':field',field)
        body = body.replace(':data',data)

    var xmlhttp=new XMLHttpRequest();
    xmlhttp.onreadystatechange=function() {
        if (this.readyState==4 && (this.status==200 || this.status==422   )) {
           // var newCharacterJSON = JSON.parse(xmlhttp.responseText)
           // ret_value  = newCharacterJSON[field]
           ret_value = GetFieldData(table,field,id)
        }
    }

    xmlhttp.open("PUT",URL,false);
    xmlhttp.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

    xmlhttp.send(body);
    return ret_value;
}


{/literal}
