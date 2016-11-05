jQuery(document).ready(function () {
	var comment = document.getElementById("comment");
	if (comment && typeof (comment) != "undefined") {
		comment.style.display = "none";

		var rtectl = document.getElementById("RTE_Comment_Container");
		var ie678 = /IE 6/.test(navigator.userAgent) || /IE 7/.test(navigator.userAgent) || /IE 8/.test(navigator.userAgent);
		if (!ie678) {
			var pctl = comment.parentNode;
			pctl.appendChild(rtectl);
			rtectl.style.display = "";
		}
		var label = pctl.firstChild;
		if (label.tagName.toLowerCase() == "label") {
			//label.style.color = "red";
			window.WP_COMMENTLABEL = label;
		}
	}
});