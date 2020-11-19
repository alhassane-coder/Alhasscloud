OC.L10N.register(
    "limit_login_to_ip",
    {
    "Delete" : "verwijderen",
    "Restrict login to IP addresses" : "Beperk inloggen tot bepaalde IP adressen",
    "Allows administrators to restrict logins to their instance to specific IP ranges." : "Sta beheerders toe om logins tot hun server te beperken tot specifiek IP ranges.",
    "This app allows administrators to restrict login to their\nNextcloud server to specific IP ranges. Note that existing sessions will be kept\nopen.\n\nThe allowed IP ranges can be administrated using the OCC command line interface\nor graphically using the admin settings. If you plan to use the OCC tool, the\nfollowing commands would be applicable.\n\nTo whitelist `127.0.0.0/24`:\n\n- `occ config:app:set limit_login_to_ip whitelisted.ranges --value 127.0.0.0/24`\n\nTo whitelist `127.0.0.0/24` and also `192.168.0.0/24`:\n\n- `occ config:app:set limit_login_to_ip whitelisted.ranges --value 127.0.0.0/24,192.168.0.0/24`" : "Deze app laat beheerders het inloggen op hun administrators\nNextcloud server beperken tot specifieke IP ranges. Let op: bestaande sessies blijven\nopen.\n\nDe toegstane IP ranges kunnen worden beheerd met de OCC commandoregel interface\nof grafisch via de beheerinstellingen. Als je de OCC tool wilt gebruiken, dan zijn\nde volgende commando's bruikbaar.\n\nOm `127.0.0.0/24` te whitelisten:\n\n- `occ config:app:set limit_login_to_ip whitelisted.ranges --value 127.0.0.0/24`\n\nOm `127.0.0.0/24` en ook `192.168.0.0/24` te whitelisten:\n\n- `occ config:app:set limit_login_to_ip whitelisted.ranges --value 127.0.0.0/24,192.168.0.0/24`",
    "Restrict login to IP ranges" : "Beperk inloggen tot IP ranges",
    "By default, %s permits logging-in from any IP address. To limit logins to specific IP ranges, you can specify those below." : "%s staat standaard toe om in te loggen vanaf elk IP-adres. Om het inloggen te beperkten tot bepaalde IP-adressen, kun je deze hieronder specificeren.",
    "Add" : "Toevoegen",
    "Not authorized" : "Niet geautoriseerd"
},
"nplurals=2; plural=(n != 1);");
