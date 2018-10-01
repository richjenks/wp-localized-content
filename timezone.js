// If cookie exists then do nothing — name provided by `localized-content.php`
if (("; " + document.cookie).indexOf("; " + localized_content_cookie + "=") === -1){

	// Convenience function for setting cookies
	function setCookie(name, value, days) {
		var expires = "";
		if (days) {
			var date = new Date();
			date.setTime(date.getTime() + (days*24*60*60*1000));
			expires = "; expires=" + date.toUTCString();
		}
		document.cookie = name + "=" + (value || "") + expires + "; path=/";
	}

	// Get and save timezone then reload
	var timezone = "";
	if (typeof Intl === "object") {
		timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
	}
	setCookie(localized_content_cookie, timezone, 1);
	window.location.reload();

}