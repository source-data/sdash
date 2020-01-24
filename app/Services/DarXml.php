<?php

namespace App\Services;

use SimpleXMLElement;

/**
 * DarXml is a utility class for SDash for Laravel. It takes information from the Laravel Eloquent Collection for
 * a panel and produces the XML for a DAR file.
 */
class DarXml {

    protected $XmlRootText = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE smart-figure PUBLIC "-//SOURCEDATA//DTD SmartFigure v1.0//EN" "SourceData-SmartFigure-1.0.dtd">
<smart-figure>
<additionalInformation>
    <paragraph id="additional-information-p1">Downloaded from SmartFigures Gallery</paragraph>
  </additionalInformation>
</smart-figure>
XML;

    protected $smartFigure;

    protected $activePanelLetter = 'a';

    public $assets = [];

    /**
     * The constructor simply generates the skeleton of the XML file with a title
     *
     * @param String $title - the title of the panel or figure being exported
     */
    public function __construct(String $title = null)
    {
        $this->smartFigure = new SimpleXMLElement($this->XmlRootText);

        if($title) $this->smartFigure->title = $title;

    }

    /**
     * Add an author to the author list for this panel/figure
     *
     * @param String $firstname - the author's first name
     * @param String $surname - the author's surname
     * @param String $organisation - the author's affiliation
     * @return void
     */
    public function appendAuthor(String $firstname, String $surname, String $organisation)
    {

        if(!isset($this->smartFigure->affiliations)) {

            $this->smartFigure->addChild("affiliations");

        }


        //does the affiliation already exist in the XML?
        $existingAff = $this->smartFigure->xpath('//affiliation[@name="' . $organisation . '"]');

        if(count($existingAff) > 0) {

            $existingAffiliation = $existingAff[0];
            $affiliationNumber = (int)$existingAffiliation["label"];
            $affiliationId = "aff" . $affiliationNumber;

        } else {

            $affiliationNumber = (count($this->smartFigure->affiliations->children()) === 0) ? 1 : count($this->smartFigure->affiliations->children()) + 1;
            $affiliationId = "aff" . $affiliationNumber;

            $newAffiliationItem = $this->smartFigure->affiliations->addChild("affiliation");
            $newAffiliationItem["id"] = $affiliationId;
            $newAffiliationItem["label"] = $affiliationNumber;
            $newAffiliationItem["name"] = $organisation;

        }

        if(!isset($this->smartFigure->authors)) {

            $this->smartFigure->addChild("authors");

        }

        $author = $this->smartFigure->authors->addChild("author");

        $author["firstName"] = $firstname;
        $author["lastName"] = $surname;
        $author["affiliations"] = $affiliationId;


    }

    /**
     * Add the panel details to the DAR XML file
     *
     * @param Collection $panel - the panel to be described
     * @return void
     */
    public function appendPanel($panel)
    {

        $panelId = 'panel-' . $this->activePanelLetter;

        if(!isset($this->smartFigure->panels)) {

            $this->smartFigure->addChild("panels");

        }

        $newPanel = $this->smartFigure->panels->addChild("panel");

        $newPanel["id"] = $panelId;
        $newPanel["label"] = strtoupper($this->activePanelLetter);

        //add legend
        $legend = $newPanel->addChild("legend");

        $para = $legend->addChild("paragraph", htmlspecialchars($panel->caption));

        $para["id"] = "panel-{$this->activePanelLetter}-p-1";

        //add image
        $image = $newPanel->addChild("image");

        $image["id"] = "panel-{$this->activePanelLetter}-image";

        $image["src"] = $panel->image->original_filename;

        $image["mimetype"] = $panel->image->mime_type;

        //add files and resources (which are another type of attachment)
        if(isset($panel->files) && count($panel->files) > 0) {

            $fileString = "";
            $resourceString = "";
            $fileCount = 1;
            $resourceCount = 1;

            foreach($panel->files as $file){

                switch($file->type){

                    case "file":
                        $fileString .= "file-{$fileCount} ";
                        $this->appendFile($file, "file-{$fileCount}");
                        $fileCount++;
                        break;
                    case "url":
                        $resourceString .= "resource-{$resourceCount} ";
                        $this->appendResource($file, "resource-{$resourceCount}");
                        $resourceCount++;
                        break;

                }

            }

            $fileString = trim($fileString);
            $resourceString = trim($resourceString);

            if($fileString)  $newPanel["files"] = $fileString;
            if($resourceString)  $newPanel["resources"] = $resourceString;

        }

        //add Keywords
        if(isset($panel->tags) && count($panel->tags) > 0){
            foreach($panel->tags as $tag){
                $this->appendKeyword($newPanel, $tag->content, $tag->meta->role ?? $tag->meta->type ?? null);
            }
        }

     }

     /**
      * Add a file description to the XML
      *
      * @param Collection $file
      * @param String $id
      * @return void
      */
    protected function appendFile($file, $id){

        if(! isset($this->smartFigure->files) ) {

            $files = $this->smartFigure->addChild("files");

        } else {

            $files = $this->smartFigure->files;

        }

        $newFile = $files->addChild("file");
        $newFile["id"] = $id;
        $newFile["mimetype"] = $file->mime_type;
        $newFile["src"] = $file->original_filename;

        $newFile->addChild("title", $file->description ?? "");
        $newFile->addChild("legend", $file->description ?? "");

    }

    /**
     * Add a resource (usually a URL) to the DAR file description
     *
     * @param Collection $resource
     * @param String $id
     * @return void
     */
    protected function appendResource($resource, $id){

        if(! isset($this->smartFigure->resources) ) {

            $resources = $this->smartFigure->addChild("resources");

        } else {

            $resources = $this->smartFigure->resources;

        }

        $newResource = $resources->addChild("resource");
        $newResource["id"] = $id;
        $newResource["href"] = $resource->url;

        $newResource->addChild("title", $resource->description ?? "");
        $newResource->addChild("legend", $resource->description ?? "");

    }

    /**
     * Add a keyword to the DAR file
     *
     * @param String $keyword
     * @param String $keywordGroupName
     * @return void
     */
    public function appendKeyword($panelXmlElement, String $keyword, String $keywordGroupName = null)
    {

        // create keywords
        if(! isset($panelXmlElement->keywords) ) {

            $keywords = $panelXmlElement->addChild("keywords");

        } else {

            $keywords = $panelXmlElement->keywords;

        }

        // create keyword group if needed
        if($keywordGroupName) {

            $keywordGroup =  $panelXmlElement->xpath('//keyword-group[@name="' . $keywordGroupName . '"]');

            if(!isset($keywordGroup[0])){

                $keywordGroup = $keywords->addChild("keyword-group");
                $keywordGroup["name"] = $keywordGroupName;

            } else {
                $keywordGroup = $keywordGroup[0];
            }
            $keywordGroup->addChild("keyword", $keyword);
        } else {
            // keywords cannot sit directly in the keyword element without a keyword group
            // so we create a general "keywords" group to contain those without an intrinsic group
            $keywordGroup =  $panelXmlElement->xpath('//keyword-group[@name="keywords"]');

            if(!isset($keywordGroup[0])){

                $keywordGroup = $keywords->addChild("keyword-group");
                $keywordGroup["name"] = "keywords";

            } else {
                $keywordGroup = $keywordGroup[0];
            }

            $keywordGroup->addChild("keyword", $keyword);
        }

    }

    /**
     * return the XML that describes the DAR file as an XML string to be saved to a file
     *
     * @return String
     */
    public function toString()
    {
        return $this->smartFigure->asXML();
    }





}