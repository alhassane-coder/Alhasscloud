OC.L10N.register(
    "limit_login_to_ip",
    {
    "Delete" : "Ezabatu",
    "Restrict login to IP addresses" : "Murriztu saio hasiera IP helbideei",
    "Allows administrators to restrict logins to their instance to specific IP ranges." : "Baimendu administratzaileei bere instantziuara saio hasierak murriztea  IP barruti zehatzei.",
    "This app allows administrators to restrict login to their\nNextcloud server to specific IP ranges. Note that existing sessions will be kept\nopen.\n\nThe allowed IP ranges can be administrated using the OCC command line interface\nor graphically using the admin settings. If you plan to use the OCC tool, the\nfollowing commands would be applicable.\n\nTo whitelist `127.0.0.0/24`:\n\n- `occ config:app:set limit_login_to_ip whitelisted.ranges --value 127.0.0.0/24`\n\nTo whitelist `127.0.0.0/24` and also `192.168.0.0/24`:\n\n- `occ config:app:set limit_login_to_ip whitelisted.ranges --value 127.0.0.0/24,192.168.0.0/24`" : "Aplikazio honi esker, administratzaileek hasierako saioa beren\nNextcloud zerbitzura mugatu dezakete IP barruti zehatzetara.\nKontuan izan lehendik dauden saioak irekita mantenduko direla.\n\nBaimendutako IP barrutiak OCC komando lerroko interfazea\nedo administratzailearen ezarpenak grafikoki erabil daitezke.\nOCC tresna erabiltzeko asmoa baduzu, agindu hauek izango lirateke.\n\n`127.0.0.0/24` zerrenda zurian sartzeko:\n\n- `occ config:app:set limit_login_to_ip whitelisted.ranges --value 127.0.0.0/24`\n\n`127.0.0.0/24` eta `192.168.0.0/24` ere zerrenda zurian sartzeko:\n\n- `occ config:app:set limit_login_to_ip whitelisted.ranges --value 127.0.0.0/24,192.168.0.0/24`",
    "Restrict login to IP ranges" : "Murriztu saio hasiera IP barrutiei",
    "By default, %s permits logging-in from any IP address. To limit logins to specific IP ranges, you can specify those below." : "Defektuz, 1%s edozein IP helbidetik baimentzen du sarbidea. IP tarte batzueta mugatzeko, hemen defini ditzakezu.",
    "Add" : "Gehitu",
    "Not authorized" : "Baimenik gabekoa"
},
"nplurals=2; plural=(n != 1);");
