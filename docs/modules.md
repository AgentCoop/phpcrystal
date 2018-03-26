# Modules
The *modules* directory in the project root is the place for your application code. By default there are three modules named *api*, *backoffice*, and *frontoffice* residing in the **./modules** directory and having the following structure:
 
 ```
modules/
├── api
│   ├── Http
│   │   └── Controllers
│   │       └── Common.php
│   │
│   └── manifest.php
│
├── backoffice
│   ├── Http
│   │   └── Controllers
│   │       └── Index.php
│   │
│   └── manifest.php
│
└── frontoffice
    ├── Http
    │   └── Controllers
    │       └── Index.php
    │
    ├── manifest.php
    │
    └── Services
        └── View
            └── Index.php

 ```
 
 Every module has a configurational file *manifest.php*.