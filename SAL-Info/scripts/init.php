<?php

class initDatabase {
    
    function init() {
        //研修内容データのすべてのデータの吸出し
        $file = "/SAL/data/data_format.json";
        $format_data = loadfile($file);
        if(!$format_data) {
            return 1;
        }

        $result = setTableStatus($format_data['tables'], false);
        
        if ($result) {
            delete('MKTK_OCCR'); delete('MKTK_LS_THEME');
            delete('MKTK_LS_FIELD'); delete('MKTK_USERS_SL');
	    delete('MKTK_LS_LIST'); delete('MKTK_LS_DATA');
	    delete('MKTK_LS_TEST'); delete('MKTK_TEST_QS');
            delete('MKTK_LS'); delete('MKTK_USERS_SET', 'WHERE COMPLETEDFLAG <> 1');
            
            reset_auto_increment('MKTK_OCCR');
            reset_auto_increment('MKTK_LS_THEME');
            reset_auto_increment('MKTK_LS_FIELD');
	    reset_auto_increment('');
            reset_auto_increment('MKTK_LS');
            $r_flag = true;
            foreach ($format_data['occr'] as $var) { $r_flag = insert('MKTK_OCCR', ['OCCRNAME'], [$var[0]]); if(!$r_flag) { break; } }
            if(!$r_flag) { return 1; }
            foreach ($format_data['Ls_Tm'] as $var) { $r_flag = insert('MKTK_LS_THEME', ['LSTHEMEGROUPID', 'LSTHEMENAME', 'OCCRID'], [$var[0], $var[1], $var[2]]); if(!$r_flag) { break; } }
            if(!$r_flag) { return 1; }
            foreach ($format_data['Ls_Fl'] as $var) { $r_flag = insert('MKTK_LS_FIELD', ['LSFIELDNAME', 'LSTHEMEGROUPID'], [$var[0], $var[1]]); if(!$r_flag) { break; } }
            if(!$r_flag) { return 1; }
            foreach ($format_data['Ls'] as $var) { $r_flag = insert('MKTK_LS', ['LSTYPEID', 'LSNAME', 'LSFIELDID'], [$var[0], $var[1], $var[2]]); if(!$r_flag) { break; } }
            if(!$r_flag) { return 1; }
            return 0;
        }
    }

}
