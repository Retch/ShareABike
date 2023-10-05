# ShareABike

## Service Setup Schema
Use the docker-compose.yml file from this repository to configure and run the software stack.
![service schema](schema/schema.jpg)

## Api Documentation
For better unerstanding, all api routes come with sample data. Values in [] are placeholder.
### Adapter
#### Update Status
The adapter sends data to the backend via the adapter endpoint. Only values that changed are sent.
```curl
curl -u [ADAPTER_USER]:[ADAPTER_PASSWORD] --location 'http://[BACKEND_HOST]/adapter/[IMEI]/updatestatus' \
--header 'Content-Type: application/json' \
--data '{
    "packetType": "S5Packet",
    "voltage": 4.02,
    "isLocked": false,
    "csq": 27,
    "satellites": 8
    "noGps": false,
    "hdop": 1.5,
    "altitude": 259.2,
    "longitudeHemisphere": "N",
    "latitudeHemisphere": "E",
    "longitudeDegrees": 11.038,
    "latitudeDegrees": 50.963,
    "btMac": "D3:D3:62:AA:11:11",
    "lockSwVersion": "124",
    "lockHwRevision": "C9",
    "lockSwDate": "2022-06-20",
    "event": "recovery"
}'
```
### Client
#### Admin
#### User
