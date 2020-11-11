<?php

class initDatabase {

    private $tables_array;
    private $insert_occr_array;
    private $insert_theme_array;
    
    public function __construct() {
        $this->tables_array = [
            ['MKTK_USERS', 'USERINDEX INT PRIMARY KEY AUTO_INCREMENT, USERID VARCHAR (20) NOT NULL UNIQUE, USERNAME VARCHAR (30) NOT NULL, PASSWORDHASH CHAR (64) NOT NULL, PERMISSION INT (1) NOT NULL, SALT CHAR (20) NOT NULL, LOGINUPTIME TIMESTAMP'],
            ['MKTK_USERS_SET', 'SETID INT PRIMARY KEY AUTO_INCREMENT, USERINDEX INT NOT NULL, YEARFLAG INT (1) NOT NULL, COMPLETEDFLAG INT (1), LASTLESSONTIME TIMESTAMP'],
            ['MKTK_USERS_SL', 'USERINDEX INT, OCCRID INT'],
            ['MKTK_LS_LIST', 'USERINDEX INT NOT NULL, LSID INT NOT NULL, COMPLETEDFLAG INT (1) NOT NULL,COMPLETEDTIME TIMESTAMP'],
            ['MKTK_TEST', 'TESTID INT PRIMARY KEY AUTO_INCREMENT, USERID VARCHAR (20) NOT NULL, TESTTYPEID INT (1) NOT NULL,SCORE INT (3) NOT NULL,TESTTIME TIMESTAMP'],
            ['MKTK_TEST_QS', 'TESTID INT NOT NULL, CONTENTID INT NOT NULL'],
            ['MKTK_OCCR', 'OCCRID INT PRIMARY KEY AUTO_INCREMENT, OCCRNAME VARCHAR (30) NOT NULL UNIQUE'],
            ['MKTK_LS_THEME', 'LSTHEMEID INT PRIMARY KEY AUTO_INCREMENT, LSTHEMEGROUPID INT NOT NULL, LSTHEMENAME VARCHAR (30) NOT NULL, OCCRID INT (1) NOT NULL'],
            ['MKTK_LS_FIELD', 'LSFIELDID INT PRIMARY KEY AUTO_INCREMENT, LSFIELDNAME VARCHAR (30) NOT NULL UNIQUE, LSTHEMEGROUPID INT NOT NULL'],
            ['MKTK_LS', 'LSID INT PRIMARY KEY AUTO_INCREMENT, LSTYPEID INT (1) NOT NULL, LSNAME VARCHAR (30) NOT NULL UNIQUE,LSFIELDID INT NOT NULL'],
            ['MKTK_LS_DATA', 'CONTENTID INT PRIMARY KEY AUTO_INCREMENT, LSID INT NOT NULL, CONTENT VARCHAR (255) NOT NULL'],
            ['MKTK_LS_TEST', 'CONTENTID INT PRIMARY KEY AUTO_INCREMENT, LSID INT NOT NULL, QUESTION VARCHAR (255) NOT NULL,ANSWER VARCHAR (255) NOT NULL,DIFFICULTY INT (1) NOT NULL']
        ];

        $this->insert_theme_array = [
            [1, 'ネットワークとセキュリティ', 1], [2, '情報セキュリティ対策', 1], [3, 'システム構築とセキュリティ', 2], [3, 'システム構築とセキュリティ', 3], [4, 'セキュアプログラミング', 3], [5, 'セキュリティテスト', 4], [6, 'システム運用・マネジメント系', 5], [7, 'ストラテジ系・法務と企業活動', 6]
        ];

        $this->insert_occr_array = [
            ['全職種'], ['システムインテグレータ'], ['開発（システム開発・ネットワーク開発）'], ['検証'], ['運用・保守'], ['その他（営業・広報・経理・総務など）']
        ];
    }

    function initDatabase() {
        $result = setTableStatus($this->tables_array, false);

        $html_text = "<h2>データベース確認終了</h2><hr><p>異常あり。データベースの設定を確認してください。</p>";
        if ($result) {
            $html_text = "<h2>データベース確認終了</h2><hr><p>異常は発見されませんでした。</p>";
            delete('MKTK_LS_THEME');
            delete('MKTK_OCCR');
            reset_auto_increment('MKTK_LS_THEME');
            reset_auto_increment('MKTK_OCCR', 0);
            foreach ($this->insert_occr_array as $var) {
                insert('MKTK_OCCR', ['OCCRNAME'], $var);
            }
            foreach ($this->insert_theme_array as $var) {
                insert('MKTK_LS_THEME', ['LSTHEMEGROUPID', 'LSTHEMENAME', 'OCCRID'], $var);
            }
        }
        $r_array = ["RES" => $result, "HTML" => $html_text];
        return $r_array;
    }

}
