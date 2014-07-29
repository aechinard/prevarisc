<?php

class DownloadController extends Zend_Controller_Action
{

  public function indexAction()
  {

    $filesystem = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('filesystem');

    if (!array_key_exists('file', $_GET)) {
      header("HTTP/1.0 500 Internal Server Error");
        exit;
    }

    $file = $this->view->escape($_GET['file']);

    if (!$filesystem->has($file)) {
      header("HTTP/1.0 404 Not Found");
      exit;
    }

    $metadata = $filesystem->getMetadata($file);

    header("Pragma: public");
    header("Expires: -1");
    header("Cache-Control: public, must-revalidate, post-check=0, pre-check=0");
    header("Content-Disposition: attachment; filename=\"" . $metadata["basename"] . "\"");
    header("Content-Type: application/octet-stream");

    echo $filesystem->read($file);

    exit;

  }

}
