<?php
header("Content-type: image/png");

// In a rectangular zone defined by upper left (x1, y1) and lower right
// (x2, y2), decide whether we've recursed far enough (and if so, whether
// to color the zone as a "box"), or if not, whether to draw a vertical
// line across the zone, a horizonal line across the zone, and whether
// to split the current zone into two or four sub-zones.  Four sub-zones
// in the case of both vertical and horizontal lines splitting the zone.
function mond($depth, $split, &$lines, $x1, $y1, $x2, $y2, &$boxes) {

	// 1 out of $splitty single lines is just a line, not a
	// divider between two sub-zones.
	// $buffer is the distance from a previously-existing line that
	// we'll draw another line.
	global $splitty, $buffer;

	if ($depth == 0) {
		if (!$split)
			$boxes[] = array($x1, $y1, $x2, $y2);
		return;
	}

	$line_choice = mt_rand(0,2);

	if ($line_choice == 0 && abs($x1 - $x2) < 2*$buffer) {
		// Can't draw a horizontal line, but maybe a vertical?
		if (abs($y1 - $y2) < 2*$buffer) {
			if (!$split)
				$boxes[] = array($x1, $y1, $x2, $y2);
			return;
		}
		$line_choice = 1;
	}

	if ($line_choice == 1 && abs($y1 - $y2) < 2*$buffer) {
		// Can't draw a vertical line, but maybe a horizontal?
		if (abs($x1 - $x2) < 2*$buffer) {
			if (!$split)
				$boxes[] = array($x1, $y1, $x2, $y2);
			return;
		}
		$line_choice = 0;
	}

	if ($line_choice == 2 && (abs($y1 - $y2) < 2*$buffer
		|| abs($x1 - $x2) < 2*$buffer)) {
		if (!$split)
			$boxes[] = array($x1, $y1, $x2, $y2);
		return;
	}

	switch ($line_choice) {
	case 0:  // Draw a line horizontally across current zone.
		$x3 = rand($x1 + $buffer, $x2 - $buffer);
		$lines[] = array(array($x3,$y1), array($x3,$y2));
		// Either just draw a line, or split zone into two sub-zones
		if (mt_rand(0,$splitty) == 0)
			mond($depth, 1, $lines, $x1, $y1, $x2, $y2, $boxes);
		else {
			// Split current zone into 2 sub-zones.
			mond($depth - 1, 0, $lines, $x1,$y1,$x3,$y2, $boxes);
			mond($depth - 1, 0, $lines, $x3,$y1,$x2,$y2, $boxes);
		}
		break;
	case 1: // Draw a line vertically across current zone.
		$y3 = rand($y1 + $buffer, $y2 - $buffer);
		$lines[] = array(array($x1,$y3), array($x2,$y3));
		// Either just draw the line, or split zone into two sub-zones
		if (mt_rand(0,$splitty) == 0)
			mond($depth, 1, $lines, $x1, $y1, $x2, $y2, $boxes);
		else {
			// Split current zone into 2 sub-zones.
			mond($depth - 1, 0, $lines, $x1,$y1,$x2,$y3, $boxes);
			mond($depth - 1, 0, $lines, $x1,$y3,$x2,$y2, $boxes);
		}
		break;

	case 2: // Draw both horizontal and vertical lines across zone.

		$x3 = mt_rand($x1 + $buffer, $x2 - $buffer);
		$y3 = mt_rand($y1 + $buffer, $y2 - $buffer);

		$lines[] = array(array($x3,$y1), array($x3,$y2));
		$lines[] = array(array($x1,$y3), array($x2,$y3));

		// Draw lines in 4 sub-zones.
		mond($depth - 1, 0, $lines, $x1,$y1,$x3,$y3, $boxes);
		mond($depth - 1, 0, $lines, $x3,$y1,$x2,$y3, $boxes);
		mond($depth - 1, 0, $lines, $x1,$y3,$x3,$y2, $boxes);
		mond($depth - 1, 0, $lines, $x3,$y3,$x2,$y2, $boxes);

		break;
	}
}

$width = isset($_REQUEST['width'])? intval($_REQUEST['width']): 500;
$height = isset($_REQUEST['height'])? intval($_REQUEST['height']): 350;
$density = isset($_REQUEST['density'])? intval($_REQUEST['density']): 4;
$whiteness = isset($_REQUEST['whiteness'])? intval($_REQUEST['whiteness']): 5;
$splitty = isset($_REQUEST['splitty'])? intval($_REQUEST['splitty']): 6;
$buffer = isset($_REQUEST['buffer'])? intval($_REQUEST['buffer']): 5;
$maf = isset($_REQUEST['maf'])? floatval($_REQUEST['maf']): 0.45;
$mar = isset($_REQUEST['mar'])? floatval($_REQUEST['mar']): 0.45;

if ($maf < 0.01) $maf = 0.345;
if ($density <= 0) $density = 4;
if ($whiteness <= 0) $whiteness = 5;
if ($splitty <= 0) $splitty = 5;
if ($buffer <= 0) $buffer = 6;
if ($mar <= 1.00 || $mar >= 20.0 ) $mar = 3.0;

$total_area = 0.00 + $width*$height;
$img = imagecreatetruecolor($width, $height);

$white   = imagecolorallocate($img, 255, 255, 255);
$black   = imagecolorallocate($img, 0, 0, 0);

$red = imagecolorallocate($img, 255, 0, 0);
$yel = imagecolorallocate($img, 255, 255, 0);
$blu = imagecolorallocate($img, 0, 0, 255);
$colors = array($red, $yel, $blu);

// Here's where the "whiteness" parameter gets used:
for ($i = 0; $i < $whiteness; ++$i)
	$colors[] = $white;

$a = array();  // Black lines
$b = array();  // Colored-in boxes

mond($density, 0, $a, 0, 0, $width, $height, $b);

// Even if $maf set to value > 1.00, and whiteness set to 0,
// some boxes show up white.  They never get into the $boxes
// array in mond(), because of $buffer checks or something.
imagefill($img, 0, 0, $white);

imagesetthickness($img, 4);  // Did Piet Mondrian vary line thickness?

// Color in boxes, as designated during mond() execution.
foreach ($b as $box) {
	$area = abs($box[0] - $box[2]) * abs($box[1] - $box[3]);
	$frac = $area/$total_area;
	$aspect_ratio = abs(floatval($box[0] - $box[2]))/abs(floatval($box[1] - $box[3]));
	if ($aspect_ratio < 1.0) $aspect_ratio = 1.0/$aspect_ratio;
	if ($frac <= $maf && $aspect_ratio <= $mar) {
		imagefilledrectangle(
			$img,
			$box[0], $box[1],
			$box[2], $box[3],
			$colors[array_rand($colors)]
		);
	}
}

// Draw in black lines.
foreach ($a as $line) {
	imageline($img, $line[0][0], $line[0][1], $line[1][0], $line[1][1], $black);
}
	
imagepng($img);

imagedestroy($img);

?>
