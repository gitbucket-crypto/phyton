
<?php

$port = 55502;
$addr ="192.168.1.59";
$sock = socket_create_listen($port);

socket_getsockname($sock, $addr, $port);

print "Server Listening on $addr:$port\n";
 return;
$fp = fopen($port_file, 'w');

fwrite($fp, $port);

fclose($fp);

while($c = socket_accept($sock)) {

   /* do something useful */

   socket_getpeername($c, $raddr, $rport);

   print "Received Connection from $raddr:$rport\n";

}

socket_close($sock);

?>
