<?php
/**
 * Copyright © Ramon Alexis Celis All rights reserved.
 * See license file for more info.
 */
if (version_compare(phpversion(), $req = '5.6.0', '<') === true) {
    ?>
    	<div style="font:12px/1.35em arial, helvetica, sans-serif;">
			<div style="margin:0 0 25px 0; border-bottom:1px solid #ccc;">
				<h3 style="margin:0; font-size:1.7em; font-weight:normal; text-transform:none; text-align:left; color:#2f2f2f;">
					Whoops, it looks like you have an invalid PHP version.
				</h3>
			</div>
			<p>Sorry for breaking it to  you, there's no way for you to fix this aside from installing the required php version.</p>
			<p>Current: <b>PHP <?=phpversion()?></b></p>
			<p>Required: <b>PHP <?=$req?></b></p>
		</div>
	<?php
    exit;
}

require_once "errors/error.php";
require_once "app/Core.php";

/**
 *	Let's Start !!!
 */
Core::app();