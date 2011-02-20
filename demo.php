<div align="center">

<?php

require_once('qasto.sdk.php');

$qasto =  new Qasto(array(
  'client_id'  => '1293381783',
  'client_secret' => '7b1e34360cfd1720231c699b69e5447b'
));


$login_url = $qasto->getLoginURL();

if($qasto->getStatus()!= TRUE)
{
?>


<p>You are not currently logged in.</p>
<p>Please <a href="<?php echo $login_url;?>">Click Here</a> to login</p>


<?php
}
else
{

$login_url = $qasto->getLogoutURL();

$info = $qasto->api('info');

    
?>
<p>You are logged in <?php echo $info->first_name; ?> <?php echo $info->last_name; ?>.</p>
<p>Please <a href="<?php echo $login_url;?>">Click Here</a> to login</p>
<h1>Your Premium Content</h1>
<img src='<?php echo $info->picture; ?>'>

<?php

}

?>
</div>