<?php

class User_model extends Base_Model
{
    public $table_name = 'users';

    /**
     * UDIDハッシュをDBとCookieに保存する(既に保存済みであれば何もしない)
     * @param  string $udid_hash 保存するUDIDハッシュ
     * @return string            void
     */
    public function save_udid_hash($udid_hash) {
        $now_date = date(DATE_UI_DB_FORMAT);
        // Cookieに保存
        $this->save_udid_hash_to_cookie($udid_hash);
        // UDIDハッシュが登録済みであれば何もせず終了する
        $user = $this->get_user_by_udid($udid_hash);
        if ($user) { return; }
        // DBに保存
        $data = array(

        );
        $this->db->insert('users', [
            'udid_hash' => $udid_hash,
            'created' => $now_date,
            'updated' => $now_date
        ]);

        // 初期カテゴリーを追加しておく
        $new_id = $this->db->insert_id();
        if($new_id) {
            $this->db->insert('user_settings', [
                'user_id' => $new_id,
                'name' => 'Default',
                'week_start_monday' => OFF,
                'zone_rate_red' => self::$ZONE_RATE_RED,
                'zone_rate_green' => self::$ZONE_RATE_GREEN,
                'created' => date(DATE_UI_DB_FORMAT),
                'updated' => date(DATE_UI_DB_FORMAT)
            ]);

            $default_categories = ['仕事', '個人目標', '家族'];
            foreach ($default_categories as $default_category) {
                $data = [
                    'user_id' => $new_id,
                    'title' => $default_category,
                    'created' => $now_date,
                    'updated' => $now_date
                ];
                $this->db->insert('categories', $data);
            }
        }
    }

    /**
     * 現在ログイン中のユーザ情報を取得する.
     * @return array ユーザ情報
     */
    public function get_loggedin_user() {
        $udid_hash = User_model::get_loggedin_user_udid_hash();
        $query = $this->db->get_where('users', array('udid_hash' => $udid_hash));
        return $query->row_array();
    }

    /**
     * ユーザ情報の設定情報を取得する.
     * @return array ユーザ情報
     */
    public function get_user_settings($user_id) {
        $udid_hash = User_model::get_loggedin_user_udid_hash();
        $query = $this->db->get_where('user_settings', array('user_id' => $user_id));
        return $query->row_array();
    }

    /**
     * CookieにUDIDハッシュを保存する
     * @param  string $udid_hash 保存するUDIDハッシュ
     * @return string            void
     */
    private function save_udid_hash_to_cookie($udid_hash) {
        // 正常なリクエストなので、UDIDのハッシュをCookieに保存する
        $secure = (ENVIRONMENT == 'production'); // 本番環境なら、セキュアな通信じゃないとCookieを使わせないようにする
        set_cookie(COOKIE_KEY_UDUD_HASH, $udid_hash, (60*60*24*365*10), '', '/', '', $secure, true);
    }

    private function get_user_by_udid($udid_hash) {
        $query = $this->db->get_where('users', array('udid_hash' => $udid_hash));
        return $query->row_array();
    }

    private function get_user_by_id($id) {
        $query = $this->db->get_where('users', array('id' => $id));
        return $query->row_array();
    }



    /**
     * UDID文字列を72文字のハッシュにして返す.
     * あくまで一意なハッシュ値を求めるためのメソッドであるため、このメソッドをパスワードハッシュのために使ってはいけない.
     * @param  string $udid UDID文字列
     * @return string       ハッシュ化した文字列
     */
    public static function generate_udid_hash($udid) {
        $SEED1 = 'bU#i8jn@al9ra'; // 変更禁止
        $SEED2 = 'g2i_nn^u6nVer'; // 変更禁止

        // 前半32文字
        $str1 = $udid;
        for ($i = 0; $i < 27; $i++) {
            $str1 = md5($str1.$SEED1);
        }

        // 後半40文字
        $str2 = $udid;
        for ($i = 0; $i < 19; $i++) {
            $str2 = sha1($SEED2.$str2);
        }

        return $str1.$str2;
    }

    /**
     * 現在ログイン中のユーザを識別するUDIDハッシュ文字列を取得する
     * @return string UDIDハッシュ(ログインしていない場合は空文字列を返す)
     */
    public static function get_loggedin_user_udid_hash() {
        if (isset($_COOKIE[COOKIE_KEY_UDUD_HASH])) {
            return $_COOKIE[COOKIE_KEY_UDUD_HASH];
        } else {
            return '';
        }
    }

}
