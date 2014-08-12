<?php

class DownloadController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $filesystem = $this->getInvokeArg('bootstrap')->getResource('filesystem');

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

    public function viewAction()
    {
        $filesystem = $this->getInvokeArg('bootstrap')->getResource('filesystem');

        if (!array_key_exists('file', $_GET)) {
            header("HTTP/1.0 500 Internal Server Error");
            exit;
        }

        $file = $this->view->escape($_GET['file']);

        if (!$filesystem->has($file)) {
            header("HTTP/1.0 404 Not Found");
            exit;
        }

        $mime = $filesystem->getMimetype($file);
        $metadata = $filesystem->getMetadata($file);

        header("Pragma: public");
        header("Expires: -1");
        header("Cache-Control: public, must-revalidate, post-check=0, pre-check=0");
        header("Content-Disposition: inline; filename=\"" . $metadata["basename"] . "\"");
        header("Content-Type: $mime");

        echo $filesystem->read($file);

        exit;
    }
}
