<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller {

    /**
     * 匿名ログインを行う画面。
     * この画面を表示したら、アプリネイティブ側からjsのanonymousSubmitメソッドを呼ぶ。
     * その際、アプリネイティブ側で保持するUDIDを引数に渡す。
     * こうすることで、たとえWebView内のCookieが消えても同一ユーザであることを維持し続けられる。
     */
    public function anonymous_login()
    {
        $this->load->model('User_model');
        $this->load->helper('form');
        $this->load->helper('cookie');
        $this->load->library('form_validation');
        $this->load->library('session');

        $udid = $this->input->post('udid');

        // UDIDが送られてきたならば、強制ログインを行う
        if ($udid) {
            // CSRF対策
            $this->form_validation->set_rules('udid', 'UDID', 'required');
            if ($this->form_validation->run() === FALSE) {
                set_status_header(400);
                exit;
            } else {
                $udid_hash = User_model::generate_udid_hash($udid);
                // 正常なリクエストなので、UDIDのハッシュをDBとCookieに保存する
                $this->User_model->save_udid_hash($udid_hash);
                // 成功画面にリダイレクト
                redirect('user/anonymous_login_success');
                exit;
            }
        }

        $this->template['message'] = $this->load->view('errors/message', [], true);
        $this->load->view('user/anonymous_login', $this->template);
    }


    /**
     * ログイン成功画面。
     * この画面は、アプリネイティブでフックするための画面。
     * アプリ側でこの画面への遷移をフックしたということは、ログインに成功してCookieにUDIDハッシュが保存されたということ。
     * アプリ側でこの画面からアプリ内での本当のTOP画面に遷移させる。
     */
    public function anonymous_login_success()
    {
        $this->load->view('user/anonymous_login_success');
    }
}
