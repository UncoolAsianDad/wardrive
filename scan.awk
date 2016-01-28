# sudo iw wlp3s0 scan | sed -e 's#(on wlan# (on wlan#g' | awk -f scan.awk
#sudo iw wlp3s0 scan |sed -e 's#(on w# (w#g' | gawk -f scan.awk > vandrico.json
function removeSSID(s) { sub(/SSID: /, "", s); return s }
function ltrim(s) { sub(/^[ \t\r\n]+/, "", s); return s }
function rtrim(s) { sub(/[ \t\r\n]+$/, "", s); return s }
function trim(s)  { return rtrim(ltrim(s)); }

BEGIN {
}

/^BSS/ {
    MAC = trim($2)
}
/freq: /{
    wifi[MAC]["freq"] = trim($2);
}
/SSID:/ {
    wifi[MAC]["SSID"] = removeSSID(trim($0));
}
/signal/ {
    wifi[MAC]["signal"] = trim($2);
}
/DS Parameter set: channel/ {
    wifi[MAC]["channel"] = trim($5);
}

END {
#    printf "MAC,SSID,freq,channel,signal"
    for (w in wifi) {
        printf "%s,%s,%d,%d,%d\n", w, wifi[w]["SSID"], wifi[w]["freq"], wifi[w]["channel"],wifi[w]["signal"]
    }    
}

JSONEND {

    printf "["
    for (w in wifi) {
        printf "%s\n{\"mac\":\"%s\", \"ssid\":\"%s\", \"channel\":\"%s\", \"signal\":\"%s\"}", comma, w, wifi[w]["SSID"], wifi[w]["channel"],wifi[w]["signal"]
        comma = ", "
    }
    printf "]"
}
