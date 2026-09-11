<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Pdf Library
 * Thin CI wrapper around dompdf. Loading this library ($this->load->library('pdf'))
 * exposes a ready-to-use Dompdf instance as $this->dompdf on the controller,
 * matching the existing $this->dompdf->loadHtml()/render()/stream() call sites.
 */
class Pdf
{
    public function __construct()
    {
        require_once APPPATH . '../vendor/autoload.php';

        $CI = &get_instance();
        $CI->dompdf = new \Dompdf\Dompdf();
    }
}
