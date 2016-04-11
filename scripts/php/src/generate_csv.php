<?php
namespace llgc\src;

use \llgc\src\csvTemplate;

class Generate_csv {


  /**
   * Array containing all the metadata for the manifest
   * @var array
   */
  private $manifestData = [];


  /**
   * Generate a single item csv
   * @param array $data
   * @return null
   */
  public function generateSingleItemData( $data )
  {

    $base = $this->generateBaseMetaData( $data );

    $items = [];

    // get array of images
    $images = $data['sequences'][0]['canvases'];

    $id     = $this->getIds( $images[0]['@id'] , 'base' );

    // loop through all the images
    foreach($images as $img) {

      $imageid  = $this->getIds($img['@id'] , 'single');
      $title    = $img['label'];

      $items[] = [
        'id'    => 'llgc_' . $id . '_' . $imageid,
        'image' => $imageid . '.jpg',
        'title' => $title
      ];

    }

    $this->createCsv($base , $items , $id);

  }


  /**
   * Generate a multipart csv
   * @param array $data
   * @return null
   */
  public function generateMulitpartData( $data )
  {
        
    // get metadata
    $base = $this->generateBaseMetaData( $data );

    // get array of images
    $images = $data['sequences'][0]['canvases'];

    $id     = $this->getIds( $images[0]['@id'] , 'base' );
    $itemid = $this->getIds( $images[0]['@id'] , 'single' );

    $items[$id] = [
      'id'        => 'llgc_' . $id . '_' . $itemid,
      'title'     => $images[0]['label'],
      'image'     => $itemid . '.jpg',
      'children'  => []
    ];

    // loop through all the images
    foreach($images as $img) {

      $imageid  = $this->getIds($img['@id'] , 'single');
      $title    = $img['label'];

      $items[$id]['children'][] = [
        'id'    => 'llgc_' . $id . '_' . $imageid,
        'image' => $imageid . '.jpg',
        'title' => $title
      ];

    }

    $this->createCsv($base , $items , $id);

  }


  /**
   * Get manifest or item id from the manifest
   * @param string $idString
   * @param string $type - base|single
   * @return string
   */
  private function getIds($idString , $type)
  {

    // get manifest ID and item id
    $regex = "/http\:\/\/dams\.llgc\.org\.uk\/iiif\/2\.0\/(.*?)\/canvas\/(.*?)\.json/";

    if($type == 'base') {
      return preg_replace($regex, "$1", $idString);
    }else{
      return preg_replace($regex, "$2", $idString);
    }    

  }


  /**
   * Get the base metadata for the manifest
   * @param array $data
   * @return array
   */
  private function generateBaseMetaData( $data ) 
  {
    $dataArr = $data['metadata'];

    $arr = [
      'title'       => $dataArr[0]['value'],
      'author'      => $dataArr[1]['value'],
      'date'        => $dataArr[2]['value'],
      'description' => $dataArr[3]['value'],
      'url'         => $this->cleanURI( $dataArr[4]['value'] ),
      'reference'   => $dataArr[5]['value'],
      'license_en'  => $dataArr[6]['value'][0]['@value'],
      'license_cy'  => $dataArr[6]['value'][1]['@value'],

    ];

    return $arr;

  }


  /**
   * Cleam the HREF for the handle
   * @param string $url
   * @return string
   */
  private function cleanURI($url)
  {
    // clean handle if it contains HTML
    if(preg_match('/href/', $url)) {
      $url = preg_replace("/<a href=\"(.*?)\">(.*?)<\/a>/", "$1", $url);
    }

    return $url;
  }


  /**
   * Format the date facet
   * @param string $date
   * @return string
   */
  private function formatDateFacet( $date )
  {
    $fullDate = explode('-', $date);
    $dateFacet = substr_replace($fullDate[0] , '0' , -1 , 4);  

    $dateFacetArr = [
      '17' => '1800',
      '18' => '1810',
      '19' => '1820',
      '20' => '1830',
      '21' => '1840',
      '22' => '1850',
      '23' => '1860',
      '24' => '1870',
      '25' => '1880',
      '26' => '1890',
      '28' => '1900',
      '29' => '1910',
      '30' => '1920',
      '31' => '1930',
      '32' => '1940',
      '33' => '1950',
      '34' => '1960',
      '35' => '1970',
      '36' => '1980',
      '37' => '1990',
      '39' => '2000',
      '40' => '2010'
    ];

    $dateFacetFinal = array_search($dateFacet , $dateFacetArr);

    return $dateFacetFinal;
  }


  /**
   * Create CSV file
   * @param array $base
   * @param array $items
   * @param string $title
   * @return null
   */
  public function createCsv($base , $items , $title)
  {

    // Get CSV header details
    $listHeader = new csvTemplate();

    $list = $listHeader->csvHeader();
    
    if(count($items) == 1) {
      $data = $this->addMultipartToCsv($base, $items);
    }else{
      $data = $this->addSingleItemsToCsv($base, $items);
    }

    // loop through all the items and add them to array
    foreach($data as $d) {
      array_push($list, $d);
    }
        
    // write the data to the csv file      
    $fp = fopen('llgc_' . $title . '/llgc_ '. $title . '.csv', 'w');
    
    echo "\nCreating CSV file\n";

    foreach ($list as $fields) {
      fputcsv($fp, $fields , ',' , '"');
    }
    
    fclose($fp);
    
  }


  /**
   * [addSingleItemsToCsv description]
   * @param [type] $base  [description]
   * @param [type] $items [description]
   */
  private function addSingleItemsToCsv( $base, $items )
  {

    $list = [];

    foreach ($items as $item => $value) {

      array_push($list, array(
        $value['id'], // Image Identifier
        '', // Parent ID*
        '', // Page Order*
        $value['image'], // Image/File Name
        $value['title'], // Title EN
        $value['title'], // Title CY
        $base['description'], // Description EN
        $base['description'], // Description CY
        '', // Item type
        '', // Tags EN
        '', // Tags CY
        $base['date'].'-01-01', // Date
        '', // Owner
        $base['author'], // Creator
        $base['url'], // Website en
        $base['url'], // Website cy
        '', // What facet
        $this->formatDateFacet($base['date']), // When facet
        '', // Location (lat, lon)
        '', // Location description en
        '', // Location description cy
        '', // Right Type 1
        '', // Right Holder 1 EN
        '', // Right Holder 1 CY
        '', // Begin Date 1
        '', // Right Type 2
        '', // Right Holder 2 EN
        '', // Right Holder 2 CY
        '', // Begin Date 2
        '', // Right Type 3
        '', // Right Holder 3 EN
        '', // Right Holder 3 CY
        '', // Begin Date 3
        '', // Addional rights        
      ));
    }

    return $list;
  }


  /**
   * [addSingleItemsToCsv description]
   * @param [type] $base  [description]
   * @param [type] $items [description]
   */
  private function addMultipartToCsv( $base, $items )
  {

    $list = [];

    $key = key($items);

    foreach ($items[$key]['children'] as $item => $value) {

      if($item == 0) {
        $parent = '';
      }else{
        $parent = $items[$key]['id'];
      }
     
      array_push($list, array(
        $value['id'], // Image Identifier
        $parent, // Parent ID*
        '', // Page Order*
        $value['image'], // Image/File Name
        $value['title'], // Title EN
        $value['title'], // Title CY
        $base['description'], // Description EN
        $base['description'], // Description CY
        '', // Item type
        '', // Tags EN
        '', // Tags CY
        $base['date'].'-01-01', // Date
        '', // Owner
        $base['author'], // Creator
        $base['url'], // Website en
        $base['url'], // Website cy
        '', // What facet
        $this->formatDateFacet($base['date']), // When facet
        '', // Location (lat, lon)
        '', // Location description en
        '', // Location description cy
        '', // Right Type 1
        '', // Right Holder 1 EN
        '', // Right Holder 1 CY
        '', // Begin Date 1
        '', // Right Type 2
        '', // Right Holder 2 EN
        '', // Right Holder 2 CY
        '', // Begin Date 2
        '', // Right Type 3
        '', // Right Holder 3 EN
        '', // Right Holder 3 CY
        '', // Begin Date 3
        '', // Addional rights        
      ));
    }

    return $list;

  }

}