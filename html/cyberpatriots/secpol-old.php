<?php require "/var/www/php/headerNoSignin.php"; ?>
	<title id="title">Cyber Patriots</title>
<style>
	div{margin-bottom: 24px;}
	section{margin-left: 32px;}
</style>
<?php require "/var/www/php/bodyTop.php"; ?>
<div style="text-align: left">
<a style="display: block; text-align: left;font-size: 32px;" href=".">Back</a>
<h1>Local Security Policy</h1>
<div>
	Settings for Local Security Policy, Windows Services, and Windows Registry can waste a lot of your time during competitions, and there are 100s of them.
	Having to set each one manually every time and double checking them wastes at least half an hour for each windows VM in every competition. Here's a way to set all the settings in less than 2 minutes (once you know how to do it)!
</div>
<div>
	This is organized into phases:
	<ol>
		<li><a href="#1">Opening the editor</a></li>
		<li><a href="#2">Making your Security Template</a></li>
		<li><a href="#3">Applying the settings</a></li>
	</ol>
</div>
<h2 id="1">Phase 1 - Opening the editor</h2>
<section>
	<div>
		Open up the MMC view located <a href="secpolViewer.msc">Here</a>.
		<details>
			<summary>(Or if you dont trust msc files)</summary>
			Open up Microsoft Management Console (search mmc in your taskbar and open the cute red toolbox).
			Then go to <b>File -> Add/remove snap in</b> and add <b>Security Configuration</b> and <b>Security Templates</b>.
		</details><br>
		In this, you can see a couple tabs to your left, where the Magic takes place.
		<img src="secpolview.png" style="border: 2px solid black">
	</div>
</section>
<h2 id="2">Phase 2 - Making your Security Template</h2>
<section>
	<div>Open the Security Templates tab (from Phase 1), then from here, you have two options.</div>
	<ul>
		<li><b>Start from scratch:</b> Make your own template (right click, new template). This will have no set settings, so it's a good clean slate.</li>
		<li><b>Start off of my template:</b> I have a template with around 40 settings set for you <a href="Cyber%20Patriots%20Reccommended%209-30-19.inf">Here</a>, including password complexity, audit settings, CTRL+ALT+DELETE, and more. To add it, click "new template search path" and choose the folder the template's in.</li>
	</ul>
	<div>
		Now that you have a template open, you can start making changes. The most optimal template is where no settings say "Not Defined" because then Windows know exactly what they should be.

	</div>
	<hr>
	<h3>Setting Security Policy Settings (Account Policies, Local Policies, and Event Log)</h3>
	<section>
		<div>
			These are what you would usually find in Local Security Policy.
		</div>
		<div>
			Account Policies are all user-based settings. Make sure passwords are secure enough (Password Policy) and lockout is long enough (Account Lockout Policy).
			The third setting here is Kerberos, which is used for securely authenticating on a network.
			Tickets shouldn't last too long to make sure users still have access, and clocks needs to be close to make sure they're not being manipulated for longer tickets.
		</div>
		<div>
			Local Policies are all system-based settings.
			In Audit Policy, you can choose "Success" and "Failure" for each one. For example, on "Audit Account logon events", if set to "Success" it logs when someone gets in the system. If set to failure, it logs failed logon attempts (wrong password, disabled account, etc).
		</div>
		<div>
			Event Log is to choose logging policies. Logs are important for detecting and reporting problems.
		</div>
	</section>
	<hr>
	<h3>Setting System Services</h3>
	<section>
		<div>
			Under the "System Services" tab, you control what is running in the background on your computer.
		</div>
		<div>
			You will want to <b>Set to Disabled</b> anything insecure or innapropriate, such as XBox gaming, Faxing, and SNMP Trap.
		</div>
		<div>
			You will also want to <b>Set to Automatic</b> anything the user will need, such as Windows Update, Windows Time, Windows Backup, and the Power service.
		</div>
	</section>
	<hr>
	<h3>Setting the Registry</h3>
	<section>
		<div>
			Under the "Registry" tab is where you set advanced windows settings, such as stopping USB auto-mount. This can be useful if you know your way around the Registry.
		</div>
	</section>
	<hr>
	<div>
		Once you have all your desired changes, save it to a .inf file (and if you also like open source, share it on <a href="/share">DataDeer Share</a>)!
	</div>
</section>
<h2 id="3">Phase 3 - Applying the settings</h2>
<section>
	<div>
		Now that you have your .inf file all set up, open the Security Configuration and Analysis tab (from Phase 1).<br>
	</div>
	<div>
		From here, windows gives you directions on what to do (basically, make a new database named whatever, select template, configure computer now).
	</div>
	<div>
		If you need help, here's how to do it. Right click the "Security Configuration and Analysis" thing to the left, click open database.
		Make up some database filename ("asdf.sdb", it doesn't matter).
		Now, <b>Find your inf file, and click open</b>.
		Feel free to go to "analyze computer" to see what it's changing, and if you're happy, click "configure computer now" to change the settings.
	</div>
</section>
<h2>Phase 4 - Profit</h2>
<section>
	<div>
		At this point, you should have gotten around 20-50 points.
		If you made a config file better than mine, tell me and I'll promote it!
		Thankyou!
	</div>
</section>
</div>
<?php require "/var/www/php/footer.php"; ?>