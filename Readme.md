# ShareABike

## Service Setup Schema
Use the docker-compose.yml file from this repository to configure and run the software stack.
![service schema](schema/schema.jpg)

## Api Documentation
For better unerstanding, all api routes come with sample data. Values in [] are placeholder.
### Adapter
#### Update Status
The adapter sends data to the backend via the adapter endpoint. Only values that changed are sent from the adaptet to the backend. All values are optional.
##### Request
```curl
curl -u [ADAPTER_BACKEND_USER]:[ADAPTER_BACKEND_PASSWORD] --location 'http://[BACKEND_HOST]/adapter/[IMEI]/updatestatus' \
--header 'Content-Type: application/json' \
--data '{
    "packetType": "S5Packet",
    "voltage": 4.02,
    "isLocked": false,
    "csq": 27,
    "satellites": 8,
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
##### Response:
Status codes
- 200: Data was successfully received
- 401: Propably username or password not correct
- 404: Propably lock with given imei does not exist in backend database
### Auth
The backend uses jwt auth with refresh token in cookie.
#### Login
##### Request
```
curl --location 'http://[BACKEND_HOST]/api/login_check' \
--header 'Content-Type: application/json' \
--data-raw '{
    "username": [USER_EMAIL],
    "password": [USER_PASSWORD]
}'
```
##### Response
If successful, a jwt will be returned in the body and the refresh token will be contained in a cookie.
#### Check jwt
To check the validity of a jwt. Jwt validity period can be adjusted in the compose.
##### Request
```
curl --location 'http://[BACKEND_HOST]:8000/api/jwt_check' \
--header 'Authorization: Bearer [JWT]
```
##### Response
Status codes:
- 200: jwt is valid
- 401: jwt is invalid
### Admin
### User
