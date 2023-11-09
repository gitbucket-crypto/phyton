<?php
//---------------------------------------
declare(strict_types=1);
ini_set("default_socket_timeout", '6000');
ini_set('allow_url_fopen',"on");
//---------------------------------------
ini_set('display_errors','0');
ini_set('display_startup_errors','0');
//---------------------------------------
$address = "10.166.0.2";
$port = 55502;
$loop= true;
//---------------------------------------
const NUMBER_OF_CONNECTIOS =1;
//---------------------------------------

echo " -------------------------------------------- ".PHP_EOL;
echo "  Iniciando em ".$address." porta ".$port."   ".PHP_EOL;
echo " -------------------------------------------- ".PHP_EOL;

try
{
    setUID(GUID());
    $conn = connect();
    $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
    socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1);
    socket_bind($socket, $address, $port);
    socket_listen($socket, NUMBER_OF_CONNECTIOS);
    $client  = socket_accept($socket);

    do
    {
        if(false ===( $result =  socket_read($client, 2024, PHP_BINARY_READ)))
        {
            echo "error on read"; exit;
        }

        $buf = bin2hex(@$result);
        if(!$buf = trim($buf))
        {
            $loop = true;
            continue;
        }
        parseData($buf, $conn);
    }
    while($loop = true);

}
catch(\Exception $e)
{
    var_dump($e); exit;
}
finally
{
    setUID(GUID());
}


 function parseData($raw, $conn)
{
    $dateTime = 'NOW()';
    //-----------------------------
    if(strlen($raw)=='128')
    {
        $data.='mac=>'.$raw.'@'; //salva a string do mac address
    }
    if(strlen($raw)>'128')
    {
        $data.='=>'.$raw.'@';
    }

    if(strlen($data)>='1124')
    {
        $mac= '--';
        $SQL ="INSERT INTO predatasets (uid, raw_data, raw_bytes,datetime, rmc_ip, rmc_mac) VALUES ( ? , ? , ? , ? , ? , ? )";
        $stmt = $conn -> prepare($SQL);
        $stmt->execute( [getUID(), strval( $data), strval($data)  , $dateTime  , 'not present', $mac] ) or die('saveRawDatasets');
        unset($stmt);
        setUID(GUID());
    }
}

 $UID;


function setUID($uid)
{
    $UID = $uid;
}

function getUID()
{
    return $UID;
}





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


function GUID()
{
    $guid = strtoupper(bin2hex(openssl_random_pseudo_bytes(16)));
    return $guid;
}
