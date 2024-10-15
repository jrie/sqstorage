# Installation of sqStorage with Docker

Follow these steps to install sqStorage on your system with docker.

## Prerequisites

- Docker and Docker Compose must be installed.

## Installation Steps

1. Clone the repository:

   ```bash
   git clone https://github.com/web-rpi/sqstorage.git
   ```
   
2. Change into the directory:

   ```bash
   cd sqstorage
   ```

3. Copy the example database configuration:

   ```bash
   cp ./support/dba-example.php ./dba.php
   ```

4. Open the dba.php file for editing:

   ```bash
   nano ./dba.php
   ```
   Adjust the settings (user, password, database name, host, port, etc.) as necessary.

5. Start the Docker containers:
   ```bash
   docker-compose up -d
   ```

## Accessing sqStorage
Once sqStorage is running, you can access it in your browser at the following address:
```
http://<your-ip-address>:1337
```

After accessing this URL, you will be asked to enter the access data for the database so that sqStorage can establish a connection to the database. It is important that the "Database-Server" is set to the "docker-container-hostname" of the sqStorage-DB-server. Usually, this is "sqstorage-db-1".


## Done!
Your sqStorage installation should now be running.
