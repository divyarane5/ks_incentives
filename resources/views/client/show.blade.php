
<?php
//print_r($client); exit;
$click = "/homebazaar_hrms/click/".$client->id;
$const = ["[client_name]", "[sales_person]", "[name]","[designation]","[mobile]","[pid]","[click]"];
$dynamic   = [$client->client_name, $client->sales_person, $client->u_name,$client->d_name,$client->mobile,$client->id,$click];

$temp = str_replace($const, $dynamic, $client->content);

echo $temp;
?>