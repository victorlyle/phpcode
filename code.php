<?php


// $attr="D:\laravel\app";

// $dir = "D:\laravel\app"; //当前目录
// list_file($dir);

// function list_file($dir){
//     $list = glob($dir.'\*'); // 得到该文件下的所有文件和文件夹
//     foreach($list as $file){//遍历
//         if(is_dir($file)){ //判断是不是文件夹
//             echo $file;
//             echo "<br/>";
//             list_file($file); //继续遍历
//         }
// 	}
// }


// $test="D:\laravel\app";
// file_traversal($test);

//     function file_traversal($test) {
//         foreach (glob($test.'\*') as $val) {
//                 echo $val;
//                 echo "<br>";
//                 if(is_dir($val)) {
//                     file_traversal($val);
//                 }
                
            
//         }
//     }




// $attr= array(45,21,13,78,49,88,34);
//  for ($i=0;$i=count($attr)-2;$i--){
//      $flag=false;
//      for ($j=0;$j<=$i;$j++) {
//          if($attr[$j]>$attr[$j+1]) {
//              $temp= $attr[$j];
//              $attr[$j]=$attr[$j+1];
//              $attr[$j+1]=$temp;
//              $flag=true;
//              print_r($attr);
//              echo "<br/>";
//          }
//      }
//      if ($flag === false) {
//          break;
//      }
//  }

// $attr="D:\laravel";
// file_2($attr);

// function file_2($attr) {
    
//     $file_con=glob($attr.'/*');
    
//     foreach ($file_con as $val) {
        
//         if(is_dir($val)){
            
//             echo $val;
//             echo '<br>';
//             file_2($val);
//         }
//     }
// }

// $attr =array(23,56,22,1,343,2);

// for ($i=0;$i=count($attr)-2;$i--) {
//     $flag=false;
//     for ($j=0;$j<=$i;$j++) {
        
        
//         if ($attr[$j]>$attr[$j+1]) {
//             $temp=$attr[$j];
//             $attr[$j]=$attr[$j+1];
//             $attr[$j+1]=$temp;
//             $flag=true;
//             print_r($attr);
//             echo "<br/>";
            
//         }
//     }
//     if ($flag === false) {
//         break;
//     }
    
// }

// $f="D:/laravel/app";
// file_content($f);
// function file_content ($f) {
    
//     $content= glob($f.'/*');
    
//     foreach ($content as $val) {
        
//         file_content($val);
//         if (!is_dir($val)){
//             echo $val;
//             echo "<br>";
        
//         }
//     }
    
// }
// $url="http://www.sina.com.cn/abc/de/fg.php?id=1";
// $temp =pathinfo($url,PATHINFO_EXTENSION);
// $temp=explode('?',$temp);
// print_r($temp[0]);



// $attr =array(23,32,11,1,45,87,12);

// for ($i=0;$i=count($attr)-2;$i--) {
//     $flag =false;
//     for ($j=0;$j<=$i;$j++) {
        
//         if($attr[$j]>$attr[$j+1]) {
            
//             $temp =$attr[$j];
//             $attr[$j]=$attr[$j+1];
//             $attr[$j+1]=$temp;
//             $flag=true;
//             print_r($attr);
//             echo "<br>";
//         }
        
//     }
//     if ($flag === false) {
//         break;
//     }
// }

// $attr ="D:/laravel/app";
// file_con($attr);
// function file_con($attr) {
    
//     $file_content=glob($attr.'/*');
    
//     foreach ($file_content as $val) {
        
//         if (is_dir($val)) {
//             print_r($val);
//             echo "<br>";
//             file_con($val);
//         }
        
//     }
// }
// $url="https://www.baidu.com/";
// $temp=pathinfo($url,PATHINFO_EXTENSION);
// print_r($temp);

// $str=1;
// $start= 200;
// $length=20;
// subString($str, $start, $length);

// function subString($str, $start, $length) {
//     $i = 0;
//     //完整排除之前的UTF8字符
//     while($i < $start) {
//         $ord = ord($str{$i});
//         if($ord < 192) {
//             $i++;
//         } elseif($ord <224) {
//             $i += 2;
//         } else {
//             $i += 3;
//         }
//     }
//     //开始截取
//     $result = '';
//     while($i < $start + $length && $i < strlen($str)) {
//         $ord = ord($str{$i});
//         if($ord < 192) {
//             $result .= $str{$i};
//             $i++;
//         } elseif($ord <224) {
//             $result .= $str{$i}.$str{$i+1};
//             $i += 2;
//         } else {
//             $result .= $str{$i}.$str{$i+1}.$str{$i+2};
//             $i += 3;
//         }
//     }
//     if($i < strlen($str)) {
//         $result .= '...';
//     }
//     return $result;
// }


// function zh_substr($string,$begin,$length){
//     if($begin<0){
//         $begin += strlen($string);
//     }
//     if($begin<0){
//         $begin = 0;
//     }
//     if($begin>=strlen($string)){
//         return false;
//     }
//     if($length<0){
//         $end = $length+strlen($string);
//     }else{
//         $end = $begin+$length;
//     }
//     if($end<0){
//         return false;
//     } elseif($end>=strlen($string)){
//         $end = strlen($string)-1;
//     }
   
//     while(ord($string[$begin])>127 and ord($string[$begin])<192){
//         $begin--;
//         $end--;
//     }
//     while(ord($string[$end])>127 and ord($string[$end])<192){
//         $end--;
//     }
//     return substr($string,$begin,$end-$begin);
// }

// $attr=array(23,56,22,2,98,55,13);

// for ($i=0;$i=count($attr)-2;$i--) {
//     $flag=false;
//     for ($j=0;$j<=$i;$j++) {
        
//        if ($attr[$j]>$attr[$j+1]) {
//            $temp=$attr[$j];
//            $attr[$j]=$attr[$j+1];
//            $attr[$j+1]=$temp;
//            $flag=true;
//            print_r($attr);
//            echo "<br>";
//        }
        
//     }
//     if ($flag ===false) {
//         break;
//     }
// }

// $attr="D:/laravel/app";
// file_content($attr);
// function file_content($attr){
    
//     $file_con=glob($attr.'/*');
//     foreach ($file_con as $val) {
        
//         if(is_dir($val)){
//             echo $val;
//             echo "<br>";
//             file_content($val);
//         }
//     }
// }

// $attr="https://www.taobao.com/";

// $val=pathinfo($attr);
// print_r($val);



// $attr=array(45,67,12,88,43,23);

// for ($i=0;$i=count($attr)-2;$i--) {
//     $flag=false;
//     for ($j=0;$j<=$i;$j++) {
        
//         if ($attr[$j]>$attr[$j+1]) {
            
//             $temp=$attr[$j];
//             $attr[$j]=$attr[$j+1];
//             $attr[$j+1]=$temp;
//             $flag=true;
//             print_r($attr);
//             echo "<br>";
//         }
//     }
//     if ($flag===false) {
//         break;
//     }
    
// }

// $attr="D:/laravel/app";
// file_co($attr);

// function file_co ($attr) {
    
//     $fileco=glob($attr.'/*');
    
//     foreach ($fileco as $val) {
        
//         print_r($val);
//         echo "<br>";
//         file_co($val);
//     }    
// }
// $subject = array(
//     'name' => 'spark1985',
//     'email' => 'spark@imooccom',
//     'mobile' => '13312345678'
// );
// $pattern='/\w+@\w+\.\w+/';
// if(!preg_match($pattern, $subject['email'])) {
//     echo ('wrong');
// }
// echo '11';
// function func1($a) {
//    return $a= $a+1;
// }
// echo phpinfo();
// $openid="XUyMRd4aHiG5hHPMGzI2AbABFZsn73jv";
//  $url = 'http://10.0.1.87:8081/public-interface.php';
//     $data = '{"id":1,"!version":1,"method":"com.innofidei.departmentc.getalldepartment","params":{
//   "company":"'.$openid.'"
//   },"jsonrpc":"2.0"}';

//     $header = array('Content-Type:application/json','charset:gb2312');

//     $ch = curl_init($url);
//     curl_setopt($ch, CURLOPT_HEADER, 0);
//     curl_setopt($ch, CURLOPT_POST, 1);
//     curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
//     curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
//     curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
//     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
//     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//     curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
//     curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
//     $response = curl_exec($ch);
//     $errno    = curl_errno($ch);
//     $errmsg   = curl_error($ch);

//     curl_close($ch);
//     $defaultGroup =json_decode($response)->data;
//     foreach ($defaultGroup as $v) {
//         $name =$v->name;
//         $parent =$v->parent;
//         print_r($v);
//     }
//    session_start();
//    $value=array(
//        'uid'=>'111112222',
//        'name'=>'222222'
//    );
   
//    $securekey ='1590key';
//    $str=serialize($value);
//    $info =mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($securekey), $str,MCRYPT_MODE_ECB);
   
//    $after =mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($securekey), $info, MCRYPT_MODE_ECB);
//    $afterinfo=unserialize($after);
//    print_r($afterinfo);
// $img=imagecreatetruecolor(200, 40);
// $black =imagecolorallocate($img, 0x00, 0x00, 0x00);
// $white =imagecolorallocate($img, 0xff, 0xff, 0xff);
// $green=imagecolorallocate($img, 0x00, 0xff, 0x00);
// imagefill($img, 5, 0, $white);
// $code='';

// for ($i=1;$i<5;$i++) {
//     $code.=rand(0, 9);
// }
// for ($i=1;$i<50;$i++) {
//     imagesetpixel($img, rand(0, 50), rand(0, 50), $green);
// }
// imagestring($img, 9, 5, 0, $code, $black);
// header("content-type: image/png");
// imagepng($img);
// imagedestroy($img);
// class MqMsgEvent{
//     const USER_INSERT = 'userInsert';
//     const USER_UPDATE = 'userUpdate';
//     const USER_DELETE = 'userDelete';
//     const DEPARTMENT_INSERT = 'departmentInsert';
//     const DEPARTMENT_UPDATE = 'departmentupdate';
//     const DEPARTMENT_DELETE = 'departmentDelete';
//     const COMPANY_INSERT = 'companyInsert';
//     const COMPANY_UPDATE = 'companyUpdate';
//     const COMPANY_DELETE = 'companyDelete';
//     const ADMIN_INSERT='adminInsert';
//     const ADMIN_UPDATE='adminUpdate';
//     const ADMIN_DELETE='adminDelete';
//     const LOGOUT_USER = 'logoutUser';
// }
// class test {
//     public function process($msg){
    
//         $result=json_decode($msg->body)->event;
//         if(defined("MqMsgEvent::$result") ){echo 11;
//             $val=constant("MqMsgEvent::$result");
//             $this->$val($msg);
    
//         } else {
//            echo "error";exit;
//         }-\
//     }
// }
// $msg = new stdClass();
// $msg->body = json_encode(array(
//    'event'=>'ADMIN_INSERT',
//    'messageType'=>'ADMIN_MANAGER',
//    'messageBody'=>array('openid'=>'IXst9Dsl5Vyte2ChChp8nqUlvD2ekdLt')
// ));
// $ws =new test();
// $ws=$ws->process($msg);

// $attr = array(34,2,67,32,6,15);

// for ($i=0;$i=count($attr)-2;$i--) {
//     $flag=false;
//     for ($j=0;$j<=$i;$j++) {
        
//         if($attr[$j]>$attr[$j+1]) {
//             $temp=$attr[$j];
//             $attr[$j]=$attr[$j+1];
//             $attr[$j+1]=$temp;
//             $flag=true;
//             print_r($attr);
//             echo '<br>';
//         }
//     }
//     if ($flag===false) {
//         break;
//     }
// }
class  test {
    
    public function logOrgInsert() {
    
        $sql =new PDO("pgsql:host=10.0.1.87;port=5432;dbname=newpro", 'immuser', 'immuser');
        $time ='1 Y';
        $aa= fetchAll();
        print_r($usercount);
        echo 111;
        
    
    
    }
}

$s =new test();
$ww=$s->logOrgInsert();
print_r($ww);exit;
?>
