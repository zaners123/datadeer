<?php

function getCoinsOfUser($conn, $username) {
	return mysqli_fetch_assoc(
		mysqli_query($conn,
			sprintf("select ((select coalesce(sum(value),0) from currency where touser='%s') - (select coalesce(sum(value),0) from currency where fromuser='%s')) as c;",
				mysqli_real_escape_string($conn, $username),
				mysqli_real_escape_string($conn, $username)
			)
		)
	)["c"];
}

function getLeaderboard($conn) {
	return mysqli_query($conn, "
select u,sum(v) as coins from (
	select touser as u,coalesce(sum(value),0) as v from currency group by u
union all
	select fromuser as u,-coalesce(sum(value),0) as v from currency group by u
) as a group by u order by coins desc,u limit 6;
");
}

function transferCoins($conn,$userFrom,$userTo,$coins,$reason) {
	//can't be same person
	if ($userFrom == $userTo) return false;
	//can't send negative
	if ($coins < 0) return false;
	//can't send more than you have
	if ($userFrom !== "dealer") {
		if (getCoinsOfUser($conn, $userFrom) < $coins) return false;
	}

	//transfer the money
	$res = mysqli_query($conn,sprintf("insert into currency(fromuser,touser,time,value,reason) values ('%s','%s',%s,%s,'%s')",
		mysqli_real_escape_string($conn, $userFrom),
		mysqli_real_escape_string($conn, $userTo),
		$_SERVER["REQUEST_TIME_FLOAT"],
		mysqli_real_escape_string($conn, $coins),
		$reason
	));
	if (count(mysqli_error_list($conn))>0) {
		error_log("DeerCoin Transfer SQL Failed!: ".json_encode(mysqli_error_list($conn)));
	}
	return $res == true;
}