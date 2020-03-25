<?php require "/var/www/php/header.php" ?>
	<title>Raspberry Sprinkler Installation</title>
<?php require "/var/www/php/bodyTop.php"; ?>
<h1>Raspberry Sprinkler Installation</h1>
	If anyone actually installs this, please contact me <a href="mailto:admin@datadeer.net">admin@datadeer.net</a>, and tell me what you think, because when making this, I had no idea if anyone would use it.
	Also if you want any feature added in the app, just ask and i’ll see what I can do. Also <b>if any part of this is confusing, ask me personally</b> (or look it up).

<h4>Time: This will take you 45-120 minutes to fully set up. Possibly less if you have wiring experience.</h4>

	<h2>Index</h2>
<ul>
	<li><a href="#0">Part 0 - Materials</a></li>
	<li><a href="#1">Part 1 – Software</a></li>
	<li><a href="#2">Part 2 – Pi to Relay</a></li>
	<li><a href="#3">Part 3 – Finding a Sprinkler Relay Connection</a></li>
	<li><a href="#4">Part 4 – Relay to Sprinklers</a></li>
	<li><a href="#5">Part 5 – App to Pi</a></li>
	<li><a href="#6">Part 6 – (Optional) Visual Live Monitor</a></li>
	<li><a href="#7">Part 7 – Server Options</a></li>
	<li><a href="#8">Part 8 – Checklist</a></li>
</ul>

<h2>Summary</h2>
The installation summary is first you get the parts, then download software to the computer, then plug it in to your sprinklers.
<h2 id="0">Part 0 - Materials</h2>
<img style="border: 5px solid black" src="parts.jpg" class="fl" width="500px">
To install the Raspberry Sprinkler's required hardware, you will need the following materials:
<h3>A Raspberry Pi</h3>
A Raspberry Pi (Known to work with 2B and 3, should work with other versions such as zero W)<br>
I would recommend buying this in a kit. If not bought in a kit you would need a 5v power supply, a Micro USB card and a USB wifi adapter (unless it is integrated or wired/ethernet).
You also need a way of getting internet. Either by directly connecting it with a cable, or wireless.<br>
Here's a list of Raspberry Pis with <b>Approximate price</b>. I would recommend using a Raspberry Pi Zero W:
<ul>
	<li>$35 - <a href="https://www.raspberrypi.org/products/raspberry-pi-zero-w/#buy-now-modal">A small wireless RaspberryPi</a></li>
	<li>$35 - <a href="https://www.amazon.com/CanaKit-Raspberry-Wireless-Complete-Starter/dp/B07CMVDHWB">A small wireless RaspberryPi</a></li>
	<li>$50 - <a href="https://www.raspberrypi.org/products/raspberry-pi-3-model-b/#buy-now-modal">Raspberry Pi 3B (A powerful model)</a></li>
	<li><a href="https://www.canakit.com/raspberry-pi-zero.html">Raspberry Pi Zero W from CanaKit</a></li>
</ul>

<h3>A Relay</h3>
A relay with at least one channel per station, I bought one On Amazon for less than $10. You would need a female-female ribbon cable to connect this to the Pi; relays commonly
come with one in the box, if you don’t receive one, they can be found online for around $5. If you have more than eight stations, you can use multiple relays.<br>
<br>
Here is the $9 one I used: <a href="https://www.amazon.com/ELEGOO-Channel-Optocoupler-Arduino-Raspberry/dp/B01HCFJC0Y">8ch relay</a><br>

<h3>Some Wires</h3>
You would need this to connect the relay to the sprinkler system. You could buy "Rainbow Wire" (to be neat), or just use some wire (Speaker wire works).
I used <a href="https://www.amazon.com/gp/product/B007R9SQQM">This</a> wire to connect the stations and <a href="https://www.amazon.com/gp/product/B07F8BDWXC">This</a> wire to connect the RaspberryPi to the Relay<br>

<h3>A Box</h3>
If you want to be neat, you can keep the entire thing enclosed in a plastic box, such as <a href="https://www.amazon.com/gp/product/B01N4FSKZM">This (the one I, the owner, used)</a>.

<h2 id="1">Part 1 – Software</h2>
After getting all of the materials, you would want to plug in the Raspberry Pi, its power supply, a keyboard, and a mouse. Also plug in your desired internet interface
(unless it is a Raspberry Pi 3; they have integrated Wi-Fi). This is most commonly USB with Wi-Fi or just Ethernet.<br>

<h3>SSH</h3>
SSH is optional, but makes it much easier to fix problems.<br>
To enable, go into settings (Start>Preferences>Raspberry Pi Configuration>Interfaces>SSH) and enable SSH then click OK.

<h3>Install Java</h3>
Then run in the pi’s terminal (CTRL+ALT+T to open):<
(Ctrl+Alt+T) "<b>sudo apt-get install default-jre</b>". This is Java, which is needed to run the Sprinkler Server.<br>
<h3>Download SprinklerServer.jar</h3>

The jar file is the executable file that runs the sprinklers. You can download it <a href="SprinklerServer.jar">Here</a> or directly from the terminal by
	running the command "<b>wget datadeer.net/rs/SprinklerServer.jar</b>"
	then "<b>mv SprinklerServer.jar Desktop/</b>" to put it on the desktop.<br>
	to make it executable run "<b>chmod +x /home/pi/Desktop/SprinklerServer.jar</b>"

<h3>Run on Startup</h3>
	“<b>sudo nano /etc/rc.local</b>” and type in the file, before exit 0, “sudo java -jar /home/pi/Desktop/SprinklerServer.jar”. This will make the Pi run the program upon startup. To exit nano, type Ctrl+x, then y, then enter.<br>
<h3>Set Timezone</h3>
	In order to get the program to have the right time, switch it to UTC. This is so it is not off by timezones and such. Run the command “<b>sudo dpkg-reconfigure tzdata</b>”,
	then scroll to the bottom and select “none of the above”. Then select “UTC”. RESTART for the time change to take effect. Run "<b>date</b>" and make sure it says “UTC”<br>
<h3>Give it a Static IP</h3>
Two things you need for getting the internet to work are the device and the ip address. To get the device, type in a terminal (Ctrl+Alt+T) “ifconfig”. This will list all
	devices and their subnets. Find the desired (web) interface you will use (eth and eno are ethernet, wlan and really long names are wifi; don’t use lo or global).
	Look, in the description of this device, for “inet x.x.x.x”. Use the first three x’s and that is the subnet you will use.<br>
	After this, run “<b>sudo nano /etc/dhcpcd.conf</b>”. Then type:<br>
&emsp;interface Insert interface here<br>
&emsp;static ip_address=Subnet.250/24<br>
&emsp;static routers=Subnet.1<br>
&emsp;static domain_name_servers=Subnet.1<br>
For me this looked like:<br>
&emsp;interface wlan0<br>
&emsp;static ip_address=10.0.0.250/24<br>
&emsp;static routers=10.0.0.1<br>
&emsp;static domain_name_servers=10.0.0.1<br>
Yours should look very similar.<br>
This will make it so the pi’s ip address won’t change, so it will reliably be the same. After setting this, restart the Pi and test that the internet works. If not, look up “Raspberry Pi static IP”.
<h3>Port Forwarding</h3>
Port Forwarding allows your Raspberry Sprinkler to be accessed from anywhere (and is not necessary if you only want to access the sprinkler over your house's WiFi)<br>
Go into your <b>Internet router’s configuration</b> (this varies from router to router, but try looking up Subnet.1 in your web browser). Then, under Advanced, set port forwarding to forward port 51919 to “Subnet.250”.
This would be the same IP address that you gave the raspberry pi. Then, click apply or OK. That is all the router configuration necessary.<br>

<h2 id="2">Part 2 – Pi to Relay</h2>
<img class="fl" alt="pinout" src="j8header-3b-large.png" width="500px">
After getting internet, now plug the Pi into the relay with the female-female ribbon cable. To know where to plug it in on your raspberry pi, look up the 5v pin, the GND pin,
	and the data pins (the first 8 data pins if you have 8 stations). After getting this information, plug the ribbon cable into the relay. Then plug the relay’s GND into the Pi’s GND.
	After this, plug the relay’s 5v into the pi’s 5v. (CAUTION: If the relay is 3.3v, plug the relay’s 3.3v into the pi’s 3.3v). After this plug the relay data in #1 into the pi’s GPIO pin #0.<br>
Your exact pinout (where to plug in the wires) depends on your model:
<ul>
	<li><a href="https://pi4j.com/1.2/pins/model-zero-rev1.html">Raspberry Pi Zero</a></li>
	<li><a href="https://pi4j.com/1.2/pins/model-zerow-rev1.html">Raspberry Pi Zero W</a></li>
	<li><a href="https://pi4j.com/1.2/pins/model-2b-rev1.html">Raspberry Pi 2B</a></li>
	<li><a href="https://pi4j.com/1.2/pins/model-3b-rev1.html">Raspberry Pi 3B</a></li>
	<li>For any other model, go <a href="https://pi4j.com/1.2/pins/model-zero-rev1.html">Here</a> and click the model name.</li>
</ul>

For more than 8 stations, you can either use two relays (by having the pi’s 8th GPIO pin go to the second relay’s first data pin and giving both power) OR by buying a relay with 16 channels.

<h2 id="3">Part 3 – Finding a Sprinkler Relay Connection</h2>
<h3>Know what you're doing first</h3>
After this, you would plug the relay into the sprinkler line, so that when the relay is closed (connected), the solenoid valve would open.
This varies widely from house to house, but can be done roughly the same way. Do not do this step and ask for help from a friend if you don’t know how electronics well. This means
	if you don’t know voltage, amperage, resistance, AC, DC, short circuting, and similar subjects off of the top of your head, please ask for help from a friend (or email support from me, the app’s creator).<br>
<h3>Find Paths</h3>
So, the goal is to, using a multimeter, find out what needs to be connected to what to make the sprinklers run. Start by testing the with the multimeter the voltage DC between the
some lines: GND,both power lines (test each AC or each DC depending on the power supply), and one station (I would use the closest station to see easily when it is on). Then,
test the exact same thing, but with the station running. In my case (Rain Dial), the voltage difference dropped to zero from the right AC line to the station.
After learning this, I turned off the station and manually connected the right AC line (the <b>Power Source</b>) and station 1 with an alligator clip
(if you do not have an alligator clip lying around anything metal could work). After connecting the two, it turned on the line. This connection could vary for you, but should be similar.<br>
All of these directions may seem complicated, but its as simple as safely testing sprinkler line pins with the power and ground to see when the station turns on, but please be safe.

<h2 id="4">Part 4 – Relay to Sprinklers</h2>
This manual connecting pins with metal can be done automatically with the relay. To connect the relay to the sprinkler system, connect the station 1 to relay 1.
	Then connect relay 1’s NO (Normally Open) part to the power source you found in part 3. Then do this for the other channels. <b>If you have a Main Valve or Pump, see <a href="#7">Server Options</a>.</b><br>
<h2 id="5">Part 5 – App to Pi</h2>
Open the app and go into the settings. Configure the IP address to the one you set statically on the Pi. Press apply, then go to the manual timers and test that
	turning them on turns on that station. If that works, you have successfully set up your Raspberry Sprinklers! It should be noted that if you turn on <b>all
	stations at the same time</b>, the Sprinkler Server could reset because of too much power draw.<br>
<h2 id="6">Part 6 – (Optional) Visual Live Monitor</h2>
<h3>Summary</h3>
<img src="endResult.png" alt="yard" class="fl">
The Visual Live Monitor is an <b>optional</b> feature of a top-down view of your lawn
<h3>Image of Lawn (<a href="Burtchart Gardens.png">Example Image</a>)</h3>First get an arial image of your lawn. To get this, I would recommend Google Maps. Simply type your address,
	select satellite imagery, and take a screenshot that fits your entire lawn. After getting the image I would recommend scaling it down to less than a megapixel
	(largest width or height 1024) for less memory usage. This scaling can be done in Microsoft Paint, GIMP, Pinta, or any simple image editor because they tell
	you your mouse coordinates in the side-bars.
<h3>Coordinates (<a href="exampleLawn.csv">Example Coordinate File</a>)</h3>
	The other thing you need is a coordinate list of your lawn. This is stored as a
	CSV file in a simple format. Each line represents a station. Each line also needs image pixel coordinates to know where to draw the squares. These image pixels
	can be found in the same image editor. Parts of a station are represented by these boxes, having a top left corner and a bottom right corner. <b>A station can be
	multiple boxes</b>. Together these represent your lawn. The format is: “station_number,left,top,right,bottom”. An example is in the folder. An example of where they
	should go can be found in the folder. After you have both of these resources, you should plug your phone into your computer for “File Sharing” and move both files
	into your “Internal Storage” in a folder named “Sprinkler”. They don’t have to be here, but anywhere else is harder to find. After this, you select your image by going into the app and
	tapping the image icon. Then go into the location you put the image and tap it. After that, type the square icon and choose the CSV you made of the stations. After selecting both of these,
	you can now see much easier which stations are on. You can also zoom in and tap a station to set its automatic or manual timing.<br>
<h2 id="7">Part 7 – Server Options</h2>
<h3>Adding Parameters</h3>
	To add program parameters, run the program with text after it, spaced out evenly. For example, type “<b>sudo java -jar SprinklerServer.jar STATUS</b>” when running the jar for the
	status LED. Ensure that it is spelled right and that it is in all capital letters. If it is spelled wrong or in lowercase, it will not be used.<br>
	<h3>Status</h3> If you want to have a status light flash when the program is running (and also when it receives input data), add “<b>STATUS</b>” as a parameter (SEE Adding Parameters). This will flash the
	next unused GPIO pin of the Raspberry PI. To wire this, wire an LED to this pin (the longer part of the LED is the positive side). Then, wire the shorter end to a 110-330 ohm resistor and
	wire the other end of the resistor to Ground on the Raspberry Pi. Now, after starting the Raspberry Pi, the light will flash as long as the program is running. If the program crashes or
	the pi stops it or the pi dies, the light will indicate that you should try to fix the raspberry pi.<br>
<h3>Main Valve</h3> A main valve is used for stations for many reasons. One is if you need a pump to use your sprinklers that would run whenever a station is running. When in use, the first GPIO pin
	(pin number zero) is made the pump pin and will be set LOW (this would close the relay). To run the program so that it has a Main Valve, add "<b>MV</b>" as a parameter (SEE Adding Parameters).<br>
<h3>Quick Change</h3> By default, the Sprinkler Server has a ten second pause between stopping one station and starting the next. This is to prevent valve damage by water pressure complications.
	This can be disabled for an instant change instead by adding "<b>QUICK</b>" as a parameter (SEE Adding Parameters).<br>
<h3>Inverted Pins</h3> For nearly all relays, they are not connected by a HIGH signal and connect when receiving a LOW signal on that relay data-in line. If you have a relay that is the opposite
	or if you are hooking this up to something other than a relay for some reason, add<br>
	"<b>INVERT</b>".
<h2 id="8">Part 8 – Checklist</h2>
If you want to be sure, here is a checklist to speed you up:<br>
<ol>
	<li>Buy all the parts</li>
	<li>Download Raspbian (You don’t need full, and light is harder to use) and write it to a MicroSD card</li>
	<li>Boot up from it by plugging into the Raspberry Pi a monitor, keyboard, mouse, the MicroSD card, and finally the power supply.</li>
	<li>Click on the taskbar the Wi-Fi symbol and type in your SSID and password.</li>
	<li>If you want SSH, click on the taskbar menu, go to settings, and enable SSH.</li>
	<li>Put SprinklerServer.jar on your desktop</li>
	<li>Edit rc.local to run SprinklerServer.jar as sudo</li>
	<li>Make sure you configure Server Options as shown in part 7, or it may not work at all.</li>
	<li>Change the timezone to UTC.</li>
	<li>Make the IP static.</li>
	<li>Restart the Pi (this assigns the static IP, finalizes the timezone change, and runs SprinklerServer).</li>
	<li>Configure the router with port forwarding on 51919 to the static IP you made.</li>
	<li>Connect the relay to the Pi with ribbon cabling.</li>
	<li>Test that the app can change the relay (you hear a click when the relay toggles). If it doesn’t change the relay, reread the steps or email me for support</li>
	<li>Shutdown the pi, unplug your keyboard, monitor, and mouse, and bring it to the Sprinkler Box.</li>
	<li>For each relay, connect one end to the valve power supply and the other end to the valve’s power.</li>
	<li>Connect the valve’s ground to the power supply’s ground (or the other AC line if it is AC).</li>
	<li>Plug in the Pi.</li>
	<li>Test each station. If some are swapped or not running, check the wiring.</li>
</ol>
Raspberry Pi is a trademark of the Raspberry Pi Foundation
<?php require "/var/www/php/footer.php" ?>