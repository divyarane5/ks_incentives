
<?php
//print_r($client); exit;
$const = ["[client_name]", "[sales_person]", "[name]","[designation]","[mobile]","[pid]"];
$dynamic   = [$client->client_name, $client->sales_person, $client->u_name,$client->d_name,$client->mobile,$client->id];

$temp = str_replace($const, $dynamic, $client->content);

echo $temp;
?>