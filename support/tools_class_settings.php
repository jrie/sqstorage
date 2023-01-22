<?php

/**
 * Class  SETTINGS
 */
class SETTINGS{
    public static function  SettingsGet($namespace){
      $res = DB::queryFirstRow('SELECT * FROM settings WHERE `namespace` LIKE %s ',$namespace);
      if($res === null) return $res;
      return json_decode($res['jsondoc'],true);
    }


    public static function SettingsGetSingle($namespace,$setting,$default=""){
      $res = DB::queryFirstRow('SELECT * FROM settings WHERE `namespace` LIKE %s ',$namespace);
      if($res === null) return $default;
      $tmp = json_decode($res['jsondoc'],true);
      if(!isset($tmp[$setting]))return $default;
      return $tmp[$setting];
    }

      public static function SettingsSet($namespace,$setting,$value){
       $oldSettings = SETTINGS::SettingsGet($namespace);
       if($oldSettings === null){
          $oldSettings = array();
          $update = false;
       }else{
          $update = true;
       }
       $oldSettings[$setting] = $value;
       $json = json_encode($oldSettings);
       if($update){
            DB::query('UPDATE settings SET jsondoc = %s WHERE `namespace`LIKE %s',$json,$namespace);
       }else{
            DB::query('INSERT INTO settings (`namespace`,`jsondoc`) VALUES (%s,%s)', $namespace,$json);
       }
    }




}
