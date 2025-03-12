### Backend ElectronMessenger

The backendElectronDSMessenger repository provides the backend for the Medicare Desktop Messenger. It is built with PHP and utilizes a PDO class to ensure a secure and flexible database connection. The integration of ODBC drivers guarantees compatibility with MSSQL databases. The API follows the RESTful architecture principle, supporting HTTP methods PUT, DELETE, POST, OPTIONS, and GET to handle client requests. The implementation ensures a secure and stable communication channel between the messenger frontend and the database, enabling efficient management and processing of messages and user data.

## Installation Steps

[ - ] Check integration of ODBC drivers (msodbcsql.msi)

[ + ] Chech integration of ODBC drivers in php.ini

[ + ] Chech Extensions DLL (php_pdo_sqlsrv_82_ts_x64.dll & php_sqlsrv_82_ts_x64.dll) in C:\xampp\php\ext\

[ + ] PHP Version minimum 8.2 or bigger required
