OC.L10N.register(
    "limit_login_to_ip",
    {
    "Delete" : "Διαγραφή",
    "Restrict login to IP addresses" : "Περιορισμένη σύνδεση σε διευθύνσεις IP",
    "Allows administrators to restrict logins to their instance to specific IP ranges." : "Επιτρέπει στους διαχειριστές να μην επιτρέπουν την είσοδο απο συγκεκριμένες διευθύνσεις IPs.",
    "This app allows administrators to restrict login to their\nNextcloud server to specific IP ranges. Note that existing sessions will be kept\nopen.\n\nThe allowed IP ranges can be administrated using the OCC command line interface\nor graphically using the admin settings. If you plan to use the OCC tool, the\nfollowing commands would be applicable.\n\nTo whitelist `127.0.0.0/24`:\n\n- `occ config:app:set limit_login_to_ip whitelisted.ranges --value 127.0.0.0/24`\n\nTo whitelist `127.0.0.0/24` and also `192.168.0.0/24`:\n\n- `occ config:app:set limit_login_to_ip whitelisted.ranges --value 127.0.0.0/24,192.168.0.0/24`" : "Επιτρέπει στους διαχειριστές να μην επιτρέπουν την είσοδο\nστον διακομιστή τους Nextcoud απο συγκεκριμένες διευθύνσεις IPs. Σημ: Οι τρέχουσες συνδέσεις θα διατηρηθούν\nανοικτές.\n\nΟι επιτρεπόμενες διευθύνσεις IPs μπορούν να διαχειριστούν με την γραμμή εντολών του OCC.\nή με γραφικά από τις ρυθμίσεις διαχειριστή. Αν χρησιμοποιήσετε το εργαλείο OCC, οι\nπαρακάτω εντολές θα σας ήταν χρήσιμες.\n\nΓια την επιτρεπόμενη λίστα `127.0.0.0/24`:\n\n- `occ config:app:set limit_login_to_ip whitelisted.ranges --value 127.0.0.0/24`\n\nΓια την επιτρεπόμενη λίστα `127.0.0.0/24` και `192.168.0.0/24`:\n\n- `occ config:app:set limit_login_to_ip whitelisted.ranges --value 127.0.0.0/24,192.168.0.0/24`",
    "Restrict login to IP ranges" : "Περιορισμένη σύνδεση σε διευθύνσεις IP",
    "By default, %s permits logging-in from any IP address. To limit logins to specific IP ranges, you can specify those below." : "Από προεπιλογή το %s επιτρέπει την είσοδο από οποιαδήποτε διεύθυνση IP. Για τον περιορισμό εισόδου από συγκεκριμένες διευθύνσεις IP, μπορείτε να τις ορίσετε παρακάτω.",
    "Add" : "Προσθήκη",
    "Not authorized" : "Μη έγκυρος"
},
"nplurals=2; plural=(n != 1);");
