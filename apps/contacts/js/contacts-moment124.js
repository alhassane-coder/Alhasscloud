(window.webpackJsonp=window.webpackJsonp||[]).push([[15],{399:function(r,u,e){!function(r){"use strict";
//! moment.js locale configuration
function u(r){return r%100==11||r%10!=1}function e(r,e,n,a){var s=r+" ";switch(n){case"s":return e||a?"nokkrar sekúndur":"nokkrum sekúndum";case"ss":return u(r)?s+(e||a?"sekúndur":"sekúndum"):s+"sekúnda";case"m":return e?"mínúta":"mínútu";case"mm":return u(r)?s+(e||a?"mínútur":"mínútum"):e?s+"mínúta":s+"mínútu";case"hh":return u(r)?s+(e||a?"klukkustundir":"klukkustundum"):s+"klukkustund";case"d":return e?"dagur":a?"dag":"degi";case"dd":return u(r)?e?s+"dagar":s+(a?"daga":"dögum"):e?s+"dagur":s+(a?"dag":"degi");case"M":return e?"mánuður":a?"mánuð":"mánuði";case"MM":return u(r)?e?s+"mánuðir":s+(a?"mánuði":"mánuðum"):e?s+"mánuður":s+(a?"mánuð":"mánuði");case"y":return e||a?"ár":"ári";case"yy":return u(r)?s+(e||a?"ár":"árum"):s+(e||a?"ár":"ári")}}r.defineLocale("is",{months:"janúar_febrúar_mars_apríl_maí_júní_júlí_ágúst_september_október_nóvember_desember".split("_"),monthsShort:"jan_feb_mar_apr_maí_jún_júl_ágú_sep_okt_nóv_des".split("_"),weekdays:"sunnudagur_mánudagur_þriðjudagur_miðvikudagur_fimmtudagur_föstudagur_laugardagur".split("_"),weekdaysShort:"sun_mán_þri_mið_fim_fös_lau".split("_"),weekdaysMin:"Su_Má_Þr_Mi_Fi_Fö_La".split("_"),longDateFormat:{LT:"H:mm",LTS:"H:mm:ss",L:"DD.MM.YYYY",LL:"D. MMMM YYYY",LLL:"D. MMMM YYYY [kl.] H:mm",LLLL:"dddd, D. MMMM YYYY [kl.] H:mm"},calendar:{sameDay:"[í dag kl.] LT",nextDay:"[á morgun kl.] LT",nextWeek:"dddd [kl.] LT",lastDay:"[í gær kl.] LT",lastWeek:"[síðasta] dddd [kl.] LT",sameElse:"L"},relativeTime:{future:"eftir %s",past:"fyrir %s síðan",s:e,ss:e,m:e,mm:e,h:"klukkustund",hh:e,d:e,dd:e,M:e,MM:e,y:e,yy:e},dayOfMonthOrdinalParse:/\d{1,2}\./,ordinal:"%d.",week:{dow:1,doy:4}})}(e(68))}}]);
//# sourceMappingURL=contacts-moment124.js.map?v=7be7f53350595d1fe69f