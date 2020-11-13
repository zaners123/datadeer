# DataDeer.net's Source!
This contains **all** of the website [DataDeer.net](https://datadeer.net), such as: 
 - File Sharing
 - A Search Engine
 - Two-player Checkers
 - Private Messaging
 - A currency (DeerCoin)
Except for:
- media (images,shared content)
- easter eggs (such as /secret)
- my passwords (hopefully)

# *All* of the code? Like, the entire website?
Yes, everything you can do on DataDeer.net is now in code form! Why? Because I like open source. This is the biggest website I know of that is 100% open source. Beat that, Google. (Well, Wikipedia is like 80% open source, so I'll put them on this cool website list)

# I want to put something on DataDeer!!!!
Yes! Please do! Read this whole paragraph first!
Post a Github Issue with the goal of your new code, and make a corresponding branch.
When you're done, write a pull request.

Likely reasons for denial may include:
 - Poorly formatted code (Look at other files, such as html/deercoin/index.php, to see how to format your code)
 - Adds nothing of value
 - Has security vulnerabilities

Examples of code I would accept would be anything that adds value to the codebase, such as:
 - A Cool New Feature (for ideas, check out [TODO.md]())
 - Fixing/Cleaning/Patching existing code

# Setup your own website

Do you have a couple hours, $12, and an old computer lying around? Make a website based off of DataDeer.net!

## Find something to run it on

You don't need expensive server hardware.
If you have any computer lying around, even with the lowest of specs, you can run a fast web server.

Really just about any computer could work. It could be a spare Desktop PC. A laptop. This thing has about the same hardware requirements as Windows Vista, which came out in 2006.

## Get a Linux server; anything Debian-based will work (such as Debian, Ubuntu, etc) 

Then follow a guide on how to set up a [Debian](https://www.debian.org/distrib/) or [Ubuntu](https://ubuntu.com/tutorials/tutorial-install-ubuntu-server#1-overview) Serve

Make sure you set the server up as headless (in Debian don't select any Desktop Managers), give it a static IP, and set up SSH keys (for remote access).

## Install website software
After you got the server running, run these commands:
```bash
# Basic website software
sudo apt-get install apache2 php7.2 php7.2-curl php7.2-mysql php7.2-xml php7.2-soap php7.2-xmlrpc php7.2-zip php7.2-intl php7.2-gd
# HTTPS software (you need to make a certbot HTTPS certificate)
sudo apt-get install certbot python-certbot-apache
# For things like user accounts and other features to work
sudo add-apt-repository universe
sudo add-apt-repository ppa:certbot/certbot
echo "deb https://apache.bintray.com/couchdb-deb bionic main" | sudo tee -a /etc/apt/sources.list
curl -L https://couchdb.apache.org/repo/bintray-pubkey.asc | sudo apt-key add -
sudo apt-get install mysql-server couchdb openjdk-8-jre-headless fortune cowsay imagemagick
```
## Clone or download the code into your web root
```bash
# Go in website directory
cd /var/www/
# Download the code
git clone https://github.com/zaners123/datadeer
```

## Finishing Setup
The last part is to make it public
 - Give it a Domain Name (these cost around $1/month)
 - Set up your router to forward (only) ports 80 and 443 to the static IP you gave it
 - Give the domain to your friends!
 - Have Fun!
 - Remember to keep the code Open Source
    - See [/LICENSE]() for more info 