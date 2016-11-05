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

	// this is for content
	var comment = document.getElementById("content");
	if (!comment || typeof (comment) == "undefined")
		comment = document.getElementById("replycontent");
	if (!comment || typeof (comment) == "undefined")
		return;
	if (comment.value)
		window.WP_RTEEDITOR.SetText(comment.value);
	window.WP_RTEEDITOR.AttachEvent("TextChanged", RTE_Comment_ReadEditorText);
	var ie678 = /IE 6/.test(navigator.userAgent)
			|| /IE 7/.test(navigator.userAgent)
			|| /IE 8/.test(navigator.userAgent);
	if (ie678) {
		var pctl = comment.parentNode;
		var rtectl = document.getElementById("RTE_Post_Container");
		pctl.appendChild(rtectl);
		rtectl.style.display = "";
	}

}

function RTE_Comment_ReadEditorText() {
	if (!window.WP_RTEEDITOR)
		return;

	var comment = document.getElementById("content");
	if (!comment || typeof (comment) == "undefined")
		comment = document.getElementById("replycontent");
	if (!comment || typeof (comment) == "undefined")
		return;
	comment.value = window.WP_RTEEDITOR.GetText();
	comment.innerHTML = window.WP_RTEEDITOR.GetText();
	document.title = [ comment.id, comment.value, comment.innerHTML ];
}
