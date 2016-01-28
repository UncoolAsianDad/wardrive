SELECT
	substr(mac,1,15) AS mac_prefix,	
	location,
	avg(`signal`) AS avgStr,
	substr(freq,1,2) AS freq,	
	ssid, count(*) AS count

FROM aps
WHERE
	freq < 5000 AND
	ssid like 'Wavefront - %'
GROUP BY
	location,
	substr(freq,1,1),
	substr(mac,1,15)
ORDER BY
	mac_prefix,freq, avgStr desc,location;
