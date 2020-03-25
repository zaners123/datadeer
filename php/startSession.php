<?php


//starts session (thus reading session variables, etc)
if (session_status() === PHP_SESSION_NONE) {
	session_start();
}