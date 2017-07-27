<?php
// bootstrap.php
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Yaml\Yaml;

require_once "vendor/autoload.php";

function GetEntityManager() {
  // Create a simple "default" Doctrine ORM configuration for Annotations
  $isDevMode = true;
  //$config = Setup::createAnnotationMetadataConfiguration(array(__DIR__."/src"), $isDevMode);
  // or if you prefer yaml or XML
  //$config = Setup::createXMLMetadataConfiguration(array(__DIR__."/config/xml"), $isDevMode);
  $config = Setup::createYAMLMetadataConfiguration(array(__DIR__."/src/config"), $isDevMode);

  // database configuration parameters
  $fn = __DIR__.'/connection.yml';
  if(!file_exists($fn)) {
    throw new \Exception("Missing file: $fn");
  }
  $conn = Yaml::parse(file_get_contents($fn));

  // obtaining the entity manager
  $entityManager = EntityManager::create($conn, $config);
  return $entityManager;
}
