<?php
/**TERMS:
 *      Pattern money - money earned on frequent patterns (or common single expenses)
 * DATABASE:
 *  So you need a document-based database (Document-based means its basically pure JSON)
 *      So looks like casandra is out of the picture
 *      CouchDB looks ok
 * As this is highly uninterpretable and unstructured data, I would need something NOSQL to store it.
 *  Input method for income/expense:
 *      Each of these would have two types, pattern/repetitive(EX: job/food) and single(EX: lottery/car).
 *      Expense could include categories (loans, house, food, water, phone, etc).
 *          Some of these categories would be predefined, some could be custom added.
 *  Predicted wealth
 *      Based off of pattern money
 *  Charts of the data
 *      We got line charts, pie charts, something with bars, some of those classic ones that go up like stock
 *  Recomendations
 *      Have it recommend things like taking the bus?
 */

require "/var/www/php/header.php"; ?>
<title>Finances</title>
<?php require "/var/www/php/bodyTop.php"; ?>
<h1>DataDeer.net is great for Finances!</h1>

<h2>Put your <a href="table.php?r=s">One-Time finances here</a></h2>
<h2>Put your <a href="table.php?r=m">Repeating finances here</a></h2>
<h2>Chart your finances <a href="chart.php">Here</a></h2>
<h2>Manage your financial account <a href="manage.php">here</a></h2>