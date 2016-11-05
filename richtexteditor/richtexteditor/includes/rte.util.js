jQuery(document).ready(function () {
	var comment = document.getElementById("respond");
	var replyrow = document.getElementById("replyrow");
	if (comment && typeof (comment) != "undefined")
	{
		window.RTECONTAINER = comment;
		window.RTECHECKERTIMER = setInterval(RTE_CheckParent, 100);
	}
	else if (replyrow && typeof (replyrow) != "undefined")
	{
		window.RTECONTAINER = replyrow;
		window.RTECHECKERTIMER = setInterval(RTE_CheckParent, 100);
	}	
});

function RTE_CheckParent()
{
	/*for 3.0-3.2*/
	var ed_reply_qtags = document.getElementById("ed_reply_qtags");
	if (ed_reply_qtags && typeof (ed_reply_qtags) != "undefined")
		ed_reply_qtags.style.display="none";

	var post_status_info = document.getElementById("post-status-info");
	if (post_status_info && typeof (post_status_info) != "undefined")
		post_status_info.style.display = "none";

	var editor_toolbar = document.getElementById("editor-toolbar");
	if (editor_toolbar && typeof (editor_toolbar) != "undefined")
		editor_toolbar.style.display = "none";

	var quicktags = document.getElementById("quicktags");
	if (quicktags && typeof (quicktags) != "undefined")
		quicktags.style.display = "none";

	var content_parent = document.getElementById("content_parent");
	if (content_parent && typeof (content_parent) != "undefined")
		content_parent.style.display = "none";

	var editorcontainer = document.getElementById("editorcontainer");
	if (editorcontainer && typeof (editorcontainer) != "undefined")
		editorcontainer.style.border = "none";

	/*check reply row*/
	var replyrow = document.getElementById("replyrow");
	if (replyrow && !document.getElementById("content"))
	{
		var prow = replyrow.parentNode;
		var trs = prow.getElementsByTagName("tr");
		var ci = -1;
		for (var i = 0; i < trs.length; i++)
		{
			if (!trs[i].id)
				continue;
			if (trs[i].id == replyrow.id)
			{
				ci = i;
				break;
			}
		}
		if (ci == -1)
			return;
		if (!window.WP_REPLYROWINDEX) {
			window.WP_REPLYROWINDEX = ci;
			window.WP_RTEEDITOR.DetachFrame();
			window.WP_RTEEDITOR.AttachFrame();
			window.WP_RTEEDITOR.SetText("");
			return;
		}
		if (ci != window.WP_REPLYROWINDEX)
		{
			window.WP_REPLYROWINDEX = ci;
			window.WP_RTEEDITOR.DetachFrame();
			window.WP_RTEEDITOR.AttachFrame();
			window.WP_RTEEDITOR.SetText("");
		}
		return;
	}

	/*end for 3.0-3.2*/

	if (!window.WP_RTEEDITOR)
		return;
	if (!window.RTECONTAINER)
		return;
	if (!window.RTEPARENT)
	{
		window.RTEPARENT = window.RTECONTAINER.parentNode;
		return;
	}
	var curparent = window.RTECONTAINER.parentNode;
	//document.title = [curparent, window.RTEPARENT];
	if (window.RTEPARENT == curparent)
		return;
	window.RTEPARENT = curparent;
	window.WP_RTEEDITOR.DetachFrame();
	//rteeditor._config.skin_div_parent.insertBefore(div, rteeditor._config.skin_div_holder);
	//rteeditor._config.skin_div_parent.removeChild(rteeditor._config.skin_div_holder);
	//rteeditor._config.skin_div_holder = div;
	window.WP_RTEEDITOR.AttachFrame();
	window.WP_RTEEDITOR.SetText("");
}