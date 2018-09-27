var cookie = 'STYXKEY_timezone';

function setCookie(name, value, days) {
	var expires = "";
	if (days) {
		var date = new Date();
		date.setTime(date.getTime() + (days*24*60*60*1000));
		expires = "; expires=" + date.toUTCString();
	}
	document.cookie = name + "=" + (value || "") + expires + "; path=/";
}

if (document.cookie.indexOf(cookie) < 0){
	var timezone = false;
	if (typeof Intl === "object") {
		timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
	}
	setCookie(cookie, timezone, 1);
	window.location.reload();
}