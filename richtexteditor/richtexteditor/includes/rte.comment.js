//var rteeditor;
function RichTextEditor_OnLoader(loader) {
	if (!loader)
		return;
	var config = loader._config;

}
function RichTextEditor_OnLoad(editor) {
	// add internal links
	editor.LoadLinks(function(group) {
		group.links.push({
			text : "Home",
			href : homeurl
		});
		var titles = pagetitles.split(";");
		var urls = pageurls.split(";");
		for ( var i = 0; i < titles.length - 1; i++) {
			group.links.push({
				text : titles[i],
				href : urls[i]
			});
		}
	});
	window.WP_RTEEDITOR = editor;

	// this is for comment
	var comment = document.getElementById("comment");
	if (!comment || typeof (comment) == "undefined")
		return;
	window.WP_RTEEDITOR.AttachEvent("TextChanged", RTE_Comment_ReadEditorText);
	var rtectl = document.getElementById("RTE_Comment_Container");
	var ie678 = /IE 6/.test(navigator.userAgent)
			|| /IE 7/.test(navigator.userAgent)
			|| /IE 8/.test(navigator.userAgent);
	if (ie678) {
		var pctl = comment.parentNode;
		pctl.appendChild(rtectl);
		rtectl.style.display = "";
	}

	RTE_AttachEditorEvent(rtectl);
}

function RTE_Comment_ReadEditorText() {
	if (!window.WP_RTEEDITOR)
		return;

	var comment = document.getElementById("comment");
	if (!comment || typeof (comment) == "undefined")
		return;
	comment.value = window.WP_RTEEDITOR.GetText();
}

function RTE_AttachEditorEvent(rtectl) {
	if (document.addEventListener) {
		rtectl.addEventListener("mouseover", rteeditor_document_onmouseover,
				false);
		rtectl.addEventListener("mouseout", rteeditor_document_onmouseout,
				false);
	} else {
		rtectl.attachEvent("onmouseover", rteeditor_document_onmouseover);
		rtectl.attachEvent("onmouseout", rteeditor_document_onmouseout);
	}
}

function rteeditor_document_onmouseover(e) {
	if (!window.WP_COMMENTLABEL)
		return;
	window.WP_COMMENTLABEL.style.display = "none";
}
function rteeditor_document_onmouseout(e) {
	if (!window.WP_COMMENTLABEL)
		return;
	window.WP_COMMENTLABEL.style.display = "";
}