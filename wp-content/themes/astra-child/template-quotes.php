<?php

/* Template Name: Quotes */

get_header();

for ( $i = 1; $i <= 5; $i ++ ) {
	echo "<p>Quote $i : " . hs_get_kanye_quotes() . "</p>";
}

get_footer();