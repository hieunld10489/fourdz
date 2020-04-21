<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @property Page_model $Page_model
 */
class Page extends Gen_Controller {

    public function index($page)
    {
        // 不正な記号を除去
        $page = $this->remove_invalid_characters($page);

        $page_info = $this->Page_model->get_info($page);
        $data['header_text'] = $page_info['title'];
        $data['back_url'] = $page_info['back_url'];

        $this->renderView($data, 'page/' . $page);
    }

    private function remove_invalid_characters($filename) {
        $filename = str_replace(' ', '', $filename);
        $filename = str_replace('~', '', $filename);
        $filename = str_replace('.', '', $filename);
        $filename = str_replace('/', '', $filename);
        $filename = str_replace('\\', '', $filename);
        return $filename;
    }

}
