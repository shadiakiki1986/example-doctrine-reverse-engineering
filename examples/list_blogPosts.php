<?php
// list_blogPosts.php <name>
require_once __DIR__."/../cli-config.php";

$blogPostRepository = $entityManager->getRepository('\MfBfDriver\ORM\Core\BlogPost');
$blogPosts = $blogPostRepository->findAll();

if(count($blogPosts)==0) {
  echo "None".PHP_EOL;
}

$blogPosts = array_slice($blogPosts, 0, 10);

function myPrint($blogPost) {
  echo sprintf("%s: %s\n", $blogPost->getId(), $blogPost->getName());
}

foreach ($blogPosts as $blogPost) {
      myPrint($blogPost);
}
