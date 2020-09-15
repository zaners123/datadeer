<?php require "/var/www/php/headerNoSignin.php"; ?>
	<title id="title">Cyber Patriots</title>
	<style>div{margin-bottom: 24px;}</style>
<?php require "/var/www/php/bodyTop.php"; ?>
	<a style="display: block; text-align: left;font-size: 32px;" href=".">Back</a>
	<h1>Practice VMs</h1>

	<div>
		<video controls="controls" src="15- purging vsftpd.mp4"> </video>
	</div>
<div>(This video is an example of how easy it is to get vulnerabilities).</div>


	<table align="center" border="1">
		<thead>
			<tr>
				<td>Name</td>
				<td>Vulnerability Count</td>
				<td>Virtual Machine (VirtualBox)</td>
				<td>Virtual Machine (VMWare)</td>
				<td>How-to videos</td>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>Debian</td>
				<td>23</td>
				<td><a href="score deb 64.zip">score deb 64.zip</a></td>
				<td> </td>
				<td><a href="debian-training.zip">debian-training.zip</a></td>
			</tr>
			<tr>
				<td>Kali</td>
				<td>23</td>
				<td><a href="score kali 64.zip">score kali 64.zip</a></td>
				<td> </td>
				<td><a href="kali-training.zip">kali-training.zip</a></td>
			</tr>
			<tr>
				<td>Ubuntu</td>
				<td>23</td>
				<td> </td>
				<td><a href="Ubuntu16Scoring.zip">Ubuntu16Scoring.zip</a></td>
				<td><a href="ubuntu-training.zip">ubuntu-training.zip</a></td>
			</tr>

		</tbody>
	</table>

	<div>
		Here is a table of practice VMs, and a video on how to get each vulnerability.
	</div>

	<div>
		To use them, extract the .zip file and import them into VirtualBox.
		You can then use the scoring machine and see how many points you have gotten.
	</div>

	<div>
		The how to videos is a file containing a video on how to get points for each vulnerability, and then showing the scoring engine giving me the points.
	</div>
	<div>

	</div>

<?php require "/var/www/php/footer.php"; ?>