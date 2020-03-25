# DataDeer.net's Source!
This contains **all** of the website datadeer.net, except for:
- media (images,shared content)
- easter eggs (such as /secret)
- my passwords (hopefully)

# *All* of the code? Like, the entire website?
Yes, everything you can do on DataDeer.net is now in code form! Why? Because I like open source. This is the biggest website I know of that is 100% open source. Beat that, Google. (Well, Wikipedia is like 80% open source, so I'll put them on this cool website list)

# OMG I want to put something on DataDeer!!!!
Yes! Please do! Read this whole paragraph first! Just fork this Github, add the code, and request a merge with master! After requesting a merge, you could email admin@d______r.net and I will either accept your merge or tell you why I denied it. Look at other files, such as html/dog/dog.php, to see how to format your code. Notice how I put the footer in.

# What can I do with it?
If you follow the setup instructions, you can set up a similar website that could possibly be nearly as cool!

# Setup your own website
So, you want your own website based off of DataDeer.net? Here are the steps:
## Get a Linux server (Preferrably Ubuntu Server due to simplicity)
You don't need fancy server hardware, if you have any computer lying around, even with the lowest of specs, you can have a fast server. Then follow [https://ubuntu.com/tutorials/tutorial-install-ubuntu-server#1-overview](This Official Guide) to set up a server. Make sure you set it up to be headless for convinience.

## Install website software
After you got the server running, run these commands:
```bash
# Basic website software
sudo apt-get install apache2 php7.2 php7.2-curl php7.2-mysql php7.2-xml php7.2-soap php7.2-xmlrpc php7.2-zip php7.2-intl php7.2-gd
# HTTPS software
sudo apt-get install certbot python-certbot-apache
# For things like user accounts and other features to work
sudo add-apt-repository universe
sudo add-apt-repository ppa:certbot/certbot
echo "deb https://apache.bintray.com/couchdb-deb bionic main" | sudo tee -a /etc/apt/sources.list
curl -L https://couchdb.apache.org/repo/bintray-pubkey.asc | sudo apt-key add -
sudo apt-get install mysql-server couchdb openjdk-8-jre-headless fortune cowsay imagemagick
```
## Clone or download the necessary parts
```bash
# Go in website directory
cd /var/www/
# Download the code
git clone https://github.com/zaners123/datadeer
```
## Make it unique

You better not just copy all the code and end it there! Add fun stuff! To summarize the liscence, if you do edit the code, your project has to be open source, too.
