<?php require "/var/www/php/headerNoSignin.php"; ?>
	<title id="title">Cyber Patriots</title>
	<style>div{margin-bottom: 24px;}</style>
<?php require "/var/www/php/bodyTop.php"; ?>
<a style="display: block; text-align: left;font-size: 32px;" href=".">Back</a>
<h1>The Judge</h1>

<div>
	The Judge is what I named a small linux script to see basic vulnerabilities (catches about 30).
	It has stuff for UFW, shell-shock, checking installed programs, guest account, ClamAV, ssh root, password settings, etc.
</div>
<div>
	It might randomly yell at you even if nothing is wrong, don't take it as gospel. To make it an executable, run "<b>chmod +x judge.sh</b>"
	<br>Also, run it with root permissions, such as
	"<b>sudo ./judge.sh</b>"
</div>
<div>Download it <a href="judge.sh">Here</a>!</div>

<?php require "/var/www/php/footer.php"; ?>