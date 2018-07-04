<?php
namespace llgc\src;

class download_images {

  /**
   * URL for the image
   * 
   * @var  string
   */
  private $url = '';


  /**
   * Array of images to download
   * 
   * @var  array
   */
  private $images = [];


  /**
   * Download a local copy of the image
   * Reisze the image to best fit on PCW
   * @param  string $image - image path
   * @param  string $height - image height
   * @param  string $width - image width
   * @return string
   */
  public function downloadLocalCopyOfImage($data)
  {
    // print_r($data);
    // create an array of images to download
    $this->getListOfImages($data);

    // get the manifest id
    $id = str_replace(['http://dams.llgc.org.uk/iiif/2.0/','/manifest.json'], '', $data['@id']);

    // loop through all the images and download them to web_ready
    foreach($this->images as $image) {
      

      $imagePidAddr = str_replace('https://damsssl.llgc.org.uk/iiif/test/2.0/image/', '', $image);
      
      $imagePidArr = explode('/', $imagePidAddr);
      // echo $imagePidArr[0]."\n";

      $imagePath = 'http://fedoradev.llgc.org.uk/iiif/2.0/image/'.$imagePidArr[0].'/full/900,/0/default.jpg';

      // proxy rules
      $aContext = array(
          'http' => array(
              'proxy' => 'http://cache.llgc.org.uk:80',
              'request_fulluri' => true,
          ),
      );

      $cxContext = stream_context_create($aContext);

      $sFile = file_get_contents($imagePath);

      // set filename
      // $tempfilename = str_replace('http://dams.llgc.org.uk/iiif/test/2.0/image/', '', $image);

      // set filename
      $filename = $imagePidArr[0];

      echo "Downloading $filename.jpg \n";

      // place image in web_ready
      // file_put_contents('llgc_' . $id . '/' . $filename . '.jpg', $sFile);

      $dirIdArr = str_replace('https://damsssl.llgc.org.uk/iiif/2.0/', '', $id);
      $dirIdPart = explode('/', $dirIdArr);

      file_put_contents('./llgc_'.$dirIdPart[0].'/web_ready/'.$filename.'.jpg', $sFile);

    }

  }


  /**
   * Get a list of images to download
   * @param array $data
   * @return null
   */
  private function getListOfImages($data)
  {

    // all the images
    $arr = $data['sequences'][0]['canvases'];
  
    // loop through all the images in the array
    foreach($arr as $a) {

      $temp = $a['images'][0]['resource']['service'];

      $id     = $temp['@id'];
      $height = $temp['height'];
      $width  = $temp['width'];

      $height = (int) $height / 4;
      $width = (int) $width / 4;

      $height = round($height);
      $width =  round($width);

      // temporary
      // $height = '500';
      // $width = '500';

      // set the URL to the image path according to the IIIF presentation API
      // $url = str_replace('2.0', '', $id);

      $url = $id . '/full/' . $width . ',' . $height . '/0/default.jpg';

      $url = str_replace('iiif/', 'iiif/test/', $url);

      // add the formatted image URI to the array
      array_push($this->images, $url);

    }
  }


}