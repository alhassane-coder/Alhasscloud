OC.L10N.register(
    "limit_login_to_ip",
    {
    "Delete" : "Suprimeix",
    "Restrict login to IP addresses" : "Restringeix l'accés a adreces IP",
    "Allows administrators to restrict logins to their instance to specific IP ranges." : "Permet als administradors restringir els inicis de sessió a la seva instància a rangs d’IP específics.",
    "This app allows administrators to restrict login to their\nNextcloud server to specific IP ranges. Note that existing sessions will be kept\nopen.\n\nThe allowed IP ranges can be administrated using the OCC command line interface\nor graphically using the admin settings. If you plan to use the OCC tool, the\nfollowing commands would be applicable.\n\nTo whitelist `127.0.0.0/24`:\n\n- `occ config:app:set limit_login_to_ip whitelisted.ranges --value 127.0.0.0/24`\n\nTo whitelist `127.0.0.0/24` and also `192.168.0.0/24`:\n\n- `occ config:app:set limit_login_to_ip whitelisted.ranges --value 127.0.0.0/24,192.168.0.0/24`" : "Aquesta aplicació permet als administradors restringir l’inici de sessió al seu\nservidor Nextcloud a rangs d’IP específics. Tingueu en compte que es mantindran les sessions existents\nobertes.\n\nEls rangs d’IP permesos es poden administrar mitjançant la interfície de línia d’ordres de l’OCC\no gràficament mitjançant la configuració d’administrador. Si teniu previst fer servir l’eina OCC,\nles següents ordres serien aplicables.\n\nA la llista blanca `127.0.0.0/24`:\n\n- `occ config:app:set limit_login_to_ip whitelisted.ranges --value 127.0.0.0/24`\n\nA la llista blanca `127.0.0.0/24` i també` 192.168.0.0/24`:\n\n- `occ config:app:set limit_login_to_ip whitelisted.ranges --value 127.0.0.0/24,192.168.0.0/24`",
    "Restrict login to IP ranges" : "Restringeix l'accés a rangs d'IPs",
    "By default, %s permits logging-in from any IP address. To limit logins to specific IP ranges, you can specify those below." : "Per defecte, %s permet l'accés des de qualsevol adreça IP. Per limitar els accessos a rangs d'IPs específics, especifiqueu-los a sota.",
    "Add" : "Afegeix",
    "Not authorized" : "No autoritzat"
},
"nplurals=2; plural=(n != 1);");
