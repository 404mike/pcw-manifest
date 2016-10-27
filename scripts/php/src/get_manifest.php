<?php
namespace llgc\src;


class get_manifest {

  /**
   * Manfiest URL
   * 
   * @var  string
   */
  private $manifestURL = 'http://dams.llgc.org.uk/iiif/2.0/$LLGCID/manifest.json';


  /**
   * Get the JSON manifest file from the dams website
   * convert to an array
   * @param $id string
   * @return array
   */
  public function get( $id )
  {

    $url = str_replace('$LLGCID', $id, $this->manifestURL);

    $json = file_get_contents($url);

    $data = json_decode($json , true);

    return $data;

  }


}