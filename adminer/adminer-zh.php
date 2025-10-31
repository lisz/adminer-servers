<?php
/** Adminer - Compact database management
* @link https://www.adminer.org/
* @author Jakub Vrana, https://www.vrana.cz/
* @copyright 2007 Jakub Vrana
* @license https://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
* @license https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2 (one or other)
* @version 5.4.2-dev
*/namespace
Adminer;const
VERSION="5.4.2-dev";error_reporting(24575);set_error_handler(function($Dc,$Fc){return!!preg_match('~^Undefined (array key|offset|index)~',$Fc);},E_WARNING|E_NOTICE);$bd=!preg_match('~^(unsafe_raw)?$~',ini_get("filter.default"));if($bd||ini_get("filter.default_flags")){foreach(array('_GET','_POST','_COOKIE','_SERVER')as$X){$tj=filter_input_array(constant("INPUT$X"),FILTER_UNSAFE_RAW);if($tj)$$X=$tj;}}if(function_exists("mb_internal_encoding"))mb_internal_encoding("8bit");function
connection($g=null){return($g?:Db::$instance);}function
adminer(){return
Adminer::$instance;}function
driver(){return
Driver::$instance;}function
connect(){$Gb=adminer()->credentials();$J=Driver::connect($Gb[0],$Gb[1],$Gb[2]);return(is_object($J)?$J:null);}function
idf_unescape($u){if(!preg_match('~^[`\'"[]~',$u))return$u;$Je=substr($u,-1);return
str_replace($Je.$Je,$Je,substr($u,1,-1));}function
q($Q){return
connection()->quote($Q);}function
escape_string($X){return
substr(q($X),1,-1);}function
idx($va,$x,$k=null){return($va&&array_key_exists($x,$va)?$va[$x]:$k);}function
number($X){return
preg_replace('~[^0-9]+~','',$X);}function
number_type(){return'((?<!o)int(?!er)|numeric|real|float|double|decimal|money)';}function
remove_slashes(array$bh,$bd=false){if(function_exists("get_magic_quotes_gpc")&&get_magic_quotes_gpc()){while(list($x,$X)=each($bh)){foreach($X
as$Be=>$W){unset($bh[$x][$Be]);if(is_array($W)){$bh[$x][stripslashes($Be)]=$W;$bh[]=&$bh[$x][stripslashes($Be)];}else$bh[$x][stripslashes($Be)]=($bd?$W:stripslashes($W));}}}}function
bracket_escape($u,$Ca=false){static$dj=array(':'=>':1',']'=>':2','['=>':3','"'=>':4');return
strtr($u,($Ca?array_flip($dj):$dj));}function
min_version($Lj,$Xe="",$g=null){$g=connection($g);$Wh=$g->server_info;if($Xe&&preg_match('~([\d.]+)-MariaDB~',$Wh,$A)){$Wh=$A[1];$Lj=$Xe;}return$Lj&&version_compare($Wh,$Lj)>=0;}function
charset(Db$f){return(min_version("5.5.3",0,$f)?"utf8mb4":"utf8");}function
ini_bool($le){$X=ini_get($le);return(preg_match('~^(on|true|yes)$~i',$X)||(int)$X);}function
ini_bytes($le){$X=ini_get($le);switch(strtolower(substr($X,-1))){case'g':$X=(int)$X*1024;case'm':$X=(int)$X*1024;case'k':$X=(int)$X*1024;}return$X;}function
sid(){static$J;if($J===null)$J=(SID&&!($_COOKIE&&ini_bool("session.use_cookies")));return$J;}function
set_password($Kj,$N,$V,$F){$_SESSION["pwds"][$Kj][$N][$V]=($_COOKIE["adminer_key"]&&is_string($F)?array(encrypt_string($F,$_COOKIE["adminer_key"])):$F);}function
get_password(){$J=get_session("pwds");if(is_array($J))$J=($_COOKIE["adminer_key"]?decrypt_string($J[0],$_COOKIE["adminer_key"]):false);return$J;}function
get_val($H,$m=0,$ub=null){$ub=connection($ub);$I=$ub->query($H);if(!is_object($I))return
false;$K=$I->fetch_row();return($K?$K[$m]:false);}function
get_vals($H,$d=0){$J=array();$I=connection()->query($H);if(is_object($I)){while($K=$I->fetch_row())$J[]=$K[$d];}return$J;}function
get_key_vals($H,$g=null,$Zh=true){$g=connection($g);$J=array();$I=$g->query($H);if(is_object($I)){while($K=$I->fetch_row()){if($Zh)$J[$K[0]]=$K[1];else$J[]=$K[0];}}return$J;}function
get_rows($H,$g=null,$l="<p class='error'>"){$ub=connection($g);$J=array();$I=$ub->query($H);if(is_object($I)){while($K=$I->fetch_assoc())$J[]=$K;}elseif(!$I&&!$g&&$l&&(defined('Adminer\PAGE_HEADER')||$l=="-- "))echo$l.error()."\n";return$J;}function
unique_array($K,array$w){foreach($w
as$v){if(preg_match("~PRIMARY|UNIQUE~",$v["type"])&&!$v["partial"]){$J=array();foreach($v["columns"]as$x){if(!isset($K[$x]))continue
2;$J[$x]=$K[$x];}return$J;}}}function
escape_key($x){if(preg_match('(^([\w(]+)('.str_replace("_",".*",preg_quote(idf_escape("_"))).')([ \w)]+)$)',$x,$A))return$A[1].idf_escape(idf_unescape($A[2])).$A[3];return
idf_escape($x);}function
where(array$Z,array$n=array()){$J=array();foreach((array)$Z["where"]as$x=>$X){$x=bracket_escape($x,true);$d=escape_key($x);$m=idx($n,$x,array());$Yc=$m["type"];$J[]=$d.(JUSH=="sql"&&$Yc=="json"?" = CAST(".q($X)." AS JSON)":(JUSH=="pgsql"&&preg_match('~^json~',$Yc)?"::jsonb = ".q($X)."::jsonb":(JUSH=="sql"&&is_numeric($X)&&preg_match('~\.~',$X)?" LIKE ".q($X):(JUSH=="mssql"&&strpos($Yc,"datetime")===false?" LIKE ".q(preg_replace('~[_%[]~','[\0]',$X)):" = ".unconvert_field($m,q($X))))));if(JUSH=="sql"&&preg_match('~char|text~',$Yc)&&preg_match("~[^ -@]~",$X))$J[]="$d = ".q($X)." COLLATE ".charset(connection())."_bin";}foreach((array)$Z["null"]as$x)$J[]=escape_key($x)." IS NULL";return
implode(" AND ",$J);}function
where_check($X,array$n=array()){parse_str($X,$Wa);remove_slashes(array(&$Wa));return
where($Wa,$n);}function
where_link($s,$d,$Y,$Yf="="){return"&where%5B$s%5D%5Bcol%5D=".urlencode($d)."&where%5B$s%5D%5Bop%5D=".urlencode(($Y!==null?$Yf:"IS NULL"))."&where%5B$s%5D%5Bval%5D=".urlencode($Y);}function
convert_fields(array$e,array$n,array$M=array()){$J="";foreach($e
as$x=>$X){if($M&&!in_array(idf_escape($x),$M))continue;$wa=convert_field($n[$x]);if($wa)$J
.=", $wa AS ".idf_escape($x);}return$J;}function
cookie($B,$Y,$Qe=2592000){header("Set-Cookie: $B=".urlencode($Y).($Qe?"; expires=".gmdate("D, d M Y H:i:s",time()+$Qe)." GMT":"")."; path=".preg_replace('~\?.*~','',$_SERVER["REQUEST_URI"]).(HTTPS?"; secure":"")."; HttpOnly; SameSite=lax",false);}function
get_settings($Cb){parse_str($_COOKIE[$Cb],$ai);return$ai;}function
get_setting($x,$Cb="adminer_settings",$k=null){return
idx(get_settings($Cb),$x,$k);}function
save_settings(array$ai,$Cb="adminer_settings"){$Y=http_build_query($ai+get_settings($Cb));cookie($Cb,$Y);$_COOKIE[$Cb]=$Y;}function
restart_session(){if(!ini_bool("session.use_cookies")&&(!function_exists('session_status')||session_status()==1))session_start();}function
stop_session($jd=false){$Cj=ini_bool("session.use_cookies");if(!$Cj||$jd){session_write_close();if($Cj&&@ini_set("session.use_cookies",'0')===false)session_start();}}function&get_session($x){return$_SESSION[$x][DRIVER][SERVER][$_GET["username"]];}function
set_session($x,$X){$_SESSION[$x][DRIVER][SERVER][$_GET["username"]]=$X;}function
auth_url($Kj,$N,$V,$j=null){$zj=remove_from_uri(implode("|",array_keys(SqlDriver::$drivers))."|username|ext|".($j!==null?"db|":"").($Kj=='mssql'||$Kj=='pgsql'?"":"ns|").session_name());preg_match('~([^?]*)\??(.*)~',$zj,$A);return"$A[1]?".(sid()?SID."&":"").($Kj!="server"||$N!=""?urlencode($Kj)."=".urlencode($N)."&":"").($_GET["ext"]?"ext=".urlencode($_GET["ext"])."&":"")."username=".urlencode($V).($j!=""?"&db=".urlencode($j):"").($A[2]?"&$A[2]":"");}function
is_ajax(){return($_SERVER["HTTP_X_REQUESTED_WITH"]=="XMLHttpRequest");}function
redirect($Te,$mf=null){if($mf!==null){restart_session();$_SESSION["messages"][preg_replace('~^[^?]*~','',($Te!==null?$Te:$_SERVER["REQUEST_URI"]))][]=$mf;}if($Te!==null){if($Te=="")$Te=".";header("Location: $Te");exit;}}function
query_redirect($H,$Te,$mf,$kh=true,$Kc=true,$Tc=false,$Qi=""){if($Kc){$pi=microtime(true);$Tc=!connection()->query($H);$Qi=format_time($pi);}$ji=($H?adminer()->messageQuery($H,$Qi,$Tc):"");if($Tc){adminer()->error
.=error().$ji.script("messagesPrint();")."<br>";return
false;}if($kh)redirect($Te,$mf.$ji);return
true;}class
Queries{static$queries=array();static$start=0;}function
queries($H){if(!Queries::$start)Queries::$start=microtime(true);Queries::$queries[]=(preg_match('~;$~',$H)?"DELIMITER ;;\n$H;\nDELIMITER ":$H).";";return
connection()->query($H);}function
apply_queries($H,array$T,$Gc='Adminer\table'){foreach($T
as$R){if(!queries("$H ".$Gc($R)))return
false;}return
true;}function
queries_redirect($Te,$mf,$kh){$fh=implode("\n",Queries::$queries);$Qi=format_time(Queries::$start);return
query_redirect($fh,$Te,$mf,$kh,false,!$kh,$Qi);}function
format_time($pi){return
sprintf('%.3f 秒',max(0,microtime(true)-$pi));}function
relative_uri(){return
str_replace(":","%3a",preg_replace('~^[^?]*/([^?]*)~','\1',$_SERVER["REQUEST_URI"]));}function
remove_from_uri($vg=""){return
substr(preg_replace("~(?<=[?&])($vg".(SID?"":"|".session_name()).")=[^&]*&~",'',relative_uri()."&"),0,-1);}function
get_file($x,$Sb=false,$Yb=""){$ad=$_FILES[$x];if(!$ad)return
null;foreach($ad
as$x=>$X)$ad[$x]=(array)$X;$J='';foreach($ad["error"]as$x=>$l){if($l)return$l;$B=$ad["name"][$x];$Yi=$ad["tmp_name"][$x];$zb=file_get_contents($Sb&&preg_match('~\.gz$~',$B)?"compress.zlib://$Yi":$Yi);if($Sb){$pi=substr($zb,0,3);if(function_exists("iconv")&&preg_match("~^\xFE\xFF|^\xFF\xFE~",$pi))$zb=iconv("utf-16","utf-8",$zb);elseif($pi=="\xEF\xBB\xBF")$zb=substr($zb,3);}$J
.=$zb;if($Yb)$J
.=(preg_match("($Yb\\s*\$)",$zb)?"":$Yb)."\n\n";}return$J;}function
upload_error($l){$hf=($l==UPLOAD_ERR_INI_SIZE?ini_get("upload_max_filesize"):0);return($l?'不能上传文件。'.($hf?" ".sprintf('最多允许的文件大小为 %sB。',$hf):""):'文件不存在。');}function
repeat_pattern($Hg,$y){return
str_repeat("$Hg{0,65535}",$y/65535)."$Hg{0,".($y%65535)."}";}function
is_utf8($X){return(preg_match('~~u',$X)&&!preg_match('~[\0-\x8\xB\xC\xE-\x1F]~',$X));}function
format_number($X){return
strtr(number_format($X,0,".",','),preg_split('~~u','0123456789',-1,PREG_SPLIT_NO_EMPTY));}function
friendly_url($X){return
preg_replace('~\W~i','-',$X);}function
table_status1($R,$Uc=false){$J=table_status($R,$Uc);return($J?reset($J):array("Name"=>$R));}function
column_foreign_keys($R){$J=array();foreach(adminer()->foreignKeys($R)as$p){foreach($p["source"]as$X)$J[$X][]=$p;}return$J;}function
fields_from_edit(){$J=array();foreach((array)$_POST["field_keys"]as$x=>$X){if($X!=""){$X=bracket_escape($X);$_POST["function"][$X]=$_POST["field_funs"][$x];$_POST["fields"][$X]=$_POST["field_vals"][$x];}}foreach((array)$_POST["fields"]as$x=>$X){$B=bracket_escape($x,true);$J[$B]=array("field"=>$B,"privileges"=>array("insert"=>1,"update"=>1,"where"=>1,"order"=>1),"null"=>1,"auto_increment"=>($x==driver()->primary),);}return$J;}function
dump_headers($Rd,$xf=false){$J=adminer()->dumpHeaders($Rd,$xf);$rg=$_POST["output"];if($rg!="text")header("Content-Disposition: attachment; filename=".adminer()->dumpFilename($Rd).".$J".($rg!="file"&&preg_match('~^[0-9a-z]+$~',$rg)?".$rg":""));session_write_close();if(!ob_get_level())ob_start(null,4096);ob_flush();flush();return$J;}function
dump_csv(array$K){foreach($K
as$x=>$X){if(preg_match('~["\n,;\t]|^0.|\.\d*0$~',$X)||$X==="")$K[$x]='"'.str_replace('"','""',$X).'"';}echo
implode(($_POST["format"]=="csv"?",":($_POST["format"]=="tsv"?"\t":";")),$K)."\r\n";}function
apply_sql_function($r,$d){return($r?($r=="unixepoch"?"DATETIME($d, '$r')":($r=="count distinct"?"COUNT(DISTINCT ":strtoupper("$r("))."$d)"):$d);}function
get_temp_dir(){$J=ini_get("upload_tmp_dir");if(!$J){if(function_exists('sys_get_temp_dir'))$J=sys_get_temp_dir();else{$o=@tempnam("","");if(!$o)return'';$J=dirname($o);unlink($o);}}return$J;}function
file_open_lock($o){if(is_link($o))return;$q=@fopen($o,"c+");if(!$q)return;@chmod($o,0660);if(!flock($q,LOCK_EX)){fclose($q);return;}return$q;}function
file_write_unlock($q,$Mb){rewind($q);fwrite($q,$Mb);ftruncate($q,strlen($Mb));file_unlock($q);}function
file_unlock($q){flock($q,LOCK_UN);fclose($q);}function
first(array$va){return
reset($va);}function
password_file($h){$o=get_temp_dir()."/adminer.key";if(!$h&&!file_exists($o))return'';$q=file_open_lock($o);if(!$q)return'';$J=stream_get_contents($q);if(!$J){$J=rand_string();file_write_unlock($q,$J);}else
file_unlock($q);return$J;}function
rand_string(){return
md5(uniqid(strval(mt_rand()),true));}function
select_value($X,$_,array$m,$Pi){if(is_array($X)){$J="";foreach($X
as$Be=>$W)$J
.="<tr>".($X!=array_values($X)?"<th>".h($Be):"")."<td>".select_value($W,$_,$m,$Pi);return"<table>$J</table>";}if(!$_)$_=adminer()->selectLink($X,$m);if($_===null){if(is_mail($X))$_="mailto:$X";if(is_url($X))$_=$X;}$J=adminer()->editVal($X,$m);if($J!==null){if(!is_utf8($J))$J="\0";elseif($Pi!=""&&is_shortable($m))$J=shorten_utf8($J,max(0,+$Pi));else$J=h($J);}return
adminer()->selectVal($J,$_,$m,$X);}function
is_blob(array$m){return
preg_match('~blob|bytea|raw|file~',$m["type"])&&!in_array($m["type"],idx(driver()->structuredTypes(),'用户类型',array()));}function
is_mail($uc){$xa='[-a-z0-9!#$%&\'*+/=?^_`{|}~]';$hc='[a-z0-9]([-a-z0-9]{0,61}[a-z0-9])';$Hg="$xa+(\\.$xa+)*@($hc?\\.)+$hc";return
is_string($uc)&&preg_match("(^$Hg(,\\s*$Hg)*\$)i",$uc);}function
is_url($Q){$hc='[a-z0-9]([-a-z0-9]{0,61}[a-z0-9])';return
preg_match("~^(https?)://($hc?\\.)+$hc(:\\d+)?(/.*)?(\\?.*)?(#.*)?\$~i",$Q);}function
is_shortable(array$m){return
preg_match('~char|text|json|lob|geometry|point|linestring|polygon|string|bytea|hstore~',$m["type"]);}function
host_port($N){return(preg_match('~^(\[(.+)]|([^:]+)):([^:]+)$~',$N,$A)?array($A[2].$A[3],$A[4]):array($N,''));}function
count_rows($R,array$Z,$ve,array$xd){$H=" FROM ".table($R).($Z?" WHERE ".implode(" AND ",$Z):"");return($ve&&(JUSH=="sql"||count($xd)==1)?"SELECT COUNT(DISTINCT ".implode(", ",$xd).")$H":"SELECT COUNT(*)".($ve?" FROM (SELECT 1$H GROUP BY ".implode(", ",$xd).") x":$H));}function
slow_query($H){$j=adminer()->database();$Ri=adminer()->queryTimeout();$ei=driver()->slowQuery($H,$Ri);$g=null;if(!$ei&&support("kill")){$g=connect();if($g&&($j==""||$g->select_db($j))){$Ee=get_val(connection_id(),0,$g);echo
script("const timeout = setTimeout(() => { ajax('".js_escape(ME)."script=kill', function () {}, 'kill=$Ee&token=".get_token()."'); }, 1000 * $Ri);");}}ob_flush();flush();$J=@get_key_vals(($ei?:$H),$g,false);if($g){echo
script("clearTimeout(timeout);");ob_flush();flush();}return$J;}function
get_token(){$ih=rand(1,1e6);return($ih^$_SESSION["token"]).":$ih";}function
verify_token(){list($Zi,$ih)=explode(":",$_POST["token"]);return($ih^$_SESSION["token"])==$Zi;}function
lzw_decompress($Ia){$dc=256;$Ja=8;$gb=array();$vh=0;$wh=0;for($s=0;$s<strlen($Ia);$s++){$vh=($vh<<8)+ord($Ia[$s]);$wh+=8;if($wh>=$Ja){$wh-=$Ja;$gb[]=$vh>>$wh;$vh&=(1<<$wh)-1;$dc++;if($dc>>$Ja)$Ja++;}}$cc=range("\0","\xFF");$J="";$Uj="";foreach($gb
as$s=>$fb){$tc=$cc[$fb];if(!isset($tc))$tc=$Uj.$Uj[0];$J
.=$tc;if($s)$cc[]=$Uj.$tc[0];$Uj=$tc;}return$J;}function
script($gi,$cj="\n"){return"<script".nonce().">$gi</script>$cj";}function
script_src($_j,$Vb=false){return"<script src='".h($_j)."'".nonce().($Vb?" defer":"")."></script>\n";}function
nonce(){return' nonce="'.get_nonce().'"';}function
input_hidden($B,$Y=""){return"<input type='hidden' name='".h($B)."' value='".h($Y)."'>\n";}function
input_token(){return
input_hidden("token",get_token());}function
target_blank(){return' target="_blank" rel="noreferrer noopener"';}function
h($Q){return
str_replace("\0","&#0;",htmlspecialchars($Q,ENT_QUOTES,'utf-8'));}function
nl_br($Q){return
str_replace("\n","<br>",$Q);}function
checkbox($B,$Y,$Za,$Ge="",$Xf="",$db="",$Ie=""){$J="<input type='checkbox' name='$B' value='".h($Y)."'".($Za?" checked":"").($Ie?" aria-labelledby='$Ie'":"").">".($Xf?script("qsl('input').onclick = function () { $Xf };",""):"");return($Ge!=""||$db?"<label".($db?" class='$db'":"").">$J".h($Ge)."</label>":$J);}function
optionlist($cg,$Oh=null,$Dj=false){$J="";foreach($cg
as$Be=>$W){$dg=array($Be=>$W);if(is_array($W)){$J
.='<optgroup label="'.h($Be).'">';$dg=$W;}foreach($dg
as$x=>$X)$J
.='<option'.($Dj||is_string($x)?' value="'.h($x).'"':'').($Oh!==null&&($Dj||is_string($x)?(string)$x:$X)===$Oh?' selected':'').'>'.h($X);if(is_array($W))$J
.='</optgroup>';}return$J;}function
html_select($B,array$cg,$Y="",$Wf="",$Ie=""){static$Ge=0;$He="";if(!$Ie&&substr($cg[""],0,1)=="("){$Ge++;$Ie="label-$Ge";$He="<option value='' id='$Ie'>".h($cg[""]);unset($cg[""]);}return"<select name='".h($B)."'".($Ie?" aria-labelledby='$Ie'":"").">".$He.optionlist($cg,$Y)."</select>".($Wf?script("qsl('select').onchange = function () { $Wf };",""):"");}function
html_radios($B,array$cg,$Y="",$Sh=""){$J="";foreach($cg
as$x=>$X)$J
.="<label><input type='radio' name='".h($B)."' value='".h($x)."'".($x==$Y?" checked":"").">".h($X)."</label>$Sh";return$J;}function
confirm($mf="",$Ph="qsl('input')"){return
script("$Ph.onclick = () => confirm('".($mf?js_escape($mf):'您确定吗？')."');","");}function
print_fieldset($t,$Oe,$Oj=false){echo"<fieldset><legend>","<a href='#fieldset-$t'>$Oe</a>",script("qsl('a').onclick = partial(toggle, 'fieldset-$t');",""),"</legend>","<div id='fieldset-$t'".($Oj?"":" class='hidden'").">\n";}function
bold($La,$db=""){return($La?" class='active $db'":($db?" class='$db'":""));}function
js_escape($Q){return
addcslashes($Q,"\r\n'\\/");}function
pagination($D,$Jb){return" ".($D==$Jb?$D+1:'<a href="'.h(remove_from_uri("page").($D?"&page=$D".($_GET["next"]?"&next=".urlencode($_GET["next"]):""):"")).'">'.($D+1)."</a>");}function
hidden_fields(array$bh,array$Vd=array(),$Tg=''){$J=false;foreach($bh
as$x=>$X){if(!in_array($x,$Vd)){if(is_array($X))hidden_fields($X,array(),$x);else{$J=true;echo
input_hidden(($Tg?$Tg."[$x]":$x),$X);}}}return$J;}function
hidden_fields_get(){echo(sid()?input_hidden(session_name(),session_id()):''),(SERVER!==null?input_hidden(DRIVER,SERVER):""),input_hidden("username",$_GET["username"]);}function
file_input($ne){$cf="max_file_uploads";$df=ini_get($cf);$xj="upload_max_filesize";$yj=ini_get($xj);return(ini_bool("file_uploads")?$ne.script("qsl('input[type=\"file\"]').onchange = partialArg(fileChange, "."$df, '".sprintf('Increase %s.',"$cf = $df")."', ".ini_bytes("upload_max_filesize").", '".sprintf('Increase %s.',"$xj = $yj")."')"):'文件上传被禁用。');}function
enum_input($U,$ya,array$m,$Y,$xc=""){preg_match_all("~'((?:[^']|'')*)'~",$m["length"],$af);$Tg=($m["type"]=="enum"?"val-":"");$Za=(is_array($Y)?in_array("null",$Y):$Y===null);$J=($m["null"]&&$Tg?"<label><input type='$U'$ya value='null'".($Za?" checked":"")."><i>$xc</i></label>":"");foreach($af[1]as$X){$X=stripcslashes(str_replace("''","'",$X));$Za=(is_array($Y)?in_array($Tg.$X,$Y):$Y===$X);$J
.=" <label><input type='$U'$ya value='".h($Tg.$X)."'".($Za?' checked':'').'>'.h(adminer()->editVal($X,$m)).'</label>';}return$J;}function
input(array$m,$Y,$r,$Ba=false){$B=h(bracket_escape($m["field"]));echo"<td class='function'>";if(is_array($Y)&&!$r){$Y=json_encode($Y,128|64|256);$r="json";}$uh=(JUSH=="mssql"&&$m["auto_increment"]);if($uh&&!$_POST["save"])$r=null;$sd=(isset($_GET["select"])||$uh?array("orig"=>'原始'):array())+adminer()->editFunctions($m);$Cc=driver()->enumLength($m);if($Cc){$m["type"]="enum";$m["length"]=$Cc;}$ec=stripos($m["default"],"GENERATED ALWAYS AS ")===0?" disabled=''":"";$ya=" name='fields[$B]".($m["type"]=="enum"||$m["type"]=="set"?"[]":"")."'$ec".($Ba?" autofocus":"");echo
driver()->unconvertFunction($m)." ";$R=$_GET["edit"]?:$_GET["select"];if($m["type"]=="enum")echo
h($sd[""])."<td>".adminer()->editInput($R,$m,$ya,$Y);else{$Ed=(in_array($r,$sd)||isset($sd[$r]));echo(count($sd)>1?"<select name='function[$B]'$ec>".optionlist($sd,$r===null||$Ed?$r:"")."</select>".on_help("event.target.value.replace(/^SQL\$/, '')",1).script("qsl('select').onchange = functionChange;",""):h(reset($sd))).'<td>';$ne=adminer()->editInput($R,$m,$ya,$Y);if($ne!="")echo$ne;elseif(preg_match('~bool~',$m["type"]))echo"<input type='hidden'$ya value='0'>"."<input type='checkbox'".(preg_match('~^(1|t|true|y|yes|on)$~i',$Y)?" checked='checked'":"")."$ya value='1'>";elseif($m["type"]=="set")echo
enum_input("checkbox",$ya,$m,(is_string($Y)?explode(",",$Y):$Y));elseif(is_blob($m)&&ini_bool("file_uploads"))echo"<input type='file' name='fields-$B'>";elseif($r=="json"||preg_match('~^jsonb?$~',$m["type"]))echo"<textarea$ya cols='50' rows='12' class='jush-js'>".h($Y).'</textarea>';elseif(($Ni=preg_match('~text|lob|memo~i',$m["type"]))||preg_match("~\n~",$Y)){if($Ni&&JUSH!="sqlite")$ya
.=" cols='50' rows='12'";else{$L=min(12,substr_count($Y,"\n")+1);$ya
.=" cols='30' rows='$L'";}echo"<textarea$ya>".h($Y).'</textarea>';}else{$nj=driver()->types();$jf=(!preg_match('~int~',$m["type"])&&preg_match('~^(\d+)(,(\d+))?$~',$m["length"],$A)?((preg_match("~binary~",$m["type"])?2:1)*$A[1]+($A[3]?1:0)+($A[2]&&!$m["unsigned"]?1:0)):($nj[$m["type"]]?$nj[$m["type"]]+($m["unsigned"]?0:1):0));if(JUSH=='sql'&&min_version(5.6)&&preg_match('~time~',$m["type"]))$jf+=7;echo"<input".((!$Ed||$r==="")&&preg_match('~(?<!o)int(?!er)~',$m["type"])&&!preg_match('~\[\]~',$m["full_type"])?" type='number'":"")." value='".h($Y)."'".($jf?" data-maxlength='$jf'":"").(preg_match('~char|binary~',$m["type"])&&$jf>20?" size='".($jf>99?60:40)."'":"")."$ya>";}echo
adminer()->editHint($R,$m,$Y);$cd=0;foreach($sd
as$x=>$X){if($x===""||!$X)break;$cd++;}if($cd&&count($sd)>1)echo
script("qsl('td').oninput = partial(skipOriginal, $cd);");}}function
process_input(array$m){if(stripos($m["default"],"GENERATED ALWAYS AS ")===0)return;$u=bracket_escape($m["field"]);$r=idx($_POST["function"],$u);$Y=idx($_POST["fields"],$u);if($m["type"]=="enum"||driver()->enumLength($m)){$Y=$Y[0];if($Y=="orig")return
false;if($Y=="null")return"NULL";$Y=substr($Y,4);}if($m["auto_increment"]&&$Y=="")return
null;if($r=="orig")return(preg_match('~^CURRENT_TIMESTAMP~i',$m["on_update"])?idf_escape($m["field"]):false);if($r=="NULL")return"NULL";if($m["type"]=="set")$Y=implode(",",(array)$Y);if($r=="json"){$r="";$Y=json_decode($Y,true);if(!is_array($Y))return
false;return$Y;}if(is_blob($m)&&ini_bool("file_uploads")){$ad=get_file("fields-$u");if(!is_string($ad))return
false;return
driver()->quoteBinary($ad);}return
adminer()->processInput($m,$Y,$r);}function
search_tables(){$_GET["where"][0]["val"]=$_POST["query"];$Rh="<ul>\n";foreach(table_status('',true)as$R=>$S){$B=adminer()->tableName($S);if(isset($S["Engine"])&&$B!=""&&(!$_POST["tables"]||in_array($R,$_POST["tables"]))){$I=connection()->query("SELECT".limit("1 FROM ".table($R)," WHERE ".implode(" AND ",adminer()->selectSearchProcess(fields($R),array())),1));if(!$I||$I->fetch_row()){$Xg="<a href='".h(ME."select=".urlencode($R)."&where[0][op]=".urlencode($_GET["where"][0]["op"])."&where[0][val]=".urlencode($_GET["where"][0]["val"]))."'>$B</a>";echo"$Rh<li>".($I?$Xg:"<p class='error'>$Xg: ".error())."\n";$Rh="";}}}echo($Rh?"<p class='message'>".'没有表。':"</ul>")."\n";}function
on_help($mb,$ci=0){return
script("mixin(qsl('select, input'), {onmouseover: function (event) { helpMouseover.call(this, event, $mb, $ci) }, onmouseout: helpMouseout});","");}function
edit_form($R,array$n,$K,$wj,$l=''){$Ai=adminer()->tableName(table_status1($R,true));page_header(($wj?'编辑':'插入'),$l,array("select"=>array($R,$Ai)),$Ai);adminer()->editRowPrint($R,$n,$K,$wj);if($K===false){echo"<p class='error'>".'无数据。'."\n";return;}echo"<form action='' method='post' enctype='multipart/form-data' id='form'>\n";if(!$n)echo"<p class='error'>".'您没有权限更新这个表。'."\n";else{echo"<table class='layout'>".script("qsl('table').onkeydown = editingKeydown;");$Ba=!$_POST;foreach($n
as$B=>$m){echo"<tr><th>".adminer()->fieldName($m);$k=idx($_GET["set"],bracket_escape($B));if($k===null){$k=$m["default"];if($m["type"]=="bit"&&preg_match("~^b'([01]*)'\$~",$k,$rh))$k=$rh[1];if(JUSH=="sql"&&preg_match('~binary~',$m["type"]))$k=bin2hex($k);}$Y=($K!==null?($K[$B]!=""&&JUSH=="sql"&&preg_match("~enum|set~",$m["type"])&&is_array($K[$B])?implode(",",$K[$B]):(is_bool($K[$B])?+$K[$B]:$K[$B])):(!$wj&&$m["auto_increment"]?"":(isset($_GET["select"])?false:$k)));if(!$_POST["save"]&&is_string($Y))$Y=adminer()->editVal($Y,$m);$r=($_POST["save"]?idx($_POST["function"],$B,""):($wj&&preg_match('~^CURRENT_TIMESTAMP~i',$m["on_update"])?"now":($Y===false?null:($Y!==null?'':'NULL'))));if(!$_POST&&!$wj&&$Y==$m["default"]&&preg_match('~^[\w.]+\(~',$Y))$r="SQL";if(preg_match("~time~",$m["type"])&&preg_match('~^CURRENT_TIMESTAMP~i',$Y)){$Y="";$r="now";}if($m["type"]=="uuid"&&$Y=="uuid()"){$Y="";$r="uuid";}if($Ba!==false)$Ba=($m["auto_increment"]||$r=="now"||$r=="uuid"?null:true);input($m,$Y,$r,$Ba);if($Ba)$Ba=false;echo"\n";}if(!support("table")&&!fields($R))echo"<tr>"."<th><input name='field_keys[]'>".script("qsl('input').oninput = fieldChange;")."<td class='function'>".html_select("field_funs[]",adminer()->editFunctions(array("null"=>isset($_GET["select"]))))."<td><input name='field_vals[]'>"."\n";echo"</table>\n";}echo"<p>\n";if($n){echo"<input type='submit' value='".'保存'."'>\n";if(!isset($_GET["select"]))echo"<input type='submit' name='insert' value='".($wj?'保存并继续编辑':'保存并插入下一个')."' title='Ctrl+Shift+Enter'>\n",($wj?script("qsl('input').onclick = function () { return !ajaxForm(this.form, '".'保存中'."…', this); };"):"");}echo($wj?"<input type='submit' name='delete' value='".'删除'."'>".confirm()."\n":"");if(isset($_GET["select"]))hidden_fields(array("check"=>(array)$_POST["check"],"clone"=>$_POST["clone"],"all"=>$_POST["all"]));echo
input_hidden("referer",(isset($_POST["referer"])?$_POST["referer"]:$_SERVER["HTTP_REFERER"])),input_hidden("save",1),input_token(),"</form>\n";}function
shorten_utf8($Q,$y=80,$vi=""){if(!preg_match("(^(".repeat_pattern("[\t\r\n -\x{10FFFF}]",$y).")($)?)u",$Q,$A))preg_match("(^(".repeat_pattern("[\t\r\n -~]",$y).")($)?)",$Q,$A);return
h($A[1]).$vi.(isset($A[2])?"":"<i>…</i>");}function
icon($Qd,$B,$Pd,$Ti){return"<button type='submit' name='$B' title='".h($Ti)."' class='icon icon-$Qd'><span>$Pd</span></button>";}if(isset($_GET["file"])){if(substr(VERSION,-4)!='-dev'){if($_SERVER["HTTP_IF_MODIFIED_SINCE"]){header("HTTP/1.1 304 Not Modified");exit;}header("Expires: ".gmdate("D, d M Y H:i:s",time()+365*24*60*60)." GMT");header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");header("Cache-Control: immutable");}@ini_set("zlib.output_compression",'1');if($_GET["file"]=="default.css"){header("Content-Type: text/css; charset=utf-8");echo
lzw_decompress("h:M��h��g�б���\"P�i��m��cQCa��	2ó��d<��f�a��:;NB�q�R;1Lf�9��u7&)�l;3�����J/��CQX�r2M�a�i0���)��e:LuÝh�-9��23l��i7��m�Zw4���њ<-��̴�!�U,��Fé�vt2��S,��a�҇F�VX�a�Nq�)�-���ǜh�:n5���9�Y�;j��-��_�9kr��ٓ;.�tTq�o�0�����{��y��\r�Hn��GS��Zh��;�i^�ux�WΒC@����k��=��b����/A��0�+�(���l���\\��x�:\r��b8\0�0!\0F�\nB�͎�(�3�\r\\�����Ȅa���'I�|�(i�\n�\r���4O�g@�4�C��@@�!�QB��	°�c��¯�q,\r1Eh��&2PZ���iG�H9G�\"v���������4r����D�R�\n�pJ�-A�|/.�c�Du�����:,��=��R�]U5�mV�k�LLQ@-\\����@9��%�S�r���MPD��Ia\r�(YY\\�@X�p��:��p�l�LC �������O,\r�2]7�?m06�p�T��aҥC�;_˗�yȴd�>��bn���n�ܣ3�X���8\r�[ˀ-)�i>V[Y�y&L3�#�X|�	�X�\\ù`�C���#��H��2�2.#���Z�`�<��s����Ò��\0u�h־��M��_\niZeO/CӒ_�`3���1>�=��k3����R/;�/d��\0�����ڵm���7/���A�X�������q.�s�L��� :\$�F�������w�8�߾~�H�j��\"�����Գ7gS���FL�ί�Q�_��O'W��]c=�5�1X~7;��i��\r�*\n��JS1Z���������c���t��A�V�86f�d�y;Y�]��zI�p�����c�3�Y�]}@�\$.+�1�'>Z�cpd���GL��#k�8Pz�Y�Au�v�]s9���_Aq���:���\nK�hB�;���XbAHq,��CI�`����j�S[ˌ�1�V�r���;�p�B��)#鐉;4�H��/*�<�3L��;lf�\n�s\$K`�}��Ք���7�jx`d�%j]��4��Y��HbY��J`�GG��.��K��f�I�)2�Mfָ�X�RC��̱V,���~g\0���g6�:�[j�1H�:AlIq�u3\"���q��|8<9s'�Q]J�|�\0�`p���jf�O�b�����q��\$����1J�>R�H(ǔq\n#r����@�e(y�VJ�0�Q҈��6�P�[C:�G伞���4���^����PZ��\\���(\n��)�~���9R%�Sj�{��7�0�_��s	z|8�H�	\"@�#9DVL�\$H5�WJ@��z�a�J �^	�)�2\nQv��]�������j (A���BB05�6�b˰][��k�A�wvkg�ƴ���+k[jm�zc�}�MyDZi�\$5e��ʷ���	�A��CY%.W�b*뮼�.���q/%}B�X���ZV337�ʻa�������wW[�L�Q�޲�_��2`�1I�i,�曣�Mf&(s-����Aİ�*��Dw��TN�ɻ�jX\$�x�+;���F�93�JkS;���qR{>l�;B1A�I�b)��(6��r�\r�\rڇ����Z�R^SOy/��M#��9{k���v\"�KC�J��rEo\0��\\,�|�fa͚��hI��/o�4�k^p�1H�^����phǡV�vox@�`�g�&�(����;��~Ǎz�6�8�*���5����E���p����Ә���3��ņg��rD�L�)4g{���峩�L��&�>脻����Z�7�\0��̊@�����ff�RVh֝��I�ۈ���r�w)����=x^�,k��2��ݓj�b�l0u�\"�fp��1�RI��z[]�w�pN6dI�z���n.7X{;��3��-I	����7pjÝ�R�#�,�_-���[�>3�\\���Wq�q�J֘�uh���FbL�K���yVľ����ѕ�����V���f{K}S��ޝ��M���̀��.M�\\�ix�b���1�+�α?<�3�~H��\$�\\�2�\$� e�6t�Ö�\$s���x��x���C�nSkV��=z6����'æ�Na��ָh��������R�噣8g�����w:_�����ҒIRKÝ�.�nkVU+dwj��%�`#,{�醳����Y����(oվ��.�c�0g�DXOk�7��K��l��hx;�؏ ݃L��\$09*�9 �hNr�M�.>\0�rP9�\$�g	\0\$\\F�*�d'��L�:�b���4�2����9��@�Hnb�-��E #Ĝ����rPY�� t� �\n�5.�����\$op�l�X\n@`\r��	��\r���� � ���	������ �	@�@�\n � �	\0j@�Q@�1\r��@� �	\$p	 V\0�``\n\0�\n �\n@�'����\n\0`\r����	��\r���\0�r����	\0�`�	���{	,�\"��^P�0�\n��4�\n0���.0�p���\rp�\r��p���p��q�Q0�%���1Q8\n �\0�k�ȼ\0^���\0`��@���>\n�o1w�,Y	h*=����P�:іV��и.q����\r�\r�p���1��Q	��1� �`��/17����\r�^��\"y`�\n�� �#��\0�	 p\n��\n��`� �r �Q��b�1��3\n��#��#�1�\$q�\$ѱ%0�%q�%��&�&q� �&�'1�\rR}16	 �@b\r`�`�\r��	�����d���	j\n�``��\n��`dcсP��,�1R��\$�rI�O �	Q	�Y32b1�&��01��� �� f��\0�\0���f�\0j\n�f`�	 �\n`�@�\$n=`�\0��v nI�\$�P(�d'�����g�6��-��-�C7R��� �	4��-1�&��2t\r�\"\n 	H*@�	�`\n � �	��l�2�,z\r�~� �\r�F�th�������m����z�~�\0]G�F\\��I�\\��}It�C\n�T�}���IEJ\rx����>�Mp��IH�~��fht��.b��xYE��iK��oj�\n���L��tr�.�~d�H�2U4�G�\\A��4��uPt����谐����L/�P�	\"G!R��Mt�O-��<#�APuI��R�\$�c���D�Ɗ����-��G�O`Pv�^W@tH;Q��Rę�\$��gK�F<\rR*\$4���'�����[��I��Um��h:+��5@/�l�I���2���^�\0OD�����\rR'�\r�TЭ[����Ī��MC�M�Z4�E B\"�`���euN�,䙬�]��t�\r�`�@h��*\r�.V��%�!MBlPF��\"��&�/@�v\\C��:mMgn����i8�I2\rp�vj����+Z mT�ue��fv>f�И�`DU[ZT�V�C�T�\r��Uv�k�^���L��b/�K�Sev2�ubv�OVD��Im�\$�%�X?ud�!W�|,\r�+�cnUe�Z��ʖ����-~X��������BGd�\$i��Mv!t#L�3o�UI�O�u?ZweR���cw�.�`ȡi��\rb�%�b���H�\"\"\"h�_\$b@�z��\0f\"��rW��*��B|\$\$�B�נ\"@r��(\r`� �C���(0&�.`�Nk9B\n&#(���@䂯��d��^����� �@�`�I-{�0��\n�B�{�4sG{��;z��b�{ �{b�ׯ�){B��xK���Ň5=cڪ��y��&�J�Pr�I/��� \0��V\r�׉��=����N\\ئ=�K��}XV�x�����إ�ˋx��d�Պی*H'�δ��{X�=��=\0�8�\0����[ɫ�J��t��O�e����ɋ��\r�����DX���Ň��}�z������)�y'��'��я�I��(�[�l(5�`f\\�`���e�.lY(�=z�ה!�Y%h��O�+����`ٙ\"e� ��ė���K������������ߚ�#�S��E�I�Y����.H�JtG���`��H�J5���5��~ ��6C��h����XDz\n�x��ysh���FK�c�zj�Z�Y8(��%�|y�I��ߑ؃���e��Y�X���u�� ��i�]��c���M��;�ȧ���>ǡ��Q�T����� [~W�~��c݂z�����z�����\r�:  \0�rY��x)��!��ɡ�K��+�z!��ӀC+����ٮ�ï:ݎ�������Zg��~z4f��	�:����s�Ӫ��+��x�%����=��G��I�f3?������+Y��q�@��G���y��o��Ѵ�p\r�~�{W���[����y�:\0�\\���;e�ۡ�YI\"��zdk�Z�|[u��u��+�׹9q��nR ˮ�B����ׁz|\r�ᤄ��k�^��[1��%�.��pA�2<��=�ء��\$�;�5�)��m��!���XX���Y�x�5vT\\�Q�%:��>��ɛ�;��e�|/���y����W��xנ|g������C��\\�����<��9z\\�#�.FV;8��N�X7����\"8&d5�P�4Gj?�\0�?\"=���HER");}elseif($_GET["file"]=="dark.css"){header("Content-Type: text/css; charset=utf-8");echo
lzw_decompress("h:M��h��g���h0�LЁ�d91�S!��	�F�!��\"-6N����bd�Gg���:;Nr�)��c7�\r�(H�b81��s9���k\r�c)�m8�O��VA��c1��c34Of*��-�P��1��r41��6��d2�ց���o���#3���B�f#	��g9Φ�،fc\r�I���b6E�C&��,�bu��m7a�V���s��#m!��h��r���v\\3\rL:SA��dk5�n������aF��3��e6fS��y���r!�L��-�K,�3L�@��J��˲�*J��쵣����	������b�c��9���9���@����H�8��\\���6>�`�Ŏ��;�A��<T�'�p&q�qE��4�\rl���h�<5#p��R �#I��%��fBI��ܲ��>�ʫ29<��C�j2��7j��8j��c(n���?(a\0�@�5*3:δ�6����0��-�A�lL��P�4@�ɰ�\$�H�4�n31��1�t�0��͙9���WO!�r��������H����9�Q��96�F���<�7�\r�-xC\n ��@�������:\$i�ضm���4�Kid��{\n6\r���xhˋ�#^'4V�@a��<�#h0�S�-�c��9�+p���a�2�cy�h�BO\$��9�w�iX�ɔ�VY9�*r�Htm	�@b��|@�/��l�\$z���+�%p2l���.�������7�;�&{��m��X�C<l9��6x9�m�������7R��0\\�4��P�)A�o��x���q�O#����f[;��6~P�\r�a��T�GT0���u�ޟ���\n3�\\ \\ʎ�J�ud�CG���PZ�>����d8�Ҩ������C?V��dL��L.(ti���>�,�֜�R+9i��ޞC\$��#\"�AC�hV�b\n��6�T2�ew�\nf��6m	!1'c��;��*eLRn\r�G\$�2S\$��0���a�'�l6�&�~A�d\$�J�\$s� �ȃB4���j�.�RC̔�Q�j�\"7\n�Xs!�6=�BȀ}");}elseif($_GET["file"]=="functions.js"){header("Content-Type: text/javascript; charset=utf-8");echo
lzw_decompress("':�̢���i1��1��	4������Q6a&��:OAI��e:NF�D|�!���Cy��m2��\"���r<�̱���/C�#����:DbqSe�J�˦Cܺ\n\n��ǱS\rZ��H\$RAܞS+XKvtd�g:��6��EvXŞ�j��mҩej�2�M�����B��&ʮ�L�C�3���Q0�L��-x�\n��D���yNa�Pn:�����s��͐�(�cL��/���(�5{���Qy4��g-�����i4ڃf��(��bU���k��o7�&�ä�*ACb����`.����\r����������\n��Ch�<\r)`�إ`�7�Cʒ���Z���X�<�Q�1X���@�0dp9EQ�f����F�\r��!���(h��)��\np'#Č��H�(i*�r��&<#��7K��~�# ��A:N6�����l�,�\r��JP�3�!@�2>Cr���h�N��]�(a0M3�2��6��U��E2'!<��#3R�<�����X���CH�7�#n�+��a\$!��2��P�0�.�wd�r:Y����E��!]�<��j��@�\\�pl�_\r�Z���ғ�TͩZ�s�3\"�~9���j��P�)Q�YbݕD�Yc��`��z�c��Ѩ��'�#t�BOh�*2��<ŒO�fg-Z����#��8a�^��+r2b��\\��~0�������W����n��p!#�`��Z��6�1�2��@�ky��9\r��B3�pޅ�6��<�!p�G�9�n�o�6s��#F�3���bA��6�9���Z�#��6��%?�s��\"��|؂�)�b�Jc\r����N�s��ih8����ݟ�:�;��H�ތ�u�I5�@�1��A�PaH^\$H�v��@ÛL~���b9�'�����S?P�-���0�C�\nR�m�4���ȓ:���Ը�2��4��h(k\njI��6\"�EY�#��W�r�\r��G8�@t���Xԓ��BS\nc0�k�C I\rʰ<u`A!�)��2��C�\0=��� ���P�1�ӢK!�!��p�Is�,6�d���i1+����k���<��^�	�\n��20�Fԉ_\$�)f\0��C8E^��/3W!א)�u�*���&\$�2�Y\n�]��Ek�DV�\$�J���xTse!�RY� R��`=L���ޫ\nl_.!�V!�\r\nH�k��\$א`{1	|�����i<jRrPTG|��w�4b�\r���4d�,�E��6���<�h[N�q@Oi�>'ѩ\r����;�]#��}�0�ASI�Jd�A/Q����⸵�@t\r�UG��_G�<��<y-I�z򄤝�\"�P��B\0������q`��vA��a̡J�R�ʮ)��JB.�T��L��y����Cpp�\0(7�cYY�a��M��1�em4�c��r��S)o����p�C!I���Sb�0m��(d�EH����߳�X���/���P���y�X��85��\$+�֖���gd�����y��ϝ�J��� �lE��ur�,dCX�}e������m�]��2�̽�(-z����Z��;I��\\�) ,�\n�>�)����\rVS\njx*w`ⴷSFi��d��,���Z�JFM}Њ ��\\Z�P��`�z�Z�E]�d��ɟO�cmԁ]� ������%�\"w4��\n\$��zV�SQD�:�6���G�wM��S0B�-s��)�Z�c|�^R��E�8kM���s�d�ka�)h%\"P��0nn��/��#;��g\rd��8��F<3\$�,�P);<4`��<2\n����@w-��͗A�0�����Lr�Yh�XC�a�>��t��L��2�yto;2��Q��t��frm�:��A�����AN��\\\"k�5oV�Ƀ=��t�7r1�p�Av\\+�9���{��^(i��f�=�r����u���t�]y�ޅ��C���������gi�vf���+�Ø|��;�����]�~��|\re��쿓�݂�'�����������	�\0+W��co�w6wd Su�j�3@���0!��\n .w�m[8x<��cM�\n9���'a���1>���[���d��ux��<\"Y�c��B!i���w�}��5U�k�����]������{�IךR����=f W~�]�(bea�'ub�m�>�)\$��P��-��6��R*IGu#ƕUK�AX�t�(�`_��\"���p� &U���I��]��YG6P�]Ar!b� *ЙJ�o��ӯ�������v��*���!�~_���4B���_~RB�iK����`�&J�\0���N\0�\$�����C�K �S���jZ�����0pvMJ�bN`L��e�/`RO.0P�82`�	����d Gx�bP�-(@ɸ�@�4�H%<&���Z����p����%\0�p��Є���	��	��/\"��J��\ns��_��\r��g�`��!k�pX	��:�v��6p\$�'���RUeZ��d\$�\nL�B���.�d�n����tm�>v�j��)�	M�\r\0�.�ʊH��\"�5�*!e�ZJ�����f(dc��(x��jg\0\\������ Z@���|`^��r)<�(������)������@Yk�m��l3Qyс@���ѐf��Pn�����T��N�mR�q���Vmv�N֍�|�ШZ��Ȇ�(Yp��\"�4Ǩ���&��%�l�P`Ā�Xx bbd�r0Fr5�<�C��z���6�he!��\rdz���K;�t��\n�͠�HƋQ�\$Q�Enn�n\r���#�T\$��ˈ(ȟѩ|c�,�-�#��\r���J�{d�E\n\$��Br�iT��+�2PED�Be�}&%Rf��\n��^�C��Z�Z RV��A,�;���<���\0O1���c^\r%�\r ��`�n\0y1��.��\r�ĂK1�M3H�\r\"�0\0NkX�Pr��{3 �}	\nS�d��ڗ�x.Z�RT�wS;53 .�s4sO3F��2�S~YFpZs�'�@ّOqR4\n�6q6@Dh�6��7vE�l\"�^;-�(�&�b*�*��.! �\r�!#�x'G\"�͆w��\"�� �2!\"R(v�X��|\"D�v��)@�,�zm�A�wT@��  �\n����ЫhдID�P\$m>�\r&`�>�4��A#*�#�<�w\$T{\$�4@��dӴRem6�-#Dd�%E�DT\\�\$)@��WC�(t�\"M��#@�TF�\r,g�\rP8�~��֣J��c����ĹƂ� ʎ\"�L�Z��\r+P4�=���S�T�A)�0\"�CDh�M\n�%F�p���|�fLNlFtDmH����5�=H�\n��ļ4���\$�K�6\rbZ�\r\"pEQ%�wJ��V0��M%�l\"h�PF�A��A㌮�/G�6�h6]5�\$�f�S�CLiRT?R���C����HU�Z��YbF�/�.�Z�\"\"^�y�6R�G ���n��܌�\$���\\&O�(v^ �KU�Ѯ��am�(\r������\$_��%�+KTt��.ٖ36\n�c��:�@6 �jP�AQ�F�/S�k\"<4A�gA�aU�\$'����f��QO\"�k~�S;����.��:��k��9�����e]`n���-7��;��+V��8W��2H�U��YlB�v��⯎�Ԇ����	����p���l�m\0�4B�)�X�\0��Q�qFSq�4��nFx+p��E�Sov�GW7o�w�KRW�\r4`|cq�e7,�19�u��u�cq�\"LC�t�h�)�\r��J�\\�W@�	�|D#S\r�%�5l�!%+�+�^�k^ʙ`/�7��(z*񘋀���E��{�S(W��-�Xė0V��0�����=��a	~�fB�˕2Q���ru mC�����t�r(\0Q!K;xN�W������?b<�@�`�X,��`0e�ƂN'�����&~��t��u�\"| �i� �B� 7�R�� ��lSu��8A��dF%(�������?3@A-oQ�ź@|~�K���^@x��b��~�D�@س�����TN�Z�C�	W���ix<\0P|��\n\0�\n`�����\"&?st|ï�w�%����md�u�N�^8�[t�9��B\$�������'\">U�~�98����ÔF�f ���u����/)9����\0��A�z\"FWAx�\$'�jG�(\"� �s%T��H����e,	M�7�b� ǅ�a� ˓�ƃ�&wY�φ3���� /�\rϖ�����{�\"�ݜp{%4b��`팤��~n��E3	������9��3X�d���ՏZ��9�'��@����l�f����Q�bP�*G�o���`8������A��B|�z	@�	��b�Zn_�h�'ѢF\$f���`��HdDd�H%4\rs�AjLR�'��f�9g I��,R\\����>\n��H[�\"���\rӁ����L�,%�FLl8gzL�<0k�o\$�k��`��KP�v�@d�'V�:V��M�%���@�6�<\r��T���LE��NԀS#�.�[�x4�a�̭�LL����\n@��\0۫tٲ�\n^F�������5`� R��7�lL�u�(��d���� �\r�Bf/uCf�4�cҞ B���_�nL�\0� \$��aYƦ���~�Uk�v�e�˥�˲\0�Z�aZ����Xأ��|C�q��/<}س���ú���� Z��*�w\nO��z`�5��18�c����������I�Q2Ys�K�����\n�\\��\"�� ð�c��*�B����.�R1<3+���*�S�[�4�m쭛:R�h��ITdev�I�H���-Zw\\�%n�56�\n�W�i�\$�ōow��+�����r��&Jq+�}�D����j��d��?�U%BBe�/M��Nm=τ�U��b\$HRf�wb|��x d�2�NiS���g�@�q@��>�Sv�������|�kr�x��\0{�R�=F������#r��8	��Z�v�8*ʳ�{2S�+;S���Ө�+yL\$\"_��B�8��\"E�%������\n����p�p''�p��wUҪ\"8бI\\ @���ʾ �Ln���R�#M�D��q�LN��\n\\��̎\$`~@`\0u�~^@��l�-{5�,@bru�o[�����}�/�y.�� {�6q��R�p��\$�+1�3����+��O!D)����\nu�<��,����=�Jd�+}��d#�0ɞc��3U3�EY���\r��tj5ҥ7�e��wׄǡ���^��q߂�9�<\$}k���RI-���+'_Ne?S�R�hd*X�4��c}��\"@��vi>;5>Dn� �\r��)bN�uP@Y�G<��6i�#PB2A�-�0d0+���gK����?�n���d�d�O������c�i<����0\0�\\����g����ꡖ��NTi'����;i�mj�܈�����u�J+�V~����'ol`����\",�������F��	��{C�����T a�NEۃQ�p� p��+?�\n�>�'l��* t�Kάp�(YC\n-q̔0�\"*ɕ�,#���7��\"%�+q���B��=�i.@�x7:�%GcYI��0*��Ðk�ۈ�\\����Q_{����#��\r�{H�[p� >7�ch�n����.����S|&J�MǾ8��m�Oh���	��qJ&�a�ݢ�'�.b�Op��\$�����D@�C�HB�	��&�ݡ|\$Ԭ-6��+�+ �����p��ଡAC\r�ɓ��/�0�����M��iZ�nE�͢j*>��!Ңu%��g�0���@��5}r��+3�%��-m��G�<���T;0�����DV�d�g�9'lM��H�� F@�P��un�tFB%�M�t'�G�2��@2�<�e��;�`��=LX�2���X�}oc.L�+�xӎ�&D�a����ɫ�F2\ngL�E��.\\xSL�x�;lw�D=0_QV,a 5�+L��+�|\$�i�jZ\n��D�E�,B�t\\�'H0����R~(\\\"��:��n*���(��o�1w��Q��r���E�te�F��\$�Sђ]�\rL�yF���\\B�i�h��hd��&ᚇh;fo��B-y`���0��J�lP�xao�\$�Xq�,(���C*	��:�/����HG\"��c��C���Q�\nF�Ԅ�#�8�F:У\0��Ok��D��])�ϚtT8L�𒨔�n�`���|�HJ���� �� \"�6�{����?=I<HGc ŤF�@�,C ��@j�\$L���(�nEʑP��jb�n�Α���W� \r�Lq����sPH�ꉝz\\V\$k�ҏtr5�,��l����<�'\0^S02�0f -5\"ac�\"3U�p��\"ܘ�%��\0'Zt\"96��9_ @Z{�0I��D�ZE@��N�h`�\"�`�\0�����ɹ(G�H��Ch� �I��f`@ZD�\$)�K�;Z��\0�/�C�T>r_R@O�`1r�TҨIb\0�*�8�����h\$�_�p�Rĕ\$��Ni^ʪP/O)��.ŹT6�\\�ٔ@T���rą`)���T=�n\0��2��e�+�9ʢ\\��@�����>�PH�1	�y#���r�<�a�e�K��/�c�M@_.\09ˈ��������B����0i���a�\n��de�a�%|S2�����#����n��D�\$/�+E�d����_2P��\$s,ok�#�<�	�A�đr{B���A-Q4Ҥ�\n�\ry�!�b䱎���O��@ɬ��k�� �\"�r��*�݇��Y��/��ȑ a0��%�.gE~��&� 89����#@M_ ���7K䃸J`�X)�B\$�(	:�g��n*�|�M6PZ��Ht�Jtq�Cx�[ڼ����l=\n���U3�f\\̔J�P	,�:�}TA�SYH(�\n���I�ٲ�!t(2U\"�\\�X�^s�	��a!�\nPr��`�X3fnb�����J���&�z�zQSf ���t�!T?�9%�(Q��B�}6B�kP\0�>�g�&~fhU�r��,� p5Hi��p����qɚ�g�V�V��Og�WEJ8�0G��ak���@N NM��U�UxȪ��S�x	��	�K�@c�1y�VlϠ��C����2Q^rP6|�I^M�,�j%d�`ܫ��F��\\#%�|�C����7싢�G�TN�����i��H���Q�O���C�yB��\$�%T���*�>z\r�MM Kp� ��J7O۷�4�%�\$�p����4������͂��EҪ\"T��\0O�\0��@>	r�O�]���x�}^�I��@� źqn��0�Bb�ȵ�I�(�M/�;���}RN\n�C�<�b�PԵu?�=Pe�C����L^'�S��?}4)��S-���1\r5S�OE�SF����AOR+�ޙ+v��5�&C)ِ��KSDB߳N|E\rc�U�Yʾ���V���?H�)実+sF��k�LPW-�,�U:�&��t{��Vo���J�l'��W�e74X�n GF�'���`��Cc��%Il�j�u6����v�U��Z�\0*���Nԟ#��(���n�-;|��4�]X���y'����;��Z���) s9����%��R+\$��	��Q��(\"�_kX��������\nM#���\"!p~:�*����\$�3O������6�+���\nB�{1��|H�K<[`3��#��F@��ǐ! |�؊\0��>�����[nrMM�+��mO_�2��Ȇ�\0�e^	�7Z�&�B�J褓h7QO%rf�p��΁�֞�m�ب�Ç�4E�l���+���V��i�N S�Z�Wt�2W�[;��v\"%��\$^�-(I\$��S@R-&�T�z��k(��	�%R8�uY\0[9-���(�)E��8�=^����G�5#����)�1V��b\r]�Ne;&�Y�`r��I��Pݱ���ֲ��\0�@P�7���0H���؍R�x�\0000C|�n=��`��TT��\rEhON���'��&�tc�K ��ܕU5��������P3\\��2\"\0y�5�V]���6>�U!��@�hu��(�\"E%07B��6��d�HN������ij';@��e�MzlSfjKY�֍���-uh��H���smL@��\"r�j���j'l7	�(u�u��E��e�a�@�+�K�:ӕ�%n�z�V���;�[�_Vz_��E���8�<�Sb�������6g��:c����7\n����%Q�� K�7�ܮB����w�u�5��0��֚���y�ncnK����T8�ʙ�s��W=+�=K\n_[p�G���C5����'�D\"��M<\":|Mq4���f�s�x	�qlͰ��QP��aOY�E=���6nT떒�Bt�h�C\0p��@n��D(a�P�\"���'ZN��۬��\r�LNX�g��<!w�����[��B)��)~���c�x��v�i¦�q�����a�@K��7s�EQdý��k����?\"�3�-\"U��|������|21D>߳�]­&���\\h�TƳ5�\0`Tz���s -�N����\"�f��N�LU�]n(D�(��&%\"�e\\��O��N�Inۿ��\0����ƕ���@����V�|R�MYC�T����b�UH�p)���S�s� q�i���`Z5vt坉�*�OO\n�(�����F��58�!ax@�{^P����?���eh}\\�j^2�L�,6�.�N	K�%����u���ip��!?�l��� -5�w���K\"V��\\�Is��2!��\$4�5v\n�����gr��N��}��;��������W%D(pWa�\0�v'��6��V��ƿ0W��E4�EUl�8�LD��E�<kO��H��DU�	`vS��L��!DTMbnWV��Cd��)Ze蟀���:�2�d8��K�ބ�4�-G�b;wQW�30\r�f\0�,�`Qhl�֍�0�P��0h@\\�r�8��T���⛜�1�`�&���w�X�>�F?��|P�*�M�qZѯ��}��0k`��#�իc�'[�ֱˍ|s�IJ��\r����<OaƼ@�W��u�T��:��E^������!k�����a\$�>5��u_��KcCQ�r-ъ�'\r�iC������@8�S�PS�_Xgl�%�	�n1r.<�w_aɺĳ�Gh�4\n�W�Z��aBn,\\\0���DU�\nbbZ'���72���r�¢��}�Y>/�w\\Y�`^7J�j�S�������S.��o%�Jg\0GD,���>7���R�0������3��6�%i\0S�^L��A��\ri��O<���a phv[�{���\0�E�^x�ܼg�YzW�yG�a��:(�>C�����e\0���])�3yts_a�7�+��B��C�eT��f�o�P����2E�C��v�>�w�l�z�*p�Y����q�����Q�p\nv[|q�ҨE[�Xi���=�z(	�M�n�]7F\r��Cs4|-} ���Ŀ(NU�?,��څ��������q	��p�q~��� ��F��%�88��靦��\$�ް�[���r�o!3��(����g���ץpJ!���q�Z�v?��c���L��7��6��\$�m���q��8l!��5�C�;Q,��d�sF�-O��fÈ�\$���6�%U�C��f\"��e(j�\rMt�F����R�x;n�B\$��SS�x'��G��陊M�	��4ͬ'k��~��#9e��Y���~��뭈;f�+�j�K�9p���M�'X�/rt�\0�\\�J%Q���R�\rвO3�|�寚���ϱ�4��xF���s5E�Ԑ;ԒWR��JX�ʶ�J�\$��wzO��&ǵ��z�k�S�\n�\nNUP���.��0���bdk��P���	G6�+B�z�1ΎhQ>sHv�����Q�٠E�p��M��)��\n�\\�ў�Pz���.s��� g��)a~��ȥ�!(!�G�hr[�*�����բ�`��~�\"!�O���5�G3Ş*qkgB�,\$���**1�c.�n	8��\$d���VSne�MiZ���7žg�A�5�����\n�`�,�2��a�ү��mMkʻ��ɯ��/-��6�@?#`��)�Ԁ�ha���)Vc�]�_=�Rz\\�VR��=�ط�(-�ot�\$ܥ�\n���dSm�y��fө�N\r�m(t;D���p�2�ݶ��ZRl)�9M̛�,/��Yix��kя)�.�2@S^���u���d�6�!��>VB�� x<��Kt06���@��\nG�A�P�(��NbD��K\n�\"��cN��\ră.p���'2L��d�ꟲ���\\Ly�A=	��D��m3�%�@��������8�qbSP\"�ޢ�Ʈ/�Dz�C&�O��\0007f��D^1�X��/��,\n��v�Wx%f)��' �D�dQ@��I(ҋ7Y��|���A�Q��D��ڠe 8ׇ7k)_ �@\"\"��%�}�	�(��1�1؍�\r����e���?-ɵH��&�����\rL���'�eۮ0�T�]��C!�emNz�	Uz���Ɉ���S�ܜaf�7�M�^C�D���(_������#\"�dr5�9���81��hf�ȭ�a_�×tZX\0�U����{2nn]��;FR��!�}>s�Hi��y#���?\"Ť�����>{���/?7�F��Y����?Aj��.�U�!5`H��\$r\0��'\n�\":.��dԂٙƪ�q�Rխoh��>���{��1��+�>����t��k�%-D�=9�}�C@�8cm�Hr���W�n��\0Ď<(�RR�8����YV��`�pp�.U�e_`����^���쵛n^�_�R|�r΅p�7/!M5���|���\n�&�F��VVz��O�A�~ш|ƛ��4NȒ��Ք��g�yh-���\nN\"r\"���Gc�s����D�'�Xo٧���O�{��{Y{��E�=T�e�Z������{\";�H��Xz�t��w�*-����U���w�-��\"��<A^�O��T �]�D?:���������<��p�q�[���,)�&`�{xKI�I`�`��c��0����D�y8���qC��Y��CF���J���nk�[�8����:\n^�ց��T�!X*M�<�5`\0��6A�2o�P.��a�AH��#x[�����▞�� '�o@��O0^���h|�P�=+�)�d[����X-��W�!����Æ�/:\"�0k#XǞ<����h�CG�ݠ@F�(�k����l�&H�F0OSz���w�Q��3���z|+��\r9b�T�}'ܬwA�\r�nF�����!�g0�lp��l�1�+�|�h�kz��i&��u�D�{K��\\���\$t(�;���ì��H�r|Bw�D3[M�!:(�{�Z��(|-�Hy0�^�'׽�}�*����NK������5KU���jM�\"��w�]%���{1q��z���)]�Ů[k�\0O4������UF�\0�c���mZEGt�sDQZ�)n;7�<�qhlXx�I��^�V��&�ͷ�C�`,ɑ%��1\"@1�|�)�R�k��V��}S,�#!��G���]��Ex���YT��<%�Qѿ�@�����m���Jc��B��B i����G��f2����cD��nէ�=J���I_�������'����iA�&,��{��c��4��oV�%�d�2�x�e���#s_U�H�ՉW�!  =۷�O�<(y\0�.��G�'�\r����57�pV�(�þ:��}�RRHHy[��	����� 1����O\")��L�l���1�������������+<~�	\0���s���?�B@��d�����?n��~�&LЄ��?���@:@;��y���Q�>�����f���:\0�t�+j�sz�K�,b^�p���HX�?�P�\\D�?v\"�����\"�&� ?�����t��`�V?�\0���J�wC1O���#�Ɛ�*	��@̿�\0���Ƈ���/#8\"�O�\"�\0���6�Nc�ä�[�p@C�h\0{\0	�pDO��Ft��H/!h@��L�;�@���w���I��~C�ˀ¸)�E��4+���)���Eb�?]�d��\$�<���`o������?}�8�b���/�J���o#��IV,Ac��3�Xa ��o�xi����\"椌CU���D�k�YȊ�}�\n\r\0,G�\0�|q�� �.Ŋ���N�q�pN�Д�jBO\$|C�p}��4`����\\*4��bA���+�D_������X�\$�����@��6\n\0\$�~ˣ�\0��Jb݅��� U�p�X�iD\"�ێ��lg�t'���� �+x�<���N��51e���0`��B8q�\"O- 	C!�Қ�mɵ����*��f@#�6�ZЛ9���ZR�ǁ������	HZL� e����9�9�� T n��?xX\$0��%\0002�\n�y�!��e�:\$�QssA��nxK���l1'��Nz!p���.Ṇ�c�p���1@��)m�:@P�\0�1\n�(CR�5D(���P�1#	�d7�+\n��Bu��ha�M	a�\0�>�1W���\0a�4 s�-ׂ'�jp���\nJmQ����)�");}elseif($_GET["file"]=="jush.js"){header("Content-Type: text/javascript; charset=utf-8");echo
lzw_decompress("v0��F����==��FS	��_6MƳ���r:�E�CI��o:�C��Xc��\r�؄J(:=�E���a28�x�?�'�i�SANN���xs�NB��Vl0���S	��Ul�(D|҄��P��>�E�㩶yHch��-3Eb�� �b��pE�p�9.����~\n�?Kb�iw|�`��d.�x8EN��!��2��3���\r���Y���y6GFmY�8o7\n\r�0�<d4�E'�\n#�\r���.�C!�^t�(��bqH��.���s���2�N�q٤�9��#{�c�����3nӸ2��r�:<�+�9�CȨ���\n<�\r`��/b�\\���!�H�2SڙF#8Ј�I�78�K��*ں�!���鎑��+��:+���&�2|�:��9���:��N���pA/#�� �0D�\\�'�1����2�a@��+J�.�c,�����1��@^.B��ь�`OK=�`B��P�6����>(�eK%! ^!Ϭ�B��HS�s8^9�3�O1��.Xj+���M	#+�F�:�7�S�\$0�V(�FQ�\r!I��*�X�/̊���67=�۪X3݆؇���^��gf#W��g��8ߋ�h�7��E�k\r�ŹG�)��t�We4�Vו��\rC+����8\r�\0a�Rؾ7��0���^v�6�n��xP\\����@y��A�R��� �o��`�K~f����\n�{�f9�����ť��~�!�`���@C�.�A��޺.�����9���z��\n�l�빨w~��\${XHpɉ�ح/��Ԥg����=Ѥ�c�H�f�d��%j�����c5�^cH{\$��\n��\r!�4��n����6���cH���[�.6��`ӥ��λ�\\7��������W��ޔ>�}���hW���^����L����ژ� rY_���WV:@v\n����øi�4�0�B���E��*`z|ڑ���\"�����C(m��ΈQ�\$X����awK���- �M0�՚�^��?�\"\r��t\r��h�� ���}���y��z��Ƀ�I? �b�wA?���щ��A����h�d6���A��^2a�˃��Z�R ��иհ(�,>�����)���ޑ�K��2��!C���l�\"�\$!�@[fzXHytA�'X��Sʒ%�H\n�4&4�VSZ_������3�RlM�M}��\$R:�NiՁ@崿�,���c�Y�2�c�g�p::؁ Hh�PZ`�F8�A�W�^�|CH+g���\"8G�\0t��������\n�t��&\n#�s����Ԗ�u�'N���0ب�\$')�/`�	\$\0\\D05�6K㲿=A~B�E�Z�ʖ�٤��@�Y�;'n�S�=D2���l�'/�j���x�i&�{WнY;�q\"D�A8a^1\r�1��l�x�)傄�*R:p �*P{��0�r��i���2f��vM3ק����!�d�Q3��<\n9�No���4Sjo���6\0D	�	����hKYj��=I�xU-�u�\$�ܹ��B���n�{�a�\0ra�\\ N�-\0ᆃ���C�wZ��9��+�T!��ª\0Ԡ`��!��.��Z\n�r�P�?�l�he>w�0�A)��_!�7��[0�J��>�TR�)�x�8p牱DM��-E�pq�\"\r���\$q�����Td�Uz����V�L�|�P2§��s鉰)�=�C'�pٔ�E��`ʉ�\"\\��[�/N����|�Foy�&�Y��\"YJ�4�v�{;b��>:�]�9���:�H	[Cv4�hA�1?�&���3�Җg����jL�I�`�7ʀZ^��:��k\0�5|�ՠ�h'2���\$�����`�C��!�D���m]-@�R�}<� 1u�y\r؅`�e��h�c�N�\r=�̴��p6�.��l�夘6�ED�p�}ǹU��Raqւy�����;����Ӽ	p=��7A�=�T�^6��9�#�����6![�E\\�@:���oo���#3��/�^�)�;�{K�. v8�<b�m)�W���\n�2�s��θ]�a'o���%���C���{����x�\\\n��Y�������uh�G\0�hp;|*�[�8���+vj~��ȟ��|r�S˷�x�Lwtwʻ�����=��>��v��\nL1���E\nA�!� �}|�H'�\0��pA	�>0�¹n*�L!���B	��`�l�\0��B!L\"�Ϳ�w���E[sf8�oPݞA�w�)���E����|�#�\\S�sU3���&�>^��{����_ϖj/���bR/�/���L��\n�ΰ�δ�E\$��� �N@<�vzo����m�\0\0�p4�P�.v�Ƭ��\$��M��N��:�0�o6�O����!�.�������0�p̔!��\0\ro�p^��c���	�Z��0k\nP���P�f��|�b�	��%m	���?P�\n�PB��6��.��Эn���\r�����P<�p�� ����pXR`\\�_�����\$M���(�'��N��A(~jÂL&�`Bh\r,�O��`�c�k���٢��)1*����el����d\0р~�fڻƖ`K����\rn�'��qt�`¶�V���W�D: ؐ���6\r���\r���ҀZ�q�۱�xI����|F��bR%k���͆� �@�*�r%\"���+�!bX�rzif���I�P>�\$�E&X�%>��Bm`r^%Rb��Bd�m#�a!�V���!d�����\rˎ�RO'�Χ�o��&�S&R�@�Q�9&��g���q�L���Lc�Gb{j{g�+����ȱ�\n��\0�D��Κ�(��q����L8Ւ��o��Nv��\n��	 �%fP��� �\r�n�Kt��[)�V�g\n\"�Խ�N��\$�W���d��4�'r�\r�#`����\0��ǲ6�@��v�����(�\rr���n�b\$��@��[f��k7�r7`\\	�6�� D�\"�b`�E@�\$\0�RT�&�\"~0��`��\nb�G�)	=>�[?Ji��6ge��`d���k�9�`�����\"�z4&\$n4��z��7�I� ӹC)^ь�6�ĝ��\rŨ���.�sP\r�Ȏ�BP���A+I8`W��NHmG�W4n�P�HT�H��JbT�}H�OA��Hj����Fٔ8N��=���Q7�@7L��f�M�M�z�S`5޾�� ����P��\r�\$=�%4�dnX\n�Xd�ɋ����O�橧�\r��)F�*h���n B��5\$���JLbs�M�9+P\"�N�SW5�_O�c6��#j�\"��ODn��fgrwe�U�%)d�VH�UqO~\$TK`�5�q���H���\"�u��kTUbRCF��\r����/�\r��'�QU��rf�wU����4��� K0r�;3�L�\n�)�����CR��D�F���fX��m[����澵�e�Lq��FE\"��xx\r(���\$�4�S\$)F	J�t\$ʕ\$E(��EF�&T|g Z����8	Z�\\\r/�<��E,agl.��ť6��b������m(�`Z�f�i'��V�׍(:m�e�:��Ϋv>�T�C��7rf֦���nȴ��R6�6��nRVzHOn��\r�D��zc��D�5�TW�s�T�AE��t��7q�n3�)J'l��'L�i�9i֠ J\"k�<�@�<�����^%�:\0�K(Җ햬��tN��,�ipE-(mӺS�~��{K�+M���Ywt9����R\$�z�_��Ҳȡ�2�� �7\0�&�y}�\0(���l\"*>��ؿ�ִ�M��X�@)w�VW�{���@,\0�\"g��\$B�h�c.6(FxP\0\\5b?�π��>w�dܺD��>\r뀔�{�>�7���`�ɂ��󾬨[�Q��][�x�E�`��&dL\r�sl#/ǅ��}�Y]Q�E�\0[���Q��XA���q�1׊�_8\"ؿ/إ]��0�;}��A5���fW�>xJ�(��[��x�-v�����6'L�Z�R����P�!�vV0鑸�-0�\r�7�����%�#vF ��d��%��殚��mesRXw��l��.#\$o�8�=�Z�����tlI����.łt�/!�M�{���lN�,lK��ǹ�̃�,�,��x;�x/iye[�5�Y��Xɗ�{h�=�5RY]Q乇Xy�tֹ�RJ\$JP)�\"5�,����J�K�E}�Q�������\r�;X@�fysxLQx���Y9b�?��8D�o��[]Y��c��-��0��ᢸ�n:�Q�������@��B��K�z?�:Cf��^9�R��GG+���/��]��-�X���}�@O�\n'�Y*�MN�*���-����zD�z���q���ZW�:[��a�9u��xÚm����s�v�,u�0�Ă?�vpջ!ĵ|r	�;��	9���w�V6�y�k�[�f~F���w���f6j-Z����f�ً�_�/�W���k�;c�d�~,�q�H�K�u ƿ��\n\r����M�����kQ��!�@ߡ�����1��͟������+Ǆ7�}�N�%�l^`Ϲ;Bm`��g�[�O��qیxٺ��;}�X����S������8�;�c���x7���W��N��J|�.ە�-�ژ�ڝ�c���(��`��aX�����\n�2�U\\�?�P�\"��z骣��j'��-��=R�A�9Ϸ����D�O�ߥ\\_�X�܊�!���Uޑ���Q�P\r�E��U?�`u���-e;�w�����̆��Zy������=�̈���Y��lC��P\r���9��Q�y�ᬁ�9��9�~P+�\"��EbSv1���������'��c8F�S����[��<��>T;��|��ٵ����0O�}\n�}�9�љ�ę#��Y�3�]�R)��s��2���Ͻ=��C~�ԅ�ǧ���Y�۱�kՄ��k�K�ز��;���7�Q|HZ��{:9\n��|5��l5�|y�k�\r_���f(\0�	��	�a\r*\r�|����̋�ּ�]qd(v���`��\rذ\r�h3�M>93v�A�u����|���#捎^�z~^W��^}�%����d\0rȇ~i�����5`�r��9�E��I�=a�~��w�	~ieu��c�D�uB?����ʬ�AN\n'�2 ?�@T�Cn�fKD�K�M�ԩG6 ZMI�TqG�����_3�V�&T��`�VQ�t�EvZ����:��;��?R��T��n\r(����{p?c�q�?�1�����亚Sh��'��b}`�-�`�!��`�:/h�\$F�~=T��|����m��3^��~B�^�����>��~S�G��U�j\r�,��˦�K�%��jf\0�d'�}���\n��0e+\nyV�M�]��L�(__�v�2jx>\r��7��P>#d?!}�N0~��uEu�A���z��_��vwy�O6x���<ױ?�կeu{�P���9��k����瘤[\"c\\:� j�rn���kL[���������M�2ڸӲ�&�\0��_|���\08�\\�n\n8�V�k\\�`?�\nE�Z�Z�YT��t�S�e�Yn����H�^�����B-@�_e�.�.ܷ�+4�P���b`�B8��J}S��E6l7/%lH_>!P1�<&�JσK%�	���yfX��X��V��#��{ɞ�\\��Y���E����f'�*[F	�����\\��#�C���V�,�!�Hឌ��/�>���ۀ\\�gל\0����pQƎT�==��G�=���{�XF�Lw���Z;�&�4_`mT:������`(4i[:�����d�� Ҭ��`\"ORD V��E�\0�!��}�\0�eV�ĬI��S��ah��\\@���xb�ܥ�'l��/�}K��}@�]�\$x\n��\$�7�\rA�\n{'�PP��D`e�؂U����J	��T:5�|�dg9��T��K��@3��.��81�a�g�<�@b�����/������Pi��&�tE��<r��DpP#����b��ݢ͚!�QqK�c#ȶFR.'<f\0F0�r-Q�z#AظF<�dg��8�\"^5�w�|lT�7�݈�\\�[�{X���.��8(\"�0F���f�vpR���&p���pP.�\$�A�u�.`��2��@��@����;��q��dw\"u(:j�]\0� ��� )�i܀~w �����`��pg\"E�/1��b����E��q�����TU�j�\$���@ϸ۪Q+���,h��W)n=��hL�^�2��9哑4t�7#�.\0��;�dq�Rϖ�G6Fq�����\n�AB����8)�T�2R��!�	�R���N9�'��.��������`{�0���	5��n	�kBd0��I�'Rg4��'�7�pG|��E\0���P\0_��	(�6�&Q`/�i:�yD��.2�T� )��\nT�>\$�e:I@��gì	�\"���5M�0��ke'�N�*L���Į��+�8F^W�p�	��	Y8@^��H�p ��A���]�[�~Yޔ̰%�b���\\A<��e'�[�Гx%:��z�Ij�������.`	|3ª�h�H}�e����#'�TA�L���g� >��/����@�\"}!8���3�0�C�b�N�'0\n@T�\0���T\n�=ˀ�Y��\0F=`f\\P,@	�A����4�M*i�N#��O2��̴Q%��̦i2y��	�O=4Ӧ�3�L��+�p\nf��0�X��U(�\\��g@LL��\0�\0%�  ����5�/No� �p�W6���kSO�TЧ8��M�q�`��}6Y�L�h3� ��8�.N:r�=��4\0%>f��r{\0@|��\0�=�(��\0�9���\0[�R)�M�g3�p��\n�1Mbe��Tπ�0*L�e��<��59\0Ζh�[����t \0�wS�=0\0�zɝ\0�@Y�\0�i���l�?����7�&�\$\n�nC����	��Bf\0N=�'d�L��4���E59��κ�њ�g����~���l���?�������9�P&��	�����?��r��*��{hC;��6~t*M\0@�B��J�4�-'75`Ўx4:�5����	�.���heD�QBtB�(;C��Q^�tI���:j'O��3.��36`Q�����D�.�r?=�ӆJ��\"|%R�p~�Bd�x��E�3e;��*T��%k@2���9.�\n�y�Q�UX@�I\"R��dA���U>�:Jl�c�8��\"�s�lW\"�.�����K�XV�l�Bp�����b�PnN���� �L�ن���ҩE�+�i;�u1�:R\$\r�l���\0�'��d!�q��D	��b�m��7�wN��Ɯ��#<H��(\0SXCu����V�kLVbꪤ8��Nx1Ȅ��Xw���@����\$a��N\0.�iJ!%D@�P�z΢&l�=Bi�P�-Ԋb�|Z�jR�Ԧ���;K�Q��T��nf�L�Ri@\0�m�	��v(����R���5`��6A`���↙���n-���95���j���DM���z�jnG�3�!c���Q> >L�	�|�L����\n�:m���9*�(	\0^���g}<�#͸\\�(��6���x���Q�v)���Vo�	���7���\np3��i��6��f�e���0g�A���� Ap(M?�'�����SU.2X�;�s N�(�A��\0���מ��Y;�+M��G�]f���������]0��J�V\"��J}D�\0�\0�\0�S���*k�o:���}4��M|&\\�ESN�w�BpU�Su�הQgkyE{\0N\0�N\\6�t�����\\?D%�:�	|������'�2�k��Ւ�v&�2�^�b;Ԫ��Z\0]cz�X�Ł�\\U\\*���V���j����K�u\rk��ƴ��\$��@�XZ�\0��`E�P����Z��	Xf�Y�a�%Y�e�4��B7���xWʛ�\$���0ރ�c��Y�s!�q�,�R\0��&�E��Pw�_�AW�� �ڙ��~	Ɓq⚰��� ^j�\nhK#��l7g�>Lp�1E�B����U��E&V-��&:_5�Z��\r���E�P\$�X2Х��+��]�W6�ͨ�ITŦ�d~o��MR,��PY�IO����0+�Xٖ�R� ���	fb���I��\\Bzڗ��V�Hy	��P�d�>�C��U<�`��0:����Zʶn\r\$Ao%FT��HT\n���*� )���*��9�W�<cE\\��'��1*i�E�:���T����l84ZD�R�8�\n@z�Z�>sp�;�@Wq��\0���(\n뙀�H�'�^���i�\0��Ӟ�\0.;Xi�`ҼOB��E�������ǡ�0U��<p>�m`?\0Q��i�A�\\��\n\0�A;��0�u��c��>��P�gU��\$��DJ#OSƜu�n�v��,���@�+�gz��<�v���h�oQ�,��\n�����ڄ�.�x��V�/\$�����PYi	�ޠ��b5�yNf�X�ĵ+Q�mB�j��o����j�Nʪ.��K[�j�`:ê��ƾ-s�wo[�A-F�Үp�i0�6�5�� ��@{{�/W�e\0k�s�:a��X�e����?<@�(M���t�)��\n)aR�2>��_!�q�zf3�\"��\$�%�E�a,���R���G��ďg��K��n�2!�f�20_����	�X����X��	8r����ͣ����F�H| `��*}	Ax�#�E�`ԝ�X��:�S�����;#)�T[��t��9T)̘y�\\a#�Z)��m��(�5�T.#�W[��ZL�TD=\0�}ߤ	j��F��1�0��BJ��C�+�U2�5�É2-JF8�b*+��݆�+4�Fk�Mh�o�8a�_��Ѥ���UC\0���~�@�d��%�B�%a��>2�����+�\$�y��&�l����FE���7a���,F,q	�1�b����im�N2�c�+�1�Ō��`�5�;à�n!�i�'�b���!�`���4�G�<H���~�6X��.0�\\>����jkq&LJ�«`=�>�mY�����\0~��p9��~��HJߎ���g��iz�\"�2�>c9���`�U�a`/�&���+q�xr�yf#r��h�'�Bd�5�%	rU��-*�)�CNQDg��ǒ�����2�#<�\0�*����o�O�y��!9^&X1��e�_�1���f���D\r�x+�n8������x�����ar��`w%S�U���LŇ��TS���<9��pX�0\$>��!���`@�`2�X\r��\n�sV-��f�R8\n�r	�e�h~��\0�@�j1\"���J�BW��@\0��X3j��H�F�<�\$�\$�%�*�R���&��\"U����L 	��@���\$�|h@��/��\0YeM��<��Qq,<���&Q33\n-�L���pl���s\n�vCۡ;'E�5s\0��S�	ߦaeA���ŠP&hY����|L�8Y�;�p��c���F���y������Hx�`���f��6�)prY�0k�Ll8�X��ʬp6,��ҵ���-TTZ]Z(t�XP�L\r��y3F24�%�И���\\�UxI(�\n./\0�\$I�TT>�E��d��&`1	��ݧ`��'��o��ӧ����\"e;�5�0��:�(�-�0<e��d^���;�S��N�5�pj7Q��Ԉ5QM�Gl�z%n��1S�l�U�^��buMP�X:�Ƥ�~�RkX�t��-��:,����u��h��B3rD��t߼�p\"�x��|�+|ժ��3nz�r���B�w�BT�РC���mv'Ii�F�Qau��OB:!�R��溍�����v������o`��`�X%����<��B�En)@j\\��׈n[�M~�����9q3�\0Y�S�������:mA\0w�M���j��8���A��١��ߣb�\$�~����Al�_ý�J-�����A�Z�\0006ȧ��m�O[j01	�p��l�n`y�Y	��Q=�3�L���ك�^A,���P��Hlw�����p�o�[~G�j�N���ܑE�*P�y�4\r�(��v�ӹ��nOi������d_v%k\0R��\\n@�[�ܠ�G�T��ngs�-Z��vϼԔ[t���v�V^@=�h�%M.�Ja�1;��;[��@7'��'��r��)4@�k�݅��l@L|>a�V�\0SN�\0z��j2��-Hx��W ��\\����L%�-�kz�L�+&�͑h������}�ōeݕ�{U/w5�����Z������1�X��7����ݵ��l��7��7�s���φ#*<��Lm�d>�wP%���)��}�@C|����k��y�RIZk,���\r+��\r�)*��`���p�5��������~xw��g>�ϔ��=�pK�Aҍ8W��=j�KV�p��}�?�}_.������4Gf*�`_!��`����`h5eN��&F������\0�x�r���i���2���,��+�����8�H�;pGkf'R��ã���5z��÷/H����s	�voE��3S��Y�������h{��y9\"�徭Ñ���o���*�	��/\r�\r�.�E���ۜ�ɑ�=��i��E��Sz8�Ȇ��Tr���0��O1��k�����>��*i�l�T��V�����\0�VqOF���=D�P�4^��8�#�|z@E���`@W�X��qr�H��⛥{[�\$�O�l�r_L�&����ut;���G*E�;�r{<��*�!t�L�{�\$��Nn�5��1fr�����I�g�.�j�{�[)6A���\r8�+H7��z�y�C�p\0=�VW}��:d�v\"i�Σ�`Q��M9�����F+�8�݆Z���ܞť|\n� X������a�vl\n��.�f;�>�_#4AǞ�Y_�!�x��+�^�w��.\"�i.�t�D|3ӥ8z}�Mpu����&z���O!U�>^p3�~�l�zn�o!�w�>D�0�y1�(w�\\�����I������ij��t(�'��ih�}���h3	�\n��w1���B�ķ��`��,^f�)���e�٢� H]��D\r�E�	��F�`&/}\n��:D)�-\n�CD���_~��������'�C\\����A��_)�����h DC�}���ZD����>G<vL����7����h�N�8�`O�yh��K��}tPK1x��=P�3|�p!p���\0(Y��\0V4�,셸�R{GV���\"���0^÷m+��-�H�����9}��wh���}F/p���c>�|{��Q^\0�·�\0�q��K������q�d�AN��F04�N\ns՜`�\$y��`��������w�H�<z�\n=@0�a�n�oG�w����y7����+�?�D/}@�3Yh��o/'����?{K���ݨ5ž_{���o�A��y��@��L��g������n�o+��3;�M7������w��q�.~���|_uC)�������_���Zƾ3��u�sݷ�\rf<n\$�S#z,��8��p�|+�ca���T��������T�H7���o�|�!��BW���x|�(�Y�@�L�=�����00�������E���\"�BQE��_o��^+�_�xS���4��zX��5�=0k�pǶ����͉�I߄4�~��:p��Z�x��6G��Xo�S�ͮpd٧S\0L�%����p1}9O���˅����	�#��UQ���\"�c�b�~�77���7�|G�陖��^���x躼�n��1�\"U�������G�s�j�P| #����N���ǲ  ����F?�����qH���U�`��\"�h?�벡�l��硃\0�(���<�\0� ۛ�!P>`�\"����׉�3j�;�@�D��?R߰��h?��z	�f�����s�����e���l�8����b˿�\0[w��>L��LW�@�m&�r�I�!�s� �`		@ۃ�0�\n@�@�����,@��5�ZC�`��L�:�k�NU���aJq�����l��a��+�\"3�ix�*�,�5�?5oD!��̓���̄����[n�� �S��H�T���t�l�\$	6)��в̎�@�郷.���૬E��J#%@��ړV�%	�N��\$��G�����M\0�>�/�4�5���=�\0^RK�a�^�T�E���*Q�펤�bViF���(z�k���P�}�8�\"Y���D�����'�{A��JMaI�9pBPx�����A��Vp���ƞ�N��\0B�03\0006 ��(�.�Iπ��|`(�~\0ʦ���5��PɆg\09JK�|i5����`��yhW`���\n;�\0�!�Yas�\\�z`>�H�H*�}������0�\\�:��I��8\$#�A�;9����|pnAz/���_�\n4Ф��\n`(PeB���0��i	��eBB�t���Aq\nP�r%�b�gA�l\"xA��P{\0T.���%a	��5�\$��I5�Ε�����v�'�mAhvXĐ������WéB�|éB���/����\0.�C�0�6��1i7�ݼ1��«\n*ᗈ�t�N%d�M���}	X\\���|X��oL)?Cy\n�W)6B�S�9Ċ\$���R��.0�U���\r�P�;�ؖ2VP�C&/�B@�C~��2P^B�_i\"p��1������^p�B�\"�������/��S �.'�/0h'	<=0�­�\r�uC�,P�D\nRSiFA��@���	�'�e���\"a��<=c��PaC��\"��B/�4py��q�K�5f+'d�\0��M�\n\$���	Cq�l��Dc���r�i�?��t/q�M�5\0�D�Z\"J\0���n���q%�O0t��	�I�C�3�>p����'��Z̓j㻮�xA�����)�;`9�^\"t���/�Z��B��2�=���N���Q\0003�^�\n@��&`	\0�������(����`�%��\\-D�l2\"�ND�Z��3]*�o���QqI��p	F\0��%�z����!a \0�x��dÙ�G\$��Y��E�8�i���R\0����1I\0cdY�q�(5�sŭPɁ\r�����#��Z1zE�DZ�u���_�8��|`1sE�L`�v�.H���h\r�\\^@���] '��hZ�G@`p�]ɟE#,[�� lQNr+LeqE5��R\"!�\r�e����xSQ��t(�e1>%9�\0;�A<TqR�NdU#!�V�����F�a�kų�[\0/źy#\$��w�\\��F	�[Q�Ʒ�1���dl1�F��[q�*x4Qϡ��T^1���n�F�5\0�#\$e�`���Lo�nF�p	���N�p��E�o�����Q�R ��c1iF��kˆ���/�������Nh@��x\r�3�B�\0�8\0^/�B@�T��\ra�\0^ �w�Q��Reє�\\H���M4\$8Cq���:`�3��i�d�0��0�(`>B�P�勭ʮ�a�v8'�P�-d\$\0ր��#�^&4,}*K]�ϑ\\�&�X	��b@��^�X�A?�\n@6�+'|�`�5? 3�\0�pJ\0�	\"�@'�����H=�\n\0\$�j�f`�� �fr�,oXaf��:q`��Zt�!Hd�мd؀�\0�v���;~@>:8`��>0<�T��Z\0\"JN�(+ �F��Q��!tn�>\0���}q�0̬Y�'��K�.\0�t�@0�\\�HH�>5>�q�6���o�'�Z8�q�P=��꼑1�7��\0���D*༬�Ċ�,H�_���G��ԑ!j_�RF���,\0�\$�^�>�0Lp	2Ll\rl��꘍%1�\r&~�]IC%L���\$�H;�>���,��#ᚡ,�~s\n�#6�l����((�M-�f��r�e\"5�\r�\$8T��Ȃ0�oq�-�(;A���%����&s����n�Z�\\��;��Z��\0�#��!,�J���;�80��&������(/}c��/@3��[��2\0�s�u�or�Ki*Fh��c1,�\"&j�|w�j�<�p\n��\0�%��(\ri���E(��8)H��4�8������ћ�\0G�Z�\nͬ��F��~�9H)8?��\0�\$�%҃ʘ?(-��p/#�:���䙑j[��)�Jмdp�K\"�#���*Lp	�>\0�T����s�q���]���Ռ.�O�ʀ�1\"�����e҈���f'?K�İ��&bS�<�63a+�2�������X�+��/|}rY�,��E�\0���Ñ�e,C�\$ʖ+��\"�	Y*Z���+%��'���`KH\n��A���*\\�O��xԘ7!RK��ܷ�u�r�Co��X���}.���I�/\r2�(c(�&���P��?��\0�@\n�҈�\"3�c\"K�������'�L�;%��o6ʌ\nԨ������\0텱-xb �I�+���HIB\"�IM�&&!T��L04�3�[.�b�j��/,������+�㟠���0y/�\r5b't�#O�x4�\n�\0��-���,���NY�@6L8h\0�L{0�Ǌq��2\nA\nq�Pd	hR&\0��D�����C��������\n5��E���&,�N���L�܉p,��O-3`R�K�3d��j˵,(B�8˳�:��3��@)���\0��K_-d]���jⴰ�w13��sEL�3���GM.��R����|�.�\0��D���HQpBB�\"�\$��ҳc4�԰M3����MU4��W3a4��0DL���f�5�Ƞ6\$(|�(Q���c��6+۳?M�5���e��3�5�h;��\"�2�}`�������\\ۤ	���D�\0�L�,۰\"��7H�?� ��M��r�Su��7�fsSM�	\0fg:M�7�#�������@�@>⺭-�4�MXK���+�%R\r��\r�!j�4��+/`�,N8�~�5>�¬Z��?�H5�#�T��\nE�M���\0��gl>7�?RZ�N]|��<��8�w'����9�B���8�6�'��O��p�Ig����8��\n'��̡o��r(za�*/�'�E��JE�F2hH�3A�>4�;d�2>J(���\r�������P�\n	N�*\$������Z����P�����*i0�2��2Y\$���B��!a��j�ci+��)�ad�KHT�#Ga<��Ȣ��j�[8|���/�^aH)��t��OldoL �<� ��<Թ\n�ɬ\"��`�\0�=\$��GO�4�,�OLnd�I���>��M�\r��T��GO�=�@)H�>h!��\0�+�r�4?*Yt�\$��(\0!B�U%��P��7A���.�2ǐ���3�L�I��O������2[j��0\r�k�!�B���'0)����r<dl�ט�/H�2 9L�:h�,:�:P�,�W )�����^Q���Y�@�c2D����\0���ge0�SPG2��6L��Z���1L�� ;�H&��\0�Bt����+t�3^�j/ר���:��Jq��:��6P�?t�g�H�\rm���C\0�@����\r�8�~_\\��+T���	P2p�+�L�f� )\n	�_��\0�\0�)����g&05�P���ȇ���\rd��j\0���߇m*(B�SA@����6?{*(�sML��;71�T\\(!���j�\n�/�<�5h��/��ZNE�׳\$L{E�ס�H���q�:�\\�s^ј��9Q )��@�Ȑ�#�nz�1(5tOJ� O�݃Y6����ͺ�t�2���;�۪�ID��n�oF۳�6���\r3n��H4�a�M�\r\\���Ɨh��\n)�[,����&�����.r�hy:f~����G&4b�B\r!a����H�B�*�bH�͑�)��S6\0�O0�a��0K&#Q�3e'�cJ#��NZd���)3e�=%SAXB�*�I�?T�\0�#��t��I�\r��P'J����L�J�'�q҃Je'���J,���KE)4���M-�a��\"�lT�R�*�@���KH\"!���L\"T���(�;�.��L8�B�R�J�/��\0�K�'!�R�I�\r����J0��JkL�3��Q�L�1���H/�J�|n��L 9p�\0��c#�6�l2 (�6R�UD�7�S��K�1ԗ�\\\n@E�6JM�(���	R\rX9%�%R[�]*X%S���x:\r��0�T�BӼ� %���X����IJ\$aE0w/�'e���@m7RS�!��#�͆��� Z\0Qԅ<�zH�J�7\r6�2��6�@��R����D�ܥ,�fCx\\���PS�0��P;`��q�:)n�+�E��X!L��N@��\n�_X\$#�f�-r0�\n̢*p04�ʇ�~5���ID#�b���I��.�B���\0ׇ�*X5���mD|���jQiE�t�r���H�'u=�O�s2^EE� ����d�L~�!c\r�Ҿ���s��̉kP�~��-���<�#��\n\0�a����E�FR0�ȼXi�5�x<�!��\0��:\$�U�<�ɬP�\0B�2P{�\r86c,��4\"pc����C �8Oh�#ـ�H��� ��\ne��R.��`)����\"�\0�`	��\0�(�������\\��EH��\nu`'�V�\0 �>�\0�b@&\0��4�EB�t-5uA����_%��YQ�T��NU<JZAKT9�%�lD	U:[�T���LV�0�xR`!\0�5P�%d�2c�ϖ*�)�� ���׀����#� ��;�K�Uqb7�!�SuN�?W�TC��y\raA�D\\`'�%�X�~u�U^m�E�);W��uy��W�K5{�YW�e�~UCW�a�%dd5��VyX�Lp�U/YL=�dV{���:�T�\\RUv���2�h�XK�OEV�W�>����#�3>lQ�1�^mn�E��x�K�qT�u�M�|gEn��Gh�ou���[�RQZ�4������e��\0�Ұ侌�\\�5ʗr�s�̆r�X��)�](SB�W,�=s֟�FU�\0O]q��H\0�DEv��S��w���u]�v��Nh\n�G��W]]��Q�y\0���m^x�8C^\"��W,\rev�[�]Mxag�\\ٛ����\n*���Gt��r��]� �:�5~��^�gW�_�~��W�_��܅Ih>����@A�5σ�`��W��:+��l6͂`�KȆ��`�O�p׈�<VM=`�|AMj\0O���^S��W�``Q���A]��5�U`Je�*��Z`���l��\0007\0[]�@5\$�l\"�a�\nKD��U�R�V�ע��W�]i2@���B��\r`Hq`�:�e2��A���wP;c+v2Fx+�\r�<ئ)+j��j��VW@e��1X�_Մ���(ml=\\�r�<Y	c��=/W\"5z�1X��p:)�_����0�c�TXܯ��63X�b+vNX����MK\\��6S��eH�U��em�VOJ�t�g酱cI\"�/�Yd�2��2H0�4*M��Zكc}�A&��~����gf��2�f�Wi�]e���Y\$wB�Y��Օ�6�Sf�/D��g;Y��gC[�&\\�rVwY�)X��J��^y��yg#��0�g̒�L\"�hE��Y�]�o��\nJM��!�k�G	`(�]X�e-��Y`�f��Y�ce�є��`����W�\r���wZTID��Xiex��#H�@��i���SZsL(;���i�+U���j���Zs4P_v�Z�8l��m���Zmjx���X�M�V4Y�i#�XOڧ����Z9gu��7�Gi����Sk]�V4Z�xvsX�h�CG�Skke��X�g���u��\n-����sf��`6[k�e�T��k��ɗ\0�����[\"�]\n���l��V˼<D0Z��عlM�b��`�`*��(퐶�[<=�\0�%l崡d�ae,w@7��t�᱂�ڀ��@[d���\nm!��[Lm�a���u�\0�[wm��ߡ���JS��n-���Xgk�p)��շ!�t0]�\"�ӧc��\rDۇn����?n��\n�1km�єۢWplu��o���[[F�/��r�M�V�FSn�?J��Sx��9[]l2�VB��Xlw	���V67��db�<؎�%�A���jm���R�pQ���\\-n5À�\\H�ջ��\\h��G�����\0�` \"��sc\0u���m=q�Φ-�sf�`���Z10r�m[mr��єܱrP%5ʍe�Y6��0~�lk�89_}��4��\\q5�-\\��]ɖ�)s\0�\"���\$-��7ܖ\r��V\"�D)0�W<�s�ϗ\"��h�,Gee��#�Sr�6!:m�Z�Hwe��W5�!sm��9�#r�N7J[�c���Y�\r�HZEt���&]r5�G�=u[�\"���ͅ�+���v7[X`meKW�u��7I��u�� �]5r��7b\\�v5����u=��N��v+�7\\��r�ӕ�ѽ\\���m]�v�9�=�ov���PY�vISWrZ�D}r�CM=tM�WE�Mu�ڷt]�[��wEX%w�w#�2T�Z���vV7}W7w�CWz#w��w|\n��	�����PSN]�xe��u}�7X��h}�Vv���8�	��t���[		�0\\`\rMnU�\\�q�q�,\\u\\��Wo�[���?u0�W#�4���a ����v�跑\\X�zU�V���lVMj\r|H��������(�ϏB�0��&~2faA�)bP�(�+V0�8���E{Hi�&��`�w��]{���^�JlI�_\n��*�*��Ph��^��\\�π��\"�@\$\0���0�����ڭ��`)\0�=�� &��|pW�_\r|��W�.�|�+���}�`)��Vx�i��8�+��1{]�Ѧ`�	����9(\\��\0�=�0��>�@\"����+�=雀�ff'�-Y�!�?{����\n(���߭}������}�4W�^�\0h����{��7����\0��}e�\$��������}���γ���\0'��*oN(�ـH�7�^�{m�*5�	}xAr�@H\n)�_�	��\0��8\\�)Ո�����\0V��g�,`u�Ԁ��%W�,�}e��q�]��_�{���Ɠ=������V �&�(�ɩ`'��&_/~��W�A�\nE���H=��z}]�C����`\nX/%}���_y}����߂�5�x6.�~E�W��~u�\0V=n@'_	���w����i���83�x��`�[h*ކ�=j{*\0߫~�	��߶-��P���4����ˁ�\\�K����i��_q~��JaI~���aU�����[��s7�U�Vh������Zj�a-U�f5m`p\\�뀞�][xh��U��&�=���`Q��N&\0��	���=�\n�8�oU���\0�0=�L��\\x<\0u�d��|&��bLaφ��o� =fxd&����t�q�Y�cU��~\0)�P��!8o\0��N��a��aU��n�����nD��5|��X6����#�h=����H��M��^ؙP\\��]|�%X:\0�{��x�a[��X�	ȡV\0	i�b�����`}���3�_́H��'�(\\��'�{pk��z�X	x��U��,\0)�ɋ���C~X��X�&߀�&-X�a���	8������b��(��݋F�`bӋ�I�T���*BJHC�E =P��(bًv%��⿉�2����{�3������j�B.8	�+��2������/��a�'��cO��eX�⍍�	W�`���i��m��x@H��Jii�\0�6c�'݆b���\0��P�c�\0�=r�������8�aǇ��+�\0��̭5f\0�W=�C�ʟ+#��� �<�r�Y��=⣏�\\����Lu���ⷉ��S?`=�/�c1��3x��=U�/�K��&18�`��^(��'3��=�'%�jiw�V<�lXb�-�^A��V=��%�3����*�ba�V:�����|�*�ۀ�3��r�O��5�df\n�2c>�G���U�W�`�z�.G �aņ��d��roRc!J�l�\"��^.�ˏ��~%��	�nK��cq�~G�2�����+䥒�@�/�3��&Y-^�n�2D=fF�7�s��N��b�{>��\0���O�)VA��+��eZi@*���Rǧ�U��Jb��:�B�'��C���n��T	�����P�Ja/��U9G䭂`�YJ\0�U�	���=����ӈ�fyeL=�f��_s�\nw�Z�c|v;�㿎Viޫ[�h�fV:�VW�[U~=Jsؼ�0H�+\0�>f[�W�?YX���,T�`��@)�m���	Ula���28�H�2����`�@!����T��m��9~��|h	���z�`����U������\np�a��*�dq��K��d��%����y�boI�+S���x.ǖ�Q�!\0�����F\0��h����=�P�_��Zy�*����PY�\0���!r�J=���V�\"���{C�g�0���U�c8���F2 *\0�-c6\0��GI�f��9I���\n�fȀ�������Q��i\n�L��U�,f�.h��੗�gXm�n��k\0���Z \$��my�g��o\0�M}:i5�g(����!�xjޫAV	�_�>f�\$��.GxJ_�Lp�\0*\0��0���^�~ꈸ��d�v�]_}T����; �w���%��+Yת��.w�����n���1����&��X����f<��by���w꧇3��`!�X�x	��=����H��	('���'U{��*�_U���+�IVecy'����ธ��Y(���W������•>.�����Fv9��=VNU������,��=�1�h<���	�V�·����	ԫc\$���h�Jei���{X8��D��\nc=�1w��X��2�h��.���'� �ЏU���#ӏ{��cu�\0���ɺ��Y�ٺ��0��������x�\0�=@�������/�H\nX�h�.W8容���8㞡���)(JE�\0\$\0��\0�;'꡸\nZ� =f�ZiLv�Z�S��:F�=��}X|.����8�&`����Ʀc�.CɛPv���)�\0f��\n�4�m�j�X~'��3?e�\"*	(����_(��f��3�}����՘��d��1~�X�D� �/⁖�9�i���;C�H�j� \"c����5&a�v�Z�-}&�w�i�#S��nI;��\"y�fŅ�R߿:⨣�rK��[��\"�����)����	��/�ZG���(&V��J:B������`(Lt���i�f��}�@CӀ����#�h�=� ��e�X�@c��=�(	)��G%*���fr�	bec5��:���ކzr��>s��iG�6E5be���0�ewY\0zwgŃ���\0����y(��F%���f�X��`姸�/�-�֫ /⧞^}80��=�G��>:�&�f �`U|����k��r�h���o��V.�\n�:�^��U����=�*��G�>X�b_A����'�����\0���n�?bK�F~���J����+��hw�懅��\0����D�f�}��Y��OL�q>��&�It�<�\0���S�Ȥe%�Q��H@ ����^U�N�i2I��6����RA{2k�8���U얁�I�!xZ��_N����Y�;\0T���;��?D�����r�nh 3��\$�bq\n��c�x�ŵOIT\n�v�z�۰�j�lDáć�<�.���pd��R\0��uQNP�ڮp*VH}��ň��nEƻQQo���<�D�{@��)�X\nYր��t%j�\\ R5\0��R5~�&}�)����:@gϐ�	c���}.�1���Hh�&�M�^�ul��.���᫑�i:�a1����jd��N ��l��y#᧠#{?៳�\\��m����<�[���_m>X	F&g�vΗ�i{~^J��-D�A��umL}���9�[�UQ�D|��)�N#����mS�}-G/I�\n�\r# [���K�rlL\"���v�7;`�ID��� �`�\0�&�i\$��b��5)L\0�H���E�2`K1��SL�.\$��bH6��6���\0�R����\0�^bސ\$\$���R	\n/~ښ�51\08�~���B�Ȁ����a��V'u�L���!m�I���\"j(�4��PM5��P\"4{l�~*��>\0�(5{o�L�0h��\0ƿHh���!I}r�_!|�0U!��R)5,`�\"\rm�\0��k#��GB0T��#!܂��ȁ)e�Ȅ@�: ��E��PJKId�#ȗ'f�⍝'ɺt�n���[z�G?˻S\n��n�gE�����_i��1i������tvI+/���3]Ln���[�7� 3k�,�I[bO�Hf�����	b��%S\$��v�PO5>��i��62�oQ��!�!SCE��PQk�f�a&��Qv�{��c���Mv�2婎מ����ú���F��ck�P{;�V�V��o�;�c\"o�;��k�����oC���U������3+��nL8p)ﾋ�{@����~�����Xfɀ����{���|<����\r���� �Wg@U!��0+�{��l��E��U�U�Ҿ��rO�^��&���̤�!O�Y����� �-��;�p`��hwpj\r`�%����Fp8\r��ȖI�@\rM�a�G�wl��T��{<���m�ݛ�T\\�5���S�+�[lʖ�_X��h�����sR�2�]���4�L�t�[�U\rЄP��u��@���B(����m�,�p��j��B��raSa��0f�i4\n����W������2 ��J���A>�l�KH5G�T��y��e��zEH,\r�z��a7%�(\$�͵&����0���dE�=�\$O�>��\r�T�#�r=}����� 9�>0M�����<l�0,���A��.�\"�%�0���W;U�����q@,J�&����	V��}����A�%�i�?�K<s�0�:Rt܇����+2�����:�c=���E\0��#��ƫ�V��ʋr/`�G8@:\0�ӹ<�P�KMp2�����o����L�A\r�(XK#��\$F���\0ɱT�1�m���(�\"r�Ic1\\��uP0K.�J��� ܄�H��wr�����Xr��.E��F�{*��.��X�=�{!�7���ܷ�G�g.[\n��\r�.��r��̇�����/tal#��/�1\0ȿJ�\\���0��s���ܷQ���%�6�7̧.�ɀ�D.�(s/��/�s?��1���)�4��sJo2��sNW4s���O6<�l�1�\$Rls#��{swP77���m�Ǡ<�c<�ܿs�+i|pI�\n����3\n�Co?��P�~����#�K������t��0�D\0��n�	�#���n��G�T��1��SF�S�y^/5'<�	�ژEQ�,�\$<�����??�B��(�?\"�W��>�L\0�7�6H��A\\�E��o>�\\�{��P�q'9\$]�t6 �=a2�b #���Ц��0��YSh�tY©x��qk_A#���}��� �����fݛl�aB��}р���1���`4]y�`�����!](	�ЈI���|�x!���,D\\��\"�����@�Q�_;Ν�j/��Je\"ܠ7Hw�d�p�	ѿO�����6@���_B�mu\r�x8=��`����a��Q}IʭѨ#�@trߗF�PPa6߽M�(N��\0���b!�,p���R���X�gQ���s?tg�]�Ӆ]��ݭ/��d�a��zE�]I*O���xyM}3*#�/Y���,�5��'�E�i�L�8!F��-�G*|�@�f\"\r�wĜh�����\\\"�\n\r`1\0�P��=y\0���h�x\0�״�}}�Qx@3]{״{=xt��h�CuNW_<�t���h��갢��'סb�!�\nWw=]x]ן8�װ3�=������^	��a>���0�7����Sv�\"�)�p?`�nm�E`�q��@��]\r������\\V'���'ib��凎@����^���&e�>����ؘ�1��ؿ\"�b%��f!��幽�,�d����o��oP]E\\�O2�b�F�]\0\r��e3+v��E�a���a�ArtE�\"C�M���c���a%KMu-�bc�Q��o={pg�g}�w=G�t\"7v'�wr]�����3�v%װqހ�؝B�I�8q�whȄqV�1���\"e�]�0/v��a;`�Uw\$,�]�Q�X\"���m0Lw)?��>�e�\"��#>pW'k(���a�br�v��L�w�	�ؘ�[���l�<*�p6��}�v�����\0���Qb��؟}Q��/�Ϫ��^�a�'ͦb(v�M݉���K�\0@�]����^Vg|�_(w}}����d�m��|��G����#�O^�v'/���p�ܨӜx%#�l}{wk����c�\0�F�Tngt����aw����hb�{Y�%`�T/Op�%x(�@<�Oܭ(�1Xk!�\"]�\0������(#��)�'�a7&=6��^�d�5㴈%�ګ���xgh�^T��EDGbjES¯r}��A��Šwe�1:�lx:�L�؂��U��jG{/�]��a�^�*�\nf��\0;�&����19����(f�T}���È�\\9�#��v��)V�SCa�f�OI@֘�4�`</p��X{}�OR��g�d!Qx�҅�b'QR!�\r�~mf\r��)[w/��fEwᶣT�0w�а��[��޿O�]��xf>xu�z��Q�2���Q\n.������`u1�	ck���;��Ϙ`�G��=�w�d��b�W����'�����Iů]d���d�ukҜ��Pi��Qv���I�@���yMPc�75reִ仑;o��+��}�k��\\:���~�bXx9�5�.�#�̜�����W����5�kz!�Qw��N�p��B	�;���H��](�oҏ�����d�eMv�g߮ޏ�0/��޾�9�����FL'w����sW��{;�c�X�5cW�Am���R�e���xTw�q;�j�8zbm��w�o���w����~�s��;i>ї���R�L\n~�כ�K�핉�	�_ϵ�;�ce����yN�o�e�O��,���#�R���A�7U핪�d�!��.���9\"�}�e����]U=�`����������p<�����L��_������#(o���v�<���b�m���\0�dם��Q���_xM#���rA�'�>����6W�/[�HCu��>y���Q�avҗ\0+�)�_��v�Nu������fG�{�ki �uKAGQ�v����\0n)D�>�LZ@Y�����5��y�\"N�>�*��W�wՈO�5�I������m{^)ب���6^/~���{��v�1t�G��v����u��p`�,v���D��|�ۄԝNp��Ϫ�;M�Ǥ=E{8_�a>���S�?P`d��=�):���>�x����}6�d����'S�^wz�m�@�П󟖿?���O�6d|�\n@���7!��\\h�[�oݵ�r��Q��/�__�Y�a����ǹ�c�_/��\$�����\\���/٦�<ĩu�}���\"P1�0*T��#ܜ?n@���q}�ޯ\\�YI��G֞���)��\\\n�5���ӶP}x��'�V&t��T���}����Q�9�����WĝE}\$~ҝ�}��Ba5���*(5 2���?Z�e{�دY�S�w���[������\r��K�����E~cٗ&_����� �� Դ���GQ�L��@o�L~���⽸�KG���tMw�3R}1�?T��q*H�`����� ߌt#�l�����?���\r����T�L/J[�^��O�����ϕW5��}@T���6�u��T��|J��'u�?�����#��_�u��jv됬?�A\n|h/�!)�)��ݟ����'Z�mtM�����Y����?L�;�2`i�g���]��I2��[�Q]�w_u�_�~T}��5��T���q���s9{l���W�\n�_�qI��S]��T-�JFt`\$/c&j����2]S�M���Zhl2��ױ�}{��`%��eA0\0������B�A��\0�a{\n�q��ؕvgD\$0�XRma&OT�iVLB���'A����C�\0`��'5iH��\0%(-Ud\0ؐ�#�a�D��Pu�\\���%`�hN�	ѹ�/�\r�'�O�:�K�Ţ��B�\0^���`�|��i\0h�Q�c��r� \0�´�ĥUV@5��\rX�:�l�\$`'+*Vȭ}[b+�	���]�4F��x�F�ݑ�E��+��C��ȅ�Em�L�WH�Q����_Iq�TQ����y�q�e�ê�@�W�U\\��3P%>ԁ4	~��4��'�ܮ#Y6�I��Q��2)���V��eʛX�V��ȁfLhhv��*��)Ls�n�#%���K���[�\nɬ�0)�@ˁX�]w�(�{���\0��\r�\n�?P*�i��smad+���k\"ց�\\XH�\r�\0W[��K��0@�q� =��d(�#���Q��D�\0�W��W���8)�\nJ(��#k�������;e�\"�\0׭PE���8Pk�U̓\0��Ô�`w��Y8S�	)�\0�+D�.E~�'r�d\0��Yp�`D�)��n�ɗ&��\nh��6��y®����~���(�/���ZZ�xH���+�^���P�';g\0&�FcP��=1[�K��\0!��b�NH=�B�5�Y �2���q5�\0{���FU���d�1V�\0Rw�Xd2��ɡ�zf��م�X��D�.�XUh�C_~��>I4\r��c�	d�M��33 ��z\0V��>Z��A��3U��6�\\W��`��ycLy�s�(E�&3�*�͒�/�4��`�4eb�F\r�A�xlMش`hu}�3-�S0pِ\0+FVMNc����V�Kc��@z�po �0_��J��\\��� �PV@��G8<�ر��cI���/�nMA�fye������/}\0�(���PDߘ�|_��Վ4���S��A�`vM�?H7jZ���&U}iZ�8w`Ç��pO���!�C�E�A�d��\r�|�h�u��\\&d�K(f��k!/��0�L{U�;���Z���j�:\r�	v7P�X���cm	ʲF������O\nd#XA,����e��)��V� ف4�h�Й��;�=�W��e�P��`}왠���\0���&�tm!9A�cM�\r��(2X�~c��e�;&�9���Bx��]���� ��h\r	�Q6(O�x��A��\n.)Q���<A��	���'��P� ��>`�	��z 	!�ځ�_�ɶ��QA��4��<L�&�PP��±b�B�!7m\0�6B�|�R\$,p�l�ae0t�V��\"�S����2h���#v1���bB�a.��,�HLQa@��b�ᣀ�\\Pu�i²b�	&,.�I��!'\$'�	F�bg���U��hkٌY:6�+����c����|&��q!9��g�ƚa7�Ol�X�A+\n�SHP�k!v��4��q;�8���I���*���1�-pw��A�d��\$1ell����c�\n��HU0�aV��`�\\+XeLg��Ӆr��Q�i�a\rC\"�W�\$\"��ad�ĄN�Q7HE��kCCe�ʢ�g�jж��2����d6x\\la��A���\r��6�^p!y�Q\0W���8�q��4]\0���=I59%pZ1��c�V�\n��l��a�B�j��l�sg�l,y�a�\0��Y}0{Ffl���4Ԃ��9��wvB���g�Ws&���ǚM2jr��~KQ�_l���O���e{�%�El��h�<g�Œc�^��u�_AH�	Z�nPc�r5�&�MՉD�h,:�#�Ka���4=6\nl���1���=UX�� F�\\n�����\r�ӱ�Ek��`d@�+Yڂ�\0�M��>�\r��hp�ᥛq[�mE	��3ff�V+9�W\0Z0��l����:Ʊ�k�*�mD�O�j��m�EK>9�H+7VT���γp'M���%Ecj��_�g��\0>X�+��R��dk�E�\rF�'�b�Oa�#?�a�Za;4mÕ��]fv���IhMN���M �6Igf���C\n������D[gg\"�;�j�Y�3Ɉ��1�z�����A���\"f1�J�,c��q��F��7C-&��ٛ=�7�=��h�O	��8���a~�(�y��#x@-,Y��i�����\rHV�yZ�4�.���K���ڳ�^��	�rFƞ,1Y�D�H��h�O֠�!��Aj�8��i`?0V'��Y���m�}�:Ք={X5Z0g@'6KcO cK�\nq�ٲVj��=�L�6�a���&�����X�-d�b�Qk.��#a;�,IaU��g���r����\n޴Mf��T�±P����?�J\"��s�s��\r��7G��%!ӛ��%=7S5�\$P��&�i!o����|g�	��A��q�������6�IZ&��):C�`�Ӫ\$In�zU�{k�u�R��n��U����܎<���P�}��U`���D�6�n&�J7�Wt�V�۠'�I�\"([��+pZ��˷����~�����@�@UB�E[�BMt�5�1Gwm��77%RF	qi���������cr��CyF�(o@�ú�\$�Ϲ�>�rb3V*�M�í���%�o�J�#{~v�d^[��^4<��,�v6�q\\[��'���(rs���38�o\$ޙ[��@% �\";S;�-cr 7*\$�(rwe��R���b���z��e��Yx�&���!�k�B�w`*����Qr�1a�UE�G�\n7Wl.�B�\\�Ƃ`�7f\n�M���9.b\\��N0�8y �A���hS�ލ�;��թ�\"���p����ݷ��^����{:�F,�>A\"qm����/�.���G✩�~���C�Q�B��>�_R0S� Eц�7�luF*|A��� t71qP��[����^����X��������R��)4\$L�ӌ�Į��T8���ex���# �qP�TS��g�Q�\\w'�Q���MzAȯ��S^��22�0�#,�y~�������1�_�Ɍ|�@����A�hE�\n���#mE�Q����J5��ז�PCFy�wړ��B^����uF�w9\0g���#t#B>��5\"\\U�o��ܝ}����q���]�ƕ��4È�>��_�z���������ލ���y4Sn��ѫ^���\"�-�LkH��K8dzO�U�j����Z��*L��6\$k��1�S\$��C�������C������6�����xVj?�=ht�m�f�oF�K��6m�ڱ������!n��\$�V�*B0j�\rtj�lT!�o@��\"to��	��͍�8@�ɀfp9T���'tp����9,�y���B���N�K�Ȍ��4d�d�I�QV�E�q_�N�����6'�����_�\"���P�Ol���R��\0|O�?�\r��@d�Joe�&�p�7�tUQ�㤡�\r�9Ґ�OqV!�GRu۹@|uH}գL��m{�:�뎑\\I	_�C�т�h�S]GEX���\n���Oţ�vV�S�q��q�#�7��2b;���E]@G;u�:p_��QV	F�~��<`H�ϔ��jT�a�w��q��1G�|�5b�yG���޺T�%�b\"���c�>��R��=�����Ϳ5|bVΚ��_�uת@	�\0���~x#�'_-����z> TG� �\\(Gǂ@����t��z��G���\0ӂx�����G�u���ʜ}�� VAl+�}�<~%Q�����X*y�h�H���G�WNk4����iC�~�⏮H��S�c��포�3�D�r@�G�\n	I�i���k����z������2̾vh��(JǕH�s�)��[L�l\n�z\$��(d�ҋ|�M��������j���3�����\$\0\0e���=#L���\0��iS@@S��R�Mq�/��#й�&S�?���g@Q.U]��އ�YR\$h(�i=WڕC)*���B�,�k�ۑ묂��\0��jH�f\n��ƇY\0DD�S��Nh@@�l?��A��8�Kd>�kn������F�q�\"6�Jl�o(a����9H�� �CQ肤H�8�x���S�n���#�E�8\rY'�ʄ��;��[B�P��X�4��G��*y��W�fĥ\rrr4Uںe�SCA��TQ��q�Y��A��X'�]���u�󯣢�:7O��F؞Y��^�7����T�N�\nw��\n	����@a��\"f�Ԉ@�,dz8�*�#�-�j�UR?�H���4%��K�@�WH�:{��XU�E�*t��H�2a��iĪ�n^ȸ)OD�8�g\$��A\\��H�@^�eL�I�.�wQ��Aw��P��~�xi�%�P\r�_�\0w|��6�G�DպTr�\r��E�O�O9�	I#8�ȩI�Ip*5���D�)A��k��}�.\n`�Y(��Qؔ��:GD�D�oG�p����p\r���E^vT�]T`�.([v\$�L������':]��&��]�d�t� ���l�#}�d��\0�� T���:�T�2f�=5u��,�Y��gS�.�~���k�g��^cE-��m,�7�bsd3<<t��M���qO��ǟ?0Y&Q�7���]\0ɷ����;����.�49Ix��\nF��'O�L��Z��1=�׾�x]>�,vV��L���s�Ĭ�&�4��	22IT��*�ŵZ����6ކHpzr۵�@d�6��|�%�-1�����+��H��Q��7n/\0\\y3>5D����1RIa��:K]�.�\08�\$(��i��-L��(��3�� �9�:�W#Qh�� W�J�RJTںbYFΣd_�#�v2AG����A}%�tb��!JyԷ�CJI�'h�Ug��A�'�u����!H��De(Fݔ����q���E^\0��S3�@P�R�Jg\n�G���C�P����'!�#]K�����;>\n@� ����cR���1s�Ӄ\r�*%,��|(���`TX��dC�!zi*R@�r��O�v%\0M<��������޾�����O�j�b(����T������	�d�<%��������uH|(�Z@t��*r�d�<b�@<��H򌜞�;��+	���d���\$>8O`�\re{�IZ�O��z>�~V؂y[����*5�)��K��HN��F��I�;l}���w G�J�ڂ�S���&�]�%a�첕�XY��@�����d�&4����2[���2u����ߎ�<��q&QN9Ya�D=1*�X���ز��,C�z��8�ۊ��o҆ pr��RPL��2��{ o�f���!~������7Q�\"��LaAb�z�Y�Bu4�.t`�hq���!\rU)���0!�+�SC�K���^*�`,�\$\0Z�U`ȥa��� \0\$\$�\0t-X�8�v�(3?be\0�\r\$��`@S��>Z��jʽ��X�-�Z⯉l\n�e�@`\$ޭ^[�-�lJ�@�@hD'-�\0aukʻ���R ��\$S���i�����-yt\0�� ��A\n^K~\\�	Yk�E|@	��Oux���R�ɗZ�.���2���+�X.��\\���%�����1]�ˉuѿV#)�x(�|�Ս���tK�Yg/:]t�z�]���^_.*Is������^���|��K݁/|�B��t��e�K�/֊%��y{��%���-�_�����ϰ�V^�~\0@E�D� ���ș~�P�{���4S��OН9&2D䘧1��m�|�n0��XC����f0�9�d!�íV>�Q�L;��L� ֲ�+�,�<�tP�	�3h�˰,%v�P�ڜób�a�������ب05�������,�I����0����I*�gT�.S:.�\$Y�0�������0lJ#�6'��x�A8H�U��g���z��L����(����;����#�C�+B\"�g��b%��h*�)��F���̴8jؽ�#�ɓ�4@ip�)�CE&}��b;�(�M!��N�C���}����#�H8�_\"C�ye��`�iDP�m.\n���+|L�\$c�����1�Bǝ�c+�e�\n��Vc��ͥ�x�����D�*�%lK���X�Z+��.%�5\"�2�@#��j2Q&4v�\rZ�w'[��,6A�L���,����z�M�����`��T��~��Yf��ej�b'QBV��>��H�n6�ɰp�U?��YDmP�pȹf�kv��%�Y��D�G����ҙ\r0�@����}j(G�m?鹔Aq�C5f�9�\0��͹P2����}��\"�m�S��%qN\"\r�n\rL��D0%m�⊤Gn.܎��-��ҕ��B��X�2OR�*��I�5!�aG#����1�cVĠJ��ǹ%�V\0��������*��_�>�|D�̞��of������F�Y:g03%\r?��IL��]ŌD2��x��T��\0�%�y~(�P<��#�0͚�	'K������*��(���/Q£�I�^xb#�W�\r^jKE|x�m�����n��K)~�}�;˗=R��(Ȍd�x\rZ{G�f��=��u�R9��m2��<d]K\$(�7D��R*�f�-0\$��F���ύ�S�oZ��q@K��sy��@v�(�N��K[Sͺ}n�>o�i�EѪ%��:�m��fɸ;?+���l������ۜR�o�\rii�\\c��\n��-y�\0һ�`Ї�gVL;`�t��2�e�!���D�p]�����KYV��\0���hx��\0/\0�-��'tW����nV��!ζ��@�� bV\0�U�`�s���^�4:H�� \r���.��\0��P��+\0s��\0��p�S��/w\0j\0����� \r�\0006\0k9�r�Y��'-�8\0o9��h9�3�\0\0004��9�s��ϳ�\0�a��9�r�9ͳ�\r��2\0s:�)�`	�(\08��\0�t��p�\r�N\\�9H�u��gK�o�?9Nst�@s�'8N�6�:0����R�Φ�):�tT�y����9�:r���s��6�2\0q9�tt�iҳ��Nǝ:�u\\�SI�@\0003�;9�t\$�)���1\0006\0o:u��)۳��cN۝w:Zr��̳��Nҝ�:6u��P\0@N�*\0�s����O�9�c;:w8 ���OΡ\0a;�r����Ӝ\0�g��:X�Y�S�[�N�\0c:�wT�I��gy�؝�:t��г�'�N�\0h���`��w�U9�w�����'�Ο�9�t��9�Ӥ'T�ϝ7:t�03��jNߝG�t���᳠'P�S�J\0�x��	�3��vO�!::u���s�gB�H��;x��Y��IgzNp��\0�z  �I'LO[��=Bs���3��_N���\0���)���>J��9\\���3�\r��4��9���Ӫ��`��<0����\0NÝ79�{���S�'jΫ�!>nv|�i��'6���9�x���ݧ)φ��<v���3��~Ϣ�\0�y\\���3�'oOL\0i9Fu��y�S��EOX��=(���s�g��2��:n|��I� '4N�\0i=Fxh)���gBβ�j\0�}�����'�Ϊ\0g>6|��I�3�g��؝9=�sl���s�g�N��<Z|4���S�g:N[���6u����S����Ϝ�;|��y�s�gQ\08��<�t��Y���A�Ǟ�?�x����g�O���<��d�Z'g�UONW[<�t���ӷ��O6��?lۼ���ӝg�N�?rt���\r�;N��e>z��I��ؓ��fn�>������3�g�N��%?~t���3����(�W>�tD��ӻ�Nϩ�q9�|���3�g�χ��=�{���ڳ�'��ܠ\0�wl��\$�'6\0004��@ʃ����㧴�9��>�{1����'�Ox*�;�zT�i������SA�~<�J\r���X��9:.}������P`�m>⁄��4�>�p��?P�l��T�/���u����O��B.|4�Yϓ����2�F�w����.'��˟\rB�v)�t#�f�f��<�xT�3�gPi*�=.x,�Z��g�ϲ��CNy9���g�ϵ��C�|5\0�T0�6���ARt��yʴ�P%�:����S��VNf��=Rt�	۳��=Pޟ�B�=\r	�4\"'kN���9�T�Yӳ�'gΜ��<�u��j��������;�tU��(jO8��?v�\\�i�3�h;O�1@�}d�\rt>��P��iA�\r�\$�g��ݟ�C2|�\n:\nӛ'��\n��C���M�Ρ�{?��,�z)s��8�ܠ�@Jw���s����š�CrU�+4\0�O\r�:�x���R�3N͞Q:bt,����hoN���<�v��	�4�HQ\$��;�~�F�T'y�םUD�zKt*����6�#E.s�z󵧫Nv��>w�z�E'��G��9�~M��W�NOA\0c<\n�M)ѓ��\$N��-=�w����h�O���C�m*T!�Q�KB.t�)���(���1G�eZ3S�g�ρ�IDVx���6����0�{?�z�Y�S�gy���9vu\\�9��g�Q���Ev|�Y�eh_Ϝ��:2x=9��4�ܣ!;^��T�!H(�۟#?�	,�z/S�'�Og��<��\ndl(:N���:����5�Mg�Qբ;9b����s��N��5E��u4:�qQ���F�}\r�D���ϥ��9f����s���O��E;ށ�����g����9\\�U#���hB�`�yB&t\$�*t�(�M��?ڎ�\nGt`��N眷H�z���]��О�i=�U#��s�g�R�;G&�=y�t8�-Q���ENwL�	��2�g�C��EX����4M���ޟ�FF��9�ti�oO\r�_GB��IӴ_�J�Xޜ5�����@�Q�#aEN{<�Өh�Qҝ�<�,���TRgH��F~�\n#E��¡=�z��y��g0O^�=Bu\r*PST��IN��->V���:0���O�IB�~��紫h��\"�#A�v,�JE����O��/?:�\r*5S���Pޜ�F��u���VNy��<�v\r(Y�t5��O��;�~�)�T'^���:uU� o(�R��)E����QS����v�5:��d���\nizN���H>s�Z\0003٧KP��eB\"��	�\"TO)�N����{�:Ԉ���E=x]:��gKR���K*����s�g�O�1J���I��\0����w?Vy��s���O�mH��u39���h{Ҿ��DƓ<�ڴ#`�{�8���=���/gb\"�姈{E�1\n@\"3n`}1�C8m���2N&��X��:�m,��A�\\m=M��L5�zBd��ub{Mݚ�&�m�i�2��|��?E��X�1�N�Z�c3e�EuǀBh\\��\nD�X>i�[S���ڜ�js�Y�D� ����3�`��bvbS\n��.�j�n�8�u#��ׯ��	q�b����2��N\n� |�-�4����V�:y�5RR��'��d'f�\n/���Ų�%tt�W�4.��]��	��Z�T�Jk�O�(�z}�}\0���a��K��9�u��k��D~��_����W���LrG�t3i���f�ΕV��Q,�Ыa0�aUA6	��a��+{R�,:��V�SLɉ\$W^!7�1��jS�\0tM\"\0{J�	Wʱ�DQ��8P���R_���\r��Dh�[؉r��+�ܒ�`�Wӳ���e�\0�'U\"�(_.Lޢc\n�-:E�\r�ơ<��\$�\0P\$��>��\0�Yp�Ш�M���B�~��6�\0�FF���@)��(��\$�4V<lF\0�ԃ`�3��L9��C����&�H&<���\0��%�@V̌&\0�\0sk�1�Y���@׻T ecP��{`5�������f���0b6��o@´��N�]<jo��*ZԹwR!��\nz���\\0�a6΄��Eږ�U�`3\"�H�����j�\n��e�Oa|UK��u5�1�'��N��jp1��2�hS��\n��̿���SS�EN�R��)�\n�\n.���JV^0j*c3�|�n��1vdA픫\"�\0�Rޤ�XY������6�r�S�aS�Ee�TY�S.�x�u�s0�4)R�#2����\0PV�)Z&U��j��+h�̉��V\$��	�'�&����S�[L���<f�Y[1�\\�F�Z���^�9V�MP���6C�mP6�Vd�# �B��P�>`���1*��ө����]���/ʃ��.��'*m�\"��bb��-��UvI�XW�ç��ɖI3r}�yZ@U`dZ���?ʮ���dh��k�u[*~�X���m��zZ�5#@Ӏ��2X=Wxo���/�gR*mKxP�g���̪����\$p���ӳZ�_15�mV�=uk����Z���kVi�c�\$�v���-��Wu�T���S}i�R�L(%WJ�`���VAUݴ4�S�n�[�����Ҫd+��Lj껳G&�Uݚ�66T���p2@`i	B��9\"n�\"*�f^�n�Uj� \n�Uم��\r\\V������bz��EWz��a�0-j�M¬l-��}�wC`���n�L\ny�8�^U��~M��{f��uG�B��Uޮu`�/�*��j����\\9FO�*��K�����k5n��n��P6<hXUj�0��V�=�U�Տ�V�+XU�%VHv,OZBUh��\r���=���&!��`��eWJ}��,C+�\$�� �2���W�RTh��Yv=XJ����p\0�V\"�5X��5��6U���Y��-ʬ5��4�whsVf�['p�g+@�w�H�e�`�Ɛ��եa�Yҭ���쓪�Ӑ�\n�\n����\r�Kը�5Vٙ3�Ӱ�kE�ga�ZA��\n�����{�t�I�\n�^��貌�����kZ����*C+��P�%]�װ����\r���</z�셪�B���Z���5���aU���Z��g�oLmj��o��Z^�f����Ռ�K[n��mp�t����_sX�ʚ��u�i����\rX�a��U��1Ą�P.ak:���+K�&�����b�/d�����2��2h75��w�у�UJ��YJǰn+ UZjE��Cy��k#�Į9�L|:��n+%Uf����rh;��k0��{Yi��r'���Vb��U��-X*��sk5/y��Y��m�5�k9�Cb�Y��Q]j�5i�A�+{Z�4*��g+G�Nb{Zz�uiR�5׫H�*�W�#:�5bY`ո��Zb��k��,OkS�o��R�\r�7��u�kXUͮ]͍E]j����Aݭu:��s�̌j��W��[\n�u_F������|����Ux\n�I��U�_.�\0�:C�z����a�^F��!��u�a׬��X�\"��������UR�,\"j�u�����[ڽea*��`����v��pJ�LqY6ב�Q\\*��p��5�����c���O��A�S?�8��n��9f�l���:����y�<��/�x\$M~긕�j����Q�~�<µl�)����dV���6���㫼\0TH �D�v6k��B[b�N���D\np�Ș	ĉ�2�\nw�X o���ɨ�N�U��\n�S��S��N��+@��)��h�On\")7�\0\$��0�\0�L��j|�{�+�2�v����tS!@)�KfW_���J�v����ξ8���jlD�_�O���BaUE[t��3c��Mv��;����\0__O5�b2��\r���3G'�_��[;9�,�8����V�UT�S�#�@4�'�R@<��\n��, ���	a6��[Uj�w�:�3�����-.\nX��0���|�dMXG\0B��\n��~��\0����xOι8g�)�,�Q�M]L����ev,\0,����b��E�:�6,I�Lx�]Yb����~l��\0HU~Β�0�z0	�5j+yS�ĤL8E��VX���Y��݊�m65,ZS���c\nJ�P\n1��D!l�M����̟�}�/c��KBګU�͠��i��J�����i�Bd�c��Aʳ�K*�\rcab~�=fjӅ]�mX=�1d1��p�!r��X��!	��e�Z�����h�T���6F�Ur��TBf�U��zi�L\n�W`��} ��Ү����|�eQ4����&�݂�0٫s%X;5	�Y>U�P&e��U6Q,�f�e0�5��2�[j+W��eU��<�ײ�g�Qx>[H0�@l\"���a�P�~u�*/4g]`|+*6u�E�33���Q��M���V^ه�`)Q����&L>,��Q���l����m�l\"�̈́\0L������4��gf.�`;*5*@ٜa�\n� MHW�pFl�\0(4�b~�ݑf�L�U�2ѭ,Qa �6��)��Y2i�c�=\0���@\n�YhX٤����\0�ݚ��W�']ɭ݈�u��EU�f�W5����,\$S��^�i;�?��Yׯ�m�n��2��c��Y��g�=�{<c�*Y2��g��B�U-��W�h��b��H�l��}0�z�a]Lu�\rb,6P��RՂ]�K�m*[�Nj�`�)��I�1m����\0�=��-�k��qc�!��̭���\0�\\\$�t\r�-X�fD����eW�/s���c��\r���3�q1�QSP�[GE�̸��l�3P��f\\UT�����a��2\"~&����Ñ������,1/�a^�Z�ݞ�4�zl��i�_򫭄*��L�c�ɉ�Ά�k�M���5�a��\0�\$���y�x6�l��\\J��!��j��E�z��1�Tqa����.��lͣ�\0�L��;;R��k��e�Vī8F앫��'�DL���kU�[&4д�j���Vo��Z~UijN��\r�V�ߙ�ڵ�t6��Q�-l��Fgkj�ݓ��V*�So`z�fe�L�#uK�ճ��^�N�)3{W�m_U��_��k\r�hE�xX��ChEb&L�&?�[jT��z�\$̭y�ϳKj�ȳZ��{7�7�(�\r�!������k���6����|\0P�4(*�׫^f�I5\n���\n��Nu� �*�[��R�k@�K{V�\0�	�Y�?Ԓ�`B ��6�1��\0ɀ.#�4��3����E�<��^�(��ᄗ��4�����E�\"�1�d\0^\n�d�`ĸ�m<\0\\���T�\0000�>��O0W2MԀD��`XGm�)�Kl�Q������<F�XI9Vۂ��6��'7Ͷ�����\nr\0����o����g��;���U�a}m��s	�m��5��\$����r�A��%�[oح��j�5m��ŷs����v�GnF�\r��t����ۀ�Vn��qV��\$�gm�ݭ��f��-�[��)n�ܵ��v��-��y��;�ݽ�+x�������Y>��E�[lV�-�[f��nf���m��-�[ٷAo@���{`Im�[޷/oB�U�ku�-�[귮\0�ݕ�+|v�����n��m��~����[��;o����{��m�O~��o��\r�K�.�%��pܼ��~6���J��pR���k��\nЮ�:rލ��lW-�ۼ�ap��%�+m�.���p��= ��-���Qp��M�ku�.�.�Gn�D�˂����7p��}��3���D��A����ۊ3�.\0NT�Cq:��k����\\V��p��u�k��-�Q\\��qr��ˋv��/�\\��q��uˌw.2�=�Er��\0��W.�z��p:���W�!�~����\r{�7�:\\q�r&�ŋ��.\\���q����7�I\\�;=r��{��n5۠�sr2�;�\$nCOԸ�rb����nXm��q���[r�-�T[��mq��e�k��-n\\�mr^�h_[��-.d�_�mrr�Ÿ���nD\\Ƹ�r��ۗw#�kP��;��}�ۚ�2.`ܚ��sB�%����(n\09�or��u˫u;�m\\�K9��m�{�w3nw\\��s��B�[��n�[��		-�;��>���t�D��n7Ant\0'�ro%Ћ���.��[�/r2�Ы�WC.�]�5tAd��k��F�����t��M�ˠ�Ln�]��tN��+�7I.��&�ct�fD�K��K�N\0#\0�F�u�2��Ln��G��;�{�Ի�3���])��uV��[��H.�P⺰j&v���N4D��;u�諬�n�N�-�>��X9Ϸ�O׺�9���ҙ�7]��|�}u���˨w[�])*�u�s=؋�7Pg-]��yv2���	�Wb.�݆�ut��y�f��]��u�{\r٫��h.��c��vj�ً��X'*]w�Ev����˳�X�Xݮ�Gv��-�i��k��ݬ�Wu�t�y�Wp�����gu��u�+��r.��l��w\n�����Y���w��wZ�-�K��['�]ֻ�wr�m�)��u���Ի��)��\$�] a[X��t�̀\$�N��[���j&{e����@�]��o��Uы��{n���YJ����މ���n�]���t���K��')���9z�eޫ������2�&Lj�=����M�]���xj���;�7R/\r^�Ow�r��Kķ.��3�@�\n�T��7�o^)�/w����6z��^6��>��%�*v�/]{��y6����ȗ�/#�.�\rw��=���{M���GV�����į,^+�&PA�(i,^��\n��K��כ's^n�sy��\\�y�w��Oh��vf��ٻ�7��j���w�su���wi�@�.��w��m嫵���%N��!z~u��wn�I^.�{w�z��ɓ�oLNQ�}9���I��uo-]׽[y*u�ޫ���oP~�ex�z�k����^���vz�����'g�𝞽Tvz����(m��O�CyqfE���W��g\\P��{��œw(oc�Q��{.���#Q3��h^�%z��%��7s��`���{����׺.hSUT?l~`j?�7�sX��ˮ�����5���K��]��%y���|V!�\"(�� ��p����=�^�>���HU��[�Y]��z(;�(�ob�.�-�j�u�o�[�w]��ezV�U�a�0�QS���|ɜ����BϮmvӍw(E16,����]�[{��/�W�q_*\r�pi�0�k�\$j���uo�ҩ�Ԅ��Φ�=����~X���.�_:�4��U��)c\"��u��=j����}ũ�5��`���;|&���TLTo�U� Mʮ	�U]�C��}|b������ꯏY���|����KU�o��)�8�\"��\$��ras\0P��Wε�b*̗����LƢ�\r_:ǵ|o�1!�w}�ezڿ�o�V�M|���M:�Vn�<V����Ua�镇o��̅����W{�+���'GPAV+(Uu�kx	���Yz�-p\n�P�֫��V�E�;��ݫsӎLL���iy�����V���Rݎ%��7�+��Ŀ|J�������_�)Z�\0,:������V��1Z^��@�֗�vֹ�W[��[���Y��ن�Lڶ��j�w�W�߷�7�Ε�:��I�V��/[�a���UëT����\n��\0+{�+��0����9������Z�T�<,Ko�1�m(�b�W��t�,����٥�#9����5e�h��I����*|���AS���P\n���\"'�6f+����_�E���wU�e�Tr�mR���)�o_��Ln�\0k[���(�v��Q\\j�e��5.��~N)ȸ��mؠb�`����]i�H�p��;w\0q�:l\0S�Qw}��\0��\\��Xü�I�p���=6�)��DN&<6�1\n���8���m��D�^	��5ex��)������G,f���[�G.u2t�8ø}��F|�~�q?A������P��szJ��	���y��x��\r*���I̢q<2�1���1\0c\0��i�:�C�R�@�8����-\0c�`aJ��Ls�����1�	���Q��e�EN�)L�R�&`Q��\0�0��6'mM���k��%�.G�oY �S�m��I��¤|����P(�P\r��G+х\0U��7/Mb�U&.R�\\|>\0JMᰩ+@L2�\\6�**E�)�Z���V�1\nmb�I��*�9{�t���z��m��F��\0l[������2��Kq�:�����N\n�]�(��:���Q�|xG	rތu�(c��.�����1�%ԋ��'�f^��	�-��a��X u�k��v�`�ų�~����2�ׅ ���f	0�Ҋ�k�\"\r;�)eX>p�8J�)N��Jl8��F��Á��+uYM����^�Y��'��(\n�XâU�s#�0��t�JIÁ��m2r���)����]�0���<;�p�-�ê`6k�L,8y�@��`4]�o�9�Vp}���1�.\"���6���0F �0�\\\0�b\r�0���,)%�E�Ee)�c��<D+�N�0|'8���pI	�T&4����2��\\F\0�q%1GՈ`:�Ȣ؎��aM�U4� ��w���UbA�#R�T�Bx[F��o���l4�8\"R�wbQ���}�n�xof��w�ll0_�@D����)�����\0e ��m���1�2Sr�LO{��q��,!�',OO�;bnD��<b �5�t�;�n��	JOn(\\N���+�c��6(�)�v��b~\0��f''���d��86�1�r٪xJ�f7-V�����H�8��ʝX68�0L����b��D��x������Y�\0EFe���\0����~\n�yV*���W~C�I��@3�7�n�P¾ߞ����E�ِ�*l6��懱6ō��߫\rl�,\\B1nDlh':,>.H����'D+��uRh��\n�FłNKv.�Z�\0=b��g0EQE}�\nq����vWxT����t�C�Ĩ8��0��0CMW�Bp��/l?�I�m���/5Q R\0�@;JB%Ґ���@�AT�ƶ\"�j�<f��\ny��l?��J��v���v,4r�x)T���4�4B��	\r�Ə,ˍLh��v'ƈTG<�%�.t����t�h�9jr�����&q���m\0pq�[EV�4^)pEa!_�.6\0���A�C�M�'\$DEi>	� �'�k�W�D0�\r`\nB(@&\\w>��r���)��U�h�9�n`q�,��+��8W�G@��,��L���a�#8�Wc��5��:<mNvԃ�v��,ܠ\\�\$��*K�4���5�uf��:IҞ�&<����H�mf�L4�y�p�");}elseif($_GET["file"]=="logo.png"){header("Content-Type: image/png");echo"�PNG\r\n\n\0\0\0\rIHDR\0\0\09\0\0\09\0\0\0~6��\0\0\0000PLTE\0\0\0���+NvYt�s���������������su�IJ����/.�������C��\0\0\0tRNS\0@��f\0\0\0	pHYs\0\0\0\0\0��\0\0�IDAT8�Ք�N�@��E��l϶��p6�G.\$=���>��	w5r}�z7�>��P�#\$��K�j�7��ݶ����?4m�����t&�~�3!0�0��^��Af0�\"��,��*��4���o�E���X(*Y��	6	�PcOW���܊m��r�0�~/��L�\rXj#�m���j�C�]G�m�\0�}���ߑu�A9�X�\n��8�V�Y�+�D#�iq�nKQ8J�1Q6��Y0�`��P�bQ�\\h�~>�:pSɀ������GE�Q=�I�{�*�3�2�7�\ne�L�B�~�/R(\$�)�� ��HQn�i�6J�	<��-.�w�ɪj�Vm���m�?S�H��v����Ʃ��\0��^�q��)���]��U�92�,;�Ǎ�'p���!X˃����L�D.�tæ��/w����R��	w�d��r2�Ƥ�4[=�E5�S+�c\0\0\0\0IEND�B`�";}exit;}if($_GET["script"]=="version"){$o=get_temp_dir()."/adminer.version";@unlink($o);$q=file_open_lock($o);if($q)file_write_unlock($q,serialize(array("signature"=>$_POST["signature"],"version"=>$_POST["version"])));exit;}if(!$_SERVER["REQUEST_URI"])$_SERVER["REQUEST_URI"]=$_SERVER["ORIG_PATH_INFO"];if(!strpos($_SERVER["REQUEST_URI"],'?')&&$_SERVER["QUERY_STRING"]!="")$_SERVER["REQUEST_URI"].="?$_SERVER[QUERY_STRING]";if($_SERVER["HTTP_X_FORWARDED_PREFIX"])$_SERVER["REQUEST_URI"]=$_SERVER["HTTP_X_FORWARDED_PREFIX"].$_SERVER["REQUEST_URI"];define('Adminer\HTTPS',($_SERVER["HTTPS"]&&strcasecmp($_SERVER["HTTPS"],"off"))||ini_bool("session.cookie_secure"));@ini_set("session.use_trans_sid",'0');if(!defined("SID")){session_cache_limiter("");session_name("adminer_sid");session_set_cookie_params(0,preg_replace('~\?.*~','',$_SERVER["REQUEST_URI"]),"",HTTPS,true);session_start();}remove_slashes(array(&$_GET,&$_POST,&$_COOKIE),$bd);if(function_exists("get_magic_quotes_runtime")&&get_magic_quotes_runtime())set_magic_quotes_runtime(false);@set_time_limit(0);@ini_set("precision",'15');function
lang($u,$Kf=null){$ua=func_get_args();$ua[0]=$u;return
call_user_func_array('Adminer\lang_format',$ua);}function
lang_format($ej,$Kf=null){if(is_array($ej)){$Og=($Kf==1?0:1);$ej=$ej[$Og];}$ej=str_replace("'",'’',$ej);$ua=func_get_args();array_shift($ua);$nd=str_replace("%d","%s",$ej);if($nd!=$ej)$ua[0]=format_number($Kf);return
vsprintf($nd,$ua);}define('Adminer\LANG','zh');abstract
class
SqlDb{static$instance;var$extension;var$flavor='';var$server_info;var$affected_rows=0;var$info='';var$errno=0;var$error='';protected$multi;abstract
function
attach($N,$V,$F);abstract
function
quote($Q);abstract
function
select_db($Ob);abstract
function
query($H,$oj=false);function
multi_query($H){return$this->multi=$this->query($H);}function
store_result(){return$this->multi;}function
next_result(){return
false;}}if(extension_loaded('pdo')){abstract
class
PdoDb
extends
SqlDb{protected$pdo;function
dsn($oc,$V,$F,array$cg=array()){$cg[\PDO::ATTR_ERRMODE]=\PDO::ERRMODE_SILENT;$cg[\PDO::ATTR_STATEMENT_CLASS]=array('Adminer\PdoResult');try{$this->pdo=new
\PDO($oc,$V,$F,$cg);}catch(\Exception$Ic){return$Ic->getMessage();}$this->server_info=@$this->pdo->getAttribute(\PDO::ATTR_SERVER_VERSION);return'';}function
quote($Q){return$this->pdo->quote($Q);}function
query($H,$oj=false){$I=$this->pdo->query($H);$this->error="";if(!$I){list(,$this->errno,$this->error)=$this->pdo->errorInfo();if(!$this->error)$this->error='未知错误。';return
false;}$this->store_result($I);return$I;}function
store_result($I=null){if(!$I){$I=$this->multi;if(!$I)return
false;}if($I->columnCount()){$I->num_rows=$I->rowCount();return$I;}$this->affected_rows=$I->rowCount();return
true;}function
next_result(){$I=$this->multi;if(!is_object($I))return
false;$I->_offset=0;return@$I->nextRowset();}}class
PdoResult
extends
\PDOStatement{var$_offset=0,$num_rows;function
fetch_assoc(){return$this->fetch_array(\PDO::FETCH_ASSOC);}function
fetch_row(){return$this->fetch_array(\PDO::FETCH_NUM);}private
function
fetch_array($uf){$J=$this->fetch($uf);return($J?array_map(array($this,'unresource'),$J):$J);}private
function
unresource($X){return(is_resource($X)?stream_get_contents($X):$X);}function
fetch_field(){$K=(object)$this->getColumnMeta($this->_offset++);$U=$K->pdo_type;$K->type=($U==\PDO::PARAM_INT?0:15);$K->charsetnr=($U==\PDO::PARAM_LOB||(isset($K->flags)&&in_array("blob",(array)$K->flags))?63:0);return$K;}function
seek($C){for($s=0;$s<$C;$s++)$this->fetch();}}}function
add_driver($t,$B){SqlDriver::$drivers[$t]=$B;}function
get_driver($t){return
SqlDriver::$drivers[$t];}abstract
class
SqlDriver{static$instance;static$drivers=array();static$extensions=array();static$jush;protected$conn;protected$types=array();var$insertFunctions=array();var$editFunctions=array();var$unsigned=array();var$operators=array();var$functions=array();var$grouping=array();var$onActions="RESTRICT|NO ACTION|CASCADE|SET NULL|SET DEFAULT";var$partitionBy=array();var$inout="IN|OUT|INOUT";var$enumLength="'(?:''|[^'\\\\]|\\\\.)*'";var$generated=array();static
function
connect($N,$V,$F){$f=new
Db;return($f->attach($N,$V,$F)?:$f);}function
__construct(Db$f){$this->conn=$f;}function
types(){return
call_user_func_array('array_merge',array_values($this->types));}function
structuredTypes(){return
array_map('array_keys',$this->types);}function
enumLength(array$m){}function
unconvertFunction(array$m){}function
select($R,array$M,array$Z,array$xd,array$eg=array(),$z=1,$D=0,$Xg=false){$ve=(count($xd)<count($M));$H=adminer()->selectQueryBuild($M,$Z,$xd,$eg,$z,$D);if(!$H)$H="SELECT".limit(($_GET["page"]!="last"&&$z&&$xd&&$ve&&JUSH=="sql"?"SQL_CALC_FOUND_ROWS ":"").implode(", ",$M)."\nFROM ".table($R),($Z?"\nWHERE ".implode(" AND ",$Z):"").($xd&&$ve?"\nGROUP BY ".implode(", ",$xd):"").($eg?"\nORDER BY ".implode(", ",$eg):""),$z,($D?$z*$D:0),"\n");$pi=microtime(true);$J=$this->conn->query($H);if($Xg)echo
adminer()->selectQuery($H,$pi,!$J);return$J;}function
delete($R,$gh,$z=0){$H="FROM ".table($R);return
queries("DELETE".($z?limit1($R,$H,$gh):" $H$gh"));}function
update($R,array$O,$gh,$z=0,$Sh="\n"){$Ij=array();foreach($O
as$x=>$X)$Ij[]="$x = $X";$H=table($R)." SET$Sh".implode(",$Sh",$Ij);return
queries("UPDATE".($z?limit1($R,$H,$gh,$Sh):" $H$gh"));}function
insert($R,array$O){return
queries("INSERT INTO ".table($R).($O?" (".implode(", ",array_keys($O)).")\nVALUES (".implode(", ",$O).")":" DEFAULT VALUES").$this->insertReturning($R));}function
insertReturning($R){return"";}function
insertUpdate($R,array$L,array$G){return
false;}function
begin(){return
queries("BEGIN");}function
commit(){return
queries("COMMIT");}function
rollback(){return
queries("ROLLBACK");}function
slowQuery($H,$Ri){}function
convertSearch($u,array$X,array$m){return$u;}function
value($X,array$m){return(method_exists($this->conn,'value')?$this->conn->value($X,$m):$X);}function
quoteBinary($Eh){return
q($Eh);}function
warnings(){}function
tableHelp($B,$ze=false){}function
inheritsFrom($R){return
array();}function
inheritedTables($R){return
array();}function
partitionsInfo($R){return
array();}function
hasCStyleEscapes(){return
false;}function
engines(){return
array();}function
supportsIndex(array$S){return!is_view($S);}function
indexAlgorithms(array$zi){return
array();}function
checkConstraints($R){return
get_key_vals("SELECT c.CONSTRAINT_NAME, CHECK_CLAUSE
FROM INFORMATION_SCHEMA.CHECK_CONSTRAINTS c
JOIN INFORMATION_SCHEMA.TABLE_CONSTRAINTS t ON c.CONSTRAINT_SCHEMA = t.CONSTRAINT_SCHEMA AND c.CONSTRAINT_NAME = t.CONSTRAINT_NAME
WHERE c.CONSTRAINT_SCHEMA = ".q($_GET["ns"]!=""?$_GET["ns"]:DB)."
AND t.TABLE_NAME = ".q($R)."
AND CHECK_CLAUSE NOT LIKE '% IS NOT NULL'",$this->conn);}function
allFields(){$J=array();if(DB!=""){foreach(get_rows("SELECT TABLE_NAME AS tab, COLUMN_NAME AS field, IS_NULLABLE AS nullable, DATA_TYPE AS type, CHARACTER_MAXIMUM_LENGTH AS length".(JUSH=='sql'?", COLUMN_KEY = 'PRI' AS `primary`":"")."
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = ".q($_GET["ns"]!=""?$_GET["ns"]:DB)."
ORDER BY TABLE_NAME, ORDINAL_POSITION",$this->conn)as$K){$K["null"]=($K["nullable"]=="YES");$J[$K["tab"]][]=$K;}}return$J;}}add_driver("sqlite","SQLite");if(isset($_GET["sqlite"])){define('Adminer\DRIVER',"sqlite");if(class_exists("SQLite3")&&$_GET["ext"]!="pdo"){abstract
class
SqliteDb
extends
SqlDb{var$extension="SQLite3";private$link;function
attach($o,$V,$F){$this->link=new
\SQLite3($o);$Lj=$this->link->version();$this->server_info=$Lj["versionString"];return'';}function
query($H,$oj=false){$I=@$this->link->query($H);$this->error="";if(!$I){$this->errno=$this->link->lastErrorCode();$this->error=$this->link->lastErrorMsg();return
false;}elseif($I->numColumns())return
new
Result($I);$this->affected_rows=$this->link->changes();return
true;}function
quote($Q){return(is_utf8($Q)?"'".$this->link->escapeString($Q)."'":"x'".first(unpack('H*',$Q))."'");}}class
Result{var$num_rows;private$result,$offset=0;function
__construct($I){$this->result=$I;}function
fetch_assoc(){return$this->result->fetchArray(SQLITE3_ASSOC);}function
fetch_row(){return$this->result->fetchArray(SQLITE3_NUM);}function
fetch_field(){$d=$this->offset++;$U=$this->result->columnType($d);return(object)array("name"=>$this->result->columnName($d),"type"=>($U==SQLITE3_TEXT?15:0),"charsetnr"=>($U==SQLITE3_BLOB?63:0),);}function
__destruct(){$this->result->finalize();}}}elseif(extension_loaded("pdo_sqlite")){abstract
class
SqliteDb
extends
PdoDb{var$extension="PDO_SQLite";function
attach($o,$V,$F){return$this->dsn(DRIVER.":$o","","");}}}if(class_exists('Adminer\SqliteDb')){class
Db
extends
SqliteDb{function
attach($o,$V,$F){parent::attach($o,$V,$F);$this->query("PRAGMA foreign_keys = 1");$this->query("PRAGMA busy_timeout = 500");return'';}function
select_db($o){if(is_readable($o)&&$this->query("ATTACH ".$this->quote(preg_match("~(^[/\\\\]|:)~",$o)?$o:dirname($_SERVER["SCRIPT_FILENAME"])."/$o")." AS a"))return!self::attach($o,'','');return
false;}}}class
Driver
extends
SqlDriver{static$extensions=array("SQLite3","PDO_SQLite");static$jush="sqlite";protected$types=array(array("integer"=>0,"real"=>0,"numeric"=>0,"text"=>0,"blob"=>0));var$insertFunctions=array();var$editFunctions=array("integer|real|numeric"=>"+/-","text"=>"||",);var$operators=array("=","<",">","<=",">=","!=","LIKE","LIKE %%","IN","IS NULL","NOT LIKE","NOT IN","IS NOT NULL","SQL");var$functions=array("hex","length","lower","round","unixepoch","upper");var$grouping=array("avg","count","count distinct","group_concat","max","min","sum");static
function
connect($N,$V,$F){if($F!="")return'数据库不支持密码。';return
parent::connect(":memory:","","");}function
__construct(Db$f){parent::__construct($f);if(min_version(3.31,0,$f))$this->generated=array("STORED","VIRTUAL");}function
structuredTypes(){return
array_keys($this->types[0]);}function
insertUpdate($R,array$L,array$G){$Ij=array();foreach($L
as$O)$Ij[]="(".implode(", ",$O).")";return
queries("REPLACE INTO ".table($R)." (".implode(", ",array_keys(reset($L))).") VALUES\n".implode(",\n",$Ij));}function
tableHelp($B,$ze=false){if($B=="sqlite_sequence")return"fileformat2.html#seqtab";if($B=="sqlite_master")return"fileformat2.html#$B";}function
checkConstraints($R){preg_match_all('~ CHECK *(\( *(((?>[^()]*[^() ])|(?1))*) *\))~',get_val("SELECT sql FROM sqlite_master WHERE type = 'table' AND name = ".q($R),0,$this->conn),$af);return
array_combine($af[2],$af[2]);}function
allFields(){$J=array();foreach(tables_list()as$R=>$U){foreach(fields($R)as$m)$J[$R][]=$m;}return$J;}}function
idf_escape($u){return'"'.str_replace('"','""',$u).'"';}function
table($u){return
idf_escape($u);}function
get_databases($id){return
array();}function
limit($H,$Z,$z,$C=0,$Sh=" "){return" $H$Z".($z?$Sh."LIMIT $z".($C?" OFFSET $C":""):"");}function
limit1($R,$H,$Z,$Sh="\n"){return(preg_match('~^INTO~',$H)||get_val("SELECT sqlite_compileoption_used('ENABLE_UPDATE_DELETE_LIMIT')")?limit($H,$Z,1,0,$Sh):" $H WHERE rowid = (SELECT rowid FROM ".table($R).$Z.$Sh."LIMIT 1)");}function
db_collation($j,$jb){return
get_val("PRAGMA encoding");}function
logged_user(){return
get_current_user();}function
tables_list(){return
get_key_vals("SELECT name, type FROM sqlite_master WHERE type IN ('table', 'view') ORDER BY (name = 'sqlite_sequence'), name");}function
count_tables($i){return
array();}function
table_status($B=""){$J=array();foreach(get_rows("SELECT name AS Name, type AS Engine, 'rowid' AS Oid, '' AS Auto_increment FROM sqlite_master WHERE type IN ('table', 'view') ".($B!=""?"AND name = ".q($B):"ORDER BY name"))as$K){$K["Rows"]=get_val("SELECT COUNT(*) FROM ".idf_escape($K["Name"]));$J[$K["Name"]]=$K;}foreach(get_rows("SELECT * FROM sqlite_sequence".($B!=""?" WHERE name = ".q($B):""),null,"")as$K)$J[$K["name"]]["Auto_increment"]=$K["seq"];return$J;}function
is_view($S){return$S["Engine"]=="view";}function
fk_support($S){return!get_val("SELECT sqlite_compileoption_used('OMIT_FOREIGN_KEY')");}function
fields($R){$J=array();$G="";foreach(get_rows("PRAGMA table_".(min_version(3.31)?"x":"")."info(".table($R).")")as$K){$B=$K["name"];$U=strtolower($K["type"]);$k=$K["dflt_value"];$J[$B]=array("field"=>$B,"type"=>(preg_match('~int~i',$U)?"integer":(preg_match('~char|clob|text~i',$U)?"text":(preg_match('~blob~i',$U)?"blob":(preg_match('~real|floa|doub~i',$U)?"real":"numeric")))),"full_type"=>$U,"default"=>(preg_match("~^'(.*)'$~",$k,$A)?str_replace("''","'",$A[1]):($k=="NULL"?null:$k)),"null"=>!$K["notnull"],"privileges"=>array("select"=>1,"insert"=>1,"update"=>1,"where"=>1,"order"=>1),"primary"=>$K["pk"],);if($K["pk"]){if($G!="")$J[$G]["auto_increment"]=false;elseif(preg_match('~^integer$~i',$U))$J[$B]["auto_increment"]=true;$G=$B;}}$ji=get_val("SELECT sql FROM sqlite_master WHERE type = 'table' AND name = ".q($R));$u='(("[^"]*+")+|[a-z0-9_]+)';preg_match_all('~'.$u.'\s+text\s+COLLATE\s+(\'[^\']+\'|\S+)~i',$ji,$af,PREG_SET_ORDER);foreach($af
as$A){$B=str_replace('""','"',preg_replace('~^"|"$~','',$A[1]));if($J[$B])$J[$B]["collation"]=trim($A[3],"'");}preg_match_all('~'.$u.'\s.*GENERATED ALWAYS AS \((.+)\) (STORED|VIRTUAL)~i',$ji,$af,PREG_SET_ORDER);foreach($af
as$A){$B=str_replace('""','"',preg_replace('~^"|"$~','',$A[1]));$J[$B]["default"]=$A[3];$J[$B]["generated"]=strtoupper($A[4]);}return$J;}function
indexes($R,$g=null){$g=connection($g);$J=array();$ji=get_val("SELECT sql FROM sqlite_master WHERE type = 'table' AND name = ".q($R),0,$g);if(preg_match('~\bPRIMARY\s+KEY\s*\((([^)"]+|"[^"]*"|`[^`]*`)++)~i',$ji,$A)){$J[""]=array("type"=>"PRIMARY","columns"=>array(),"lengths"=>array(),"descs"=>array());preg_match_all('~((("[^"]*+")+|(?:`[^`]*+`)+)|(\S+))(\s+(ASC|DESC))?(,\s*|$)~i',$A[1],$af,PREG_SET_ORDER);foreach($af
as$A){$J[""]["columns"][]=idf_unescape($A[2]).$A[4];$J[""]["descs"][]=(preg_match('~DESC~i',$A[5])?'1':null);}}if(!$J){foreach(fields($R)as$B=>$m){if($m["primary"])$J[""]=array("type"=>"PRIMARY","columns"=>array($B),"lengths"=>array(),"descs"=>array(null));}}$ni=get_key_vals("SELECT name, sql FROM sqlite_master WHERE type = 'index' AND tbl_name = ".q($R),$g);foreach(get_rows("PRAGMA index_list(".table($R).")",$g)as$K){$B=$K["name"];$v=array("type"=>($K["unique"]?"UNIQUE":"INDEX"));$v["lengths"]=array();$v["descs"]=array();foreach(get_rows("PRAGMA index_info(".idf_escape($B).")",$g)as$Dh){$v["columns"][]=$Dh["name"];$v["descs"][]=null;}if(preg_match('~^CREATE( UNIQUE)? INDEX '.preg_quote(idf_escape($B).' ON '.idf_escape($R),'~').' \((.*)\)$~i',$ni[$B],$rh)){preg_match_all('/("[^"]*+")+( DESC)?/',$rh[2],$af);foreach($af[2]as$x=>$X){if($X)$v["descs"][$x]='1';}}if(!$J[""]||$v["type"]!="UNIQUE"||$v["columns"]!=$J[""]["columns"]||$v["descs"]!=$J[""]["descs"]||!preg_match("~^sqlite_~",$B))$J[$B]=$v;}return$J;}function
foreign_keys($R){$J=array();foreach(get_rows("PRAGMA foreign_key_list(".table($R).")")as$K){$p=&$J[$K["id"]];if(!$p)$p=$K;$p["source"][]=$K["from"];$p["target"][]=$K["to"];}return$J;}function
view($B){return
array("select"=>preg_replace('~^(?:[^`"[]+|`[^`]*`|"[^"]*")* AS\s+~iU','',get_val("SELECT sql FROM sqlite_master WHERE type = 'view' AND name = ".q($B))));}function
collations(){return(isset($_GET["create"])?get_vals("PRAGMA collation_list",1):array());}function
information_schema($j){return
false;}function
error(){return
h(connection()->error);}function
check_sqlite_name($B){$Qc="db|sdb|sqlite";if(!preg_match("~^[^\\0]*\\.($Qc)\$~",$B)){connection()->error=sprintf('请使用其中一个扩展：%s。',str_replace("|",", ",$Qc));return
false;}return
true;}function
create_database($j,$c){if(file_exists($j)){connection()->error='文件已存在。';return
false;}if(!check_sqlite_name($j))return
false;try{$_=new
Db();$_->attach($j,'','');}catch(\Exception$Ic){connection()->error=$Ic->getMessage();return
false;}$_->query('PRAGMA encoding = "UTF-8"');$_->query('CREATE TABLE adminer (i)');$_->query('DROP TABLE adminer');return
true;}function
drop_databases($i){connection()->attach(":memory:",'','');foreach($i
as$j){if(!@unlink($j)){connection()->error='文件已存在。';return
false;}}return
true;}function
rename_database($B,$c){if(!check_sqlite_name($B))return
false;connection()->attach(":memory:",'','');connection()->error='文件已存在。';return@rename(DB,$B);}function
auto_increment(){return" PRIMARY KEY AUTOINCREMENT";}function
alter_table($R,$B,$n,$kd,$ob,$yc,$c,$_a,$E){$Bj=($R==""||$kd);foreach($n
as$m){if($m[0]!=""||!$m[1]||$m[2]){$Bj=true;break;}}$b=array();$pg=array();foreach($n
as$m){if($m[1]){$b[]=($Bj?$m[1]:"ADD ".implode($m[1]));if($m[0]!="")$pg[$m[0]]=$m[1][0];}}if(!$Bj){foreach($b
as$X){if(!queries("ALTER TABLE ".table($R)." $X"))return
false;}if($R!=$B&&!queries("ALTER TABLE ".table($R)." RENAME TO ".table($B)))return
false;}elseif(!recreate_table($R,$B,$b,$pg,$kd,$_a))return
false;if($_a){queries("BEGIN");queries("UPDATE sqlite_sequence SET seq = $_a WHERE name = ".q($B));if(!connection()->affected_rows)queries("INSERT INTO sqlite_sequence (name, seq) VALUES (".q($B).", $_a)");queries("COMMIT");}return
true;}function
recreate_table($R,$B,array$n,array$pg,array$kd,$_a="",$w=array(),$kc="",$ja=""){if($R!=""){if(!$n){foreach(fields($R)as$x=>$m){if($w)$m["auto_increment"]=0;$n[]=process_field($m,$m);$pg[$x]=idf_escape($x);}}$Wg=false;foreach($n
as$m){if($m[6])$Wg=true;}$mc=array();foreach($w
as$x=>$X){if($X[2]=="DROP"){$mc[$X[1]]=true;unset($w[$x]);}}foreach(indexes($R)as$Ce=>$v){$e=array();foreach($v["columns"]as$x=>$d){if(!$pg[$d])continue
2;$e[]=$pg[$d].($v["descs"][$x]?" DESC":"");}if(!$mc[$Ce]){if($v["type"]!="PRIMARY"||!$Wg)$w[]=array($v["type"],$Ce,$e);}}foreach($w
as$x=>$X){if($X[0]=="PRIMARY"){unset($w[$x]);$kd[]="  PRIMARY KEY (".implode(", ",$X[2]).")";}}foreach(foreign_keys($R)as$Ce=>$p){foreach($p["source"]as$x=>$d){if(!$pg[$d])continue
2;$p["source"][$x]=idf_unescape($pg[$d]);}if(!isset($kd[" $Ce"]))$kd[]=" ".format_foreign_key($p);}queries("BEGIN");}$Ua=array();foreach($n
as$m){if(preg_match('~GENERATED~',$m[3]))unset($pg[array_search($m[0],$pg)]);$Ua[]="  ".implode($m);}$Ua=array_merge($Ua,array_filter($kd));foreach(driver()->checkConstraints($R)as$Wa){if($Wa!=$kc)$Ua[]="  CHECK ($Wa)";}if($ja)$Ua[]="  CHECK ($ja)";$Li=($R==$B?"adminer_$B":$B);if(!queries("CREATE TABLE ".table($Li)." (\n".implode(",\n",$Ua)."\n)"))return
false;if($R!=""){if($pg&&!queries("INSERT INTO ".table($Li)." (".implode(", ",$pg).") SELECT ".implode(", ",array_map('Adminer\idf_escape',array_keys($pg)))." FROM ".table($R)))return
false;$lj=array();foreach(triggers($R)as$jj=>$Si){$ij=trigger($jj,$R);$lj[]="CREATE TRIGGER ".idf_escape($jj)." ".implode(" ",$Si)." ON ".table($B)."\n$ij[Statement]";}$_a=$_a?"":get_val("SELECT seq FROM sqlite_sequence WHERE name = ".q($R));if(!queries("DROP TABLE ".table($R))||($R==$B&&!queries("ALTER TABLE ".table($Li)." RENAME TO ".table($B)))||!alter_indexes($B,$w))return
false;if($_a)queries("UPDATE sqlite_sequence SET seq = $_a WHERE name = ".q($B));foreach($lj
as$ij){if(!queries($ij))return
false;}queries("COMMIT");}return
true;}function
index_sql($R,$U,$B,$e){return"CREATE $U ".($U!="INDEX"?"INDEX ":"").idf_escape($B!=""?$B:uniqid($R."_"))." ON ".table($R)." $e";}function
alter_indexes($R,$b){foreach($b
as$G){if($G[0]=="PRIMARY")return
recreate_table($R,$R,array(),array(),array(),"",$b);}foreach(array_reverse($b)as$X){if(!queries($X[2]=="DROP"?"DROP INDEX ".idf_escape($X[1]):index_sql($R,$X[0],$X[1],"(".implode(", ",$X[2]).")")))return
false;}return
true;}function
truncate_tables($T){return
apply_queries("DELETE FROM",$T);}function
drop_views($Nj){return
apply_queries("DROP VIEW",$Nj);}function
drop_tables($T){return
apply_queries("DROP TABLE",$T);}function
move_tables($T,$Nj,$Ji){return
false;}function
trigger($B,$R){if($B=="")return
array("Statement"=>"BEGIN\n\t;\nEND");$u='(?:[^`"\s]+|`[^`]*`|"[^"]*")+';$kj=trigger_options();preg_match("~^CREATE\\s+TRIGGER\\s*$u\\s*(".implode("|",$kj["Timing"]).")\\s+([a-z]+)(?:\\s+OF\\s+($u))?\\s+ON\\s*$u\\s*(?:FOR\\s+EACH\\s+ROW\\s)?(.*)~is",get_val("SELECT sql FROM sqlite_master WHERE type = 'trigger' AND name = ".q($B)),$A);$Mf=$A[3];return
array("Timing"=>strtoupper($A[1]),"Event"=>strtoupper($A[2]).($Mf?" OF":""),"Of"=>idf_unescape($Mf),"Trigger"=>$B,"Statement"=>$A[4],);}function
triggers($R){$J=array();$kj=trigger_options();foreach(get_rows("SELECT * FROM sqlite_master WHERE type = 'trigger' AND tbl_name = ".q($R))as$K){preg_match('~^CREATE\s+TRIGGER\s*(?:[^`"\s]+|`[^`]*`|"[^"]*")+\s*('.implode("|",$kj["Timing"]).')\s*(.*?)\s+ON\b~i',$K["sql"],$A);$J[$K["name"]]=array($A[1],$A[2]);}return$J;}function
trigger_options(){return
array("Timing"=>array("BEFORE","AFTER","INSTEAD OF"),"Event"=>array("INSERT","UPDATE","UPDATE OF","DELETE"),"Type"=>array("FOR EACH ROW"),);}function
begin(){return
queries("BEGIN");}function
last_id($I){return
get_val("SELECT LAST_INSERT_ROWID()");}function
explain($f,$H){return$f->query("EXPLAIN QUERY PLAN $H");}function
found_rows($S,$Z){}function
types(){return
array();}function
create_sql($R,$_a,$ti){$J=get_val("SELECT sql FROM sqlite_master WHERE type IN ('table', 'view') AND name = ".q($R));foreach(indexes($R)as$B=>$v){if($B=='')continue;$J
.=";\n\n".index_sql($R,$v['type'],$B,"(".implode(", ",array_map('Adminer\idf_escape',$v['columns'])).")");}return$J;}function
truncate_sql($R){return"DELETE FROM ".table($R);}function
use_sql($Ob,$ti=""){}function
trigger_sql($R){return
implode(get_vals("SELECT sql || ';;\n' FROM sqlite_master WHERE type = 'trigger' AND tbl_name = ".q($R)));}function
show_variables(){$J=array();foreach(get_rows("PRAGMA pragma_list")as$K){$B=$K["name"];if($B!="pragma_list"&&$B!="compile_options"){$J[$B]=array($B,'');foreach(get_rows("PRAGMA $B")as$K)$J[$B][1].=implode(", ",$K)."\n";}}return$J;}function
show_status(){$J=array();foreach(get_vals("PRAGMA compile_options")as$bg)$J[]=explode("=",$bg,2)+array('','');return$J;}function
convert_field($m){}function
unconvert_field($m,$J){return$J;}function
support($Vc){return
preg_match('~^(check|columns|database|drop_col|dump|indexes|descidx|move_col|sql|status|table|trigger|variables|view|view_trigger)$~',$Vc);}}add_driver("pgsql","PostgreSQL");if(isset($_GET["pgsql"])){define('Adminer\DRIVER',"pgsql");if(extension_loaded("pgsql")&&$_GET["ext"]!="pdo"){class
PgsqlDb
extends
SqlDb{var$extension="PgSQL";var$timeout=0;private$link,$string,$database=true;function
_error($Dc,$l){if(ini_bool("html_errors"))$l=html_entity_decode(strip_tags($l));$l=preg_replace('~^[^:]*: ~','',$l);$this->error=$l;}function
attach($N,$V,$F){$j=adminer()->database();set_error_handler(array($this,'_error'));list($Nd,$Ng)=host_port(addcslashes($N,"'\\"));$this->string="host='$Nd'".($Ng?" port='$Ng'":"")." user='".addcslashes($V,"'\\")."' password='".addcslashes($F,"'\\")."'";$oi=adminer()->connectSsl();if(isset($oi["mode"]))$this->string
.=" sslmode='".$oi["mode"]."'";$this->link=@pg_connect("$this->string dbname='".($j!=""?addcslashes($j,"'\\"):"postgres")."'",PGSQL_CONNECT_FORCE_NEW);if(!$this->link&&$j!=""){$this->database=false;$this->link=@pg_connect("$this->string dbname='postgres'",PGSQL_CONNECT_FORCE_NEW);}restore_error_handler();if($this->link)pg_set_client_encoding($this->link,"UTF8");return($this->link?'':$this->error);}function
quote($Q){return(function_exists('pg_escape_literal')?pg_escape_literal($this->link,$Q):"'".pg_escape_string($this->link,$Q)."'");}function
value($X,array$m){return($m["type"]=="bytea"&&$X!==null?pg_unescape_bytea($X):$X);}function
select_db($Ob){if($Ob==adminer()->database())return$this->database;$J=@pg_connect("$this->string dbname='".addcslashes($Ob,"'\\")."'",PGSQL_CONNECT_FORCE_NEW);if($J)$this->link=$J;return$J;}function
close(){$this->link=@pg_connect("$this->string dbname='postgres'");}function
query($H,$oj=false){$I=@pg_query($this->link,$H);$this->error="";if(!$I){$this->error=pg_last_error($this->link);$J=false;}elseif(!pg_num_fields($I)){$this->affected_rows=pg_affected_rows($I);$J=true;}else$J=new
Result($I);if($this->timeout){$this->timeout=0;$this->query("RESET statement_timeout");}return$J;}function
warnings(){return
h(pg_last_notice($this->link));}function
copyFrom($R,array$L){$this->error='';set_error_handler(function($Dc,$l){$this->error=(ini_bool('html_errors')?html_entity_decode($l):$l);return
true;});$J=pg_copy_from($this->link,$R,$L);restore_error_handler();return$J;}}class
Result{var$num_rows;private$result,$offset=0;function
__construct($I){$this->result=$I;$this->num_rows=pg_num_rows($I);}function
fetch_assoc(){return
pg_fetch_assoc($this->result);}function
fetch_row(){return
pg_fetch_row($this->result);}function
fetch_field(){$d=$this->offset++;$J=new
\stdClass;$J->orgtable=pg_field_table($this->result,$d);$J->name=pg_field_name($this->result,$d);$U=pg_field_type($this->result,$d);$J->type=(preg_match(number_type(),$U)?0:15);$J->charsetnr=($U=="bytea"?63:0);return$J;}function
__destruct(){pg_free_result($this->result);}}}elseif(extension_loaded("pdo_pgsql")){class
PgsqlDb
extends
PdoDb{var$extension="PDO_PgSQL";var$timeout=0;function
attach($N,$V,$F){$j=adminer()->database();list($Nd,$Ng)=host_port(addcslashes($N,"'\\"));$oc="pgsql:host='$Nd'".($Ng?" port='$Ng'":"")." client_encoding=utf8 dbname='".($j!=""?addcslashes($j,"'\\"):"postgres")."'";$oi=adminer()->connectSsl();if(isset($oi["mode"]))$oc
.=" sslmode='".$oi["mode"]."'";return$this->dsn($oc,$V,$F);}function
select_db($Ob){return(adminer()->database()==$Ob);}function
query($H,$oj=false){$J=parent::query($H,$oj);if($this->timeout){$this->timeout=0;parent::query("RESET statement_timeout");}return$J;}function
warnings(){}function
copyFrom($R,array$L){$J=$this->pdo->pgsqlCopyFromArray($R,$L);$this->error=idx($this->pdo->errorInfo(),2)?:'';return$J;}function
close(){}}}if(class_exists('Adminer\PgsqlDb')){class
Db
extends
PgsqlDb{function
multi_query($H){if(preg_match('~\bCOPY\s+(.+?)\s+FROM\s+stdin;\n?(.*)\n\\\\\.$~is',str_replace("\r\n","\n",$H),$A)){$L=explode("\n",$A[2]);$this->affected_rows=count($L);return$this->copyFrom($A[1],$L);}return
parent::multi_query($H);}}}class
Driver
extends
SqlDriver{static$extensions=array("PgSQL","PDO_PgSQL");static$jush="pgsql";var$operators=array("=","<",">","<=",">=","!=","~","!~","LIKE","LIKE %%","ILIKE","ILIKE %%","IN","IS NULL","NOT LIKE","NOT ILIKE","NOT IN","IS NOT NULL","SQL");var$functions=array("char_length","lower","round","to_hex","to_timestamp","upper");var$grouping=array("avg","count","count distinct","max","min","sum");var$nsOid="(SELECT oid FROM pg_namespace WHERE nspname = current_schema())";static
function
connect($N,$V,$F){$f=parent::connect($N,$V,$F);if(is_string($f))return$f;$Lj=get_val("SELECT version()",0,$f);$f->flavor=(preg_match('~CockroachDB~',$Lj)?'cockroach':'');$f->server_info=preg_replace('~^\D*([\d.]+[-\w]*).*~','\1',$Lj);if(min_version(9,0,$f))$f->query("SET application_name = 'Adminer'");if($f->flavor=='cockroach')add_driver(DRIVER,"CockroachDB");return$f;}function
__construct(Db$f){parent::__construct($f);$this->types=array('数字'=>array("smallint"=>5,"integer"=>10,"bigint"=>19,"boolean"=>1,"numeric"=>0,"real"=>7,"double precision"=>16,"money"=>20),'日期时间'=>array("date"=>13,"time"=>17,"timestamp"=>20,"timestamptz"=>21,"interval"=>0),'字符串'=>array("character"=>0,"character varying"=>0,"text"=>0,"tsquery"=>0,"tsvector"=>0,"uuid"=>0,"xml"=>0),'二进制'=>array("bit"=>0,"bit varying"=>0,"bytea"=>0),'网络'=>array("cidr"=>43,"inet"=>43,"macaddr"=>17,"macaddr8"=>23,"txid_snapshot"=>0),'几何图形'=>array("box"=>0,"circle"=>0,"line"=>0,"lseg"=>0,"path"=>0,"point"=>0,"polygon"=>0),);if(min_version(9.2,0,$f)){$this->types['字符串']["json"]=4294967295;if(min_version(9.4,0,$f))$this->types['字符串']["jsonb"]=4294967295;}$this->insertFunctions=array("char"=>"md5","date|time"=>"now",);$this->editFunctions=array(number_type()=>"+/-","date|time"=>"+ interval/- interval","char|text"=>"||",);if(min_version(12,0,$f))$this->generated=array("STORED");$this->partitionBy=array("RANGE","LIST");if(!$f->flavor)$this->partitionBy[]="HASH";}function
enumLength(array$m){$_c=$this->types['用户类型'][$m["type"]];return($_c?type_values($_c):"");}function
setUserTypes($nj){$this->types['用户类型']=array_flip($nj);}function
insertReturning($R){$_a=array_filter(fields($R),function($m){return$m['auto_increment'];});return(count($_a)==1?" RETURNING ".idf_escape(key($_a)):"");}function
insertUpdate($R,array$L,array$G){foreach($L
as$O){$wj=array();$Z=array();foreach($O
as$x=>$X){$wj[]="$x = $X";if(isset($G[idf_unescape($x)]))$Z[]="$x = $X";}if(!(($Z&&queries("UPDATE ".table($R)." SET ".implode(", ",$wj)." WHERE ".implode(" AND ",$Z))&&$this->conn->affected_rows)||queries("INSERT INTO ".table($R)." (".implode(", ",array_keys($O)).") VALUES (".implode(", ",$O).")")))return
false;}return
true;}function
slowQuery($H,$Ri){$this->conn->query("SET statement_timeout = ".(1000*$Ri));$this->conn->timeout=1000*$Ri;return$H;}function
convertSearch($u,array$X,array$m){$Oi="char|text";if(strpos($X["op"],"LIKE")===false)$Oi
.="|date|time(stamp)?|boolean|uuid|inet|cidr|macaddr|".number_type();return(preg_match("~$Oi~",$m["type"])?$u:"CAST($u AS text)");}function
quoteBinary($Eh){return"'\\x".bin2hex($Eh)."'";}function
warnings(){return$this->conn->warnings();}function
tableHelp($B,$ze=false){$Se=array("information_schema"=>"infoschema","pg_catalog"=>($ze?"view":"catalog"),);$_=$Se[$_GET["ns"]];if($_)return"$_-".str_replace("_","-",$B).".html";}function
inheritsFrom($R){return
get_vals("SELECT relname FROM pg_class JOIN pg_inherits ON inhparent = oid WHERE inhrelid = ".$this->tableOid($R)." ORDER BY 1");}function
inheritedTables($R){return
get_vals("SELECT relname FROM pg_inherits JOIN pg_class ON inhrelid = oid WHERE inhparent = ".$this->tableOid($R)." ORDER BY 1");}function
partitionsInfo($R){$K=(min_version(10)?$this->conn->query("SELECT * FROM pg_partitioned_table WHERE partrelid = ".$this->tableOid($R))->fetch_assoc():null);if($K){$ya=get_vals("SELECT attname FROM pg_attribute WHERE attrelid = $K[partrelid] AND attnum IN (".str_replace(" ",", ",$K["partattrs"]).")");$Oa=array('h'=>'HASH','l'=>'LIST','r'=>'RANGE');return
array("partition_by"=>$Oa[$K["partstrat"]],"partition"=>implode(", ",array_map('Adminer\idf_escape',$ya)),);}return
array();}function
tableOid($R){return"(SELECT oid FROM pg_class WHERE relnamespace = $this->nsOid AND relname = ".q($R)." AND relkind IN ('r', 'm', 'v', 'f', 'p'))";}function
indexAlgorithms(array$zi){static$J=array();if(!$J)$J=get_vals("SELECT amname FROM pg_am".(min_version(9.6)?" WHERE amtype = 'i'":"")." ORDER BY amname = '".($this->conn->flavor=='cockroach'?"prefix":"btree")."' DESC, amname");return$J;}function
supportsIndex(array$S){return$S["Engine"]!="view";}function
hasCStyleEscapes(){static$Qa;if($Qa===null)$Qa=(get_val("SHOW standard_conforming_strings",0,$this->conn)=="off");return$Qa;}}function
idf_escape($u){return'"'.str_replace('"','""',$u).'"';}function
table($u){return
idf_escape($u);}function
get_databases($id){return
get_vals("SELECT datname FROM pg_database
WHERE datallowconn = TRUE AND has_database_privilege(datname, 'CONNECT')
ORDER BY datname");}function
limit($H,$Z,$z,$C=0,$Sh=" "){return" $H$Z".($z?$Sh."LIMIT $z".($C?" OFFSET $C":""):"");}function
limit1($R,$H,$Z,$Sh="\n"){return(preg_match('~^INTO~',$H)?limit($H,$Z,1,0,$Sh):" $H".(is_view(table_status1($R))?$Z:$Sh."WHERE ctid = (SELECT ctid FROM ".table($R).$Z.$Sh."LIMIT 1)"));}function
db_collation($j,$jb){return
get_val("SELECT datcollate FROM pg_database WHERE datname = ".q($j));}function
logged_user(){return
get_val("SELECT user");}function
tables_list(){$H="SELECT table_name, table_type FROM information_schema.tables WHERE table_schema = current_schema()";if(support("materializedview"))$H
.="
UNION ALL
SELECT matviewname, 'MATERIALIZED VIEW'
FROM pg_matviews
WHERE schemaname = current_schema()";$H
.="
ORDER BY 1";return
get_key_vals($H);}function
count_tables($i){$J=array();foreach($i
as$j){if(connection()->select_db($j))$J[$j]=count(tables_list());}return$J;}function
table_status($B=""){static$Gd;if($Gd===null)$Gd=get_val("SELECT 'pg_table_size'::regproc");$J=array();foreach(get_rows("SELECT
	relname AS \"Name\",
	CASE relkind WHEN 'v' THEN 'view' WHEN 'm' THEN 'materialized view' ELSE 'table' END AS \"Engine\"".($Gd?",
	pg_table_size(c.oid) AS \"Data_length\",
	pg_indexes_size(c.oid) AS \"Index_length\"":"").",
	obj_description(c.oid, 'pg_class') AS \"Comment\",
	".(min_version(12)?"''":"CASE WHEN relhasoids THEN 'oid' ELSE '' END")." AS \"Oid\",
	reltuples AS \"Rows\",
	".(min_version(10)?"relispartition::int AS partition,":"")."
	current_schema() AS nspname
FROM pg_class c
WHERE relkind IN ('r', 'm', 'v', 'f', 'p')
AND relnamespace = ".driver()->nsOid."
".($B!=""?"AND relname = ".q($B):"ORDER BY relname"))as$K)$J[$K["Name"]]=$K;return$J;}function
is_view($S){return
in_array($S["Engine"],array("view","materialized view"));}function
fk_support($S){return
true;}function
fields($R){$J=array();$ra=array('timestamp without time zone'=>'timestamp','timestamp with time zone'=>'timestamptz',);foreach(get_rows("SELECT
	a.attname AS field,
	format_type(a.atttypid, a.atttypmod) AS full_type,
	pg_get_expr(d.adbin, d.adrelid) AS default,
	a.attnotnull::int,
	col_description(a.attrelid, a.attnum) AS comment".(min_version(10)?",
	a.attidentity".(min_version(12)?",
	a.attgenerated":""):"")."
FROM pg_attribute a
LEFT JOIN pg_attrdef d ON a.attrelid = d.adrelid AND a.attnum = d.adnum
WHERE a.attrelid = ".driver()->tableOid($R)."
AND NOT a.attisdropped
AND a.attnum > 0
ORDER BY a.attnum")as$K){preg_match('~([^([]+)(\((.*)\))?([a-z ]+)?((\[[0-9]*])*)$~',$K["full_type"],$A);list(,$U,$y,$K["length"],$ka,$va)=$A;$K["length"].=$va;$Ya=$U.$ka;if(isset($ra[$Ya])){$K["type"]=$ra[$Ya];$K["full_type"]=$K["type"].$y.$va;}else{$K["type"]=$U;$K["full_type"]=$K["type"].$y.$ka.$va;}if(in_array($K['attidentity'],array('a','d')))$K['default']='GENERATED '.($K['attidentity']=='d'?'BY DEFAULT':'ALWAYS').' AS IDENTITY';$K["generated"]=($K["attgenerated"]=="s"?"STORED":"");$K["null"]=!$K["attnotnull"];$K["auto_increment"]=$K['attidentity']||preg_match('~^nextval\(~i',$K["default"])||preg_match('~^unique_rowid\(~',$K["default"]);$K["privileges"]=array("insert"=>1,"select"=>1,"update"=>1,"where"=>1,"order"=>1);if(preg_match('~(.+)::[^,)]+(.*)~',$K["default"],$A))$K["default"]=($A[1]=="NULL"?null:idf_unescape($A[1]).$A[2]);$J[$K["field"]]=$K;}return$J;}function
indexes($R,$g=null){$g=connection($g);$J=array();$Bi=driver()->tableOid($R);$e=get_key_vals("SELECT attnum, attname FROM pg_attribute WHERE attrelid = $Bi AND attnum > 0",$g);foreach(get_rows("SELECT relname, indisunique::int, indisprimary::int, indkey, indoption, amname, pg_get_expr(indpred, indrelid, true) AS partial, pg_get_expr(indexprs, indrelid) AS indexpr
FROM pg_index
JOIN pg_class ON indexrelid = oid
JOIN pg_am ON pg_am.oid = pg_class.relam
WHERE indrelid = $Bi
ORDER BY indisprimary DESC, indisunique DESC",$g)as$K){$sh=$K["relname"];$J[$sh]["type"]=($K["indisprimary"]?"PRIMARY":($K["indisunique"]?"UNIQUE":"INDEX"));$J[$sh]["columns"]=array();$J[$sh]["descs"]=array();$J[$sh]["algorithm"]=$K["amname"];$J[$sh]["partial"]=$K["partial"];$fe=preg_split('~(?<=\)), (?=\()~',$K["indexpr"]);foreach(explode(" ",$K["indkey"])as$ge)$J[$sh]["columns"][]=($ge?$e[$ge]:array_shift($fe));foreach(explode(" ",$K["indoption"])as$he)$J[$sh]["descs"][]=(intval($he)&1?'1':null);$J[$sh]["lengths"]=array();}return$J;}function
foreign_keys($R){$J=array();foreach(get_rows("SELECT conname, condeferrable::int AS deferrable, pg_get_constraintdef(oid) AS definition
FROM pg_constraint
WHERE conrelid = ".driver()->tableOid($R)."
AND contype = 'f'::char
ORDER BY conkey, conname")as$K){if(preg_match('~FOREIGN KEY\s*\((.+)\)\s*REFERENCES (.+)\((.+)\)(.*)$~iA',$K['definition'],$A)){$K['source']=array_map('Adminer\idf_unescape',array_map('trim',explode(',',$A[1])));if(preg_match('~^(("([^"]|"")+"|[^"]+)\.)?"?("([^"]|"")+"|[^"]+)$~',$A[2],$Ye)){$K['ns']=idf_unescape($Ye[2]);$K['table']=idf_unescape($Ye[4]);}$K['target']=array_map('Adminer\idf_unescape',array_map('trim',explode(',',$A[3])));$K['on_delete']=(preg_match("~ON DELETE (".driver()->onActions.")~",$A[4],$Ye)?$Ye[1]:'NO ACTION');$K['on_update']=(preg_match("~ON UPDATE (".driver()->onActions.")~",$A[4],$Ye)?$Ye[1]:'NO ACTION');$J[$K['conname']]=$K;}}return$J;}function
view($B){return
array("select"=>trim(get_val("SELECT pg_get_viewdef(".driver()->tableOid($B).")")));}function
collations(){return
array();}function
information_schema($j){return
get_schema()=="information_schema";}function
error(){$J=h(connection()->error);if(preg_match('~^(.*\n)?([^\n]*)\n( *)\^(\n.*)?$~s',$J,$A))$J=$A[1].preg_replace('~((?:[^&]|&[^;]*;){'.strlen($A[3]).'})(.*)~','\1<b>\2</b>',$A[2]).$A[4];return
nl_br($J);}function
create_database($j,$c){return
queries("CREATE DATABASE ".idf_escape($j).($c?" ENCODING ".idf_escape($c):""));}function
drop_databases($i){connection()->close();return
apply_queries("DROP DATABASE",$i,'Adminer\idf_escape');}function
rename_database($B,$c){connection()->close();return
queries("ALTER DATABASE ".idf_escape(DB)." RENAME TO ".idf_escape($B));}function
auto_increment(){return"";}function
alter_table($R,$B,$n,$kd,$ob,$yc,$c,$_a,$E){$b=array();$fh=array();if($R!=""&&$R!=$B)$fh[]="ALTER TABLE ".table($R)." RENAME TO ".table($B);$Th="";foreach($n
as$m){$d=idf_escape($m[0]);$X=$m[1];if(!$X)$b[]="DROP $d";else{$Hj=$X[5];unset($X[5]);if($m[0]==""){if(isset($X[6]))$X[1]=($X[1]==" bigint"?" big":($X[1]==" smallint"?" small":" "))."serial";$b[]=($R!=""?"ADD ":"  ").implode($X);if(isset($X[6]))$b[]=($R!=""?"ADD":" ")." PRIMARY KEY ($X[0])";}else{if($d!=$X[0])$fh[]="ALTER TABLE ".table($B)." RENAME $d TO $X[0]";$b[]="ALTER $d TYPE$X[1]";$Uh=$R."_".idf_unescape($X[0])."_seq";$b[]="ALTER $d ".($X[3]?"SET".preg_replace('~GENERATED ALWAYS(.*) STORED~','EXPRESSION\1',$X[3]):(isset($X[6])?"SET DEFAULT nextval(".q($Uh).")":"DROP DEFAULT"));if(isset($X[6]))$Th="CREATE SEQUENCE IF NOT EXISTS ".idf_escape($Uh)." OWNED BY ".idf_escape($R).".$X[0]";$b[]="ALTER $d ".($X[2]==" NULL"?"DROP NOT":"SET").$X[2];}if($m[0]!=""||$Hj!="")$fh[]="COMMENT ON COLUMN ".table($B).".$X[0] IS ".($Hj!=""?substr($Hj,9):"''");}}$b=array_merge($b,$kd);if($R==""){$P="";if($E){$eb=(connection()->flavor=='cockroach');$P=" PARTITION BY $E[partition_by]($E[partition])";if($E["partition_by"]=='HASH'){$Dg=+$E["partitions"];for($s=0;$s<$Dg;$s++)$fh[]="CREATE TABLE ".idf_escape($B."_$s")." PARTITION OF ".idf_escape($B)." FOR VALUES WITH (MODULUS $Dg, REMAINDER $s)";}else{$Vg="MINVALUE";foreach($E["partition_names"]as$s=>$X){$Y=$E["partition_values"][$s];$_g=" VALUES ".($E["partition_by"]=='LIST'?"IN ($Y)":"FROM ($Vg) TO ($Y)");if($eb)$P
.=($s?",":" (")."\n  PARTITION ".(preg_match('~^DEFAULT$~i',$X)?$X:idf_escape($X))."$_g";else$fh[]="CREATE TABLE ".idf_escape($B."_$X")." PARTITION OF ".idf_escape($B)." FOR$_g";$Vg=$Y;}$P
.=($eb?"\n)":"");}}array_unshift($fh,"CREATE TABLE ".table($B)." (\n".implode(",\n",$b)."\n)$P");}elseif($b)array_unshift($fh,"ALTER TABLE ".table($R)."\n".implode(",\n",$b));if($Th)array_unshift($fh,$Th);if($ob!==null)$fh[]="COMMENT ON TABLE ".table($B)." IS ".q($ob);foreach($fh
as$H){if(!queries($H))return
false;}return
true;}function
alter_indexes($R,$b){$h=array();$jc=array();$fh=array();foreach($b
as$X){if($X[0]!="INDEX")$h[]=($X[2]=="DROP"?"\nDROP CONSTRAINT ".idf_escape($X[1]):"\nADD".($X[1]!=""?" CONSTRAINT ".idf_escape($X[1]):"")." $X[0] ".($X[0]=="PRIMARY"?"KEY ":"")."(".implode(", ",$X[2]).")");elseif($X[2]=="DROP")$jc[]=idf_escape($X[1]);else$fh[]="CREATE INDEX ".idf_escape($X[1]!=""?$X[1]:uniqid($R."_"))." ON ".table($R).($X[3]?" USING $X[3]":"")." (".implode(", ",$X[2]).")".($X[4]?" WHERE $X[4]":"");}if($h)array_unshift($fh,"ALTER TABLE ".table($R).implode(",",$h));if($jc)array_unshift($fh,"DROP INDEX ".implode(", ",$jc));foreach($fh
as$H){if(!queries($H))return
false;}return
true;}function
truncate_tables($T){return
queries("TRUNCATE ".implode(", ",array_map('Adminer\table',$T)));}function
drop_views($Nj){return
drop_tables($Nj);}function
drop_tables($T){foreach($T
as$R){$P=table_status1($R);if(!queries("DROP ".strtoupper($P["Engine"])." ".table($R)))return
false;}return
true;}function
move_tables($T,$Nj,$Ji){foreach(array_merge($T,$Nj)as$R){$P=table_status1($R);if(!queries("ALTER ".strtoupper($P["Engine"])." ".table($R)." SET SCHEMA ".idf_escape($Ji)))return
false;}return
true;}function
trigger($B,$R){if($B=="")return
array("Statement"=>"EXECUTE PROCEDURE ()");$e=array();$Z="WHERE trigger_schema = current_schema() AND event_object_table = ".q($R)." AND trigger_name = ".q($B);foreach(get_rows("SELECT * FROM information_schema.triggered_update_columns $Z")as$K)$e[]=$K["event_object_column"];$J=array();foreach(get_rows('SELECT trigger_name AS "Trigger", action_timing AS "Timing", event_manipulation AS "Event", \'FOR EACH \' || action_orientation AS "Type", action_statement AS "Statement"
FROM information_schema.triggers'."
$Z
ORDER BY event_manipulation DESC")as$K){if($e&&$K["Event"]=="UPDATE")$K["Event"].=" OF";$K["Of"]=implode(", ",$e);if($J)$K["Event"].=" OR $J[Event]";$J=$K;}return$J;}function
triggers($R){$J=array();foreach(get_rows("SELECT * FROM information_schema.triggers WHERE trigger_schema = current_schema() AND event_object_table = ".q($R))as$K){$ij=trigger($K["trigger_name"],$R);$J[$ij["Trigger"]]=array($ij["Timing"],$ij["Event"]);}return$J;}function
trigger_options(){return
array("Timing"=>array("BEFORE","AFTER"),"Event"=>array("INSERT","UPDATE","UPDATE OF","DELETE","INSERT OR UPDATE","INSERT OR UPDATE OF","DELETE OR INSERT","DELETE OR UPDATE","DELETE OR UPDATE OF","DELETE OR INSERT OR UPDATE","DELETE OR INSERT OR UPDATE OF"),"Type"=>array("FOR EACH ROW","FOR EACH STATEMENT"),);}function
routine($B,$U){$L=get_rows('SELECT routine_definition AS definition, LOWER(external_language) AS language, *
FROM information_schema.routines
WHERE routine_schema = current_schema() AND specific_name = '.q($B));$J=idx($L,0,array());$J["returns"]=array("type"=>$J["type_udt_name"]);$J["fields"]=get_rows('SELECT COALESCE(parameter_name, ordinal_position::text) AS field, data_type AS type, character_maximum_length AS length, parameter_mode AS inout
FROM information_schema.parameters
WHERE specific_schema = current_schema() AND specific_name = '.q($B).'
ORDER BY ordinal_position');return$J;}function
routines(){return
get_rows('SELECT specific_name AS "SPECIFIC_NAME", routine_type AS "ROUTINE_TYPE", routine_name AS "ROUTINE_NAME", type_udt_name AS "DTD_IDENTIFIER"
FROM information_schema.routines
WHERE routine_schema = current_schema()
ORDER BY SPECIFIC_NAME');}function
routine_languages(){return
get_vals("SELECT LOWER(lanname) FROM pg_catalog.pg_language");}function
routine_id($B,$K){$J=array();foreach($K["fields"]as$m){$y=$m["length"];$J[]=$m["type"].($y?"($y)":"");}return
idf_escape($B)."(".implode(", ",$J).")";}function
last_id($I){$K=(is_object($I)?$I->fetch_row():array());return($K?$K[0]:0);}function
explain($f,$H){return$f->query("EXPLAIN $H");}function
found_rows($S,$Z){if(preg_match("~ rows=([0-9]+)~",get_val("EXPLAIN SELECT * FROM ".idf_escape($S["Name"]).($Z?" WHERE ".implode(" AND ",$Z):"")),$rh))return$rh[1];}function
types(){return
get_key_vals("SELECT oid, typname
FROM pg_type
WHERE typnamespace = ".driver()->nsOid."
AND typtype IN ('b','d','e')
AND typelem = 0");}function
type_values($t){$Cc=get_vals("SELECT enumlabel FROM pg_enum WHERE enumtypid = $t ORDER BY enumsortorder");return($Cc?"'".implode("', '",array_map('addslashes',$Cc))."'":"");}function
schemas(){return
get_vals("SELECT nspname FROM pg_namespace ORDER BY nspname");}function
get_schema(){return
get_val("SELECT current_schema()");}function
set_schema($Gh,$g=null){if(!$g)$g=connection();$J=$g->query("SET search_path TO ".idf_escape($Gh));driver()->setUserTypes(types());return$J;}function
foreign_keys_sql($R){$J="";$P=table_status1($R);$gd=foreign_keys($R);ksort($gd);foreach($gd
as$fd=>$ed)$J
.="ALTER TABLE ONLY ".idf_escape($P['nspname']).".".idf_escape($P['Name'])." ADD CONSTRAINT ".idf_escape($fd)." $ed[definition] ".($ed['deferrable']?'DEFERRABLE':'NOT DEFERRABLE').";\n";return($J?"$J\n":$J);}function
create_sql($R,$_a,$ti){$xh=array();$Vh=array();$P=table_status1($R);if(is_view($P)){$Mj=view($R);return
rtrim("CREATE VIEW ".idf_escape($R)." AS $Mj[select]",";");}$n=fields($R);if(count($P)<2||empty($n))return
false;$J="CREATE TABLE ".idf_escape($P['nspname']).".".idf_escape($P['Name'])." (\n    ";foreach($n
as$m){$yg=idf_escape($m['field']).' '.$m['full_type'].default_value($m).($m['null']?"":" NOT NULL");$xh[]=$yg;if(preg_match('~nextval\(\'([^\']+)\'\)~',$m['default'],$af)){$Uh=$af[1];$ii=first(get_rows((min_version(10)?"SELECT *, cache_size AS cache_value FROM pg_sequences WHERE schemaname = current_schema() AND sequencename = ".q(idf_unescape($Uh)):"SELECT * FROM $Uh"),null,"-- "));$Vh[]=($ti=="DROP+CREATE"?"DROP SEQUENCE IF EXISTS $Uh;\n":"")."CREATE SEQUENCE $Uh INCREMENT $ii[increment_by] MINVALUE $ii[min_value] MAXVALUE $ii[max_value]".($_a&&$ii['last_value']?" START ".($ii["last_value"]+1):"")." CACHE $ii[cache_value];";}}if(!empty($Vh))$J=implode("\n\n",$Vh)."\n\n$J";$G="";foreach(indexes($R)as$de=>$v){if($v['type']=='PRIMARY'){$G=$de;$xh[]="CONSTRAINT ".idf_escape($de)." PRIMARY KEY (".implode(', ',array_map('Adminer\idf_escape',$v['columns'])).")";}}foreach(driver()->checkConstraints($R)as$vb=>$xb)$xh[]="CONSTRAINT ".idf_escape($vb)." CHECK $xb";$J
.=implode(",\n    ",$xh)."\n)";$_g=driver()->partitionsInfo($P['Name']);if($_g)$J
.="\nPARTITION BY $_g[partition_by]($_g[partition])";$J
.="\nWITH (oids = ".($P['Oid']?'true':'false').");";if($P['Comment'])$J
.="\n\nCOMMENT ON TABLE ".idf_escape($P['nspname']).".".idf_escape($P['Name'])." IS ".q($P['Comment']).";";foreach($n
as$Xc=>$m){if($m['comment'])$J
.="\n\nCOMMENT ON COLUMN ".idf_escape($P['nspname']).".".idf_escape($P['Name']).".".idf_escape($Xc)." IS ".q($m['comment']).";";}foreach(get_rows("SELECT indexdef FROM pg_catalog.pg_indexes WHERE schemaname = current_schema() AND tablename = ".q($R).($G?" AND indexname != ".q($G):""),null,"-- ")as$K)$J
.="\n\n$K[indexdef];";return
rtrim($J,';');}function
truncate_sql($R){return"TRUNCATE ".table($R);}function
trigger_sql($R){$P=table_status1($R);$J="";foreach(triggers($R)as$hj=>$gj){$ij=trigger($hj,$P['Name']);$J
.="\nCREATE TRIGGER ".idf_escape($ij['Trigger'])." $ij[Timing] $ij[Event] ON ".idf_escape($P["nspname"]).".".idf_escape($P['Name'])." $ij[Type] $ij[Statement];;\n";}return$J;}function
use_sql($Ob,$ti=""){$B=idf_escape($Ob);$J="";if(preg_match('~CREATE~',$ti)){if($ti=="DROP+CREATE")$J="DROP DATABASE IF EXISTS $B;\n";$J
.="CREATE DATABASE $B;\n";}return"$J\\connect $B";}function
show_variables(){return
get_rows("SHOW ALL");}function
process_list(){return
get_rows("SELECT * FROM pg_stat_activity ORDER BY ".(min_version(9.2)?"pid":"procpid"));}function
convert_field($m){}function
unconvert_field($m,$J){return$J;}function
support($Vc){return
preg_match('~^(check|columns|comment|database|drop_col|dump|descidx|indexes|kill|partial_indexes|routine|scheme|sequence|sql|table|trigger|type|variables|view'.(min_version(9.3)?'|materializedview':'').(min_version(11)?'|procedure':'').(connection()->flavor=='cockroach'?'':'|processlist').')$~',$Vc);}function
kill_process($X){return
queries("SELECT pg_terminate_backend(".number($X).")");}function
connection_id(){return"SELECT pg_backend_pid()";}function
max_connections(){return
get_val("SHOW max_connections");}}add_driver("oracle","Oracle (beta)");if(isset($_GET["oracle"])){define('Adminer\DRIVER',"oracle");if(extension_loaded("oci8")&&$_GET["ext"]!="pdo"){class
Db
extends
SqlDb{var$extension="oci8";var$_current_db;private$link;function
_error($Dc,$l){if(ini_bool("html_errors"))$l=html_entity_decode(strip_tags($l));$l=preg_replace('~^[^:]*: ~','',$l);$this->error=$l;}function
attach($N,$V,$F){$this->link=@oci_new_connect($V,$F,$N,"AL32UTF8");if($this->link){$this->server_info=oci_server_version($this->link);return'';}$l=oci_error();return$l["message"];}function
quote($Q){return"'".str_replace("'","''",$Q)."'";}function
select_db($Ob){$this->_current_db=$Ob;return
true;}function
query($H,$oj=false){$I=oci_parse($this->link,$H);$this->error="";if(!$I){$l=oci_error($this->link);$this->errno=$l["code"];$this->error=$l["message"];return
false;}set_error_handler(array($this,'_error'));$J=@oci_execute($I);restore_error_handler();if($J){if(oci_num_fields($I))return
new
Result($I);$this->affected_rows=oci_num_rows($I);oci_free_statement($I);}return$J;}function
timeout($vf){return
oci_set_call_timeout($this->link,$vf);}}class
Result{var$num_rows;private$result,$offset=1;function
__construct($I){$this->result=$I;}private
function
convert($K){foreach((array)$K
as$x=>$X){if(is_a($X,'OCILob')||is_a($X,'OCI-Lob'))$K[$x]=$X->load();}return$K;}function
fetch_assoc(){return$this->convert(oci_fetch_assoc($this->result));}function
fetch_row(){return$this->convert(oci_fetch_row($this->result));}function
fetch_field(){$d=$this->offset++;$J=new
\stdClass;$J->name=oci_field_name($this->result,$d);$J->type=oci_field_type($this->result,$d);$J->charsetnr=(preg_match("~raw|blob|bfile~",$J->type)?63:0);return$J;}function
__destruct(){oci_free_statement($this->result);}}}elseif(extension_loaded("pdo_oci")){class
Db
extends
PdoDb{var$extension="PDO_OCI";var$_current_db;function
attach($N,$V,$F){return$this->dsn("oci:dbname=//$N;charset=AL32UTF8",$V,$F);}function
select_db($Ob){$this->_current_db=$Ob;return
true;}}}class
Driver
extends
SqlDriver{static$extensions=array("OCI8","PDO_OCI");static$jush="oracle";var$insertFunctions=array("date"=>"current_date","timestamp"=>"current_timestamp",);var$editFunctions=array("number|float|double"=>"+/-","date|timestamp"=>"+ interval/- interval","char|clob"=>"||",);var$operators=array("=","<",">","<=",">=","!=","LIKE","LIKE %%","IN","IS NULL","NOT LIKE","NOT IN","IS NOT NULL","SQL");var$functions=array("length","lower","round","upper");var$grouping=array("avg","count","count distinct","max","min","sum");function
__construct(Db$f){parent::__construct($f);$this->types=array('数字'=>array("number"=>38,"binary_float"=>12,"binary_double"=>21),'日期时间'=>array("date"=>10,"timestamp"=>29,"interval year"=>12,"interval day"=>28),'字符串'=>array("char"=>2000,"varchar2"=>4000,"nchar"=>2000,"nvarchar2"=>4000,"clob"=>4294967295,"nclob"=>4294967295),'二进制'=>array("raw"=>2000,"long raw"=>2147483648,"blob"=>4294967295,"bfile"=>4294967296),);}function
begin(){return
true;}function
insertUpdate($R,array$L,array$G){foreach($L
as$O){$wj=array();$Z=array();foreach($O
as$x=>$X){$wj[]="$x = $X";if(isset($G[idf_unescape($x)]))$Z[]="$x = $X";}if(!(($Z&&queries("UPDATE ".table($R)." SET ".implode(", ",$wj)." WHERE ".implode(" AND ",$Z))&&$this->conn->affected_rows)||queries("INSERT INTO ".table($R)." (".implode(", ",array_keys($O)).") VALUES (".implode(", ",$O).")")))return
false;}return
true;}function
hasCStyleEscapes(){return
true;}}function
idf_escape($u){return'"'.str_replace('"','""',$u).'"';}function
table($u){return
idf_escape($u);}function
get_databases($id){return
get_vals("SELECT DISTINCT tablespace_name FROM (
SELECT tablespace_name FROM user_tablespaces
UNION SELECT tablespace_name FROM all_tables WHERE tablespace_name IS NOT NULL
)
ORDER BY 1");}function
limit($H,$Z,$z,$C=0,$Sh=" "){return($C?" * FROM (SELECT t.*, rownum AS rnum FROM (SELECT $H$Z) t WHERE rownum <= ".($z+$C).") WHERE rnum > $C":($z?" * FROM (SELECT $H$Z) WHERE rownum <= ".($z+$C):" $H$Z"));}function
limit1($R,$H,$Z,$Sh="\n"){return" $H$Z";}function
db_collation($j,$jb){return
get_val("SELECT value FROM nls_database_parameters WHERE parameter = 'NLS_CHARACTERSET'");}function
logged_user(){return
get_val("SELECT USER FROM DUAL");}function
get_current_db(){$j=connection()->_current_db?:DB;unset(connection()->_current_db);return$j;}function
where_owner($Tg,$sg="owner"){if(!$_GET["ns"])return'';return"$Tg$sg = sys_context('USERENV', 'CURRENT_SCHEMA')";}function
views_table($e){$sg=where_owner('');return"(SELECT $e FROM all_views WHERE ".($sg?:"rownum < 0").")";}function
tables_list(){$Mj=views_table("view_name");$sg=where_owner(" AND ");return
get_key_vals("SELECT table_name, 'table' FROM all_tables WHERE tablespace_name = ".q(DB)."$sg
UNION SELECT view_name, 'view' FROM $Mj
ORDER BY 1");}function
count_tables($i){$J=array();foreach($i
as$j)$J[$j]=get_val("SELECT COUNT(*) FROM all_tables WHERE tablespace_name = ".q($j));return$J;}function
table_status($B=""){$J=array();$Lh=q($B);$j=get_current_db();$Mj=views_table("view_name");$sg=where_owner(" AND ");foreach(get_rows('SELECT table_name "Name", \'table\' "Engine", avg_row_len * num_rows "Data_length", num_rows "Rows" FROM all_tables WHERE tablespace_name = '.q($j).$sg.($B!=""?" AND table_name = $Lh":"")."
UNION SELECT view_name, 'view', 0, 0 FROM $Mj".($B!=""?" WHERE view_name = $Lh":"")."
ORDER BY 1")as$K)$J[$K["Name"]]=$K;return$J;}function
is_view($S){return$S["Engine"]=="view";}function
fk_support($S){return
true;}function
fields($R){$J=array();$sg=where_owner(" AND ");foreach(get_rows("SELECT * FROM all_tab_columns WHERE table_name = ".q($R)."$sg ORDER BY column_id")as$K){$U=$K["DATA_TYPE"];$y="$K[DATA_PRECISION],$K[DATA_SCALE]";if($y==",")$y=$K["CHAR_COL_DECL_LENGTH"];$J[$K["COLUMN_NAME"]]=array("field"=>$K["COLUMN_NAME"],"full_type"=>$U.($y?"($y)":""),"type"=>strtolower($U),"length"=>$y,"default"=>$K["DATA_DEFAULT"],"null"=>($K["NULLABLE"]=="Y"),"privileges"=>array("insert"=>1,"select"=>1,"update"=>1,"where"=>1,"order"=>1),);}return$J;}function
indexes($R,$g=null){$J=array();$sg=where_owner(" AND ","aic.table_owner");foreach(get_rows("SELECT aic.*, ac.constraint_type, atc.data_default
FROM all_ind_columns aic
LEFT JOIN all_constraints ac ON aic.index_name = ac.constraint_name AND aic.table_name = ac.table_name AND aic.index_owner = ac.owner
LEFT JOIN all_tab_cols atc ON aic.column_name = atc.column_name AND aic.table_name = atc.table_name AND aic.index_owner = atc.owner
WHERE aic.table_name = ".q($R)."$sg
ORDER BY ac.constraint_type, aic.column_position",$g)as$K){$de=$K["INDEX_NAME"];$lb=$K["DATA_DEFAULT"];$lb=($lb?trim($lb,'"'):$K["COLUMN_NAME"]);$J[$de]["type"]=($K["CONSTRAINT_TYPE"]=="P"?"PRIMARY":($K["CONSTRAINT_TYPE"]=="U"?"UNIQUE":"INDEX"));$J[$de]["columns"][]=$lb;$J[$de]["lengths"][]=($K["CHAR_LENGTH"]&&$K["CHAR_LENGTH"]!=$K["COLUMN_LENGTH"]?$K["CHAR_LENGTH"]:null);$J[$de]["descs"][]=($K["DESCEND"]&&$K["DESCEND"]=="DESC"?'1':null);}return$J;}function
view($B){$Mj=views_table("view_name, text");$L=get_rows('SELECT text "select" FROM '.$Mj.' WHERE view_name = '.q($B));return
reset($L);}function
collations(){return
array();}function
information_schema($j){return
get_schema()=="INFORMATION_SCHEMA";}function
error(){return
h(connection()->error);}function
explain($f,$H){$f->query("EXPLAIN PLAN FOR $H");return$f->query("SELECT * FROM plan_table");}function
found_rows($S,$Z){}function
auto_increment(){return"";}function
alter_table($R,$B,$n,$kd,$ob,$yc,$c,$_a,$E){$b=$jc=array();$lg=($R?fields($R):array());foreach($n
as$m){$X=$m[1];if($X&&$m[0]!=""&&idf_escape($m[0])!=$X[0])queries("ALTER TABLE ".table($R)." RENAME COLUMN ".idf_escape($m[0])." TO $X[0]");$kg=$lg[$m[0]];if($X&&$kg){$Of=process_field($kg,$kg);if($X[2]==$Of[2])$X[2]="";}if($X)$b[]=($R!=""?($m[0]!=""?"MODIFY (":"ADD ("):"  ").implode($X).($R!=""?")":"");else$jc[]=idf_escape($m[0]);}if($R=="")return
queries("CREATE TABLE ".table($B)." (\n".implode(",\n",$b)."\n)");return(!$b||queries("ALTER TABLE ".table($R)."\n".implode("\n",$b)))&&(!$jc||queries("ALTER TABLE ".table($R)." DROP (".implode(", ",$jc).")"))&&($R==$B||queries("ALTER TABLE ".table($R)." RENAME TO ".table($B)));}function
alter_indexes($R,$b){$jc=array();$fh=array();foreach($b
as$X){if($X[0]!="INDEX"){$X[2]=preg_replace('~ DESC$~','',$X[2]);$h=($X[2]=="DROP"?"\nDROP CONSTRAINT ".idf_escape($X[1]):"\nADD".($X[1]!=""?" CONSTRAINT ".idf_escape($X[1]):"")." $X[0] ".($X[0]=="PRIMARY"?"KEY ":"")."(".implode(", ",$X[2]).")");array_unshift($fh,"ALTER TABLE ".table($R).$h);}elseif($X[2]=="DROP")$jc[]=idf_escape($X[1]);else$fh[]="CREATE INDEX ".idf_escape($X[1]!=""?$X[1]:uniqid($R."_"))." ON ".table($R)." (".implode(", ",$X[2]).")";}if($jc)array_unshift($fh,"DROP INDEX ".implode(", ",$jc));foreach($fh
as$H){if(!queries($H))return
false;}return
true;}function
foreign_keys($R){$J=array();$H="SELECT c_list.CONSTRAINT_NAME as NAME,
c_src.COLUMN_NAME as SRC_COLUMN,
c_dest.OWNER as DEST_DB,
c_dest.TABLE_NAME as DEST_TABLE,
c_dest.COLUMN_NAME as DEST_COLUMN,
c_list.DELETE_RULE as ON_DELETE
FROM ALL_CONSTRAINTS c_list, ALL_CONS_COLUMNS c_src, ALL_CONS_COLUMNS c_dest
WHERE c_list.CONSTRAINT_NAME = c_src.CONSTRAINT_NAME
AND c_list.R_CONSTRAINT_NAME = c_dest.CONSTRAINT_NAME
AND c_list.CONSTRAINT_TYPE = 'R'
AND c_src.TABLE_NAME = ".q($R);foreach(get_rows($H)as$K)$J[$K['NAME']]=array("db"=>$K['DEST_DB'],"table"=>$K['DEST_TABLE'],"source"=>array($K['SRC_COLUMN']),"target"=>array($K['DEST_COLUMN']),"on_delete"=>$K['ON_DELETE'],"on_update"=>null,);return$J;}function
truncate_tables($T){return
apply_queries("TRUNCATE TABLE",$T);}function
drop_views($Nj){return
apply_queries("DROP VIEW",$Nj);}function
drop_tables($T){return
apply_queries("DROP TABLE",$T);}function
last_id($I){return
0;}function
schemas(){$J=get_vals("SELECT DISTINCT owner FROM dba_segments WHERE owner IN (SELECT username FROM dba_users WHERE default_tablespace NOT IN ('SYSTEM','SYSAUX')) ORDER BY 1");return($J?:get_vals("SELECT DISTINCT owner FROM all_tables WHERE tablespace_name = ".q(DB)." ORDER BY 1"));}function
get_schema(){return
get_val("SELECT sys_context('USERENV', 'SESSION_USER') FROM dual");}function
set_schema($Ih,$g=null){if(!$g)$g=connection();return$g->query("ALTER SESSION SET CURRENT_SCHEMA = ".idf_escape($Ih));}function
show_variables(){return
get_rows('SELECT name, display_value FROM v$parameter');}function
show_status(){$J=array();$L=get_rows('SELECT * FROM v$instance');foreach(reset($L)as$x=>$X)$J[]=array($x,$X);return$J;}function
process_list(){return
get_rows('SELECT
	sess.process AS "process",
	sess.username AS "user",
	sess.schemaname AS "schema",
	sess.status AS "status",
	sess.wait_class AS "wait_class",
	sess.seconds_in_wait AS "seconds_in_wait",
	sql.sql_text AS "sql_text",
	sess.machine AS "machine",
	sess.port AS "port"
FROM v$session sess LEFT OUTER JOIN v$sql sql
ON sql.sql_id = sess.sql_id
WHERE sess.type = \'USER\'
ORDER BY PROCESS
');}function
convert_field($m){}function
unconvert_field($m,$J){return$J;}function
support($Vc){return
preg_match('~^(columns|database|drop_col|indexes|descidx|processlist|scheme|sql|status|table|variables|view)$~',$Vc);}}add_driver("mssql","MS SQL");if(isset($_GET["mssql"])){define('Adminer\DRIVER',"mssql");if(extension_loaded("sqlsrv")&&$_GET["ext"]!="pdo"){class
Db
extends
SqlDb{var$extension="sqlsrv";private$link,$result;private
function
get_error(){$this->error="";foreach(sqlsrv_errors()as$l){$this->errno=$l["code"];$this->error
.="$l[message]\n";}$this->error=rtrim($this->error);}function
attach($N,$V,$F){$wb=array("UID"=>$V,"PWD"=>$F,"CharacterSet"=>"UTF-8");$oi=adminer()->connectSsl();if(isset($oi["Encrypt"]))$wb["Encrypt"]=$oi["Encrypt"];if(isset($oi["TrustServerCertificate"]))$wb["TrustServerCertificate"]=$oi["TrustServerCertificate"];$j=adminer()->database();if($j!="")$wb["Database"]=$j;list($Nd,$Ng)=host_port($N);$this->link=@sqlsrv_connect($Nd.($Ng?",$Ng":""),$wb);if($this->link){$ie=sqlsrv_server_info($this->link);$this->server_info=$ie['SQLServerVersion'];}else$this->get_error();return($this->link?'':$this->error);}function
quote($Q){$pj=strlen($Q)!=strlen(utf8_decode($Q));return($pj?"N":"")."'".str_replace("'","''",$Q)."'";}function
select_db($Ob){return$this->query(use_sql($Ob));}function
query($H,$oj=false){$I=sqlsrv_query($this->link,$H);$this->error="";if(!$I){$this->get_error();return
false;}return$this->store_result($I);}function
multi_query($H){$this->result=sqlsrv_query($this->link,$H);$this->error="";if(!$this->result){$this->get_error();return
false;}return
true;}function
store_result($I=null){if(!$I)$I=$this->result;if(!$I)return
false;if(sqlsrv_field_metadata($I))return
new
Result($I);$this->affected_rows=sqlsrv_rows_affected($I);return
true;}function
next_result(){return$this->result?!!sqlsrv_next_result($this->result):false;}}class
Result{var$num_rows;private$result,$offset=0,$fields;function
__construct($I){$this->result=$I;}private
function
convert($K){foreach((array)$K
as$x=>$X){if(is_a($X,'DateTime'))$K[$x]=$X->format("Y-m-d H:i:s");}return$K;}function
fetch_assoc(){return$this->convert(sqlsrv_fetch_array($this->result,SQLSRV_FETCH_ASSOC));}function
fetch_row(){return$this->convert(sqlsrv_fetch_array($this->result,SQLSRV_FETCH_NUMERIC));}function
fetch_field(){if(!$this->fields)$this->fields=sqlsrv_field_metadata($this->result);$m=$this->fields[$this->offset++];$J=new
\stdClass;$J->name=$m["Name"];$J->type=($m["Type"]==1?254:15);$J->charsetnr=0;return$J;}function
seek($C){for($s=0;$s<$C;$s++)sqlsrv_fetch($this->result);}function
__destruct(){sqlsrv_free_stmt($this->result);}}function
last_id($I){return
get_val("SELECT SCOPE_IDENTITY()");}function
explain($f,$H){$f->query("SET SHOWPLAN_ALL ON");$J=$f->query($H);$f->query("SET SHOWPLAN_ALL OFF");return$J;}}else{abstract
class
MssqlDb
extends
PdoDb{function
select_db($Ob){return$this->query(use_sql($Ob));}function
lastInsertId(){return$this->pdo->lastInsertId();}}function
last_id($I){return
connection()->lastInsertId();}function
explain($f,$H){}if(extension_loaded("pdo_sqlsrv")){class
Db
extends
MssqlDb{var$extension="PDO_SQLSRV";function
attach($N,$V,$F){list($Nd,$Ng)=host_port($N);return$this->dsn("sqlsrv:Server=$Nd".($Ng?",$Ng":""),$V,$F);}}}elseif(extension_loaded("pdo_dblib")){class
Db
extends
MssqlDb{var$extension="PDO_DBLIB";function
attach($N,$V,$F){list($Nd,$Ng)=host_port($N);return$this->dsn("dblib:charset=utf8;host=$Nd".($Ng?(is_numeric($Ng)?";port=":";unix_socket=").$Ng:""),$V,$F);}}}}class
Driver
extends
SqlDriver{static$extensions=array("SQLSRV","PDO_SQLSRV","PDO_DBLIB");static$jush="mssql";var$insertFunctions=array("date|time"=>"getdate");var$editFunctions=array("int|decimal|real|float|money|datetime"=>"+/-","char|text"=>"+",);var$operators=array("=","<",">","<=",">=","!=","LIKE","LIKE %%","IN","IS NULL","NOT LIKE","NOT IN","IS NOT NULL");var$functions=array("len","lower","round","upper");var$grouping=array("avg","count","count distinct","max","min","sum");var$generated=array("PERSISTED","VIRTUAL");var$onActions="NO ACTION|CASCADE|SET NULL|SET DEFAULT";static
function
connect($N,$V,$F){if($N=="")$N="localhost:1433";return
parent::connect($N,$V,$F);}function
__construct(Db$f){parent::__construct($f);$this->types=array('数字'=>array("tinyint"=>3,"smallint"=>5,"int"=>10,"bigint"=>20,"bit"=>1,"decimal"=>0,"real"=>12,"float"=>53,"smallmoney"=>10,"money"=>20),'日期时间'=>array("date"=>10,"smalldatetime"=>19,"datetime"=>19,"datetime2"=>19,"time"=>8,"datetimeoffset"=>10),'字符串'=>array("char"=>8000,"varchar"=>8000,"text"=>2147483647,"nchar"=>4000,"nvarchar"=>4000,"ntext"=>1073741823),'二进制'=>array("binary"=>8000,"varbinary"=>8000,"image"=>2147483647),);}function
insertUpdate($R,array$L,array$G){$n=fields($R);$wj=array();$Z=array();$O=reset($L);$e="c".implode(", c",range(1,count($O)));$Pa=0;$oe=array();foreach($O
as$x=>$X){$Pa++;$B=idf_unescape($x);if(!$n[$B]["auto_increment"])$oe[$x]="c$Pa";if(isset($G[$B]))$Z[]="$x = c$Pa";else$wj[]="$x = c$Pa";}$Ij=array();foreach($L
as$O)$Ij[]="(".implode(", ",$O).")";if($Z){$Sd=queries("SET IDENTITY_INSERT ".table($R)." ON");$J=queries("MERGE ".table($R)." USING (VALUES\n\t".implode(",\n\t",$Ij)."\n) AS source ($e) ON ".implode(" AND ",$Z).($wj?"\nWHEN MATCHED THEN UPDATE SET ".implode(", ",$wj):"")."\nWHEN NOT MATCHED THEN INSERT (".implode(", ",array_keys($Sd?$O:$oe)).") VALUES (".($Sd?$e:implode(", ",$oe)).");");if($Sd)queries("SET IDENTITY_INSERT ".table($R)." OFF");}else$J=queries("INSERT INTO ".table($R)." (".implode(", ",array_keys($O)).") VALUES\n".implode(",\n",$Ij));return$J;}function
begin(){return
queries("BEGIN TRANSACTION");}function
tableHelp($B,$ze=false){$Se=array("sys"=>"catalog-views/sys-","INFORMATION_SCHEMA"=>"information-schema-views/",);$_=$Se[get_schema()];if($_)return"relational-databases/system-$_".preg_replace('~_~','-',strtolower($B))."-transact-sql";}}function
idf_escape($u){return"[".str_replace("]","]]",$u)."]";}function
table($u){return($_GET["ns"]!=""?idf_escape($_GET["ns"]).".":"").idf_escape($u);}function
get_databases($id){return
get_vals("SELECT name FROM sys.databases WHERE name NOT IN ('master', 'tempdb', 'model', 'msdb')");}function
limit($H,$Z,$z,$C=0,$Sh=" "){return($z?" TOP (".($z+$C).")":"")." $H$Z";}function
limit1($R,$H,$Z,$Sh="\n"){return
limit($H,$Z,1,0,$Sh);}function
db_collation($j,$jb){return
get_val("SELECT collation_name FROM sys.databases WHERE name = ".q($j));}function
logged_user(){return
get_val("SELECT SUSER_NAME()");}function
tables_list(){return
get_key_vals("SELECT name, type_desc FROM sys.all_objects WHERE schema_id = SCHEMA_ID(".q(get_schema()).") AND type IN ('S', 'U', 'V') ORDER BY name");}function
count_tables($i){$J=array();foreach($i
as$j){connection()->select_db($j);$J[$j]=get_val("SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES");}return$J;}function
table_status($B=""){$J=array();foreach(get_rows("SELECT ao.name AS Name, ao.type_desc AS Engine, (SELECT value FROM fn_listextendedproperty(default, 'SCHEMA', schema_name(schema_id), 'TABLE', ao.name, null, null)) AS Comment
FROM sys.all_objects AS ao
WHERE schema_id = SCHEMA_ID(".q(get_schema()).") AND type IN ('S', 'U', 'V') ".($B!=""?"AND name = ".q($B):"ORDER BY name"))as$K)$J[$K["Name"]]=$K;return$J;}function
is_view($S){return$S["Engine"]=="VIEW";}function
fk_support($S){return
true;}function
fields($R){$rb=get_key_vals("SELECT objname, cast(value as varchar(max)) FROM fn_listextendedproperty('MS_DESCRIPTION', 'schema', ".q(get_schema()).", 'table', ".q($R).", 'column', NULL)");$J=array();$_i=get_val("SELECT object_id FROM sys.all_objects WHERE schema_id = SCHEMA_ID(".q(get_schema()).") AND type IN ('S', 'U', 'V') AND name = ".q($R));foreach(get_rows("SELECT c.max_length, c.precision, c.scale, c.name, c.is_nullable, c.is_identity, c.collation_name, t.name type, d.definition [default], d.name default_constraint, i.is_primary_key
FROM sys.all_columns c
JOIN sys.types t ON c.user_type_id = t.user_type_id
LEFT JOIN sys.default_constraints d ON c.default_object_id = d.object_id
LEFT JOIN sys.index_columns ic ON c.object_id = ic.object_id AND c.column_id = ic.column_id
LEFT JOIN sys.indexes i ON ic.object_id = i.object_id AND ic.index_id = i.index_id
WHERE c.object_id = ".q($_i))as$K){$U=$K["type"];$y=(preg_match("~char|binary~",$U)?intval($K["max_length"])/($U[0]=='n'?2:1):($U=="decimal"?"$K[precision],$K[scale]":""));$J[$K["name"]]=array("field"=>$K["name"],"full_type"=>$U.($y?"($y)":""),"type"=>$U,"length"=>$y,"default"=>(preg_match("~^\('(.*)'\)$~",$K["default"],$A)?str_replace("''","'",$A[1]):$K["default"]),"default_constraint"=>$K["default_constraint"],"null"=>$K["is_nullable"],"auto_increment"=>$K["is_identity"],"collation"=>$K["collation_name"],"privileges"=>array("insert"=>1,"select"=>1,"update"=>1,"where"=>1,"order"=>1),"primary"=>$K["is_primary_key"],"comment"=>$rb[$K["name"]],);}foreach(get_rows("SELECT * FROM sys.computed_columns WHERE object_id = ".q($_i))as$K){$J[$K["name"]]["generated"]=($K["is_persisted"]?"PERSISTED":"VIRTUAL");$J[$K["name"]]["default"]=$K["definition"];}return$J;}function
indexes($R,$g=null){$J=array();foreach(get_rows("SELECT i.name, key_ordinal, is_unique, is_primary_key, c.name AS column_name, is_descending_key
FROM sys.indexes i
INNER JOIN sys.index_columns ic ON i.object_id = ic.object_id AND i.index_id = ic.index_id
INNER JOIN sys.columns c ON ic.object_id = c.object_id AND ic.column_id = c.column_id
WHERE OBJECT_NAME(i.object_id) = ".q($R),$g)as$K){$B=$K["name"];$J[$B]["type"]=($K["is_primary_key"]?"PRIMARY":($K["is_unique"]?"UNIQUE":"INDEX"));$J[$B]["lengths"]=array();$J[$B]["columns"][$K["key_ordinal"]]=$K["column_name"];$J[$B]["descs"][$K["key_ordinal"]]=($K["is_descending_key"]?'1':null);}return$J;}function
view($B){return
array("select"=>preg_replace('~^(?:[^[]|\[[^]]*])*\s+AS\s+~isU','',get_val("SELECT VIEW_DEFINITION FROM INFORMATION_SCHEMA.VIEWS WHERE TABLE_SCHEMA = SCHEMA_NAME() AND TABLE_NAME = ".q($B))));}function
collations(){$J=array();foreach(get_vals("SELECT name FROM fn_helpcollations()")as$c)$J[preg_replace('~_.*~','',$c)][]=$c;return$J;}function
information_schema($j){return
get_schema()=="INFORMATION_SCHEMA";}function
error(){return
nl_br(h(preg_replace('~^(\[[^]]*])+~m','',connection()->error)));}function
create_database($j,$c){return
queries("CREATE DATABASE ".idf_escape($j).(preg_match('~^[a-z0-9_]+$~i',$c)?" COLLATE $c":""));}function
drop_databases($i){return
queries("DROP DATABASE ".implode(", ",array_map('Adminer\idf_escape',$i)));}function
rename_database($B,$c){if(preg_match('~^[a-z0-9_]+$~i',$c))queries("ALTER DATABASE ".idf_escape(DB)." COLLATE $c");queries("ALTER DATABASE ".idf_escape(DB)." MODIFY NAME = ".idf_escape($B));return
true;}function
auto_increment(){return" IDENTITY".($_POST["Auto_increment"]!=""?"(".number($_POST["Auto_increment"]).",1)":"")." PRIMARY KEY";}function
alter_table($R,$B,$n,$kd,$ob,$yc,$c,$_a,$E){$b=array();$rb=array();$lg=fields($R);foreach($n
as$m){$d=idf_escape($m[0]);$X=$m[1];if(!$X)$b["DROP"][]=" COLUMN $d";else{$X[1]=preg_replace("~( COLLATE )'(\\w+)'~",'\1\2',$X[1]);$rb[$m[0]]=$X[5];unset($X[5]);if(preg_match('~ AS ~',$X[3]))unset($X[1],$X[2]);if($m[0]=="")$b["ADD"][]="\n  ".implode("",$X).($R==""?substr($kd[$X[0]],16+strlen($X[0])):"");else{$k=$X[3];unset($X[3]);unset($X[6]);if($d!=$X[0])queries("EXEC sp_rename ".q(table($R).".$d").", ".q(idf_unescape($X[0])).", 'COLUMN'");$b["ALTER COLUMN ".implode("",$X)][]="";$kg=$lg[$m[0]];if(default_value($kg)!=$k){if($kg["default"]!==null)$b["DROP"][]=" ".idf_escape($kg["default_constraint"]);if($k)$b["ADD"][]="\n $k FOR $d";}}}}if($R=="")return
queries("CREATE TABLE ".table($B)." (".implode(",",(array)$b["ADD"])."\n)");if($R!=$B)queries("EXEC sp_rename ".q(table($R)).", ".q($B));if($kd)$b[""]=$kd;foreach($b
as$x=>$X){if(!queries("ALTER TABLE ".table($B)." $x".implode(",",$X)))return
false;}foreach($rb
as$x=>$X){$ob=substr($X,9);queries("EXEC sp_dropextendedproperty @name = N'MS_Description', @level0type = N'Schema', @level0name = ".q(get_schema()).", @level1type = N'Table', @level1name = ".q($B).", @level2type = N'Column', @level2name = ".q($x));queries("EXEC sp_addextendedproperty
@name = N'MS_Description',
@value = $ob,
@level0type = N'Schema',
@level0name = ".q(get_schema()).",
@level1type = N'Table',
@level1name = ".q($B).",
@level2type = N'Column',
@level2name = ".q($x));}return
true;}function
alter_indexes($R,$b){$v=array();$jc=array();foreach($b
as$X){if($X[2]=="DROP"){if($X[0]=="PRIMARY")$jc[]=idf_escape($X[1]);else$v[]=idf_escape($X[1])." ON ".table($R);}elseif(!queries(($X[0]!="PRIMARY"?"CREATE $X[0] ".($X[0]!="INDEX"?"INDEX ":"").idf_escape($X[1]!=""?$X[1]:uniqid($R."_"))." ON ".table($R):"ALTER TABLE ".table($R)." ADD PRIMARY KEY")." (".implode(", ",$X[2]).")"))return
false;}return(!$v||queries("DROP INDEX ".implode(", ",$v)))&&(!$jc||queries("ALTER TABLE ".table($R)." DROP ".implode(", ",$jc)));}function
found_rows($S,$Z){}function
foreign_keys($R){$J=array();$Vf=array("CASCADE","NO ACTION","SET NULL","SET DEFAULT");foreach(get_rows("EXEC sp_fkeys @fktable_name = ".q($R).", @fktable_owner = ".q(get_schema()))as$K){$p=&$J[$K["FK_NAME"]];$p["db"]=$K["PKTABLE_QUALIFIER"];$p["ns"]=$K["PKTABLE_OWNER"];$p["table"]=$K["PKTABLE_NAME"];$p["on_update"]=$Vf[$K["UPDATE_RULE"]];$p["on_delete"]=$Vf[$K["DELETE_RULE"]];$p["source"][]=$K["FKCOLUMN_NAME"];$p["target"][]=$K["PKCOLUMN_NAME"];}return$J;}function
truncate_tables($T){return
apply_queries("TRUNCATE TABLE",$T);}function
drop_views($Nj){return
queries("DROP VIEW ".implode(", ",array_map('Adminer\table',$Nj)));}function
drop_tables($T){return
queries("DROP TABLE ".implode(", ",array_map('Adminer\table',$T)));}function
move_tables($T,$Nj,$Ji){return
apply_queries("ALTER SCHEMA ".idf_escape($Ji)." TRANSFER",array_merge($T,$Nj));}function
trigger($B,$R){if($B=="")return
array();$L=get_rows("SELECT s.name [Trigger],
CASE WHEN OBJECTPROPERTY(s.id, 'ExecIsInsertTrigger') = 1 THEN 'INSERT' WHEN OBJECTPROPERTY(s.id, 'ExecIsUpdateTrigger') = 1 THEN 'UPDATE' WHEN OBJECTPROPERTY(s.id, 'ExecIsDeleteTrigger') = 1 THEN 'DELETE' END [Event],
CASE WHEN OBJECTPROPERTY(s.id, 'ExecIsInsteadOfTrigger') = 1 THEN 'INSTEAD OF' ELSE 'AFTER' END [Timing],
c.text
FROM sysobjects s
JOIN syscomments c ON s.id = c.id
WHERE s.xtype = 'TR' AND s.name = ".q($B));$J=reset($L);if($J)$J["Statement"]=preg_replace('~^.+\s+AS\s+~isU','',$J["text"]);return$J;}function
triggers($R){$J=array();foreach(get_rows("SELECT sys1.name,
CASE WHEN OBJECTPROPERTY(sys1.id, 'ExecIsInsertTrigger') = 1 THEN 'INSERT' WHEN OBJECTPROPERTY(sys1.id, 'ExecIsUpdateTrigger') = 1 THEN 'UPDATE' WHEN OBJECTPROPERTY(sys1.id, 'ExecIsDeleteTrigger') = 1 THEN 'DELETE' END [Event],
CASE WHEN OBJECTPROPERTY(sys1.id, 'ExecIsInsteadOfTrigger') = 1 THEN 'INSTEAD OF' ELSE 'AFTER' END [Timing]
FROM sysobjects sys1
JOIN sysobjects sys2 ON sys1.parent_obj = sys2.id
WHERE sys1.xtype = 'TR' AND sys2.name = ".q($R))as$K)$J[$K["name"]]=array($K["Timing"],$K["Event"]);return$J;}function
trigger_options(){return
array("Timing"=>array("AFTER","INSTEAD OF"),"Event"=>array("INSERT","UPDATE","DELETE"),"Type"=>array("AS"),);}function
schemas(){return
get_vals("SELECT name FROM sys.schemas");}function
get_schema(){if($_GET["ns"]!="")return$_GET["ns"];return
get_val("SELECT SCHEMA_NAME()");}function
set_schema($Gh){$_GET["ns"]=$Gh;return
true;}function
create_sql($R,$_a,$ti){if(is_view(table_status1($R))){$Mj=view($R);return"CREATE VIEW ".table($R)." AS $Mj[select]";}$n=array();$G=false;foreach(fields($R)as$B=>$m){$X=process_field($m,$m);if($X[6])$G=true;$n[]=implode("",$X);}foreach(indexes($R)as$B=>$v){if(!$G||$v["type"]!="PRIMARY"){$e=array();foreach($v["columns"]as$x=>$X)$e[]=idf_escape($X).($v["descs"][$x]?" DESC":"");$B=idf_escape($B);$n[]=($v["type"]=="INDEX"?"INDEX $B":"CONSTRAINT $B ".($v["type"]=="UNIQUE"?"UNIQUE":"PRIMARY KEY"))." (".implode(", ",$e).")";}}foreach(driver()->checkConstraints($R)as$B=>$Wa)$n[]="CONSTRAINT ".idf_escape($B)." CHECK ($Wa)";return"CREATE TABLE ".table($R)." (\n\t".implode(",\n\t",$n)."\n)";}function
foreign_keys_sql($R){$n=array();foreach(foreign_keys($R)as$kd)$n[]=ltrim(format_foreign_key($kd));return($n?"ALTER TABLE ".table($R)." ADD\n\t".implode(",\n\t",$n).";\n\n":"");}function
truncate_sql($R){return"TRUNCATE TABLE ".table($R);}function
use_sql($Ob,$ti=""){return"USE ".idf_escape($Ob);}function
trigger_sql($R){$J="";foreach(triggers($R)as$B=>$ij)$J
.=create_trigger(" ON ".table($R),trigger($B,$R)).";";return$J;}function
convert_field($m){}function
unconvert_field($m,$J){return$J;}function
support($Vc){return
preg_match('~^(check|comment|columns|database|drop_col|dump|indexes|descidx|scheme|sql|table|trigger|view|view_trigger)$~',$Vc);}}class
Adminer{static$instance;var$error='';function
name(){return"<a href='https://www.adminer.org/'".target_blank()." id='h1'><img src='".h(preg_replace("~\\?.*~","",ME)."?file=logo.png&version=5.4.2-dev")."' width='24' height='24' alt='' id='logo'>Adminer</a>";}function
credentials(){return
array(SERVER,$_GET["username"],get_password());}function
connectSsl(){}function
permanentLogin($h=false){return
password_file($h);}function
bruteForceKey(){return$_SERVER["REMOTE_ADDR"];}function
serverName($N){return
h($N);}function
database(){return
DB;}function
databases($id=true){return
get_databases($id);}function
pluginsLinks(){}function
operators(){return
driver()->operators;}function
schemas(){return
schemas();}function
queryTimeout(){return
2;}function
afterConnect(){}function
headers(){}function
csp(array$Hb){return$Hb;}function
head($Lb=null){return
true;}function
bodyClass(){echo" adminer";}function
css(){$J=array();foreach(array("","-dark")as$uf){$o="adminer$uf.css";if(file_exists($o)){$ad=file_get_contents($o);$J["$o?v=".crc32($ad)]=($uf?"dark":(preg_match('~prefers-color-scheme:\s*dark~',$ad)?'':'light'));}}return$J;}function
loginForm(){echo"<table class='layout'>\n",adminer()->loginFormField('driver','<tr><th>'.'系统'.'<td>',html_select("auth[driver]",SqlDriver::$drivers,DRIVER,"loginDriver(this);")),adminer()->loginFormField('server','<tr><th>'.'服务器'.'<td>','<input name="auth[server]" value="'.h(SERVER).'" title="hostname[:port]" placeholder="localhost" autocapitalize="off">'),adminer()->loginFormField('username','<tr><th>'.'用户名'.'<td>','<input name="auth[username]" id="username" autofocus value="'.h($_GET["username"]).'" autocomplete="username" autocapitalize="off">'.script("const authDriver = qs('#username').form['auth[driver]']; authDriver && authDriver.onchange();")),adminer()->loginFormField('password','<tr><th>'.'密码'.'<td>','<input type="password" name="auth[password]" autocomplete="current-password">'),adminer()->loginFormField('db','<tr><th>'.'数据库'.'<td>','<input name="auth[db]" value="'.h($_GET["db"]).'" autocapitalize="off">'),"</table>\n","<p><input type='submit' value='".'登录'."'>\n",checkbox("auth[permanent]",1,$_COOKIE["adminer_permanent"],'保持登录')."\n";}function
loginFormField($B,$Id,$Y){return$Id.$Y."\n";}function
login($Ue,$F){if($F=="")return
sprintf('Adminer默认不支持访问没有密码的数据库，<a href="https://www.adminer.org/en/password/"%s>详情见这里</a>。',target_blank());return
true;}function
tableName(array$zi){return
h($zi["Name"]);}function
fieldName(array$m,$eg=0){$U=$m["full_type"];$ob=$m["comment"];$pb='<span style="white-space:pre-line;">'.h($ob).'</span>';return'<span title="'.h($U.($ob!=""?($U?": ":"").$ob:'')).'">'.h($m["field"]).($ob?'<br />'.$pb:'').'</span>';}function
selectLinks(array$zi,$O=""){$B=$zi["Name"];echo'<p class="links">';$Se=array("select"=>'选择数据');if(support("table")||support("indexes"))$Se["table"]='显示结构';$ze=false;if(support("table")){$ze=is_view($zi);if(!$ze)$Se["create"]='修改表';elseif(support("view"))$Se["view"]='修改视图';}if($O!==null)$Se["edit"]='新建数据';foreach($Se
as$x=>$X)echo" <a href='".h(ME)."$x=".urlencode($B).($x=="edit"?$O:"")."'".bold(isset($_GET[$x])).">$X</a>";echo
doc_link(array(JUSH=>driver()->tableHelp($B,$ze)),"?"),"\n";}function
foreignKeys($R){return
foreign_keys($R);}function
backwardKeys($R,$yi){return
array();}function
backwardKeysPrint(array$Da,array$K){}function
selectQuery($H,$pi,$Tc=false){$J="</p>\n";if(!$Tc&&($Qj=driver()->warnings())){$t="warnings";$J=", <a href='#$t'>".'警告'."</a>".script("qsl('a').onclick = partial(toggle, '$t');","")."$J<div id='$t' class='hidden'>\n$Qj</div>\n";}return"<p><code class='jush-".JUSH."'>".h(str_replace("\n"," ",$H))."</code> <span class='time'>(".format_time($pi).")</span>".(support("sql")?" <a href='".h(ME)."sql=".urlencode($H)."'>".'编辑'."</a>":"").$J;}function
sqlCommandQuery($H){return
shorten_utf8(trim($H),1000);}function
sqlPrintAfter(){}function
rowDescription($R){return"";}function
rowDescriptions(array$L,array$ld){return$L;}function
selectLink($X,array$m){}function
selectVal($X,$_,array$m,$og){$J=($X===null?"<i>NULL</i>":(preg_match("~char|binary|boolean~",$m["type"])&&!preg_match("~var~",$m["type"])?"<code>$X</code>":(preg_match('~json~',$m["type"])?"<code class='jush-js'>$X</code>":$X)));if(is_blob($m)&&!is_utf8($X))$J="<i>".sprintf('%d 字节',strlen($og))."</i>";return($_?"<a href='".h($_)."'".(is_url($_)?target_blank():"").">$J</a>":$J);}function
editVal($X,array$m){return$X;}function
config(){return
array();}function
tableStructurePrint(array$n,$zi=null){echo"<div class='scrollable'>\n","<table class='nowrap odds'>\n","<thead><tr><th>".'列'."<td>".'类型'.(support("comment")?"<td>".'注释':"")."</thead>\n";$si=driver()->structuredTypes();foreach($n
as$m){echo"<tr><th>".h($m["field"]);$U=h($m["full_type"]);$c=h($m["collation"]);echo"<td><span title='$c'>".(in_array($U,(array)$si['用户类型'])?"<a href='".h(ME.'type='.urlencode($U))."'>$U</a>":$U.($c&&isset($zi["Collation"])&&$c!=$zi["Collation"]?" $c":""))."</span>",($m["null"]?" <i>NULL</i>":""),($m["auto_increment"]?" <i>".'自动增量'."</i>":"");$k=h($m["default"]);echo(isset($m["default"])?" <span title='".'默认值'."'>[<b>".($m["generated"]?"<code class='jush-".JUSH."'>$k</code>":$k)."</b>]</span>":""),(support("comment")?"<td>".h($m["comment"]):""),"\n";}echo"</table>\n","</div>\n";}function
tableIndexesPrint(array$w,array$zi){$zg=false;foreach($w
as$B=>$v)$zg|=!!$v["partial"];echo"<table>\n";$Tb=first(driver()->indexAlgorithms($zi));foreach($w
as$B=>$v){ksort($v["columns"]);$Xg=array();foreach($v["columns"]as$x=>$X)$Xg[]="<i>".h($X)."</i>".($v["lengths"][$x]?"(".$v["lengths"][$x].")":"").($v["descs"][$x]?" DESC":"");echo"<tr title='".h($B)."'>","<th>$v[type]".($Tb&&$v['algorithm']!=$Tb?" ($v[algorithm])":""),"<td>".implode(", ",$Xg);if($zg)echo"<td>".($v['partial']?"<code class='jush-".JUSH."'>WHERE ".h($v['partial']):"");echo"\n";}echo"</table>\n";}function
selectColumnsPrint(array$M,array$e){print_fieldset("select",'选择',$M);$s=0;$M[""]=array();foreach($M
as$x=>$X){$X=idx($_GET["columns"],$x,array());$d=select_input(" name='columns[$s][col]'",$e,$X["col"],($x!==""?"selectFieldChange":"selectAddRow"));echo"<div>".(driver()->functions||driver()->grouping?html_select("columns[$s][fun]",array(-1=>"")+array_filter(array('函数'=>driver()->functions,'集合'=>driver()->grouping)),$X["fun"]).on_help("event.target.value && event.target.value.replace(/ |\$/, '(') + ')'",1).script("qsl('select').onchange = function () { helpClose();".($x!==""?"":" qsl('select, input', this.parentNode).onchange();")." };","")."($d)":$d)."</div>\n";$s++;}echo"</div></fieldset>\n";}function
selectSearchPrint(array$Z,array$e,array$w){print_fieldset("search",'搜索',$Z);foreach($w
as$s=>$v){if($v["type"]=="FULLTEXT")echo"<div>(<i>".implode("</i>, <i>",array_map('Adminer\h',$v["columns"]))."</i>) AGAINST"," <input type='search' name='fulltext[$s]' value='".h(idx($_GET["fulltext"],$s))."'>",script("qsl('input').oninput = selectFieldChange;",""),checkbox("boolean[$s]",1,isset($_GET["boolean"][$s]),"BOOL"),"</div>\n";}$Ta="this.parentNode.firstChild.onchange();";foreach(array_merge((array)$_GET["where"],array(array()))as$s=>$X){if(!$X||("$X[col]$X[val]"!=""&&in_array($X["op"],adminer()->operators())))echo"<div>".select_input(" name='where[$s][col]'",$e,$X["col"],($X?"selectFieldChange":"selectAddRow"),"(".'任意位置'.")"),html_select("where[$s][op]",adminer()->operators(),$X["op"],$Ta),"<input type='search' name='where[$s][val]' value='".h($X["val"])."'>",script("mixin(qsl('input'), {oninput: function () { $Ta }, onkeydown: selectSearchKeydown, onsearch: selectSearchSearch});",""),"</div>\n";}echo"</div></fieldset>\n";}function
selectOrderPrint(array$eg,array$e,array$w){print_fieldset("sort",'排序',$eg);$s=0;foreach((array)$_GET["order"]as$x=>$X){if($X!=""){echo"<div>".select_input(" name='order[$s]'",$e,$X,"selectFieldChange"),checkbox("desc[$s]",1,isset($_GET["desc"][$x]),'降序')."</div>\n";$s++;}}echo"<div>".select_input(" name='order[$s]'",$e,"","selectAddRow"),checkbox("desc[$s]",1,false,'降序')."</div>\n","</div></fieldset>\n";}function
selectLimitPrint($z){echo"<fieldset><legend>".'范围'."</legend><div>","<input type='number' name='limit' class='size' value='".intval($z)."'>",script("qsl('input').oninput = selectFieldChange;",""),"</div></fieldset>\n";}function
selectLengthPrint($Pi){if($Pi!==null)echo"<fieldset><legend>".'文本显示限制'."</legend><div>","<input type='number' name='text_length' class='size' value='".h($Pi)."'>","</div></fieldset>\n";}function
selectActionPrint(array$w){echo"<fieldset><legend>".'动作'."</legend><div>","<input type='submit' value='".'选择'."'>"," <span id='noindex' title='".'全表扫描'."'></span>","<script".nonce().">\n","const indexColumns = ";$e=array();foreach($w
as$v){$Kb=reset($v["columns"]);if($v["type"]!="FULLTEXT"&&$Kb)$e[$Kb]=1;}$e[""]=1;foreach($e
as$x=>$X)json_row($x);echo";\n","selectFieldChange.call(qs('#form')['select']);\n","</script>\n","</div></fieldset>\n";}function
selectCommandPrint(){return!information_schema(DB);}function
selectImportPrint(){return!information_schema(DB);}function
selectEmailPrint(array$vc,array$e){}function
selectColumnsProcess(array$e,array$w){$M=array();$xd=array();foreach((array)$_GET["columns"]as$x=>$X){if($X["fun"]=="count"||($X["col"]!=""&&(!$X["fun"]||in_array($X["fun"],driver()->functions)||in_array($X["fun"],driver()->grouping)))){$M[$x]=apply_sql_function($X["fun"],($X["col"]!=""?idf_escape($X["col"]):"*"));if(!in_array($X["fun"],driver()->grouping))$xd[]=$M[$x];}}return
array($M,$xd);}function
selectSearchProcess(array$n,array$w){$J=array();foreach($w
as$s=>$v){if($v["type"]=="FULLTEXT"&&idx($_GET["fulltext"],$s)!="")$J[]="MATCH (".implode(", ",array_map('Adminer\idf_escape',$v["columns"])).") AGAINST (".q($_GET["fulltext"][$s]).(isset($_GET["boolean"][$s])?" IN BOOLEAN MODE":"").")";}foreach((array)$_GET["where"]as$x=>$X){$hb=$X["col"];if("$hb$X[val]"!=""&&in_array($X["op"],adminer()->operators())){$tb=array();foreach(($hb!=""?array($hb=>$n[$hb]):$n)as$B=>$m){$Tg="";$sb=" $X[op]";if(preg_match('~IN$~',$X["op"])){$Xd=process_length($X["val"]);$sb
.=" ".($Xd!=""?$Xd:"(NULL)");}elseif($X["op"]=="SQL")$sb=" $X[val]";elseif(preg_match('~^(I?LIKE) %%$~',$X["op"],$A))$sb=" $A[1] ".adminer()->processInput($m,"%$X[val]%");elseif($X["op"]=="FIND_IN_SET"){$Tg="$X[op](".q($X["val"]).", ";$sb=")";}elseif(!preg_match('~NULL$~',$X["op"]))$sb
.=" ".adminer()->processInput($m,$X["val"]);if($hb!=""||(isset($m["privileges"]["where"])&&(preg_match('~^[-\d.'.(preg_match('~IN$~',$X["op"])?',':'').']+$~',$X["val"])||!preg_match('~'.number_type().'|bit~',$m["type"]))&&(!preg_match("~[\x80-\xFF]~",$X["val"])||preg_match('~char|text|enum|set~',$m["type"]))&&(!preg_match('~date|timestamp~',$m["type"])||preg_match('~^\d+-\d+-\d+~',$X["val"]))))$tb[]=$Tg.driver()->convertSearch(idf_escape($B),$X,$m).$sb;}$J[]=(count($tb)==1?$tb[0]:($tb?"(".implode(" OR ",$tb).")":"1 = 0"));}}return$J;}function
selectOrderProcess(array$n,array$w){$J=array();foreach((array)$_GET["order"]as$x=>$X){if($X!="")$J[]=(preg_match('~^((COUNT\(DISTINCT |[A-Z0-9_]+\()(`(?:[^`]|``)+`|"(?:[^"]|"")+")\)|COUNT\(\*\))$~',$X)?$X:idf_escape($X)).(isset($_GET["desc"][$x])?" DESC":"");}return$J;}function
selectLimitProcess(){return(isset($_GET["limit"])?intval($_GET["limit"]):50);}function
selectLengthProcess(){return(isset($_GET["text_length"])?"$_GET[text_length]":"100");}function
selectEmailProcess(array$Z,array$ld){return
false;}function
selectQueryBuild(array$M,array$Z,array$xd,array$eg,$z,$D){return"";}function
messageQuery($H,$Qi,$Tc=false){restart_session();$Kd=&get_session("queries");if(!idx($Kd,$_GET["db"]))$Kd[$_GET["db"]]=array();if(strlen($H)>1e6)$H=preg_replace('~[\x80-\xFF]+$~','',substr($H,0,1e6))."\n…";$Kd[$_GET["db"]][]=array($H,time(),$Qi);$li="sql-".count($Kd[$_GET["db"]]);$J="<a href='#$li' class='toggle'>".'SQL命令'."</a> <a href='' class='jsonly copy'>🗐</a>\n";if(!$Tc&&($Qj=driver()->warnings())){$t="warnings-".count($Kd[$_GET["db"]]);$J="<a href='#$t' class='toggle'>".'警告'."</a>, $J<div id='$t' class='hidden'>\n$Qj</div>\n";}return" <span class='time'>".@date("H:i:s")."</span>"." $J<div id='$li' class='hidden'><pre><code class='jush-".JUSH."'>".shorten_utf8($H,1e4)."</code></pre>".($Qi?" <span class='time'>($Qi)</span>":'').(support("sql")?'<p><a href="'.h(str_replace("db=".urlencode(DB),"db=".urlencode($_GET["db"]),ME).'sql=&history='.(count($Kd[$_GET["db"]])-1)).'">'.'编辑'.'</a>':'').'</div>';}function
editRowPrint($R,array$n,$K,$wj){}function
editFunctions(array$m){$J=($m["null"]?"NULL/":"");$wj=isset($_GET["select"])||where($_GET);foreach(array(driver()->insertFunctions,driver()->editFunctions)as$x=>$sd){if(!$x||(!isset($_GET["call"])&&$wj)){foreach($sd
as$Hg=>$X){if(!$Hg||preg_match("~$Hg~",$m["type"]))$J
.="/$X";}}if($x&&$sd&&!preg_match('~set|bool~',$m["type"])&&!is_blob($m))$J
.="/SQL";}if($m["auto_increment"]&&!$wj)$J='自动增量';return
explode("/",$J);}function
editInput($R,array$m,$ya,$Y){if($m["type"]=="enum")return(isset($_GET["select"])?"<label><input type='radio'$ya value='orig' checked><i>".'原始'."</i></label> ":"").enum_input("radio",$ya,$m,$Y,"NULL");return"";}function
editHint($R,array$m,$Y){return"";}function
processInput(array$m,$Y,$r=""){if($r=="SQL")return$Y;$B=$m["field"];$J=q($Y);if(preg_match('~^(now|getdate|uuid)$~',$r))$J="$r()";elseif(preg_match('~^current_(date|timestamp)$~',$r))$J=$r;elseif(preg_match('~^([+-]|\|\|)$~',$r))$J=idf_escape($B)." $r $J";elseif(preg_match('~^[+-] interval$~',$r))$J=idf_escape($B)." $r ".(preg_match("~^(\\d+|'[0-9.: -]') [A-Z_]+\$~i",$Y)&&JUSH!="pgsql"?$Y:$J);elseif(preg_match('~^(addtime|subtime|concat)$~',$r))$J="$r(".idf_escape($B).", $J)";elseif(preg_match('~^(md5|sha1|password|encrypt)$~',$r))$J="$r($J)";return
unconvert_field($m,$J);}function
dumpOutput(){$J=array('text'=>'打开','file'=>'保存');if(function_exists('gzencode'))$J['gz']='gzip';return$J;}function
dumpFormat(){return(support("dump")?array('sql'=>'SQL'):array())+array('csv'=>'CSV,','csv;'=>'CSV;','tsv'=>'TSV');}function
dumpDatabase($j){}function
dumpTable($R,$ti,$ze=0){if($_POST["format"]!="sql"){echo"\xef\xbb\xbf";if($ti)dump_csv(array_keys(fields($R)));}else{if($ze==2){$n=array();foreach(fields($R)as$B=>$m)$n[]=idf_escape($B)." $m[full_type]";$h="CREATE TABLE ".table($R)." (".implode(", ",$n).")";}else$h=create_sql($R,$_POST["auto_increment"],$ti);set_utf8mb4($h);if($ti&&$h){if($ti=="DROP+CREATE"||$ze==1)echo"DROP ".($ze==2?"VIEW":"TABLE")." IF EXISTS ".table($R).";\n";if($ze==1)$h=remove_definer($h);echo"$h;\n\n";}}}function
dumpData($R,$ti,$H){if($ti){$ef=(JUSH=="sqlite"?0:1048576);$n=array();$Td=false;if($_POST["format"]=="sql"){if($ti=="TRUNCATE+INSERT")echo
truncate_sql($R).";\n";$n=fields($R);if(JUSH=="mssql"){foreach($n
as$m){if($m["auto_increment"]){echo"SET IDENTITY_INSERT ".table($R)." ON;\n";$Td=true;break;}}}}$I=connection()->query($H,1);if($I){$oe="";$Na="";$De=array();$td=array();$vi="";$Wc=($R!=''?'fetch_assoc':'fetch_row');$Db=0;while($K=$I->$Wc()){if(!$De){$Ij=array();foreach($K
as$X){$m=$I->fetch_field();if(idx($n[$m->name],'generated')){$td[$m->name]=true;continue;}$De[]=$m->name;$x=idf_escape($m->name);$Ij[]="$x = VALUES($x)";}$vi=($ti=="INSERT+UPDATE"?"\nON DUPLICATE KEY UPDATE ".implode(", ",$Ij):"").";\n";}if($_POST["format"]!="sql"){if($ti=="table"){dump_csv($De);$ti="INSERT";}dump_csv($K);}else{if(!$oe)$oe="INSERT INTO ".table($R)." (".implode(", ",array_map('Adminer\idf_escape',$De)).") VALUES";foreach($K
as$x=>$X){if($td[$x]){unset($K[$x]);continue;}$m=$n[$x];$K[$x]=($X!==null?unconvert_field($m,preg_match(number_type(),$m["type"])&&!preg_match('~\[~',$m["full_type"])&&is_numeric($X)?$X:q(($X===false?0:$X))):"NULL");}$Eh=($ef?"\n":" ")."(".implode(",\t",$K).")";if(!$Na)$Na=$oe.$Eh;elseif(JUSH=='mssql'?$Db%1000!=0:strlen($Na)+4+strlen($Eh)+strlen($vi)<$ef)$Na
.=",$Eh";else{echo$Na.$vi;$Na=$oe.$Eh;}}$Db++;}if($Na)echo$Na.$vi;}elseif($_POST["format"]=="sql")echo"-- ".str_replace("\n"," ",connection()->error)."\n";if($Td)echo"SET IDENTITY_INSERT ".table($R)." OFF;\n";}}function
dumpFilename($Rd){return
friendly_url($Rd!=""?$Rd:(SERVER?:"localhost"));}function
dumpHeaders($Rd,$xf=false){$rg=$_POST["output"];$Oc=(preg_match('~sql~',$_POST["format"])?"sql":($xf?"tar":"csv"));header("Content-Type: ".($rg=="gz"?"application/x-gzip":($Oc=="tar"?"application/x-tar":($Oc=="sql"||$rg!="file"?"text/plain":"text/csv")."; charset=utf-8")));if($rg=="gz"){ob_start(function($Q){return
gzencode($Q);},1e6);}return$Oc;}function
dumpFooter(){if($_POST["format"]=="sql")echo"-- ".gmdate("Y-m-d H:i:s e")."\n";}function
importServerPath(){return"adminer.sql";}function
homepage(){echo'<p class="links">'.($_GET["ns"]==""&&support("database")?'<a href="'.h(ME).'database=">'.'修改数据库'."</a>\n":""),(support("scheme")?"<a href='".h(ME)."scheme='>".($_GET["ns"]!=""?'修改模式':'创建模式')."</a>\n":""),($_GET["ns"]!==""?'<a href="'.h(ME).'schema=">'.'数据库概要'."</a>\n":""),(support("privileges")?"<a href='".h(ME)."privileges='>".'权限'."</a>\n":"");if($_GET["ns"]!=="")echo(support("routine")?"<a href='#routines'>".'子程序'."</a>\n":""),(support("sequence")?"<a href='#sequences'>".'序列'."</a>\n":""),(support("type")?"<a href='#user-types'>".'用户类型'."</a>\n":""),(support("event")?"<a href='#events'>".'事件'."</a>\n":"");return
true;}function
navigation($tf){echo"<h1>".adminer()->name()." <span class='version'>".VERSION;$Ef=$_COOKIE["adminer_version"];echo" <a href='https://www.adminer.org/#download'".target_blank()." id='version'>".(version_compare(VERSION,$Ef)<0?h($Ef):"")."</a>","</span></h1>\n";if($tf=="auth"){$rg="";foreach((array)$_SESSION["pwds"]as$Kj=>$Xh){foreach($Xh
as$N=>$Fj){$B=h(get_setting("vendor-$Kj-$N")?:get_driver($Kj));foreach($Fj
as$V=>$F){if($F!==null){$Rb=$_SESSION["db"][$Kj][$N][$V];foreach(($Rb?array_keys($Rb):array(""))as$j)$rg
.="<li><a href='".h(auth_url($Kj,$N,$V,$j))."'>($B) ".h("$V@".($N!=""?adminer()->serverName($N):"").($j!=""?" - $j":""))."</a>\n";}}}}if($rg)echo"<ul id='logins'>\n$rg</ul>\n".script("mixin(qs('#logins'), {onmouseover: menuOver, onmouseout: menuOut});");}else{$T=array();if($_GET["ns"]!==""&&!$tf&&DB!=""){connection()->select_db(DB);$T=table_status('',true);}adminer()->syntaxHighlighting($T);adminer()->databasesPrint($tf);$ia=array();if(DB==""||!$tf){if(support("sql")){$ia[]="<a href='".h(ME)."sql='".bold(isset($_GET["sql"])&&!isset($_GET["import"])).">".'SQL命令'."</a>";$ia[]="<a href='".h(ME)."import='".bold(isset($_GET["import"])).">".'导入'."</a>";}$ia[]="<a href='".h(ME)."dump=".urlencode(isset($_GET["table"])?$_GET["table"]:$_GET["select"])."' id='dump'".bold(isset($_GET["dump"])).">".'导出'."</a>";}$Yd=$_GET["ns"]!==""&&!$tf&&DB!="";if($Yd)$ia[]='<a href="'.h(ME).'create="'.bold($_GET["create"]==="").">".'创建表'."</a>";echo($ia?"<p class='links'>\n".implode("\n",$ia)."\n":"");if($Yd){if($T)adminer()->tablesPrint($T);else
echo"<p class='message'>".'没有表。'."</p>\n";}}}function
syntaxHighlighting(array$T){echo
script_src(preg_replace("~\\?.*~","",ME)."?file=jush.js&version=5.4.2-dev",true);if(support("sql")){echo"<script".nonce().">\n";if($T){$Se=array();foreach($T
as$R=>$U)$Se[]=preg_quote($R,'/');echo"var jushLinks = { ".JUSH.":";json_row(js_escape(ME).(support("table")?"table":"select").'=$&','/\b('.implode('|',$Se).')\b/g',false);if(support('routine')){foreach(routines()as$K)json_row(js_escape(ME).'function='.urlencode($K["SPECIFIC_NAME"]).'&name=$&','/\b'.preg_quote($K["ROUTINE_NAME"],'/').'(?=["`]?\()/g',false);}json_row('');echo"};\n";foreach(array("bac","bra","sqlite_quo","mssql_bra")as$X)echo"jushLinks.$X = jushLinks.".JUSH.";\n";if(isset($_GET["sql"])||isset($_GET["trigger"])||isset($_GET["check"])){$Fi=array_fill_keys(array_keys($T),array());foreach(driver()->allFields()as$R=>$n){foreach($n
as$m)$Fi[$R][]=$m["field"];}echo"addEventListener('DOMContentLoaded', () => { autocompleter = jush.autocompleteSql('".idf_escape("")."', ".json_encode($Fi)."); });\n";}}echo"</script>\n";}echo
script("syntaxHighlighting('".preg_replace('~^(\d\.?\d).*~s','\1',connection()->server_info)."', '".connection()->flavor."');");}function
databasesPrint($tf){$i=adminer()->databases();if(DB&&$i&&!in_array(DB,$i))array_unshift($i,DB);echo"<form action=''>\n<p id='dbs'>\n";hidden_fields_get();$Pb=script("mixin(qsl('select'), {onmousedown: dbMouseDown, onchange: dbChange});");echo"<label title='".'数据库'."'>".'数据库'.": ".($i?html_select("db",array(""=>"")+$i,DB).$Pb:"<input name='db' value='".h(DB)."' autocapitalize='off' size='19'>\n")."</label>","<input type='submit' value='".'使用'."'".($i?" class='hidden'":"").">\n";if(support("scheme")){if($tf!="db"&&DB!=""&&connection()->select_db(DB)){echo"<br><label>".'模式'.": ".html_select("ns",array(""=>"")+adminer()->schemas(),$_GET["ns"])."$Pb</label>";if($_GET["ns"]!="")set_schema($_GET["ns"]);}}foreach(array("import","sql","schema","dump","privileges")as$X){if(isset($_GET[$X])){echo
input_hidden($X);break;}}echo"</p></form>\n";}function
tablesPrint(array$T){echo"<ul id='tables'>".script("mixin(qs('#tables'), {onmouseover: menuOver, onmouseout: menuOut});");foreach($T
as$R=>$P){$R="$R";$B=adminer()->tableName($P);if($B!=""&&!$P["partition"])echo'<li><a href="'.h(ME).'select='.urlencode($R).'"'.bold($_GET["select"]==$R||$_GET["edit"]==$R,"select")." title='".'选择数据'."'>".'选择'."</a> ",(support("table")||support("indexes")?'<a href="'.h(ME).'table='.urlencode($R).'"'.bold(in_array($R,array($_GET["table"],$_GET["create"],$_GET["indexes"],$_GET["foreign"],$_GET["trigger"],$_GET["check"],$_GET["view"])),(is_view($P)?"view":"structure"))." title='".'显示结构'."'>$B</a>":"<span>$B</span>")."\n";}echo"</ul>\n";}function
showVariables(){return
show_variables();}function
showStatus(){return
show_status();}function
processList(){return
process_list();}function
killProcess($t){return
kill_process($t);}}class
Plugins{private
static$append=array('dumpFormat'=>true,'dumpOutput'=>true,'editRowPrint'=>true,'editFunctions'=>true,'config'=>true);var$plugins;var$error='';private$hooks=array();function
__construct($Mg){if($Mg===null){$Mg=array();$Ha="adminer-plugins";if(is_dir($Ha)){foreach(glob("$Ha/*.php")as$o)$Zd=include_once"./$o";}$Jd=" href='https://www.adminer.org/plugins/#use'".target_blank();if(file_exists("$Ha.php")){$Zd=include_once"./$Ha.php";if(is_array($Zd)){foreach($Zd
as$Lg)$Mg[get_class($Lg)]=$Lg;}else$this->error
.=sprintf('%s must <a%s>return an array</a>.',"<b>$Ha.php</b>",$Jd)."<br>";}foreach(get_declared_classes()as$db){if(!$Mg[$db]&&preg_match('~^Adminer\w~i',$db)){$ph=new
\ReflectionClass($db);$yb=$ph->getConstructor();if($yb&&$yb->getNumberOfRequiredParameters())$this->error
.=sprintf('<a%s>Configure</a> %s in %s.',$Jd,"<b>$db</b>","<b>$Ha.php</b>")."<br>";else$Mg[$db]=new$db;}}}$this->plugins=$Mg;$la=new
Adminer;$Mg[]=$la;$ph=new
\ReflectionObject($la);foreach($ph->getMethods()as$rf){foreach($Mg
as$Lg){$B=$rf->getName();if(method_exists($Lg,$B))$this->hooks[$B][]=$Lg;}}}function
__call($B,array$wg){$ua=array();foreach($wg
as$x=>$X)$ua[]=&$wg[$x];$J=null;foreach($this->hooks[$B]as$Lg){$Y=call_user_func_array(array($Lg,$B),$ua);if($Y!==null){if(!self::$append[$B])return$Y;$J=$Y+(array)$J;}}return$J;}}abstract
class
Plugin{protected$translations=array();function
description(){return$this->lang('');}function
screenshot(){return"";}protected
function
lang($u,$Kf=null){$ua=func_get_args();$ua[0]=idx($this->translations[LANG],$u)?:$u;return
call_user_func_array('Adminer\lang_format',$ua);}}Adminer::$instance=(function_exists('adminer_object')?adminer_object():(is_dir("adminer-plugins")||file_exists("adminer-plugins.php")?new
Plugins(null):new
Adminer));SqlDriver::$drivers=array("server"=>"MySQL / MariaDB")+SqlDriver::$drivers;if(!defined('Adminer\DRIVER')){define('Adminer\DRIVER',"server");if(extension_loaded("mysqli")&&$_GET["ext"]!="pdo"){class
Db
extends
\MySQLi{static$instance;var$extension="MySQLi",$flavor='';function
__construct(){parent::init();}function
attach($N,$V,$F){mysqli_report(MYSQLI_REPORT_OFF);list($Nd,$Ng)=host_port($N);$oi=adminer()->connectSsl();if($oi)$this->ssl_set($oi['key'],$oi['cert'],$oi['ca'],'','');$J=@$this->real_connect(($N!=""?$Nd:ini_get("mysqli.default_host")),($N.$V!=""?$V:ini_get("mysqli.default_user")),($N.$V.$F!=""?$F:ini_get("mysqli.default_pw")),null,(is_numeric($Ng)?intval($Ng):ini_get("mysqli.default_port")),(is_numeric($Ng)?null:$Ng),($oi?($oi['verify']!==false?2048:64):0));$this->options(MYSQLI_OPT_LOCAL_INFILE,0);return($J?'':$this->error);}function
set_charset($Va){if(parent::set_charset($Va))return
true;parent::set_charset('utf8');return$this->query("SET NAMES $Va");}function
next_result(){return
self::more_results()&&parent::next_result();}function
quote($Q){return"'".$this->escape_string($Q)."'";}}}elseif(extension_loaded("mysql")&&!((ini_bool("sql.safe_mode")||ini_bool("mysql.allow_local_infile"))&&extension_loaded("pdo_mysql"))){class
Db
extends
SqlDb{private$link;function
attach($N,$V,$F){if(ini_bool("mysql.allow_local_infile"))return
sprintf('禁用 %s 或启用 %s 或 %s 扩展。',"'mysql.allow_local_infile'","MySQLi","PDO_MySQL");$this->link=@mysql_connect(($N!=""?$N:ini_get("mysql.default_host")),($N.$V!=""?$V:ini_get("mysql.default_user")),($N.$V.$F!=""?$F:ini_get("mysql.default_password")),true,131072);if(!$this->link)return
mysql_error();$this->server_info=mysql_get_server_info($this->link);return'';}function
set_charset($Va){if(function_exists('mysql_set_charset')){if(mysql_set_charset($Va,$this->link))return
true;mysql_set_charset('utf8',$this->link);}return$this->query("SET NAMES $Va");}function
quote($Q){return"'".mysql_real_escape_string($Q,$this->link)."'";}function
select_db($Ob){return
mysql_select_db($Ob,$this->link);}function
query($H,$oj=false){$I=@($oj?mysql_unbuffered_query($H,$this->link):mysql_query($H,$this->link));$this->error="";if(!$I){$this->errno=mysql_errno($this->link);$this->error=mysql_error($this->link);return
false;}if($I===true){$this->affected_rows=mysql_affected_rows($this->link);$this->info=mysql_info($this->link);return
true;}return
new
Result($I);}}class
Result{var$num_rows;private$result;private$offset=0;function
__construct($I){$this->result=$I;$this->num_rows=mysql_num_rows($I);}function
fetch_assoc(){return
mysql_fetch_assoc($this->result);}function
fetch_row(){return
mysql_fetch_row($this->result);}function
fetch_field(){$J=mysql_fetch_field($this->result,$this->offset++);$J->orgtable=$J->table;$J->charsetnr=($J->blob?63:0);return$J;}function
__destruct(){mysql_free_result($this->result);}}}elseif(extension_loaded("pdo_mysql")){class
Db
extends
PdoDb{var$extension="PDO_MySQL";function
attach($N,$V,$F){$cg=array(\PDO::MYSQL_ATTR_LOCAL_INFILE=>false);$oi=adminer()->connectSsl();if($oi){if($oi['key'])$cg[\PDO::MYSQL_ATTR_SSL_KEY]=$oi['key'];if($oi['cert'])$cg[\PDO::MYSQL_ATTR_SSL_CERT]=$oi['cert'];if($oi['ca'])$cg[\PDO::MYSQL_ATTR_SSL_CA]=$oi['ca'];if(isset($oi['verify']))$cg[\PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT]=$oi['verify'];}list($Nd,$Ng)=host_port($N);return$this->dsn("mysql:charset=utf8;host=$Nd".($Ng?(is_numeric($Ng)?";port=":";unix_socket=").$Ng:""),$V,$F,$cg);}function
set_charset($Va){return$this->query("SET NAMES $Va");}function
select_db($Ob){return$this->query("USE ".idf_escape($Ob));}function
query($H,$oj=false){$this->pdo->setAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY,!$oj);return
parent::query($H,$oj);}}}class
Driver
extends
SqlDriver{static$extensions=array("MySQLi","MySQL","PDO_MySQL");static$jush="sql";var$unsigned=array("unsigned","zerofill","unsigned zerofill");var$operators=array("=","<",">","<=",">=","!=","LIKE","LIKE %%","REGEXP","IN","FIND_IN_SET","IS NULL","NOT LIKE","NOT REGEXP","NOT IN","IS NOT NULL","SQL");var$functions=array("char_length","date","from_unixtime","lower","round","floor","ceil","sec_to_time","time_to_sec","upper");var$grouping=array("avg","count","count distinct","group_concat","max","min","sum");static
function
connect($N,$V,$F){$f=parent::connect($N,$V,$F);if(is_string($f)){if(function_exists('iconv')&&!is_utf8($f)&&strlen($Eh=iconv("windows-1250","utf-8",$f))>strlen($f))$f=$Eh;return$f;}$f->set_charset(charset($f));$f->query("SET sql_quote_show_create = 1, autocommit = 1");$f->flavor=(preg_match('~MariaDB~',$f->server_info)?'maria':'mysql');add_driver(DRIVER,($f->flavor=='maria'?"MariaDB":"MySQL"));return$f;}function
__construct(Db$f){parent::__construct($f);$this->types=array('数字'=>array("tinyint"=>3,"smallint"=>5,"mediumint"=>8,"int"=>10,"bigint"=>20,"decimal"=>66,"float"=>12,"double"=>21),'日期时间'=>array("date"=>10,"datetime"=>19,"timestamp"=>19,"time"=>10,"year"=>4),'字符串'=>array("char"=>255,"varchar"=>65535,"tinytext"=>255,"text"=>65535,"mediumtext"=>16777215,"longtext"=>4294967295),'列表'=>array("enum"=>65535,"set"=>64),'二进制'=>array("bit"=>20,"binary"=>255,"varbinary"=>65535,"tinyblob"=>255,"blob"=>65535,"mediumblob"=>16777215,"longblob"=>4294967295),'几何图形'=>array("geometry"=>0,"point"=>0,"linestring"=>0,"polygon"=>0,"multipoint"=>0,"multilinestring"=>0,"multipolygon"=>0,"geometrycollection"=>0),);$this->insertFunctions=array("char"=>"md5/sha1/password/encrypt/uuid","binary"=>"md5/sha1","date|time"=>"now",);$this->editFunctions=array(number_type()=>"+/-","date"=>"+ interval/- interval","time"=>"addtime/subtime","char|text"=>"concat",);if(min_version('5.7.8',10.2,$f))$this->types['字符串']["json"]=4294967295;if(min_version('',10.7,$f)){$this->types['字符串']["uuid"]=128;$this->insertFunctions['uuid']='uuid';}if(min_version(9,'',$f)){$this->types['数字']["vector"]=16383;$this->insertFunctions['vector']='string_to_vector';}if(min_version(5.1,'',$f))$this->partitionBy=array("HASH","LINEAR HASH","KEY","LINEAR KEY","RANGE","LIST");if(min_version(5.7,10.2,$f))$this->generated=array("STORED","VIRTUAL");}function
unconvertFunction(array$m){return(preg_match("~binary~",$m["type"])?"<code class='jush-sql'>UNHEX</code>":($m["type"]=="bit"?doc_link(array('sql'=>'bit-value-literals.html'),"<code>b''</code>"):(preg_match("~geometry|point|linestring|polygon~",$m["type"])?"<code class='jush-sql'>GeomFromText</code>":"")));}function
insert($R,array$O){return($O?parent::insert($R,$O):queries("INSERT INTO ".table($R)." ()\nVALUES ()"));}function
insertUpdate($R,array$L,array$G){$e=array_keys(reset($L));$Tg="INSERT INTO ".table($R)." (".implode(", ",$e).") VALUES\n";$Ij=array();foreach($e
as$x)$Ij[$x]="$x = VALUES($x)";$vi="\nON DUPLICATE KEY UPDATE ".implode(", ",$Ij);$Ij=array();$y=0;foreach($L
as$O){$Y="(".implode(", ",$O).")";if($Ij&&(strlen($Tg)+$y+strlen($Y)+strlen($vi)>1e6)){if(!queries($Tg.implode(",\n",$Ij).$vi))return
false;$Ij=array();$y=0;}$Ij[]=$Y;$y+=strlen($Y)+2;}return
queries($Tg.implode(",\n",$Ij).$vi);}function
slowQuery($H,$Ri){if(min_version('5.7.8','10.1.2')){if($this->conn->flavor=='maria')return"SET STATEMENT max_statement_time=$Ri FOR $H";elseif(preg_match('~^(SELECT\b)(.+)~is',$H,$A))return"$A[1] /*+ MAX_EXECUTION_TIME(".($Ri*1000).") */ $A[2]";}}function
convertSearch($u,array$X,array$m){return(preg_match('~char|text|enum|set~',$m["type"])&&!preg_match("~^utf8~",$m["collation"])&&preg_match('~[\x80-\xFF]~',$X['val'])?"CONVERT($u USING ".charset($this->conn).")":$u);}function
warnings(){$I=$this->conn->query("SHOW WARNINGS");if($I&&$I->num_rows){ob_start();print_select_result($I);return
ob_get_clean();}}function
tableHelp($B,$ze=false){$We=($this->conn->flavor=='maria');if(information_schema(DB))return
strtolower("information-schema-".($We?"$B-table/":str_replace("_","-",$B)."-table.html"));if(DB=="mysql")return($We?"mysql$B-table/":"system-schema.html");}function
partitionsInfo($R){$qd="FROM information_schema.PARTITIONS WHERE TABLE_SCHEMA = ".q(DB)." AND TABLE_NAME = ".q($R);$I=$this->conn->query("SELECT PARTITION_METHOD, PARTITION_EXPRESSION, PARTITION_ORDINAL_POSITION $qd ORDER BY PARTITION_ORDINAL_POSITION DESC LIMIT 1");$J=array();list($J["partition_by"],$J["partition"],$J["partitions"])=$I->fetch_row();$Dg=get_key_vals("SELECT PARTITION_NAME, PARTITION_DESCRIPTION $qd AND PARTITION_NAME != '' ORDER BY PARTITION_ORDINAL_POSITION");$J["partition_names"]=array_keys($Dg);$J["partition_values"]=array_values($Dg);return$J;}function
hasCStyleEscapes(){static$Qa;if($Qa===null){$mi=get_val("SHOW VARIABLES LIKE 'sql_mode'",1,$this->conn);$Qa=(strpos($mi,'NO_BACKSLASH_ESCAPES')===false);}return$Qa;}function
engines(){$J=array();foreach(get_rows("SHOW ENGINES")as$K){if(preg_match("~YES|DEFAULT~",$K["Support"]))$J[]=$K["Engine"];}return$J;}function
indexAlgorithms(array$zi){return(preg_match('~^(MEMORY|NDB)$~',$zi["Engine"])?array("HASH","BTREE"):array());}}function
idf_escape($u){return"`".str_replace("`","``",$u)."`";}function
table($u){return
idf_escape($u);}function
get_databases($id){$J=get_session("dbs");if($J===null){$H="SELECT SCHEMA_NAME FROM information_schema.SCHEMATA ORDER BY SCHEMA_NAME";$J=($id?slow_query($H):get_vals($H));restart_session();set_session("dbs",$J);stop_session();}return$J;}function
limit($H,$Z,$z,$C=0,$Sh=" "){return" $H$Z".($z?$Sh."LIMIT $z".($C?" OFFSET $C":""):"");}function
limit1($R,$H,$Z,$Sh="\n"){return
limit($H,$Z,1,0,$Sh);}function
db_collation($j,array$jb){$J=null;$h=get_val("SHOW CREATE DATABASE ".idf_escape($j),1);if(preg_match('~ COLLATE ([^ ]+)~',$h,$A))$J=$A[1];elseif(preg_match('~ CHARACTER SET ([^ ]+)~',$h,$A))$J=$jb[$A[1]][-1];return$J;}function
logged_user(){return
get_val("SELECT USER()");}function
tables_list(){return
get_key_vals("SELECT TABLE_NAME, TABLE_TYPE FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() ORDER BY TABLE_NAME");}function
count_tables(array$i){$J=array();foreach($i
as$j)$J[$j]=count(get_vals("SHOW TABLES IN ".idf_escape($j)));return$J;}function
table_status($B="",$Uc=false){$J=array();foreach(get_rows($Uc?"SELECT TABLE_NAME AS Name, ENGINE AS Engine, TABLE_COMMENT AS Comment FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() ".($B!=""?"AND TABLE_NAME = ".q($B):"ORDER BY Name"):"SHOW TABLE STATUS".($B!=""?" LIKE ".q(addcslashes($B,"%_\\")):""))as$K){if($K["Engine"]=="InnoDB")$K["Comment"]=preg_replace('~(?:(.+); )?InnoDB free: .*~','\1',$K["Comment"]);if(!isset($K["Engine"]))$K["Comment"]="";if($B!="")$K["Name"]=$B;$J[$K["Name"]]=$K;}return$J;}function
is_view(array$S){return$S["Engine"]===null;}function
fk_support(array$S){return
preg_match('~InnoDB|IBMDB2I'.(min_version(5.6)?'|NDB':'').'~i',$S["Engine"]);}function
fields($R){$We=(connection()->flavor=='maria');$J=array();foreach(get_rows("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ".q($R)." ORDER BY ORDINAL_POSITION")as$K){$m=$K["COLUMN_NAME"];$U=$K["COLUMN_TYPE"];$ud=$K["GENERATION_EXPRESSION"];$Rc=$K["EXTRA"];preg_match('~^(VIRTUAL|PERSISTENT|STORED)~',$Rc,$td);preg_match('~^([^( ]+)(?:\((.+)\))?( unsigned)?( zerofill)?$~',$U,$Ze);$k=$K["COLUMN_DEFAULT"];if($k!=""){$ye=preg_match('~text|json~',$Ze[1]);if(!$We&&$ye)$k=preg_replace("~^(_\w+)?('.*')$~",'\2',stripslashes($k));if($We||$ye){$k=($k=="NULL"?null:preg_replace_callback("~^'(.*)'$~",function($A){return
stripslashes(str_replace("''","'",$A[1]));},$k));}if(!$We&&preg_match('~binary~',$Ze[1])&&preg_match('~^0x(\w*)$~',$k,$A))$k=pack("H*",$A[1]);}$J[$m]=array("field"=>$m,"full_type"=>$U,"type"=>$Ze[1],"length"=>$Ze[2],"unsigned"=>ltrim($Ze[3].$Ze[4]),"default"=>($td?($We?$ud:stripslashes($ud)):$k),"null"=>($K["IS_NULLABLE"]=="YES"),"auto_increment"=>($Rc=="auto_increment"),"on_update"=>(preg_match('~\bon update (\w+)~i',$Rc,$A)?$A[1]:""),"collation"=>$K["COLLATION_NAME"],"privileges"=>array_flip(explode(",","$K[PRIVILEGES],where,order")),"comment"=>$K["COLUMN_COMMENT"],"primary"=>($K["COLUMN_KEY"]=="PRI"),"generated"=>($td[1]=="PERSISTENT"?"STORED":$td[1]),);}return$J;}function
indexes($R,$g=null){$J=array();foreach(get_rows("SHOW INDEX FROM ".table($R),$g)as$K){$B=$K["Key_name"];$J[$B]["type"]=($B=="PRIMARY"?"PRIMARY":($K["Index_type"]=="FULLTEXT"?"FULLTEXT":($K["Non_unique"]?($K["Index_type"]=="SPATIAL"?"SPATIAL":"INDEX"):"UNIQUE")));$J[$B]["columns"][]=$K["Column_name"];$J[$B]["lengths"][]=($K["Index_type"]=="SPATIAL"?null:$K["Sub_part"]);$J[$B]["descs"][]=null;$J[$B]["algorithm"]=$K["Index_type"];}return$J;}function
foreign_keys($R){static$Hg='(?:`(?:[^`]|``)+`|"(?:[^"]|"")+")';$J=array();$Eb=get_val("SHOW CREATE TABLE ".table($R),1);if($Eb){preg_match_all("~CONSTRAINT ($Hg) FOREIGN KEY ?\\(((?:$Hg,? ?)+)\\) REFERENCES ($Hg)(?:\\.($Hg))? \\(((?:$Hg,? ?)+)\\)(?: ON DELETE (".driver()->onActions."))?(?: ON UPDATE (".driver()->onActions."))?~",$Eb,$af,PREG_SET_ORDER);foreach($af
as$A){preg_match_all("~$Hg~",$A[2],$gi);preg_match_all("~$Hg~",$A[5],$Ji);$J[idf_unescape($A[1])]=array("db"=>idf_unescape($A[4]!=""?$A[3]:$A[4]),"table"=>idf_unescape($A[4]!=""?$A[4]:$A[3]),"source"=>array_map('Adminer\idf_unescape',$gi[0]),"target"=>array_map('Adminer\idf_unescape',$Ji[0]),"on_delete"=>($A[6]?:"RESTRICT"),"on_update"=>($A[7]?:"RESTRICT"),);}}return$J;}function
view($B){return
array("select"=>preg_replace('~^(?:[^`]|`[^`]*`)*\s+AS\s+~isU','',get_val("SHOW CREATE VIEW ".table($B),1)));}function
collations(){$J=array();foreach(get_rows("SHOW COLLATION")as$K){if($K["Default"])$J[$K["Charset"]][-1]=$K["Collation"];else$J[$K["Charset"]][]=$K["Collation"];}ksort($J);foreach($J
as$x=>$X)sort($J[$x]);return$J;}function
information_schema($j){return($j=="information_schema")||(min_version(5.5)&&$j=="performance_schema");}function
error(){return
h(preg_replace('~^You have an error.*syntax to use~U',"Syntax error",connection()->error));}function
create_database($j,$c){return
queries("CREATE DATABASE ".idf_escape($j).($c?" COLLATE ".q($c):""));}function
drop_databases(array$i){$J=apply_queries("DROP DATABASE",$i,'Adminer\idf_escape');restart_session();set_session("dbs",null);return$J;}function
rename_database($B,$c){$J=false;if(create_database($B,$c)){$T=array();$Nj=array();foreach(tables_list()as$R=>$U){if($U=='VIEW')$Nj[]=$R;else$T[]=$R;}$J=(!$T&&!$Nj)||move_tables($T,$Nj,$B);drop_databases($J?array(DB):array());}return$J;}function
auto_increment(){$Aa=" PRIMARY KEY";if($_GET["create"]!=""&&$_POST["auto_increment_col"]){foreach(indexes($_GET["create"])as$v){if(in_array($_POST["fields"][$_POST["auto_increment_col"]]["orig"],$v["columns"],true)){$Aa="";break;}if($v["type"]=="PRIMARY")$Aa=" UNIQUE";}}return" AUTO_INCREMENT$Aa";}function
alter_table($R,$B,array$n,array$kd,$ob,$yc,$c,$_a,$E){$b=array();foreach($n
as$m){if($m[1]){$k=$m[1][3];if(preg_match('~ GENERATED~',$k)){$m[1][3]=(connection()->flavor=='maria'?"":$m[1][2]);$m[1][2]=$k;}$b[]=($R!=""?($m[0]!=""?"CHANGE ".idf_escape($m[0]):"ADD"):" ")." ".implode($m[1]).($R!=""?$m[2]:"");}else$b[]="DROP ".idf_escape($m[0]);}$b=array_merge($b,$kd);$P=($ob!==null?" COMMENT=".q($ob):"").($yc?" ENGINE=".q($yc):"").($c?" COLLATE ".q($c):"").($_a!=""?" AUTO_INCREMENT=$_a":"");if($E){$Dg=array();if($E["partition_by"]=='RANGE'||$E["partition_by"]=='LIST'){foreach($E["partition_names"]as$x=>$X){$Y=$E["partition_values"][$x];$Dg[]="\n  PARTITION ".idf_escape($X)." VALUES ".($E["partition_by"]=='RANGE'?"LESS THAN":"IN").($Y!=""?" ($Y)":" MAXVALUE");}}$P
.="\nPARTITION BY $E[partition_by]($E[partition])";if($Dg)$P
.=" (".implode(",",$Dg)."\n)";elseif($E["partitions"])$P
.=" PARTITIONS ".(+$E["partitions"]);}elseif($E===null)$P
.="\nREMOVE PARTITIONING";if($R=="")return
queries("CREATE TABLE ".table($B)." (\n".implode(",\n",$b)."\n)$P");if($R!=$B)$b[]="RENAME TO ".table($B);if($P)$b[]=ltrim($P);return($b?queries("ALTER TABLE ".table($R)."\n".implode(",\n",$b)):true);}function
alter_indexes($R,$b){$Ua=array();foreach($b
as$X)$Ua[]=($X[2]=="DROP"?"\nDROP INDEX ".idf_escape($X[1]):"\nADD $X[0] ".($X[0]=="PRIMARY"?"KEY ":"").($X[1]!=""?idf_escape($X[1])." ":"")."(".implode(", ",$X[2]).")");return
queries("ALTER TABLE ".table($R).implode(",",$Ua));}function
truncate_tables(array$T){return
apply_queries("TRUNCATE TABLE",$T);}function
drop_views(array$Nj){return
queries("DROP VIEW ".implode(", ",array_map('Adminer\table',$Nj)));}function
drop_tables(array$T){return
queries("DROP TABLE ".implode(", ",array_map('Adminer\table',$T)));}function
move_tables(array$T,array$Nj,$Ji){$th=array();foreach($T
as$R)$th[]=table($R)." TO ".idf_escape($Ji).".".table($R);if(!$th||queries("RENAME TABLE ".implode(", ",$th))){$Xb=array();foreach($Nj
as$R)$Xb[table($R)]=view($R);connection()->select_db($Ji);$j=idf_escape(DB);foreach($Xb
as$B=>$Mj){if(!queries("CREATE VIEW $B AS ".str_replace(" $j."," ",$Mj["select"]))||!queries("DROP VIEW $j.$B"))return
false;}return
true;}return
false;}function
copy_tables(array$T,array$Nj,$Ji){queries("SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO'");foreach($T
as$R){$B=($Ji==DB?table("copy_$R"):idf_escape($Ji).".".table($R));if(($_POST["overwrite"]&&!queries("\nDROP TABLE IF EXISTS $B"))||!queries("CREATE TABLE $B LIKE ".table($R))||!queries("INSERT INTO $B SELECT * FROM ".table($R)))return
false;foreach(get_rows("SHOW TRIGGERS LIKE ".q(addcslashes($R,"%_\\")))as$K){$ij=$K["Trigger"];if(!queries("CREATE TRIGGER ".($Ji==DB?idf_escape("copy_$ij"):idf_escape($Ji).".".idf_escape($ij))." $K[Timing] $K[Event] ON $B FOR EACH ROW\n$K[Statement];"))return
false;}}foreach($Nj
as$R){$B=($Ji==DB?table("copy_$R"):idf_escape($Ji).".".table($R));$Mj=view($R);if(($_POST["overwrite"]&&!queries("DROP VIEW IF EXISTS $B"))||!queries("CREATE VIEW $B AS $Mj[select]"))return
false;}return
true;}function
trigger($B,$R){if($B=="")return
array();$L=get_rows("SHOW TRIGGERS WHERE `Trigger` = ".q($B));return
reset($L);}function
triggers($R){$J=array();foreach(get_rows("SHOW TRIGGERS LIKE ".q(addcslashes($R,"%_\\")))as$K)$J[$K["Trigger"]]=array($K["Timing"],$K["Event"]);return$J;}function
trigger_options(){return
array("Timing"=>array("BEFORE","AFTER"),"Event"=>array("INSERT","UPDATE","DELETE"),"Type"=>array("FOR EACH ROW"),);}function
routine($B,$U){$n=get_rows("SELECT
	PARAMETER_NAME field,
	DATA_TYPE type,
	CHARACTER_MAXIMUM_LENGTH length,
	REGEXP_REPLACE(DTD_IDENTIFIER, '^[^ ]+ ', '') `unsigned`,
	1 `null`,
	DTD_IDENTIFIER full_type,
	PARAMETER_MODE `inout`,
	CHARACTER_SET_NAME collation
FROM information_schema.PARAMETERS
WHERE SPECIFIC_SCHEMA = DATABASE() AND ROUTINE_TYPE = '$U' AND SPECIFIC_NAME = ".q($B)."
ORDER BY ORDINAL_POSITION");$J=connection()->query("SELECT ROUTINE_COMMENT comment, ROUTINE_DEFINITION definition, 'SQL' language
FROM information_schema.ROUTINES
WHERE ROUTINE_SCHEMA = DATABASE() AND ROUTINE_TYPE = '$U' AND ROUTINE_NAME = ".q($B))->fetch_assoc();if($n&&$n[0]['field']=='')$J['returns']=array_shift($n);$J['fields']=$n;return$J;}function
routines(){return
get_rows("SELECT SPECIFIC_NAME, ROUTINE_NAME, ROUTINE_TYPE, DTD_IDENTIFIER FROM information_schema.ROUTINES WHERE ROUTINE_SCHEMA = DATABASE()");}function
routine_languages(){return
array();}function
routine_id($B,array$K){return
idf_escape($B);}function
last_id($I){return
get_val("SELECT LAST_INSERT_ID()");}function
explain(Db$f,$H){return$f->query("EXPLAIN ".(min_version(5.1)&&!min_version(5.7)?"PARTITIONS ":"").$H);}function
found_rows(array$S,array$Z){return($Z||$S["Engine"]!="InnoDB"?null:$S["Rows"]);}function
create_sql($R,$_a,$ti){$J=get_val("SHOW CREATE TABLE ".table($R),1);if(!$_a)$J=preg_replace('~ AUTO_INCREMENT=\d+~','',$J);return$J;}function
truncate_sql($R){return"TRUNCATE ".table($R);}function
use_sql($Ob,$ti=""){$B=idf_escape($Ob);$J="";if(preg_match('~CREATE~',$ti)&&($h=get_val("SHOW CREATE DATABASE $B",1))){set_utf8mb4($h);if($ti=="DROP+CREATE")$J="DROP DATABASE IF EXISTS $B;\n";$J
.="$h;\n";}return$J."USE $B";}function
trigger_sql($R){$J="";foreach(get_rows("SHOW TRIGGERS LIKE ".q(addcslashes($R,"%_\\")),null,"-- ")as$K)$J
.="\nCREATE TRIGGER ".idf_escape($K["Trigger"])." $K[Timing] $K[Event] ON ".table($K["Table"])." FOR EACH ROW\n$K[Statement];;\n";return$J;}function
show_variables(){return
get_rows("SHOW VARIABLES");}function
show_status(){return
get_rows("SHOW STATUS");}function
process_list(){return
get_rows("SHOW FULL PROCESSLIST");}function
convert_field(array$m){if(preg_match("~binary~",$m["type"]))return"HEX(".idf_escape($m["field"]).")";if($m["type"]=="bit")return"BIN(".idf_escape($m["field"])." + 0)";if(preg_match("~geometry|point|linestring|polygon~",$m["type"]))return(min_version(8)?"ST_":"")."AsWKT(".idf_escape($m["field"]).")";}function
unconvert_field(array$m,$J){if(preg_match("~binary~",$m["type"]))$J="UNHEX($J)";if($m["type"]=="bit")$J="CONVERT(b$J, UNSIGNED)";if(preg_match("~geometry|point|linestring|polygon~",$m["type"])){$Tg=(min_version(8)?"ST_":"");$J=$Tg."GeomFromText($J, $Tg"."SRID($m[field]))";}return$J;}function
support($Vc){return
preg_match('~^(comment|columns|copy|database|drop_col|dump|indexes|kill|privileges|move_col|procedure|processlist|routine|sql|status|table|trigger|variables|view'.(min_version(5.1)?'|event':'').(min_version(8)?'|descidx':'').(min_version('8.0.16','10.2.1')?'|check':'').')$~',$Vc);}function
kill_process($t){return
queries("KILL ".number($t));}function
connection_id(){return"SELECT CONNECTION_ID()";}function
max_connections(){return
get_val("SELECT @@max_connections");}function
types(){return
array();}function
type_values($t){return"";}function
schemas(){return
array();}function
get_schema(){return"";}function
set_schema($Gh,$g=null){return
true;}}define('Adminer\JUSH',Driver::$jush);define('Adminer\SERVER',"".$_GET[DRIVER]);define('Adminer\DB',"$_GET[db]");define('Adminer\ME',preg_replace('~\?.*~','',relative_uri()).'?'.(sid()?SID.'&':'').(SERVER!==null?DRIVER."=".urlencode(SERVER).'&':'').($_GET["ext"]?"ext=".urlencode($_GET["ext"]).'&':'').(isset($_GET["username"])?"username=".urlencode($_GET["username"]).'&':'').(DB!=""?'db='.urlencode(DB).'&'.(isset($_GET["ns"])?"ns=".urlencode($_GET["ns"])."&":""):''));function
page_header($Ti,$l="",$Ma=array(),$Ui=""){page_headers();if(is_ajax()&&$l){page_messages($l);exit;}if(!ob_get_level())ob_start('ob_gzhandler',4096);$Vi=$Ti.($Ui!=""?": $Ui":"");$Wi=strip_tags($Vi.(SERVER!=""&&SERVER!="localhost"?h(" - ".SERVER):"")." - ".adminer()->name());echo'<!DOCTYPE html>
<html lang="zh" dir="ltr">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="robots" content="noindex">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>',$Wi,'</title>
<link rel="stylesheet" href="',h(preg_replace("~\\?.*~","",ME)."?file=default.css&version=5.4.2-dev"),'">
';$Ib=adminer()->css();if(is_int(key($Ib)))$Ib=array_fill_keys($Ib,'light');$Fd=in_array('light',$Ib)||in_array('',$Ib);$Dd=in_array('dark',$Ib)||in_array('',$Ib);$Lb=($Fd?($Dd?null:false):($Dd?:null));$kf=" media='(prefers-color-scheme: dark)'";if($Lb!==false)echo"<link rel='stylesheet'".($Lb?"":$kf)." href='".h(preg_replace("~\\?.*~","",ME)."?file=dark.css&version=5.4.2-dev")."'>\n";echo"<meta name='color-scheme' content='".($Lb===null?"light dark":($Lb?"dark":"light"))."'>\n",script_src(preg_replace("~\\?.*~","",ME)."?file=functions.js&version=5.4.2-dev");if(adminer()->head($Lb))echo"<link rel='icon' href='data:image/gif;base64,R0lGODlhEAAQAJEAAAQCBPz+/PwCBAROZCH5BAEAAAAALAAAAAAQABAAAAI2hI+pGO1rmghihiUdvUBnZ3XBQA7f05mOak1RWXrNq5nQWHMKvuoJ37BhVEEfYxQzHjWQ5qIAADs='>\n","<link rel='apple-touch-icon' href='".h(preg_replace("~\\?.*~","",ME)."?file=logo.png&version=5.4.2-dev")."'>\n";foreach($Ib
as$_j=>$uf){$ya=($uf=='dark'&&!$Lb?$kf:($uf=='light'&&$Dd?" media='(prefers-color-scheme: light)'":""));echo"<link rel='stylesheet'$ya href='".h($_j)."'>\n";}echo"\n<body class='".'ltr'." nojs";adminer()->bodyClass();echo"'>\n";$o=get_temp_dir()."/adminer.version";if(!$_COOKIE["adminer_version"]&&function_exists('openssl_verify')&&file_exists($o)&&filemtime($o)+86400>time()){$Lj=unserialize(file_get_contents($o));$dh="-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAwqWOVuF5uw7/+Z70djoK
RlHIZFZPO0uYRezq90+7Amk+FDNd7KkL5eDve+vHRJBLAszF/7XKXe11xwliIsFs
DFWQlsABVZB3oisKCBEuI71J4kPH8dKGEWR9jDHFw3cWmoH3PmqImX6FISWbG3B8
h7FIx3jEaw5ckVPVTeo5JRm/1DZzJxjyDenXvBQ/6o9DgZKeNDgxwKzH+sw9/YCO
jHnq1cFpOIISzARlrHMa/43YfeNRAm/tsBXjSxembBPo7aQZLAWHmaj5+K19H10B
nCpz9Y++cipkVEiKRGih4ZEvjoFysEOdRLj6WiD/uUNky4xGeA6LaJqh5XpkFkcQ
fQIDAQAB
-----END PUBLIC KEY-----
";if(openssl_verify($Lj["version"],base64_decode($Lj["signature"]),$dh)==1)$_COOKIE["adminer_version"]=$Lj["version"];}echo
script("mixin(document.body, {onkeydown: bodyKeydown, onclick: bodyClick".(isset($_COOKIE["adminer_version"])?"":", onload: partial(verifyVersion, '".VERSION."', '".js_escape(ME)."', '".get_token()."')")."});
document.body.classList.replace('nojs', 'js');
const offlineMessage = '".js_escape('您离线了。')."';
const thousandsSeparator = '".js_escape(',')."';"),"<div id='help' class='jush-".JUSH." jsonly hidden'></div>\n",script("mixin(qs('#help'), {onmouseover: () => { helpOpen = 1; }, onmouseout: helpMouseout});"),"<div id='content'>\n","<span id='menuopen' class='jsonly'>".icon("move","","menu","")."</span>".script("qs('#menuopen').onclick = event => { qs('#foot').classList.toggle('foot'); event.stopPropagation(); }");if($Ma!==null){$_=substr(preg_replace('~\b(username|db|ns)=[^&]*&~','',ME),0,-1);echo'<p id="breadcrumb"><a href="'.h($_?:".").'">'.get_driver(DRIVER).'</a> » ';$_=substr(preg_replace('~\b(db|ns)=[^&]*&~','',ME),0,-1);$N=adminer()->serverName(SERVER);$N=($N!=""?$N:'服务器');if($Ma===false)echo"$N\n";else{echo"<a href='".h($_)."' accesskey='1' title='Alt+Shift+1'>$N</a> » ";if($_GET["ns"]!=""||(DB!=""&&is_array($Ma)))echo'<a href="'.h($_."&db=".urlencode(DB).(support("scheme")?"&ns=":"")).'">'.h(DB).'</a> » ';if(is_array($Ma)){if($_GET["ns"]!="")echo'<a href="'.h(substr(ME,0,-1)).'">'.h($_GET["ns"]).'</a> » ';foreach($Ma
as$x=>$X){$Zb=(is_array($X)?$X[1]:h($X));if($Zb!="")echo"<a href='".h(ME."$x=").urlencode(is_array($X)?$X[0]:$X)."'>$Zb</a> » ";}}echo"$Ti\n";}}echo"<h2>$Vi</h2>\n","<div id='ajaxstatus' class='jsonly hidden'></div>\n";restart_session();page_messages($l);$i=&get_session("dbs");if(DB!=""&&$i&&!in_array(DB,$i,true))$i=null;stop_session();define('Adminer\PAGE_HEADER',1);}function
page_headers(){header("Content-Type: text/html; charset=utf-8");header("Cache-Control: no-cache");header("X-Frame-Options: deny");header("X-XSS-Protection: 0");header("X-Content-Type-Options: nosniff");header("Referrer-Policy: origin-when-cross-origin");foreach(adminer()->csp(csp())as$Hb){$Hd=array();foreach($Hb
as$x=>$X)$Hd[]="$x $X";header("Content-Security-Policy: ".implode("; ",$Hd));}adminer()->headers();}function
csp(){return
array(array("script-src"=>"'self' 'unsafe-inline' 'nonce-".get_nonce()."' 'strict-dynamic'","connect-src"=>"'self'","frame-src"=>"https://www.adminer.org","object-src"=>"'none'","base-uri"=>"'none'","form-action"=>"'self'",),);}function
get_nonce(){static$Gf;if(!$Gf)$Gf=base64_encode(rand_string());return$Gf;}function
page_messages($l){$zj=preg_replace('~^[^?]*~','',$_SERVER["REQUEST_URI"]);$qf=idx($_SESSION["messages"],$zj);if($qf){echo"<div class='message'>".implode("</div>\n<div class='message'>",$qf)."</div>".script("messagesPrint();");unset($_SESSION["messages"][$zj]);}if($l)echo"<div class='error'>$l</div>\n";if(adminer()->error)echo"<div class='error'>".adminer()->error."</div>\n";}function
page_footer($tf=""){echo"</div>\n\n<div id='foot' class='foot'>\n<div id='menu'>\n";adminer()->navigation($tf);echo"</div>\n";if($tf!="auth")echo'<form action="" method="post">
<p class="logout">
<span>',h($_GET["username"])."\n",'</span>
<input type="submit" name="logout" value="登出" id="logout">
',input_token(),'</form>
';echo"</div>\n\n",script("setupSubmitHighlight(document);");}function
int32($zf){while($zf>=2147483648)$zf-=4294967296;while($zf<=-2147483649)$zf+=4294967296;return(int)$zf;}function
long2str(array$W,$Pj){$Eh='';foreach($W
as$X)$Eh
.=pack('V',$X);if($Pj)return
substr($Eh,0,end($W));return$Eh;}function
str2long($Eh,$Pj){$W=array_values(unpack('V*',str_pad($Eh,4*ceil(strlen($Eh)/4),"\0")));if($Pj)$W[]=strlen($Eh);return$W;}function
xxtea_mx($Wj,$Vj,$wi,$Be){return
int32((($Wj>>5&0x7FFFFFF)^$Vj<<2)+(($Vj>>3&0x1FFFFFFF)^$Wj<<4))^int32(($wi^$Vj)+($Be^$Wj));}function
encrypt_string($ri,$x){if($ri=="")return"";$x=array_values(unpack("V*",pack("H*",md5($x))));$W=str2long($ri,true);$zf=count($W)-1;$Wj=$W[$zf];$Vj=$W[0];$eh=floor(6+52/($zf+1));$wi=0;while($eh-->0){$wi=int32($wi+0x9E3779B9);$pc=$wi>>2&3;for($tg=0;$tg<$zf;$tg++){$Vj=$W[$tg+1];$yf=xxtea_mx($Wj,$Vj,$wi,$x[$tg&3^$pc]);$Wj=int32($W[$tg]+$yf);$W[$tg]=$Wj;}$Vj=$W[0];$yf=xxtea_mx($Wj,$Vj,$wi,$x[$tg&3^$pc]);$Wj=int32($W[$zf]+$yf);$W[$zf]=$Wj;}return
long2str($W,false);}function
decrypt_string($ri,$x){if($ri=="")return"";if(!$x)return
false;$x=array_values(unpack("V*",pack("H*",md5($x))));$W=str2long($ri,false);$zf=count($W)-1;$Wj=$W[$zf];$Vj=$W[0];$eh=floor(6+52/($zf+1));$wi=int32($eh*0x9E3779B9);while($wi){$pc=$wi>>2&3;for($tg=$zf;$tg>0;$tg--){$Wj=$W[$tg-1];$yf=xxtea_mx($Wj,$Vj,$wi,$x[$tg&3^$pc]);$Vj=int32($W[$tg]-$yf);$W[$tg]=$Vj;}$Wj=$W[$zf];$yf=xxtea_mx($Wj,$Vj,$wi,$x[$tg&3^$pc]);$Vj=int32($W[0]-$yf);$W[0]=$Vj;$wi=int32($wi-0x9E3779B9);}return
long2str($W,true);}$Jg=array();if($_COOKIE["adminer_permanent"]){foreach(explode(" ",$_COOKIE["adminer_permanent"])as$X){list($x)=explode(":",$X);$Jg[$x]=$X;}}function
add_invalid_login(){$Fa=get_temp_dir()."/adminer.invalid";foreach(glob("$Fa*")?:array($Fa)as$o){$q=file_open_lock($o);if($q)break;}if(!$q)$q=file_open_lock("$Fa-".rand_string());if(!$q)return;$te=unserialize(stream_get_contents($q));$Qi=time();if($te){foreach($te
as$ue=>$X){if($X[0]<$Qi)unset($te[$ue]);}}$se=&$te[adminer()->bruteForceKey()];if(!$se)$se=array($Qi+30*60,0);$se[1]++;file_write_unlock($q,serialize($te));}function
check_invalid_login(array&$Jg){$te=array();foreach(glob(get_temp_dir()."/adminer.invalid*")as$o){$q=file_open_lock($o);if($q){$te=unserialize(stream_get_contents($q));file_unlock($q);break;}}$se=idx($te,adminer()->bruteForceKey(),array());$Ff=($se[1]>29?$se[0]-time():0);if($Ff>0)auth_error(sprintf('登录失败次数过多，请 %d 分钟后重试。',ceil($Ff/60)),$Jg);}$za=$_POST["auth"];if($za){session_regenerate_id();$Kj=$za["driver"];$N=$za["server"];$V=$za["username"];$F=(string)$za["password"];$j=$za["db"];set_password($Kj,$N,$V,$F);$_SESSION["db"][$Kj][$N][$V][$j]=true;if($za["permanent"]){$x=implode("-",array_map('base64_encode',array($Kj,$N,$V,$j)));$Yg=adminer()->permanentLogin(true);$Jg[$x]="$x:".base64_encode($Yg?encrypt_string($F,$Yg):"");cookie("adminer_permanent",implode(" ",$Jg));}if(count($_POST)==1||DRIVER!=$Kj||SERVER!=$N||$_GET["username"]!==$V||DB!=$j)redirect(auth_url($Kj,$N,$V,$j));}elseif($_POST["logout"]&&(!$_SESSION["token"]||verify_token())){foreach(array("pwds","db","dbs","queries")as$x)set_session($x,null);unset_permanent($Jg);redirect(substr(preg_replace('~\b(username|db|ns)=[^&]*&~','',ME),0,-1),'成功登出。'.' '.'感谢使用Adminer，请考虑为我们<a href="https://www.adminer.org/en/donation/">捐款（英文页面）</a>。');}elseif($Jg&&!$_SESSION["pwds"]){session_regenerate_id();$Yg=adminer()->permanentLogin();foreach($Jg
as$x=>$X){list(,$cb)=explode(":",$X);list($Kj,$N,$V,$j)=array_map('base64_decode',explode("-",$x));set_password($Kj,$N,$V,decrypt_string(base64_decode($cb),$Yg));$_SESSION["db"][$Kj][$N][$V][$j]=true;}}function
unset_permanent(array&$Jg){foreach($Jg
as$x=>$X){list($Kj,$N,$V,$j)=array_map('base64_decode',explode("-",$x));if($Kj==DRIVER&&$N==SERVER&&$V==$_GET["username"]&&$j==DB)unset($Jg[$x]);}cookie("adminer_permanent",implode(" ",$Jg));}function
auth_error($l,array&$Jg){$Yh=session_name();if(isset($_GET["username"])){header("HTTP/1.1 403 Forbidden");if(($_COOKIE[$Yh]||$_GET[$Yh])&&!$_SESSION["token"])$l='会话已过期，请重新登录。';else{restart_session();add_invalid_login();$F=get_password();if($F!==null){if($F===false)$l
.=($l?'<br>':'').sprintf('主密码已过期。<a href="https://www.adminer.org/en/extension/"%s>请扩展</a> %s 方法让它永久化。',target_blank(),'<code>permanentLogin()</code>');set_password(DRIVER,SERVER,$_GET["username"],null);}unset_permanent($Jg);}}if(!$_COOKIE[$Yh]&&$_GET[$Yh]&&ini_bool("session.use_only_cookies"))$l='必须启用会话支持。';$wg=session_get_cookie_params();cookie("adminer_key",($_COOKIE["adminer_key"]?:rand_string()),$wg["lifetime"]);if(!$_SESSION["token"])$_SESSION["token"]=rand(1,1e6);page_header('登录',$l,null);echo"<form action='' method='post'>\n","<div>";if(hidden_fields($_POST,array("auth")))echo"<p class='message'>".'此操作将在成功使用相同的凭据登录后执行。'."\n";echo"</div>\n";adminer()->loginForm();echo"</form>\n";page_footer("auth");exit;}if(isset($_GET["username"])&&!class_exists('Adminer\Db')){unset($_SESSION["pwds"][DRIVER]);unset_permanent($Jg);page_header('没有扩展',sprintf('没有支持的 PHP 扩展可用（%s）。',implode(", ",Driver::$extensions)),false);page_footer("auth");exit;}$f='';if(isset($_GET["username"])&&is_string(get_password())){list(,$Ng)=host_port(SERVER);if(preg_match('~^\s*([-+]?\d+)~',$Ng,$A)&&($A[1]<1024||$A[1]>65535))auth_error('不允许连接到特权端口。',$Jg);check_invalid_login($Jg);$Gb=adminer()->credentials();$f=Driver::connect($Gb[0],$Gb[1],$Gb[2]);if(is_object($f)){Db::$instance=$f;Driver::$instance=new
Driver($f);if($f->flavor)save_settings(array("vendor-".DRIVER."-".SERVER=>get_driver(DRIVER)));}}$Ue=null;if(!is_object($f)||($Ue=adminer()->login($_GET["username"],get_password()))!==true){$l=(is_string($f)?nl_br(h($f)):(is_string($Ue)?$Ue:'无效凭据。')).(preg_match('~^ | $~',get_password())?'<br>'.'您输入的密码中有一个空格，这可能是导致问题的原因。':'');auth_error($l,$Jg);}if($_POST["logout"]&&$_SESSION["token"]&&!verify_token()){page_header('登出','无效 CSRF 令牌。请重新发送表单。');page_footer("db");exit;}if(!$_SESSION["token"])$_SESSION["token"]=rand(1,1e6);stop_session(true);if($za&&$_POST["token"])$_POST["token"]=get_token();$l='';if($_POST){if(!verify_token()){$le="max_input_vars";$if=ini_get($le);if(extension_loaded("suhosin")){foreach(array("suhosin.request.max_vars","suhosin.post.max_vars")as$x){$X=ini_get($x);if($X&&(!$if||$X<$if)){$le=$x;$if=$X;}}}$l=(!$_POST["token"]&&$if?sprintf('超过最多允许的字段数量。请增加 %s。',"'$le'"):'无效 CSRF 令牌。请重新发送表单。'.' '.'如果您并没有从Adminer发送请求，请关闭此页面。');}}elseif($_SERVER["REQUEST_METHOD"]=="POST"){$l=sprintf('POST 数据太大。请减少数据或者增加 %s 配置命令。',"'post_max_size'");if(isset($_GET["sql"]))$l
.=' '.'您可以通过FTP上传大型SQL文件并从服务器导入。';}function
print_select_result($I,$g=null,array$ig=array(),$z=0){$Se=array();$w=array();$e=array();$Ka=array();$nj=array();$J=array();for($s=0;(!$z||$s<$z)&&($K=$I->fetch_row());$s++){if(!$s){echo"<div class='scrollable'>\n","<table class='nowrap odds'>\n","<thead><tr>";for($_e=0;$_e<count($K);$_e++){$m=$I->fetch_field();$B=$m->name;$hg=(isset($m->orgtable)?$m->orgtable:"");$gg=(isset($m->orgname)?$m->orgname:$B);if($ig&&JUSH=="sql")$Se[$_e]=($B=="table"?"table=":($B=="possible_keys"?"indexes=":null));elseif($hg!=""){if(isset($m->table))$J[$m->table]=$hg;if(!isset($w[$hg])){$w[$hg]=array();foreach(indexes($hg,$g)as$v){if($v["type"]=="PRIMARY"){$w[$hg]=array_flip($v["columns"]);break;}}$e[$hg]=$w[$hg];}if(isset($e[$hg][$gg])){unset($e[$hg][$gg]);$w[$hg][$gg]=$_e;$Se[$_e]=$hg;}}if($m->charsetnr==63)$Ka[$_e]=true;$nj[$_e]=$m->type;echo"<th".($hg!=""||$m->name!=$gg?" title='".h(($hg!=""?"$hg.":"").$gg)."'":"").">".h($B).($ig?doc_link(array('sql'=>"explain-output.html#explain_".strtolower($B),'mariadb'=>"explain/#the-columns-in-explain-select",)):"");}echo"</thead>\n";}echo"<tr>";foreach($K
as$x=>$X){$_="";if(isset($Se[$x])&&!$e[$Se[$x]]){if($ig&&JUSH=="sql"){$R=$K[array_search("table=",$Se)];$_=ME.$Se[$x].urlencode($ig[$R]!=""?$ig[$R]:$R);}else{$_=ME."edit=".urlencode($Se[$x]);foreach($w[$Se[$x]]as$hb=>$_e){if($K[$_e]===null){$_="";break;}$_
.="&where".urlencode("[".bracket_escape($hb)."]")."=".urlencode($K[$_e]);}}}elseif(is_url($X))$_=$X;if($X===null)$X="<i>NULL</i>";elseif($Ka[$x]&&!is_utf8($X))$X="<i>".sprintf('%d 字节',strlen($X))."</i>";else{$X=h($X);if($nj[$x]==254)$X="<code>$X</code>";}if($_)$X="<a href='".h($_)."'".(is_url($_)?target_blank():'').">$X</a>";echo"<td".($nj[$x]<=9||$nj[$x]==246?" class='number'":"").">$X";}}echo($s?"</table>\n</div>":"<p class='message'>".'无数据。')."\n";return$J;}function
referencable_primary($Qh){$J=array();foreach(table_status('',true)as$Ai=>$R){if($Ai!=$Qh&&fk_support($R)){foreach(fields($Ai)as$m){if($m["primary"]){if($J[$Ai]){unset($J[$Ai]);break;}$J[$Ai]=$m;}}}}return$J;}function
textarea($B,$Y,$L=10,$kb=80){echo"<textarea name='".h($B)."' rows='$L' cols='$kb' class='sqlarea jush-".JUSH."' spellcheck='false' wrap='off'>";if(is_array($Y)){foreach($Y
as$X)echo
h($X[0])."\n\n\n";}else
echo
h($Y);echo"</textarea>";}function
select_input($ya,array$cg,$Y="",$Wf="",$Kg=""){$Ii=($cg?"select":"input");return"<$Ii$ya".($cg?"><option value=''>$Kg".optionlist($cg,$Y,true)."</select>":" size='10' value='".h($Y)."' placeholder='$Kg'>").($Wf?script("qsl('$Ii').onchange = $Wf;",""):"");}function
json_row($x,$X=null,$Gc=true){static$cd=true;if($cd)echo"{";if($x!=""){echo($cd?"":",")."\n\t\"".addcslashes($x,"\r\n\t\"\\/").'": '.($X!==null?($Gc?'"'.addcslashes($X,"\r\n\"\\/").'"':$X):'null');$cd=false;}else{echo"\n}\n";$cd=true;}}function
edit_type($x,array$m,array$jb,array$md=array(),array$Sc=array()){$U=$m["type"];echo"<td><select name='".h($x)."[type]' class='type' aria-labelledby='label-type'>";if($U&&!array_key_exists($U,driver()->types())&&!isset($md[$U])&&!in_array($U,$Sc))$Sc[]=$U;$si=driver()->structuredTypes();if($md)$si['外键']=$md;echo
optionlist(array_merge($Sc,$si),$U),"</select><td>","<input name='".h($x)."[length]' value='".h($m["length"])."' size='3'".(!$m["length"]&&preg_match('~var(char|binary)$~',$U)?" class='required'":"")." aria-labelledby='label-length'>","<td class='options'>",($jb?"<input list='collations' name='".h($x)."[collation]'".(preg_match('~(char|text|enum|set)$~',$U)?"":" class='hidden'")." value='".h($m["collation"])."' placeholder='(".'校对'.")'>":''),(driver()->unsigned?"<select name='".h($x)."[unsigned]'".(!$U||preg_match(number_type(),$U)?"":" class='hidden'").'><option>'.optionlist(driver()->unsigned,$m["unsigned"]).'</select>':''),(isset($m['on_update'])?"<select name='".h($x)."[on_update]'".(preg_match('~timestamp|datetime~',$U)?"":" class='hidden'").'>'.optionlist(array(""=>"(".'ON UPDATE'.")","CURRENT_TIMESTAMP"),(preg_match('~^CURRENT_TIMESTAMP~i',$m["on_update"])?"CURRENT_TIMESTAMP":$m["on_update"])).'</select>':''),($md?"<select name='".h($x)."[on_delete]'".(preg_match("~`~",$U)?"":" class='hidden'")."><option value=''>(".'ON DELETE'.")".optionlist(explode("|",driver()->onActions),$m["on_delete"])."</select> ":" ");}function
process_length($y){$Bc=driver()->enumLength;return(preg_match("~^\\s*\\(?\\s*$Bc(?:\\s*,\\s*$Bc)*+\\s*\\)?\\s*\$~",$y)&&preg_match_all("~$Bc~",$y,$af)?"(".implode(",",$af[0]).")":preg_replace('~^[0-9].*~','(\0)',preg_replace('~[^-0-9,+()[\]]~','',$y)));}function
process_type(array$m,$ib="COLLATE"){return" $m[type]".process_length($m["length"]).(preg_match(number_type(),$m["type"])&&in_array($m["unsigned"],driver()->unsigned)?" $m[unsigned]":"").(preg_match('~char|text|enum|set~',$m["type"])&&$m["collation"]?" $ib ".(JUSH=="mssql"?$m["collation"]:q($m["collation"])):"");}function
process_field(array$m,array$mj){if($m["on_update"])$m["on_update"]=str_ireplace("current_timestamp()","CURRENT_TIMESTAMP",$m["on_update"]);return
array(idf_escape(trim($m["field"])),process_type($mj),($m["null"]?" NULL":" NOT NULL"),default_value($m),(preg_match('~timestamp|datetime~',$m["type"])&&$m["on_update"]?" ON UPDATE $m[on_update]":""),(support("comment")&&$m["comment"]!=""?" COMMENT ".q($m["comment"]):""),($m["auto_increment"]?auto_increment():null),);}function
default_value(array$m){$k=$m["default"];$td=$m["generated"];return($k===null?"":(in_array($td,driver()->generated)?(JUSH=="mssql"?" AS ($k)".($td=="VIRTUAL"?"":" $td")."":" GENERATED ALWAYS AS ($k) $td"):" DEFAULT ".(!preg_match('~^GENERATED ~i',$k)&&(preg_match('~char|binary|text|json|enum|set~',$m["type"])||preg_match('~^(?![a-z])~i',$k))?(JUSH=="sql"&&preg_match('~text|json~',$m["type"])?"(".q($k).")":q($k)):str_ireplace("current_timestamp()","CURRENT_TIMESTAMP",(JUSH=="sqlite"?"($k)":$k)))));}function
type_class($U){foreach(array('char'=>'text','date'=>'time|year','binary'=>'blob','enum'=>'set',)as$x=>$X){if(preg_match("~$x|$X~",$U))return" class='$x'";}}function
edit_fields(array$n,array$jb,$U="TABLE",array$md=array()){$n=array_values($n);$Ub=(($_POST?$_POST["defaults"]:get_setting("defaults"))?"":" class='hidden'");$qb=(($_POST?$_POST["comments"]:get_setting("comments"))?"":" class='hidden'");echo"<thead><tr>\n",($U=="PROCEDURE"?"<td>":""),"<th id='label-name'>".($U=="TABLE"?'字段名':'参数名'),"<td id='label-type'>".'类型'."<textarea id='enum-edit' rows='4' cols='12' wrap='off' style='display: none;'></textarea>".script("qs('#enum-edit').onblur = editingLengthBlur;"),"<td id='label-length'>".'长度',"<td>".'选项';if($U=="TABLE")echo"<td id='label-null'>NULL\n","<td><input type='radio' name='auto_increment_col' value=''><abbr id='label-ai' title='".'自动增量'."'>AI</abbr>",doc_link(array('sql'=>"example-auto-increment.html",'mariadb'=>"auto_increment/",'sqlite'=>"autoinc.html",'pgsql'=>"datatype-numeric.html#DATATYPE-SERIAL",'mssql'=>"t-sql/statements/create-table-transact-sql-identity-property",)),"<td id='label-default'$Ub>".'默认值',(support("comment")?"<td id='label-comment'$qb>".'注释':"");echo"<td>".icon("plus","add[".(support("move_col")?0:count($n))."]","+",'下一行插入'),"</thead>\n<tbody>\n",script("mixin(qsl('tbody'), {onclick: editingClick, onkeydown: editingKeydown, oninput: editingInput});");foreach($n
as$s=>$m){$s++;$jg=$m[($_POST?"orig":"field")];$fc=(isset($_POST["add"][$s-1])||(isset($m["field"])&&!idx($_POST["drop_col"],$s)))&&(support("drop_col")||$jg=="");echo"<tr".($fc?"":" style='display: none;'").">\n",($U=="PROCEDURE"?"<td>".html_select("fields[$s][inout]",explode("|",driver()->inout),$m["inout"]):"")."<th>";if($fc)echo"<input name='fields[$s][field]' value='".h($m["field"])."' data-maxlength='64' autocapitalize='off' aria-labelledby='label-name'".(isset($_POST["add"][$s-1])?" autofocus":"").">";echo
input_hidden("fields[$s][orig]",$jg);edit_type("fields[$s]",$m,$jb,$md);if($U=="TABLE")echo"<td>".checkbox("fields[$s][null]",1,$m["null"],"","","block","label-null"),"<td><label class='block'><input type='radio' name='auto_increment_col' value='$s'".($m["auto_increment"]?" checked":"")." aria-labelledby='label-ai'></label>","<td$Ub>".(driver()->generated?html_select("fields[$s][generated]",array_merge(array("","DEFAULT"),driver()->generated),$m["generated"])." ":checkbox("fields[$s][generated]",1,$m["generated"],"","","","label-default")),"<input name='fields[$s][default]' value='".h($m["default"])."' aria-labelledby='label-default'>",(support("comment")?"<td$qb><input name='fields[$s][comment]' value='".h($m["comment"])."' data-maxlength='".(min_version(5.5)?1024:255)."' aria-labelledby='label-comment'>":"");echo"<td>",(support("move_col")?icon("plus","add[$s]","+",'下一行插入')." ".icon("up","up[$s]","↑",'上移')." ".icon("down","down[$s]","↓",'下移')." ":""),($jg==""||support("drop_col")?icon("cross","drop_col[$s]","x",'移除'):"");}}function
process_fields(array&$n){$C=0;if($_POST["up"]){$Je=0;foreach($n
as$x=>$m){if(key($_POST["up"])==$x){unset($n[$x]);array_splice($n,$Je,0,array($m));break;}if(isset($m["field"]))$Je=$C;$C++;}}elseif($_POST["down"]){$od=false;foreach($n
as$x=>$m){if(isset($m["field"])&&$od){unset($n[key($_POST["down"])]);array_splice($n,$C,0,array($od));break;}if(key($_POST["down"])==$x)$od=$m;$C++;}}elseif($_POST["add"]){$n=array_values($n);array_splice($n,key($_POST["add"]),0,array(array()));}elseif(!$_POST["drop_col"])return
false;return
true;}function
normalize_enum(array$A){$X=$A[0];return"'".str_replace("'","''",addcslashes(stripcslashes(str_replace($X[0].$X[0],$X[0],substr($X,1,-1))),'\\'))."'";}function
grant($vd,array$ah,$e,$Tf){if(!$ah)return
true;if($ah==array("ALL PRIVILEGES","GRANT OPTION"))return($vd=="GRANT"?queries("$vd ALL PRIVILEGES$Tf WITH GRANT OPTION"):queries("$vd ALL PRIVILEGES$Tf")&&queries("$vd GRANT OPTION$Tf"));return
queries("$vd ".preg_replace('~(GRANT OPTION)\([^)]*\)~','\1',implode("$e, ",$ah).$e).$Tf);}function
drop_create($jc,$h,$lc,$Mi,$nc,$Te,$pf,$nf,$of,$Qf,$Cf){if($_POST["drop"])query_redirect($jc,$Te,$pf);elseif($Qf=="")query_redirect($h,$Te,$of);elseif($Qf!=$Cf){$Fb=queries($h);queries_redirect($Te,$nf,$Fb&&queries($jc));if($Fb)queries($lc);}else
queries_redirect($Te,$nf,queries($Mi)&&queries($nc)&&queries($jc)&&queries($h));}function
create_trigger($Tf,array$K){$Si=" $K[Timing] $K[Event]".(preg_match('~ OF~',$K["Event"])?" $K[Of]":"");return"CREATE TRIGGER ".idf_escape($K["Trigger"]).(JUSH=="mssql"?$Tf.$Si:$Si.$Tf).rtrim(" $K[Type]\n$K[Statement]",";").";";}function
create_routine($Ah,array$K){$O=array();$n=(array)$K["fields"];ksort($n);foreach($n
as$m){if($m["field"]!="")$O[]=(preg_match("~^(".driver()->inout.")\$~",$m["inout"])?"$m[inout] ":"").idf_escape($m["field"]).process_type($m,"CHARACTER SET");}$Wb=rtrim($K["definition"],";");return"CREATE $Ah ".idf_escape(trim($K["name"]))." (".implode(", ",$O).")".($Ah=="FUNCTION"?" RETURNS".process_type($K["returns"],"CHARACTER SET"):"").($K["language"]?" LANGUAGE $K[language]":"").(JUSH=="pgsql"?" AS ".q($Wb):"\n$Wb;");}function
remove_definer($H){return
preg_replace('~^([A-Z =]+) DEFINER=`'.preg_replace('~@(.*)~','`@`(%|\1)',logged_user()).'`~','\1',$H);}function
format_foreign_key(array$p){$j=$p["db"];$Hf=$p["ns"];return" FOREIGN KEY (".implode(", ",array_map('Adminer\idf_escape',$p["source"])).") REFERENCES ".($j!=""&&$j!=$_GET["db"]?idf_escape($j).".":"").($Hf!=""&&$Hf!=$_GET["ns"]?idf_escape($Hf).".":"").idf_escape($p["table"])." (".implode(", ",array_map('Adminer\idf_escape',$p["target"])).")".(preg_match("~^(".driver()->onActions.")\$~",$p["on_delete"])?" ON DELETE $p[on_delete]":"").(preg_match("~^(".driver()->onActions.")\$~",$p["on_update"])?" ON UPDATE $p[on_update]":"");}function
tar_file($o,$Xi){$J=pack("a100a8a8a8a12a12",$o,644,0,0,decoct($Xi->size),decoct(time()));$bb=8*32;for($s=0;$s<strlen($J);$s++)$bb+=ord($J[$s]);$J
.=sprintf("%06o",$bb)."\0 ";echo$J,str_repeat("\0",512-strlen($J));$Xi->send();echo
str_repeat("\0",511-($Xi->size+511)%512);}function
doc_link(array$Gg,$Ni="<sup>?</sup>"){$Wh=connection()->server_info;$Lj=preg_replace('~^(\d\.?\d).*~s','\1',$Wh);$Aj=array('sql'=>"https://dev.mysql.com/doc/refman/$Lj/en/",'sqlite'=>"https://www.sqlite.org/",'pgsql'=>"https://www.postgresql.org/docs/".(connection()->flavor=='cockroach'?"current":$Lj)."/",'mssql'=>"https://learn.microsoft.com/en-us/sql/",'oracle'=>"https://www.oracle.com/pls/topic/lookup?ctx=db".preg_replace('~^.* (\d+)\.(\d+)\.\d+\.\d+\.\d+.*~s','\1\2',$Wh)."&id=",);if(connection()->flavor=='maria'){$Aj['sql']="https://mariadb.com/kb/en/";$Gg['sql']=(isset($Gg['mariadb'])?$Gg['mariadb']:str_replace(".html","/",$Gg['sql']));}return($Gg[JUSH]?"<a href='".h($Aj[JUSH].$Gg[JUSH].(JUSH=='mssql'?"?view=sql-server-ver$Lj":""))."'".target_blank().">$Ni</a>":"");}function
db_size($j){if(!connection()->select_db($j))return"?";$J=0;foreach(table_status()as$S)$J+=$S["Data_length"]+$S["Index_length"];return
format_number($J);}function
set_utf8mb4($h){static$O=false;if(!$O&&preg_match('~\butf8mb4~i',$h)){$O=true;echo"SET NAMES ".charset(connection()).";\n\n";}}if(isset($_GET["status"]))$_GET["variables"]=$_GET["status"];if(isset($_GET["import"]))$_GET["sql"]=$_GET["import"];if(!(DB!=""?connection()->select_db(DB):isset($_GET["sql"])||isset($_GET["dump"])||isset($_GET["database"])||isset($_GET["processlist"])||isset($_GET["privileges"])||isset($_GET["user"])||isset($_GET["variables"])||$_GET["script"]=="connect"||$_GET["script"]=="kill")){if(DB!=""||$_GET["refresh"]){restart_session();set_session("dbs",null);}if(DB!=""){header("HTTP/1.1 404 Not Found");page_header('数据库'.": ".h(DB),'无效数据库。',true);}else{if($_POST["db"]&&!$l)queries_redirect(substr(ME,0,-1),'已删除数据库。',drop_databases($_POST["db"]));page_header('选择数据库',$l,false);echo"<p class='links'>\n";foreach(array('database'=>'创建数据库','privileges'=>'权限','processlist'=>'进程列表','variables'=>'变量','status'=>'状态',)as$x=>$X){if(support($x))echo"<a href='".h(ME)."$x='>$X</a>\n";}echo"<p>".sprintf('%s 版本：%s， 使用PHP扩展 %s',get_driver(DRIVER),"<b>".h(connection()->server_info)."</b>","<b>".connection()->extension."</b>")."\n","<p>".sprintf('登录用户：%s',"<b>".h(logged_user())."</b>")."\n";$i=adminer()->databases();if($i){$Ih=support("scheme");$jb=collations();echo"<form action='' method='post'>\n","<table class='checkable odds'>\n",script("mixin(qsl('table'), {onclick: tableClick, ondblclick: partialArg(tableClick, true)});"),"<thead><tr>".(support("database")?"<td>":"")."<th>".'数据库'.(get_session("dbs")!==null?" - <a href='".h(ME)."refresh=1'>".'刷新'."</a>":"")."<td>".'校对'."<td>".'表'."<td>".'大小'." - <a href='".h(ME)."dbsize=1'>".'计算'."</a>".script("qsl('a').onclick = partial(ajaxSetHtml, '".js_escape(ME)."script=connect');","")."</thead>\n";$i=($_GET["dbsize"]?count_tables($i):array_flip($i));foreach($i
as$j=>$T){$_h=h(ME)."db=".urlencode($j);$t=h("Db-".$j);echo"<tr>".(support("database")?"<td>".checkbox("db[]",$j,in_array($j,(array)$_POST["db"]),"","","",$t):""),"<th><a href='$_h' id='$t'>".h($j)."</a>";$c=h(db_collation($j,$jb));echo"<td>".(support("database")?"<a href='$_h".($Ih?"&amp;ns=":"")."&amp;database=' title='".'修改数据库'."'>$c</a>":$c),"<td align='right'><a href='$_h&amp;schema=' id='tables-".h($j)."' title='".'数据库概要'."'>".($_GET["dbsize"]?$T:"?")."</a>","<td align='right' id='size-".h($j)."'>".($_GET["dbsize"]?db_size($j):"?"),"\n";}echo"</table>\n",(support("database")?"<div class='footer'><div>\n"."<fieldset><legend>".'已选中'." <span id='selected'></span></legend><div>\n".input_hidden("all").script("qsl('input').onclick = function () { selectCount('selected', formChecked(this, /^db/)); };")."<input type='submit' name='drop' value='".'删除'."'>".confirm()."\n"."</div></fieldset>\n"."</div></div>\n":""),input_token(),"</form>\n",script("tableCheck();");}if(!empty(adminer()->plugins)){echo"<div class='plugins'>\n","<h3>".'Loaded plugins'."</h3>\n<ul>\n";foreach(adminer()->plugins
as$Lg){$ac=(method_exists($Lg,'description')?$Lg->description():"");if(!$ac){$ph=new
\ReflectionObject($Lg);if(preg_match('~^/[\s*]+(.+)~',$ph->getDocComment(),$A))$ac=$A[1];}$Jh=(method_exists($Lg,'screenshot')?$Lg->screenshot():"");echo"<li><b>".get_class($Lg)."</b>".h($ac?": $ac":"").($Jh?" (<a href='".h($Jh)."'".target_blank().">".'screenshot'."</a>)":"")."\n";}echo"</ul>\n";adminer()->pluginsLinks();echo"</div>\n";}}page_footer("db");exit;}if(support("scheme")){if(DB!=""&&$_GET["ns"]!==""){if(!isset($_GET["ns"]))redirect(preg_replace('~ns=[^&]*&~','',ME)."ns=".get_schema());if(!set_schema($_GET["ns"])){header("HTTP/1.1 404 Not Found");page_header('模式'.": ".h($_GET["ns"]),'非法模式。',true);page_footer("ns");exit;}}}adminer()->afterConnect();class
TmpFile{private$handler;var$size;function
__construct(){$this->handler=tmpfile();}function
write($_b){$this->size+=strlen($_b);fwrite($this->handler,$_b);}function
send(){fseek($this->handler,0);fpassthru($this->handler);fclose($this->handler);}}if(isset($_GET["select"])&&($_POST["edit"]||$_POST["clone"])&&!$_POST["save"])$_GET["edit"]=$_GET["select"];if(isset($_GET["callf"]))$_GET["call"]=$_GET["callf"];if(isset($_GET["function"]))$_GET["procedure"]=$_GET["function"];if(isset($_GET["download"])){$a=$_GET["download"];$n=fields($a);header("Content-Type: application/octet-stream");header("Content-Disposition: attachment; filename=".friendly_url("$a-".implode("_",$_GET["where"])).".".friendly_url($_GET["field"]));$M=array(idf_escape($_GET["field"]));$I=driver()->select($a,$M,array(where($_GET,$n)),$M);$K=($I?$I->fetch_row():array());echo
driver()->value($K[0],$n[$_GET["field"]]);exit;}elseif(isset($_GET["table"])){$a=$_GET["table"];$n=fields($a);if(!$n)$l=error()?:'没有表。';$S=table_status1($a);$B=adminer()->tableName($S);page_header(($n&&is_view($S)?$S['Engine']=='materialized view'?'物化视图':'视图':'表').": ".($B!=""?$B:h($a)),$l);$zh=array();foreach($n
as$x=>$m)$zh+=$m["privileges"];adminer()->selectLinks($S,(isset($zh["insert"])||!support("table")?"":null));$ob=$S["Comment"];if($ob!="")echo"<p class='nowrap'>".'注释'.": ".h($ob)."\n";if($n)adminer()->tableStructurePrint($n,$S);function
tables_links(array$T){echo"<ul>\n";foreach($T
as$R)echo"<li><a href='".h(ME."table=".urlencode($R))."'>".h($R)."</a>";echo"</ul>\n";}$ke=driver()->inheritsFrom($a);if($ke){echo"<h3>".'Inherits from'."</h3>\n";tables_links($ke);}if(support("indexes")&&driver()->supportsIndex($S)){echo"<h3 id='indexes'>".'索引'."</h3>\n";$w=indexes($a);if($w)adminer()->tableIndexesPrint($w,$S);echo'<p class="links"><a href="'.h(ME).'indexes='.urlencode($a).'">'.'修改索引'."</a>\n";}if(!is_view($S)){if(fk_support($S)){echo"<h3 id='foreign-keys'>".'外键'."</h3>\n";$md=foreign_keys($a);if($md){echo"<table>\n","<thead><tr><th>".'源'."<td>".'目标'."<td>".'ON DELETE'."<td>".'ON UPDATE'."<td></thead>\n";foreach($md
as$B=>$p){echo"<tr title='".h($B)."'>","<th><i>".implode("</i>, <i>",array_map('Adminer\h',$p["source"]))."</i>";$_=($p["db"]!=""?preg_replace('~db=[^&]*~',"db=".urlencode($p["db"]),ME):($p["ns"]!=""?preg_replace('~ns=[^&]*~',"ns=".urlencode($p["ns"]),ME):ME));echo"<td><a href='".h($_."table=".urlencode($p["table"]))."'>".($p["db"]!=""&&$p["db"]!=DB?"<b>".h($p["db"])."</b>.":"").($p["ns"]!=""&&$p["ns"]!=$_GET["ns"]?"<b>".h($p["ns"])."</b>.":"").h($p["table"])."</a>","(<i>".implode("</i>, <i>",array_map('Adminer\h',$p["target"]))."</i>)","<td>".h($p["on_delete"]),"<td>".h($p["on_update"]),'<td><a href="'.h(ME.'foreign='.urlencode($a).'&name='.urlencode($B)).'">'.'修改'.'</a>',"\n";}echo"</table>\n";}echo'<p class="links"><a href="'.h(ME).'foreign='.urlencode($a).'">'.'添加外键'."</a>\n";}if(support("check")){echo"<h3 id='checks'>".'Checks'."</h3>\n";$Xa=driver()->checkConstraints($a);if($Xa){echo"<table>\n";foreach($Xa
as$x=>$X)echo"<tr title='".h($x)."'>","<td><code class='jush-".JUSH."'>".h($X),"<td><a href='".h(ME.'check='.urlencode($a).'&name='.urlencode($x))."'>".'修改'."</a>","\n";echo"</table>\n";}echo'<p class="links"><a href="'.h(ME).'check='.urlencode($a).'">'.'Create check'."</a>\n";}}if(support(is_view($S)?"view_trigger":"trigger")){echo"<h3 id='triggers'>".'触发器'."</h3>\n";$lj=triggers($a);if($lj){echo"<table>\n";foreach($lj
as$x=>$X)echo"<tr valign='top'><td>".h($X[0])."<td>".h($X[1])."<th>".h($x)."<td><a href='".h(ME.'trigger='.urlencode($a).'&name='.urlencode($x))."'>".'修改'."</a>\n";echo"</table>\n";}echo'<p class="links"><a href="'.h(ME).'trigger='.urlencode($a).'">'.'创建触发器'."</a>\n";}$je=driver()->inheritedTables($a);if($je){echo"<h3 id='partitions'>".'Inherited by'."</h3>\n";$_g=driver()->partitionsInfo($a);if($_g)echo"<p><code class='jush-".JUSH."'>BY ".h("$_g[partition_by]($_g[partition])")."</code>\n";tables_links($je);}}elseif(isset($_GET["schema"])){page_header('数据库概要',"",array(),h(DB.($_GET["ns"]?".$_GET[ns]":"")));$Ci=array();$Di=array();$ca=($_GET["schema"]?:$_COOKIE["adminer_schema-".str_replace(".","_",DB)]);preg_match_all('~([^:]+):([-0-9.]+)x([-0-9.]+)(_|$)~',$ca,$af,PREG_SET_ORDER);foreach($af
as$s=>$A){$Ci[$A[1]]=array($A[2],$A[3]);$Di[]="\n\t'".js_escape($A[1])."': [ $A[2], $A[3] ]";}$aj=0;$Ga=-1;$Gh=array();$oh=array();$Ne=array();$sa=driver()->allFields();foreach(table_status('',true)as$R=>$S){if(is_view($S))continue;$Og=0;$Gh[$R]["fields"]=array();foreach($sa[$R]as$m){$Og+=1.25;$m["pos"]=$Og;$Gh[$R]["fields"][$m["field"]]=$m;}$Gh[$R]["pos"]=($Ci[$R]?:array($aj,0));foreach(adminer()->foreignKeys($R)as$X){if(!$X["db"]){$Le=$Ga;if(idx($Ci[$R],1)||idx($Ci[$X["table"]],1))$Le=min(idx($Ci[$R],1,0),idx($Ci[$X["table"]],1,0))-1;else$Ga-=.1;while($Ne[(string)$Le])$Le-=.0001;$Gh[$R]["references"][$X["table"]][(string)$Le]=array($X["source"],$X["target"]);$oh[$X["table"]][$R][(string)$Le]=$X["target"];$Ne[(string)$Le]=true;}}$aj=max($aj,$Gh[$R]["pos"][0]+2.5+$Og);}echo'<div id="schema" style="height: ',$aj,'em;">
<script',nonce(),'>
qs(\'#schema\').onselectstart = () => false;
const tablePos = {',implode(",",$Di)."\n",'};
const em = qs(\'#schema\').offsetHeight / ',$aj,';
document.onmousemove = schemaMousemove;
document.onmouseup = partialArg(schemaMouseup, \'',js_escape(DB),'\');
</script>
';foreach($Gh
as$B=>$R){echo"<div class='table' style='top: ".$R["pos"][0]."em; left: ".$R["pos"][1]."em;'>",'<a href="'.h(ME).'table='.urlencode($B).'"><b>'.h($B)."</b></a>",script("qsl('div').onmousedown = schemaMousedown;");foreach($R["fields"]as$m){$X='<span'.type_class($m["type"]).' title="'.h($m["type"].($m["length"]?"($m[length])":"").($m["null"]?" NULL":'')).'">'.h($m["field"]).'</span>';echo"<br>".($m["primary"]?"<i>$X</i>":$X);}foreach((array)$R["references"]as$Ki=>$qh){foreach($qh
as$Le=>$lh){$Me=$Le-idx($Ci[$B],1);$s=0;foreach($lh[0]as$gi)echo"\n<div class='references' title='".h($Ki)."' id='refs$Le-".($s++)."' style='left: $Me"."em; top: ".$R["fields"][$gi]["pos"]."em; padding-top: .5em;'>"."<div style='border-top: 1px solid gray; width: ".(-$Me)."em;'></div></div>";}}foreach((array)$oh[$B]as$Ki=>$qh){foreach($qh
as$Le=>$e){$Me=$Le-idx($Ci[$B],1);$s=0;foreach($e
as$Ji)echo"\n<div class='references arrow' title='".h($Ki)."' id='refd$Le-".($s++)."' style='left: $Me"."em; top: ".$R["fields"][$Ji]["pos"]."em;'>"."<div style='height: .5em; border-bottom: 1px solid gray; width: ".(-$Me)."em;'></div>"."</div>";}}echo"\n</div>\n";}foreach($Gh
as$B=>$R){foreach((array)$R["references"]as$Ki=>$qh){foreach($qh
as$Le=>$lh){$sf=$aj;$gf=-10;foreach($lh[0]as$x=>$gi){$Pg=$R["pos"][0]+$R["fields"][$gi]["pos"];$Qg=$Gh[$Ki]["pos"][0]+$Gh[$Ki]["fields"][$lh[1][$x]]["pos"];$sf=min($sf,$Pg,$Qg);$gf=max($gf,$Pg,$Qg);}echo"<div class='references' id='refl$Le' style='left: $Le"."em; top: $sf"."em; padding: .5em 0;'><div style='border-right: 1px solid gray; margin-top: 1px; height: ".($gf-$sf)."em;'></div></div>\n";}}}echo'</div>
<p class="links"><a href="',h(ME."schema=".urlencode($ca)),'" id="schema-link">固定链接</a>
';}elseif(isset($_GET["dump"])){$a=$_GET["dump"];if($_POST&&!$l){save_settings(array_intersect_key($_POST,array_flip(array("output","format","db_style","types","routines","events","table_style","auto_increment","triggers","data_style"))),"adminer_export");$T=array_flip((array)$_POST["tables"])+array_flip((array)$_POST["data"]);$Oc=dump_headers((count($T)==1?key($T):DB),(DB==""||count($T)>1));$xe=preg_match('~sql~',$_POST["format"]);if($xe){echo"-- Adminer ".VERSION." ".get_driver(DRIVER)." ".str_replace("\n"," ",connection()->server_info)." dump\n\n";if(JUSH=="sql"){echo"SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
".($_POST["data_style"]?"SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';
":"")."
";connection()->query("SET time_zone = '+00:00'");connection()->query("SET sql_mode = ''");}}$ti=$_POST["db_style"];$i=array(DB);if(DB==""){$i=$_POST["databases"];if(is_string($i))$i=explode("\n",rtrim(str_replace("\r","",$i),"\n"));}foreach((array)$i
as$j){adminer()->dumpDatabase($j);if(connection()->select_db($j)){if($xe){if($ti)echo
use_sql($j,$ti).";\n\n";$qg="";if($_POST["types"]){foreach(types()as$t=>$U){$Cc=type_values($t);if($Cc)$qg
.=($ti!='DROP+CREATE'?"DROP TYPE IF EXISTS ".idf_escape($U).";;\n":"")."CREATE TYPE ".idf_escape($U)." AS ENUM ($Cc);\n\n";else$qg
.="-- Could not export type $U\n\n";}}if($_POST["routines"]){foreach(routines()as$K){$B=$K["ROUTINE_NAME"];$Ah=$K["ROUTINE_TYPE"];$h=create_routine($Ah,array("name"=>$B)+routine($K["SPECIFIC_NAME"],$Ah));set_utf8mb4($h);$qg
.=($ti!='DROP+CREATE'?"DROP $Ah IF EXISTS ".idf_escape($B).";;\n":"")."$h;\n\n";}}if($_POST["events"]){foreach(get_rows("SHOW EVENTS",null,"-- ")as$K){$h=remove_definer(get_val("SHOW CREATE EVENT ".idf_escape($K["Name"]),3));set_utf8mb4($h);$qg
.=($ti!='DROP+CREATE'?"DROP EVENT IF EXISTS ".idf_escape($K["Name"]).";;\n":"")."$h;;\n\n";}}echo($qg&&JUSH=='sql'?"DELIMITER ;;\n\n$qg"."DELIMITER ;\n\n":$qg);}if($_POST["table_style"]||$_POST["data_style"]){$Nj=array();foreach(table_status('',true)as$B=>$S){$R=(DB==""||in_array($B,(array)$_POST["tables"]));$Mb=(DB==""||in_array($B,(array)$_POST["data"]));if($R||$Mb){$Xi=null;if($Oc=="tar"){$Xi=new
TmpFile;ob_start(array($Xi,'write'),1e5);}adminer()->dumpTable($B,($R?$_POST["table_style"]:""),(is_view($S)?2:0));if(is_view($S))$Nj[]=$B;elseif($Mb){$n=fields($B);adminer()->dumpData($B,$_POST["data_style"],"SELECT *".convert_fields($n,$n)." FROM ".table($B));}if($xe&&$_POST["triggers"]&&$R&&($lj=trigger_sql($B)))echo"\nDELIMITER ;;\n$lj\nDELIMITER ;\n";if($Oc=="tar"){ob_end_flush();tar_file((DB!=""?"":"$j/")."$B.csv",$Xi);}elseif($xe)echo"\n";}}if(function_exists('Adminer\foreign_keys_sql')){foreach(table_status('',true)as$B=>$S){$R=(DB==""||in_array($B,(array)$_POST["tables"]));if($R&&!is_view($S))echo
foreign_keys_sql($B);}}foreach($Nj
as$Mj)adminer()->dumpTable($Mj,$_POST["table_style"],1);if($Oc=="tar")echo
pack("x512");}}}adminer()->dumpFooter();exit;}page_header('导出',$l,($_GET["export"]!=""?array("table"=>$_GET["export"]):array()),h(DB));echo'
<form action="" method="post">
<table class="layout">
';$Qb=array('','USE','DROP+CREATE','CREATE');$Ei=array('','DROP+CREATE','CREATE');$Nb=array('','TRUNCATE+INSERT','INSERT');if(JUSH=="sql")$Nb[]='INSERT+UPDATE';$K=get_settings("adminer_export");if(!$K)$K=array("output"=>"text","format"=>"sql","db_style"=>(DB!=""?"":"CREATE"),"table_style"=>"DROP+CREATE","data_style"=>"INSERT");if(!isset($K["events"])){$K["routines"]=$K["events"]=($_GET["dump"]=="");$K["triggers"]=$K["table_style"];}echo"<tr><th>".'输出'."<td>".html_radios("output",adminer()->dumpOutput(),$K["output"])."\n","<tr><th>".'格式'."<td>".html_radios("format",adminer()->dumpFormat(),$K["format"])."\n",(JUSH=="sqlite"?"":"<tr><th>".'数据库'."<td>".html_select('db_style',$Qb,$K["db_style"]).(support("type")?checkbox("types",1,$K["types"],'用户类型'):"").(support("routine")?checkbox("routines",1,$K["routines"],'子程序'):"").(support("event")?checkbox("events",1,$K["events"],'事件'):"")),"<tr><th>".'表'."<td>".html_select('table_style',$Ei,$K["table_style"]).checkbox("auto_increment",1,$K["auto_increment"],'自动增量').(support("trigger")?checkbox("triggers",1,$K["triggers"],'触发器'):""),"<tr><th>".'数据'."<td>".html_select('data_style',$Nb,$K["data_style"]),'</table>
<p><input type="submit" value="导出">
',input_token(),'
<table>
',script("qsl('table').onclick = dumpClick;");$Ug=array();if(DB!=""){$Za=($a!=""?"":" checked");echo"<thead><tr>","<th style='text-align: left;'><label class='block'><input type='checkbox' id='check-tables'$Za>".'表'."</label>".script("qs('#check-tables').onclick = partial(formCheck, /^tables\\[/);",""),"<th style='text-align: right;'><label class='block'>".'数据'."<input type='checkbox' id='check-data'$Za></label>".script("qs('#check-data').onclick = partial(formCheck, /^data\\[/);",""),"</thead>\n";$Nj="";$Gi=tables_list();foreach($Gi
as$B=>$U){$Tg=preg_replace('~_.*~','',$B);$Za=($a==""||$a==(substr($a,-1)=="%"?"$Tg%":$B));$Xg="<tr><td>".checkbox("tables[]",$B,$Za,$B,"","block");if($U!==null&&!preg_match('~table~i',$U))$Nj
.="$Xg\n";else
echo"$Xg<td align='right'><label class='block'><span id='Rows-".h($B)."'></span>".checkbox("data[]",$B,$Za)."</label>\n";$Ug[$Tg]++;}echo$Nj;if($Gi)echo
script("ajaxSetHtml('".js_escape(ME)."script=db');");}else{echo"<thead><tr><th style='text-align: left;'>","<label class='block'><input type='checkbox' id='check-databases'".($a==""?" checked":"").">".'数据库'."</label>",script("qs('#check-databases').onclick = partial(formCheck, /^databases\\[/);",""),"</thead>\n";$i=adminer()->databases();if($i){foreach($i
as$j){if(!information_schema($j)){$Tg=preg_replace('~_.*~','',$j);echo"<tr><td>".checkbox("databases[]",$j,$a==""||$a=="$Tg%",$j,"","block")."\n";$Ug[$Tg]++;}}}else
echo"<tr><td><textarea name='databases' rows='10' cols='20'></textarea>";}echo'</table>
</form>
';$cd=true;foreach($Ug
as$x=>$X){if($x!=""&&$X>1){echo($cd?"<p>":" ")."<a href='".h(ME)."dump=".urlencode("$x%")."'>".h($x)."</a>";$cd=false;}}}elseif(isset($_GET["privileges"])){page_header('权限');echo'<p class="links"><a href="'.h(ME).'user=">'.'创建用户'."</a>";$I=connection()->query("SELECT User, Host FROM mysql.".(DB==""?"user":"db WHERE ".q(DB)." LIKE Db")." ORDER BY Host, User");$vd=$I;if(!$I)$I=connection()->query("SELECT SUBSTRING_INDEX(CURRENT_USER, '@', 1) AS User, SUBSTRING_INDEX(CURRENT_USER, '@', -1) AS Host");echo"<form action=''><p>\n";hidden_fields_get();echo
input_hidden("db",DB),($vd?"":input_hidden("grant")),"<table class='odds'>\n","<thead><tr><th>".'用户名'."<th>".'服务器'."<th></thead>\n";while($K=$I->fetch_assoc())echo'<tr><td>'.h($K["User"])."<td>".h($K["Host"]).'<td><a href="'.h(ME.'user='.urlencode($K["User"]).'&host='.urlencode($K["Host"])).'">'.'编辑'."</a>\n";if(!$vd||DB!="")echo"<tr><td><input name='user' autocapitalize='off'><td><input name='host' value='localhost' autocapitalize='off'><td><input type='submit' value='".'编辑'."'>\n";echo"</table>\n","</form>\n";}elseif(isset($_GET["sql"])){if(!$l&&$_POST["export"]){save_settings(array("output"=>$_POST["output"],"format"=>$_POST["format"]),"adminer_import");dump_headers("sql");if($_POST["format"]=="sql")echo"$_POST[query]\n";else{adminer()->dumpTable("","");adminer()->dumpData("","table",$_POST["query"]);adminer()->dumpFooter();}exit;}restart_session();$Ld=&get_session("queries");$Kd=&$Ld[DB];if(!$l&&$_POST["clear"]){$Kd=array();redirect(remove_from_uri("history"));}stop_session();page_header((isset($_GET["import"])?'导入':'SQL命令'),$l);$Re='--'.(JUSH=='sql'?' ':'');if(!$l&&$_POST){$q=false;if(!isset($_GET["import"]))$H=$_POST["query"];elseif($_POST["webfile"]){$ki=adminer()->importServerPath();$q=@fopen((file_exists($ki)?$ki:"compress.zlib://$ki.gz"),"rb");$H=($q?fread($q,1e6):false);}else$H=get_file("sql_file",true,";");if(is_string($H)){if(function_exists('memory_get_usage')&&($lf=ini_bytes("memory_limit"))!="-1")@ini_set("memory_limit",max($lf,strval(2*strlen($H)+memory_get_usage()+8e6)));if($H!=""&&strlen($H)<1e6){$eh=$H.(preg_match("~;[ \t\r\n]*\$~",$H)?"":";");if(!$Kd||first(end($Kd))!=$eh){restart_session();$Kd[]=array($eh,time());set_session("queries",$Ld);stop_session();}}$hi="(?:\\s|/\\*[\s\S]*?\\*/|(?:#|$Re)[^\n]*\n?|--\r?\n)";$Yb=";";$C=0;$xc=true;$g=connect();if($g&&DB!=""){$g->select_db(DB);if($_GET["ns"]!="")set_schema($_GET["ns"],$g);}$nb=0;$Ec=array();$xg='[\'"'.(JUSH=="sql"?'`#':(JUSH=="sqlite"?'`[':(JUSH=="mssql"?'[':''))).']|/\*|'.$Re.'|$'.(JUSH=="pgsql"?'|\$([a-zA-Z]\w*)?\$':'');$bj=microtime(true);$ma=get_settings("adminer_import");while($H!=""){if(!$C&&preg_match("~^$hi*+DELIMITER\\s+(\\S+)~i",$H,$A)){$Yb=preg_quote($A[1]);$H=substr($H,strlen($A[0]));}elseif(!$C&&JUSH=='pgsql'&&preg_match("~^($hi*+COPY\\s+)[^;]+\\s+FROM\\s+stdin;~i",$H,$A)){$Yb="\n\\\\\\.\r?\n";$C=strlen($A[0]);}else{preg_match("($Yb\\s*|$xg)",$H,$A,PREG_OFFSET_CAPTURE,$C);list($od,$Og)=$A[0];if(!$od&&$q&&!feof($q))$H
.=fread($q,1e5);else{if(!$od&&rtrim($H)=="")break;$C=$Og+strlen($od);if($od&&!preg_match("(^$Yb)",$od)){$Ra=driver()->hasCStyleEscapes()||(JUSH=="pgsql"&&($Og>0&&strtolower($H[$Og-1])=="e"));$Hg=($od=='/*'?'\*/':($od=='['?']':(preg_match("~^$Re|^#~",$od)?"\n":preg_quote($od).($Ra?'|\\\\.':''))));while(preg_match("($Hg|\$)s",$H,$A,PREG_OFFSET_CAPTURE,$C)){$Eh=$A[0][0];if(!$Eh&&$q&&!feof($q))$H
.=fread($q,1e5);else{$C=$A[0][1]+strlen($Eh);if(!$Eh||$Eh[0]!="\\")break;}}}else{$xc=false;$eh=substr($H,0,$Og+($Yb[0]=="\n"?3:0));$nb++;$Xg="<pre id='sql-$nb'><code class='jush-".JUSH."'>".adminer()->sqlCommandQuery($eh)."</code></pre>\n";if(JUSH=="sqlite"&&preg_match("~^$hi*+ATTACH\\b~i",$eh,$A)){echo$Xg,"<p class='error'>".'不支持ATTACH查询。'."\n";$Ec[]=" <a href='#sql-$nb'>$nb</a>";if($_POST["error_stops"])break;}else{if(!$_POST["only_errors"]){echo$Xg;ob_flush();flush();}$pi=microtime(true);if(connection()->multi_query($eh)&&$g&&preg_match("~^$hi*+USE\\b~i",$eh))$g->query($eh);do{$I=connection()->store_result();if(connection()->error){echo($_POST["only_errors"]?$Xg:""),"<p class='error'>".'查询出错'.(connection()->errno?" (".connection()->errno.")":"").": ".error()."\n";$Ec[]=" <a href='#sql-$nb'>$nb</a>";if($_POST["error_stops"])break
2;}else{$Qi=" <span class='time'>(".format_time($pi).")</span>".(strlen($eh)<1000?" <a href='".h(ME)."sql=".urlencode(trim($eh))."'>".'编辑'."</a>":"");$oa=connection()->affected_rows;$Qj=($_POST["only_errors"]?"":driver()->warnings());$Rj="warnings-$nb";if($Qj)$Qi
.=", <a href='#$Rj'>".'警告'."</a>".script("qsl('a').onclick = partial(toggle, '$Rj');","");$Mc=null;$ig=null;$Nc="explain-$nb";if(is_object($I)){$z=$_POST["limit"];$ig=print_select_result($I,$g,array(),$z);if(!$_POST["only_errors"]){echo"<form action='' method='post'>\n";$Jf=$I->num_rows;echo"<p class='sql-footer'>".($Jf?($z&&$Jf>$z?sprintf('%d / ',$z):"").sprintf('%d 行',$Jf):""),$Qi;if($g&&preg_match("~^($hi|\\()*+SELECT\\b~i",$eh)&&($Mc=explain($g,$eh)))echo", <a href='#$Nc'>Explain</a>".script("qsl('a').onclick = partial(toggle, '$Nc');","");$t="export-$nb";echo", <a href='#$t'>".'导出'."</a>".script("qsl('a').onclick = partial(toggle, '$t');","")."<span id='$t' class='hidden'>: ".html_select("output",adminer()->dumpOutput(),$ma["output"])." ".html_select("format",adminer()->dumpFormat(),$ma["format"]).input_hidden("query",$eh)."<input type='submit' name='export' value='".'导出'."'>".input_token()."</span>\n"."</form>\n";}}else{if(preg_match("~^$hi*+(CREATE|DROP|ALTER)$hi++(DATABASE|SCHEMA)\\b~i",$eh)){restart_session();set_session("dbs",null);stop_session();}if(!$_POST["only_errors"])echo"<p class='message' title='".h(connection()->info)."'>".sprintf('查询执行完毕，%d 行受影响。',$oa)."$Qi\n";}echo($Qj?"<div id='$Rj' class='hidden'>\n$Qj</div>\n":"");if($Mc){echo"<div id='$Nc' class='hidden explain'>\n";print_select_result($Mc,$g,$ig);echo"</div>\n";}}$pi=microtime(true);}while(connection()->next_result());}$H=substr($H,$C);$C=0;}}}}if($xc)echo"<p class='message'>".'没有命令被执行。'."\n";elseif($_POST["only_errors"])echo"<p class='message'>".sprintf('%d 条查询已成功执行。',$nb-count($Ec))," <span class='time'>(".format_time($bj).")</span>\n";elseif($Ec&&$nb>1)echo"<p class='error'>".'查询出错'.": ".implode("",$Ec)."\n";}else
echo"<p class='error'>".upload_error($H)."\n";}echo'
<form action="" method="post" enctype="multipart/form-data" id="form">
';$Kc="<input type='submit' value='".'执行'."' title='Ctrl+Enter'>";if(!isset($_GET["import"])){$eh=$_GET["sql"];if($_POST)$eh=$_POST["query"];elseif($_GET["history"]=="all")$eh=$Kd;elseif($_GET["history"]!="")$eh=idx($Kd[$_GET["history"]],0);echo"<p>";textarea("query",$eh,20);echo
script(($_POST?"":"qs('textarea').focus();\n")."qs('#form').onsubmit = partial(sqlSubmit, qs('#form'), '".js_escape(remove_from_uri("sql|limit|error_stops|only_errors|history"))."');"),"<p>";adminer()->sqlPrintAfter();echo"$Kc\n",'限制行数'.": <input type='number' name='limit' class='size' value='".h($_POST?$_POST["limit"]:$_GET["limit"])."'>\n";}else{$Ad=(extension_loaded("zlib")?"[.gz]":"");echo"<fieldset><legend>".'文件上传'."</legend><div>",file_input("SQL$Ad: <input type='file' name='sql_file[]' multiple>\n$Kc"),"</div></fieldset>\n";$Wd=adminer()->importServerPath();if($Wd)echo"<fieldset><legend>".'来自服务器'."</legend><div>",sprintf('Web服务器文件 %s',"<code>".h($Wd)."$Ad</code>"),' <input type="submit" name="webfile" value="'.'运行文件'.'">',"</div></fieldset>\n";echo"<p>";}echo
checkbox("error_stops",1,($_POST?$_POST["error_stops"]:isset($_GET["import"])||$_GET["error_stops"]),'出错时停止')."\n",checkbox("only_errors",1,($_POST?$_POST["only_errors"]:isset($_GET["import"])||$_GET["only_errors"]),'仅显示错误')."\n",input_token();if(!isset($_GET["import"])&&$Kd){print_fieldset("history",'历史',$_GET["history"]!="");for($X=end($Kd);$X;$X=prev($Kd)){$x=key($Kd);list($eh,$Qi,$sc)=$X;echo'<a href="'.h(ME."sql=&history=$x").'">'.'编辑'."</a>"." <span class='time' title='".@date('Y-m-d',$Qi)."'>".@date("H:i:s",$Qi)."</span>"." <code class='jush-".JUSH."'>".shorten_utf8(ltrim(str_replace("\n"," ",str_replace("\r","",preg_replace("~^(#|$Re).*~m",'',$eh)))),80,"</code>").($sc?" <span class='time'>($sc)</span>":"")."<br>\n";}echo"<input type='submit' name='clear' value='".'清除'."'>\n","<a href='".h(ME."sql=&history=all")."'>".'编辑全部'."</a>\n","</div></fieldset>\n";}echo'</form>
';}elseif(isset($_GET["edit"])){$a=$_GET["edit"];$n=fields($a);$Z=(isset($_GET["select"])?($_POST["check"]&&count($_POST["check"])==1?where_check($_POST["check"][0],$n):""):where($_GET,$n));$wj=(isset($_GET["select"])?$_POST["edit"]:$Z);foreach($n
as$B=>$m){if(!isset($m["privileges"][$wj?"update":"insert"])||adminer()->fieldName($m)==""||$m["generated"])unset($n[$B]);}if($_POST&&!$l&&!isset($_GET["select"])){$Te=$_POST["referer"];if($_POST["insert"])$Te=($wj?null:$_SERVER["REQUEST_URI"]);elseif(!preg_match('~^.+&select=.+$~',$Te))$Te=ME."select=".urlencode($a);$w=indexes($a);$rj=unique_array($_GET["where"],$w);$hh="\nWHERE $Z";if(isset($_POST["delete"]))queries_redirect($Te,'已删除项目。',driver()->delete($a,$hh,$rj?0:1));else{$O=array();foreach($n
as$B=>$m){$X=process_input($m);if($X!==false&&$X!==null)$O[idf_escape($B)]=$X;}if($wj){if(!$O)redirect($Te);queries_redirect($Te,'已更新项目。',driver()->update($a,$O,$hh,$rj?0:1));if(is_ajax()){page_headers();page_messages($l);exit;}}else{$I=driver()->insert($a,$O);$Ke=($I?last_id($I):0);queries_redirect($Te,sprintf('已插入项目%s。',($Ke?" $Ke":"")),$I);}}}$K=null;if($_POST["save"])$K=(array)$_POST["fields"];elseif($Z){$M=array();foreach($n
as$B=>$m){if(isset($m["privileges"]["select"])){$wa=($_POST["clone"]&&$m["auto_increment"]?"''":convert_field($m));$M[]=($wa?"$wa AS ":"").idf_escape($B);}}$K=array();if(!support("table"))$M=array("*");if($M){$I=driver()->select($a,$M,array($Z),$M,array(),(isset($_GET["select"])?2:1));if(!$I)$l=error();else{$K=$I->fetch_assoc();if(!$K)$K=false;}if(isset($_GET["select"])&&(!$K||$I->fetch_assoc()))$K=null;}}if(!support("table")&&!$n){if(!$Z){$I=driver()->select($a,array("*"),array(),array("*"));$K=($I?$I->fetch_assoc():false);if(!$K)$K=array(driver()->primary=>"");}if($K){foreach($K
as$x=>$X){if(!$Z)$K[$x]=null;$n[$x]=array("field"=>$x,"null"=>($x!=driver()->primary),"auto_increment"=>($x==driver()->primary));}}}edit_form($a,$n,$K,$wj,$l);}elseif(isset($_GET["create"])){$a=$_GET["create"];$Bg=driver()->partitionBy;$Eg=($Bg?driver()->partitionsInfo($a):array());$nh=referencable_primary($a);$md=array();foreach($nh
as$Ai=>$m)$md[str_replace("`","``",$Ai)."`".str_replace("`","``",$m["field"])]=$Ai;$lg=array();$S=array();if($a!=""){$lg=fields($a);$S=table_status1($a);if(count($S)<2)$l='没有表。';}$K=$_POST;$K["fields"]=(array)$K["fields"];if($K["auto_increment_col"])$K["fields"][$K["auto_increment_col"]]["auto_increment"]=true;if($_POST)save_settings(array("comments"=>$_POST["comments"],"defaults"=>$_POST["defaults"]));if($_POST&&!process_fields($K["fields"])&&!$l){if($_POST["drop"])queries_redirect(substr(ME,0,-1),'已删除表。',drop_tables(array($a)));else{$n=array();$sa=array();$Bj=false;$kd=array();$kg=reset($lg);$qa=" FIRST";foreach($K["fields"]as$x=>$m){$p=$md[$m["type"]];$mj=($p!==null?$nh[$p]:$m);if($m["field"]!=""){if(!$m["generated"])$m["default"]=null;$ch=process_field($m,$mj);$sa[]=array($m["orig"],$ch,$qa);if(!$kg||$ch!==process_field($kg,$kg)){$n[]=array($m["orig"],$ch,$qa);if($m["orig"]!=""||$qa)$Bj=true;}if($p!==null)$kd[idf_escape($m["field"])]=($a!=""&&JUSH!="sqlite"?"ADD":" ").format_foreign_key(array('table'=>$md[$m["type"]],'source'=>array($m["field"]),'target'=>array($mj["field"]),'on_delete'=>$m["on_delete"],));$qa=" AFTER ".idf_escape($m["field"]);}elseif($m["orig"]!=""){$Bj=true;$n[]=array($m["orig"]);}if($m["orig"]!=""){$kg=next($lg);if(!$kg)$qa="";}}$E=array();if(in_array($K["partition_by"],$Bg)){foreach($K
as$x=>$X){if(preg_match('~^partition~',$x))$E[$x]=$X;}foreach($E["partition_names"]as$x=>$B){if($B==""){unset($E["partition_names"][$x]);unset($E["partition_values"][$x]);}}$E["partition_names"]=array_values($E["partition_names"]);$E["partition_values"]=array_values($E["partition_values"]);if($E==$Eg)$E=array();}elseif(preg_match("~partitioned~",$S["Create_options"]))$E=null;$mf='已修改表。';if($a==""){cookie("adminer_engine",$K["Engine"]);$mf='已创建表。';}$B=trim($K["name"]);queries_redirect(ME.(support("table")?"table=":"select=").urlencode($B),$mf,alter_table($a,$B,(JUSH=="sqlite"&&($Bj||$kd)?$sa:$n),$kd,($K["Comment"]!=$S["Comment"]?$K["Comment"]:null),($K["Engine"]&&$K["Engine"]!=$S["Engine"]?$K["Engine"]:""),($K["Collation"]&&$K["Collation"]!=$S["Collation"]?$K["Collation"]:""),($K["Auto_increment"]!=""?number($K["Auto_increment"]):""),$E));}}page_header(($a!=""?'修改表':'创建表'),$l,array("table"=>$a),h($a));if(!$_POST){$nj=driver()->types();$K=array("Engine"=>$_COOKIE["adminer_engine"],"fields"=>array(array("field"=>"","type"=>(isset($nj["int"])?"int":(isset($nj["integer"])?"integer":"")),"on_update"=>"")),"partition_names"=>array(""),);if($a!=""){$K=$S;$K["name"]=$a;$K["fields"]=array();if(!$_GET["auto_increment"])$K["Auto_increment"]="";foreach($lg
as$m){$m["generated"]=$m["generated"]?:(isset($m["default"])?"DEFAULT":"");$K["fields"][]=$m;}if($Bg){$K+=$Eg;$K["partition_names"][]="";$K["partition_values"][]="";}}}$jb=collations();if(is_array(reset($jb)))$jb=call_user_func_array('array_merge',array_values($jb));$zc=driver()->engines();foreach($zc
as$yc){if(!strcasecmp($yc,$K["Engine"])){$K["Engine"]=$yc;break;}}echo'
<form action="" method="post" id="form">
<p>
';if(support("columns")||$a==""){echo'表名'.": <input name='name'".($a==""&&!$_POST?" autofocus":"")." data-maxlength='64' value='".h($K["name"])."' autocapitalize='off'>\n",($zc?html_select("Engine",array(""=>"(".'引擎'.")")+$zc,$K["Engine"]).on_help("event.target.value",1).script("qsl('select').onchange = helpClose;")."\n":"");if($jb)echo"<datalist id='collations'>".optionlist($jb)."</datalist>\n",(preg_match("~sqlite|mssql~",JUSH)?"":"<input list='collations' name='Collation' value='".h($K["Collation"])."' placeholder='(".'校对'.")'>\n");echo"<input type='submit' value='".'保存'."'>\n";}if(support("columns")){echo"<div class='scrollable'>\n","<table id='edit-fields' class='nowrap'>\n";edit_fields($K["fields"],$jb,"TABLE",$md);echo"</table>\n",script("editFields();"),"</div>\n<p>\n",'自动增量'.": <input type='number' name='Auto_increment' class='size' value='".h($K["Auto_increment"])."'>\n",checkbox("defaults",1,($_POST?$_POST["defaults"]:get_setting("defaults")),'默认值',"columnShow(this.checked, 5)","jsonly");$rb=($_POST?$_POST["comments"]:get_setting("comments"));echo(support("comment")?checkbox("comments",1,$rb,'注释',"editingCommentsClick(this, true);","jsonly").' '.(preg_match('~\n~',$K["Comment"])?"<textarea name='Comment' rows='2' cols='20'".($rb?"":" class='hidden'").">".h($K["Comment"])."</textarea>":'<input name="Comment" value="'.h($K["Comment"]).'" data-maxlength="'.(min_version(5.5)?2048:60).'"'.($rb?"":" class='hidden'").'>'):''),'<p>
<input type="submit" value="保存">
';}echo'
';if($a!="")echo'<input type="submit" name="drop" value="删除">',confirm(sprintf('删除 %s?',$a));if($Bg&&(JUSH=='sql'||$a=="")){$Cg=preg_match('~RANGE|LIST~',$K["partition_by"]);print_fieldset("partition",'分区类型',$K["partition_by"]);echo"<p>".html_select("partition_by",array_merge(array(""),$Bg),$K["partition_by"]).on_help("event.target.value.replace(/./, 'PARTITION BY \$&')",1).script("qsl('select').onchange = partitionByChange;"),"(<input name='partition' value='".h($K["partition"])."'>)\n",'分区'.": <input type='number' name='partitions' class='size".($Cg||!$K["partition_by"]?" hidden":"")."' value='".h($K["partitions"])."'>\n","<table id='partition-table'".($Cg?"":" class='hidden'").">\n","<thead><tr><th>".'分区名'."<th>".'值'."</thead>\n";foreach($K["partition_names"]as$x=>$X)echo'<tr>','<td><input name="partition_names[]" value="'.h($X).'" autocapitalize="off">',($x==count($K["partition_names"])-1?script("qsl('input').oninput = partitionNameChange;"):''),'<td><input name="partition_values[]" value="'.h(idx($K["partition_values"],$x)).'">';echo"</table>\n</div></fieldset>\n";}echo
input_token(),'</form>
';}elseif(isset($_GET["indexes"])){$a=$_GET["indexes"];$ee=array("PRIMARY","UNIQUE","INDEX");$S=table_status1($a,true);$be=driver()->indexAlgorithms($S);if(preg_match('~MyISAM|M?aria'.(min_version(5.6,'10.0.5')?'|InnoDB':'').'~i',$S["Engine"]))$ee[]="FULLTEXT";if(preg_match('~MyISAM|M?aria'.(min_version(5.7,'10.2.2')?'|InnoDB':'').'~i',$S["Engine"]))$ee[]="SPATIAL";$w=indexes($a);$n=fields($a);$G=array();if(JUSH=="mongo"){$G=$w["_id_"];unset($ee[0]);unset($w["_id_"]);}$K=$_POST;if($K)save_settings(array("index_options"=>$K["options"]));if($_POST&&!$l&&!$_POST["add"]&&!$_POST["drop_col"]){$b=array();foreach($K["indexes"]as$v){$B=$v["name"];if(in_array($v["type"],$ee)){$e=array();$Pe=array();$bc=array();$ce=(support("partial_indexes")?$v["partial"]:"");$ae=(in_array($v["algorithm"],$be)?$v["algorithm"]:"");$O=array();ksort($v["columns"]);foreach($v["columns"]as$x=>$d){if($d!=""){$y=idx($v["lengths"],$x);$Zb=idx($v["descs"],$x);$O[]=($n[$d]?idf_escape($d):$d).($y?"(".(+$y).")":"").($Zb?" DESC":"");$e[]=$d;$Pe[]=($y?:null);$bc[]=$Zb;}}$Lc=$w[$B];if($Lc){ksort($Lc["columns"]);ksort($Lc["lengths"]);ksort($Lc["descs"]);if($v["type"]==$Lc["type"]&&array_values($Lc["columns"])===$e&&(!$Lc["lengths"]||array_values($Lc["lengths"])===$Pe)&&array_values($Lc["descs"])===$bc&&$Lc["partial"]==$ce&&(!$be||$Lc["algorithm"]==$ae)){unset($w[$B]);continue;}}if($e)$b[]=array($v["type"],$B,$O,$ae,$ce);}}foreach($w
as$B=>$Lc)$b[]=array($Lc["type"],$B,"DROP");if(!$b)redirect(ME."table=".urlencode($a));queries_redirect(ME."table=".urlencode($a),'已修改索引。',alter_indexes($a,$b));}page_header('索引',$l,array("table"=>$a),h($a));$Zc=array_keys($n);if($_POST["add"]){foreach($K["indexes"]as$x=>$v){if($v["columns"][count($v["columns"])]!="")$K["indexes"][$x]["columns"][]="";}$v=end($K["indexes"]);if($v["type"]||array_filter($v["columns"],'strlen'))$K["indexes"][]=array("columns"=>array(1=>""));}if(!$K){foreach($w
as$x=>$v){$w[$x]["name"]=$x;$w[$x]["columns"][]="";}$w[]=array("columns"=>array(1=>""));$K["indexes"]=$w;}$Pe=(JUSH=="sql"||JUSH=="mssql");$bi=($_POST?$_POST["options"]:get_setting("index_options"));echo'
<form action="" method="post">
<div class="scrollable">
<table class="nowrap">
<thead><tr>
<th id="label-type">索引类型
';$Ud=" class='idxopts".($bi?"":" hidden")."'";if($be)echo"<th id='label-algorithm'$Ud>".'Algorithm'.doc_link(array('sql'=>'create-index.html#create-index-storage-engine-index-types','mariadb'=>'storage-engine-index-types/','pgsql'=>'indexes-types.html',));echo'<th><input type="submit" class="wayoff">','Columns'.($Pe?"<span$Ud> (".'length'.")</span>":"");if($Pe||support("descidx"))echo
checkbox("options",1,$bi,'选项',"indexOptionsShow(this.checked)","jsonly")."\n";echo'<th id="label-name">名称
';if(support("partial_indexes"))echo"<th id='label-condition'$Ud>".'Condition';echo'<th><noscript>',icon("plus","add[0]","+",'下一行插入'),'</noscript>
</thead>
';if($G){echo"<tr><td>PRIMARY<td>";foreach($G["columns"]as$x=>$d)echo
select_input(" disabled",$Zc,$d),"<label><input disabled type='checkbox'>".'降序'."</label> ";echo"<td><td>\n";}$_e=1;foreach($K["indexes"]as$v){if(!$_POST["drop_col"]||$_e!=key($_POST["drop_col"])){echo"<tr><td>".html_select("indexes[$_e][type]",array(-1=>"")+$ee,$v["type"],($_e==count($K["indexes"])?"indexesAddRow.call(this);":""),"label-type");if($be)echo"<td$Ud>".html_select("indexes[$_e][algorithm]",array_merge(array(""),$be),$v['algorithm'],"label-algorithm");echo"<td>";ksort($v["columns"]);$s=1;foreach($v["columns"]as$x=>$d){echo"<span>".select_input(" name='indexes[$_e][columns][$s]' title='".'列'."'",($n&&($d==""||$n[$d])?array_combine($Zc,$Zc):array()),$d,"partial(".($s==count($v["columns"])?"indexesAddColumn":"indexesChangeColumn").", '".js_escape(JUSH=="sql"?"":$_GET["indexes"]."_")."')"),"<span$Ud>",($Pe?"<input type='number' name='indexes[$_e][lengths][$s]' class='size' value='".h(idx($v["lengths"],$x))."' title='".'长度'."'>":""),(support("descidx")?checkbox("indexes[$_e][descs][$s]",1,idx($v["descs"],$x),'降序'):""),"</span> </span>";$s++;}echo"<td><input name='indexes[$_e][name]' value='".h($v["name"])."' autocapitalize='off' aria-labelledby='label-name'>\n";if(support("partial_indexes"))echo"<td$Ud><input name='indexes[$_e][partial]' value='".h($v["partial"])."' autocapitalize='off' aria-labelledby='label-condition'>\n";echo"<td>".icon("cross","drop_col[$_e]","x",'移除').script("qsl('button').onclick = partial(editingRemoveRow, 'indexes\$1[type]');");}$_e++;}echo'</table>
</div>
<p>
<input type="submit" value="保存">
',input_token(),'</form>
';}elseif(isset($_GET["database"])){$K=$_POST;if($_POST&&!$l&&!$_POST["add"]){$B=trim($K["name"]);if($_POST["drop"]){$_GET["db"]="";queries_redirect(remove_from_uri("db|database"),'已删除数据库。',drop_databases(array(DB)));}elseif(DB!==$B){if(DB!=""){$_GET["db"]=$B;queries_redirect(preg_replace('~\bdb=[^&]*&~','',ME)."db=".urlencode($B),'已重命名数据库。',rename_database($B,$K["collation"]));}else{$i=explode("\n",str_replace("\r","",$B));$ui=true;$Je="";foreach($i
as$j){if(count($i)==1||$j!=""){if(!create_database($j,$K["collation"]))$ui=false;$Je=$j;}}restart_session();set_session("dbs",null);queries_redirect(ME."db=".urlencode($Je),'已创建数据库。',$ui);}}else{if(!$K["collation"])redirect(substr(ME,0,-1));query_redirect("ALTER DATABASE ".idf_escape($B).(preg_match('~^[a-z0-9_]+$~i',$K["collation"])?" COLLATE $K[collation]":""),substr(ME,0,-1),'已修改数据库。');}}page_header(DB!=""?'修改数据库':'创建数据库',$l,array(),h(DB));$jb=collations();$B=DB;if($_POST)$B=$K["name"];elseif(DB!="")$K["collation"]=db_collation(DB,$jb);elseif(JUSH=="sql"){foreach(get_vals("SHOW GRANTS")as$vd){if(preg_match('~ ON (`(([^\\\\`]|``|\\\\.)*)%`\.\*)?~',$vd,$A)&&$A[1]){$B=stripcslashes(idf_unescape("`$A[2]`"));break;}}}echo'
<form action="" method="post">
<p>
',($_POST["add"]||strpos($B,"\n")?'<textarea autofocus name="name" rows="10" cols="40">'.h($B).'</textarea><br>':'<input name="name" autofocus value="'.h($B).'" data-maxlength="64" autocapitalize="off">')."\n".($jb?html_select("collation",array(""=>"(".'校对'.")")+$jb,$K["collation"]).doc_link(array('sql'=>"charset-charsets.html",'mariadb'=>"supported-character-sets-and-collations/",'mssql'=>"relational-databases/system-functions/sys-fn-helpcollations-transact-sql",)):""),'<input type="submit" value="保存">
';if(DB!="")echo"<input type='submit' name='drop' value='".'删除'."'>".confirm(sprintf('删除 %s?',DB))."\n";elseif(!$_POST["add"]&&$_GET["db"]=="")echo
icon("plus","add[0]","+",'下一行插入')."\n";echo
input_token(),'</form>
';}elseif(isset($_GET["scheme"])){$K=$_POST;if($_POST&&!$l){$_=preg_replace('~ns=[^&]*&~','',ME)."ns=";if($_POST["drop"])query_redirect("DROP SCHEMA ".idf_escape($_GET["ns"]),$_,'已删除模式。');else{$B=trim($K["name"]);$_
.=urlencode($B);if($_GET["ns"]=="")query_redirect("CREATE SCHEMA ".idf_escape($B),$_,'已创建模式。');elseif($_GET["ns"]!=$B)query_redirect("ALTER SCHEMA ".idf_escape($_GET["ns"])." RENAME TO ".idf_escape($B),$_,'已修改模式。');else
redirect($_);}}page_header($_GET["ns"]!=""?'修改模式':'创建模式',$l);if(!$K)$K["name"]=$_GET["ns"];echo'
<form action="" method="post">
<p><input name="name" autofocus value="',h($K["name"]),'" autocapitalize="off">
<input type="submit" value="保存">
';if($_GET["ns"]!="")echo"<input type='submit' name='drop' value='".'删除'."'>".confirm(sprintf('删除 %s?',$_GET["ns"]))."\n";echo
input_token(),'</form>
';}elseif(isset($_GET["call"])){$ba=($_GET["name"]?:$_GET["call"]);page_header('调用'.": ".h($ba),$l);$Ah=routine($_GET["call"],(isset($_GET["callf"])?"FUNCTION":"PROCEDURE"));$Xd=array();$qg=array();foreach($Ah["fields"]as$s=>$m){if(substr($m["inout"],-3)=="OUT"&&JUSH=='sql')$qg[$s]="@".idf_escape($m["field"])." AS ".idf_escape($m["field"]);if(!$m["inout"]||substr($m["inout"],0,2)=="IN")$Xd[]=$s;}if(!$l&&$_POST){$Sa=array();foreach($Ah["fields"]as$x=>$m){$X="";if(in_array($x,$Xd)){$X=process_input($m);if($X===false)$X="''";if(isset($qg[$x]))connection()->query("SET @".idf_escape($m["field"])." = $X");}if(isset($qg[$x]))$Sa[]="@".idf_escape($m["field"]);elseif(in_array($x,$Xd))$Sa[]=$X;}$H=(isset($_GET["callf"])?"SELECT ":"CALL ").(idx($Ah["returns"],"type")=="record"?"* FROM ":"").table($ba)."(".implode(", ",$Sa).")";$pi=microtime(true);$I=connection()->multi_query($H);$oa=connection()->affected_rows;echo
adminer()->selectQuery($H,$pi,!$I);if(!$I)echo"<p class='error'>".error()."\n";else{$g=connect();if($g)$g->select_db(DB);do{$I=connection()->store_result();if(is_object($I))print_select_result($I,$g);else
echo"<p class='message'>".sprintf('子程序被调用，%d 行被影响。',$oa)." <span class='time'>".@date("H:i:s")."</span>\n";}while(connection()->next_result());if($qg)print_select_result(connection()->query("SELECT ".implode(", ",$qg)));}}echo'
<form action="" method="post">
';if($Xd){echo"<table class='layout'>\n";foreach($Xd
as$x){$m=$Ah["fields"][$x];$B=$m["field"];echo"<tr><th>".adminer()->fieldName($m);$Y=idx($_POST["fields"],$B);if($Y!=""){if($m["type"]=="set")$Y=implode(",",$Y);}input($m,$Y,idx($_POST["function"],$B,""));echo"\n";}echo"</table>\n";}echo'<p>
<input type="submit" value="调用">
',input_token(),'</form>

<pre>
';function
pre_tr($Eh){return
preg_replace('~^~m','<tr>',preg_replace('~\|~','<td>',preg_replace('~\|$~m',"",rtrim($Eh))));}$R='(\+--[-+]+\+\n)';$K='(\| .* \|\n)';echo
preg_replace_callback("~^$R?$K$R?($K*)$R?~m",function($A){$dd=pre_tr($A[2]);return"<table>\n".($A[1]?"<thead>$dd</thead>\n":$dd).pre_tr($A[4])."\n</table>";},preg_replace('~(\n(    -|mysql)&gt; )(.+)~',"\\1<code class='jush-sql'>\\3</code>",preg_replace('~(.+)\n---+\n~',"<b>\\1</b>\n",h($Ah['comment']))));echo'</pre>
';}elseif(isset($_GET["foreign"])){$a=$_GET["foreign"];$B=$_GET["name"];$K=$_POST;if($_POST&&!$l&&!$_POST["add"]&&!$_POST["change"]&&!$_POST["change-js"]){if(!$_POST["drop"]){$K["source"]=array_filter($K["source"],'strlen');ksort($K["source"]);$Ji=array();foreach($K["source"]as$x=>$X)$Ji[$x]=$K["target"][$x];$K["target"]=$Ji;}if(JUSH=="sqlite")$I=recreate_table($a,$a,array(),array(),array(" $B"=>($K["drop"]?"":" ".format_foreign_key($K))));else{$b="ALTER TABLE ".table($a);$I=($B==""||queries("$b DROP ".(JUSH=="sql"?"FOREIGN KEY ":"CONSTRAINT ").idf_escape($B)));if(!$K["drop"])$I=queries("$b ADD".format_foreign_key($K));}queries_redirect(ME."table=".urlencode($a),($K["drop"]?'已删除外键。':($B!=""?'已修改外键。':'已创建外键。')),$I);if(!$K["drop"])$l='源列和目标列必须具有相同的数据类型，在目标列上必须有一个索引并且引用的数据必须存在。';}page_header('外键',$l,array("table"=>$a),h($a));if($_POST){ksort($K["source"]);if($_POST["add"])$K["source"][]="";elseif($_POST["change"]||$_POST["change-js"])$K["target"]=array();}elseif($B!=""){$md=foreign_keys($a);$K=$md[$B];$K["source"][]="";}else{$K["table"]=$a;$K["source"]=array("");}echo'
<form action="" method="post">
';$gi=array_keys(fields($a));if($K["db"]!="")connection()->select_db($K["db"]);if($K["ns"]!=""){$mg=get_schema();set_schema($K["ns"]);}$mh=array_keys(array_filter(table_status('',true),'Adminer\fk_support'));$Ji=array_keys(fields(in_array($K["table"],$mh)?$K["table"]:reset($mh)));$Wf="this.form['change-js'].value = '1'; this.form.submit();";echo"<p><label>".'目标表'.": ".html_select("table",$mh,$K["table"],$Wf)."</label>\n";if(support("scheme")){$Hh=array_filter(adminer()->schemas(),function($Gh){return!preg_match('~^information_schema$~i',$Gh);});echo"<label>".'模式'.": ".html_select("ns",$Hh,$K["ns"]!=""?$K["ns"]:$_GET["ns"],$Wf)."</label>";if($K["ns"]!="")set_schema($mg);}elseif(JUSH!="sqlite"){$Rb=array();foreach(adminer()->databases()as$j){if(!information_schema($j))$Rb[]=$j;}echo"<label>".'数据库'.": ".html_select("db",$Rb,$K["db"]!=""?$K["db"]:$_GET["db"],$Wf)."</label>";}echo
input_hidden("change-js"),'<noscript><p><input type="submit" name="change" value="修改"></noscript>
<table>
<thead><tr><th id="label-source">源<th id="label-target">目标</thead>
';$_e=0;foreach($K["source"]as$x=>$X){echo"<tr>","<td>".html_select("source[".(+$x)."]",array(-1=>"")+$gi,$X,($_e==count($K["source"])-1?"foreignAddRow.call(this);":""),"label-source"),"<td>".html_select("target[".(+$x)."]",$Ji,idx($K["target"],$x),"","label-target");$_e++;}echo'</table>
<p>
<label>ON DELETE: ',html_select("on_delete",array(-1=>"")+explode("|",driver()->onActions),$K["on_delete"]),'</label>
<label>ON UPDATE: ',html_select("on_update",array(-1=>"")+explode("|",driver()->onActions),$K["on_update"]),'</label>
',doc_link(array('sql'=>"innodb-foreign-key-constraints.html",'mariadb'=>"foreign-keys/",'pgsql'=>"sql-createtable.html#SQL-CREATETABLE-REFERENCES",'mssql'=>"t-sql/statements/create-table-transact-sql",'oracle'=>"SQLRF01111",)),'<p>
<input type="submit" value="保存">
<noscript><p><input type="submit" name="add" value="增加列"></noscript>
';if($B!="")echo'<input type="submit" name="drop" value="删除">',confirm(sprintf('删除 %s?',$B));echo
input_token(),'</form>
';}elseif(isset($_GET["view"])){$a=$_GET["view"];$K=$_POST;$ng="VIEW";if(JUSH=="pgsql"&&$a!=""){$P=table_status1($a);$ng=strtoupper($P["Engine"]);}if($_POST&&!$l){$B=trim($K["name"]);$wa=" AS\n$K[select]";$Te=ME."table=".urlencode($B);$mf='已修改视图。';$U=($_POST["materialized"]?"MATERIALIZED VIEW":"VIEW");if(!$_POST["drop"]&&$a==$B&&JUSH!="sqlite"&&$U=="VIEW"&&$ng=="VIEW")query_redirect((JUSH=="mssql"?"ALTER":"CREATE OR REPLACE")." VIEW ".table($B).$wa,$Te,$mf);else{$Li=$B."_adminer_".uniqid();drop_create("DROP $ng ".table($a),"CREATE $U ".table($B).$wa,"DROP $U ".table($B),"CREATE $U ".table($Li).$wa,"DROP $U ".table($Li),($_POST["drop"]?substr(ME,0,-1):$Te),'已删除视图。',$mf,'已创建视图。',$a,$B);}}if(!$_POST&&$a!=""){$K=view($a);$K["name"]=$a;$K["materialized"]=($ng!="VIEW");if(!$l)$l=error();}page_header(($a!=""?'修改视图':'创建视图'),$l,array("table"=>$a),h($a));echo'
<form action="" method="post">
<p>名称: <input name="name" value="',h($K["name"]),'" data-maxlength="64" autocapitalize="off">
',(support("materializedview")?" ".checkbox("materialized",1,$K["materialized"],'物化视图'):""),'<p>';textarea("select",$K["select"]);echo'<p>
<input type="submit" value="保存">
';if($a!="")echo'<input type="submit" name="drop" value="删除">',confirm(sprintf('删除 %s?',$a));echo
input_token(),'</form>
';}elseif(isset($_GET["event"])){$aa=$_GET["event"];$re=array("YEAR","QUARTER","MONTH","DAY","HOUR","MINUTE","WEEK","SECOND","YEAR_MONTH","DAY_HOUR","DAY_MINUTE","DAY_SECOND","HOUR_MINUTE","HOUR_SECOND","MINUTE_SECOND");$qi=array("ENABLED"=>"ENABLE","DISABLED"=>"DISABLE","SLAVESIDE_DISABLED"=>"DISABLE ON SLAVE");$K=$_POST;if($_POST&&!$l){if($_POST["drop"])query_redirect("DROP EVENT ".idf_escape($aa),substr(ME,0,-1),'已删除事件。');elseif(in_array($K["INTERVAL_FIELD"],$re)&&isset($qi[$K["STATUS"]])){$Fh="\nON SCHEDULE ".($K["INTERVAL_VALUE"]?"EVERY ".q($K["INTERVAL_VALUE"])." $K[INTERVAL_FIELD]".($K["STARTS"]?" STARTS ".q($K["STARTS"]):"").($K["ENDS"]?" ENDS ".q($K["ENDS"]):""):"AT ".q($K["STARTS"]))." ON COMPLETION".($K["ON_COMPLETION"]?"":" NOT")." PRESERVE";queries_redirect(substr(ME,0,-1),($aa!=""?'已修改事件。':'已创建事件。'),queries(($aa!=""?"ALTER EVENT ".idf_escape($aa).$Fh.($aa!=$K["EVENT_NAME"]?"\nRENAME TO ".idf_escape($K["EVENT_NAME"]):""):"CREATE EVENT ".idf_escape($K["EVENT_NAME"]).$Fh)."\n".$qi[$K["STATUS"]]." COMMENT ".q($K["EVENT_COMMENT"]).rtrim(" DO\n$K[EVENT_DEFINITION]",";").";"));}}page_header(($aa!=""?'修改事件'.": ".h($aa):'创建事件'),$l);if(!$K&&$aa!=""){$L=get_rows("SELECT * FROM information_schema.EVENTS WHERE EVENT_SCHEMA = ".q(DB)." AND EVENT_NAME = ".q($aa));$K=reset($L);}echo'
<form action="" method="post">
<table class="layout">
<tr><th>名称<td><input name="EVENT_NAME" value="',h($K["EVENT_NAME"]),'" data-maxlength="64" autocapitalize="off">
<tr><th title="datetime">开始<td><input name="STARTS" value="',h("$K[EXECUTE_AT]$K[STARTS]"),'">
<tr><th title="datetime">结束<td><input name="ENDS" value="',h($K["ENDS"]),'">
<tr><th>每<td><input type="number" name="INTERVAL_VALUE" value="',h($K["INTERVAL_VALUE"]),'" class="size"> ',html_select("INTERVAL_FIELD",$re,$K["INTERVAL_FIELD"]),'<tr><th>状态<td>',html_select("STATUS",$qi,$K["STATUS"]),'<tr><th>注释<td><input name="EVENT_COMMENT" value="',h($K["EVENT_COMMENT"]),'" data-maxlength="64">
<tr><th><td>',checkbox("ON_COMPLETION","PRESERVE",$K["ON_COMPLETION"]=="PRESERVE",'完成后仍保留'),'</table>
<p>';textarea("EVENT_DEFINITION",$K["EVENT_DEFINITION"]);echo'<p>
<input type="submit" value="保存">
';if($aa!="")echo'<input type="submit" name="drop" value="删除">',confirm(sprintf('删除 %s?',$aa));echo
input_token(),'</form>
';}elseif(isset($_GET["procedure"])){$ba=($_GET["name"]?:$_GET["procedure"]);$Ah=(isset($_GET["function"])?"FUNCTION":"PROCEDURE");$K=$_POST;$K["fields"]=(array)$K["fields"];if($_POST&&!process_fields($K["fields"])&&!$l){$jg=routine($_GET["procedure"],$Ah);$Li="$K[name]_adminer_".uniqid();foreach($K["fields"]as$x=>$m){if($m["field"]=="")unset($K["fields"][$x]);}drop_create("DROP $Ah ".routine_id($ba,$jg),create_routine($Ah,$K),"DROP $Ah ".routine_id($K["name"],$K),create_routine($Ah,array("name"=>$Li)+$K),"DROP $Ah ".routine_id($Li,$K),substr(ME,0,-1),'已删除子程序。','已修改子程序。','已创建子程序。',$ba,$K["name"]);}page_header(($ba!=""?(isset($_GET["function"])?'修改函数':'修改过程').": ".h($ba):(isset($_GET["function"])?'创建函数':'创建过程')),$l);if(!$_POST){if($ba=="")$K["language"]="sql";else{$K=routine($_GET["procedure"],$Ah);$K["name"]=$ba;}}$jb=get_vals("SHOW CHARACTER SET");sort($jb);$Bh=routine_languages();echo($jb?"<datalist id='collations'>".optionlist($jb)."</datalist>":""),'
<form action="" method="post" id="form">
<p>名称: <input name="name" value="',h($K["name"]),'" data-maxlength="64" autocapitalize="off">
',($Bh?"<label>".'语言'.": ".html_select("language",$Bh,$K["language"])."</label>\n":""),'<input type="submit" value="保存">
<div class="scrollable">
<table class="nowrap">
';edit_fields($K["fields"],$jb,$Ah);if(isset($_GET["function"])){echo"<tr><td>".'返回类型';edit_type("returns",(array)$K["returns"],$jb,array(),(JUSH=="pgsql"?array("void","trigger"):array()));}echo'</table>
',script("editFields();"),'</div>
<p>';textarea("definition",$K["definition"],20);echo'<p>
<input type="submit" value="保存">
';if($ba!="")echo'<input type="submit" name="drop" value="删除">',confirm(sprintf('删除 %s?',$ba));echo
input_token(),'</form>
';}elseif(isset($_GET["sequence"])){$da=$_GET["sequence"];$K=$_POST;if($_POST&&!$l){$_=substr(ME,0,-1);$B=trim($K["name"]);if($_POST["drop"])query_redirect("DROP SEQUENCE ".idf_escape($da),$_,'已删除序列。');elseif($da=="")query_redirect("CREATE SEQUENCE ".idf_escape($B),$_,'已创建序列。');elseif($da!=$B)query_redirect("ALTER SEQUENCE ".idf_escape($da)." RENAME TO ".idf_escape($B),$_,'已修改序列。');else
redirect($_);}page_header($da!=""?'修改序列'.": ".h($da):'创建序列',$l);if(!$K)$K["name"]=$da;echo'
<form action="" method="post">
<p><input name="name" value="',h($K["name"]),'" autocapitalize="off">
<input type="submit" value="保存">
';if($da!="")echo"<input type='submit' name='drop' value='".'删除'."'>".confirm(sprintf('删除 %s?',$da))."\n";echo
input_token(),'</form>
';}elseif(isset($_GET["type"])){$ea=$_GET["type"];$K=$_POST;if($_POST&&!$l){$_=substr(ME,0,-1);if($_POST["drop"])query_redirect("DROP TYPE ".idf_escape($ea),$_,'已删除类型。');else
query_redirect("CREATE TYPE ".idf_escape(trim($K["name"]))." $K[as]",$_,'已创建类型。');}page_header($ea!=""?'修改类型'.": ".h($ea):'创建类型',$l);if(!$K)$K["as"]="AS ";echo'
<form action="" method="post">
<p>
';if($ea!=""){$nj=driver()->types();$Cc=type_values($nj[$ea]);if($Cc)echo"<code class='jush-".JUSH."'>ENUM (".h($Cc).")</code>\n<p>";echo"<input type='submit' name='drop' value='".'删除'."'>".confirm(sprintf('删除 %s?',$ea))."\n";}else{echo'名称'.": <input name='name' value='".h($K['name'])."' autocapitalize='off'>\n",doc_link(array('pgsql'=>"datatype-enum.html",),"?");textarea("as",$K["as"]);echo"<p><input type='submit' value='".'保存'."'>\n";}echo
input_token(),'</form>
';}elseif(isset($_GET["check"])){$a=$_GET["check"];$B=$_GET["name"];$K=$_POST;if($K&&!$l){if(JUSH=="sqlite")$I=recreate_table($a,$a,array(),array(),array(),"",array(),"$B",($K["drop"]?"":$K["clause"]));else{$I=($B==""||queries("ALTER TABLE ".table($a)." DROP CONSTRAINT ".idf_escape($B)));if(!$K["drop"])$I=queries("ALTER TABLE ".table($a)." ADD".($K["name"]!=""?" CONSTRAINT ".idf_escape($K["name"]):"")." CHECK ($K[clause])");}queries_redirect(ME."table=".urlencode($a),($K["drop"]?'Check has been dropped.':($B!=""?'Check has been altered.':'Check has been created.')),$I);}page_header(($B!=""?'Alter check'.": ".h($B):'Create check'),$l,array("table"=>$a));if(!$K){$ab=driver()->checkConstraints($a);$K=array("name"=>$B,"clause"=>$ab[$B]);}echo'
<form action="" method="post">
<p>';if(JUSH!="sqlite")echo'名称'.': <input name="name" value="'.h($K["name"]).'" data-maxlength="64" autocapitalize="off"> ';echo
doc_link(array('sql'=>"create-table-check-constraints.html",'mariadb'=>"constraint/",'pgsql'=>"ddl-constraints.html#DDL-CONSTRAINTS-CHECK-CONSTRAINTS",'mssql'=>"relational-databases/tables/create-check-constraints",'sqlite'=>"lang_createtable.html#check_constraints",),"?"),'<p>';textarea("clause",$K["clause"]);echo'<p><input type="submit" value="保存">
';if($B!="")echo'<input type="submit" name="drop" value="删除">',confirm(sprintf('删除 %s?',$B));echo
input_token(),'</form>
';}elseif(isset($_GET["trigger"])){$a=$_GET["trigger"];$B="$_GET[name]";$kj=trigger_options();$K=(array)trigger($B,$a)+array("Trigger"=>$a."_bi");if($_POST){if(!$l&&in_array($_POST["Timing"],$kj["Timing"])&&in_array($_POST["Event"],$kj["Event"])&&in_array($_POST["Type"],$kj["Type"])){$Tf=" ON ".table($a);$jc="DROP TRIGGER ".idf_escape($B).(JUSH=="pgsql"?$Tf:"");$Te=ME."table=".urlencode($a);if($_POST["drop"])query_redirect($jc,$Te,'已删除触发器。');else{if($B!="")queries($jc);queries_redirect($Te,($B!=""?'已修改触发器。':'已创建触发器。'),queries(create_trigger($Tf,$_POST)));if($B!="")queries(create_trigger($Tf,$K+array("Type"=>reset($kj["Type"]))));}}$K=$_POST;}page_header(($B!=""?'修改触发器'.": ".h($B):'创建触发器'),$l,array("table"=>$a));echo'
<form action="" method="post" id="form">
<table class="layout">
<tr><th>时间<td>',html_select("Timing",$kj["Timing"],$K["Timing"],"triggerChange(/^".preg_quote($a,"/")."_[ba][iud]$/, '".js_escape($a)."', this.form);"),'<tr><th>事件<td>',html_select("Event",$kj["Event"],$K["Event"],"this.form['Timing'].onchange();"),(in_array("UPDATE OF",$kj["Event"])?" <input name='Of' value='".h($K["Of"])."' class='hidden'>":""),'<tr><th>类型<td>',html_select("Type",$kj["Type"],$K["Type"]),'</table>
<p>名称: <input name="Trigger" value="',h($K["Trigger"]),'" data-maxlength="64" autocapitalize="off">
',script("qs('#form')['Timing'].onchange();"),'<p>';textarea("Statement",$K["Statement"]);echo'<p>
<input type="submit" value="保存">
';if($B!="")echo'<input type="submit" name="drop" value="删除">',confirm(sprintf('删除 %s?',$B));echo
input_token(),'</form>
';}elseif(isset($_GET["user"])){$fa=$_GET["user"];$ah=array(""=>array("All privileges"=>""));foreach(get_rows("SHOW PRIVILEGES")as$K){foreach(explode(",",($K["Privilege"]=="Grant option"?"":$K["Context"]))as$Ab)$ah[$Ab][$K["Privilege"]]=$K["Comment"];}$ah["Server Admin"]+=$ah["File access on server"];$ah["Databases"]["Create routine"]=$ah["Procedures"]["Create routine"];unset($ah["Procedures"]["Create routine"]);$ah["Columns"]=array();foreach(array("Select","Insert","Update","References")as$X)$ah["Columns"][$X]=$ah["Tables"][$X];unset($ah["Server Admin"]["Usage"]);foreach($ah["Tables"]as$x=>$X)unset($ah["Databases"][$x]);$Bf=array();if($_POST){foreach($_POST["objects"]as$x=>$X)$Bf[$X]=(array)$Bf[$X]+idx($_POST["grants"],$x,array());}$wd=array();$Rf="";if(isset($_GET["host"])&&($I=connection()->query("SHOW GRANTS FOR ".q($fa)."@".q($_GET["host"])))){while($K=$I->fetch_row()){if(preg_match('~GRANT (.*) ON (.*) TO ~',$K[0],$A)&&preg_match_all('~ *([^(,]*[^ ,(])( *\([^)]+\))?~',$A[1],$af,PREG_SET_ORDER)){foreach($af
as$X){if($X[1]!="USAGE")$wd["$A[2]$X[2]"][$X[1]]=true;if(preg_match('~ WITH GRANT OPTION~',$K[0]))$wd["$A[2]$X[2]"]["GRANT OPTION"]=true;}}if(preg_match("~ IDENTIFIED BY PASSWORD '([^']+)~",$K[0],$A))$Rf=$A[1];}}if($_POST&&!$l){$Sf=(isset($_GET["host"])?q($fa)."@".q($_GET["host"]):"''");if($_POST["drop"])query_redirect("DROP USER $Sf",ME."privileges=",'已删除用户。');else{$Df=q($_POST["user"])."@".q($_POST["host"]);$Fg=$_POST["pass"];if($Fg!=''&&!$_POST["hashed"]&&!min_version(8)){$Fg=get_val("SELECT PASSWORD(".q($Fg).")");$l=!$Fg;}$Fb=false;if(!$l){if($Sf!=$Df){$Fb=queries((min_version(5)?"CREATE USER":"GRANT USAGE ON *.* TO")." $Df IDENTIFIED BY ".(min_version(8)?"":"PASSWORD ").q($Fg));$l=!$Fb;}elseif($Fg!=$Rf)queries("SET PASSWORD FOR $Df = ".q($Fg));}if(!$l){$yh=array();foreach($Bf
as$Lf=>$vd){if(isset($_GET["grant"]))$vd=array_filter($vd);$vd=array_keys($vd);if(isset($_GET["grant"]))$yh=array_diff(array_keys(array_filter($Bf[$Lf],'strlen')),$vd);elseif($Sf==$Df){$Pf=array_keys((array)$wd[$Lf]);$yh=array_diff($Pf,$vd);$vd=array_diff($vd,$Pf);unset($wd[$Lf]);}if(preg_match('~^(.+)\s*(\(.*\))?$~U',$Lf,$A)&&(!grant("REVOKE",$yh,$A[2]," ON $A[1] FROM $Df")||!grant("GRANT",$vd,$A[2]," ON $A[1] TO $Df"))){$l=true;break;}}}if(!$l&&isset($_GET["host"])){if($Sf!=$Df)queries("DROP USER $Sf");elseif(!isset($_GET["grant"])){foreach($wd
as$Lf=>$yh){if(preg_match('~^(.+)(\(.*\))?$~U',$Lf,$A))grant("REVOKE",array_keys($yh),$A[2]," ON $A[1] FROM $Df");}}}queries_redirect(ME."privileges=",(isset($_GET["host"])?'已修改用户。':'已创建用户。'),!$l);if($Fb)connection()->query("DROP USER $Df");}}page_header((isset($_GET["host"])?'用户名'.": ".h("$fa@$_GET[host]"):'创建用户'),$l,array("privileges"=>array('','权限')));$K=$_POST;if($K)$wd=$Bf;else{$K=$_GET+array("host"=>get_val("SELECT SUBSTRING_INDEX(CURRENT_USER, '@', -1)"));$K["pass"]=$Rf;if($Rf!="")$K["hashed"]=true;$wd[(DB==""||$wd?"":idf_escape(addcslashes(DB,"%_\\"))).".*"]=array();}echo'<form action="" method="post">
<table class="layout">
<tr><th>服务器<td><input name="host" data-maxlength="60" value="',h($K["host"]),'" autocapitalize="off">
<tr><th>用户名<td><input name="user" data-maxlength="80" value="',h($K["user"]),'" autocapitalize="off">
<tr><th>密码<td><input name="pass" id="pass" value="',h($K["pass"]),'" autocomplete="new-password">
',($K["hashed"]?"":script("typePassword(qs('#pass'));")),(min_version(8)?"":checkbox("hashed",1,$K["hashed"],'Hashed',"typePassword(this.form['pass'], this.checked);")),'</table>

',"<table class='odds'>\n","<thead><tr><th colspan='2'>".'权限'.doc_link(array('sql'=>"grant.html#priv_level"));$s=0;foreach($wd
as$Lf=>$vd){echo'<th>'.($Lf!="*.*"?"<input name='objects[$s]' value='".h($Lf)."' size='10' autocapitalize='off'>":input_hidden("objects[$s]","*.*")."*.*");$s++;}echo"</thead>\n";foreach(array(""=>"","Server Admin"=>'服务器',"Databases"=>'数据库',"Tables"=>'表',"Columns"=>'列',"Procedures"=>'子程序',)as$Ab=>$Zb){foreach((array)$ah[$Ab]as$Zg=>$ob){echo"<tr><td".($Zb?">$Zb<td":" colspan='2'").' lang="en" title="'.h($ob).'">'.h($Zg);$s=0;foreach($wd
as$Lf=>$vd){$B="'grants[$s][".h(strtoupper($Zg))."]'";$Y=$vd[strtoupper($Zg)];if($Ab=="Server Admin"&&$Lf!=(isset($wd["*.*"])?"*.*":".*"))echo"<td>";elseif(isset($_GET["grant"]))echo"<td><select name=$B><option><option value='1'".($Y?" selected":"").">".'授权'."<option value='0'".($Y=="0"?" selected":"").">".'废除'."</select>";else
echo"<td align='center'><label class='block'>","<input type='checkbox' name=$B value='1'".($Y?" checked":"").($Zg=="All privileges"?" id='grants-$s-all'>":">".($Zg=="Grant option"?"":script("qsl('input').onclick = function () { if (this.checked) formUncheck('grants-$s-all'); };"))),"</label>";$s++;}}}echo"</table>\n",'<p>
<input type="submit" value="保存">
';if(isset($_GET["host"]))echo'<input type="submit" name="drop" value="删除">',confirm(sprintf('删除 %s?',"$fa@$_GET[host]"));echo
input_token(),'</form>
';}elseif(isset($_GET["processlist"])){if(support("kill")){if($_POST&&!$l){$Fe=0;foreach((array)$_POST["kill"]as$X){if(adminer()->killProcess($X))$Fe++;}queries_redirect(ME."processlist=",sprintf('%d 个进程被终止。',$Fe),$Fe||!$_POST["kill"]);}}page_header('进程列表',$l);echo'
<form action="" method="post">
<div class="scrollable">
<table class="nowrap checkable odds">
',script("mixin(qsl('table'), {onclick: tableClick, ondblclick: partialArg(tableClick, true)});");$s=-1;foreach(adminer()->processList()as$s=>$K){if(!$s){echo"<thead><tr lang='en'>".(support("kill")?"<th>":"");foreach($K
as$x=>$X)echo"<th>$x".doc_link(array('sql'=>"show-processlist.html#processlist_".strtolower($x),'pgsql'=>"monitoring-stats.html#PG-STAT-ACTIVITY-VIEW",'oracle'=>"REFRN30223",));echo"</thead>\n";}echo"<tr>".(support("kill")?"<td>".checkbox("kill[]",$K[JUSH=="sql"?"Id":"pid"],0):"");foreach($K
as$x=>$X)echo"<td>".((JUSH=="sql"&&$x=="Info"&&preg_match("~Query|Killed~",$K["Command"])&&$X!="")||(JUSH=="pgsql"&&$x=="current_query"&&$X!="<IDLE>")||(JUSH=="oracle"&&$x=="sql_text"&&$X!="")?"<code class='jush-".JUSH."'>".shorten_utf8($X,100,"</code>").' <a href="'.h(ME.($K["db"]!=""?"db=".urlencode($K["db"])."&":"")."sql=".urlencode($X)).'">'.'复制'.'</a>':h($X));echo"\n";}echo'</table>
</div>
<p>
';if(support("kill"))echo($s+1)."/".sprintf('共计 %d',max_connections()),"<p><input type='submit' value='".'终止'."'>\n";echo
input_token(),'</form>
',script("tableCheck();");}elseif(isset($_GET["select"])){$a=$_GET["select"];$S=table_status1($a);$w=indexes($a);$n=fields($a);$md=column_foreign_keys($a);$Nf=$S["Oid"];$na=get_settings("adminer_import");$zh=array();$e=array();$Mh=array();$fg=array();$Pi="";foreach($n
as$x=>$m){$B=adminer()->fieldName($m);$_f=html_entity_decode(strip_tags($B),ENT_QUOTES);if(isset($m["privileges"]["select"])&&$B!=""){$e[$x]=$_f;if(is_shortable($m))$Pi=adminer()->selectLengthProcess();}if(isset($m["privileges"]["where"])&&$B!="")$Mh[$x]=$_f;if(isset($m["privileges"]["order"])&&$B!="")$fg[$x]=$_f;$zh+=$m["privileges"];}list($M,$xd)=adminer()->selectColumnsProcess($e,$w);$M=array_unique($M);$xd=array_unique($xd);$ve=count($xd)<count($M);$Z=adminer()->selectSearchProcess($n,$w);$eg=adminer()->selectOrderProcess($n,$w);$z=adminer()->selectLimitProcess();if($_GET["val"]&&is_ajax()){header("Content-Type: text/plain; charset=utf-8");foreach($_GET["val"]as$sj=>$K){$wa=convert_field($n[key($K)]);$M=array($wa?:idf_escape(key($K)));$Z[]=where_check($sj,$n);$J=driver()->select($a,$M,$Z,$M);if($J)echo
first($J->fetch_row());}exit;}$G=$uj=array();foreach($w
as$v){if($v["type"]=="PRIMARY"){$G=array_flip($v["columns"]);$uj=($M?$G:array());foreach($uj
as$x=>$X){if(in_array(idf_escape($x),$M))unset($uj[$x]);}break;}}if($Nf&&!$G){$G=$uj=array($Nf=>0);$w[]=array("type"=>"PRIMARY","columns"=>array($Nf));}if($_POST&&!$l){$Tj=$Z;if(!$_POST["all"]&&is_array($_POST["check"])){$ab=array();foreach($_POST["check"]as$Wa)$ab[]=where_check($Wa,$n);$Tj[]="((".implode(") OR (",$ab)."))";}$Tj=($Tj?"\nWHERE ".implode(" AND ",$Tj):"");if($_POST["export"]){save_settings(array("output"=>$_POST["output"],"format"=>$_POST["format"]),"adminer_import");dump_headers($a);adminer()->dumpTable($a,"");$qd=($M?implode(", ",$M):"*").convert_fields($e,$n,$M)."\nFROM ".table($a);$zd=($xd&&$ve?"\nGROUP BY ".implode(", ",$xd):"").($eg?"\nORDER BY ".implode(", ",$eg):"");$H="SELECT $qd$Tj$zd";if(is_array($_POST["check"])&&!$G){$qj=array();foreach($_POST["check"]as$X)$qj[]="(SELECT".limit($qd,"\nWHERE ".($Z?implode(" AND ",$Z)." AND ":"").where_check($X,$n).$zd,1).")";$H=implode(" UNION ALL ",$qj);}adminer()->dumpData($a,"table",$H);adminer()->dumpFooter();exit;}if(!adminer()->selectEmailProcess($Z,$md)){if($_POST["save"]||$_POST["delete"]){$I=true;$oa=0;$O=array();if(!$_POST["delete"]){foreach($_POST["fields"]as$B=>$X){$X=process_input($n[$B]);if($X!==null&&($_POST["clone"]||$X!==false))$O[idf_escape($B)]=($X!==false?$X:idf_escape($B));}}if($_POST["delete"]||$O){$H=($_POST["clone"]?"INTO ".table($a)." (".implode(", ",array_keys($O)).")\nSELECT ".implode(", ",$O)."\nFROM ".table($a):"");if($_POST["all"]||($G&&is_array($_POST["check"]))||$ve){$I=($_POST["delete"]?driver()->delete($a,$Tj):($_POST["clone"]?queries("INSERT $H$Tj".driver()->insertReturning($a)):driver()->update($a,$O,$Tj)));$oa=connection()->affected_rows;if(is_object($I))$oa+=$I->num_rows;}else{foreach((array)$_POST["check"]as$X){$Sj="\nWHERE ".($Z?implode(" AND ",$Z)." AND ":"").where_check($X,$n);$I=($_POST["delete"]?driver()->delete($a,$Sj,1):($_POST["clone"]?queries("INSERT".limit1($a,$H,$Sj)):driver()->update($a,$O,$Sj,1)));if(!$I)break;$oa+=connection()->affected_rows;}}}$mf=sprintf('%d 个项目受到影响。',$oa);if($_POST["clone"]&&$I&&$oa==1){$Ke=last_id($I);if($Ke)$mf=sprintf('已插入项目%s。'," $Ke");}queries_redirect(remove_from_uri($_POST["all"]&&$_POST["delete"]?"page":""),$mf,$I);if(!$_POST["delete"]){$Rg=(array)$_POST["fields"];edit_form($a,array_intersect_key($n,$Rg),$Rg,!$_POST["clone"],$l);page_footer();exit;}}elseif(!$_POST["import"]){if(!$_POST["val"])$l='按住Ctrl并单击某个值进行修改。';else{$I=true;$oa=0;foreach($_POST["val"]as$sj=>$K){$O=array();foreach($K
as$x=>$X){$x=bracket_escape($x,true);$O[idf_escape($x)]=(preg_match('~char|text~',$n[$x]["type"])||$X!=""?adminer()->processInput($n[$x],$X):"NULL");}$I=driver()->update($a,$O," WHERE ".($Z?implode(" AND ",$Z)." AND ":"").where_check($sj,$n),($ve||$G?0:1)," ");if(!$I)break;$oa+=connection()->affected_rows;}queries_redirect(remove_from_uri(),sprintf('%d 个项目受到影响。',$oa),$I);}}elseif(!is_string($ad=get_file("csv_file",true)))$l=upload_error($ad);elseif(!preg_match('~~u',$ad))$l='文件必须使用UTF-8编码。';else{save_settings(array("output"=>$na["output"],"format"=>$_POST["separator"]),"adminer_import");$I=true;$kb=array_keys($n);preg_match_all('~(?>"[^"]*"|[^"\r\n]+)+~',$ad,$af);$oa=count($af[0]);driver()->begin();$Sh=($_POST["separator"]=="csv"?",":($_POST["separator"]=="tsv"?"\t":";"));$L=array();foreach($af[0]as$x=>$X){preg_match_all("~((?>\"[^\"]*\")+|[^$Sh]*)$Sh~",$X.$Sh,$bf);if(!$x&&!array_diff($bf[1],$kb)){$kb=$bf[1];$oa--;}else{$O=array();foreach($bf[1]as$s=>$hb)$O[idf_escape($kb[$s])]=($hb==""&&$n[$kb[$s]]["null"]?"NULL":q(preg_match('~^".*"$~s',$hb)?str_replace('""','"',substr($hb,1,-1)):$hb));$L[]=$O;}}$I=(!$L||driver()->insertUpdate($a,$L,$G));if($I)driver()->commit();queries_redirect(remove_from_uri("page"),sprintf('%d 行已导入。',$oa),$I);driver()->rollback();}}}$Ai=adminer()->tableName($S);if(is_ajax()){page_headers();ob_start();}else
page_header('选择'.": $Ai",$l);$O=null;if(isset($zh["insert"])||!support("table")){$wg=array();foreach((array)$_GET["where"]as$X){if(isset($md[$X["col"]])&&count($md[$X["col"]])==1&&($X["op"]=="="||(!$X["op"]&&(is_array($X["val"])||!preg_match('~[_%]~',$X["val"])))))$wg["set"."[".bracket_escape($X["col"])."]"]=$X["val"];}$O=$wg?"&".http_build_query($wg):"";}adminer()->selectLinks($S,$O);if(!$e&&support("table"))echo"<p class='error'>".'不能选择该表'.($n?".":": ".error())."\n";else{echo"<form action='' id='form'>\n","<div style='display: none;'>";hidden_fields_get();echo(DB!=""?input_hidden("db",DB).(isset($_GET["ns"])?input_hidden("ns",$_GET["ns"]):""):""),input_hidden("select",$a),"</div>\n";adminer()->selectColumnsPrint($M,$e);adminer()->selectSearchPrint($Z,$Mh,$w);adminer()->selectOrderPrint($eg,$fg,$w);adminer()->selectLimitPrint($z);adminer()->selectLengthPrint($Pi);adminer()->selectActionPrint($w);echo"</form>\n";$D=$_GET["page"];$pd=null;if($D=="last"){$pd=get_val(count_rows($a,$Z,$ve,$xd));$D=floor(max(0,intval($pd)-1)/$z);}$Nh=$M;$yd=$xd;if(!$Nh){$Nh[]="*";$Bb=convert_fields($e,$n,$M);if($Bb)$Nh[]=substr($Bb,2);}foreach($M
as$x=>$X){$m=$n[idf_unescape($X)];if($m&&($wa=convert_field($m)))$Nh[$x]="$wa AS $X";}if(!$ve&&$uj){foreach($uj
as$x=>$X){$Nh[]=idf_escape($x);if($yd)$yd[]=idf_escape($x);}}$I=driver()->select($a,$Nh,$Z,$yd,$eg,$z,$D,true);if(!$I)echo"<p class='error'>".error()."\n";else{if(JUSH=="mssql"&&$D)$I->seek($z*$D);$wc=array();echo"<form action='' method='post' enctype='multipart/form-data'>\n";$L=array();while($K=$I->fetch_assoc()){if($D&&JUSH=="oracle")unset($K["RNUM"]);$L[]=$K;}if($_GET["page"]!="last"&&$z&&$xd&&$ve&&JUSH=="sql")$pd=get_val(" SELECT FOUND_ROWS()");if(!$L)echo"<p class='message'>".'无数据。'."\n";else{$Ea=adminer()->backwardKeys($a,$Ai);echo"<div class='scrollable'>","<table id='table' class='nowrap checkable odds'>",script("mixin(qs('#table'), {onclick: tableClick, ondblclick: partialArg(tableClick, true), onkeydown: editingKeydown});"),"<thead><tr>".(!$xd&&$M?"":"<td><input type='checkbox' id='all-page' class='jsonly'>".script("qs('#all-page').onclick = partial(formCheck, /check/);","")." <a href='".h($_GET["modify"]?remove_from_uri("modify"):$_SERVER["REQUEST_URI"]."&modify=1")."'>".'修改'."</a>");$Af=array();$sd=array();reset($M);$jh=1;foreach($L[0]as$x=>$X){if(!isset($uj[$x])){$X=idx($_GET["columns"],key($M))?:array();$m=$n[$M?($X?$X["col"]:current($M)):$x];$B=($m?adminer()->fieldName($m,$jh):($X["fun"]?"*":h($x)));if($B!=""){$jh++;$Af[$x]=$B;$d=idf_escape($x);$Od=remove_from_uri('(order|desc)[^=]*|page').'&order%5B0%5D='.urlencode($x);$Zb="&desc%5B0%5D=1";echo"<th id='th[".h(bracket_escape($x))."]'>".script("mixin(qsl('th'), {onmouseover: partial(columnMouse), onmouseout: partial(columnMouse, ' hidden')});","");$rd=apply_sql_function($X["fun"],$B);$fi=isset($m["privileges"]["order"])||$rd;echo($fi?"<a href='".h($Od.($eg[0]==$d||$eg[0]==$x?$Zb:''))."'>$rd</a>":$rd),"<span class='column hidden'>";if($fi)echo"<a href='".h($Od.$Zb)."' title='".'降序'."' class='text'> ↓</a>";if(!$X["fun"]&&isset($m["privileges"]["where"]))echo'<a href="#fieldset-search" title="'.'搜索'.'" class="text jsonly"> =</a>',script("qsl('a').onclick = partial(selectSearch, '".js_escape($x)."');");echo"</span>";}$sd[$x]=$X["fun"];next($M);}}$Pe=array();if($_GET["modify"]){foreach($L
as$K){foreach($K
as$x=>$X)$Pe[$x]=max($Pe[$x],min(40,strlen(utf8_decode($X))));}}echo($Ea?"<th>".'关联信息':"")."</thead>\n";if(is_ajax())ob_end_clean();foreach(adminer()->rowDescriptions($L,$md)as$zf=>$K){$rj=unique_array($L[$zf],$w);if(!$rj){$rj=array();reset($M);foreach($L[$zf]as$x=>$X){if(!preg_match('~^(COUNT|AVG|GROUP_CONCAT|MAX|MIN|SUM)\(~',current($M)))$rj[$x]=$X;next($M);}}$sj="";foreach($rj
as$x=>$X){$m=(array)$n[$x];if((JUSH=="sql"||JUSH=="pgsql")&&preg_match('~char|text|enum|set~',$m["type"])&&strlen($X)>64){$x=(strpos($x,'(')?$x:idf_escape($x));$x="MD5(".(JUSH!='sql'||preg_match("~^utf8~",$m["collation"])?$x:"CONVERT($x USING ".charset(connection()).")").")";$X=md5($X);}$sj
.="&".($X!==null?urlencode("where[".bracket_escape($x)."]")."=".urlencode($X===false?"f":$X):"null%5B%5D=".urlencode($x));}echo"<tr>".(!$xd&&$M?"":"<td>".checkbox("check[]",substr($sj,1),in_array(substr($sj,1),(array)$_POST["check"])).($ve||information_schema(DB)?"":" <a href='".h(ME."edit=".urlencode($a).$sj)."' class='edit'>".'编辑'."</a>"));reset($M);foreach($K
as$x=>$X){if(isset($Af[$x])){$d=current($M);$m=(array)$n[$x];$X=driver()->value($X,$m);if($X!=""&&(!isset($wc[$x])||$wc[$x]!=""))$wc[$x]=(is_mail($X)?$Af[$x]:"");$_="";if(is_blob($m)&&$X!="")$_=ME.'download='.urlencode($a).'&field='.urlencode($x).$sj;if(!$_&&$X!==null){foreach((array)$md[$x]as$p){if(count($md[$x])==1||end($p["source"])==$x){$_="";foreach($p["source"]as$s=>$gi)$_
.=where_link($s,$p["target"][$s],$L[$zf][$gi]);$_=($p["db"]!=""?preg_replace('~([?&]db=)[^&]+~','\1'.urlencode($p["db"]),ME):ME).'select='.urlencode($p["table"]).$_;if($p["ns"])$_=preg_replace('~([?&]ns=)[^&]+~','\1'.urlencode($p["ns"]),$_);if(count($p["source"])==1)break;}}}if($d=="COUNT(*)"){$_=ME."select=".urlencode($a);$s=0;foreach((array)$_GET["where"]as$W){if(!array_key_exists($W["col"],$rj))$_
.=where_link($s++,$W["col"],$W["val"],$W["op"]);}foreach($rj
as$Be=>$W)$_
.=where_link($s++,$Be,$W);}$Pd=select_value($X,$_,$m,$Pi);$t=h("val[$sj][".bracket_escape($x)."]");$Sg=idx(idx($_POST["val"],$sj),bracket_escape($x));$rc=!is_array($K[$x])&&is_utf8($Pd)&&$L[$zf][$x]==$K[$x]&&!$sd[$x]&&!$m["generated"];$U=(preg_match('~^(AVG|MIN|MAX)\((.+)\)~',$d,$A)?$n[idf_unescape($A[2])]["type"]:$m["type"]);$Ni=preg_match('~text|json|lob~',$U);$we=preg_match(number_type(),$U)||preg_match('~^(CHAR_LENGTH|ROUND|FLOOR|CEIL|TIME_TO_SEC|COUNT|SUM)\(~',$d);echo"<td id='$t'".($we&&($X===null||is_numeric(strip_tags($Pd))||$U=="money")?" class='number'":"");if(($_GET["modify"]&&$rc&&$X!==null)||$Sg!==null){$Bd=h($Sg!==null?$Sg:$K[$x]);echo">".($Ni?"<textarea name='$t' cols='30' rows='".(substr_count($K[$x],"\n")+1)."'>$Bd</textarea>":"<input name='$t' value='$Bd' size='$Pe[$x]'>");}else{$Ve=strpos($Pd,"<i>…</i>");echo" data-text='".($Ve?2:($Ni?1:0))."'".($rc?"":" data-warning='".h('使用编辑链接修改该值。')."'").">$Pd";}}next($M);}if($Ea)echo"<td>";adminer()->backwardKeysPrint($Ea,$L[$zf]);echo"</tr>\n";}if(is_ajax())exit;echo"</table>\n","</div>\n";}if(!is_ajax()){if($L||$D){$Jc=true;if($_GET["page"]!="last"){if(!$z||(count($L)<$z&&($L||!$D)))$pd=($D?$D*$z:0)+count($L);elseif(JUSH!="sql"||!$ve){$pd=($ve?false:found_rows($S,$Z));if(intval($pd)<max(1e4,2*($D+1)*$z))$pd=first(slow_query(count_rows($a,$Z,$ve,$xd)));else$Jc=false;}}$ug=($z&&($pd===false||$pd>$z||$D));if($ug)echo(($pd===false?count($L)+1:$pd-$D*$z)>$z?'<p><a href="'.h(remove_from_uri("page")."&page=".($D+1)).'" class="loadmore">'.'加载更多数据'.'</a>'.script("qsl('a').onclick = partial(selectLoadMore, $z, '".'加载中'."…');",""):''),"\n";echo"<div class='footer'><div>\n";if($ug){$ff=($pd===false?$D+(count($L)>=$z?2:1):floor(($pd-1)/$z));echo"<fieldset>";if(JUSH!="simpledb"){echo"<legend><a href='".h(remove_from_uri("page"))."'>".'页面'."</a></legend>",script("qsl('a').onclick = function () { pageClick(this.href, +prompt('".'页面'."', '".($D+1)."')); return false; };"),pagination(0,$D).($D>5?" …":"");for($s=max(1,$D-4);$s<min($ff,$D+5);$s++)echo
pagination($s,$D);if($ff>0)echo($D+5<$ff?" …":""),($Jc&&$pd!==false?pagination($ff,$D):" <a href='".h(remove_from_uri("page")."&page=last")."' title='~$ff'>".'最后'."</a>");}else
echo"<legend>".'页面'."</legend>",pagination(0,$D).($D>1?" …":""),($D?pagination($D,$D):""),($ff>$D?pagination($D+1,$D).($ff>$D+1?" …":""):"");echo"</fieldset>\n";}echo"<fieldset>","<legend>".'所有结果'."</legend>";$gc=($Jc?"":"~ ").$pd;$Xf="const checked = formChecked(this, /check/); selectCount('selected', this.checked ? '$gc' : checked); selectCount('selected2', this.checked || !checked ? '$gc' : checked);";echo
checkbox("all",1,0,($pd!==false?($Jc?"":"~ ").sprintf('%d 行',$pd):""),$Xf)."\n","</fieldset>\n";if(adminer()->selectCommandPrint())echo'<fieldset',($_GET["modify"]?'':' class="jsonly"'),'><legend>修改</legend><div>
<input type="submit" value="保存"',($_GET["modify"]?'':' title="'.'按住Ctrl并单击某个值进行修改。'.'"'),'>
</div></fieldset>
<fieldset><legend>已选中 <span id="selected"></span></legend><div>
<input type="submit" name="edit" value="编辑">
<input type="submit" name="clone" value="复制">
<input type="submit" name="delete" value="删除">',confirm(),'</div></fieldset>
';$nd=adminer()->dumpFormat();foreach((array)$_GET["columns"]as$d){if($d["fun"]){unset($nd['sql']);break;}}if($nd){print_fieldset("export",'导出'." <span id='selected2'></span>");$rg=adminer()->dumpOutput();echo($rg?html_select("output",$rg,$na["output"])." ":""),html_select("format",$nd,$na["format"])," <input type='submit' name='export' value='".'导出'."'>\n","</div></fieldset>\n";}adminer()->selectEmailPrint(array_filter($wc,'strlen'),$e);echo"</div></div>\n";}if(adminer()->selectImportPrint())echo"<p>","<a href='#import'>".'导入'."</a>",script("qsl('a').onclick = partial(toggle, 'import');",""),"<span id='import'".($_POST["import"]?"":" class='hidden'").">: ",file_input("<input type='file' name='csv_file'> ".html_select("separator",array("csv"=>"CSV,","csv;"=>"CSV;","tsv"=>"TSV"),$na["format"])." <input type='submit' name='import' value='".'导入'."'>"),"</span>";echo
input_token(),"</form>\n",(!$xd&&$M?"":script("tableCheck();"));}}}if(is_ajax()){ob_end_clean();exit;}}elseif(isset($_GET["variables"])){$P=isset($_GET["status"]);page_header($P?'状态':'变量');$Jj=($P?adminer()->showStatus():adminer()->showVariables());if(!$Jj)echo"<p class='message'>".'无数据。'."\n";else{echo"<table>\n";foreach($Jj
as$K){echo"<tr>";$x=array_shift($K);echo"<th><code class='jush-".JUSH.($P?"status":"set")."'>".h($x)."</code>";foreach($K
as$X)echo"<td>".nl_br(h($X));}echo"</table>\n";}}elseif(isset($_GET["script"])){header("Content-Type: text/javascript; charset=utf-8");if($_GET["script"]=="db"){$xi=array("Data_length"=>0,"Index_length"=>0,"Data_free"=>0);foreach(table_status()as$B=>$S){json_row("Comment-$B",h($S["Comment"]));if(!is_view($S)||preg_match('~materialized~i',$S["Engine"])){foreach(array("Engine","Collation")as$x)json_row("$x-$B",h($S[$x]));foreach($xi+array("Auto_increment"=>0,"Rows"=>0)as$x=>$X){if($S[$x]!=""){$X=format_number($S[$x]);if($X>=0)json_row("$x-$B",($x=="Rows"&&$X&&$S["Engine"]==(JUSH=="pgsql"?"table":"InnoDB")?"~ $X":$X));if(isset($xi[$x]))$xi[$x]+=($S["Engine"]!="InnoDB"||$x!="Data_free"?$S[$x]:0);}elseif(array_key_exists($x,$S))json_row("$x-$B","?");}}}foreach($xi
as$x=>$X)json_row("sum-$x",format_number($X));json_row("");}elseif($_GET["script"]=="kill")connection()->query("KILL ".number($_POST["kill"]));else{foreach(count_tables(adminer()->databases())as$j=>$X){json_row("tables-$j",$X);json_row("size-$j",db_size($j));}json_row("");}exit;}else{$Hi=array_merge((array)$_POST["tables"],(array)$_POST["views"]);if($Hi&&!$l&&!$_POST["search"]){$I=true;$mf="";if(JUSH=="sql"&&$_POST["tables"]&&count($_POST["tables"])>1&&($_POST["drop"]||$_POST["truncate"]||$_POST["copy"]))queries("SET foreign_key_checks = 0");if($_POST["truncate"]){if($_POST["tables"])$I=truncate_tables($_POST["tables"]);$mf='已清空表。';}elseif($_POST["move"]){$I=move_tables((array)$_POST["tables"],(array)$_POST["views"],$_POST["target"]);$mf='已转移表。';}elseif($_POST["copy"]){$I=copy_tables((array)$_POST["tables"],(array)$_POST["views"],$_POST["target"]);$mf='已复制表。';}elseif($_POST["drop"]){if($_POST["views"])$I=drop_views($_POST["views"]);if($I&&$_POST["tables"])$I=drop_tables($_POST["tables"]);$mf='已删除表。';}elseif(JUSH=="sqlite"&&$_POST["check"]){foreach((array)$_POST["tables"]as$R){foreach(get_rows("PRAGMA integrity_check(".q($R).")")as$K)$mf
.="<b>".h($R)."</b>: ".h($K["integrity_check"])."<br>";}}elseif(JUSH!="sql"){$I=(JUSH=="sqlite"?queries("VACUUM"):apply_queries("VACUUM".($_POST["optimize"]?"":" ANALYZE"),$_POST["tables"]));$mf='已优化表。';}elseif(!$_POST["tables"])$mf='没有表。';elseif($I=queries(($_POST["optimize"]?"OPTIMIZE":($_POST["check"]?"CHECK":($_POST["repair"]?"REPAIR":"ANALYZE")))." TABLE ".implode(", ",array_map('Adminer\idf_escape',$_POST["tables"])))){while($K=$I->fetch_assoc())$mf
.="<b>".h($K["Table"])."</b>: ".h($K["Msg_text"])."<br>";}queries_redirect(substr(ME,0,-1),$mf,$I);}page_header(($_GET["ns"]==""?'数据库'.": ".h(DB):'模式'.": ".h($_GET["ns"])),$l,true);if(adminer()->homepage()){if($_GET["ns"]!==""){echo"<h3 id='tables-views'>".'表和视图'."</h3>\n";$Gi=tables_list();if(!$Gi)echo"<p class='message'>".'没有表。'."\n";else{echo"<form action='' method='post'>\n";if(support("table")){echo"<fieldset><legend>".'在表中搜索数据'." <span id='selected2'></span></legend><div>",html_select("op",adminer()->operators(),idx($_POST,"op",JUSH=="elastic"?"should":"LIKE %%"))," <input type='search' name='query' value='".h($_POST["query"])."'>",script("qsl('input').onkeydown = partialArg(bodyKeydown, 'search');","")," <input type='submit' name='search' value='".'搜索'."'>\n","</div></fieldset>\n";if($_POST["search"]&&$_POST["query"]!=""){$_GET["where"][0]["op"]=$_POST["op"];search_tables();}}echo"<div class='scrollable'>\n","<table class='nowrap checkable odds'>\n",script("mixin(qsl('table'), {onclick: tableClick, ondblclick: partialArg(tableClick, true)});"),'<thead><tr class="wrap">','<td><input id="check-all" type="checkbox" class="jsonly">'.script("qs('#check-all').onclick = partial(formCheck, /^(tables|views)\[/);",""),'<th>'.'表','<td>'.'引擎'.doc_link(array('sql'=>'storage-engines.html')),'<td>'.'校对'.doc_link(array('sql'=>'charset-charsets.html','mariadb'=>'supported-character-sets-and-collations/')),'<td>'.'数据长度'.doc_link(array('sql'=>'show-table-status.html','pgsql'=>'functions-admin.html#FUNCTIONS-ADMIN-DBOBJECT','oracle'=>'REFRN20286')),'<td>'.'索引长度'.doc_link(array('sql'=>'show-table-status.html','pgsql'=>'functions-admin.html#FUNCTIONS-ADMIN-DBOBJECT')),'<td>'.'数据空闲'.doc_link(array('sql'=>'show-table-status.html')),'<td>'.'自动增量'.doc_link(array('sql'=>'example-auto-increment.html','mariadb'=>'auto_increment/')),'<td>'.'行数'.doc_link(array('sql'=>'show-table-status.html','pgsql'=>'catalog-pg-class.html#CATALOG-PG-CLASS','oracle'=>'REFRN20286')),(support("comment")?'<td>'.'注释'.doc_link(array('sql'=>'show-table-status.html','pgsql'=>'functions-info.html#FUNCTIONS-INFO-COMMENT-TABLE')):''),"</thead>\n";$T=0;foreach($Gi
as$B=>$U){$Mj=($U!==null&&!preg_match('~table|sequence~i',$U));$t=h("Table-".$B);echo'<tr><td>'.checkbox(($Mj?"views[]":"tables[]"),$B,in_array("$B",$Hi,true),"","","",$t),'<th>'.(support("table")||support("indexes")?"<a href='".h(ME)."table=".urlencode($B)."' title='".'显示结构'."' id='$t'>".h($B).'</a>':h($B));if($Mj&&!preg_match('~materialized~i',$U)){$Ti='视图';echo'<td colspan="6">'.(support("view")?"<a href='".h(ME)."view=".urlencode($B)."' title='".'修改视图'."'>$Ti</a>":$Ti),'<td align="right"><a href="'.h(ME)."select=".urlencode($B).'" title="'.'选择数据'.'">?</a>';}else{foreach(array("Engine"=>array(),"Collation"=>array(),"Data_length"=>array("create",'修改表'),"Index_length"=>array("indexes",'修改索引'),"Data_free"=>array("edit",'新建数据'),"Auto_increment"=>array("auto_increment=1&create",'修改表'),"Rows"=>array("select",'选择数据'),)as$x=>$_){$t=" id='$x-".h($B)."'";echo($_?"<td align='right'>".(support("table")||$x=="Rows"||(support("indexes")&&$x!="Data_length")?"<a href='".h(ME."$_[0]=").urlencode($B)."'$t title='$_[1]'>?</a>":"<span$t>?</span>"):"<td id='$x-".h($B)."'>");}$T++;}echo(support("comment")?"<td id='Comment-".h($B)."'>":""),"\n";}echo"<tr><td><th>".sprintf('共计 %d',count($Gi)),"<td>".h(JUSH=="sql"?get_val("SELECT @@default_storage_engine"):""),"<td>".h(db_collation(DB,collations()));foreach(array("Data_length","Index_length","Data_free")as$x)echo"<td align='right' id='sum-$x'>";echo"\n","</table>\n",script("ajaxSetHtml('".js_escape(ME)."script=db');"),"</div>\n";if(!information_schema(DB)){echo"<div class='footer'><div>\n";$Gj="<input type='submit' value='".'整理（Vacuum）'."'> ".on_help("'VACUUM'");$ag="<input type='submit' name='optimize' value='".'优化'."'> ".on_help(JUSH=="sql"?"'OPTIMIZE TABLE'":"'VACUUM OPTIMIZE'");echo"<fieldset><legend>".'已选中'." <span id='selected'></span></legend><div>".(JUSH=="sqlite"?$Gj."<input type='submit' name='check' value='".'检查'."'> ".on_help("'PRAGMA integrity_check'"):(JUSH=="pgsql"?$Gj.$ag:(JUSH=="sql"?"<input type='submit' value='".'分析'."'> ".on_help("'ANALYZE TABLE'").$ag."<input type='submit' name='check' value='".'检查'."'> ".on_help("'CHECK TABLE'")."<input type='submit' name='repair' value='".'修复'."'> ".on_help("'REPAIR TABLE'"):"")))."<input type='submit' name='truncate' value='".'清空'."'> ".on_help(JUSH=="sqlite"?"'DELETE'":"'TRUNCATE".(JUSH=="pgsql"?"'":" TABLE'")).confirm()."<input type='submit' name='drop' value='".'删除'."'>".on_help("'DROP TABLE'").confirm()."\n";$i=(support("scheme")?adminer()->schemas():adminer()->databases());echo"</div></fieldset>\n";$Kh="";if(count($i)!=1&&JUSH!="sqlite"){echo"<fieldset><legend>".'转移到其它数据库'." <span id='selected3'></span></legend><div>";$j=(isset($_POST["target"])?$_POST["target"]:(support("scheme")?$_GET["ns"]:DB));echo($i?html_select("target",$i,$j):'<input name="target" value="'.h($j).'" autocapitalize="off">'),"</label> <input type='submit' name='move' value='".'转移'."'>",(support("copy")?" <input type='submit' name='copy' value='".'复制'."'> ".checkbox("overwrite",1,$_POST["overwrite"],'覆盖'):""),"</div></fieldset>\n";$Kh=" selectCount('selected3', formChecked(this, /^(tables|views)\[/));";}echo"<input type='hidden' name='all' value=''>",script("qsl('input').onclick = function () { selectCount('selected', formChecked(this, /^(tables|views)\[/));".(support("table")?" selectCount('selected2', formChecked(this, /^tables\[/) || $T);":"")."$Kh }"),input_token(),"</div></div>\n";}echo"</form>\n",script("tableCheck();");}echo"<p class='links'><a href='".h(ME)."create='>".'创建表'."</a>\n",(support("view")?"<a href='".h(ME)."view='>".'创建视图'."</a>\n":"");if(support("routine")){echo"<h3 id='routines'>".'子程序'."</h3>\n";$Ch=routines();if($Ch){echo"<table class='odds'>\n",'<thead><tr><th>'.'名称'.'<td>'.'类型'.'<td>'.'返回类型'."<td></thead>\n";foreach($Ch
as$K){$B=($K["SPECIFIC_NAME"]==$K["ROUTINE_NAME"]?"":"&name=".urlencode($K["ROUTINE_NAME"]));echo'<tr>','<th><a href="'.h(ME.($K["ROUTINE_TYPE"]!="PROCEDURE"?'callf=':'call=').urlencode($K["SPECIFIC_NAME"]).$B).'">'.h($K["ROUTINE_NAME"]).'</a>','<td>'.h($K["ROUTINE_TYPE"]),'<td>'.h($K["DTD_IDENTIFIER"]),'<td><a href="'.h(ME.($K["ROUTINE_TYPE"]!="PROCEDURE"?'function=':'procedure=').urlencode($K["SPECIFIC_NAME"]).$B).'">'.'修改'."</a>";}echo"</table>\n";}echo'<p class="links">'.(support("procedure")?'<a href="'.h(ME).'procedure=">'.'创建过程'.'</a>':'').'<a href="'.h(ME).'function=">'.'创建函数'."</a>\n";}if(support("sequence")){echo"<h3 id='sequences'>".'序列'."</h3>\n";$Vh=get_vals("SELECT sequence_name FROM information_schema.sequences WHERE sequence_schema = current_schema() ORDER BY sequence_name");if($Vh){echo"<table class='odds'>\n","<thead><tr><th>".'名称'."</thead>\n";foreach($Vh
as$X)echo"<tr><th><a href='".h(ME)."sequence=".urlencode($X)."'>".h($X)."</a>\n";echo"</table>\n";}echo"<p class='links'><a href='".h(ME)."sequence='>".'创建序列'."</a>\n";}if(support("type")){echo"<h3 id='user-types'>".'用户类型'."</h3>\n";$Ej=types();if($Ej){echo"<table class='odds'>\n","<thead><tr><th>".'名称'."</thead>\n";foreach($Ej
as$X)echo"<tr><th><a href='".h(ME)."type=".urlencode($X)."'>".h($X)."</a>\n";echo"</table>\n";}echo"<p class='links'><a href='".h(ME)."type='>".'创建类型'."</a>\n";}if(support("event")){echo"<h3 id='events'>".'事件'."</h3>\n";$L=get_rows("SHOW EVENTS");if($L){echo"<table>\n","<thead><tr><th>".'名称'."<td>".'调度'."<td>".'开始'."<td>".'结束'."<td></thead>\n";foreach($L
as$K)echo"<tr>","<th>".h($K["Name"]),"<td>".($K["Execute at"]?'在指定时间'."<td>".$K["Execute at"]:'每'." ".$K["Interval value"]." ".$K["Interval field"]."<td>$K[Starts]"),"<td>$K[Ends]",'<td><a href="'.h(ME).'event='.urlencode($K["Name"]).'">'.'修改'.'</a>';echo"</table>\n";$Hc=get_val("SELECT @@event_scheduler");if($Hc&&$Hc!="ON")echo"<p class='error'><code class='jush-sqlset'>event_scheduler</code>: ".h($Hc)."\n";}echo'<p class="links"><a href="'.h(ME).'event=">'.'创建事件'."</a>\n";}}}}page_footer();