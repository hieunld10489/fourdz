<?php

class Page_model extends Base_Model
{
    public $table_name = 'pages';

    /**
     * 固定ページの情報を返す.
     * @param  string $page 対象ページ名
     * @return array
     *         array(
     *             'title' => '',
     *             'back_url' => ''
     *         )
     */
    public function get_info($page) {
        $howto_url = site_url('page/index/howto');

        switch ($page) {
            // 1階層目
            case 'about'    : return $this->create_info('4DXとは');
            case 'company'  : return $this->create_info('運用会社');
            case 'copyright': return $this->create_info('著作権について');
            case 'howto'    : return $this->create_info('利用方法');
            case 'qa'       : return $this->create_info('よくある質問');
            case 'term'     : return $this->create_info('利用規約');
            case 'help'     : return $this->create_info('ヘルプ');
            // 2階層目(howto配下)
            case 'point_project'    : return $this->create_info('スコアボード設定のポイント', $howto_url);
            case 'sample_project'   : return $this->create_info('スコアボード利用方法', $howto_url);
            //利用方法
            case 'accountability'   : return $this->create_info('利用方法', $howto_url);
            case 'point_wig'        : return $this->create_info('利用方法', $howto_url);
            case 'sample_wig'       : return $this->create_info('利用方法', $howto_url);

            case 'point_measure'    : return $this->create_info('先行指標設定のポイント', $howto_url);
            case 'sample_measure'   : return $this->create_info('先行指標利用方法', $howto_url);
            case 'point_commitment' : return $this->create_info('今週のコミットメント設定のポイント', $howto_url);
            case 'sample_commitment': return $this->create_info('今週のコミットメント利用方法', $howto_url);
            // その他
            default: return $this->create_info('');
        }
    }

    private function create_info($title, $back_url = null) {
        $page_info = array(
            'title' => $title,
            'back_url' => $back_url
        );
        if ($back_url == null) {
            $page_info['back_url'] = site_url('project');
        }
        return $page_info;
    }

}
