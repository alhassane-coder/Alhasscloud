OC.L10N.register(
    "bruteforcesettings",
    {
    "Brute-force settings" : "Postavke zaštite od dešifriranja različitim ključevima",
    "Whitelist IPs" : "Dopuštene IP adrese",
    "Brute Force Protection is meant to protect Nextcloud servers from attempts to\nguess user passwords in various ways. Besides the obvious \"*let's try a big\nlist of commonly used passwords*\" attack, it also makes it harder to use\nslightly more sophisticated attacks via the reset password form or trying to\nfind app password tokens.\n\nIf triggered, brute force protection makes requests coming from an IP on a\nbruteforce protected controller with the same API slower for a 24 hour period.\n\nWith this app, the admin can exempt an IP address or range from this\nprotection which can be useful for testing purposes or when there are false\npositives due to a lot of users on one IP address." : "Zaštita od dešifriranja različitim ključevima (brute force protection) štiti Nextcloudove poslužitelje od pokušaja\notkrivanja korisničkih zaporki na različite načine. Osim uobičajenog napada po načelu „*pokušajmo prvo s velikim\npopisom najčešće korištenih zaporki*”, također otežava korištenje\nsloženijih napada putem obrasca za resetiranje zaporke ili pokušaja\notkrivanja tokena zaporke aplikacije.\n\nAko se aktivira, zaštita od dešifriranja različitim ključevima usporava zahtjeve koji dolaze s određene IP adrese\nna zaštićeni kontroler s istim API-jem tijekom razdoblja od 24 sata.\n\nOva aplikacija omogućuje administratoru izuzimanje IP adrese ili raspona IP adresa\niz ove zaštite što može biti korisno u svrhu testiranja ili kada postoje lažni\npozitivni rezultati zbog velikog broja korisnika na jednoj IP adresi.",
    "Brute-force IP whitelist" : "Dopuštene IP adrese u pogledu zaštite od dešifriranja različitim ključevima",
    "To whitelist IP ranges from the brute-force protection specify them below. Note that any whitelisted IP can perform authentication attempts without any throttling. For security reasons, it is recommended to whitelist as few hosts as possible or ideally even none at all." : "Kako biste dopustili određeni raspon IP adresa u pogledu zaštite od dešifriranja različitim ključevima, navedite ga u nastavku. Imajte na umu da bilo koja dopuštena IP adresa može provoditi autentifikaciju bez ikakvih ograničenja. Iz sigurnosnih se razloga preporučuje da dopustite što je moguće manje računala ili, još bolje, nijedno računalo.",
    "Add new whitelist" : "Dodaj novi popis dopuštenih IP adresa",
    "Add" : "Dodaj",
    "Delete" : "Izbriši"
},
"nplurals=3; plural=n%10==1 && n%100!=11 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2;");