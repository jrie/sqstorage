![Logo sqStorage](https://dwrox.net/sqstorage.png "Logo sqStorage")


### REST-API

SQStorage provides a REST API to read, write and delete data
To achieve that [php-crud-api](https://github.com/mevdschee/php-crud-api "Maurits van der Schees php-crud-api") was included 

####  API usage
The API is available by calling 
* `api/`
or
* `api.php`

with the following operations    

    ----------------------------------------------------------------------------------------
    GET    /records/{table}      - list      - lists records
    POST   /records/{table}      - create    - creates records
    GET    /records/{table}/{id} - read      - reads a record by primary key
    PUT    /records/{table}/{id} - update    - updates columns of a record by primary key
    DELETE /records/{table}/{id} - delete    - deletes a record by primary key
    PATCH  /records/{table}/{id} - increment - increments columns of a record by primary key

##### Acessible tables
The following tables can be manipulated by the API
* customfields
* fielddata
* headcategories
* images
* items
* storages
* subcategories


##### Examples

###### Reveive Data
To receive the json data from item with id 1
`GET http://sqstorage-uri/api.php/records/items/1`

To receive a list of the available storages
`GET http://sqstorage-uri/api.php/records/storages/`

###### Update Data
To set the name of the storage with the id 1 to "newstorage"
`PUT http://sqstorage-uri/api.php/records/storages/1`
with the body data 
`{"label":"Newstorage"}`



#### Security
If you've configured sqStorage to use the login functionality by setting the variable `$useRegistration` to `true`, the API will also use that. Authentification is done either by
* the `$_SESSION` variable from within sqStorage
or
* by `POST http://sqstorage-uri/api/login` with the body data `{"username":"YouUserName","password":"YourPassWord"}`

###### Guest access
Registered user with assigned to the user group guest can only perform `GET` requests



