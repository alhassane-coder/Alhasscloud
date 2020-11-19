OC.L10N.register(
    "limit_login_to_ip",
    {
    "Delete" : "Видалити",
    "Restrict login to IP addresses" : "Обмежити вхід для IP адрес",
    "Allows administrators to restrict logins to their instance to specific IP ranges." : "Дозволяє адміністраторам обмежувати вхід для певних IP адрес або діапазонів.",
    "This app allows administrators to restrict login to their\nNextcloud server to specific IP ranges. Note that existing sessions will be kept\nopen.\n\nThe allowed IP ranges can be administrated using the OCC command line interface\nor graphically using the admin settings. If you plan to use the OCC tool, the\nfollowing commands would be applicable.\n\nTo whitelist `127.0.0.0/24`:\n\n- `occ config:app:set limit_login_to_ip whitelisted.ranges --value 127.0.0.0/24`\n\nTo whitelist `127.0.0.0/24` and also `192.168.0.0/24`:\n\n- `occ config:app:set limit_login_to_ip whitelisted.ranges --value 127.0.0.0/24,192.168.0.0/24`" : "Цей застосунок дозволяє адміністраторам встановлювати обмеження для входу в Nextcloud з певних IP адрес або діапазонів. Майте на увазі, що вже відкриті сесії продовжать роботу.\nДозволені IP діапазони можуть бути налаштовані за допомогою OCC з командного рядка або через графічний інтерфейс у налаштуваннях адміністратора. Якщо ви плануєте користуватися інструментом OCC, знадобляться наступні команди:\nДля внесення `127.0.0.0/24` до білого списку:\n\n- `occ config:app:set limit_login_to_ip whitelisted.ranges --value 127.0.0.0/24`\n\nДля внесення `127.0.0.0/24` разом з `192.168.0.0/24` до білого списку:\n\n- `occ config:app:set limit_login_to_ip whitelisted.ranges --value 127.0.0.0/24,192.168.0.0/24`",
    "Restrict login to IP ranges" : "Обмежити вхід для IP діапазонів",
    "By default, %s permits logging-in from any IP address. To limit logins to specific IP ranges, you can specify those below." : "Стандартно %s дозволяє вхід з будь-якої IP адреси. Для обмеження входу з певних IP діапазонів ви можете використовувати наступне.",
    "Add" : "Додати",
    "Not authorized" : "Не авторизовано"
},
"nplurals=4; plural=(n % 1 == 0 && n % 10 == 1 && n % 100 != 11 ? 0 : n % 1 == 0 && n % 10 >= 2 && n % 10 <= 4 && (n % 100 < 12 || n % 100 > 14) ? 1 : n % 1 == 0 && (n % 10 ==0 || (n % 10 >=5 && n % 10 <=9) || (n % 100 >=11 && n % 100 <=14 )) ? 2: 3);");
