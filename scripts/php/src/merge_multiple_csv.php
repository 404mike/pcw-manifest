<?php
namespace llgc\src;

require dirname(__FILE__) . '/../vendor/autoload.php';

use \llgc\src\csvTemplate;

class Merge_multiple_csv {


  /**
   * Directory path
   *
   * @var string
   */
  private $path;


  /**
   * Array containing all spreadsheet information
   *
   * @var array
   */
  private $contents = [];



  public function __construct()
  {
    $currentDir = dirname(__FILE__);

    $this->path = str_replace('scripts/php/src', '', $currentDir);

    // get all the llgc_* spreadsheets
    $this->get_all_llgc_dir();

    // create master csv file
    $this->create_master_csv();
  }


  /**
   * Loop through all the llgc_* directories
   * move all the images to single web_ready directory
   */
  private function get_all_llgc_dir()
  {
    // scan directory
    $files = array_diff(scandir($this->path), array('.', '..'));

    // loop through all the files
    foreach($files as $key => $value) {

      // check to see if the directory has a llgc_ name
      if(preg_match("/llgc_(\d+)/", $value, $output_array)) {

        // get the contents of the csv in the current loop
        $this->get_single_csv($value);

        // move the images in the web_ready to the master web_ready
        $this->move_image($value);
      }
    }
  }


  /**
   * Get the contents of the csv
   *
   * @param $csv string
   */
  private function get_single_csv($csv)
  {
    // get the path path to the csv files
    $csvFile = $this->path . $csv . '/' . $csv . '.csv';

    // convert the csv files to an arryay
    if(file_exists( $csvFile )) {
      $csv = array_map('str_getcsv', file( $csvFile ));

      // remove unwanted fields
      unset($csv[0]);
      unset($csv[1]);
      unset($csv[2]);
      unset($csv[3]);

      // loop through each item in the array and add to
      // contents array
      foreach($csv as $key => $value) {
        $this->contents[] = $value;
      }
    }else{
      echo "Unable to open $csvFile\n";
    }

  }


  /**
   * Create master csv file
   * combines all the data in contents array
   *
   */
  private function create_master_csv()
  {
    // Get CSV header details
    $listHeader = new csvTemplate();

    $list = $listHeader->csvHeader();
    
    // get the data from contents array and format
    $data = $this->addMultipartToCsv();

    // loop through all the items and add them to array
    foreach($data as $d) {
      array_push($list, $d);
    }
        
    // write the data to the csv file      
    $fp = fopen($this->path . '/master/merged.csv', 'w');
    
    echo "\nCreating CSV file\n";

    foreach ($list as $fields) {
      fputcsv($fp, $fields , ',' , '"');
    }
    
    fclose($fp);
  }


  /**
   * Create an array of all the items from the contents array
   * Returns formatted array to insert into the csv
   *
   * @return array
   */
  private function addMultipartToCsv()
  {

    $list = [];

    foreach ($this->contents as $item => $value) {
    
      array_push($list, array(
        $value[0], // Image Identifier
        $value[1], // Parent ID*
        $value[2], // Page Order*
        $value[3], // Image/File Name
        str_replace([']','[ '], '', $value[4]), // Title EN
        str_replace([']','[ '], '', $value[5]), // Title CY
        $value[6], // Description EN
        $value[7], // Description CY
        $value[8], // Item type
        $value[9], // Tags EN
        $value[10], // Tags CY
        $value[11], // Date
        $value[12], // Owner
        'Harries, D. C., 1865-1940', // Creator
        $value[14], // Website en
        $value[15], // Website cy
        $value[16], // What facet
        $value[17], // When facet
        $value[18], // Location (lat, lon)
        $value[19], // Location description en
        $value[20], // Location description cy
        $value[21], // Right Type 1
        $value[22], // Right Holder 1 EN
        $value[23], // Right Holder 1 CY
        $value[24], // Begin Date 1
        $value[25], // Right Type 2
        $value[26], // Right Holder 2 EN
        $value[27], // Right Holder 2 CY
        $value[28], // Begin Date 2
        $value[29], // Right Type 3
        $value[30], // Right Holder 3 EN
        $value[31], // Right Holder 3 CY
        $value[32], // Begin Date 3
        $value[33], // Addional rights        
      ));
    }

    return $list;

  }

  /**
   * Copy the images from the llgc_ web_ready directory to the 
   * master web_ready directory
   *
   * @param $csv string - csv file name
   * @return 
   */
  private function move_image($csv)
  {
    $path = $this->path . $csv . '/temp/';
    
    $destination = $this->path . '/master/web_ready/';

    // echo "Loading $path and moving to $destination\n";

    $files = array_diff(scandir($path), array('.', '..'));

    // loop through all the files
    foreach($files as $key => $value) {
      echo "Moving $value\n";
      if(!copy($path . $value, $destination . $value)) {
        echo "Could not copy $value\n";
      }
    }

  }


}

(new Merge_multiple_csv());