<?php require "/var/www/php/headerNoSignin.php"; ?>
	<title id="title">Cyber Patriots</title>
	<style>
		div{margin-bottom: 24px;}
		section{margin-left: 32px;}
	</style>
<?php require "/var/www/bodyTop.php"; ?>
	<div style="text-align: left">
		<a style="display: block; text-align: left;font-size: 32px;" href=".">Back</a>
		<div style="text-align: center">
			<img src="export.png">
		</div>
		<h1 class="center">Automating Local Security Policy, Firewall, and Services.</h1>
		<div>A <b>LOT</b> of menus on windows has an import/export button, which can also be used to import/export security options. Because of this, you dont need to know how to script, and you can accurately and easily configure settings for the Cyber Patriots round before it starts.</div>
		<div>All of these methods involve "Exporting" which makes a file with all your settings. You can then save this, then "Import" it in the destination VM.</div>

		<div><b>If any part of this is confusing</b>, Email the writer at <a href="mailto:admin@datadeer.net">admin@datadeer.net</a> and I will clarify, and update the page.</div>

		<section style="float:left; display: block">
			<hr class="hrBreak">
			<h1 class="center">Table Of Contents</h1>
			<div>Chapter One: <a href="#1"><img src="secpol.png">Local Security Policies Importing/Exporting</a></div>
			<div>Chapter Two: <a href="#2"><img src="firewall-search.png">Firewall Importing/Exporting</a></div>
			<div>Chapter Three: <a href="#3"><img src="services.png">Services Importing/Exporting</a></div>
			<div>Chapter Four: <a href="#4"><img src="templates.png">Example Templates</a></div>
			<div>Chapter Five: <a href="#5"><img src="reviewTemplate.png">Reviewing Templates</a></div>
		</section>
		<section style="float:left; display: block">
			<hr class="hrBreak">
			<h2 class="center" id="1">Chapter One: Local Security Policies Importing/Exporting</h2>
			<div>
				<div style="float: left">
					<img style="align-content: end" align="right" src="search.png" width="25%">
					<div><b>Step One: Set your rules -</b> Open up Security Policy (Just search SecPol on your task bar, it's the grey box with the lock icon). Then, change rules so the computer becomes more secure.</div>

					<div>
						Some example settings you could set include:
						<ul style="margin-left: 16px">
							<li>"Account Policies > Password Policy > <b>Minimum password length</b>" to 14 characters (New passwords have to be long)</li>
							<li>"Account Policies > Password Policy > <b>Minimum password age</b>" to 9 days</li>
							<li>"Account Policies > Password Policy > <b>Maximum password age</b>" to 30 days (The users will have to change their password monthly)</li>
							<li>"Account Policies > Account Lockout Policy > <b>Account Lockout Duration</b>" to 30 minutes</li>
							<li>"Account Policies > Account Lockout Policy > <b>Account lockout threshold</b>" to 4 invalid logon attempts</li>
							<li>"Account Policies > Account Lockout Policy > <b>Reset account lockout counter after</b>" to 30 minutes</li>
							<li>"Local Policies > Audit Policies > <b>Audit account logon events</b>" to Success,Failure (This will let the computer log when people sign in, or if they get the wrong password)</li>
							<li>"Local Policies > Security Options > <b>Accounts: Guest account status</b>" to Disabled (This disables the Guest account)</li>
							<li>"Local Policies > Security Options > <b>Devices: Prevent users from installing printer drivers</b>" to Enabled</li>
						</ul>
					</div>
				</div>

				<img style="align-content: start" align="left" src="header.png" width="25%">

				<div><b>Step Two: Export your policies -</b> In the top bar, under "Action", click "Export Policy". This will make a file that holds all of your SecPol settings. If you open it in Notepad, you can see all the things it sets.</div>

				<div><b>Step Three: Import your policies -</b> In the VM click "Import Policy" and select the file you made earlier. You'll automatically have all your settings transferred over.</div>

				<div>
					<b>Step Four: Review the template - </b> This is optional, but if before applying the template you want to review it,
					see <a href="#5">Chapter Five, Reviewing Templates</a>
				</div>

			</div>
		</section>
		<!--		DIVIDER -->
		<section style="text-align:left;float:left; display: block">
			<hr class="hrBreak">
			<h2 class="center" id="2">Chapter Two: Firewall Importing/Exporting</h2>
			<img id="imgabove" style="align-content: start" align="left" src="firewallrightclick.png" width="50%">
			<img style="align-content: start" align="left" src="blockports.png" width="50%">

			<div>Luckily, Windows Firewall can also be imported and exported to, just like in Local Security Policy.</div>
			<div>If you open "Windows Defender Firewall with Advanced Security" (Just search firewall and click the long name), you'll be able to right click as shown in the image and Import/Export to other systems.</div>

			<div><b>Step One: Delete All Existing Rules -</b> First just do a good 'ol "CTRL+A" to select all rules, and delete them all (do this for inbound and outbound rules). The reason you can delete them all is because as long as your firewall is on, there should be no inbound "holes", and all outbound traffic is implicitly allowed, so all "Allow" rules for that are useless.</div>

			<div><b>Step Two: Make a TCP Rule -</b> Make a new rule (Right click, new rule). Check the "Port" option, because we want to block ports. <u>Ports are numbers used to say what can go through, like EMail or web browsing.</u></div>

			<div>Select "<b>TCP</b>" and under specific remote ports put "<b>20,21,23,25,69,110,137-139,445</b>". This includes many insecure protocols, such as FTP, Telnet, EMail, TFTP, NetBios, and SMB. These ports are commonly seen as important to close because they are commonly insecure.</div>

			<div>Then go through the menu, selecting "<b>Block the connection</b>" and applying it to all networks (Domain, Private, and Public) because we want it to always be on. Name it "Block TCP Ports"</div>

			<div><b>Step Three: Make a UDP Rule -</b> Go through the rule-adding process again, but this time, select "<b>UDP</b>" and for specific remote ports put the same numbers as before. Some of these are only one or the other (TCP or UDP), but it's fine to block it on both. Name this "Block UDP ports".</div>

			<div><b>Step Four: Export your template (Shown in <a href="#imgabove">image above</a>) -</b> Right click in the place shown in <a href="#imgabove">the image above</a> and click "Export Policy". This makes a text file holding all your cool rules.</div>

			<div><b>Step Five: Import your template -</b> Move your rules file to a new computer (you could use <a href="/share">DataDeer Share</a>), then right click in <a href="#imgabove">the same place</a> and click "Import Policy". Then, your rules will be applied.</div>

		</section>
<!--		DIVIDER -->
		<section style="float: left; display: block">
			<hr class="hrBreak">
			<h2 class="center" id="3">Chapter Three: Services Importing/Exporting</h2>

			<div>Exporting Services is a bit harder than the other ones, but not too bad.</div>
			<div style="float: left">
				<img style="align-content: start" align="left" src="mmcAddSnapIn.png" width="39%">
				<img style="align-content: end" align="right" src="secTemplate.png" width="61%">
				<b>Step One: Open "Security Templates" -</b> Open MMC (Search it in the taskbar), then go to "File > Add/Remove Snap In".
					Scroll to then select Security Templates, then click "Add". Once it's on the right side, click "OK". You then see in MMC you have a tab called "Security Templates".
			</div>
			<div>
				<img align="right" width="40%" src="templateViewer.png">
				<b>Step Two: Open a Template -</b> In your tool, right click "Security Templates" and click "New Template Search Path". Then, choose the directory your templates are/will be in (such as your Desktop).
			</div>
			<div>
				You can then either open a template (.inf file) you have already made (Such as in <a href="#1">Chapter One</a>), or just make a new template (Right click, new template).
			</div>
			<div>
				<img align="right" width="40%" src="templateServices.png">
				<b>Step Three: Change the template -</b> In the template, open the System Services page. Here, you can set services' startup type.
				Services have four startup types:
				<ul>
					<li>Automatic: The service is always running, and starts in bootup.</li>
					<li>Automatic (Delayed Start): The service is always running, but starts later to speed up startup.</li>
					<li>Manual: The service is only running when it's needed. An example of this is Windows Backup, which only runs during backups.</li>
					<li>Disabled: The service never runs, even when requested. This could cause dependency problems.</li>
				</ul>
				Some services you could set include:
				<ul>
					<li>Print Spooler: Disable - This service is used by printers, which could be used for sharing confidential information.</li>
					<li>Fax: Disable - The fax service could be used for sharing confidential information.</li>
					<li>SNMP Trap: Disable - This service is used for getting EMail, which could leak confidential information.</li>
					<li>Xbox Services: Disable - These could be used for unnecessary gaming. There are about 5 of them.</li>
					<li>Windows Update: Automatic - This is vital for security.</li>
					<li>DNS Client: Automatic - This is necessary for caching DNS, which can improve internet speeds.</li>
				</ul>
			</div>

			<div>
				<img align="right" width="40%" src="saveTemplate.png">
				<b>Step Four: Export the template -</b> To export the template, right click it, then click "Save As". You can save this to your Desktop.
			</div>

			<div>
				<img id="3-5-img" style="align-content: start" align="left" width="25%" src="header.png">

				<b>Step Five: Import the template - </b> Open SecPol (Search it in the taskbar), then click "Action > Import Policy",
				as shown in <a href="#3-5-img">the image to the left</a>.
			</div>
			<div>
				<b>Step Six: Review the template - </b> This is optional, but if before applying the template you want to review it,
				see <a href="#5">Chapter Five, Reviewing Templates</a>
			</div>
		</section>
		<!--		DIVIDER -->
		<section style="float: left; display: block">
			<hr class="hrBreak">
			<h2 class="center" id="4">Chapter Four: Example Templates</h2>
			<div>Here's some templates I would recommend using. You could either use them plainly, or add on to them your own ideas!</div>

			<div>
				<b><a href="secPolImport.inf">Recommended SecPol Configuration</a> -</b> This currently contains about 130 Security Policy settings, along with around 20 Windows service startup configurations.
				To use/edit it, see <a href="#1">Local Security Policies Importing/Exporting</a>.
			</div>

			<div><b><a href="firewallImport.wfw">Recommended Firewall Configuration</a> -</b> This contains all necessary ports to block, and doesn't leave a million open ports. Remember, if the readme needs SSH or HTTP, to enable that.</div>

			<div><b><a href="mmcSnapins.msc">Recommended MMC menu</a> -</b> An MMC SnapIn with all the tools you should need, in order, such as user management, shared folders, firewall, GPEdit, and more!</div>
		</section>
		<!--		DIVIDER -->
		<section style="float: left; display: block">
			<hr class="hrBreak">
			<h2 class="center" id="5">Chapter Five: Reviewing Templates</h2>
			<div>
				Reviewing templates is optional, but if you have multiple conflicting templates, it can be helpful.
				You can compare the template to your currently applied settings, and see how they differ.
			</div>
			<div>
				<img id="5-1-img" style="align-content: start" align="left" width="40%" src="mmcAddSnapIn.png">
				<img id="5-1-2-img" style="align-content: start" align="left" width="60%" src="addSnapInSCA.png">
				<b>Step One: Open Security Configuration and Analysis -</b> SCA is a snap-in, so to open it, open MMC (search it in the taskbar) and click
				"File > Add/Remove Snap-in" (as shown in <a href="#5-1-2.png">the image</a>. Then add the "Security Configuration and Analysis" snap in by scrolling to it, clicking it, then clicking "Add". Once you added it, click "OK".
			</div>

			<div style="float: left; display: block">
				<img id="5-1-2-img" style="align-content: start" align="left" width="40%" src="openNewDatabase.png">
				<div>
					<b>Step Two: Make a new Database -</b>With the snap-in opened, this is where it starts to get confusing, so listen closely, and read ahead before starting. Right click it, and click "Open Database".
					<b>Make a database on your Desktop by making up a new name, such as "<u>asdf.sdb</u>", and click "Open". You don't need to find an existing database</b>.
				</div>

				<div><b>Step Three: Import your Templates -</b> It will prompt you to "Import Templates".
					If you don't have a template, either consult <a href="#1">Chapter One</a> or <a href="#3">Chapter Three</a>
					to make a template, or <a href="#4">Chapter Four to <b>use a premade template</b></a>.
				</div>

				<div>
					If you want to import more templates, right click the "Security Configuration and Analysis" again, and click "Import Template".
				</div>
			</div>
			<div style="float: left; display: block">
				<img id="5-4-img" style="align-content: start" align="left" width="40%" src="analyzeSCM.png">
				<img id="5-4-img" style="align-content: start" align="left" width="60%" src="scmExample.png">
				<div>
					<b>Step Four: Analyze the template -</b> Right click the "Security Configuration Analysis" again, and click "Analyze Computer Now".
					Click "OK" for the error log file path. Now, when you go into the tabs inside SCA, you can see <img src="scmCheck.png"> check marks if your computer is set to the same thing,
					or an <img src="scmX.png"> X if they are different. An example of this is in <a href="#5-4-img">the image above</a> where the Max password age on the computer is 30 days,
					but in my database (AKA template), it's 29 (which is slightly more secure).
				</div>
				<div>
					If you want to change the template, double click the row in the view, and change it.
					Remember that this will change the database only, so to change the template, right click the "Security Configuration and Analysis", and click "Export Template".
				</div>
			</div>

			<div>
				<b>Step Five: Apply the template -</b> After analyzing the template, if it looks good, right click "Security Configuration and Analysis", and click "Configure Computer Now".
				This will set the computer's settings to what you just reviewed as acceptable.
			</div>
			<!--				TODO this: add SCA snap in, open database (make database file), analyze, configure.-->
		</section>
	</div>
<?php require "/var/www/php/footer.php"; ?>