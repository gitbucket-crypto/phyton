<?php
declare(strict_types=1);
ini_set("default_socket_timeout", '6000');
ini_set('allow_url_fopen',"true");
ini_set('allow_url_open','true');
//---------------------------------------
ini_set('display_errors','0');
ini_set('display_startup_errors','0');
//---------------------------------------
date_default_timezone_set('America/Sao_Paulo');


class Service
{
    function __construct()
    {
        $this->data = null;
        $this->start();
    }


    function start()
    {
        $address ="192.168.1.59"; //"10.166.0.2";
        $port = 55502;
        $loop= true;
        //---------------------------------------
        $NUMBER_OF_CONNECTIONS =1;
        //---------------------------------------

        echo " -------------------------------------------- ".PHP_EOL;
        echo "  Iniciando em ".$address." porta ".$port."   ".PHP_EOL;
        echo " -------------------------------------------- ".PHP_EOL;

        $conn = $this->connect();

        try
        {
            static::setUID($this->GUID());
            $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
            socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1);
            socket_bind($socket, $address, $port);
            socket_listen($socket, $NUMBER_OF_CONNECTIONS);
            $client =  socket_accept($socket);
            $addr = socket_getsockname($client, $address, $port);
            print_r($addr);
            do
            {
                if(false ===( $result =  socket_read($client, 1224, PHP_BINARY_READ)))
                {
                    echo "  error on read   ".PHP_EOL; sleep(1);
                }

                $buf = bin2hex(@$result);
                if(!$buf = trim($buf))
                {
                    $loop = true;
                    continue;
                }
                self::parseData($buf, $conn);socket_set_nonblock($socket);
            }
            while($loop = true);

        }
        catch(\Exception $e)
        {
            var_dump($e); exit;
        }
        finally
        {
          //  setUID(GUID());
        }

    }

    private static $UID;

    private $data;


    static function setUID($uid)
    {
        static::$UID = $uid;
    }

    static function getUID()
    {
        return static::$UID;
    }



    private function parseData($raw, $conn)
    {
        $dateTime = 'NOW()';
        //-----------------------------
        if(strlen($raw)=='128')
        {
            $this->data.='mac=>'.$raw.'@'; //salva a string do mac address
        }
        if(strlen($raw)>'128')
        {
            $this->data.='=>'.$raw.'||';
        }
        if(stripos($this->data, 'mac=>') !=false && $this->data!=null )
        {
            $mac= '--';
            $processed =0;
            $SQL ="INSERT INTO public.predatasets (uid, raw_data, raw_bytes,datetime, rmc_ip, rmc_mac, processed) VALUES ( ? , ? , ? , ? , ? , ? , ?)";
            $stmt = $conn -> prepare($SQL);
            $stmt->execute( [static::getUID(), strval( $this->data), strval($this->data)  , $dateTime  , 'not present', $mac, $processed] ) or die('parseData error on INSERT');
            unset($stmt);
            $this->data = null;
            static::setUID($this->GUID()); sleep(1);
        }
    }



    private function connect()
    {
        $host = "127.0.0.1"; //192.168.210.40
        $port = "5432";
        $dbname = "iotDatabase";
        $user= "postgres";
        $password = 'fast9002';

        $conn  = new \PDO('pgsql:host='.$host.';port='.$port.';dbname='.$dbname.';user='.$user.';password='.$password);
        $conn ->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        return $conn;
    }


    private function GUID()
    {
        $guid = strtoupper(bin2hex(openssl_random_pseudo_bytes(16)));
        return $guid;
    }


}
new Service();

?>
