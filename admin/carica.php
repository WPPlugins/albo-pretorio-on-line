<?php
$ftmp = $_FILES['image']['tmp_name'];
$oname = $_FILES['image']['name'];
$fname = 'upload/'.$_FILES['image']['name'];
if(move_uploaded_file($ftmp, $fname)){
echo "<script>";
echo "var par = window.parent.document;";
echo "var images = par.getElementById('images'); ";
echo "images.innerHTML = 'file $oname caricato';";
echo "</script>";
}
?>
<script language="javascript">
function upload(){
// hide old iframe
var par = window.parent.document;
// add image progress
var images = par.getElementById('images');
var new_div = par.createElement('div');
var new_img = par.createElement('img');
new_img.src = 'indicator.gif';
new_img.className = 'load';
new_div.appendChild(new_img);
images.appendChild(new_div);
// send
document.iform.submit();
}
</script>
<form name="iform" action="" method="post" enctype="multipart/form-data">
<input id="file" type="file" name="image" onchange="upload()" />
</form>