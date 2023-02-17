<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Checking for IGOR Pro Updates</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta name="generator" content="Igor">
<link rel="stylesheet" href="/css/products.css" type="text/css">
</head>
<body>
<!-- Google Analytics code -->
<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try {
var pageTracker = _gat._getTracker("UA-10089045-1");
pageTracker._setDomainName("none");
pageTracker._setAllowLinker(true);
pageTracker._trackPageview();
} catch(err) {}</script>
<!-- End GA code -->

<div id="banner">
	<h2>Technical graphing and data analysis software</h2>
	<h2 class="botag">... for scientists and engineers</h2>
	<a href="/index.html"></a>
</div>

<div id="MainNavBar">
	<ul id="mainNavList">
		<li><a href="/index.html">Home</a></li>
		<li><a href="/products/products.htm">Products</a></li>
		<li><a href="/users/users.htm">User Resources</a></li>
		<li><a href="/support/support.htm">Support</a></li>
		<li><a href="/order/order.htm">Order</a></li>
		<li><a href="/news/news.htm">News</a></li>
		<li><a href="/search/search.htm">Search</a></li>
	</ul>
</div>
<div id="NavBarDivider"></div>
<div id="contentNoNav2">
<center>
<?php
	// Platform handling - set all "want" platforms to true so if none is given the user sees all the available platforms.
	$wantMac= 1;
	$wantWin= 1;
	$wantWinX64= 1;
	$platform= "_no parameter_";		// the requesting platform (from the URL)
	$igorkind= "";		// the requesting platform (from the URL), decriptive
	$is64BitIgor= 0;	// the requesting platform (from the URL)
	$needs32BitIgorFor64BitIgor= 1;	// the Win 64-bit update requires 32-bit Igor 6.10 release or later, and 6.30 is recommended.


	$serialValid = 1;
	$serial = 0;
// TESTING $serial = 38318;
	if (isset($_REQUEST['serial'])) {
		$serial = $_REQUEST['serial'];
		$serial = filter_var($serial, FILTER_SANITIZE_NUMBER_INT);
	}

	switch( $serial ) {
		case 7210: // JP170428: revoked (Leon Lagnado)
		case 7768: // JP170428: revoked (Wade Regehr)
		case 13960: // JP170428: revoked (Don Paul)
		case 16116: // JP170417: revoked
		case 16647: // JP170417: revoked
		case 22194: // JP170428: revoked (Stigler)
		case 22478: // JP170501: revoked
		case 22828: // JP170428: revoked
		case 30195:	// JP170330: revoked
		case 32286:	// JP170322: Aerodyne - revoked
		case 33995: // JP170428: revoked (Abbatt)
		case 34317:	// JP170330: revoked
		case 35055: // JP170428: revoked (Rieke)
		case 35774:	// JP170327: revoked
		case 36791:	// JP170330: revoked
		case 37185:	// JP170331: revoked
		case 38463: // JP170417: revoked
		case 39043:	// pirated by Mathieu, order number 41969.
		case 39425: // JP170417: revoked
		case 39553:	// JP170322: Aerodyne - revoked
		case 40398:	// JP170330: revoked
		case 40766: // JP170428: revoked
		case 41229:	// JP170327: revoked
		case 42451: // JP170417: revoked
		case 43109: // JP170508: revoked
		case 43740: // JP170428: revoked
		case 44172: // JP170417: revoked
		case 45527:	// JP170330: revoked
		case 45743:	// JP170405: revoked
		case 46766:	// JP170330: revoked
		case 46768:	// JP170331: revoked
		case 47273:	// JP170330: revoked
		case 47611:	// JP170327: revoked
		case 50135:	// JP170330: revoked
		case 50198:	// JP170405: revoked
		case 50666: // JP170428: revoked
		case 54329:	// JP170331: revoked
		case 56547:	// 8/6/2015 (TP)
		case 56851: // JP170428: revoked
		case 56893: // JP170428: revoked
		case 57041: // JP170428: revoked
		case 57605:	// aeshdc@gmail.com
		case 62100: // JP170428: revoked (UO/CW)
		case 62352:	// JP170323: revoked
		case 67842:	// JP170330: revoked
		case 86170:	// aeshdc@gmail.com
		case 92919:	// JP151022: Guanghui Hu
		case 29498: // JP170504: revoked
		case 38318: // JP170504: revoked
		case 27306: // JP170505: revoked
		case 35540: // JP170508: revoked
		case 60699: // JP170509: revoked
		case 18862: // JP170602: revoked
		case 54910: // JP170605: revoked
		case 35519: // JP170605: revoked
		case 16143: // JP170605: revoked
		case 40095: // JP170605: revoked
		case 42597: // JP170609: revoked
		case 38540: // JP170609: revoked
		case 38139: // JP170609: revoked
		case 37955: // JP170609: revoked
		case 47796: // JP170619: revoked
		case 48941:	// JP170629: revoked
		case 34129:	// JP170629: revoked
		case 65100:	// JP170629: revoked
		case 50285:	// JP170629: revoked
		case 767:	// JP170629: revoked
		case 25267:	// JP170629: revoked
		case 44169:	// JP170630: revoked
		case 41041:	// JP170630: revoked
		case 26063:	// JP170630: revoked
		case 23041:	// JP170630: revoked
		case 29837:	// JP170706: revoked
		case 39312:	// JP170706: revoked
		case 41481:	// JP170706: revoked
		case 24720:	// JP170706: revoked
		case 40147:	// JP170706: revoked
		case 25626:	// JP170706: revoked
		case 48374:	// JP170706: revoked
		case 47080:	// JP170706: revoked
		case 56901:	// JP170707: revoked
		case 53149:	// JP170707: revoked
		case 53774:	// JP170707: revoked
		case 50725:	// JP170707: revoked
		case 14078:	// JP170711: revoked (105 users!)
		case 55914:	// JP170712: revoked
		case 42493:	// JP170713: revoked
		case 29536:	// JP170724: revoked
		case 27768:	// JP170724: revoked
		case 54477:	// JP170726: revoked
		case 50508:	// JP170726: revoked
		case 30088:	// JP170726: revoked
		case 10943:	// JP170726: revoked
		case 64286:	// JP170802: revoked
		case 45519:	// JP170807: revoked
		case 48564:	// JP170807: revoked
		case 56111:	// JP170810: revoked
		case 18809:	// JP170810: revoked
		case 44178:	// JP170810: revoked
		case 27963:	// JP170810: revoked
		case 21042:	// JP170810: revoked
		case 39936:	// JP170810: revoked
		case 64769:	// JP170810: revoked
		case 38315:	// JP170810: revoked
		case 38257:	// JP170814: revoked
		case 51805:	// JP170815: revoked
		case 30181:	// JP170815: revoked
		case 7174:	// JP170815: revoked
		case 30269:	// JP170816: revoked
		case 48459:	// JP170817: revoked
		case 43336:	// JP170817: revoked
		case 51124:	// JP170817: revoked
		case 12695:	// JP170817: revoked
		case 40531:	// JP170824: revoked
		case 57393:	// JP170907: revoked
		case 66873:	// JP170918: revoked
		case 12386:	// JP170918: revoked
		case 35863:	// JP170918: revoked
		case 39646:	// JP170920: revoked
		case 47996:	// JP170920: revoked
		//case 28846:	// JP170810: revoked, JP171002: unrevoked
		//case 42025:	// JP170508: revoked, JP171002: unrevoked
		//case 39179:	// JP170609: revoked, JP171002: unrevoked
		case 39111:	// JP171004: revoked
		case 14026:	// JP171004: revoked
		case 29497:	// JP171004: revoked
		case 19304:	// JP171010: revoked
		case 40530:	// JP171010: revoked
		case 36878:	// JP171106: revoked

		case 48114:	// JP180119: revoked
		case 60462: // JP180129: revoked
		case 68384: // JP180209: revoked
		case 26090: // JP180209: revoked
		case 39384: // JP180209: revoked
		case 42595: // JP180209: revoked
		case 44364: // JP180220: revoked
		case 33802: // JP180220: revoked
		case 55526: // JP180316: revoked
		case 52738: // JP180316: revoked
		case 57663: // JP180316: revoked
		case 32769: // JP180316: revoked
		case 31171: // JP180321: revoked
		case 11637: // JP180322: revoked
		case 51813: // JP180329: revoked
		case 56725: // JP180330: revoked
		case 37713: // JP180330: revoked
		case 21647: // JP180330: revoked
		case 40673: // JP180405: revoked
		case 40462: // JP180416: revoked
		case 54644: // JP180416: revoked
		case 44845: // JP180416: revoked
		case 9601: // JP180416: revoked
		case 52404: // JP180417: revoked
		case 36205: // JP180417: revoked
		case 36634: // JP180417: revoked
		case 5443: // JP180417: revoked
		case 44515: // JP180417: revoked
		case 40175: // JP180417: revoked
		case 27022: // JP180911: revoked
		case 30506: // JP180911: revoked
		case 56871: // JP180917: revoked
		case 66403: // JP180917: revoked
		case 19705: // JP180917: revoked
		case 50733: // JP180917: revoked
		
		case 55506: // JP190125: revoked
		case 42420: // JP190125: revoked
		case 62994: // JP190125: revoked
		case 47333: // JP190125: revoked
		case 41219: // JP190125: revoked
		case 39525: // JP190125: revoked
		case 48713: // JP190128: revoked
		case 49539: // JP190128: revoked
		case 50018: // JP190128: revoked
		case 65631: // JP190214: revoked
		case 27000: // JP190214: revoked
		case 42541: // JP190214: revoked
		case 39967: // JP190411: revoked
		case 42593: // JP190719: revoked
		case 60681:	// JP190809: revoked
		case 23779:	// JP190809: revoked
		case 47064:	// JP190809: revoked
		case 71794:	// JP190809: revoked
		case 26615:	// JP190820: revoked
		case 39061:	// JP190820: revoked
		case 51522:	// JP190820: revoked
		case 69345:	// JP191004: revoked (Asylum)
		case 30943:	// JP191004: revoked (Asylum)
		case 58887:	// JP191004: revoked (Asylum)
		case 74982:	// JP191004: revoked (Asylum)
		case 51203:	// JP190820: revoked
		case 65824:	// JP190820: revoked
		case 43546:	// JP190820: revoked
		case 53801:	// JP190820: revoked
		case 2840:	// JP190820: revoked
		case 63884:	// JP191029: revoked
		case 42180:	// JP191111: revoked
		case 7196:	// JP191121: revoked
		case 58796:	// JP191126: revoked
		case 43897:	// JP191212: revoked
		case 9665:	// JP191213: revoked
		case 43897:	// JP191213: revoked
		case 44624:	// JP191213: revoked
		
		case 65501:	// JP200108: revoked
		case 42441:	// JP200108: revoked
		case 39986:	// JP200108: revoked
		case 35440:	// JP200108: revoked
		case 40670:	// JP200108: revoked
		case 56612:	// JP200108: revoked
		case 74167:	// JP200108: revoked
		case 41787:	// JP200108: revoked
		case 50450:	// JP200226: revoked
		case 68074: // JP200902: revoked

		case 54502:	// JP210609: revoked
		case 65499: // JP210610: revoked

		// serial numbers that have been returned
		case 42543:
		case 43957:
		case 45199:
		case 45475:
		case 49351:
		case 50494:
		case 51313:
		case 51314:
		case 53841:
		case 60380:
		case 60769:
		case 62563:
		case 62831:
		case 63475:
		case 63711:
		case 65342:
		case 65910:
		case 69583:
		case 69716: // Yi Hsun Yang return
		case 70081:	// Serial Number	70081	Activation Key	RMZS-WTJA-FELT-TZWT-YEYT-TNSJ-E	Qty:  1
			$serialValid = 0;
			break;
	}

// TESTING $platform = "Mac";
// TESTING $platform = "Win";
// TESTING $platform = "Win64";

	if (isset($_REQUEST['platform'])) {
		$platform = $_REQUEST['platform'];
		$platform = trim(filter_var($platform, FILTER_SANITIZE_STRING));
	}
	switch(strtolower($platform)) {	// switch is case-sensitive.
		case "mac":
			$wantWin= 0;
			$wantWinX64= 0;
			$needs32BitIgorFor64BitIgor= 0;
			$igorkind= " for Macintosh";
			break;
		case "win":
			$wantMac= 0;
			$igorkind= " for Windows (32-bit)";
			break;
		case "win64":
			$wantMac= 0;
			$igorkind= " for Windows (64-bit)";
			$is64BitIgor= 1;
			break;
	}
	$sanitizedVersion= "";
	$version = "";

// --------- Mac
// TESTING $version = "6.22B01";	// Mac - older nightly build
// TESTING $version = "6.22";		// Mac - old release
// TESTING $version = "6.23B01";	// Mac - old nightly build after 6.22

// TESTING $version = "6.22A";		// Mac - old release
// TESTING $version = "6.23B04";	// Mac - old nightly build after 6.22A

// TESTING $version = "6.30B01";	// Mac - old public beta
// TESTING $version = "6.30B02";	// Mac - old nightly build
// TESTING $version = "6.30B03";	// Mac - old public beta
// TESTING $version = "6.30B04";	// Mac - old nightly build

// TESTING $version = "6.30";		// Mac - old release
// TESTING $version = "6.31B01";	// Mac - old nightly build after 6.30

// TESTING $version = "6.31";		// Mac - current release
// TESTING $version = "6.32B01";	// Mac - new nightly build after 6.31

// TESTING $version = "6.32";		// Mac - old release
// TESTING $version = "6.32A";		// Mac - old release
// TESTING $version = "6.33B01";	// Mac - old nightly build after 6.32 and 6.32A

// TESTING $version = "6.34";		// Mac - current release
// TESTING $version = "6.35B01";	// Mac - old nightly build after 6.34

// TESTING $version = "6.34A";		// Mac - old release - NEWER than 6.35B01!
// TESTING $version = "6.35B02";	// Mac - old nightly build after 6.34A

// TESTING $version = "6.35";		// Mac - old release
// TESTING $version = "6.36B01";	// Mac - old nightly build after 6.35

// TESTING $version = "6.35A";		// Mac - old release - NEWER than 6.36B01!
// TESTING $version = "6.36B02";	// Mac - old nightly build after 6.35A

// TESTING $version = "6.35A5";		// Mac - old release - NEWER than 6.36B02!
// TESTING $version = "6.36B03";	// Mac - old nightly build after 6.35A5

// TESTING $version = "6.36";		// Mac - old release - NEWER than 6.36B03
// TESTING $version = "6.37B01";	// Mac - old nightly build after 6.36

// TESTING $version = "6.37";		// Mac - current release - NEWER than 6.37B01
// TESTING $version = "6.38B01";	// Mac - new nightly build after 6.37

//------ Win32
// TESTING $version = "6.0.1.0";	// Win - old 6.0x release - should suggest 6.22A
// TESTING $version = "6.0.1.1";	// Win - old 6.0x beta - should suggest 6.22A

// TESTING $version = "6.1.1.0";	// Win - old 6.1x release - should suggest 6.22A,
// TESTING $version = "6.1.0.8";	// Win - old 6.1x beta- should suggest 6.22A, etc

// TESTING $version = "6.2.2.1";	// Win - older nightly build
// TESTING $version = "6.2.2.0";	// Win - old release
// TESTING $version = "6.2.3.1";	// Win - post-6.22 nightly build, actually released before 6.22A (6.2.2.2)!

// TESTING $version = "6.2.2.2";	// Win - old release (6.22A)
// TESTING $version = "6.2.3.4";	// Win - old nightly build after 6.22A

// TESTING $version = "6.3.0.1";	// Win - old public beta
// TESTING $version = "6.3.0.2";	// Win - old nightly build
// TESTING $version = "6.3.0.3";	// Win - old public beta
// TESTING $version = "6.3.0.4";	// Win - old nightly build

// TESTING $version = "6.3.0.0";	// Win - old release (6.30)
// TESTING $version = "6.3.1.1";	// Win - old nightly build after 6.30

// this is the first Release to use a .2 for a release; it was confusing the windows users to drop back to 0.
// TESTING $version = "6.3.1.2";	// Win - old release (6.31)
// TESTING $version = "6.3.2.1";	// Win - old nightly build after 6.31

// Using .2, .3 for a release.
// TESTING $version = "6.3.2.2";	// Win - old release (6.32)
// TESTING $version = "6.3.2.3";	// Win - old release (6.32A)
// TESTING $version = "6.3.3.1";	// Win - old nightly build after 6.32 and 6.32A

// TESTING $version = "6.3.4.0";	// Win - old release (6.34)
// TESTING $version = "6.3.5.1";	// Win - old nightly build after 6.34

// TESTING $version = "6.3.4.1";	// Win - old release (6.34A) - NEWER than 6.3.5.1!
// TESTING $version = "6.3.5.2";	// Win - old nightly build after 6.34A

// TESTING $version = "6.3.5.3";	// Win - old release (6.35)
// TESTING $version = "6.3.6.1";	// Win - old nightly build after 6.35

// TESTING $version = "6.3.5.4";	// Win - old release (6.35A)
// TESTING $version = "6.3.6.2";	// Win - old nightly build after 6.35A

// TESTING $version = "6.3.5.5";	// Win - old release (6.35A5)
// TESTING $version = "6.3.6.3";	// Win - old nightly build after 6.35A5

// TESTING $version = "6.3.6.4";	// Win - old release (6.36)
// TESTING $version = "6.3.7.1";	// Win - current nightly build after 6.36

// TESTING $version = "6.3.7.2";	// Win - current release (6.37)
// TESTING $version = "6.3.8.1";	// Win - current nightly build after 6.37

//------ Win64
// TESTING $version = "6.2.3.1";	// Win64 - old setupIgor6-64.exe
// TESTING $version = "6.2.3.2";	// Win64 - old nightly build

// TESTING $version = "6.2.3.3";	// Win64 - old setupIgor6-64.exe
// TESTING $version = "6.2.3.4";	// Win64 - old nightly build

// TESTING $version = "6.3.0.1";	// Win64 - old public beta setupIgor6-64.exe
// TESTING $version = "6.3.0.2";	// Win64 - old nightly build

// TESTING $version = "6.3.0.3";	// Win64 - old public beta setupIgor6-64.exe
// TESTING $version = "6.3.0.4";	// Win64 - old nightly build

// TESTING $version = "6.3.0.0";	// Win64 - old (and first 64-bit) release (6.30)
// TESTING $version = "6.3.1.1";	// Win64 - old nightly build after 6.30

// this is the first Release to use a .2 for a release; it was confusing the windows users to drop back to 0.
// TESTING $version = "6.3.1.2";	// Win64 - old release (6.31)
// TESTING $version = "6.3.2.1";	// Win64 - old nightly build after 6.31

// Using .2, .3 for a release.
// TESTING $version = "6.3.2.2";	// Win64 - old release (6.32)
// TESTING $version = "6.3.2.3";	// Win64 - old release (6.32A)
// TESTING $version = "6.3.3.1";	// Win64 - old nightly build after 6.32 and 6.32A

// TESTING $version = "6.3.4.0";	// Win64 - old release (6.34)
// TESTING $version = "6.3.5.1";	// Win64 - old nightly build after 6.34

// TESTING $version = "6.3.4.1";	// Win - old release (6.34A) - NEWER than 6.3.5.1!
// TESTING $version = "6.3.5.2";	// Win - old nightly build after 6.34A

// TESTING $version = "6.3.5.3";	// Win - old release (6.35)
// TESTING $version = "6.3.6.1";	// Win - old nightly build after 6.35

// TESTING $version = "6.3.5.4";	// Win - old release (6.35A)
// TESTING $version = "6.3.6.2";	// Win - old nightly build after 6.35A

// TESTING $version = "6.3.5.5";	// Win - old release (6.35A5)
// TESTING $version = "6.3.6.3";	// Win - old nightly build after 6.35A5

// TESTING $version = "6.3.6.4";	// Win - old release (6.36)
// TESTING $version = "6.3.7.1";	// Win - old nightly build after 6.36

// TESTING $version = "6.3.7.2";	// Win - current release (6.37)
// TESTING $version = "6.3.8.1";	// Win - current nightly build after 6.37

//------
	if (isset($_REQUEST['version'])) {
		$version = $_REQUEST['version'];	// potentially anything the user could choose to type: untrusted
		$version = trim(filter_var($version, FILTER_SANITIZE_STRING));
	}

	// Check the version string against a list of known old versions
	// as returned by StringByKey("IGORFILEVERSION",IgorInfo(3))
	// which is what shows up in the Help menu "Updates for Igor <$version>"
	$showLatest=0;		// set to true to show the latest Igor that is available (no or unknown version)
	// Allow 6.0 family to update separately from 6.1 family, since 6.0 runs on earlier OSes than 6.1.
	$have60Update=0;	// will be set to true if we have a release update for the 6.0 user.
	$latestMac60Version="6.06";
	$latestWin60Version="6.06";
	// The 6.1, 6.2, 6.3 "families" are much more similar; we have updaters on Windows from 6.10 to 6.30.
	//
	// (On Mac, 6.3 is a complete installer, since it runs out of the Igor Pro 6.3 Folder.)
//	$have61Update=0;	// set to true if we have a release update for the 6.0 or 6.1 user.
						// Jim's idea is to not set $have61Update anymore for 6.1 users:
						// 	For Mac users, set $showLatest 
						//	for Windows users, set $have63Update
//	$latestMac61Version="6.12";
//	$latestWin61Version="6.12A";	// actually 6.1.2.1, but this is the name of the version
	// Unusually, a Windows 6.1 user can download an updater to 6.30, (so we set $have63Update)
	// but a Mac 6.1 user must download an installer (Igor Pro 6.3 Folder), so for them we just set $showLatest
//	$have62Update=0;	// will be set to true if we have a release update for the 6.0, 6.1, or 6.2 user.
						// Jim's idea is to not set $have62Update anymore for 6.1 or 6.2 users:
						// 	For Mac users, set $showLatest 
						//	for Windows users, set $have63Update
					
//	$latestMac62Version="6.22A";	// that is, latest RELEASE version
//	$latestWin62Version="6.22A";

	$have63Update=0;	// will be set to true if we have a release update for the 6.0, 6.1, 6.2, or 6.3 user.
	$latestMac63Version="6.37";	// that is, latest RELEASE version
	$latestWin63Version="6.37 (6.3.7.2)";
	$latestBeta="8.0.0.0";	// current public beta (ex: 6.30B03), or "" if no public beta available. Only the latest "family" will be beta testing. In this case the 6.3 family.
	$haveBeta=1;			// was set to true below if we have a public 6.x beta update AVAILABLE for the user,
							// set to true now for Igor 7 announcement.

	// 64-bit stuff, currently only on Windows, but we offer the 64-bit update to 32-bit users, too.
	$haveX64Update=0;			// will be set to true below if we have a 64-bit update AVAILABLE for the user
	$latestX64Version="6.3.7.2";	// concurrent with 6.37 release.
	$newerX64="A newer";		// we won't know what 64-bit version they have while the 32-bit Igor is asking about updates.

	$kind="release";		// this will be the kind of the Igor the user is CURRENTLY using (according to the version number in the URL)
	switch($version) {
		default:		// this will catch early 6.0 beta copies
			$showLatest=1;
			$kind= "unknown ('" . $version . "')";
			$version="";	// unrecognized version
			break;

// 6.0x

		// list old Igor 6.0 release versions here. The current 6.0 is 6.0.5.0 on Windows, 6.05A on Mac.
		case "6.00":		// Mac StringByKey("IGORFILEVERSION",IgorInfo(3))
		case "6.0.0.0":		// Win StringByKey("IGORFILEVERSION",IgorInfo(3))
		case "6.01":		// Mac StringByKey("IGORFILEVERSION",IgorInfo(3))
		case "6.0.1.0":		// Win StringByKey("IGORFILEVERSION",IgorInfo(3))
		case "6.02":		// Mac StringByKey("IGORFILEVERSION",IgorInfo(3))
		case "6.0.2.0":		// Win StringByKey("IGORFILEVERSION",IgorInfo(3))
		case "6.02A":		// Mac StringByKey("IGORFILEVERSION",IgorInfo(3))
		case "6.0.2.4":		// Win StringByKey("IGORFILEVERSION",IgorInfo(3))
		case "6.03":		// Mac StringByKey("IGORFILEVERSION",IgorInfo(3))
		case "6.0.3.0":		// Win StringByKey("IGORFILEVERSION",IgorInfo(3))
		// unusual case: 6.03A2 is a mac-only update (no corresponding Windows update)
		case "6.03A":		// Mac StringByKey("IGORFILEVERSION",IgorInfo(3))
		case "6.03A2":		// Mac StringByKey("IGORFILEVERSION",IgorInfo(3))
		case "6.0.3.1":		// Win StringByKey("IGORFILEVERSION",IgorInfo(3)) = 6.03A
		case "6.04":		// Mac StringByKey("IGORFILEVERSION",IgorInfo(3))
		case "6.0.4.0":		// Win StringByKey("IGORFILEVERSION",IgorInfo(3))
		// unusual case: 6.05A is a mac-only update (no corresponding Windows update)
		case "6.05":		// Mac StringByKey("IGORFILEVERSION",IgorInfo(3))
		case "6.05A":		// Mac StringByKey("IGORFILEVERSION",IgorInfo(3))
		case "6.0.5.0":		// Win StringByKey("IGORFILEVERSION",IgorInfo(3))
			$have60Update=1;	// 6.06
			$showLatest= 1;		// 6.3x
			$sanitizedVersion= $version;	// $version is a version we recognize (and it's not malicious PHP code)
			break;
		// list old 6.0 BETA versions that predate our release version here
		// 6.01 betas
		case "6.01B01":		// Mac
		case "6.0.1.1":			// Win
		case "6.01B02":		// Mac
		case "6.0.1.2":			// Win
		case "6.01B03":		// Mac
		case "6.0.1.3":			// Win
		case "6.01B04":		// Mac
		case "6.0.1.4":			// Win
		case "6.01B05":		// Mac
		case "6.0.1.5":			// Win
		case "6.01B06":		// Mac
		case "6.0.1.6":			// Win
		case "6.01B07":		// Mac
		case "6.0.1.7":			// Win
		case "6.01B08":		// Mac
		case "6.0.1.8":			// Win
		case "6.01B09":		// Mac
		case "6.0.1.9":			// Win
		case "6.01B10":		// Mac
		case "6.0.1.10":		// Win
		// 6.02 betas
		case "6.02B01":	// Mac
		case "6.0.2.1":		// Win
		case "6.02B02":	// Mac
		case "6.0.2.2":		// Win
		case "6.02B03":	// Mac
		case "6.0.2.3":		// Win
		// 6.03 betas (none)
		// 6.03A betas (none)
		// 6.04 betas (none)
		// 6.05 betas (hot fix)
		case "6.0.5.1":		// Win StringByKey("IGORFILEVERSION",IgorInfo(3))
			$kind="beta";
			$have60Update=1;	// 6.06
			$showLatest= 1;		// 6.3x
			$sanitizedVersion= $version;	// $version is a version we recognize (and it's not malicious PHP code)
			break;
		// list last Igor 6.0 versions here
		case "6.06":		// Mac StringByKey("IGORFILEVERSION",IgorInfo(3))
		case "6.0.6.0":		// Win StringByKey("IGORFILEVERSION",IgorInfo(3))
			$haveUpdate=0;
			$showLatest= 1;		// 6.30
			$sanitizedVersion= $version;	// $version is a version we recognize (and it's not malicious PHP code)
			break;

// 6.1x

		// old 6.1 Mac betas
		case "6.10B01":	// Mac
		case "6.10B02":	// Mac
		case "6.10B03":	// Mac
		case "6.10B04":	// Mac
		case "6.10B05":	// Mac
		case "6.10B06":	// Mac
		case "6.10B07":	// Mac
		case "6.10B08":	// Mac (Private beta?)
			$kind="beta";
			$showLatest= 1;		// 6.30
			$sanitizedVersion= $version;	// $version is a version we recognize (and it's not malicious PHP code)
			break;
		
		// old 6.1 Win betas
		case "6.1.0.1":		// Win
		case "6.1.0.2":		// Win
		case "6.1.0.3":		// Win
		case "6.1.0.4":		// Win
		case "6.1.0.5":		// Win
		case "6.1.0.6":		// Win
		case "6.1.0.7":		// Win
		case "6.1.0.8":		// Win (Private beta?)
			$kind="beta";
			// Don't offer the 6.3 updater, because that requires release 6.10, and these predate 6.10 release
			$showLatest= 1;		// 6.30
			$sanitizedVersion= $version;	// $version is a version we recognize (and it's not malicious PHP code)
			break;
		// list old Igor 6.1 Mac release versions here
		case "6.10":		// Mac StringByKey("IGORFILEVERSION",IgorInfo(3))
		case "6.11":		// Mac StringByKey("IGORFILEVERSION",IgorInfo(3))
//			$have61Update=1;	// 6.12: SKIP to showLatest (6.30 installer) because we don't have a 6.1->6.2 updater
		// Last Igor 6.1 Mac release
		case "6.12":		// Mac StringByKey("IGORFILEVERSION",IgorInfo(3))
			$showLatest= 1;		// 6.30
			$sanitizedVersion= $version;	// $version is a version we recognize (and it's not malicious PHP code)
			break;
		// list old Igor 6.1 Win release versions here
		case "6.1.0.0":		// (6.10) Win StringByKey("IGORFILEVERSION",IgorInfo(3))
		case "6.1.0.9":		// (6.10A) Win StringByKey("IGORFILEVERSION",IgorInfo(3))
		case "6.1.1.0":		// (6.11) Win StringByKey("IGORFILEVERSION",IgorInfo(3))
		case "6.1.2.0":		// (6.12) Win StringByKey("IGORFILEVERSION",IgorInfo(3))
//			$have61Update=1;	// 6.12A: SKIP to have63Update (6.1x->6.20 updater).
		// Last Igor 6.1 Win release
		case "6.1.2.1":		// (6.12A) Win StringByKey("IGORFILEVERSION",IgorInfo(3))
//			$haveBeta=1;		// 6.20B04
			$haveX64Update=1;		// 6.30 x64
			$have63Update=1;	// WinUpdateIgor61ToLatest.exe (6.1x->6.3x)
			$sanitizedVersion= $version;	// $version is a version we recognize (and it's not malicious PHP code)
			break;

// 6.2x

		// old Mac 6.2 nightly build betas that should install the latest release
		case "6.21B01":		// Mac nightly build after 6.20
		case "6.22B01":		// Mac nightly build after 6.21
		case "6.23B01":		// Mac nightly build after 6.22 and before 6.22A
			$isNightly=1;

		// old Mac 6.2 non-nightly build betas that should install the latest release
		case "6.20B01":		// Mac
		case "6.20B02":		// Mac
		case "6.20B03":		// Mac
//			$haveBeta=1;		// 6.20B04 was the last public beta other than nightly builds
		case "6.20B04":		// Mac
			$kind="beta";
			$showLatest= 1;		// 6.30
			$sanitizedVersion= $version;	// $version is a version we recognize (and it's not malicious PHP code)
			break;
		
		// old Mac nightly 6.2 betas that should install the new beta
		case "6.23B02":		// Mac nightly build after 6.22A ? note: Igor 6.22A was released as 6.2.2.2,
		case "6.23B03":		// Mac nightly build after 6.22A ? not sure any executable had "6.23B02 or 6.23B03"
		case "6.23B04":		// Mac nightly build after 6.22A and before 6.3B01
			$isNightly=1;
			$kind="beta";
			$showLatest= 1;		// 6.30
			$sanitizedVersion= $version;	// $version is a version we recognize (and it's not malicious PHP code)
			break;
		
		// old Win 6.2 nightly betas that should install the latest updater
		case "6.2.0.1":		// Win && WinX64 (was erroneously marked "6.2.0.1" in the 6.2.0.2 installer)
		case "6.2.0.2":		// Win (there was no 6.2.0.2 Win64 released)
		case "6.2.0.3":		// Win (there was no 6.2.0.3 Win64 released)
		case "6.2.0.4":		// Win && Win64
		case "6.2.0.5":		// Win64 only
		case "6.2.0.6":		// Win64 only
		case "6.2.1.1":		// Win nightly build
		case "6.2.2.1":		// Win nightly build
		case "6.2.3.1":		// Win nightly build, Win64 setupIgor6-64.exe
		case "6.2.3.2":		// Win64 nightly build
		case "6.2.3.3":		// Win 64-bit setupIgor6-64.exe before 6.30B01.
		case "6.2.3.4":		// Win 32-bit and Win 64-bit nightly builds after 6.22A and before 6.30B01
//			$isNightly=1;
		// old Win 6.2 non-nightly betas that should install the latest updater
		// I haven't sorted these versions out, yet because there is entanglement between 64-bit and 32-bit versions.
			$kind="beta";
			$have63Update=1;	// WinUpdateIgor61ToLatest.exe (6.1x->6.3x)
			$haveX64Update=1; 	// Suggest 64-bit 6.30x64
			$newerX64= "The current";
			$sanitizedVersion= $version;			// $version is a version we recognize (and it's not malicious PHP code)
			break;

		// list old Igor 6.2 Mac release versions here
		case "6.20":		// Mac StringByKey("IGORFILEVERSION",IgorInfo(3))
		case "6.21":		// Mac StringByKey("IGORFILEVERSION",IgorInfo(3))
		case "6.22":		// Mac StringByKey("IGORFILEVERSION",IgorInfo(3))
		case "6.22A":		// Mac StringByKey("IGORFILEVERSION",IgorInfo(3))
			$showLatest= 1;		// 6.30
			$sanitizedVersion= $version;	// $version is a version we recognize (and it's not malicious PHP code)
			break;
		// list old Igor 6.1 Win release versions here
		case "6.2.0.0":		// Win StringByKey("IGORFILEVERSION",IgorInfo(3))
		case "6.2.1.0":		// Win StringByKey("IGORFILEVERSION",IgorInfo(3))
		case "6.2.2.0":		// Win StringByKey("IGORFILEVERSION",IgorInfo(3))
		case "6.2.2.2":		// (6.22A) Win StringByKey("IGORFILEVERSION",IgorInfo(3))
			$have63Update=1;	// WinUpdateIgor61ToLatest.exe (6.1x->6.3x)
			$haveX64Update=1;	// 6.30x64
			$sanitizedVersion= $version;	// $version is a version we recognize (and it's not malicious PHP code)
			break;

// 6.3X
	// 6.3 BETAS (Mac)
		// old Mac 6.3 betas that should install the new release
		case "6.30B02":		// Mac nightly build after 6.22A and before 6.3B03
		case "6.30B04":		// Mac nightly build after 6.3B03 and before 6.30
		case "6.31B01":		// Mac nightly build after 6.30 and before 6.31
		case "6.32B01":		// Mac nightly build after 6.31 and before 6.32
		case "6.33B01":		// Mac nightly build beta after 6.32 and 6.32A
							// (there was no 6.34B01 because only IgorJ ever had a 6.33)
		case "6.35B01":		// Mac nightly build beta after 6.34
		case "6.35B02":		// Mac nightly build beta after 6.34A
		case "6.36B01":		// Mac nightly build beta after 6.35
		case "6.36B02":		// Mac nightly build beta after 6.35A
		case "6.36B03":		// Mac nightly build beta after 6.35A5
		case "6.37B01":		// Mac nightly build beta after 6.36
			$isNightly=1;
		// old Mac 6.3 non-nightly build (public) betas that should install the new beta
		case "6.30B01":		// Mac public beta after 6.22A and before 6.3B03
		case "6.30B03":		// Mac Public Beta
			$kind="beta";
			$showLatest= 1;		// 6.3x
			$sanitizedVersion= $version;	// $version is a version we recognize (and it's not malicious PHP code)
			break;

		// list current beta versions for the next release version here.
		// For example, while working on 6.31B02, put 6.31B01, etc here.
		// When we ship 6.31, move these to the "list old beta versions that
		// predate our release version here" section.
		//
		// List current Mac beta versions (nightly builds)
		case "6.38B01":		// Mac nightly build beta after 6.37
			$isNightly=1;
//		case "6.38B02":		// Mac public beta installer after 6.37 (none other than nightly build, currently)
			$kind="beta";
			$showNightly= 1;
			$sanitizedVersion= $version;	// $version is a version we recognize (and it's not malicious PHP code)
			break;

	// 6.3 BETAS (Win)
		// old Win 6.3 betas that should install the new release
		case "6.3.0.2":		// Win 32-bit and Win 64-bit nightly builds after 6.22A and before 6.30B03
		case "6.3.0.4":		// Win 32-bit and Win 64-bit nightly builds after 6.30B3 and before 6.30
		case "6.3.1.1":		// Win 32-bit and Win 64-bit nightly builds after 6.30 and before 6.31
		case "6.3.2.1":		// Win 32-bit and Win 64-bit nightly builds after 6.31 and before 6.32
		case "6.3.3.1":		// Win 32-bit and Win 64-bit nightly builds after 6.32 (6.3.2.2) and 6.32A (6.3.2.3)
							// (there was no 6.34B01 because only Igor J ever had a 6.33)
		case "6.3.5.1":		// Win 32-bit and Win 64-bit nightly builds after 6.34
		case "6.3.5.2":		// Win 32-bit and Win 64-bit nightly builds after 6.34A
		case "6.3.6.1":		// Win 32-bit and Win 64-bit nightly builds after 6.35
		case "6.3.6.2":		// Win 32-bit and Win 64-bit nightly builds after 6.35A
		case "6.3.6.3":		// Win 32-bit and Win 64-bit nightly builds after 6.35A5
		case "6.3.7.1":		// Win 32-bit and Win 64-bit nightly builds after 6.36
			$isNightly=1;
		case "6.3.0.1":		// Win 64-bit setupIgor6-64.exe public beta before 6.30B03.
		case "6.3.0.3":		// Win 32-bit and Win 64-bit public beta installers
			$kind="beta";
			$have63Update=1;	// WinUpdateIgor61ToLatest.exe (6.1x->6.3x)
			$haveX64Update=1;	// 64-bit 6.31x64 setupIgor6-64.exe
			$sanitizedVersion= $version;			// $version is a version we recognize (and it's not malicious PHP code)
			break;

		// list current Windows 6.3 beta versions (public beta and nightly builds) here.
		// These have no updates on the same platform, but 32-bit and 64-bit might be out of sync.
		case "6.3.8.1":		// Win 32-bit and Win 64-bit nightly builds after 6.37
			$isNightly=1;
//		case "6.3.8.2":		// Win 32-bit and Win 64-bit public beta installers (none other than nightly build, currently)
			$kind="beta";
			$showNightly= 1;
			if( !$is64BitIgor ) {
				$needs32BitIgorFor64BitIgor= 0;	// already have 32-bit version that's good enough.
				$haveX64Update=1; 	// Suggest 64-bit 6.3x
			}
			$newerX64= "The current";
			$sanitizedVersion= $version;	// $version is a version we recognize (and it's not malicious PHP code)
			break;

	// 6.3 RELEASES (Mac)
		// list old Igor 6.3 Mac release versions here: StringByKey("IGORFILEVERSION",IgorInfo(3))
		case "6.30":
		case "6.31":
		case "6.32":
		case "6.32A":
							// There was no 6.33 release (was Japanese-only)
		case "6.34":
		case "6.34A":
		case "6.35":
		case "6.35A":
		case "6.35A5":
		case "6.36":
			$showLatest= 1;		// 6.3x
			$sanitizedVersion= $version;	// $version is a version we recognize (and it's not malicious PHP code)
			break;

		// current Igor 6.3x Mac release
		case "6.37":
			$haveBeta=1;
			$sanitizedVersion= $version;	// $version is a version we recognize (and it's not malicious PHP code)
			break;

	// 6.3 RELEASES (WIN)
		// list old Igor 6.3 Win release versions here: StringByKey("IGORFILEVERSION",IgorInfo(3))
		case "6.3.0.0":		// 
		case "6.3.1.2":		// (6.31)  - Yes, I've started never resetting to .0
		case "6.3.2.2":		// (6.32)  - Yes, I've started never resetting to .0
		case "6.3.2.3":		// (6.32A)  - Yes, I've started never resetting to .0
		case "6.3.4.0":		// (6.34)  - Since there never was a 6.34 beta (6.3.4.1), .0 was available.
		case "6.3.4.1":		// (6.34A) 
		case "6.3.5.3":		// (6.35) 
		case "6.3.5.4":		// (6.35A) 
		case "6.3.5.5":		// (6.35A5) 
		case "6.3.6.4":		// (6.36) 
			$have63Update=1;	// WinUpdateIgor61ToLatest.exe (6.1x->6.3x)
			$haveX64Update=1; 	// Suggest 64-bit 6.3x
			$sanitizedVersion= $version;	// $version is a version we recognize (and it's not malicious PHP code)
			break;

		// current Igor 6.3x Win and Win64 release
		case "6.3.7.2":		// (6.37) 
			$haveBeta=1;
			if( !$is64BitIgor ) {
				$haveX64Update=1;		// current Win x64
			}
//			$newerX64="A";		// not "A newer", since x64 6.3.4.0 is the same code as 6.3.4.0 (6.34) release
			$newerX64= "The current";
			$needs32BitIgorFor64BitIgor= 0;	// the Win 64-bit update requires 32-bit Igor current release.
			$sanitizedVersion= $version;	// $version is a version we recognize (and it's not malicious PHP code)
			break;

	}
	
	// COMPLAIN ABOUT PIRACY FIRST
	if ( !$serialValid ) {
		echo '<p><font color="red"><strong>You are using a license that has been revoked.</strong></font></p>'
			. '<p>If you own this license<br> contact <a href="mailto:sales@wavemetrics.com">sales@wavemetrics.com</a>.'
			. ' Otherwise you can <a href="https://www.wavemetrics.com/order/order.htm">place an order online</a>.</p>';
			
			// disable updates
			$showNightly=0;
			$have60Update=0;
			$have63Update=0;
			$showLatest=0;
			$haveBeta=0;
			$haveX64Update=0;
	}
	// Show current version or "unknown version"
	if ($sanitizedVersion) {
		echo "<p><b>You are using version " .$sanitizedVersion. " (a " .$kind. " version) of IGOR Pro ".$igorkind.".</b></p>";
		$newer= "A newer";	// $newer should be used only if an update is available
	} else {
	
	// 	$platform= "_no parameter_";		// the requesting platform (from the URL)

		echo "<p><b>The Igor version you are using could not be determined.</b></p>";
		echo "<p>This can happen if your entered serial number has space characters.</p>";
//		echo "<ul type='none'>";
//		echo '<li>platform / OS = "' . filter_var($platform, FILTER_SANITIZE_STRING) . '".</li>';
//		echo '<li>version = "' . filter_var($version, FILTER_SANITIZE_STRING) . '".</li>';
//		echo "</ul>";
//		echo '<p>GET params:</p>';
//		echo '<blockquote>';
//		print_r($_REQUEST);	// not safe; use only while debugging.
//		echo '</blockquote>';

		$newer= "The current";	// because we don't know how new the calling Igor actually is.
		$sanitizedVersion= "(an unrecognized version)";
	}
	
	$divider="<hr>";
	
	if ($showNightly) {
		echo $divider;
		echo '<strong>';
		
		if ($isNightly) {
			echo '<p>You are using a &quot;nightly build&quot; (beta) version of Igor.</p>';
		
		} else if( $isBeta ) {
			echo '<p>You are using a beta version of Igor.</p>';
		}
		
		echo '<p>The latest nightly builds of Igor <a href="https://www.wavemetrics.net/Downloads/latest/">are available here</a>.</strong></p>';
	}
	
	if ($have60Update) {
		echo $divider;
		// user needs to update to a newer release version
		echo '<p><strong> '.$newer.' version of IGOR Pro 6 is available for downloading.</strong></p>';
		echo '<p>For a list of what&#146s changed, see the <a href="https://www.wavemetrics.net/Updaters/WhatsChangedSince6.0.html" onClick="javascript: pageTracker._trackPageview(\'/downloads/WhatsChangedSince6.0.html\');">Release Notes.</a></p>';
		echo '<TABLE CELLSPACING=0 CELLPADDING=0 class="download">';
		
		if( $wantMac ) {
			$macLink=<<<HEREDOC
			<TR class="head">
				<TD class="l1">Macintosh (OS X 10.3.9 or later)</TD>
			</TR>
			<TR>
				<TD class="l1"><A HREF="https://www.wavemetrics.net/Updaters/MacUpdateIgor6ToLatest.dmg" onClick="javascript: pageTracker._trackPageview('/downloads/mac/MacUpdateIgor6ToLatest.dmg');"> Download IGOR Pro $latestMac60Version Updater for Mac OS X</A> 
				</TD>
			</TR>
HEREDOC;
			echo $macLink;
			if( $wantWin ) {
				echo "<TR><TD><br></TD></TR>";
			}
		}
		if( $wantWin ) {
			$winLink=<<<HEREDOC
			<TR class="head">
				<TD class="l1">Windows (2000 or later)</TD>
			</TR>
			<TR>
				<TD class="l1"><A HREF="https://www.wavemetrics.net/Updaters/WinUpdateIgor6ToLatest.exe" onClick="javascript: pageTracker._trackPageview('/downloads/win/WinUpdateIgor6ToLatest.exe');">Download IGOR Pro $latestWin60Version Updater for 32-bit Windows</A>
				</TD>
			</TR>
HEREDOC;
			echo $winLink;
		}
		echo '</TABLE>';
		echo '<br>';
		$divider="<hr>";
	}
	
	// We tell the 32-bit user to update from 6.1x to the latest version of 6.3.
	// See haveX64Update clause below for 64-bit users.
	if( $have63Update && (!$is64BitIgor || !$haveX64Update) ) {	// this is usually used ONLY for Windows
		echo $divider;
		// user needs to update to a newer release version
		echo '<p><strong> '.$newer.' 32-bit version of IGOR Pro 6 is available for downloading.</strong></p>';
		echo '<p>For a list of what&#146s changed, see the <a href="https://www.wavemetrics.net/Updaters/WhatsChangedSince6.1.html" onClick="javascript: pageTracker._trackPageview(\'/downloads/WhatsChangedSince6.1.html\');">Release Notes.</a></p>';
		echo '<TABLE CELLSPACING=0 CELLPADDING=0 class="download">';
		
		if( $wantMac ) {
			$macLink=<<<HEREDOC
			<TR class="head">
				<TD class="l1">Macintosh (OS X 10.4 or later)</TD>
			</TR>
			<TR>
				<TD class="l1">
<!--				<A HREF="https://www.wavemetrics.net/Updaters/MacUpdateIgor61ToLatest.dmg" onClick="javascript: pageTracker._trackPageview('/downloads/mac/MacUpdateIgor61ToLatest.dmg');">Download IGOR Pro $latestMac63Version Updater for Mac OS X</A> 
-->
				<A HREF="https://www.wavemetrics.net/Downloads/Mac/Igor6.dmg" onClick="javascript: pageTracker._trackPageview('/downloads/mac/Igor6.dmg');">Download IGOR Pro $latestMac63Version Installer for Mac OS X</A> 
				</TD>
			</TR>
HEREDOC;
			echo $macLink;
			if( $wantWin ) {
				echo "<TR><TD><br></TD></TR>";
			}
		}
		if( $wantWin ) {
			$winLink=<<<HEREDOC
			<TR class="head">
				<TD class="l1">Windows XP/Vista/7/8 (32-bit)</TD>
			</TR>
			<TR>
				<TD class="l1"><A HREF="https://www.wavemetrics.net/Updaters/WinUpdateIgor61ToLatest.exe" onClick="javascript: pageTracker._trackPageview('/downloads/win/WinUpdateIgor61ToLatest.dmg');"> Download IGOR Pro $latestWin63Version Updater for 32-bit Windows</A>
				</TD>
			</TR>
HEREDOC;
			echo $winLink;
		}
		echo '</TABLE>';
		echo '<br>';
		$divider="<hr>";
	}
	if( $showLatest ) {
		echo $divider;
		// user needs to update to a newer release version
		echo '<p><strong> '.$newer.' 32-bit release version of IGOR Pro 6 is available for downloading.</strong></p>';
		echo '<TABLE CELLSPACING=0 CELLPADDING=0 class="download">';
		
		if( $wantMac ) {
			$macLink=<<<HEREDOC
			<TR class="head">
				<TD class="l1">Macintosh (OS X 10.4 or later)</TD>
			</TR>
			<TR>
				<TD class="l1"><A HREF="https://www.wavemetrics.net/Downloads/Mac/ReadMe.html" onClick="javascript: pageTracker._trackPageview('/downloads/mac/ReadMe.html');">Macintosh Installation Read Me</A>
				</TD>
			</TR>
			<TR>
				<TD class="l1"><A HREF="https://www.wavemetrics.net/Downloads/Mac/Igor6.dmg" onClick="javascript: pageTracker._trackPageview('/downloads/mac/Igor6.dmg');">Download IGOR Pro $latestMac63Version Installer for Mac OS X</A> 
				</TD>
			</TR>
HEREDOC;
			echo $macLink;
			if( $wantWin ) {
				echo "<TR><TD><br></TD></TR>";
			}
		}
		if( $wantWin ) {
			$winLink=<<<HEREDOC
			<TR class="head">
				<TD class="l1">Windows XP/Vista/7/8 (32-bit)</TD>
			</TR>
			<TR>
				<TD class="l1"><A HREF="https://www.wavemetrics.net/Downloads/Win/ReadMe.html" onClick="javascript: pageTracker._trackPageview('/downloads/win/ReadMe.html');">Windows Installation Read Me</A>
				</TD>
			</TR>
			<TR>
				<TD class="l1"><A HREF="https://www.wavemetrics.net/Downloads/Win/setupIgor6.exe" onClick="javascript: pageTracker._trackPageview('/downloads/win/setupIgor6.exe');">Download IGOR Pro $latestMac63Version Installer for 32-bit Windows</A>
				</TD>
			</TR>
HEREDOC;
			echo $winLink;
		}
		echo '</TABLE>';
		echo '<br>';
		$divider="<hr>";
	}
	
	if( $haveBetaFor63 ) {
		echo $divider;
		// user can update/install the beta version.
		echo '<p><strong> '.$newer.' 32-bit <A HREF="https://www.wavemetrics.net/Updaters/AboutPublicBetaVersions.html">beta version</A> of IGOR Pro ' . $latestBeta . ' is available for downloading.</strong>';
		echo '<p>See <A HREF="https://www.wavemetrics.net/Updaters/BetaWhatsChangedSince6.1.html">the Revision Notes</A> for a list of the changes since Igor 6.1.';
		echo '<TABLE CELLSPACING=0 CELLPADDING=0 class="download">';
		
		if( $wantMac ) {
			$macLink=<<<HEREDOC
			<TR class="head">
				<TD class="l1">Macintosh (OS X 10.4 or later)</TD>
			</TR>
			<TR>
				<TD class="l1"><A HREF="https://www.wavemetrics.net/Updaters/MacIgor6.3Beta.dmg" onClick="javascript: pageTracker._trackPageview('/updaters/MacIgor6.3Beta.dmg');">Download IGOR Pro $latestBeta Installer for Mac OS X</A> 
				</TD>
			</TR>
HEREDOC;
			echo $macLink;
			if( $wantWin ) {
				echo "<TR><TD><br></TD></TR>";
			}
		}
		if( $wantWin ) {
			$winLink=<<<HEREDOC
			<TR class="head">
				<TD class="l1">Windows XP/Vista/7/8 (32-bit)</TD>
			</TR>
			<TR>
				<TD class="l1"><A HREF="https://www.wavemetrics.net/Updaters/WinSetupIgor6.3Beta.exe" onClick="javascript: pageTracker._trackPageview('/downloads/win/WinSetupIgor6.3Beta.exe');">Download IGOR Pro $latestBeta Installer for 32-bit Windows</A>
				</TD>
			</TR>
HEREDOC;
			echo $winLink;
		}
		echo '</TABLE>';
		echo '<br>';
		$divider="<hr>";
	} 
	if ($haveX64Update) {
		echo $divider;
		// user can update/install the beta version.
		echo '<p><strong> '.$newerX64.' <font color="red">64-bit</font> IGOR Pro ' . $latestX64Version . ' is available for downloading. The 64-bit version of Igor is not recommended unless you need it for extremely large data sets.</strong>';

		if ($needs32BitIgorFor64BitIgor ) {
			echo '<p>This updater adds the 64-bit Igor application and supporting files to the <strong>required previous installation of (32-bit) Igor 6.3.</strong></p><p>We recommend downloading the <A HREF="https://www.wavemetrics.net/Updaters/WinUpdateIgor61ToLatest.exe">IGOR Pro '. $latestMac63Version.' Updater for 32-bit Windows</A> before installing the 64-bit Igor application.';
		}
		
		echo '<p>See <A HREF="https://www.wavemetrics.net/Updaters/ReadMe%20(64-bit).html">the ReadMe (64-bit)</A> for important information about this 64-bit version of Igor.';
		echo '<p>See <A HREF="https://www.wavemetrics.net/Updaters/WhatsChangedSince6.3.html">the Revision Notes</A> for a list of the changes to Igor since Igor 6.22A.';
		echo '<TABLE CELLSPACING=0 CELLPADDING=0 class="download">';
		
		if( $wantWin || $wantWinX64 ) {
			$winLink=<<<HEREDOC
			<TR class="head">
				<TD class="l1">Windows XP/Vista/7/8 (64-bit)</TD>
			</TR>
			<TR>
				<TD class="l1"><A HREF="https://www.wavemetrics.net/Downloads/Win/setupIgor6-64.exe" onClick="javascript: pageTracker._trackPageview('/downloads/win/setupIgor6-64.exe');">Download IGOR Pro $latestX64Version Updater for 64-bit Windows</A>
				</TD>
			</TR>
HEREDOC;
			echo $winLink;
		}
		echo '</TABLE>';
		echo '<br>';
		$divider="<hr>";
	}
	
	
	if( $haveBeta ) {

		// Igor 8 Released
		echo $divider;

		echo '<h2><strong>Igor Pro 8 released May 22, 2018</strong></h2>';

		echo '<p>Check out Igor Pro 8\'s <a href="https://www.wavemetrics.com/igor-8-highlights">great new features</a> and <a href="https://www.wavemetrics.com/order/order_prices.htm#upGrades">upgrade prices</a>.';
		
		echo '<p><i>Igor Pro 8&rsquo;s Box Plot and Violin Plot graph types make it easy to show the distribution of values within a data set:</i>';

		echo '<p><img src="ViolinGraph0.png" width="60%">';
		echo '<p><img src="ViolinGraph1.png" width="60%">';
		echo '<p><img src="ViolinGraph2.png" width="60%">';

		// Igor 7.0 Release announcement
		echo $divider;

		echo '<h2><strong>Igor Pro 7 released July 26, 2016</strong></h2>';
		
		echo '<p>Check out Igor Pro 7\'s <a href="https://www.wavemetrics.com/products/igorpro/newfeatures/previous/whatsnew7/">new features</a> and <a href="https://www.wavemetrics.com/order/order_prices.htm#upGrades">upgrade prices</a>.';
		
		echo '<p><i>Example of transparent colors in an Igor Pro 7 graph:</i>';
		echo '<p><img src="GraphTransparency.png" width="60%">';
		$divider="<hr>";
	} 

	if( !$showLatest && !$have60Update && !$have62Update && !$haveBeta && !$haveX64Update && !$showNightly ) {
		echo "<p>You are using the latest ".$kind." version of IGOR Pro.</p>";
	}
?>
<hr>
</center>
</div>
<div id="footer">
	<ul>
		<li><a href="/index.html">Home</a></li>
		<li ><a href="/products/products.htm">Products</a></li>
		<li><a href="/users/users.htm">User Resources</a></li>
		<li><a href="/support/support.htm">Support</a></li>
		<li><a href="/order/order.htm">Order</a></li>
		<li><a href="/news/news.htm">News</a></li>
		<li><a href="/search/search.htm">Search</a></li>
	</ul>
</div>
</body>
</html>
