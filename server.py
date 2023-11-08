import socket
import codecs
import requests
import pygresql
import uuid
from uuid import getnode
from flask import Flask
from pg import DB

TCP_IP = '192.168.1.50'
TCP_PORT = 55502
BUFFER_SIZE = 1146  # Normally 1024, but we want fast response
NUMBER_OF_CONECTIONS = 1
EOL = '\n'


print(" -------------------------------------------- "+EOL)
print("  Iniciando em "+str(TCP_IP)+" porta "+str(TCP_PORT)+"  "+EOL)
print(" -------------------------------------------- "+EOL)


db = DB(dbname='IOTDatabase',
        host='localhost',
        port=5432,user='postgres',
        passwd='fast9002')

cur = conn.cursor()

go = True
processed = 0
soc = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
soc.bind((TCP_IP, TCP_PORT))
#soc.bind((socket.gethostname(), TCP_PORT))
soc.listen(NUMBER_OF_CONECTIONS)

finalData = '';
conn, addr = soc.accept()
#print ('--- ENDEREÇO DE CONEXAO:'+ str(addr))
while(go):
    data = conn.recv(1146)
    if not data:
        print('NO DATA !!!')
        go = False
        break
    string_len = len(data)

    #mac_addr = hex(data[6]),hex(data[7]),hex(data[8]),hex(data[9]),hex(data[10]),hex(data[11])
    # mac_addr = hex(data[7]),hex(data[8]),hex(data[9]),hex(data[10]),hex(data[11]),hex(data[12])
    #mac_addr = bytes.hex(data[6:12])
    mac_addr = bytes.hex(data[16:22])
    #print ('--- MAC ADDR:'+ str(mac_addr) )
    print(bytes.hex(data))
    finalData = finalData + 'mac=>'+str(mac_addr)+"&data=>"+str(bytes.hex(data))
    SQL = "INSERT INTO predatasets (uid, raw_data,  datetime, rmc_ip, rmc_mac, processed ) VALUES ('"+str(uuid.uuid4())+"' , '"+str(finalData)+"', '"+str('NOW()')+"' , '"+str(addr[0])+"' , '"+str(mac_addr)+"','"+str(processed)+"')"
    db.query(SQL)

