OC.L10N.register(
    "limit_login_to_ip",
    {
    "Delete" : "Обриши",
    "Restrict login to IP addresses" : "Ограничи пријављивање са IP адреса",
    "Allows administrators to restrict logins to their instance to specific IP ranges." : "Омогућава администраторима да ограниче приступ својим инстанцама на одређене IP опсеге.",
    "This app allows administrators to restrict login to their\nNextcloud server to specific IP ranges. Note that existing sessions will be kept\nopen.\n\nThe allowed IP ranges can be administrated using the OCC command line interface\nor graphically using the admin settings. If you plan to use the OCC tool, the\nfollowing commands would be applicable.\n\nTo whitelist `127.0.0.0/24`:\n\n- `occ config:app:set limit_login_to_ip whitelisted.ranges --value 127.0.0.0/24`\n\nTo whitelist `127.0.0.0/24` and also `192.168.0.0/24`:\n\n- `occ config:app:set limit_login_to_ip whitelisted.ranges --value 127.0.0.0/24,192.168.0.0/24`" : "Ова апликација омогућава администраторима да ограниче приступ својим Некстклауд инстанцама на одређене IP опсеге. Постојеће сесије ће остати отворене.\n\nДозвољеним IP опсезима се управља са OCC интерфејсом из командне линије или графички из администраторских поставки. Ако планирате да користите OCC алат, следеће команде би имале смисла.\n\nДа омогућите `127.0.0.0/24`:\n\n- `occ config:app:set limit_login_to_ip whitelisted.ranges --value 127.0.0.0/24`\n\nДа омогућите и `127.0.0.0/24` као и `192.168.0.0/24`:\n\n- `occ config:app:set limit_login_to_ip whitelisted.ranges --value 127.0.0.0/24,192.168.0.0/24`",
    "Restrict login to IP ranges" : "Ограничи пријављивање са опсега IP адреса",
    "By default, %s permits logging-in from any IP address. To limit logins to specific IP ranges, you can specify those below." : "Подразумевано, %s дозвољава пријављивање са било које IP адресе. Испод можете да ограничите пријаве са одређених IP адреса.",
    "Add" : "Додај",
    "Not authorized" : "Немате овлашћења"
},
"nplurals=3; plural=(n%10==1 && n%100!=11 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2);");
