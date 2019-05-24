<?php
include ("header.php");
include ("pass.php");

$info = $coin->getinfo();
$y = array_reverse($info);
$bal1 = $coin->getbalance();
$bal2 = $coin->getbalance("*", 0);
$bal3 = abs($bal1 - $bal2);
$bal4 = abs("{$y['stake']}");

$showMainAddressChangedMessage = false;
$oldAddress = $primary;
$newAddress;
// Fixes bug when the main address is set to an address not in the wallet -----------------------------------------------------
if($primary != ""){
	$hasPrimary = false;
	$addresses = $coin->getaddressesbyaccount("");

	foreach($addresses as $add){
		if($add == $primary){
			$hasPrimary = true;
		}
	}
	if ($hasPrimary) {
		$address = $primary;
	} else {

		$address = $coin->getaddressesbyaccount("")[0];

		// Duplicated code from setPrimary, as we can't access the function ----------------
		$primaryLocation = "primary".$currentWallet."address.php";
		// Open the file and erase the contents if any
		$fp = fopen($primaryLocation, "w");
		// Write the data to the file
		// CODE INJECTION WARNING!
		fwrite($fp, "<?php\n\$primary='';\n?>");
		// Close the file
		fclose($fp);
		$showMainAddressChangedMessage = true;
		$newAddress = $address;
	}
}
else{
	$address = $coin->getaddressesbyaccount("")[0];
}
//-------------------------------------------------------------

if ($currentWallet == NavCoin){
	$stakinginfo = $coin->getstakinginfo();
	$stakereport = $coin->getstakereport();
	$x = array_reverse($stakinginfo);
	$time = $x['expectedtime'];
}
    $days = floor($time / 86400);
    $hours = floor(($time / 3600) % 24);
    $minutes = floor(($time / 60) % 60);
    $fiatValue = ($bal1 * $priceUsd);
    $fiatValue = sprintf("%01.2f", $fiatValue);
    $unfiatValue = ($bal4 * $priceUsd);
    $unfiatValue = sprintf("%01.2f", $unfiatValue);
    $btcValue = ($bal1 * $priceBtc);
    $btcValue = sprintf("%01.8f", $btcValue);
    $unbtcValue = ($bal4 * $priceBtc);
    $unbtcValue = sprintf("%01.8f", $unbtcValue);
    $img = shell_exec("qrencode --output=- -l H -d 144 -s 50 -m 1 $address"); $imgData = "data:image/png;base64," . base64_encode($img);
    $combined_total_fiat_value = $fiatValue + $unfiatValue;
    $combined_total_btc_value = $btcValue + $unbtcValue;
    $fiatValue = number_format($fiatValue);
    $unfiatValue = number_format($unfiatValue);
    $combined_total_fiat_value = number_format($combined_total_fiat_value);
?>
<div class="row">
	<?php
		if ($showMainAddressChangedMessage == true)
			echo "
			<div class='col-lg-12'>
				<div class='alert alert-info'>
					<strong>Primary Address Updated:</strong>We detected your saved primary address was not owned by your wallet. It has been replaced with a valid address.
					<p><small>This can occur when you restore your wallet after manually setting the primary address.</small></p>
					<small>
						New Address: \"{$address}\"
						<br>Old Address: \"{$oldAddress}\"
					</small>
				</div>
			</div>";
	?>

    <div class="col-lg-6">
        <h3>Available Balance: <font color='green'><?php echo number_format($bal1); ?></font> <?php echo $currentWallet; ?></h3>
        <?php
            if ($bal4 > 0)
                echo "
                <h4>Unavailable Due To (Cold) Staking: <font color='red'><?php echo $bal4; ?></font> <?php echo $currentWallet; ?></h4>
                ";
         ?>
        <h4>BTC Value: <font color='green'><?php echo "{$btcValue}"; ?></font>
        <?php
            if ($bal4 > 0)
                echo "

               | <font color='red'><?php echo '{$symbol}{$unbtcValue}'; ?></font>
               ";
        ?> </h4>

        <h4><?php echo $longCurrency; ?> Value: <font color='green'><?php echo "{$symbol}{$fiatValue}"; ?></font>
        <?php
            if ($bal4 > 0)
                echo "

               | <font color='red'><?php echo '{$symbol}{$unfiatValue}'; ?></font>
               ";
        ?> </h4>
        <h4>Combined Total: <u><?php echo "{$combined_total_btc_value} BTC ({$symbol}{$combined_total_fiat_value})"; ?></u></h4><br/>
        <div class="col-lg-8">
    	<form action="lockcontrol">
       		<button class='btn btn-default btn-block'>Your Wallet Is <?php print($lockState)?> Click To Change</button>
    	</form>
		</div>
	</div>
	<div class="col-lg-3">
		<?php if (isset($_POST['show'])){
			$privKey = $coin->dumpprivkey($address);
			$privKeyImg = shell_exec("qrencode --output=- -l H -d 144 -s 50 -m 1 $privKey");
			$privKeyImgData = "data:image/png;base64," . base64_encode($privKeyImg);
		?>
		<center><img class="emrQRCode" src="<?=$privKeyImgData?>"height="30%" />
		<h4>Private Key</h4></center>
		<?php
		}
		else {
		?>
		<center><h4>In order to</h4>
			<form name="sql-data" method="post" action="<?php $_SERVER['PHP_SELF']?>">
				<button class='btn btn-default' type="submit" name="show" value="show">Show private key</button>
			</form>
		<h4>Wallet must be unlocked for sending, or not encrypted.</h4></center>
		<?php
		}
		?>
	</div>
	<div class="col-lg-3">
		<center><img class="emrQRCode" src="<?=$imgData?>"height="30%" />
		<h4>Public Key</h4></center>
	</div>
</div>
</div>

<div class="well">
<div class="row">
	<div class="col-lg-7">
	<p> Your main wallet address is: <input type="text" name="main_wallet_address" value="<?php print_r($address); ?>" onClick="this.setSelectionRange(0, this.value.length);" size="48" readonly />
	<p>The network is currently on block <?php print_r($coin->getblockcount()); ?>.
	<?php if ($currentWallet == NavCoin): ?>
		<?php echo "<p><b>Stake report</b></p><p>Last 24h: {$stakereport['Last 24H']} NAV</p><p>Last 7d: {$stakereport['Last 7 Days']} NAV</p><p>Last 30d: {$stakereport['Last 30 Days']} NAV</p><p>Last 365d: {$stakereport['Last 365 Days']} NAV</p>" ?>
	<?php endif; ?>
	<?php if ($currentWallet == Philosopherstone): ?>
		<?php echo "<p>Your stake weight is {$x['stakeweight']}.</p>"?>
	<?php endif; ?>
        <?php if ($currentWallet == NavCoin): ?>
		<?php echo "<p>Your estimated time to earn rewards is "?>
		<?php if ($hours < 1 && $days < 1)echo "$minutes minutes.</p>"?>
		<?php if ($hours == 1 && $days < 1)echo "$hours hour $minutes minutes.</p>"?>
		<?php if ($hours > 1 && $days < 1)echo "$hours hours $minutes minutes.</p>"?>
		<?php if ($hours == 0 && $days == 1)echo "$days day $hours hour $minutes minutes"?>
		<?php if ($hours == 1 && $days == 1)echo "$days day $hours hours $minutes minutes"?>
		<?php if ($hours < 1 && $days == 1)echo "$days day $hours hours $minutes minutes"?>
		<?php if ($hours == 0 && $days > 1)echo "$days days $hours hours $minutes minutes"?>
		<?php if ($hours == 1 && $days > 1)echo "$days days $hours hour $minutes minutes"?>
		<?php if ($hours > 1 && $days > 1)echo "$days days $hours hours $minutes minutes"?>
	<?php endif; ?>
		</p></p>
	</div>
     <?php if(file_exists("".$currentWallet."notes.php")){
       include(''.$currentWallet.'notes.php');
	echo "<div class='col-lg-5'>
	  <div class='form-group'>
	  <form action='notes' method='POST'><input type='hidden'>
	    <label for='notes'>Notes:</label>
	      <textarea class='form-control' name='notes' id='notes' cols='60' rows='10'>$notes</textarea>
		<button class='btn btn-default' type='submit' value='setprimary' style='margin-top:10px;'>Save Notes</button>
	  </form>
	  </div>
	</div>";}
     else{
	echo "<div class='col-lg-5'>
	  <div class='form-group'>
	  <form action='notes' method='POST'><input type='hidden'>
	    <label for='notes'>Notes:</label>
	      <textarea class='form-control' name='notes' id='notes' cols='60' rows='10'></textarea>
		<button class='btn btn-default' type='submit' value='setprimary' style='margin-top:10px;'>Save Notes</button>
	  </form>
	  </div>
	</div>";}
?>
</div>
</div>
<?php include ("footer.php"); ?>
