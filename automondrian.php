<?php
	// If set, get name/value pairs for the various image
	// parameters.  If not set, choose a pleasing default.
	// Run everything through intval() or floatval() so as
	// to eliminate cross-site scripting JavaScript injections.
	$density = isset($_REQUEST['density'])? intval($_REQUEST['density']): 4;
	$whiteness = isset($_REQUEST['whiteness'])? intval($_REQUEST['whiteness']): 5;
	$splitty = isset($_REQUEST['splitty'])? intval($_REQUEST['splitty']): 6;
	$buffer = isset($_REQUEST['buffer'])? intval($_REQUEST['buffer']): 5;
	$maf = isset($_REQUEST['maf'])? floatval($_REQUEST['maf']): 0.45;
	$mar = isset($_REQUEST['mar'])? floatval($_REQUEST['mar']): 0.45;

	// Ensure sane enough values to prevent some kind of infinite recursion,
	// etc etc.
	if ($density <= 0) $density = 4;
	if ($whiteness <= 0) $whiteness = 5;
	if ($splitty <= 0) $splitty = 5;
	if ($buffer <= 0) $buffer = 6;
	if ($maf <= 0.0 || $maf >= 1.0) $maf = 0.45;
	if ($mar <= 0.0 || $mar >= 20.0) $mar = 3.0;

	// Compose the GET parameter for the call to mond.php,
	// which actually generates a PNG image based on these
	// parameters, and the mt_rand() PRNG.
	$mondrianity =
		'buffer='.$buffer    // 2*$buffer - min distance between lines
		. '&density='.$density  // Recursion depth
		. '&whiteness='.$whiteness  // Red, yellow, blue, plus this many white color entries.
		. '&splitty='.$splitty  // 1 out of $splitty lines is just a line, not a box border
		. '&maf='.$maf        // Don't color in boxes larger than proportion $maf
		. '&mar='.$mar        // Don't color in boxes with aspect ration > $mar
	;
?>
<html>
<head>
<title>AutoMondrian</title>
</head>
<body>
<h1>AutoMondrian</h1>
<h2>Mid-century Modern for the Masses</h2>
<p><em><a href="mailto:bediger@stratigery.com">Bruce Ediger</a></em></p>
<hr/>
<form name="f" method="POST" >
<center>
<img src="mond.php?<?php echo $mondrianity; ?>" />
</center>
<hr/>
<h2>Change Mondrianity</h2>
<table>
	<tr>
		<td>Density:</td>
		<td><input type="text" name="density" size="1" value="<?php echo $density; ?>" /></td>
		<td>Higher density, more squares.</td>
	</tr>
	<tr>
		<td>Whiteness:</td>
		<td><input type="text" name="whiteness" size="1" value="<?php echo $whiteness; ?>" /></td>
		<td>Higher whiteness, fewer colored rectangles.</td>
	</tr>
	<tr>
		<td>Splitty:</td>
		<td><input type="text" name="splitty" size="1" value="<?php echo $splitty; ?>" /></td>
		<td>Higher splittyness, fewer through lines.</td>
	</tr>
	<tr>
		<td>Buffer:</td>
		<td><input type="text" name="buffer" size="1" value="<?php echo $buffer; ?>" /></td>
		<td>Higher buffer, only bigger rectangles are colored.</td>
	</tr>
	<tr>
		<td>Max Area fill:</td>
		<td><input type="text" name="maf" size="5" value="<?php echo $maf; ?>" /></td>
		<td>Higher values (up to 1.0) mean bigger rectangles are colored.</td>
	</tr>
	<tr>
		<td>Max Aspect ratio to fill:</td>
		<td><input type="text" name="mar" size="1" value="<?php echo $mar; ?>" /></td>
		<td>Closer to 1 means that only squarish rectangles are colored.</td>
	</tr>
</table>
<input type="submit" name="b1" value="Another Art!" />
</form>
<h2>But, but... why?</h2>
<p>
I saw a <a href="http://www.clear-lines.com/blog/post/Transform-a-picture-in-the-style-of-Mondrian-with-FSharp.aspx">Mondrianizer in the functional language F#</a>.
This offended me on aesthetic grounds, in that a functional program should not generate "random"
images. Additionally, only Windows users can make any further use of that Mondrianizer.
That offended me on ideological grounds.
</p>
<p>
Acting on my aesthetic sensibilities, I churned out a Mondrian-like picture generator
using a dodgy, open source, imperative language, <a href="http://www.php.net">PHP</a>.
The very sketchiness that PHP is famous for can be used to great effect
in generating "surprising" images. Now you too, can have Art,
and generate Art.  If you have a PHP-enabled website,
put <kbd><a href="automondrian.phps">automondrian.phps</a></kbd> (PHP that generates
the page you're now reading)
and <kbd><a href="mond.phps">mond.phps</a></kbd> (which generated the image above) in your <kbd>htdocs</kbd>
directory, removing the <kbd>s</kbd> suffix.
</p>
<h2>Who Owns The Incredibly Valuable <em>Intellectual Property</em>?</h2>
<p>
I probably own the copyright on the code in <kbd>automondrian.php</kbd> and
<kbd>mond.php</kbd>. Only a court trial can determine for certain.
But hypothetically, who owns the Art generated by <kbd>mond.php</kbd>,
and appearing above?  Does my choice of settings for "mondrianity"
entitle me to a copyright?  Does your choice?
Once again, it looks to me like only a court trial could tell
us with any certainty.
</p>
<p>
We may see just such a court case, albeit <a href="http://tm.durusau.net/?p=55383">with a monkey instead of a program</a>.
</p>
<h2>Other Mondrianesque Image Generators</h2>
<ul>
	<li><a href="http://www.dl.unospace.net/mondrian/">Mondrian in JavaScript</a>, a browser app with setting reminiscent of this page.</li>
	<li><a href="http://vart.institute/mondrian/index.html">More on Mondrian in JavaScript</a>, eloquent exposition.</li>
	<li><a href="http://www.stephen.com/mondrimat/">Mondrimat</a>, a browser app that helps you create Mondrianesque Art.</li>
	<li><a href="http://www.green-lion.net/mondrian_image.html">Auto-Mondrian</a></li>
	<li><a href="http://ooer.com/automondrian/">AutoMondrian</a>, also in PHP</li>
	<li><a href="https://news.ycombinator.com/item?id=5042963">A contest</a>, now, unfortunately, closed.</li>
	<li><a href="http://tombooth.co.uk/painting-in-clojure/">A Jackson Pollock program</a>. Not an auto-Mondrian, but mid-Century, for sure.</li>
</ul>
</body>
</html>
