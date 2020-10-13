<?php require "/var/www/php/header.php"; ?>
    <title>URL Shrinker</title>
<style>
    .gap{
        margin-top: 32px;
    }
</style>
<?php require "/var/www/php/bodyTop.php"; ?>
<h1>Shrink your URLs here!</h1>
<form method="post">
    <table align="center">
        <tbody>
        <tr><td style="text-align: right">Long URL:</td><td><input type="url" name="url" required></td></tr>
        <tr><td style="text-align: right">Short URL: datadeer.net/t.php?q=</td><td><input minlength="3" maxlength="100" type="text" name="short"></td></tr>
        </tbody>
    </table>
    <input type="submit" value="Create!">
    <div class="gap">

    </div>
    <div class="gap">

    </div>
</form>
<?php require "/var/www/php/footer.php"; ?>