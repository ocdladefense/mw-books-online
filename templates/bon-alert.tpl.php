<?php



// $message - The message to be displayed in the alert.
// $expiryDate - The date the subscription expires.
// $renewThroughDate - The date the subscription, when renewed, would go through.
$expiryText = $expiryDate->format("Y-m-d");


$renewThroughText = $renewThroughDate->format("F d, Y");

$renewalUrl = "https://ocdla.force.com/OcdlaProduct?id=01t0a000004OuZtAAK";
?>

<div class="bon-alert">
	<span class="alert-message">
		<?php print $message; ?>
	</span>
	<br />
	<a href="<?php print $renewalUrl; ?>">
		Renew now
	</a> to continue your subscription through <?php print $renewThroughText; ?>.
</div>