<?php

require_once('RMC.php');

$conn = connect();

function connect()
{
	$host = "127.0.0.1";
	$port = "5432";
	$dbname = "iotdatabase";
	$user= "postgres";
	$password = 'fast9002';

	$conn  = new \PDO('pgsql:host='.$host.';port='.$port.';dbname='.$dbname.';user='.$user.';password='.$password);
	$conn ->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
	return $conn;
}


$SQL ="SELECT p.uid, p.raw_data, p.datetime, p.rmc_ip,p.rmc_mac  FROM predatasets p WHERE p.processed ='0' ORDER BY p.datetime  LIMIT 2";
$stmt = $conn->query($SQL);
$data= $stmt->fetchAll(\PDO::FETCH_ASSOC) or die('sem dados a serem processados');

global $long;
global $short;
global $UID;

for($i =0 ; $i < count($data); $i++)
{
	if($data[0]['uid']==$data[$i]['uid']  && intval($i)>intval(0) )
	{
		$UID = $data[$i]['uid'];
	}
	else
	{
		$UID[] = $data[$i];
	}
}




foreach($data as $k => $v)
{
	if(stripos($v['raw_data'], '@')!=false)
	{
		$dt = explode("@",$v['raw_data']);
		if(strlen($dt[0])>='128')
		{
			if($dt[0]==$dt[1])
			{
				$short=$dt[0];   break;
			}
			else $short[] = $dt; break;
		}
		break;

	}
	else $long =$v['raw_data'];
}

$dateTime = 'NOW()';

if(!is_null($short) && gettype($short) =='string')
{
	$short =  replace('mac=>','', $short);
	$short =  replace('a1a2a3','', $short);
	$short =  replace('a3a2a1','', $short);
	if(strlen($short)=='116')
	{
		$short = substr($short,6,strlen($short));
		if(validadeMacAddres($short))
		{
			$mac = substr(AddSeparator($short,':'), 0, 17);
			if(!defined('MAC'))
			{
				define("MAC", $mac);
			}
			echo $mac; exit;
			$SQL = "UPDATE predatasets SET rmc_mac=? WHERE uid=?";//.$data[0]['uid']."'";
			$conn->prepare($SQL)->execute([$mac , $data[0]['uid'] ]);
		}

	}
}
if(gettype($short)=='array')
{

}

if(!is_null($long))
{
	print_r($long);
}


function replace($preg, $to, $string)
{
	return preg_replace('/'.$preg.'/',$to, $string);
}


function validadeMacAddres($mac)
{
	return (preg_match('/([a-fA-F0-9]{2}[:|\-]?){6}/', $mac) == 1);
}

function RemoveSeparator($mac, $separator = array(':', '-'))
{
	return str_replace($separator, '', $mac);
}

function AddSeparator($mac, $separator = ':')
{
	$result = '';
	while (strlen($mac) > 0)
	{
		$sub = substr($mac, 0, 2);
		$result .= $sub . $separator;
		$mac = substr($mac, 2, strlen($mac));
	}

	// remove trailing colon
	return substr($result, 0, strlen($result) - 1);
}


?>
