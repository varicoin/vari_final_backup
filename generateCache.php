<?php
include("includes/post.class.php");
$post = new post();
echo json_encode($post->getNewRecords());