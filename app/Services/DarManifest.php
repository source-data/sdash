<?php

namespace App\Services;

use SimpleXMLElement;

/**
 * DarManifest is a utility class for generating the manifest.xml file for exporting a figure/panel to a DAR file.
 */
class DarManifest {

    protected $XmlRootText = <<<XML
<?xml version="1.0"?>
<!DOCTYPE dar PUBLIC "-//SUBSTANCE//DTD DocumentArchive v1.0//EN" "DocumentArchive-1.0.dtd">
<dar>
  <documents>
    <document id="smart-figure" type="smart-figure" path="smart-figure.xml" />
  </documents>
  <assets>
  </assets>
</dar>
XML;

    protected $manifest;

    public function __construct()
    {
        $this->manifest = new SimpleXMLElement($this->XmlRootText);

    }

    /**
     * Add an asset such as a file to the manifest. The manifest should describe all the files in a dar file.
     *
     * @param String $id - the id to be assigned to this asset
     * @param String $type - the MIME type for the file
     * @param String $filename - file filepath to the file within the DAR archive - usually will be in the root
     * @return void
     */
    public function appendAsset(String $id, String $type, String $filename)
    {

        $asset = $this->manifest->assets->addChild("asset");
        $asset["id"] = $id;
        $asset["type"] = $type;
        $asset["path"] = $filename;

    }

    /**
     * Export the manifest file as a string
     *
     * @return String
     */
    public function toString()
    {
        return $this->manifest->asXML();
    }





}