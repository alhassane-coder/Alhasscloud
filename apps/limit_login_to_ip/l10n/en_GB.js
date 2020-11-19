OC.L10N.register(
    "limit_login_to_ip",
    {
    "Delete" : "Delete",
    "Restrict login to IP addresses" : "Restrict login to IP addresses",
    "Allows administrators to restrict logins to their instance to specific IP ranges." : "Allows administrators to restrict logins to their instance to specific IP ranges.",
    "This app allows administrators to restrict login to their\nNextcloud server to specific IP ranges. Note that existing sessions will be kept\nopen.\n\nThe allowed IP ranges can be administrated using the OCC command line interface\nor graphically using the admin settings. If you plan to use the OCC tool, the\nfollowing commands would be applicable.\n\nTo whitelist `127.0.0.0/24`:\n\n- `occ config:app:set limit_login_to_ip whitelisted.ranges --value 127.0.0.0/24`\n\nTo whitelist `127.0.0.0/24` and also `192.168.0.0/24`:\n\n- `occ config:app:set limit_login_to_ip whitelisted.ranges --value 127.0.0.0/24,192.168.0.0/24`" : "This app allows administrators to restrict login to their\nNextcloud server to specific IP ranges. Note that existing sessions will be kept\nopen.\n\nThe allowed IP ranges can be administrated using the OCC command line interface\nor graphically using the admin settings. If you plan to use the OCC tool, the\nfollowing commands would be applicable.\n\nTo whitelist `127.0.0.0/24`:\n\n- `occ config:app:set limit_login_to_ip whitelisted.ranges --value 127.0.0.0/24`\n\nTo whitelist `127.0.0.0/24` and also `192.168.0.0/24`:\n\n- `occ config:app:set limit_login_to_ip whitelisted.ranges --value 127.0.0.0/24,192.168.0.0/24`",
    "Restrict login to IP ranges" : "Restrict login to IP ranges",
    "By default, %s permits logging-in from any IP address. To limit logins to specific IP ranges, you can specify those below." : "By default, %s permits logging-in from any IP address. To limit logins to specific IP ranges, you can specify those below.",
    "Add" : "Add",
    "Not authorized" : "Not authorised"
},
"nplurals=2; plural=(n != 1);");
