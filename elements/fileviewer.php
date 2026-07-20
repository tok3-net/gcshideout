<?php
$fileviewform = "
Enter the relative path from the DiscoBoard root to view:<BR>
".inputText("filename", $filename, 50)."
".inputSubmit("View");
?>