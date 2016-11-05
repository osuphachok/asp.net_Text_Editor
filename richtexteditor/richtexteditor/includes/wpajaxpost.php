<?php require_once "../richtexteditor/include_rte.php" ?>
<?php
    $rte=new RichTextEditor();
	if($_GET["name"]!=null)
		$rte->Name=$_GET["name"];
	else		
		$rte->Name="Editor1";
	$rte->SetSecurity("*", "*", "StoragePath", "~/wp-content/uploads");
	if($_GET["imagepath"]!=null)
	{
		$rte->SetSecurity("Gallery", "*", "StoragePath", urldecode($_GET["imagepath"]));
		$rte->SetSecurity("Image", "*", "StoragePath", urldecode($_GET["imagepath"]));
	}
    if($_GET["videopath"]!=null)
	{
		$rte->SetSecurity("Video", "*", "StoragePath", urldecode($_GET["videopath"]));
	}
    if($_GET["docpath"]!=null)
	{
		$rte->SetSecurity("Document", "*", "StoragePath", urldecode($_GET["docpath"]));
	}
    if($_GET["temppath"]!=null)
	{
		$rte->SetSecurity("Template", "*", "StoragePath", urldecode($_GET["temppath"]));
	}
    $rte->MvcInit();
?>