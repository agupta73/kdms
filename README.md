To Containerize. Following are the steps:

1. Install docker

2. In the terminal window, go to the kdms folder (the root folder where these files are also stored)

3. run the command: docker-compose up --build

4. when Step 3 is successful, run following:

    go inside the mysql container and set
    echo 'max_allowed_packet = 4096M'
    to import the high volume database

    download the db dump from G-Drive and run the below command to import the database.
    mysql -u <DB_USER> -p <DB_NAME> < shared/<DB_DUMP_FILE_NAME_WITH_EXTENSION>

5. open browser and use url http://localhost:909/kdms/UI/login.php to access KDMS running on the container.
