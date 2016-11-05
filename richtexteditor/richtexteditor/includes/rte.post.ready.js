jQuery(document).ready(function () {
	var comment = document.getElementById("content");
	if (!comment || typeof(comment) == "undefined")
		comment = document.getElementById("replycontent");
	if (comment && typeof (comment) != "undefined") {
		comment.style.display = "none";

		var ie678 = /IE 6/.test(navigator.userAgent) || /IE 7/.test(navigator.userAgent) || /IE 8/.test(navigator.userAgent);
		if (!ie678) {
			var pctl = comment.parentNode;
			var rtectl = document.getElementById("RTE_Post_Container");
			pctl.appendChild(rtectl);
			rtectl.style.display = "";
		}
	}

	var ed_reply_qtags = document.getElementById("ed_reply_qtags");
	if (ed_reply_qtags && typeof (ed_reply_qtags) != "undefined") {
		ed_reply_qtags.parentNode.removeChild(ed_reply_qtags);
	}
	var replycontainer = document.getElementById("replycontainer");
	if (replycontainer)
	{
		replycontainer.style.width = "100%";
		replycontainer.style.height = "100%";
		setInterval(function () {
			if (!replycontainer.style.height || replycontainer.style.height != "100%")
				replycontainer.style.height = "100%";
		}, 500);
	}

	var ed_toolbar = document.getElementById("ed_toolbar");
	if (ed_toolbar && typeof (ed_toolbar) != "undefined")
		ed_toolbar.style.display = "none";
});