To Containerize. Following are the steps:
1. Install docker
2. In the terminal window, go to the kdms folder (the root folder where these files are also stored)
3. run the command: docker image build -t agupta73/ckdms:v1.n .   
(please note  v1.n in the above line. "n" is version number and you can change it as you want to with the code modifications. 
Also, you can use your own docker repository name, if you have one, as opposed to agupta73)
4. when 3 is successful, run following command:
docker container run -itd --name mykdms -p 909:80 -p 910:443 agupta73/ckdms:v1.1
(if you used your own repository, please replace agupta73 with your repository name)
5. open browser and use url http://localhost:909/kdms/UI/login.php to access KDMS running on the container.