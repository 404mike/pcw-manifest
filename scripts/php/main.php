<?php
namespace llgc;

require __DIR__ . '/vendor/autoload.php';

use llgc\src;

class Main {

  /**
   * Type of csv to generate - single|multipart
   * 
   * @var string
   */
  private $type = '';


  /**
   * Manifest ID
   * 
   * @var string
   */ 
  private $manifestId = '';


  /**
   * Formatted manifest data
   * 
   * @var  array
   */
  private $csvData = [];


  public function __construct()
  {
    // Get command line arguments
    global $argc, $argv;

    // set type of csv to generate
    $this->type       = $argv[2];

    // set manifest id
    $this->manifestId = $argv[3];

    // type of command - images|csv
    $command = trim($argv[1]);

    // get the manifest data
    $data = $this->getManifestData();

    // Run command to download images or generate csv
    if($command == 'images') {

      // download the images
      $this->getImages($data);

    } else {

      // create csv File
      $this->makeCsv($data);

    }

  }


  /**
   * Get the manifest file from dams
   * @return array
   */
  private function getManifestData()
  {

    $manifest = new src\get_manifest();

    return $manifest->get( $this->manifestId );

  }


  /**
   * Create CSV file
   * @return null
   */
  private function makeCsv( $data )
  {

    if($this->type == 'single') {

      $this->generateSingleItemCsv( $data );

    } else {

      $this->generateMultiPartCsv( $data );

    }

  }  


  /**
   * Generate data for single item csv file
   * @param $data
   * @return null
   */
  private function generateSingleItemCsv( $data )
  {

    $csv = new src\generate_csv();

    $this->csvData = $csv->generateSingleItemData( $data );

  }


  /**
   * Generate data for multipart csv file
   * @param $data
   * @return null
   */
  private function generateMultiPartCsv( $data )
  {

    $csv = new src\generate_csv();

    $this->csvData = $csv->generateMulitpartData( $data );

  }


  /**
   * Download local copies of the images
   * Once all images are downloaded run.sh
   * will transform the images and place them 
   * in web_ready
   * @param array $data - manifest file
   * @return null
   */
  private function getImages( $data )
  {

    $images = new src\download_images();

    $images->downloadLocalCopyOfImage( $data );

  }

}

(new Main());