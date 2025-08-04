<?php
namespace Awz\Cookiessett\Access;

use Bitrix\Main\Application;
use Bitrix\Main\UserGroupTable;

class Handlers {

    public static function OnAfterUserUpdate(&$arFields){

        $connection = Application::getConnection();
        $helper = $connection->getSqlHelper();
        /* legacy main 21.900.0 нет метода */
        if(method_exists($helper,'getInsertIgnore')){
            $r = UserGroupTable::getList([
                'select'=>['GROUP_ID'],
                'filter'=>['=USER_ID'=>$arFields["ID"]]
            ]);

            $sqlValues = [];
            while($row = $r->fetch()){
                $id = (int) $row['GROUP_ID'];
                $userId = (int) $arFields["ID"];
                $sqlValues[] = '('.$userId.',\'group\',\'G'.$id.'\')';
            }
            if($userId)
                $sqlValues[] = '('.$userId.',\'user\',\'U'.$userId.'\')';
            if(!empty($sqlValues)){
                $sql = $helper->getInsertIgnore(
                    'b_user_access',
                    '(USER_ID, PROVIDER_ID, ACCESS_CODE)',
                    'VALUES '.implode(',', $sqlValues)
                );
                $connection->query($sql);
            }
        }

    }

}