<?php
error_reporting(0);
$pv = '';
for ($x = 1; $x <= 50; $x++) {
    if (!empty($_POST["con{$x}"])) {
        $pv .= "con{$x}=" . str_replace('+', '＋', str_replace('>', '＞', str_replace('<', '＜', str_replace('#', '%23', str_replace('&', '%26', $_POST["con{$x}"]))))) . '&';
    }
}
$post = $pv . 'url=' . 'http://www.144g.com/geren_huisejianyue/' . '&ym=' . '144g.com';
$urlc = "http://api.144g.com/144g/mf2.php";
$murl = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'];
$murl = 'http://www.144g.com/result.php?';

$ch = curl_init();
curl_setopt($ch, CURLOPT_REFERER, $murl);
curl_setopt($ch, CURLOPT_URL, $urlc);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$output = curl_exec($ch);
$output = str_replace("www.144g.com",$_SERVER['HTTP_HOST'],$output);
curl_close($ch);
$vip = vip();
//echo $_SERVER['HTTP_REFERER'];
if (!$vip) {
    preg_match('|地址(.*?)地址结束|i', $output, $resurl);
    preg_match('|结果(.*?)结果结束|i', $output, $resjg);
    $resurl = $resurl[1];
    $resjg = $resjg[1];
}
function vip()
{
    include "config.php";
    $urldz = $_SERVER['HTTP_REFERER'];
    $urllength = strlen($urldz) - 2;
    for ($i = $urllength; $i >= 0; $i--) {
        if (substr($urldz, $i, 1) == '/') {
            $temid = substr($urldz, $i + 1, $urllength - $i);
            break;
        }
    }
    $viptem = urldecode($viptem);
    $viptem = json_decode($viptem, 1);
	
    $array = array();
    for ($i = 0; $i < count($viptem['geren']); $i++) {
        $array[] = $viptem['geren'][$i];
    }
    for ($i = 0; $i < count($viptem['biaobai']); $i++) {
        $array[] = $viptem['biaobai'][$i];
    }
    for ($i = 0; $i < count($viptem['quwei']); $i++) {
        $array[] = $viptem['quwei'][$i];
    }
    $viptem = $array;
    $viptem_num = count($viptem);
    for ($i = 0; $i < $viptem_num; $i++) {
        if ($viptem[$i] == $temid) {
            if (!empty($_COOKIE["vip_username"]) && !empty($_COOKIE["vip_password"])) {
                $vip_username = $_COOKIE["vip_username"];
                $vip_password = $_COOKIE["vip_password"];
                $json_name = "plugin/vip_users.json";
                $json_string = file_get_contents($json_name);
                $obj = json_decode($json_string);
                $users = $obj->users;
                $users_num = count($users);
                if ($users_num != 0) {
                    for ($i = 0; $i < $users_num; $i++) {
                        if ($users[$i][0] == $vip_username && $users[$i][1] == $vip_password) {
                            return false;
                            break;
                        } else {
                            if ($i == $users_num - 1) {
                                return true;
                            }
                        }
                    }
                } else {
                    return true;
                }
            } else {
                return true;
            }
            break;
        } else {
            if ($i == $viptem_num - 1) {
                return false;
            }
        }
    }
}