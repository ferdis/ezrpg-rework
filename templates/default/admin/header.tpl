<!DOCTYPE html>
<html lang="en">
	<head>
		<meta name="description" content="ezRPG Project, the free, open source browser-based game engine!" />
		<meta name="keywords" content="" />
		
		<link rel="stylesheet" href="../static/default/style.css" type="text/css" />
		
		<script src="../static/scripts/ext/jquery/jquery.1.8.1.min.js"></script>
		<script src="../static/scripts/ext/jquery/plugins/run.js"></script>
		<script src="../static/scripts/security.js"></script>
		
		<title>{$TITLE|default:"ezRPG rework"}</title>
	</head>
	<body>

		<div id="wrapper">

			<div id="header">
				<span id="title"><a href="./">ezRPG <span>rework</span></a></span>
				<span id="time">{$smarty.now|date_format:'%A, %H:%M'}
					<br />
					<strong>Players Online</strong>: {$ONLINE}</span>
			</div>

			<div id="nav">
				<ul>
				<li><a href="index.php">Admin</a></li>
				<li><a href="index.php?mod=Members">Members</a></li>
				<li><a href="../index.php">Back</a></li>
				<li><a href="../index.php?mod=Logout">Log Out</a></li>
				</ul>
			</div>

			<div id="body_wrap">
				<div id="body">
