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
sprintf('%.3f ç§’',max(0,microtime(true)-$pi));}function
relative_uri(){return
str_replace(":","%3a",preg_replace('~^[^?]*/([^?]*)~','\1',$_SERVER["REQUEST_URI"]));}function
remove_from_uri($vg=""){return
substr(preg_replace("~(?<=[?&])($vg".(SID?"":"|".session_name()).")=[^&]*&~",'',relative_uri()."&"),0,-1);}function
get_file($x,$Sb=false,$Yb=""){$ad=$_FILES[$x];if(!$ad)return
null;foreach($ad
as$x=>$X)$ad[$x]=(array)$X;$J='';foreach($ad["error"]as$x=>$l){if($l)return$l;$B=$ad["name"][$x];$Yi=$ad["tmp_name"][$x];$zb=file_get_contents($Sb&&preg_match('~\.gz$~',$B)?"compress.zlib://$Yi":$Yi);if($Sb){$pi=substr($zb,0,3);if(function_exists("iconv")&&preg_match("~^\xFE\xFF|^\xFF\xFE~",$pi))$zb=iconv("utf-16","utf-8",$zb);elseif($pi=="\xEF\xBB\xBF")$zb=substr($zb,3);}$J
.=$zb;if($Yb)$J
.=(preg_match("($Yb\\s*\$)",$zb)?"":$Yb)."\n\n";}return$J;}function
upload_error($l){$hf=($l==UPLOAD_ERR_INI_SIZE?ini_get("upload_max_filesize"):0);return($l?'ä¸èƒ½ä¸Šä¼ æ–‡ä»¶ã€‚'.($hf?" ".sprintf('æœ€å¤šå…è®¸çš„æ–‡ä»¶å¤§å°ä¸º %sBã€‚',$hf):""):'æ–‡ä»¶ä¸å­˜åœ¨ã€‚');}function
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
preg_match('~blob|bytea|raw|file~',$m["type"])&&!in_array($m["type"],idx(driver()->structuredTypes(),'ç”¨æˆ·ç±»å‹',array()));}function
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
script("$Ph.onclick = () => confirm('".($mf?js_escape($mf):'æ‚¨ç¡®å®šå—ï¼Ÿ')."');","");}function
print_fieldset($t,$Oe,$Oj=false){echo"<fieldset><legend>","<a href='#fieldset-$t'>$Oe</a>",script("qsl('a').onclick = partial(toggle, 'fieldset-$t');",""),"</legend>","<div id='fieldset-$t'".($Oj?"":" class='hidden'").">\n";}function
bold($La,$db=""){return($La?" class='active $db'":($db?" class='$db'":""));}function
js_escape($Q){return
addcslashes($Q,"\r\n'\\/");}function
pagination($D,$Jb){return" ".($D==$Jb?$D+1:'<a href="'.h(remove_from_uri("page").($D?"&page=$D".($_GET["next"]?"&next=".urlencode($_GET["next"]):""):"")).'">'.($D+1)."</a>");}function
hidden_fields(array$bh,array$Vd=array(),$Tg=''){$J=false;foreach($bh
as$x=>$X){if(!in_array($x,$Vd)){if(is_array($X))hidden_fields($X,array(),$x);else{$J=true;echo
input_hidden(($Tg?$Tg."[$x]":$x),$X);}}}return$J;}function
hidden_fields_get(){echo(sid()?input_hidden(session_name(),session_id()):''),(SERVER!==null?input_hidden(DRIVER,SERVER):""),input_hidden("username",$_GET["username"]);}function
file_input($ne){$cf="max_file_uploads";$df=ini_get($cf);$xj="upload_max_filesize";$yj=ini_get($xj);return(ini_bool("file_uploads")?$ne.script("qsl('input[type=\"file\"]').onchange = partialArg(fileChange, "."$df, '".sprintf('Increase %s.',"$cf = $df")."', ".ini_bytes("upload_max_filesize").", '".sprintf('Increase %s.',"$xj = $yj")."')"):'æ–‡ä»¶ä¸Šä¼ è¢«ç¦ç”¨ã€‚');}function
enum_input($U,$ya,array$m,$Y,$xc=""){preg_match_all("~'((?:[^']|'')*)'~",$m["length"],$af);$Tg=($m["type"]=="enum"?"val-":"");$Za=(is_array($Y)?in_array("null",$Y):$Y===null);$J=($m["null"]&&$Tg?"<label><input type='$U'$ya value='null'".($Za?" checked":"")."><i>$xc</i></label>":"");foreach($af[1]as$X){$X=stripcslashes(str_replace("''","'",$X));$Za=(is_array($Y)?in_array($Tg.$X,$Y):$Y===$X);$J
.=" <label><input type='$U'$ya value='".h($Tg.$X)."'".($Za?' checked':'').'>'.h(adminer()->editVal($X,$m)).'</label>';}return$J;}function
input(array$m,$Y,$r,$Ba=false){$B=h(bracket_escape($m["field"]));echo"<td class='function'>";if(is_array($Y)&&!$r){$Y=json_encode($Y,128|64|256);$r="json";}$uh=(JUSH=="mssql"&&$m["auto_increment"]);if($uh&&!$_POST["save"])$r=null;$sd=(isset($_GET["select"])||$uh?array("orig"=>'åŸå§‹'):array())+adminer()->editFunctions($m);$Cc=driver()->enumLength($m);if($Cc){$m["type"]="enum";$m["length"]=$Cc;}$ec=stripos($m["default"],"GENERATED ALWAYS AS ")===0?" disabled=''":"";$ya=" name='fields[$B]".($m["type"]=="enum"||$m["type"]=="set"?"[]":"")."'$ec".($Ba?" autofocus":"");echo
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
search_tables(){$_GET["where"][0]["val"]=$_POST["query"];$Rh="<ul>\n";foreach(table_status('',true)as$R=>$S){$B=adminer()->tableName($S);if(isset($S["Engine"])&&$B!=""&&(!$_POST["tables"]||in_array($R,$_POST["tables"]))){$I=connection()->query("SELECT".limit("1 FROM ".table($R)," WHERE ".implode(" AND ",adminer()->selectSearchProcess(fields($R),array())),1));if(!$I||$I->fetch_row()){$Xg="<a href='".h(ME."select=".urlencode($R)."&where[0][op]=".urlencode($_GET["where"][0]["op"])."&where[0][val]=".urlencode($_GET["where"][0]["val"]))."'>$B</a>";echo"$Rh<li>".($I?$Xg:"<p class='error'>$Xg: ".error())."\n";$Rh="";}}}echo($Rh?"<p class='message'>".'æ²¡æœ‰è¡¨ã€‚':"</ul>")."\n";}function
on_help($mb,$ci=0){return
script("mixin(qsl('select, input'), {onmouseover: function (event) { helpMouseover.call(this, event, $mb, $ci) }, onmouseout: helpMouseout});","");}function
edit_form($R,array$n,$K,$wj,$l=''){$Ai=adminer()->tableName(table_status1($R,true));page_header(($wj?'ç¼–è¾‘':'æ’å…¥'),$l,array("select"=>array($R,$Ai)),$Ai);adminer()->editRowPrint($R,$n,$K,$wj);if($K===false){echo"<p class='error'>".'æ— æ•°æ®ã€‚'."\n";return;}echo"<form action='' method='post' enctype='multipart/form-data' id='form'>\n";if(!$n)echo"<p class='error'>".'æ‚¨æ²¡æœ‰æƒé™æ›´æ–°è¿™ä¸ªè¡¨ã€‚'."\n";else{echo"<table class='layout'>".script("qsl('table').onkeydown = editingKeydown;");$Ba=!$_POST;foreach($n
as$B=>$m){echo"<tr><th>".adminer()->fieldName($m);$k=idx($_GET["set"],bracket_escape($B));if($k===null){$k=$m["default"];if($m["type"]=="bit"&&preg_match("~^b'([01]*)'\$~",$k,$rh))$k=$rh[1];if(JUSH=="sql"&&preg_match('~binary~',$m["type"]))$k=bin2hex($k);}$Y=($K!==null?($K[$B]!=""&&JUSH=="sql"&&preg_match("~enum|set~",$m["type"])&&is_array($K[$B])?implode(",",$K[$B]):(is_bool($K[$B])?+$K[$B]:$K[$B])):(!$wj&&$m["auto_increment"]?"":(isset($_GET["select"])?false:$k)));if(!$_POST["save"]&&is_string($Y))$Y=adminer()->editVal($Y,$m);$r=($_POST["save"]?idx($_POST["function"],$B,""):($wj&&preg_match('~^CURRENT_TIMESTAMP~i',$m["on_update"])?"now":($Y===false?null:($Y!==null?'':'NULL'))));if(!$_POST&&!$wj&&$Y==$m["default"]&&preg_match('~^[\w.]+\(~',$Y))$r="SQL";if(preg_match("~time~",$m["type"])&&preg_match('~^CURRENT_TIMESTAMP~i',$Y)){$Y="";$r="now";}if($m["type"]=="uuid"&&$Y=="uuid()"){$Y="";$r="uuid";}if($Ba!==false)$Ba=($m["auto_increment"]||$r=="now"||$r=="uuid"?null:true);input($m,$Y,$r,$Ba);if($Ba)$Ba=false;echo"\n";}if(!support("table")&&!fields($R))echo"<tr>"."<th><input name='field_keys[]'>".script("qsl('input').oninput = fieldChange;")."<td class='function'>".html_select("field_funs[]",adminer()->editFunctions(array("null"=>isset($_GET["select"]))))."<td><input name='field_vals[]'>"."\n";echo"</table>\n";}echo"<p>\n";if($n){echo"<input type='submit' value='".'ä¿å­˜'."'>\n";if(!isset($_GET["select"]))echo"<input type='submit' name='insert' value='".($wj?'ä¿å­˜å¹¶ç»§ç»­ç¼–è¾‘':'ä¿å­˜å¹¶æ’å…¥ä¸‹ä¸€ä¸ª')."' title='Ctrl+Shift+Enter'>\n",($wj?script("qsl('input').onclick = function () { return !ajaxForm(this.form, '".'ä¿å­˜ä¸­'."â€¦', this); };"):"");}echo($wj?"<input type='submit' name='delete' value='".'åˆ é™¤'."'>".confirm()."\n":"");if(isset($_GET["select"]))hidden_fields(array("check"=>(array)$_POST["check"],"clone"=>$_POST["clone"],"all"=>$_POST["all"]));echo
input_hidden("referer",(isset($_POST["referer"])?$_POST["referer"]:$_SERVER["HTTP_REFERER"])),input_hidden("save",1),input_token(),"</form>\n";}function
shorten_utf8($Q,$y=80,$vi=""){if(!preg_match("(^(".repeat_pattern("[\t\r\n -\x{10FFFF}]",$y).")($)?)u",$Q,$A))preg_match("(^(".repeat_pattern("[\t\r\n -~]",$y).")($)?)",$Q,$A);return
h($A[1]).$vi.(isset($A[2])?"":"<i>â€¦</i>");}function
icon($Qd,$B,$Pd,$Ti){return"<button type='submit' name='$B' title='".h($Ti)."' class='icon icon-$Qd'><span>$Pd</span></button>";}if(isset($_GET["file"])){if(substr(VERSION,-4)!='-dev'){if($_SERVER["HTTP_IF_MODIFIED_SINCE"]){header("HTTP/1.1 304 Not Modified");exit;}header("Expires: ".gmdate("D, d M Y H:i:s",time()+365*24*60*60)." GMT");header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");header("Cache-Control: immutable");}@ini_set("zlib.output_compression",'1');if($_GET["file"]=="default.css"){header("Content-Type: text/css; charset=utf-8");echo
lzw_decompress("h:M‡±h´ÄgÌĞ±ÜÍŒ\"PÑiÒm„™cQCa¤é	2Ã³éˆŞd<Ìfóa¼ä:;NBˆqœR;1Lf³9ÈŞu7&)¤l;3ÍÑñÈÀJ/‹†CQXÊr2MÆaäi0›„ƒ)°ìe:LuÃhæ-9ÕÍ23lÈÎi7†³màZw4™†Ñš<-•ÒÌ´¹!†U,—ŒFÃ©”vt2‘S,¬äa´Ò‡FêVXúa˜Nqã)“-—ÖÎÇœhê:n5û9ÈY¨;jµ”-Ş÷_‘9krùœÙ“;.ĞtTqËo¦0‹³­Öò®{íóyùı\rçHnìGS™ Zh²œ;¼i^ÀuxøWÎ’C@Äö¤©k€Ò=¡Ğb©Ëâì¼/AØà0¤+Â(ÚÁ°lÂÉÂ\\ê Ãxè:\rèÀb8\0æ–0!\0FÆ\nB”Íã(Ò3 \r\\ºÛêÈ„a¼„œ'Iâ|ê(iš\n‹\r©¸ú4Oüg@4ÁC’î¼†º@@†!ÄQB°İ	Â°¸c¤ÊÂ¯Äq,\r1EhèÈ&2PZ‡¦ğiGûH9G’\"v§ê’¢££¤œ4r”ÆñÍDĞR¤\n†pJë-A“|/.¯cê“Du·£¤ö:,˜Ê=°¢RÅ]U5¥mVÁkÍLLQ@-\\ª¦ËŒ@9Áã%ÚSrÁÎñMPDãÂIa\rƒ(YY\\ã@XõpÃê:£p÷lLC —Åñè¸ƒÍÊO,\rÆ2]7œ?m06ä»pÜTÑÍaÒ¥Cœ;_Ë—ÑyÈ´d‘>¨²bnğ…«n¼Ü£3÷X¾€ö8\rí[Ë€-)Ûi>V[Yãy&L3¯#ÌX|Õ	†X \\Ã¹`ËC§ç˜å#ÑÙHÉÌ2Ê2.# ö‹Zƒ`Â<¾ãs®·¹ªÃ’£º\0uœhÖ¾—¥M²Í_\niZeO/CÓ’_†`3İòğ1>‹=Ğk3£…‰R/;ä/dÛÜ\0ú‹ŒãŞÚµmùúò¾¤7/«ÖAÎXƒÂÿ„°“Ãq.½sáL£ı— :\$ÉF¢—¸ª¾£‚w‰8óß¾~«HÔj…­\"¨¼œ•¹Ô³7gSõä±âFLéÎ¯çQò_¤’O'WØö]c=ı5¾1X~7;˜™iş´\rí*\n’¨JS1Z¦™ø£ØÆßÍcå‚tœüAÔVí86fĞdÃy;Y]©õzIÀp¡Ñû§ğc‰3®YË]}Â˜@¡\$.+”1¶'>ZÃcpdàéÒGLæá„#kô8PzœYÒAuÏvİ]s9‰ÑØ_AqÎÁ„:†ÆÅ\nK€hB¼;­ÖŠXbAHq,âCIÉ`†‚çj¹S[ËŒ¶1ÆVÓrŠñÔ;¶pŞBÃÛ)#é‰;4ÌHñÒ/*Õ<Â3L Á;lfª\n¶s\$K`Ğ}ÆôÕ”£¾7ƒjx`d–%j] ¸4œ—Y¤–HbY ØJ`¤GG ’.ÅÜK‚òfÊI©)2ÂŠMfÖ¸İX‰RC‰¸Ì±V,©ÛÑ~g\0è‚àg6İ:õ[jí1H½:AlIq©u3\"™êæq¤æ|8<9s'ãQ]JÊ|Ğ\0Â`p ³îƒ«‰jf„OÆbĞÉú¬¨q¬¢\$é©²Ã1J¹>RœH(Ç”q\n#rŠ’à@e(yóVJµ0¡QÒˆ£òˆ6†Pæ[C:·Gä¼‘ İ4©‘Ò^ÓğÃPZŠµ\\´‘è(\nÖ)š~¦´°9R%×Sj·{‰7ä0Ş_šÇs	z|8ÅHê	\"@Ü#9DVLÅ\$H5ÔWJ@—…z®a¿J Ä^	‘)®2\nQvÀÔ]ëÇ†ÄÁ˜‰j (A¸Ó°BB05´6†bË°][ŒèkªA•wvkgôÆ´öºÕ+k[jm„zc¶}èMyDZií\$5e˜«Ê·°º	”A˜ CY%.W€b*ë®¼‚.­Ùóq/%}BÌXˆ­çZV337‡Ê»a™„€ºòŞwW[áLQÊŞ²ü_È2`Ç1IÑi,÷æ›£’Mf&(s-˜ä˜ëÂAÄ°Ø*””DwØÄTNÀÉ»ÅjX\$éxª+;ĞğËFÚ93µJkÂ™S;·§ÁqR{>l;B1AÈIâb) (6±­r÷\rİ\rÚ‡’Ú‚ìZ‘R^SOy/“ŞM#ÆÏ9{k„àê¸v\"úKCâJƒ¨rEo\0øÌ\\,Ñ|faÍš†³hI“©/oÌ4Äk^pî1HÈ^“ÍphÇ¡VÁvox@ø`ígŸ&(ùˆ­ü;›ƒ~ÇzÌ6×8¯*°ÆÜ5®Ü‰±E ÁÂp†éâîÓ˜˜¤´3“öÅ†gŸ™rDÑLó)4g{»ˆä½å³©—Lš&ú>è„»¢ØÚZì7¡\0ú°ÌŠ@×ĞÓÛœffÅRVhÖ²çIŠÛˆ½âğrÓw)‹ ‚„=x^˜,k’Ÿ2ôÒİ“jàbël0uë\"¬fp¨¸1ñRI¿ƒz[]¤wpN6dIªzëõån.7X{;ÁÈ3ØË-I	‹âûü7pjÃ¢R#ª,ù_-ĞüÂ[ó>3À\\æêÛWqŞq”JÖ˜uh£‡ĞFbLÁKÔåçyVÄ¾©¦ÃŞÑ•®µªüVœîÃf{K}S ÊŞ…‰Mş‡·Í€¼¦.M¶\\ªix¸bÁ¡1‡+£Î±?<Å3ê~HıÓ\$÷\\Ğ2Û\$î eØ6tÔOÌˆã\$s¼¼©xÄşx•ó§CánSkVÄÉ=z6½‰¡Ê'Ã¦äNaŸ¢Ö¸hŒÜü¸º±ı¯R¤å™£8g‰¢äÊw:_³î­íÿêÒ’IRKÃ¨.½nkVU+dwj™§%³`#,{é†³ËğÊƒY‡ı×õ(oÕ¾Éğ.¨c‚0gâDXOk†7®èKäÎlÒÍhx;ÏØ İƒLû´\$09*–9 ÜhNrüMÕ.>\0ØrP9ï\$Èg	\0\$\\Fó*²d'ÎõLå:‹bú—ğ42Àô¢ğ9Àğ@ÂHnbì-¤óE #ÄœÉÃ êrPY‚ê¨ tÍ Ø\nğ5.©àÊâî\$op l€X\n@`\r€	àˆ\r€Ğ Î ¦ ’ ‚	 ÊàêğÚ Î	@Ú@Ú\n ƒ †	\0j@ƒQ@™1\rÀ‚@“ ¢	\$p	 V\0ò``\n\0¨\n Ğ\n@¨' ìÀ¤\n\0`\rÀÚ ¬	à’\rà¤ ´\0Ğr°æÀò	\0„`‚	àî {	,\"¨È^PŸ0¥\n¬4±\n0·¤ˆ.0ÃpËğÓ\rpÛ\rğãpëğópûñqñQ0ß%€ÑÑ1Q8\n Ô\0ôkÊÈ¼\0^—àÒ\0`àÚ@´àÈ>\nÑo1w±,Y	h*=Š¡P¦:Ñ–VƒïĞ¸.q£ÅÍ\rÕ\r‘péĞñ1ÁÑQ	ÑÑ1× ƒ`Ññ/17±ëñò\r ^Àä\"y`\nÀ Œ# ˜\0ê	 p\n€ò\n€š`Œ ˆr ”Q†ğ¦bç1Ò3\n°¯#°µ#ğ¼1¥\$q«\$Ñ±%0å%q½%Ğù&Ç&qÍ ƒ&ñ'1Ú\rR}16	 ï@b\r`µ`Ü\rÀˆ	€ŞÀÌ€dàª€¨	j\n¯``À†\n€œ`dcÑP–€,ò1R×Ÿ\$¿rIÒO ‚	Q	òY32b1É&‘Ï01ÓÑÙ ’Ó fÀÏ\0ª\0¤ Îf€\0j\n f`â	 ®\n`´@˜\$n=`†\0ÈÒv nIĞ\$ÿP(Âd'Ëğô„Äà·gÉ6‘™-Šƒ-ÒC7Rçà‡ —	4à ô-1Ë&±Ñ2t\rô\"\n 	H*@	ˆ`\n ¤ è	àòlÕ2¿,z\rì~È è\r—Fìth‰Šö€Ø ëmõäÄì´z”~¡\0]GÌF\\¥×I€\\¥£}ItC\nÁT„}ªØ×IEJ\rx×ÉûÂ>ÙMp‹„IHô~êäfht„ë¯.b…—xYEìiK´ªoj\nğíÅLÀŞtr×.À~d»H‡2U4©Gà\\Aê‚ç4ş„uPtŞÃÕ½è° òàÍL/¿P×	\"G!RîÎMtŸO-Ìµ<#õAPuI‡ëRè\$“c’¹ÃD‹ÆŠ €§¢-‚ÃGâ´O`Pv§^W@tH;Q°µRÄ™Õ\$´©gKèF<\rR*\$4®' ó¨ĞÈÊ[í°ÛIªó­UmÑÆh:+ş¼5@/­l¾I¾ªí2¦‚^\0ODøšª¬Ø\rR'Â\rèTĞ­[êÖ÷ÄÄª®«MCëMÃZ4æE B\"æ`ö‚´euNí,ä™¬é]Ïğtú\rª`Ü@hö*\r¶.Vƒ–%Ú!MBlPF™Ï\"Øï&Õ/@îv\\CŞï©:mMgnò®öÊi8˜I2\rpívjí©Æ÷ï+Z mT©ueõÕfv>f´Ğ˜Ö`DU[ZTÏVĞCàµTğ\r–¹Uv‹kõ^×¦øLëÙb/¾K¶Sev2÷ubvÇOVDğÖImÕ\$ò%ÖX?udç!W•|,\rø+îµcnUe×ZÆÄÊ–€şöë-~X¯ºûîÀêÔöBGd¶\$i¶çMv!t#Lì3o·UI—O—u?ZweRÏ ëcwª. `È¡iøñ\rb§%©b€â¦H®\"\"\"hí _\$b@ázªä\0f\"ŒérW¨®*ŠæB|\$\$¬BÖ× \"@r¯‚(\r`Ê îC÷¸Ç(0&†.`ÒNk9B\n&#(Äêâ„@ä‚¯Ú«d—ü^÷º®Šü £@²`ÒI-{ƒ0£â\n–B{‚4sG{§ø;z®©b÷{ Ñ{bƒ×¯„){BàÁxKÂÀÅ‡5=cÚª‰«yåî&ìJ£PrÅI/‡ƒÜ \0ÚâV\r¥×‰í‰È=¸£‰‚N\\Ø¦=ÃK‰è}XVíx¹Š—µŠØ¥ŠË‹x²©døÕŠÛŒ*H'¦Î´¸»{XÆ=ØÊ=\0ï8¼\0¾¹…å[É«†J†ÚtÙùOØe…¹ØÉ‹èŞ\røıŒ ÊDXı§Å‡Äı}×z°“¾ ù)y'Ù'ÃÑÙIÌ(ù[l(5™`f\\Á`¿”ùe—.lY(¹=z—×”!Y%h€¾O¹+‹ù•—`Ù™\"e“ æçÄ—˜º–Kòù¥ş¿¯£˜¸ÿ– ßšÙ#S™¹EIœYû›.HÖJtG·—œ`¾ŒH¼J5»Í5˜™~ ¸€6C‹¥hø˜§ùXDz\n–x¡‚yshššFK¡c¡zj¢Z€Y8(¹ş%Ù|yŸI«£ß‘Øƒ›Úée¡úY¡X»¡™u¢Ú ´Úiœ]¦Úc¡ÚM¥ú;ŸÈ§‘ùò>Ç¡ƒšQ T©øüú¨ [~Wé~Ùcİ‚z›©úµz¥º½¢ú\r¬:  \0èrYû¢x)‚Ê!ªúÉ¡¹K¦ú+§z!£šÓ€C+˜š°´Ù®âÃ¯:İ§ª™¤ú©¢Zgšû~z4f¥¯	¥:÷£’sºÓª—ê+õxÊÂš%Œ»›=³™G–ÛIf3?˜úãø¿µ+Y´úq¶@àûGœúá™y¶»oµÙÑ´Ûp\rª~Á{Wœš¶[…·¹é®yè:\0Æ\\»‹·;e¹Û¡¶YI\"·¸zdÂ˜k©Zö|[uš‚uÏ+˜×¹9q¼¹nR Ë®¥B—˜»Ø×z|\rŠá¤„ık¤^»€î“ª[1ªÛ%‹.“pA­2<›Û=¼Ø¡•è\$é;Ö5œ)³›m¸œ!‹»ÑXXıº‹YÃx¨5vT\\®QÀ%:À¢>ÀàÉ›Û;¸›e’|/·•yÁÅ§ÅW§x× |g®œŠ™ÓÄCİÆ\\‰›ü‡¼<¼9z\\®#ğ.FV;8¡èNÍX7ø×ÊÎ\"8&d5¬P…4Gj?Ê\0Ü?\"=˜­ùHER");}elseif($_GET["file"]=="dark.css"){header("Content-Type: text/css; charset=utf-8");echo
lzw_decompress("h:M‡±h´ÄgÆÈh0ÁLĞàd91¢S!¤Û	Fƒ!°æ\"-6N‘€ÄbdGgÓ°Â:;Nr£)öc7›\rç(HØb81˜†s9¼¤Ük\rçc)Êm8O•VA¡Âc1”c34Of*’ª- P¨‚1©”r41Ùî6˜Ìd2ŒÖ•®Ûo½ÜÌ#3—‰–BÇf#	ŒÖg9Î¦êØŒfc\rÇI™ĞÂb6E‡C&¬Ğ,buÄêm7aVã•ÂÁs²#m!ôèhµårùœŞv\\3\rL:SA”Âdk5İnÇ·×ìšıÊaF†¸3é˜Òe6fS¦ëy¾óør!ÇLú -ÎK,Ì3Lâ@º“J¶ƒË²¢*J äìµ£¤‚»	¸ğ—¹Ášb©cèà9­ˆê9¹¤æ@ÏÔè¿ÃHÜ8£ \\·Ãê6>«`ğÅ¸Ş;‡Aˆà<T™'¨p&q´qEˆê4Å\rl­…ÃhÂ<5#pÏÈR Ñ#I„İ%„êfBIØŞÜ²”¨>…Ê«29<«åCîj2¯î»¦¶7j¬“8jÒìc(nÔÄç?(a\0Å@”5*3:Î´æ6Œ£˜æ0Œã-àAÀlL›•PÆ4@ÊÉ°ê\$¡H¥4 n31¶æ1Ítò0®áÍ™9ŒƒéWO!¨r¼ÚÔØÜÛÕèHÈ†£Ã9ŒQ°Â96èF±¬«<ø7°\rœ-xC\n Üã®@Òø…ÜÔƒ:\$iÜØ¶m«ªË4íKid¬²{\n6\r–…xhË‹â#^'4Vø@aÍÇ<´#h0¦Sæ-…c¸Ö9‰+pŠ«Ša2Ôcy†h®BO\$Áç9öw‡iX›É”ùVY9*r÷Htm	@bÖÑ|@ü/€l’\$z¦­ +Ô%p2l‹˜É.õØúÕÛìÄ7ï;Ç&{ÀËm„€X¨C<l9ğí6x9ïmìò¤ƒ¯À­7RüÀ0\\ê4Î÷PÈ)AÈoÀx„ÄÚqÍO#¸¥Èf[;»ª6~PÛ\rŒa¸ÊTGT0„èìu¸ŞŸ¾³Ş\n3ğ\\ \\ÊƒJ©udªCGÀ§©PZ÷>“³Áûd8ÖÒ¨èéñ½ïåôC?V…·dLğÅL.(tiƒ’­>«,ôƒÖœÃR+9i‡‡ŞC\$äØ#\"ÎAC€hV’b\nĞÊ6ğT2ƒewá\nf¡À6m	!1'cÁä;–Ø*eLRn\rì¾G\$ô2S\$áØ0†Àêa„'«l6†&ø~Ad\$ëJ†\$sœ ¦ÈƒB4òÉéjª.ÁRCÌ”ƒQ•jƒ\"7\nãXs!²6=ÎBÈ€}");}elseif($_GET["file"]=="functions.js"){header("Content-Type: text/javascript; charset=utf-8");echo
lzw_decompress("':œÌ¢™Ğäi1ã³1Ôİ	4›ÍÀ£‰ÌQ6a&ó°Ç:OAIìäe:NFáD|İ!‘Ÿ†CyŒêm2ËÅ\"ã‰ÔÊr<”Ì±˜ÙÊ/C#‚‘Ùö:DbqSe‰JË¦CÜº\n\n¡œÇ±S\rZ“H\$RAÜS+XKvtdÜg:£í6Ÿ‰EvXÅ³j‘ÉmÒ©ej×2šM§©äúB«Ç&Ê®‹L§C°3„åQ0ÕLÆé-xè\nÓìD‘ÈÂyNaäPn:ç›¼äèsœÍƒ( cLÅÜ/õ£(Æ5{ŞôQy4œøg-–‚ı¢êi4ÚƒfĞÎ(ÕëbUıÏk·îo7Ü&ãºÃ¤ô*ACb’¾¢Ø`.‡­ŠÛ\rÎĞÜü»ÏÄú¼Í\n ©ChÒ<\r)`èØ¥`æ7¥CÊ’ŒÈâZùµãXÊ<QÅ1X÷¼‰@·0dp9EQüf¾°ÓFØ\r‰ä!ƒæ‹(hô£)‰Ã\np'#ÄŒ¤£HÌ(i*†r¸æ&<#¢æ7KÈÈ~Œ# È‡A:N6ã°Ê‹©lÕ,§\r”ôJPÎ3£!@Ò2>Cr¾¡¬h°N„á]¦(a0M3Í2”×6…ÔUæ„ãE2'!<·Â#3R<ğÛãXÒæÔCHÎ7ƒ#nä+±€a\$!èÜ2àPˆ0¤.°wd¡r:Yö¨éE²æ…!]„<¹šjâ¥ó@ß\\×pl§_\rÁZ¸€Ò“¬TÍ©ZÉsò3\"²~9À©³jã‰PØ)Q“Ybİ•DëYc¿`ˆzácµÑ¨ÌÛ'ë#t“BOh¢*2ÿ…<Å’Oêfg-Z£œˆÕ# è8aĞ^ú+r2b‰ø\\á~0©áş“¥ùàW©¸ÁŞnœÙp!#•`åëZö¸6¶12×Ã@é²kyÈÆ9\rìäB3çƒpŞ…î6°è<£!pïG¯9àn‘o›6s¿ğ#FØ3íÙàbA¨Ê6ñ9¦ıÀZ£#ÂŞ6ûÊ%?‡s¨È\"ÏÉ|Ø‚§)şbœJc\r»Œ½NŞsÉÛih8Ï‡¹æİŸè:Š;èúHåŞŒõu‹I5û@è1îªAèPaH^\$H×vãÖ@Ã›L~—¨ùb9'§ø¿±S?PĞ-¯˜ò˜0Cğ\nRòmÌ4‡ŞÓÈ“:ÀõÜÔ¸ï2òÌ4œµh(k\njIŠÈ6\"˜EYˆ#¹W’rª\r‘G8£@tĞáXÔ“âÌBS\nc0Ék‚C I\rÊ°<u`A!ó)ĞÔ2”ÖC¢\0=‡¾ æáäPˆ1‘Ó¢K!¹!†åŸpÄIsÑ,6âdÃéÉi1+°ÈâÔk‰€ê<•¸^	á\nÉ20´FÔ‰_\$ë)f\0 ¤C8E^¬Ä/3W!×)Œu™*äÔè&\$ê”2Y\n©]’„EkñDV¨\$ïJ²’‡xTse!RY» R™ƒ`=Lò¸ãàŞ«\nl_.!²V!Â\r\nHĞk²\$×`{1	|± °i<jRrPTG|‚w©4b´\r‰¡Ç4d¤,§E¡È6©äÏ<Ãh[N†q@Oi×>'Ñ©\rŠ¥ó—;¦]#“æ}Ğ0»ASIšJdÑA/QÁ´â¸µÂ@t\r¥UG‚Ä_G<éÍ<y-IÉzò„¤Ğ\" PÂàB\0ıíÀÈÁœq`‘ïvAƒˆaÌ¡Jå RäÊ®)Œ…JB.¦TÜñL¡îy¢÷ Cpp\0(7†cYY•a¨M€é1•em4Óc¢¸r£«S)oñÍà‚pæC!I†¼¾SÂœb0mìñ(d“EHœøš¸ß³„X‹ª£/¬•™P©èøyÆXé85ÈÒ\$+—Ö–»²gdè€öÎÎyİÜÏ³J×Øë ¢lE“¢urÌ,dCX}e¬ìÅ¥õ«mƒ]ˆĞ2 Ì½È(-z¦‚Zåú;Iöî¼\\Š) ,\n¤>ò)·¤æ\rVS\njx*w`â´·SFiÌÓd¯¼,»áĞZÂJFM}ĞŠ À†\\Z¾Pìİ`¹zØZûE]íd¤”ÉŸOëcmÔ]À ¬Á™•‚ƒ%ş\"w4Œ¥\n\$øÉzV¢SQDÛ:İ6«äG‹wMÔîS0B‰-sÆê)ã¾Zí¤c|Ë^RšïEè8kMïÑÌsŒd¹ka™)h%\"Pà0nn÷†/Áš#;Ög\rdÈ¸8†ŞF<3\$©,åP);<4`Î¢<2\n”Êõé@w-®áÍ—AÏ0¹ºª“¹LrîYhìXCàa˜>ºæt‹ºLõì2‚yto;2‡İQª±tîÊfrmè:§”Aíù‰¡÷ANºİ\\\"kº5oVëÉƒ=îÀt…7r1İpäAv\\+9ª„â€{°ç^(iœ‰f¬=·rŠÒºŠuÚÊûtØ]yÓŞ…ĞùCö¶ºÁ³ÒõİÜgi¥vfİù+¥Ã˜|Êì;œ€¸Âà]~ÓÊ|\re÷¥ì¿“šİ‚Ú'ƒíû²‰”¦ä¯²°	½\0+W‡coµw6wd Su¼j¨3@–Œò0!ã÷\n .w€m[8x<²ËcM¬\n9ı²ı'aùŞˆ1>È£’[¶ïµúdïŞux¯à<\"Yc¸ŞB!i¹¥ê•wÀ}’ô5U¹kººÜØ]­¶¸ÔÒÀ{óI×šR…‰–¥=f W~æ]É(bea®'ubïm‘>ƒ)\$°†P÷á-šƒ6şR*IGu#Æ•UKµAXŒtÑ(Ó`_Âà\" ¾£p¸ &UËËÙIíÉ]ıÁYG6P]Ar!b¡ *Ğ™JŠo•µÓ¯åÿ™óïÁòvı½*À Ø!éš~_ªÀÙ4B³_~RB˜iKùŒ’ş`ç‰&JÛ\0­ô®N\0Ğ\$àÌşåCÂK œSĞòâjZ¤Ğ Ìû0pvMJ bN`Lÿæ­eº/`RO.0Pä82`ê	åüÆ¸d Â˜GxÇbP-(@É¸Ó@æ4¨H%<&–ÀÌZà™Àèp„¬°Š%\0®p€ĞĞ„øêã	…¯	àÈ/\"ö¢J³¢\ns†–_ÀÌ\rŒàg`‹œ!käpX	èĞ:Ävíç6p\$ú'ğÇ¥RUeZÿ¨d\$ì\nLáBºâ†ó.ŞdŒn€î¤Òtm€>v…jä•í€)‘	Mº\r\0Â.àÊŠH’Ñ\"…5‚*!eºZJº‰è’ëãf(dc±¼(xÜÑjg\0\\õ€ÂõÀ¶ Z@ºàê|`^›r)<‹(’ˆ„ˆ†È)ÌëªóÊĞì@YkÂmÌíl3QyÑ@É‘ŒÑfÎìPn„ç¼¨ĞT ò¯N·mRÕq³íâVmvúNÖ‚|úĞ¨Z²„È†Ú(Ypø‰\"„4Ç¨æàò&€î%lÒP`Ä€£Xx bbdĞr0Fr5°<»Cæ²z¨¯6ähe!¤ˆ\rdzàØK;Ät³²\nÙÍ …HÆ‹Qš\$QŸEnn¢n\rÀš©#šT\$°²Ëˆ(ÈŸÑ©|c¤,¼-ú#èÚ\r Üá‰Jµ{dÑE\n\$²ÆBrœiTÔò‘+Å2PED•Be‹}&%Rf²¥\nüƒ^ôˆCàÈZàZ RV“ÅA,Ñ;‘«ç<ÂÄì\0O1éÔêc^\r%‚\r ìë`Òn\0y1èÔ.Âğ\r´Ä‚K1æM3H®\r\"û0\0NkXPr¸¯{3 ì}	\nSÈd†ˆÚ—Šx.ZñRTñ„’wS;53 .¢s4sO3FºÙ2S~YFpZs¡'Î@Ù‘OqR4\n­6q6@DhÙ6ÍÕ7vE¢l\"Å^;-å(Â&Ïb*²*‹ò.! ä\r’!#çx'G\"€Í†w‰Á\"úÕ È2!\"R(vÀXŒæ|\"DÌvÀ¦)@á,¸zmòAÍwT@ÀÔ  Ğ\n‚ÖÓğºĞ«hĞ´IDÔP\$m>æ\r&`‡>´4ÈÒA#*ë#’<”w\$T{\$´4@›ˆdÓ´Rem6¯-#Dd¾%E¥DT\\ \$)@Ü´WC¬(t®\"MàÜ#@úTFŸ\r,g¦\rP8Ã~‘´Ö£Jü°c öŒàÄ¹Æ‚ê Ê\"™LªZÔä\r+P4ı=¥¤™Sâ™TõA)0\"¦CDhÇM\n%FÔpÖÓü|fLNlFtDmH¯ªş°5å=HÍ\n›Ä¼4ü³õ\$à¾Kñ6\rbZà¨\r\"pEQ%¤wJ´ÿV0Ô’M%ål\"hPFïA¬áAãŒ®ò/G’6 h6]5¥\$€f‹S÷CLiRT?R¨şC–ñõ£HU§Z¤æYbFş/æ.êZÜ\"\"^Îy´6R”G ²‹ÌnâúÜŒ\$ªÑå\\&OÖ(v^ ÏKUºÑ®ÎÒam³(\r€Šïº¯¾ü\$_ªæ%ñ+KTtØö.Ù–36\nëcµ”:´@6 újPÃAQõF’/S®k\"<4A„gAĞaU…\$'ëˆÓáfàûQO\"×k~²S;ÅÀ½ó.ïË: ˆk‘¼9­ü²Šóe]`nú¼Ò-7¨˜;îß+VËâ8WÀ©2H¢U‹®YlBívŞöâ¯ÖÔ†´°¶ö	§ıâîp®ÖÉl¾m\0ñ4Bò)¥XÁ\0ÊÂQßqFSq—4–ÿnFx+pÔò¦EÆSovúGW7o×w×KRW×\r4`|cqîe7,×19·u Ïu÷cqä’\"LC tÀhâ)§\r€àJÀ\\øW@à	ç|D#S\rŸ%Œ5læ!%+“+å^‡k^Ê™`/7¸‰(z*ñ˜‹€ğ“´E€İ{¦S(Wà×-“XÄ—0V£‘0Ë¥—îÈ=îÍa	~ëfBëË•2Q­êÂru mCÂìë„£tr(\0Q!K;xNıWÀúÿ§øÈ?b< @Å`ÖX,º‡`0eºÆ‚N'²Â‘…šœ¤&~‘øt”Óu‡\"| ¬i… ñBå  7¾Rø” ¸›lSu†°8Aû‰dF%(Ôú äúïó?3@A-oQŠÅº@|~©K†ÀÊ^@xóbšœ~œD¦@Ø³‰˜¸›…TNÅZ€C	WˆÒÂix<\0P|Äæ\n\0\n`¨¥ ¹\"&?st|Ã¯ˆwî%…ˆàèmdêuÀN£^8À[t©9ƒªB\$àğ§©ğ¦'\">UŒ~ÿ98‡ é“òÃ”FÄf °¹€u€È°/)9‡À™ˆ\0á˜ëAùz\"FWAx¤\$'©jG´(\"Ù ±s%T’HŠîßÀe,	Mœ7ï‹b¼ Ç…Øa„ Ë“”Æƒ·&wYÔÏ†3˜°Øø /’\rÏ–ù¯ŸÙ{›\"ùİœp{%4b„óŒ`íŒ¤Ôõ~n€åE3	•Î ›°9å3XÖd›äÕZÅ9ï'š™@‡¨‡‘l»f¯õØQbP¤*G…oŠåÅ`8•¨‘¯ùA›æB|Àz	@¦	àb¡Zn_Íhº'Ñ¢F\$f¬§`öóº†HdDdŒH%4\rsÎAjLRÈ'ŞùfÚ9g IÏØ,R\\·ø”Ê>\n†šH[´\"°Àî©ª\rÓ…ŒÂ•LÌ,%ëFLl8gzLç<0ko\$Çk­á`ÒÃKPÔvå@dÏ'V:V”ØMü%±èÕ@ø6Ç<\ràùT«‹®LE´‰NÔ€S#ö.¶[„x4¾açÌ­´LL‚® ª\n@’£\0Û«tÙ²å\n^F­—º¥ºŠ5`Í R“7ÈlL uµ(™d’º¡¹ Ô\räBf/uCf×4ÿcÒ Bïì€_´nLÔ\0© \$»îaYÆ¦¶¸€~ÀUkïv¥eôË¥¦Ë²\0™Z’aZ—“šœXØ£¦|CŠq“¨/<}Ø³¡–ÅÃº²”º¶ Zº*­w\nOã‡Åz`¼5“®18¶cø™€û®¯­®æÚIÀQ2YsÇK‹˜€æ\n£\\›\"›­ Ã°‡c†ò*õB¶€îÌ.éR1<3+õÅµ*ØSé[õ4Ómì­›:Rh‹‘ITdevÎIµHäèÒ-Zw\\Æ%nè56Œ\nÌWÓi\$ÕÅow¬˜+© ºùËrÉ¶&Jq+û}ÒDàø¼Ój«dÅÎ?æU%BBeÇ/M‚¶Nm=Ï„óU·Âb\$HRfªwb|•²x dû2æNiSàóØgÉ@îq@œß>ÎSv „§—•ƒ|ïkrŒx½Œ\0{ÔRƒ=FÿÏÎÎâ®Ï#r½‚8	ğˆZàvÈ8*Ê³£{2Sİ+;S¦œ‚Ó¨Æ+yL\$\"_Ûë©Bç8¬İ\"E¸%ºàºŒ\nø‘ĞÂp¾p''«p‚ówUÒª\"8Ğ±I\\ @… Ê¾ ‡Lnğæ Rß#MäDµşqLNÆî\n\\’Ì\$`~@`\0uç‰~^@àÕlˆ-{5ñ,@bruÁo[Á²¾¨Õ}é/ñy.×é {é6q‚°R™pàĞ\$¸+13ÛúÚú+ƒ¨O!D)…® à\nu”<¯,«áñß=‚JdÆ+}µd#©0ÉcÓ3U3»EY¹û¢\rû¦tj5Ò¥7»e©˜w×„Ç¡úµ¢^‚qß‚¿9Æ<\$}kíÍòŒRI-ø°¸+'_Ne?SÛRíhd*X˜4é®üc}¬è\"@Šˆvi>;5>Dn‰ ˜\räë)bNéuP@YäG<ñ¨6iõ#PB2A½-í0d0+ğ…ügKûø¿í?¨néãüdœdøOÀ‚Œ¯åácüi<‹ú‘‹0\0œ\\ù—ëÑgî¦ùæê¡––…NTi'  ·ô;iômjáÜˆÅ÷»¸uÎJ+ªV~À²ù 'ol`ù³¿ó\",ü†Ì£×ÓFÀå–	ıâ{C©¸¤şT aÏNEÛƒQÆp´ p€+?ø\nÆ>„'l½¤* tÉKÎ¬p°(YC\n-qÌ”0å\"*É•Á,#üâ÷7º\"%¨+qÄ¸êB±°=åi.@x7:Å%GcYIĞˆ0*™îÃkÀÛˆ„\\‡·¯ğQ_{¤ ÅÇ#Áı\rç{H³[p¨ >7ÓchënÎÂÔ.œµ£¦S|&JòMÇ¾8´Àm€OhşÄí	ÕÑqJ&a€İ¢¨'‰.bçOpØì\$ö–­Ü€D@°C‚HB–	ƒÈ&âİ¡|\$Ô¬-6°²+Ì+ÂŒ †•Âàœpº…à¬¡AC\r’É“…ì/Î0´ñÂî¢M†ÃiZŠnEœÍ¢j*>™û!Ò¢u%¤©gØ0£à€@ä¿5}r…É+3œ%Â”-m‹¢G‚<”ã¥T;0°¯¨’†DV£dÀgÛ9'lM¶ıHˆ£ F@äP˜‹unütFB%´MÄt'äGÔ2ÅÀ@2¢<«e™”;¢`ˆõ=LXÄ2àÏäX»}oc.LŠ+âxÓ†&D¨a’€¡€É«ÁF2\ngLEƒ°.\\xSLıx­;lwÑD=0_QV,a 5Š+Léó+Û|\$Åi­jZ\nê—DÖEÎ,B¾t\\Ï'H0ÁŒ±R~(\\\"¢Ö:”Ğn*ûšÕ(¡×o®1wãÕQí×röÒÃEteÓF•…\$èSÑ’]Ğ\rLäyF„‰‘\\BŒiÀh”hdáÿ&áš‡h;fo›¾B-y`ÅÔğ0ˆ„JlPéxao·\$ŠXq¼,(Ö¡†C*	Îë:¤/‚”öé®HG\"‚ğc€ˆC¢¡Q¸\nFÁÔ„Ò#ğ¶…8í¢F:Ğ£\0œ€Ok¾âDüÆ])›ÏštT8Láğ’¨”æn©`ÕÎ±|ªHJ³ˆ€Ö œ˜ \"Ò6ø{‹­ƒÁ?=I<HGc Å¤FÒ@†,C ¼@jì‰\$LŸ·â(‰nEÊ‘P¢æjb¿nãÎ‘«¶äWá \rÀLqé‰èÏĞsPH€ê‰z\\V\$kÄÒtr5‹,¤lšÈØè<ñ'\0^S02¸0f -5\"ac¼\"3U“p£æ“\"Ü˜©%•®\0'Zt\"96‘Ì9_ @Z{™0Iˆç¬DÀZE@ôÎNÃh`¡\"½` \0µ„ˆàĞÉ¹(GÃHâÄCh¥ ™I¼òf`@ZD¹\$)âKá;ZÚø\0ä/éC‘T>r_R@Oå`1r†TÒ¨Ib\0ç*¹8… ÄÇËh\$é_’pùRÄ•\$®¥Ni^ÊªP/O)¸Â.Å¹T6Ü\\’Ù”@T€¾ÑrÄ…`)øöÀT=ân\0Œ€2–œe«+€9Ê¢\\®—@¥äú‚>ÉPH1	äŠy#Êô¥rú<°a¸eÜK„Û/cM@_.\09Ëˆ““¨…ÔĞ¬B®ÔÁÙ0i†aó\n’ğdea´%|S2ô¿€å#€“¸nˆ»D\$/¹+EÎd‘•øÖ_2PšË\$s,ok¡#ü<‰	²AÂÄ‘r{B”Ù†A-Q4Ò¤Ù\nª\ryù!Æbä±«ñáOÚö@É¬Ák¤¼ ê±\"§rà*¤İ‡Œ’YÒ€/ğÈ‘ a0ñÙ%•.gE~ºù&© 89”áÃ#@M_ À”ı7Käƒ¸J`òX)²B\$¯(	:Ÿg‰–n*ù|†M6PZ†ªHtêJtq‰Cx†[Ú¼—äá…l=\n•®ÅU3Êf\\Ì”JîP	,™:É}TA»SYH(\n¢¸ØI¶Ù²Ä!t(2U\"Ë\\çX­^sÌ	Æ“a!®\nPrˆ`ÉX3fnb¥•©àèJ÷¬Ü&¸zåzQSf £üät¡!T?à9%€(QƒBø}6B°kP\0ó>õg”&~fhUğœr§,¢ p5HiˆÆpƒ„…¢qÉšügöVçVüÏOg“WEJ8â0GìÔak°Õ@N NMÄä°UĞUxÈª­ßS¦x	Áà	ğK‚@c 1yê±VlÏ ¦ÂC’“‚ğ2Q^rP6|ıI^Mª,¦j%dİ`Ü«àüF§Ï\\#%³|ÄC–¿­¡7ì‹¢ÔGÚTN–„Šãùi«H™–ÎQ­O¦ÏÁCÌyB’Ñ\$±%T°‹*á>z\rMM KpÓ J7OÛ·é4å%ò•\$¤pà’é4”°€”ŠÍ‚£¯EÒª\"Tõ\0O€\0’Õ@>	r›O¨]š¡¢xÒ}^¥IÚÖ@Ê Åºqnç…İ0©Bb¡Èµ‚IÉ(¤M/ı;é¦Ê}RN\n¡C£<b­PÔµu?Â=Pe¹C’™•…L^'ìSÔÎ?}4)ŒÓS-ÕÃğ1\r5S«OEóSFœÓ˜©AOR+ÓŞ™+v§å5Â&C)Ù®›KSDBß³N|E\rcÚUôYÊ¾Àê£Väøˆ?H˜)å®Ÿ+sFäákºLPW-ø,üU:’&™ãt{‘®Vo¤·ŠJ”l'¨ğWÈe74Xn GFª'‚®Ş`æÉCcö±%Ilñju6£ßÈÂvÂU³ğZë‹\0*œš¨NÔŸ#ö¤(¼ˆ¨n¥-;|•4«]XÇîÁy'œ °;İZÅ‘ñ) s9ÈÀ˜%€R+\$À°	¿‘QŞà(\"¡_kX˜„‘°¦˜\nM#€¦\"!p~:è*úÀ™°\$µ3O‰¸ÄÆŠª6½+•ƒà\nB{1ğà|H·K<[`3ğ#å®F@èÍÇ! |©ØŠ\0àğ—>‹Œ®˜ˆ[nrMMı+…á®mO_2¹ÑÈ†Å\0«e^	Ì7Z¸&êµBÅJè¤“h7QO%rfÆp ÎâÖ¥mØ¨â¾Ã‡Â4Eàl«úü+•àäV®£iñN SZàWté2WÅ[;ªÀv\"%Å\$^Ö-(I\$ÊÈS@R-&³Tãz¬šk(²–	ä%R8ìuY\0[9-¢ÈÎ(õ)E¹è‰8¡=^¹†¡ÁG˜5#Á¼€¾)1V¦Éb\r]”Ne;&ÌY›`r¬êI§ØPİ±ÜËÁÖ²ª \0Å@Pç7°·â0Hª¨ÃØR­x¾\0000C|än=¨Š`ĞáTT¿Ø\rEhONÈ´Á' Ò&Ütc©K ‡Ü•U5œşÖßÂÎÃõP3\\î‡à2\"\0yó5¢V]¼©6>ĞU!¡@ËhuÌÚ(¼\"E%07B…½6¼dáHN±¢–‘µìij';@‚ÕeËMzlSfjKY–Öó­®-uhó‰H–œ¯smL@éĞ\"r×jÊºéj'l7	ò•(u‘u‹ÑEåÂ•·e¥a†@ñ„+K‰:Ó•Â%n«z Vñ·ˆÑ;ä[î_Vz_­•Eàãâ8†<…Sb›¨™‹ÜÍÖ6gÀ¼:cƒÍşÀ7\nµ¨­ì%Q› K¡7óÜ®BÛë‘Úñw¨u¹5©ì0»”ÖšãÊ¹yÃncnK™‰úæ¦T8åÊ™÷s±ºW=+—=K\n_[p¢G¿Ä·C5¢ÁÖÃ'ÛD\"„İM<\":|Mq4¹¹Îf•sÁx	qlÍ°›‚QPÓ²aOY×E=ûõî6nTë–’–BtœhÄC\0pÿ×@n£ÎD(aÜP°\"„Šï‹'ZN…äÛ¬¢®\rüLNXŠg±Š<!w•¶¸›Ú[û…B)´§)~½×ãcÂx”àvšiÂ¦ÿqÉø•¶˜a¤@KÕğ7s§EQdÃ½˜ïkô÷Ä?\"Ú3-\"UÆ|•½ıíÂï|21D>ß³â]Â­&ŠŠŠ\\hèTÆ³5š\0`Tz¢ás -¼N£¹ÉÙ\"†f¸NåLU¹]n(D©(˜ê&%\"e\\¬—OãÉNæInÛ¿¤”\0ÒĞ€ìÆ•±Ø÷@Á€ÑïVä|RˆMYCÛTßÁûÿbÔUHğp)À€ÈSÕsÀ qÓi±–`Z5vtå‰¸*áOO\nñ(…£İÖëFà¦Ø58Ã!ax@€{^P¾Õ½¸?«°Àeh}\\³j^2ò„L½,6Á.ØN	K…%±•ß–u”„ipÈÈ!?²lŠ‘† -5íw½†K\"VÈØ\\ÃIs¢Ï2!ßğ\$4º5v\n’àèògrÃòNÖå}÷£;İı­Âú‡‚æW%D(pWaë\0¡v'à±6ú®Vê«ÔÆ¿0WÀñ„E4ÒEUlÂ8ÇLDî„¶EÂ<kOŠñHÉßDUÚ	`vS·¬L“Ã!DTMbnWV™ÁCd‡Š)ZeèŸ€¸ö:¾2Çd8š¦KåŞ„ş4®-GübÍ¾wQWæ30\rüf\0Ê,µ`Qhl±ÖÙ0ËPõà0h@\\Ôr·8×ÇT–ğŒâ›œÂ1ğ`¤&ÿŒÌw–Xï>ÈF?‘—|P‘*ñM¤qZÑ¯Œ¬}†Ë0k`‰œ#ÀÕ«cò’'[ÇÖ±Ë|sÉIJ˜î\rŞã¬û¿<OaÆ¼@ÔW‘¬u°TÆÆ:ÑóE^ª²ƒ¾„²!kŠĞÿ„Îa\$È>5ò–u_äâKcCQ¿r-ÑŠä'\rÈiCìœŸ§Ù@8ÎS„PSÁ_XglÒ%£	Án1r.<…w_aÉºÄ³èGhÒ4\næW×Z“ïaBn,\\\0¬±DU\nbbZ'ŠÒá72ºÍrÛÂ¢®–}¿Y>/Àw\\YĞ`^7J«jŒS‡¢•¯ğ¨S.À’o%æJg\0GD,¼Æé>7 ¹’Rî„ˆ¹0á¹¯Æ›ø3¼ß6ø%i\0Sª^Lœ·AÔØ\riòäO<º™Àa phv[¯{œ¥‡\0éE«^xóÜ¼g–YzWÎyGa»ç‹:(”>C½€öÖe\0ãÖÚ])ô3yts_a€7ç+áæ†BúœC˜eT·Şf‚oÅP€Û¤Õ2E·C¾ÚvÇ>Ùwöl–zÛ*pêY²ıö±q°™öØšQâp\nv[|qõÒ¨E[ÑXi€ó¢ì®=²z(	ÈMÛn]7F\r§©Cs4|-} ’˜Ä¿(NU£?,À¥Ú…ı°†âØºq	¸âp†q~ü¬ÿ ¦ê©F–Â% 88·×é¦‡¢\$×Ş°—[¼±µrÄo!3ãı(†°†—g†Æô×¥pJ!éÁ´qÚZ°v?Ñøc­ıÑL£7£Ğ6èü\$‡mö’Öq§í8l!Ãù5­Cš;Q,ÔdŞsFõ-O˜§fÃˆø\$äğ„6Í%U¨C¸´f\"‚çe(jº\rMtÇFœƒèëR÷x;n¦B\$÷¹SSôx'¢õGöşé™ŠMÓ	˜Ë4Í¬'kš¿~±×#9e´³Yº¢Ö~¢ìë­ˆ;fŞ+Îj¼K„9p¨ÉÔM†'XŒ/rt²\0Õ\\ÍJ%Q¨İè·R‡\rĞ²O3¤|‹å¯šù×ÂÏ±³4˜İxF–×ğµs5EÈÔ;Ô’WR’ÒJX›Ê¶—Jì\$şÁwzOöÏ&ÇµÁÄzkS×\nœ\nNUPŒâ°.ö»0À”…bdk‚ŸPåÌÚ	G6Ö+BÜz‡1ÎhQ>sHv³ÃÂÄQÙ EØp‰İMä€)›Ø\nŠ\\ŒÑÜPzÄèí.sÛÍÂ gÅá)a~ÖÆÈ¥İ!(!Gìhr[²*ª„£ªîÕ¢…`”˜~Í\"!âO’¿‰5¹G3Å*qkgB—,\$öãÛ**1€c.»n	8¨¥\$d ´±VSne‹MiZ¶íÅ7Å¾g¶Aù5Üˆ½‚Ú\nú`¶,‰2ºÇa¦Ò¯ÿömMkÊ»´ßÉ¯ğ²/-İ6µ@?#`ˆØ)ãÔ€Šha©Â†ñŠ†á)VcÆ]Ò_= Rz\\ïVR§µ=¾Ø·³(-ãotõ\$Ü¥È\n÷¢‰dSm³yµÚfÓ©ÙN\rùm(t;DÍÁÿp¸2¤İ¶²ÃZRl)Ğ9MÌ›À,/“YixªÑkÑ)’.¤2@S^úöuÚådŠ6¤!Ë>VB’à x<•¸Kt06ƒ‰ò@ÈŒ\nG‚AáP°(ûªNbD•ĞK\n•\"µäcN¬´\rÄƒ.põ€¤'2L•‡d…êŸ²µÑß\\Ly§A=	õÄDŠƒm3Ÿ%Ä@Œ™±Ùˆ¡¥Á8åqbSP\"âŞ¢™Æ®/ÏDzëC&»OûÇ\0007f€ÂD^1ÅXº/ãƒ,\n„÷vçWx%f)ŒÎ' àDdQ@™„I(Ò‹7Y¾Â|ÉİºAÿQ±¸D«—Ú e 8×‡7k)_ ñ@\"\"½¼%à}¸	¡(Ìë11Ø§\rõ¡Êãeò†á?-ÉµH&ëÍäõé\rLÛêâ€'»eÛ®0ÔT×]ÍÔC!ÀemNzì	UzöñÀÉˆ‰¢S“Üœaf¶7˜Mê^CŠD£õÂ(_ïìÃœãâ#\"ídr5¦9±Ùõ81‰Öhf¨È­áa_—Ã—tZX\0èU¼­†{2nn]¾ ;FRû²!Š}>séƒHiÎy#³´…?\"Å¤¥çíÀ>{°®Î/?7îF®òY¯°úª?Aj’Á.†Uœ!5`Â‡HÀæ\$r\0î'\n¾\":.ŒûdÔ‚Ù™ÆªíqÙRÕ­ohõİ>êŸÌ{ç×1‚İ+ä>èËÉ·t†Íkğ%-Dì=9Ê}ÄC@ã8cm’Hr°ï ÁWÀnÊ \0Ä<(ÂRR«8¾ú´YVàÅ`ëppÜ.Uƒe_`®…°¹^¦õìµ›n^ç_ÅR|ßrÎ…p‰7/!M5±ìÅ|…×À\nû&¢Fù±VVz‚‡O­AÖ~Ñˆ|Æ›¶Ğ4NÈ’¿¬Õ”ò¸ğ”g¿yh-¿\nN\"r\"³ôÕGcôsª‘©€D' XoÙ§¥ø‘O„{¥{Y{¯ÆEø=TŠeìZ‘¸ºú•î{\";•HÛÑXz¤t±ğwê*-ºÕŞõU¨çè§wú-ş¤\"›¦<A^¿OºÍT ¶]ƒD?:—şùåû©å…íæ<‘‚p„qõ[¿‰È,)©&`Û{xKIÂI`º`Îcş°0ƒ±ùªDÇy8ö‡ÉqC–­YëCFõ˜çJÍÙnkã[¹8÷É¢ñ:\n^ÛÖ«ÄTØ!X*Mú<”5`\0¯É6Aò2oĞP.µé£aøAH¨¶#x[·—†€â–ïË 'o@¿æO0^äê¨óh|ŞP=+Í)ºd[©ÇÈøX-ôWÂ!Ÿ…ÓèÃ†”/:\"‰0k#XÇ<ôâ°ôhƒCG‰İ @Fƒ(éŒk†ö‘¹l¢&H½F0OSz…ÅwæQ—ı3İÅÙz|+ˆ\r9b½TÅ}'Ü¬wA´\r°nFù‹©”Ñ!Èg0Šlp›lÑ1û+ø|¤h‘kz—Ôi&÷ªuëD±{KÖî\\¾ ¢\$t(¶;èÒäÃ¬şªHır|Bw§D3[Mâ!:(İ{ŒZ®å(|-ÓHy0ê^“'×½…}ï*£üÒöNK…¯«‘Š5KU›²ájMå\"…Âwá–]%üû–{1qÙÈz †Ÿ)]ÑÅ®[k˜\0O4ßıÒìû“UFÀ\0ócâ“œmZEGt‘sDQZã)n;7<’qhlXx§IÆÂ^ÌVîå&†Í·ÑC–`,É‘%£¡1\"@1Ç|Í)—R¥kßşVÏê}S,Ä#!ÉÍGµôá]ı¤ExåİıYTüı<%ÿQÑ¿Û@Úíö…mô¤¶Jcææ™B£‹B iœ”âGñÇf2 Š˜¨cDÇänÕ§§=Jü€I_¶ûğ‚›šî'ŸÌïóiA &,™Ğ{ËùcÃÚ4ºÇoV%„d¡2ıx€e»…‘#s_UÓHåÕ‰W!  =Û·ÏOú<(y\0€.À€G¹'Ï\r‰ğ‰57äpVòº¶(æ¿Ã¾:îç}ôRRHHy[Òÿ	´²¿ı 1åÂøO\")ññL¦lÀñ1ÂÿûíÇŞ‰«û¡Š+<~™	\0¿Âçsø¯ë?ĞB@¯ô€dÿãıäÍ?nÿ‰~Á&LĞ„­ ?ğ«ÿ@:@;ıÈy¾òğQèº>È‰ãÓfü«ù:\0¼tæ+jşszéK,b^áp·ÀıHXÅ?†PÀ\\Dè?v\"£îËü…\"¢&° ?­÷¯»‡tş›`áV?«\0“úäJ„wC1Oğ„“#êÆƒ*	ûş@Ì¿é\0ÃşÁÆ‡‹û¡/#8\"¢OÅ\"¥\0€ã¡ø6NcìÃ¤[ıp@Cóh\0{\0	¾pDOşÀFt£ÈH/!h@æÿL°;À@ÿì¦wÁôIÔ~CëË€Â¸)îE¡©4+¼¯°)”§áEbç?]«d¤í‘\$ä<¤é‡Ì`o¸¾Ò£îï?}°8Æb¾Ø¸/°Jª§Ùo#ò¼ÚIV,Ac¤´3íXa äÈoîªxiËõ£ğ\"æ¤ŒCUÁª‚D°kˆYÈŠé}©\n\r\0,GÆ\0Ê|q»¯ ‚.ÅŠ€ÆÀNqÄpN†Ğ”’jBO\$|Cõp}ŸÆÂƒ4`±ğÂÀ\\*4ÖĞbA¤àó+æD_ôòÀƒÄ™X¡\$Œ‚·‹„@œ¢6\n\0\$…~Ë£æ\0À®Jbİ…¡œÂ U…p”XõiD\"üÛ…ç lgÑt'£ş‘ ç+xÂ<¨ÓNŞ51eà’Â0`ò¿ñB8qŞ\"O-â€Š	C!¦ÒšØmÉµƒŞÚŞ*¸¸f@#6…ZĞ›9 ¤”ZRàÇ°ê¸ÅÀã	HZL€ eò½¢÷î9Â9œÀ T n€Î?xX\$î”0“´%\0002€\nÁy„!šeà:\$ÈQssAµnxKÂçl1' €Nz!p¥À¬.á¹†êcép¾“¤1@‹…)mÍ:@PÂ\0á1\nä(CRä5D(¼Š”PÌ1#	İd7’+\n‚£Buø‘haM	aî\0”>¸1W¨ı¡\0ağ˜¾4 sÒ-×‚'‘jp«‹å\nJmQ¨ş‰È) ");}elseif($_GET["file"]=="jush.js"){header("Content-Type: text/javascript; charset=utf-8");echo
lzw_decompress("v0œF£©ÌĞ==˜ÎFS	ĞÊ_6MÆ³˜èèr:™E‡CI´Êo:C„”Xc‚\ræØ„J(:=ŸE†¦a28¡xğ¸?Ä'ƒi°SANN‘ùğxs…NBáÌVl0›ŒçS	œËUl(D|Ò„çÊP¦À>šE†ã©¶yHchäÂ-3Eb“å ¸b½ßpEÁpÿ9.Š˜Ì~\n?Kb±iw|È`Ç÷d.¼x8EN¦ã!”Í2™‡3©ˆá\r‡ÑYÌèy6GFmY8o7\n\r³0²<d4˜E'¸\n#™\ròˆñ¸è.…C!Ä^tè(õÍbqHïÔ.…›¢sÿƒ2™N‚qÙ¤Ì9î‹¦÷À#{‡cëŞåµÁì3nÓ¸2»Ár¼:<ƒ+Ì9ˆCÈ¨®‰Ã\n<ô\r`Èö/bè\\š È!HØ2SÚ™F#8ĞˆÇIˆ78ÃK‘«*Úº!ÃÀèé‘ˆæ+¨¾:+¯›ù&2|¢:ã¢9ÊÁÚ:­ĞN§¶ãpA/#œÀ ˆ0Dá\\±'Ç1ØÓ‹ïª2a@¶¬+Jâ¼.£c,”ø£‚°1Œ¡@^.BàÜÑŒá`OK=`B‹ÎPè6’ Î>(ƒeK%! ^!Ï¬‰BÈáHS…s8^9Í3¤O1àÑ.Xj+†â¸îM	#+ÖF£:ˆ7SÚ\$0¾V(ÙFQÃ\r!Iƒä*¡X¶/ÌŠ˜¸ë•67=ÛªX3İ†Ø‡³ˆĞ^±ígf#WÕùg‹ğ¢8ß‹íhÆ7µ¡E©k\rÖÅ¹GÒ)íÏt…We4öV×•¤‰\rC+Œ„¸ò8\ró\0a“RØ¾7ŒÃ0æı¹^vâ6ÚnÛáxP\\áÛ°Š@y‹°AğR…ôÌ Èo¨ä`ÃK~f“ôéå\n°{‡f9˜èåÚÎÅ¥…«~†!Ğ`ö¿Á@Cµ.A‘‚Şº.‡º”²ì9¦Ìız”¡\nòlöë¹¨w~Şã\${XHpÉ‰ÛØ­/ÁåÔ¤gú‡¢³=Ñ¤écàHé¡fŸd…•%jö­ºc5¨^cH{\$»î\nµÅ\r!÷4…¡nìÿîî6õ”…cHºéê[ê.6ƒ¤`Ó¥ù»Î»è°\\7³íûğˆÊà¡ÀWÃçªŞ”>ü}ıàñhW´öÛ^ÚÛÓúL©©¿ÓÚ˜à rY_À´¦WV:@v\n˜†ÄÃ¸i‰4é¯0àBÚù¿E¬À*`z|Ú‘àœ¡\"êÁ±úC(m¡ğÎˆQ¦\$X®Õæ•awKŞÂ- ÒM0œÕšÓ^ˆ?ˆ\"\r†€t\rƒÉhî ³æà}ˆ¬­yÀ°zëÒÉƒ«I? èb€wA?‚ƒÚÑ‰Á¤AğÊáŞhd6ĞÆAÙğ^2aÌËƒ¥ôZ†R ü‚Ğ¸Õ°(’,>ÕÈÌ¬Ê)‘ˆüŞ‘àKğà2·ÿ!C¤‡lµ\"É\$!ƒ@[fzXHytAĞ'X§òSÊ’%H\n4&4ĞVSZ_©À‚†„ç3ÓRlMÓM}Ôë\$R:¹NiÕ@å´¿ä,˜¨ªc†Y“2Âc˜g¡p::Ø HhŠPZ`ÏF8®Aâ»W´^•|CH+gôÿ°\"8G‰\0tÀššÀŞŞÁú\n„t’&\n#Œs ¤ÆÔ–¨u¢'NŠª0Ø¨İ\$')Ê/`è	\$\0\\D05¿6Kã²¿=A~B—E±Z‚Ê–©Ù¤µ¢@˜Y­;'n¡SÂŸá›=D2¯¸§lï™ı'/Êj¿§Íxùi&Ğ{WĞ½Y;Èq\"D¤A8a^1\rŞ1÷løx”)å‚„Û*R:p İ*P{ó±‹Â0’rŸÉi©Àõ2f¼ŒvM3×§öÏ—!ÖdŠQ3ÔÁ<\n9“No’éÅ4Sjo›¨”6\0D	æ	ì´áæ†hKYj½«=I‚xU-Šuµ\$‡Ü¹äÅBòåìn‚{…a­\0rağ´\\ N-\0á†ƒ‚ÀÖCÈwZ9ÚÂ+ŠT!ÆÎÂª\0Ô `ÂÕ!Áñ.°ÉZ\nÕr°P?ålğhe>wş0·A)‰Ğ_!Ô7…óª[0ÉJÄÁ>‡TR±)Àx 8pç‰±DMØÃ-E¼pq‹\"\r¿”¢\$qŠÃÃÈTd«Uz±Á—êV½L˜|ÊP2Â§ÿÂsé‰°)­=˜C'åpÙ”ÁE¨¹`Ê‰æ\"\\Á[Â/N§€¤Ê|­Foy«&ÖY˜ë\"YJŸ4æ°v˜{;bˆ×>:å‘]‰9Òà:ùH	[Cv4ĞhAğ1?¯&Âéª3›Ò–g©¯ª„jLÆIÁ`£7Ê€Z^ä:µäk\0­5|ÀÕ Áh'2¤‚à\$½áåÖë`ÇCœ–!£D§ó¦Óm]-@ëRı}<Á 1uºy\rØ…`ÂeÁhÇcÛNÃ\r=ÚÌ´á˜ãp6©.ºûlí°å¤˜6¡ED«p‡}Ç¹U¶óRaqÖ‚y½¹ì¾êÚ;´š†àÓ¼	p=ÚÊ7A©=ê¦T^6˜ì9‘#ª•–Üß6![şE\\ì@:äúƒooÍÄ·#3Ü/ğ^Â)à;İ{K¡. v8›<b»m)W¾¸Ù\ní2îsÓÎ¸]ÿa'oŸ†¾%¼¶¿CâäÏ{éş‘ÖxÈ\\\nà¨çY¡ –ˆ—¡¢uhÛG\0¬hp;|*‡[Ì8‹éÜ+vj~«ÕÈŸè›Û|r‰SË·÷xæLwtwÊ»‹òşÔâ=Šø>×vì©ì\nL1€‚ E\nAğ!… Š}|H'…\0ùé‚pA	¡>0Â¹n*ÁL!„€ŠB	‡»`Ÿl‚\0‰êB!L\"Í¿¿wÿ‹ğE[sf8üoPİA±wÿ)¯ø·Eš‰æŒÈ|Ç#ó\\SğsU3Á˜ç&ı>^şÆ{û”×å_Ï–j/åîbR/á/´ñìLÁ¢\nûÎ°şÎ´üE\$¥ª ÈN@<ívzoäğ¯úßmÁ\0\0ÿp4ßPç.vËÆ¬«¯\$èèMèüNå°:ñ0æo6»OŞúï³¯!ï».¬ûí±Ïøéú0ïpÌ”!°Ï\0\roëp^ğğcÍÌû	¬Zòğ0k\nP‡ ñPúf˜ã| b¶	¼¸%m	´ÅĞ?P»\n¿PBÛÏ6ÿà.èîĞ­nŸ Æ\rÀÍ°£½P<ñpàê çîğ‘ıpXR`\\à _‰ÀÂ Ø\$MãĞ±(ó'¦ğNıîA(~jÃ‚L&â`Bh\r,èOìØ`ñc‚kâ ¶Ù¢ÊÌ)1*˜§ÇelÎÀú²d\0Ñ€~ífÚ»Æ–`K¨µÈ\rn¹'¤üqt«`Â¶€Vœ¥W†D: Øâ¾¬6\rìÃë\rÆÑîÒ€Z—qŒÛ±öxIó©Ê|FæâºbR%kíÀÊÍ†â ä@Â*Àr%\"€òÌ+‘!bX¹rzifÍìòIâP>²\$ÖE&X„%>ÌòBm`r^%Rbü²Bd’m#²a!²VÍæø!dÑå¤ÍçÜ\rËÏRO'€Î§¨o­ç&òS&R‚@ØQğ9&¤–gñõqøLìÂÇLcÉGb{j{g»+ğŞíäÈ±¨\n´‹\0¶D·ñÎš¦(±ÔqÙËÄÃL8Õ’°ŞoˆÀNvª¸\n ¤	 †%fPŠ¼Ñ å\rën›Ktš [)¢Vœg\n\"ëÔ½€N€Ë\$îWàÄ¤d€¾4Å'r¶\r†#`äíÅæ„\0¶ÎÇ²6ë@Ãv ¶»Ìí(Â\rr’„³n–b\$åÄ@ÀÌ[fÏãk7Ãr7`\\	«6ŠØ DÀ\"àb`ÖE@æ\$\0äRTÃ&‚\"~0€¸`ø£\nbşG¦)	=>À[?Ji¢å6geÙ`d°§ªkó›9ë`«€ó†Áô\"€z4&\$n4ãĞz“ª7€Ió´ Ó¹C)^ÑŒË6óÄ€Î\rÅ¨°°´.±sP\r°ÈÓBPşîñ˜A+I8`WÿNHmG±W4nÖPÙHTŸH´—JbTî´}HàOAĞİHj€¨ôFÙ”8N¢¬=…â«Q7´@7LæØfùM“MÒzÏS`5Ş¾ø¤ ¤ Î£PÀÔ\râ\$=Ğ%4…dnX\nÎXdúÉ‹îĞëòOàæ©§¼\rÀ¾)F¨*h• ön Bµî5\$˜¬µJLbsŒM‡9+P\"õNÏSW5³_OÓc6‹à#jÊ\"óÅODn§ÖfgrweçUâ%)dŸVH¹UqO~\$TK`’5…qÀúŒHØÉì\"Îu»’kTUbRCF’æ\rÀ¶µµ/ó\rŞÌ'ïQU»Órf“wU•Œ¯Í4•¿ K0rå;3¶Lê\n›)¤š‚›ÓCRËœDèFåæífXõm[³„ÎÅæ¾µºeŠLqÈğFE\"¤Àxx\r(£î×\$¥4ãS\$)F	J­t\$Ê•\$E(–˜EFê&T|gÂ Z¢¦ƒê8	ZÅ\\\r/Î<îÖE,agl.€ËÅ¥6 ÛbõğˆŠüím(Ó`Z‹fãi'¤ÖV’×(:m‡eå:õ¼Î«v>T×Côæ7rfÖ¦ø­†nÈ´¶ÚR6Ş6ÔÛnRVzHOnàö\r¶D–¼zcú¡Dû5³TW²s‡T AEÀèt‰³7qœn3š)J'l¬ 'L·i‚9iÖ  J\"kí†<Ü@‚<æŞÊÀ¾^%æ:\0ìK(Ò–í–¬¬÷tNõİ,¥ipE-(mÓºS¾~òØ{Kœ+MšÀYwt9šÀíR\$şzÖ_§òÒ²È¡2¼¹ ø7\0¾&Œy}‚\0(ÒÒÕl\"*>Â÷Ø¿÷Ö´ºM×öX‹@)wæVW¹{Ì¼…@,\0Ô\"g¢¤\$Bãhçc.6(FxP\0\\5b?ÃÏ€¬ª>wØdÜºD¾¨>\rë€”Â{¸>¯7øïË`ïÉ‚Â‘‚ó¾¬¨[ Q‚³][µxÏE´`§ú&dL\r‚sl#/Ç…·Ù}ÈY]QÒE³\0[¾·ÜQ„ÂXA‘ã‰øqŠ1×Š—_8\"Ø¿/Ø¥]±ß0Œ;}ĞŞA5†§İfW¾>xJì(¼˜[ Íx˜-v€ÉøíÕ6'LZòRû¸õP”!ğvV0é‘¸ê-0ä\r•7’ïºÙèó‡µ%ƒ#vF ÌÉdşé%Îãæ®šÏømesRXw“¸l³Â.#\$o8ç=˜ZÅ»’‘‡tlI‰×ìÌ.Å‚tÆ/!™Mì{’ìŠÆlNÃ,lKÀÊÇ¹áÌƒ€,Š,€x;…x/iye[ø5”Y¾¸XÉ—¢{h°=¶5RY]Qä¹‡Xy–tÖ¹ÃRJ\$JP)‰\"5‹,¬ÅœŠJËKéŸE}¦QŒÌõ Àç¡È\ræ;X@ØfysxLQx èÊY9b¹?œ¸8D–oÙ[]Yå†Ùc£Õ-–‚0•¸á¢¸än:„Q‘‹¹‡ƒÙê@îÀB«—Kœz?”:CfÀÚ^9ßRÙâGG+¥ë‡Õ/–º]¢š-¦X™¦¹}‹@O§\n'§Y*êMNË*»£¸-œš‰”zDÈz‘¤¸q¤ùé¨ZWª:[–úa—9u‘ÙxÃšm’º¦ºsœvÃ,u…0»Ä‚?£vpÕ»!Äµ|r	 ;û	9–ªwÀV6²y¶k›[–f~Fã«Âw¦ç¸€f6j-Z»³äÿf·Ù‹»_/¶Wù‰Ûk;c‹d¹~,·qÛHÂK½u Æ¿°æ\n\rû ššM¹÷‹• kQ¡Ú!¡@ß¡„Ÿ»¥¡1âÙÍŸ»ùø•×Ï+Ç„7ı}NÉ%¸l^`Ï¹;Bm`ÊÛg¯[œOÁŠqÛŒxÙº›ï¿;}»X½ˆÿSŠø¡ÃÀ8×;´cºŸ¨x7­‡¿Wù NÂ‚“¶J|â.Û•–-¤Ú˜¦Ú®c‡ûü(¸„`øˆaXŸº¤í\n“2ÈU\\È?ûP…\"µ¢zéª£ÿ¸j'Âõ-¬™=RÙAÃ9Ï·ŒÅáÅDôOšß¥\\_®X”ÜŠ¬!•ÜŒUŞ‘œ©©Q‚P\r E ÍU?‚`uŸ¦ä¢-e;½wù„·ÿÌ†˜ïZy§ÚÄšÁ€=˜ÌˆÅ“ÑY–ÅlC™ÌP\r¹ Æ9¦ÇQ°y¬á¬£9µÒ9¹~P+‹\"™ÉEbSv1ªœÁ†üÂŠ¸'Ìåc8FÓSÜÙÍÄ[Îä<è…>T;ÕÏ|õ½ÙµÏëÇĞ0OĞ}\nÈ}Ä9Ñ™¢Ä™#š¬YÒ3Ò]šR)šŒs²2Çù³Ï½=›½C~ Ô…ÔÇ§Í†ÒYİÛ±²kÕ„ØkäKè¾Ø²¿ˆ;ôÇÖ7Q|HZ·©{:9\n÷|5·l5§|yÜk÷\r_ÀĞ€f(\0æ¶	°·	·a\r*\rë|½¢¸ÓÌ‹ÅÖ¼×]qd(v‘‹Õ`¤é\rØ°\r²h3ÖM>93väAäuÙäÅå|ÛÖÅ#æ^Œz~^W´¸^}ä¼%äş…åd\0rÈ‡~iêôœ—ş5`¾r›“9çEŒşIÂ=aê~‰ê©wë	~ieuæšcµD¥uB?ìÌü¬Ê¬²AN\n'ë2 ?å@TİCnõfKDÚKˆMïÔ©G6 ZMIñTqGõµˆ…_3Võ&Tà–`ÚVQïtõEvZ”¾ı½:°¿;æ¤â?RÖ‘TúÄn\r(§¦âØ{p?c§q×?ú1Úğéğ¤¡ğäºšShµŠ'×çb}`Ì-‚`Ù! Ò`ø:/h¸\$FÀ~=Tº¡|µ’£ê­mæé¡3^Áç~Bº^Éçş£è>‰è~SüG§éUéj\rî,ş‹Ë¦µKª%ÓØjf\0¼d'Œ}£ö \næî0e+\nyV’M†]¸„Là(__‰vÎ2jx>\rüÛ7“äP>#d?!}ğN0~”ˆuEu½Aìïïz£é_èõvwyíO6xÛù<×±?©Õ¯eu{ŞP©÷ø9½æk£Úßìç˜¤[\"c\\:™ j°rn‰ÔØkL[™•¾ïõ»¯™¹M‰2Ú¸Ó²Ø&æ\0ÈÀ_|òŸü¨\08 \\€n\n8V’k\\…`?°\nEâZ…Z°YT¡èt¼Se½Yn¥¿À÷H^­ƒ€û•B-@î_eË.Ù.Ü·İ+4ÏPŠ„»b`ÅB8”J}Sú E6l7/%lH_>!P1Ø<&²JÏƒK%™	äÅÁyfX„ò°Xâ÷V™¯#Áû{É†\\™¢Y£”¼E±Ã¤èf'Ê*[F	˜Ÿˆ™¡\\€è˜#…CôV¤,¡!êHáŒä²Ä/À>ÇûÛ€\\¦g×œ\0³Èó pQÆT½==ìÎGŒ=‚öä{£XFğLwû™•Z;à&„4_`mT:ø‰À®µà`(4i[:°½½éŒîdøÊ Ò¬­`\"ORD VÄñEó\0†!Äù}Ú\0úeVÆÄ¬I¦¹S© ah¶”\\@”ŠÕxbøÜ¥ç'lµö/™}K–ï¸}@õ]ë\$x\n—ª\$¶7Ä\rAı\n{'âPP•€D`eˆØ‚U¾å ®J	Š…T:5ƒ|©dg9ÕÎT™ÚKòğ@3¸ù.§81ŞaÀgÎ<Ù@bîŠ½À… /ÒÎ¹ı®Piâä&¤tE­è<rÀ€DpP#Ä‘ˆÄb˜ªİ¢Íš!ÈQqK‘c#È¶FR.'<f\0F0Ær-Q¨z#AØ¸F<§dgáû8Á\"^5±w|lTÓ7İˆ¥\\[Ü{X¹ÅÂ.±€8(\"ù0Fà«‹¬f€vpRßÇâ &pö¢ôpP.Ô\$ A„uÑ.`€2¡ƒ@ ƒ@ğÀ’†;âíqĞìdw\"u(:jœ]\0Ì •ˆÀ )‘iÜ€~w ¤£¼ªÕ`èŠpg\"EÖ/1ûÒb•İàüEµÇqÀ¢èƒTUŠjÀ\$ğ¯ä@Ï¸ÛªQ+‘‘,hÎ¸W)n=Á‘hL¤^Ş2Æâ9å“‘4t7#€.\0ˆ²;‘dqÈRÏ–óG6Fq¹‘¬‰ã\náAB¸·ãÓ8)¶TÒ2R‡È!ñ	€R‰—ÅN9¨'®.ØÆÚøËÈù¡`{“0À¤Ò	5œ’n	äkBd0°»IĞ'Rg4Àá'¹7pG|ŸÒE\0ú„ÂP\0_”ƒ	(©6Ê&Q`/”i:¥yD€ü.2“T¨ )Ğä\nTÃ>\$¤e:I@ÉğgÃ¬	“\"˜®¹5M 0“ìke'ÙN²*L’¸“Ä®¤÷+É8F^Wòp–	–ª	Y8@^ø€HŸp €´A ’ä­]ù[~YŞ”Ì°%¼b ‡Ë\\A<²¥e'ˆ[ÒĞ“x%:œùzòIjÙØÀŒä“Á.`	|3ÂªÑhí¦H}€e™‰ˆÏ#'”TA÷Lˆê³g‡ >ª®/ƒ½Ëè@×\"}!8‹€œ3æ0 C¢bÀN'0\n@TÌ\0¨ù—T\nÇ=Ë€ˆY•„\0F=`f\\P,@	€A™‚–Å4é¤M*i€N#©€O2™•Ì´Q%™„Ì¦i2y—	ÀO=4Ó¦¹3ÑLĞÀ+…p\nfªœ0XóàU(æ\\ùªg@LLê\0Š\0%  ™ÔÏ›5°/NoÀ …pÀW6ù«kSOœTĞ§8óæMªqó`øÏ}6YÄLşh3š À›8À.N:r=áÅ4\0%>f¨œr{\0@|°¦\0= (›À\0ª9˜“¸\0[§R)ÈMâg3ÀpæÍ\nà1Mbe“TÏ€’0*LÂe“Ï<ô§59\0Î–hÓ[„÷§t \0ªwS¬=0\0zÉ\0€@Yà\0‚iàòl“?”ü§é7À&\$\n“nCôãçÄ	µÎBf\0N=ì'dé—L¨Ó4ŒÓE59ÙÏÎºóÑšÖgÎôÎê~Óølş€¥?Éÿò€ŸĞ€9šP&€ô	œäûÀŸ?ùÏrƒ“*š{hC;ÊĞ6~t*M\0@©BÚÎJ†4š-'75`Ğx4:Ÿ5§ÔŠÑ	ô.¡„ñheDÚQBtBÍ(;CÙôQ^‹tI ¨™:j'O‚‹3.ıç36`Q³ó ıè³DŠ.Ğr?=äÓ†J•°\"|%R€p~–BdÉx†¹Eá3e;‹ú*T¹ø%k@2Âû©9.‡\n³yÔQUX@ÅI\"RœˆdA¨¤ÀU>˜:Jl‘cÉ8¥ƒ\"s©lW\"».áÀ§ÓõK£XVÕlBpö†’²b¦PnN¹ôÒñ ËL˜Ù†ÉÜÒ©Eà+¦i;u1 :R\$\r€lüÄÅ\0Ğ'ˆd!Æq°ÅD	Š¾bÀm˜Å7ÃwN‚†Æœ¡÷#<Héö(\0SXCu§´ÆÃVškLVbêª¤8îé©Nx1È„”é§Xw×â‘@Óä”ÿ\$a£ªN\0.iJ!%D@çPªzÎ¢&l¨=BiåP°-ÔŠbã|Z¶jR—Ô¦êÅ;KáQ“‡Tº¤nfLªRi@\0Ômá	©Êv(­¸ÔR§´î5`Ìê6A`û¡ùâ†™¨ğ¸ên-€èÍ95¨äÆj‡ÒÃDM¢¹‡z°jnGà3¸!c’İÀQ> >LØ	à|œLÎæüÊ\n:mÅÀÅ9*°(	\0^šìÚg}<À#Í¸\\à(Œ–6ê€€²x¥°ÕQƒv)ªÅÎVoô	œÈ7¹¾Í\np3ƒ­i§ç6ã²Öf³eÀ¬õ0g×A™úÏŞ Ap(M? 'ğµƒ¬íSU.2X¢;às NŠ(óA¹ï\0 øÓ×È¦Y;š+M‰¡G›]f‚˜¥²·× ´á]0ıé³JŠV\"•ÍJ}D’\0î\0Á\0S©¦Õ*kĞo:õ•â}4µM|&\\‰ESN¤w©BpUïSu“×”QgkyE{\0N\0 N\\6…tµ®µÒ\\?D%¬:ï	|®ÿª°õ'®2ºk‹›Õ’Äv&§2ç^íb;ÔªÄï…±Z\0]cz“XÆÅÁ\\U\\*äáØV¼¯ïjš®øKÀu\rkû€Æ´§ä°\$î¡¯©“@÷XZ\0¸ˆ`Eñ³P¨¶¶¶Z¥—	XfèY€aí²%Yì·eÛ4’ÍB7ªøÒxWÊ›×\$¾‚Ó0Şƒ™cğÙYê¹s!®q›,ñR\0…§&ĞE…°Pwì_•AW¥à ‘Ú™ãÄ~	Æqâš°¤ñì ^jù\nhK#ªèl7g«>Lp¾1EØBŞş««U»àE&V-‚Ù&:_5»Zİß\r¦‹„EÑP\$‹X2Ğ¥…¬+”Ï]W6ÆÍ¨­ITÅ¦„d~o·¶MR,ª²PYéIOŒÀ•Ü0+XÙ–×Rº ´º	fb«¬¶Iœí\\BzÚ—’ĞV­Hy	ìÛPºdÙ>ÏCû·U<‹` ò0:¢¼ô«ZÊ¶n\r\$Ao%FT‰³HT\núÂ÷*ì )ÄÒ*­¾9¨Wñ<cE\\©Û'ÁÁ1*i¤E¤:à¬ºÃTáğÊä‰l84ZDàRö8À\n@zç”ZÌ>sp…;Ü@Wq¨¬\0•ê÷(\në™€ûH‚'Õ^œÂ¶Æiî\0£µÓØ\0.;XiÇ`Ò¼OB®óE¬êòé›Îú¯Ç¡0U…É<p>‚m`?\0QîÎiÜAĞ\\©ì\n\0‹A;¯Ğ0ôu®cçó>ÃÚPÚgU¹¡\$ğ¨õDJ#OSÆœuÚn×vÚïª,áî´@ +gzİ<ív©º‰hªoQå,•ø\n…ƒ€¼´Ú„êŒ.åxéÅV‰/\$¤¼½‘ıPYi	’Ş ª½b5ˆyNfôXæÄµ+Q mBŸjµÌo•¶ˆj¥NÊª.øÂK[±j½`:ÃªÂÆÆ¾-s¬wo[æA-F·Ò®p‚i06Ò5´¯  ‡@{{€/W¸e\0kä¦s–:aŒ˜X‚eú—Áà?<@À(M™£®t)¬\n)aR¨2>°ˆ_!ÁqÛzf3¡\"¨À\$Ş%šE´a,Ä€RÀÊìG¹‡ÄgƒK›nÓ2!Şfá20_ü‘¸	ãXüî‘‚¡”XÑë	8r¨©¥ÀÍ£“ ÊFİH| `°*}	Ax…#„EÓ`ÔÃXÁá:†S‚ƒ¸Œ¥;#)¸T[’•tœá9T)Ì˜y„\\a#ƒZ)¸×mÉĞ(¸5 T.#›W[ÃØZL¾TD=\0¥}ß¤	j‚F»†1¯0ğÁBJÁ˜C„+‡U2ç5áÃ‰2-JF8ªb*+Â€İ†Ì+4¤Fk®MhŒo¬8aÉ_ÂõÑ¤‰ˆ×UC\0Âââ~¨@ìd…£%«B¦%aƒñ>2…İòÃî+ş\$˜yŠë&¶l°ÀÃFE“ŠŒ7aãø¯,F,q	‡1±bÔÁø·imóN2ìcÄ+¾1°ÅŒ‚Ÿ`ü5˜;Ã Ún!‡iİ'ƒbÆÆî!ˆ`¤Œ¥4×GÀ<Hñö¬~â¨6Xøª.0ô\\>´„øÈjkq&LJÂ«`=Æ>ämYº³¬Ç\0~²ˆp9Œ”~˜ïHJß±ûägëãiz±\"¤2²>c9“¸Ğ`ÄUñ¦a`/‹&«ÉÖ+q¸xr»yf#rÈéh‹'’Bd5¹%	rU”Ò-*‹)¥CNQDg“ÆÇ’Õô¤ÂÎ2¤#<ª\0ë*Ãó…ú½o§OÜyàó!9^&X1»‡eó_‘1ºËÔfî­šD\r‹x+Àn8¡…ŒÛxøúÁæarğ¡¬Å`w%S™UŸù‚LÅ‡‚ÌTSÃÁˆ<9˜ÉpXæ0\$>±¼!Ãë`@Ş`2İX\r™˜\nğ sV-¶ƒfĞR8\nò°r	†eçh~ù…\0í@Èj1\"‡Ì‚JâBW ñ@\0Ä¤X3jÉÉH²F’<­\$­\$ñ%Ã*òRª„&¨ï€\"Uç¡îÎL 	¼ˆ@¥˜\$Ã|h@…´/”¾\0YeM´¹<ÀİQq,<Ïáú&Q33\n-¥L¹½ìplÖ½ÿs\n¤vCÛ¡;'E¦5s\0—†S›	ß¦aeAÌÂÆÅ P&hYê˜ÇÁ—|L8Yà;Ñp–ôc¢â°è£FŠƒÖy„•Š‹˜Hx¦`ğ‚ì¼fü©6ª)prYĞ0k§Ll8ÉXş“Ê¬p6,¾äÒµ£ƒô-TTZ]Z(tÂXPéL\r…ğy3F24å†%–Ğ˜ŞÆñ¥\\ØUxI(¸\n./\0Ä\$I·TT>›E¶òdéÊ&`1	˜ôİ§`ãÆ'‚ŠoÁÆÓ§ïéí\"e;©5°0áµ:†(Î-©0<e‹d^§à;êSú„N©5§pj7Qù¨Ôˆ5QMÍGlã z%nªõ1SêlÏUÁ^Ôî buMPêX:¥Æ¤—~İRkX¡t§ğ-«À:, †¶uÁ­hêB3rDĞÒtß¼¾p\"‡xÀ|§+|ÕªÂø3nzÇr†™ÕB²wÓBT‹Ğ C¡Êæmv'IiöFÓQauñ¬’è‡OB:!¿RßæºÆü÷ü¤v›ïÚ’ìo`¤Ğ`‡X%°öÂì<ÊÀBğEn)@j\\†Â×ˆn[¦M~ëäÁúş9q3”\0Y©SóÃÔØÀ˜à:mA\0w‘M§ê¶jëÇ8ô¢ÙA³ÀÙ¡ıß£bº\$×~šŸ“¦AlÓ_Ã½˜J-§™—ÂAŒZÉ\0006È§Ğím£O[j01	Àp’òlËn`yÛY	öÚQ=œ3›LíèÎÙƒ´^A,íÁ¢PÀÜHlw’²‹ëpÀoÁ[~G¹jîNú¥¨Ü‘E·*P±y‘4\rë(Üèv•Ó¹€ƒnOi¼Î’¶ÍÑd_v%k\0R­–\\n@Ï[¥Ü ÊGT½ÎngsÙ-ZŞèvÏ¼Ô”[tû»Şv·V^@=‹h¸%M.ÉJaœ1;îÌ;[²Ş@7'¼±'îór¯®)4@Ôk®İ…âäl@L|>aïVê\0SN±\0z‹×j2†Ÿ-Hx€ÊW ó‡Ó\\ñÖÎıL%¿-úkz¤LÑ+&™Í‘hŠ‘ÁüÖÈ}”Åeİ•ä{U/w5¦´à€¸Z¨Š­„€1X×7×øÌÈİµlÂ7ŞÏ7Ğs­«ŒÏ†#*<€ÉLmƒd>åwP%ÂÛò)‡”}ù@C|ÏÀøÓkØÕy†RIZk,÷Šœ\r+¾´\r–)*Åç`›¢äœpë5¼¼‡‘ÀÀÉÙ~xw¸ñg>¾Ï”©±=‚pK†AÒ8W¹=jKVÚp˜Ò}¸?®}_.üÎÚáÖÅ4Gf*`_!úä`µšÌŞ`h5eN©¿&FûšÁæëÄ\0«xÊráÃüi—®2®˜¨,Íæ+¸„À±“8ûH›;pGkf'R ÊÃ£àïÅ5zƒİÃ·/HÿËõès	µvoEóõ3SòÃY®ú£ìÎ©‡h{§Ìy9\"Şå¾­Ã‘¿°óoô«å*Ğ	ù¼/\rò\rÀ.ÒE¼¹ÛœùÉ‘æ=À½iˆ°EÃçSz8ÿÈ†İòTrÜôÂ0ÁˆO1áêkƒÚÓç>¢*i¥lë›TÚèV¾º¹ñ¨ş\0÷VqOFõ£È=DšPŠ4^üø8Î#‡|z@Eõ…Á`@WX¦ÃqrÿH®â›¥{[å¿\$³OªlÕr_L&ôÙĞÎut;–ãÀG*EÃ;×r{<¡Á*ì¹!tşL¾{“\$ª•Nn³5ø1fr˜ü°ÁÁI¹g‡.jˆ{½[)6AÆÇÒ\r8¯+H7”ºzÁyÀCêp\0=VW}Âà:dğv\"i·Î£ú`QôM9£ÁæÑÙF+8ãƒİ†Z·ÀàÜÅ¥|\n X†ßÙÉÿ±aÍvl\nƒ˜.§f;ø>¸_#4AÇY_©!ªxÊø+Ù^™w¦ª.\"i.ÀtïD|3Ó¥8z}ÉMpu“¹“Á&zŠâ‰õO!UÁ>^p3Ë~àl¡znèo!µwÑ>D¼0ßy1è(w¯\\•ğë»Iîö°¹ªij¢t(·'³Ûih×}¼¦êh3	Õ\n›¬w1˜õÒB Ä·‚¸`ó–´Ç,^fù)™ÜÑe—Ù¢Í H]Îğ¦D\r¥E 	û²Fî`&/}\n™ì:D)Ü-\nõCD¹Àß_~úŸÇÌÃøÅÄ'éC\\ãÉÓÆAõÁ_)ôâö©«h DCï§}·ZD¸™‰¦>G<vLèìşå7ˆ–häNæ8´`Oóyh™„KïĞ}tPK1xûµ=Pó3|Úp!pÊç\0(YØÒ\0V4 ,ì…¸¾R{GV şš\"°¡¥0^Ã·m+€Ö-æH—ÒËğã9}åâwhÇÍñ}F/pàÄœc>€|{£ÇQ^\0ïÎ‡Ğ\0ºqÖ¾KÙıªÎÀàq¤d“ANõ¦F04¯N\nsÕœ`\$y†ˆ`¦¶õ»×İwHş<z\n=@0aÁnœoGÿw†”òæy7“¼áè+ì?şD/}@ï¹3Yh»Úo/'¸·šû?{KÜû«İ¨5Å¾_{«Úào«Aîây£˜@ÃŠLçÅg‹ÜööäÍn­o+3;¶M7¹½ç¹şùûwÈÛqá¾.~½íå|_uC)ï’½«£Üó–_ıZÆ¾3£Ïuüsİ·ï\rf<n\$íS#z,òØ8úpø|+êcaÚÇğT€Äğ€‚TèH7ÙãÌo½|Ş!‡†BWáÀôx|š(ñÂ€YÈ@ûLŸ=‡Ïşƒô00à‹ìè©§¦Eî¹“\"¦BQE©ä_oùÚ^+Ò_ÏxS·¢§4ËëzXÓ—5=0kçpÇ¶ğ…ÅøÍ‰·Iß„4ˆ~¿ë:pçµûZäxßì6Gà÷Xo—SıÍ®pdÙ§S\0LÎ%‰Ü‹ép1}9OŠ§®Ë…à²øö	Ÿ#©ˆUQ´¢ø\"èc¢bÃ~Ø77ş„¿7û|Gƒé™–ß÷^‚Ò±xèº¼¼n…è1ı\"U•ŞıâÛâàG…s²j“P| #ÿÏİğNàù€Ç²  îÊåÀF?øˆÿäqH«ı²U¤`Öá\"¯h?ä¶ë²¡ŠlÿÃç¡ƒ\0(¦Ïÿ<¥\0„ Û› !P>`»\"ÿûşŒ×‰Ü3jà;˜@˜D˜?Rß°ïŸ„šÎh?ìÖz	¡f¡¬Œ¶Ñsş…‚›eæâülà8Âø»ßbË¿·\0[w…ª>LöÀLWÀ@Øm&­rËI°!sæ ¾`		@Ûƒê0”\n@‡@”û°êĞ,@çĞ5ZCù`ï‹¨æ°LÖ:ækÉNU†âÍaJq‰êÂ·ĞlóşaÀÜ+…\"3¶ixË*´,Û5?5oD!„‚Íƒ€×¢Ì„“Š‚Ó[n¹´ üSõÁH¼Të¶î‡tó’lŒ\$	6)–„Ğ²Ìó@Øéƒ·.šæíà«¬EâJ#%@£ƒÚ“V%	ÚNÀ\$ÀÀG‚ÿ‚“ªM\0>®/¨4É5€æ”Ù=©\0^RKa†^§TÉE€ö—*Q¢í¤bViF¯ş”(zğk¶ëºP}•8“\"Y€™šDÜÂú¥ä'‰{Aœ•JMaI¥9pBPx¥”Ã »AåÂVp°ò¬ÆN»Õ\0BØ03\0006 Øø(à.íIÏ€†|`(ì~\0Ê¦³ÌÁ5æåPÉ†g\09JKÉ|i5Á¦“Ø`»€yhW`¨‚¦\n;×\0¹!ªYas€\\àz`>€HH*©}Á¶Ü¢úÂ0”\\¥:”¡IÁ8\$#‚A˜;9ÙÕÁ±|pnAz/¨Ôğ_¥\n4Ğ¤‚Á\n`(PeBŸ¬0©¥i	ú˜eBB„t«­Aq\nPğr%”b¼gA×l\"xA÷šP{\0T.Ü¡%a	§É5…\$“ØI5Î•’–°œÂv¼'°mAhvXÄ¶Â‹€¹·¢WÃ©Bî|Ã©Bô”¼/€•Âı\0.°C¤0É6Ãœ1i7Ãİ¼1ğÈÂ«\n*á—ˆ¡tÅN%d²M°”†}	X\\––|X˜ÃoL)?Cy\nâW)6BºSñ9ÄŠ\$ÖÜRîˆ.0åÂ‚U¸ğê„\rºP©;ÁØ–2VPŸC&/¨B@„C~”Ü2P^BÔ_i\"pçÃ1ÔÆíÂëò^pĞBò\"„ĞÒÁô”¼/à¹ÃS ©.'¼/0h'	<=0øÂ­˜\rêuCí,PÑD\nRSiFAóŒ@ˆâ	à'‰e‚Àì\"a‚ï<=c±¼PaCñä\"ÏÃB/¬4py·ìqÃKä5f+'dñ\0¹‚Mà\n\$ €Ñ	Cq™l°òDc‰Œ°rÄi´?±Ãt/qÃMì5\0»DöZ\"J\0¶Ğ©nÂÑüq%¹O0t¤×	ÜI¼C–3”>p¡ƒæ€À'€ƒZÍƒjã»®¨xA°ÍŞà„)ğ;`9€^\"tÀ„/‚ZÆàBÑÄ2‘= èñN¿êœQ\0003€^ \n@€°&`	\0”ûÀ „š( ãˆØ`ş%ü€\\-Dál2\"©NDşZı…3]*œo°…©QqI€ò™p	F\0Âœ%Âz”ú«ˆ!a \0Êxí«¥ädÃ™ÑG\$¼ÔY €E¡8ñi‰Ü R\0¸€Ğß1I\0cdYÑq˜(5ÑsÅ­PÉ\rÅ±»ş®#ÅáZ1zE©DZñuÅôô_†8Åÿ|`1sE«L`Ñv‚.H²Æãh\r—\\^@©Æ´] 'îhZ‘G@`pŒ]ÉŸE#,[àÅ lQNr+LeqE5„R\"!—\r¤eŠˆ¥­xSQ˜¿t(Äe1>%9è£\0;ÅA<TqRÅNdU#!ÅV¨À±ŒF¬añkÅ³¨[\0/Åºy#\$ŠÆw”\\‘‡F	Ä[Q¬Æ·è1”ÅÃdl1ªFÇ¼[q­*x4QÏ¡€ËT^1©Æ¼n³Fæ5\0à#\$eÄ`¶Æ÷Lo±nFÿp	Àº©N”p‘¶Eço‘²Ç¨¬QÈR ÑŒc1iFİ¬kË†ñ´é/í¹¢°ùÃÚNh@Šàx\rà3¸B”\0à8\0^/øB@‚T§\ra«\0^ Ìw±Q‚»ReÑ”µ\\H¡¨áM4\$8CqâÅß:`ƒ3Äòiòd0‹€0¼(`>B‡PÉå‹­Ê®¨aŒv8'PÂ-d\$\0Ö€ää#^&4,}*K]ğÏ‘\\Â&¼X	¢àb@é—^ÛXõA?ƒ\n@6ì+'| `Â5? 3Ú\0˜pJ\0œ	\"…@'€”ÒH= \n\0\$jŠf` É úfrˆ,oXafõ:q`¬ÈZt‚!Hd¤Ğ¼dØ€ù\0Ävõ¶®;~@>:8`¢à>0<ĞT‰šZ\0\"JNÜ(+ ÂFĞàQØƒ!tnÀ>\0ŠÔÜ}qÁ0Ì¬YÒ'ÆÖKò.\0ætŒ@0µ\\áHHà>5>äq 6ÇŸÔoî'¶Z8¼qÍP=©´ê¼‘1ø7êÀ\0¦èD*à¼¬ñÄŠÒ,H¬_ŒŠéGÖÍÔ‘!j_øRFÇÖ˜,\0ñ\$“^Œ>É0Lp	2Ll\rl“‡ê˜%1ï\r&~„]IC%L”„È\$•H;À>ĞÀ,€#áš¡,É~s\nÀ#6®l—™¤((ñªM-¢f¨ˆr¤e\"5 \r”\$8T åÈ‚0ùoqí-Ä(;A¢ƒı%‘¦¢Æ&sğ—…®§n ÂƒZ˜\\–²;€¤ZÁ˜\0ø#Œš!,…JÃìÀ;Î80ª°&˜¬‘À¬›(/}c»Ø/@3Éù[çà2\0òsñuáorÀKi*FhÀÒc1,£\"&j†|w¦j‘<®p\nÏı\0ï%ã¥Ê(\riú íˆE(ğª€ó8)HÈÒ4¥8¾’Ê§”‘Ñ›Ç\0G²Z‰\nÍ¬§ÊFƒÌ~9H)8?Ïı\0È\$€%ÒƒÊ˜?(-‰‹p/#À:€àÉä™‘j[ü€)„JĞ¼dp…K\"#’«Ê*Lp	€>\0ìT©ÏûšsôŠqõ‹¨]ˆ¹ÄÕŒ.£OÌÊ€è1\"‘æ ëÌeÒˆŸøàf'?KÔÄ°Íÿ&bSé< 63a+“2ìËü’ŠÀÆX +…°/|}rY¦,ü–E—\0ÆğÓÃ‘öe,C \$Ê–+Œ°\"º	Y*ZÀÊ+%ø€'Ëé ‹`KH\nÊÎAˆ©Í*\\·O§ËxÔ˜7!RK‚ğÜ·‘uËr²Co’áX³ä‘í}.‘ñîIò/\r2»(c(Ë&ÊàÏP®ò¸?ôô\0í§@\nó±Òˆ‚\"3³c\"KÎ£ÿ’·Ÿ­'¼LŒ;%¬©o6ÊŒ\nÔ¨ÀúÀ¼˜’\0í…±-xb IŞ+¸ ¡HIB\"€IMú&&!TãL04£3ƒ[.‚bÆj’¿/,§¤¯ËÒá+ñãŸ ìÑä0y/ˆ\r5b't¯#OÉx4«\n¸\0ÒÀ-†„„,ÁÀë…NYê@6L8h\0…L{0üÇŠqËÛ2\nA\nqÌPd	hR&\0ÄÕD‰‚ËÂöC¼­¤¬¾ıô¡\n5çàEòû¦&,¼NÊà•L«Ü‰p,±İO-3`RìKµ3d®jËµ,(Bó8Ë³«:Ìİ3ÄÂ@)ÌØà\0®‚K_-d]‚¹ˆjâ´°¥w13ØÀsELí3œÏÓGM.ÜÑRÿ²êÅ|.À\0„¯D– ‡HQpBB‚\"Ö\$™Ò³c4œÔ°M3ÄÏÎÒMU4œ«W3a4´Õ0DL±˜™f…5¤È 6\$(|¤(Q€ÔÕc†Å6+Û³?M5„¢³eÊë3à5²Âƒh;´¶\"ñƒ‘2ü}`äÜÀüÄŸ¦\\Û¤	‘ÚáDÜ\0äLÔ,Û°\"´ÿ7H¾?ı ¾«MºÍr£Suƒ‘7 fsSMæ	\0fg:Mï7à#òÇÍö§ÌŞ@ä@>âº­-±4¶MXK¥€ù+°%R\r¡°\r€!j§4œŠ+/`À,N8ò~5>úÂ¬Z²Œ?ÚH5¬#…TÒè\nEªM‹…«\0¨gl>7¸?RZªN]|¨À<‰İ8ó¦‚w'èüï¡›ÉÇ9¸Bçç½8ğ6à¯'èˆOê«ÎpÚIg ü€İ8ôê¡\n'èÙÌ¡oÂÎr(za*/Ó'˜E²µJEĞF2hHù3Aú>4Ã;d–2>J(§Ìî\r¤Èø¬“²ÄP€\n	N›*\$…´ƒ¼ûZ¹Á¢³PÜÀó¼€*i0­2°©2Y\$ÃìòB¨Ì!a… jˆci+é¶)Íad…KHT #Ga<è¢ò°È¢„»j°[8|¦í«Ê/›^aH)ÆÕtòşOldoL <¼ Œï<Ô¹\nÉ¬\"¬²`‡\0©=\$œêGO–4ù,ˆOLnd”I…€©>œ°Mÿ\rÈçTúªGO·=ê@)Hƒ>h!Óñ\0î+úrõ4?*YtÓ\$ƒƒ(\0!BâU%¹¤P¸7AúÁ¼.Ü2Çßü°3ÿLàIÀ•OŒÑÌŠÃ2[jñ‹Ó0\rÓk‚!ğB€ÚĞ'0)š¡ˆ—r<dl¿×˜¢/HĞ2 9L—:h ,:İ:P ,ĞW )ú»“ÊÊ^Qõ€àY„@€c2DêÅõ‚\0”Èñge0ıSPG2µà6L–©Z‰§1L§“ ;ÚH&¥—\0ÙBt­à•‰+tÅ3^Ğj/×¨„€ñ:ÄÈJqĞÅ:İ 6PÉ?tëgæH\rm§è„ÉC\0¿@‚ĞãØ\r”8Æ~_\\ıó+Táí–Î	P2p¦+íLÆf  )\n	Ü_Œ®\0†\0ä)ˆ£³õg&05óPŸª¨È‡•ÉÄ\rd†áj\0îúÎß‡m*(BòSA@áû‚Ó6?{*(ÇsMLì‡Ğ;71ñT\\(!‘ØÌj•\n‰/Ó<ë5hÎê/àôZNE¼×³\$L{E¤×¡¿H”…Êq„:Ö\\»s^Ñ˜ÕÀ9Q )€¥@ÈÖ#³nz™1(5tOJ¬ OòİƒY6´·ÒØÍºítÜ2¾€É;ôÛª¹ID¶“nƒoFÛ³Ï6’˜Õ\r3nµÕH4óa«Mº\r\\¶¢Æ—h§½\n)â[,Œ€&¬™’ÇÑ.rhy:f~™š¦G&4bÇB\r!a¡ô’‹HÈBò*ÒbH¬Í‘Á)ÆÍS6\0O0¼a€›0K&#Qß3e'àºcJ#Ó³NZd¦¡Ñ)3e€=%SAXBà*ÈI?T«\0†#¤¡tªÈI°\r”œP'Jğ÷´LÙJÕ'êqÒƒJe'´¬ÈJ,¶€éKE)4³ÈÀM-“a€é\"¨lT°RÅ*ã@”³ÅKH\"!™ÒóL\"T¸Òõ( ;Ô.»‘L8£BRõJÕ/”·\0‡Kõ'!±RµIÛ\r´ÉÒßJ0ÀäJkLÕ3ô£Q³Lµ1’·ÓH/ÑJÂ|nÑıL 9pÚ\0àÓc#Í6l2 (å6RşUD¥7¡S”¯Kº1Ô—€\\\n@E€6JM¨(Ôã	R\rX9% %R[–]*X%S¾‘Öx:\r”ç0ÏT¯BÓ¼É %ØÊXÀÔó™IJ\$aE0w/ê'eßÓß@m7RSŸ!ôœ#¬Í†š–… Z\0QÔ…<ÑzH¸JÆ7\r6 2‘–6@ó‚RÁÕ”DºÜ¥,†fCx\\¥€PSÕ0´ÇP;`‰Úq€:)n´+éEÁX!L€N@²¿\næ_X\$#ÏfÚ-r0ª\nÌ¢*p04‘Ê‡Ô~5ş¶ID#ôbÀØÊIí.ËB‹ÈÇ\0×‡×*X5ààËmD|ìä­ÉjQiEòtÍr¦ HÜ'u=ôOès2^EEø ÎüØ¡díL~ !c\r€Ò¾”™…sĞÀÌ‰kPä~œš-¥ÊÃ<¥#Ïß\n\0åa¢€€¸EÂFR0ØÈ¼Xi±5”x<€!½’\0€‰:\$òU²<ÿÉ¬P¿\0B¨2P{Ä\r86c,¸É4\"pc‚¡…C Ô8Ohö#Ù€§H¸÷  €¤\neƒÑR.€ù`)€®Ÿİ\"à¤\0`	ãå\0˜(ö©º§€›À\\ ¦EH­à\nu`'³V¥\0 Î>Š\0®b@&\0‰ 4†EBÍt-5uA¾•´é_%…ªYQ·TĞµNU<JZAKT9ğ%lD	U:[•TÃáüLV™0–xR`!\05P€%dØ2cÎÏ–*¸) ™ ¨õ£×€Š€ôê#Å ôÄ;µK±Uqb7´!İSuNÕ?WíTC³Äy\raA±D\\`'•%—X¤~u“U^müEã);Wˆ»uyÃõW¬K5{ÖYWÍeÕ~UCWıaš%dd5ÀVyX„LpïU/YL=ÉdV{ û”:•T—\\RUv®”œ2ÉhÄXKäOEVÔW‘>Äşº #È3>lQ€1€^mn£EÏüx‘KÅqTÄu‘MÅ|gEn•»Gh•ou¾€Ù[üRQZ…4ÛÀ´ÈÒeÕÊ\0åÒ°ä¾Œ¿\\™5Ê—r…sÕÌ†rËX•Î)Í](SBêW,Ö=sÖŸøFUÖ\0O]q„áH\0ÉDEvÕ×SÚÅwµÙÆu]võáNh\nñG•âW]]£âQîy\0µß×m^xÕ8C^\"”áœW,\revÍ[¢]Mxag¦\\Ù›•Í×Â\n*¿àıGtÀğr‡Ó]° Â‹“:®5~€ï^ØgW‰_~õéW÷_°µÜ…Ih>µú×ä@AÆ5Ïƒé`¤ÖWî¨:+¥×l6Í‚`ŠKÈ†âı`¸Oîp×ˆØ<VM=`Í|AMj\0Oõëÿ^Séá«Wà``Q´•A]µ 5ØU`JeÆ*×ÔZ`Š¤×lú’\0007\0[]İ@5\$×l\"œaà\nKD‘áUâR„Vó×¢´–WÑ]i2@€„²BĞ\r`Hq`Ğ:üe2‰«A‹¢wP;c+v2Fx+È\rÀ<Ø¦)+j’ƒj¨ÿVW@eÀ1Xñ_Õ„ …ı(ml=\\µrÀ<Y	cíö=/W\"5z 1XäÍp:)Å_´õÚ0ÍcŒTXÜ¯ı“63X·b+vNX¼¥“áMK\\õŒ6S€åeHöU…µemVOJ™t‡gé…±cI\"–/ÙYdí•2°2H0Ù4*M”ÖZÙƒc}‹A&¡†~˜îÙgf•ö2Ÿf‹WiÙ]e…€‡Y\$wBY°˜Õ•–6ÆSfíŠ/DÎÁg;YØÛgC[è&\\™rVwY¡)X¤²J„©^yÖËyg#Ñİ0ûgÌ’ÖL\"áhE¶‚Y÷]µoƒÈ\nJMñ–!‚k¶G	`(–]Xùe-œ©Y`”fÓÀYÙce£Ñ”³`¸ÆÀ¯Wğ\r…¡‚wZTID¯±Xiex’†#H…@º·iª˜ìSZsL(;öİiğ+UùªÏj¦àäZs4P_v¤Zˆ8l€Øm©•ÛZmjx¥ÖØX¿M‹V4Y©i#ÖXOÚ§•˜µùZ9gu¤¶7£Gi£¶°ÆSk]”V4ZÎxvsXßh¸CGéSkke­·Xßg…ŸöuÚæ\n-®¶´Øsf½°`6[kòeÁTÚùk¥®É—\0Äµ±öÂ[\"Ô]\n¹Õl­±VË¼<D0Z–ŒØ¹lM±b¤Ù`ˆ`*õ…(í¶Ò[<=’\0Ù%lå´¡dÙae,w@7×æt±á±‚¼Ú€¹Á@[dĞßÁ\nm!¶°[Lm²aˆª™u·\0•[wmè¶ß¡†­¸JS€én-”–äXgkí¹p)³˜Õ·!Çt0]§\"‚Ó§cäê\rDÛ‡nÜêÖê‚?n½š\nÇ1km´Ñ”Û¢Wplu×ão•¸ì[[F‘/ÒÚr•M¹VÛFSn°?JØSxÖ9[]l2òVB€îXlw	ÚÂ¬V67¶—dbÉ<Ø×%‰A™Óùjm²ÖRİpQÔÖß\\-n5Ã€í\\HÄÕ»Öó\\hòÈGˆµËÁ†\0Å` \"»ƒsc\0uºƒ«m=qÄÎ¦-sfÛ`‹¶÷Z10röm[mr™‚Ñ”Ü±rP%5ÊeĞY6İÀ0~˜lké89_}ÊÂ4Š‰\\q5×-\\’²]É–)s\0\"’ÀŞ\$-¡—7Ü–\rÍÎV\"ÚD)0…W<Ús…Ï—\"À¦h¶,GeeĞ×#ÆSrı6!:mZ÷HweûW5!smÃ×9İ#r‹N7J[c•ÒöYİ\r–HZEt˜·&]r5ÕGİ=u[©\"’‚°Í…Ó+ÜØ„v7[X`meKWÇuÌ7I›ËuÅÍ ä]5r¥Õ7b\\ëv5ÖöÚéu=š·NÜıv+©7\\ÜÕrÅÓ•ôÑ½\\«¡·m]•vá9Á=Øovô±÷PYÉvISWrZßD}r·CM=tM‹WEİMu…Ú·t]ã[­ŞwEX%w¸w#À2TØZ¡‡†vV7}W7wCWz#wıÚw|\nˆÕ	•„úıPSN]ÓxeÑïu}à7XÚÔh}İVvÚèç8à¯	‘Ìt×„Ÿ[		0\\`\rMnUÄ\\±qµq—,\\u\\“€WoŞ[áÖŞ?u0¿W#¼4ø¤Şa · İívè·‘\\X˜zU·V¸«¨lVMj\r|H†™ ü®°¦¸(ãÏB0À«&~2faA)bP˜( +V0¢8óÃĞE{Hi¼&ø `‘w¾¨]{ø ¦^øJlI²_\nƒ *À*’™Ph·³^öÀ\\£Ï€›\"€@\$\0šî²0ª·«»ŞÚ­ğö`)\0= ô &€¡|pWÈ_\r|¥íWÑ.õ|ä+÷¹}€`)€£Vx÷i•É8 +¹ƒ1{]í£Ñ¦`°	éÀ€¤9(\\©ü\0Š=È0ªŞ>È@\"Şø›à+“=é›€¾ff'„-Y€!?{ñ·Çß\n(ôµß­}Åø—ÙŞß}¨4Wº^ì»\0h·ö¦ı{åş7·Ã¥ı\0€}e€\$¦ñÈô×ıßá}•ÿ×Î³½€Š\0'€†*oN(ßÙ€Hô7¾^ı{mğ*5ß	}xArß@H\n)º_€	—½\0°¢8\\ )ÕˆÍü÷Æ\0V™‚gà,`uï—Ô€ ›%WÀ,à}eòàq€]í—ù_ÿ{ş·Æ“=ô÷åà…€V «&È(ôÉ©`'ò &_/~İóWÚA\nEôşßH=ôíz}]ıC’ßù€`\nX/%}µï—İ_y}Í÷×íß‚»5øx6.¿~EùWæ­~uú\0V=n@'_	€íğw¶à¢i¢á†83àx–Ø`[h*Ş†Š=j{*\0ß«~¸	÷ìß¶-û¸PßÁ„4‚×ÀßËˆ\\¸K†‹‚ªi—´_q~ÅûJaI~ö÷ñaU€–—ôß[„Âs7¾U—Vhó÷À˜ZjÁa-Uúf5m`p\\©ë€š][xh¬™UîÀ&Š=°÷¾`Q†ÖN&\0˜¨	©˜á¶=¸\nø8¦oUıôø\0Ö0=í¢L†å\\x<\0u„dãØ|&êâµbLaÏ†í÷oà =fxd&øÆøtßq†YØcU¥†~\0)ìPş!8o\0«N÷õaåÎaU™ˆn¸‡à’nDÇâ5|ïX6áì™àù#åh=à÷ÃàH‰Mü’^Ø™P\\«Á]|–%X:\0¥{°ôxa[ˆÕX˜	È¡V\0	ib‡…øô•`}†ô3_ÍHô‰û'š(\\£à'±{pk°z™X	x¥¦UŠ®,\0)áÉ‹ö—ıC~X–•XŞ&ß€‚&-X¬a¯‰°	8œ­‰Ğô˜bí‰ö(¾ßİ‹FÕ`bÓ‹öI™T èõ*BJHCÚE =Põ€(bÙ‹v%ÃÚâ¿‰–2ƒÖßí{¾3¸Ë­€ºØjãB.8	à+Œ–2¸š€‰¾/ø¾a‰'ãÑcO…òeXÂâ	WÄ`›¸ißãmàx@H¬šJii˜\0ø6cÛ'İ†bˆø«\0ˆ™Pôcß\0˜=r±©™®¿ˆ–8ÔaÇ‡‚¸+¾\0—‹Ì­5f\0ƒW=íCäÊŸ+#åŞÏ ®<árâY†=â£ \\±¥¸Luôøöâ·‰íüS?`=–/à¤c1‹†3x¹Ö=UÎ/øKä&18Û`ªš^(ÃÓ'3ö=ù'%jiw»V<lXbä-‡^A·õV=ƒæ%ê3¨¸*Õba‘V:„ãÚáÑ|­*ÃÛ€¥3öráOî5Ùdf\n¶2c>»GÃÜËUíWõ`âz­.G «aÅ†¦ùd’roRc!J²l™\"áê^.¸Ë‘‹~%ùƒ	’nK˜Äcq‰~Gù2ãƒü“ +ä¥’Ş@Ù/ã3‹&Y-^èn™2D=fFù7âs†NÀ«bµ{>·Ç\0¨şOØ)VA†¶+¸eZi@*ãÁ”RÇ§ğ™UôùJbâ›Ö:™Bâ'”ÖC¸¿nÆT	ûâÿˆ–PùJa/”öU9Gä­‚`ôYJ\0¯Uğ	Éàä´=­ñØÊÓˆ’fyeL=ªføÑ_s•\nwÙZ¦c|v;¹ã¿ViŞ«[hµfV:™VWø[U~=JsØ¼á0HÒ+\0˜>f[˜WÖ?YX©ìá´,T˜`¡‹@)˜mß•î	Ulaœî28ğH2ˆÃ×á¦`ù@!€—†ŞTØ¦m—Ö9~âÃ|h	øæ z€`ÂàÁ†Uü¹ƒå…ø\np†açØ*Ùdq˜ÆK‰ûd“”%÷¹†§y”boI¥+S‹úµx.Ç–ŞQ€!\0Ÿ†˜ôùF\0šâhö‰˜Ê=Põ_àæšZy *€ƒîPYš\0ø½ó!ràJ=à¦£VÆ\"èä°ú{CÓgÀ0©ëªóUŞc8‹åÎF2 *\0®-c6\0¦GIÉf¿›9I±æÈ\n²fÈ€œŸ¤ãÙâQšÚi\nêLı›Uî€,fì.h“åà©—ÚgXm¨n­îk\0¦øœZ \$§›myÃg„Æo\0«M}:i5g(†ùÃç!›xjŞ«AV	À_Ì>fÀ\$€¬.GxJ_òLpö\0*\0¾œ0ô™Ö^ï~êˆ¸´çd»v•]_}T‚¸©á; ÎwÙÚâ%å+Y×ªÿ.wò€‡Ên¹İç1šªìÑ&åšİX‚§Šf<êßbyœíúwê§‡3ğù`!ç¿XÂx	ìä=Œˆ×ÅH¡›	('éüÎ'U{Š¾*µ_Uµ¶+ùIVecy'ªÿ‡Šà¸˜­ê€Y(èÅWùè®®Úâ€¢>.¸¾€Ÿ†Fv9¦È=VNUä‡Øøà,€¾=¹1Úh< ôº	¦V›Â·Ú€¾ 	Ô«c\$ƒÃæh¡Jei¥§Û{X8‹ÖDš†\nc=æ1w³èX®¡2Õh‰.ˆÉì'µ ÷ĞU™ô­#Ó{–õcuŒ\0‡Ÿæ‡ÉºßÍY·Ùº¨0óõ€Œœ–Šxæ\0=@˜åãÌ åè/ŸH\nX£hà.W8å®¹†øô8ã¡¸õÊ)(JEñ\0\$\0¾â†\0Ø;'ê¡¸\nZ“ =f†ZiLv‘ZS¡‘:Fç=•Î}X|.¿‚ï8Û&`¡ºˆêÆ¦c .CÉ›Pv™´‚)›\0fºø\nš4¦m£j¯X~'¥3?e¨\"*	(£ığ•_(ˆ f–Ú3«}˜Œ‚ªÕ˜¾dÇß1~X÷Dş À/â–æ†9éiŸ€;CàHŸj€ \"c§£¢ì5&a¡v‘Zé-}&œwîiÏ#S‰¹nI;›¾\"yfÅ…ØRß¿:â¨£²rK¾[›Š\"€“ö )Şê•Ğ	‰ú/¥ZG¥“(&V¾J:B„£‰ø‚`(Ltƒ˜şi†f¸«}©@CÓ€‰§ õ#ÓhÆ=† µe©XŞ@cÙÖ=(	)öäG%*ˆãÓfr®	bec5‰ˆ:Ÿâå˜Ş†zré¡>sÃæiGŸ6E5be¯ ¾0¸ewY\0zwgÅƒ”ƒé•\0±€õy(àå•F%÷İåf–XÉá`å§¸à/ä-¡Ö« /â§^}80êÔ=ÆGİé >:µ&ë«f Ù`U|®®¬Õkérï™h„™‚oÊßV.®\nì:Á^şUøßåÒ=¦*½àGš>Xºb_A‘Šß'™ş²ŠÈ\0ùšn“?bK’F~ãÑâJÊïãß+‚¼hwÌæ‡…™Ğ\0øÈôúDæf»}·êY‰¾OLÛq>ÎÛ&ÚItà<Ò\0ùúS‡È¤e%“Q®ÚH@ ºÒ®»^UäNƒi2I€Ğ6 à…ËRA{2kÁ8À²áUì–…IŞ!xZ§ê_N¼œÒĞYÑ;\0T¢ı;¿Ÿ?Dç€ê“®İÔrµnh 3®‚\$Êbq\nëócˆx‡ÅµOIT\n¡vÂzê€Û°‹jà­lDÃ¡Ä‡ï<.´½‹pd›á”R\0¸ÁuQNPò¢€Ú®p*VH}±¦ÅˆÑìnEÆ»QQoÁ <ªDê{@ÂÙ)X\nYÖ€¿t%j”\\ R5\0ò¸öR5~›&}à)€¬:@gÏ 	cÛâÇ}.º1§ù¬Hh &ßM¡^Êul€š.©Éìá«‘Öi:a1³€µjdšN ù‘lñ³Ïy#á§ #{?áŸ³ \\©ûm³†¯û<æ[³åíµ_m>X	F&g´vÎ—¾i{~^JÕÒ-D±A…ÓumL}‡şë9Ó[UQD|–’)íN#Ùù ÿmS¯}-G/Iò\n€\r# [›®ØKrlL\"ñÓÎvØ7;`‰ID˜õ‚ Å`ä\0í&ãi\$ØËbìç5)L\0ºHäæ†ÅEÔ2`K1£êSL‚.\$€çbH6‘·6ØòÚ\0¿R©š´ú\0Ë^bŞ\$\$€¹ÁR	\n/~Úšê51\08Û~µîØBùÈ€Â”¨¨Í«a˜ıV'uóšL€”Ò!mëIÆÓÊ\"j(À4‹ßPM5¹•P\"4{l—~*˜€>\0ë·(5{oîL·0h¡©\0Æ¿Hh²—×!I}rÈ_!|†0U!”ÇR)5,`È\"\rm¶\0üÈk#¼‡GB0TòÈ#!Ü‚€šÈ)eÄÈ„@: İî¯EˆÔPJKId‰#È—'fä€â'ÉºtÖn†¿¦[z˜G?Ë»S\nºünÕgE’§æÙĞ_iºê‹1iåÌ³»ô¡tvI+/ğÁ˜3]Lnğ˜[‹7ñ 3kı,èI[bOüHfğÂñÇú	b á%S\$¾İvïPO5>„Òi·¸62ĞoQ¯Â!“!SCE£PQk¼fña&ÔÌQvê–{»ä©c»„„Mvï2å©×õò¥Ó˜Ãºü¥¾FôÓck»P{;åVë»V¿°oƒ;¿c\"o±;Ôìkõ¾æúâoC¶î¹ñU¶¡ú›óï3+ı×nL8p)ï¾‹Ì{@üÍÁ¼~ğÛú€ÊXfÉ€ù¿öò{ÇÏĞ|<˜ÖŞÏ\r¼¬¡Í îWg@U!¢ë0+“{˜šlÌşEõšU³U¯Ò¾¼rOÎ^¢«&£—ÀÌ¤†!OâYÿ…Ÿ€Ú ³-Ù;p`˜ñhwpj\r`â“%ÀæıêFp8\rØÈ–I‰@\rMÁaÕG¢wl˜ÅTåÌ{<˜ÖÛm·İ›¢T\\È5„×ÏS½+ê[lÊ–Ú_X‚¡hÅ¡™„İsRŸ2®]øĞü4îLát­[†U\rĞ„PÌÙuäÌ@Ø¹B(¸óèm×,Çp³ÃjªÜBÓñraSa”ˆ0fãi4\n§¼™›WªÜÜ‡ƒò2 ¤ÖJ—ÄèƒA>ÔlÚKH5G°TüËyµàeò„­zEH,\rÔzäØa7%µ(\$ÊÍµ&‡òƒë¾0†èĞdEî¢=Æ\$Oá>ïè\r T—#ñr=}³±İ¶æ 9ì>0M·¯µ¾Â<lÜ0,Äó³Aº×.™\"É%0‹‚¸W;U°­¤‡Áq@,JÀ&‚ññÂ	Väæ}ñâÙAÎ%Çiİ?àK<sí¢0Ù:RtÜ‡ÄÛñë+2íñú—À:†c=®ÙÁE\0Ğ×#¼‹Æ«ÈVáŒÔÊ‹r/`ÿG8@:\0îÓ¹<”PöKMp2•–€¬©oŸˆ›°LçA\r§(XK#¸\$FÿóÉ\0É±T–1Ùm„•‡(•\"r“Ic1\\¤ÎuP0K.×J–À´ Ü„„HœÂwr®‹„…‰Xr´¢.E†Fß{*Êè.¼³Xó±=¾{!ï7°ºÜ·ìG±g.[\n‚Š\r‡.ºêr“ËÌ‡Û€óËç/tal#Ë÷/€1\0È¿J\\¿ó±0ÜÂs°­ºÜ·Q…Æ×%à6ï7Ì§.œÉ€×D.â(s/Ìß/¶s?Ìç1À•ó)Ì4ÜĞsJo2œÃsNW4s‡ò‡ÌO6<³l…1ï\$Rls#ÎÆ{swP77²²ìmÎÇ <ìc<ÆÜ¿s+i|pIÂ\në ú‰3\nıCo?…ÛPô~µ·ŞÈ#İK€…“œ…‘tû‘0õD\0˜÷nø	 #ŒØˆn–äG†T½Ï1 ÌSF³Sªy^/5'<¤	—Ú˜EQœ,‚\$<Ü÷óô¿??üBŠ‰(¯?\"“WÈ÷>¦L\0Â7ğ¨œ6HİĞA\\‹E©Ğo>ò\\Ü{ÏÒPÎq'9\$]t6 ½=a2Æb #ÊÁĞ¦ù 0íÖYSh…tYÂ©x¨qk_A#©ëÂ…º}“¢ À´›Ñfİ›lï’aBñô}Ñ€îâÎ1ÑõÉ`4]yÙ`‹ïãĞí!](	«ĞˆI³Ìô|ûx!à…†,D\\ñ„»À\"€ºô•Ñ@ÔQñ_;ÎØj/©ƒJe\"Ü 7HwÑd‡pú	Ñ¿O‘Øôü‹6@ÃÃĞ_Bímu\rÑx8=õ·`¾¤æÛaÅÏQ}IÊ­Ñ¨#İ@trß—Fá„PPa6ß½MŞ(NÔç\0ˆ»Ñb!’,pÓïRÀÔôXĞgQ’íôs?tgÚ]•Ó…]ŒÅİ­/ğ«ÑdÚa”İzE½]I*O£†İxyM}3*#Ä/Y¼óš,¹5İ’'ÑEi„Lè8!F˜‹-ÇG*|íƒ@àf\"\r wÄœhå±±ˆÀ\\\"ƒ\n\r`1\0™P—’=y\0ºÖĞh½x\0·×´‡}}™Qx@3]{×´{=xt§Ãh¼CuNW_<ıtú¾å·hõüê°¢‹ö'×¡b‡!ƒ\nWw=]x]×Ÿ8„×°3Ã=¦ÏıŠ ï^	¨Ùa>îãØ0Ú7ôÏ†şSv¹\")†p?`Ïnm©E`İq†¹@­Û]\r•êˆÉßÏ×\\V'ƒÙ'ibôÜå‡@†‹×ï^Öæö&e¨>½‰’·Ø˜¹1î™Ø¿\"’b%Óïf!õÔå¹½´,ºdƒ€ÔüoãÛoP]E\\ŠO2îb˜Fä]\0\rÛïe3+vùÙEëa—¼×aİArtE†\"CñMÚÁ•cö×ÿa%KMu-bcQ‰Öo={pg¸g}Ìw=G—t\"7v'Ãwr]‰ğıÚİ3ªv%×°qŞ€áØBñI´8qwhÈ„qVı1©¯Ù\"e ]¢0/vğ€a;`Uw\$,´]ÊQŞX\"à¬×Ûm0Lw)?Öå†>÷eÙ\"’Û#>pW'k(‘÷…a‡brÚv¶¿L¾w	İØ˜ƒ[–Œ±lœ<*“p6Œ¾}øv¶±ÀÈ\0»¼“Qb…“ØŸ}Qõğ«/Ïª¸¿^Ãaö'Í¦b(vÁMİ‰”ÇÒK¾\0@ß]ÜıòÓ^Vg|´_(w}}ØõÚñdm‡á|ÈøGáçŞµ#áO^ò°v'/œpÎÜ¨Óœx%#§l}{wkáé•ˆ¾câ\0¶F÷Tngtò€Ïßawòí÷ôhbİ{Yâ%`İT/Opø%x(À@<÷OÜ­(õ1Xk!ğ\"]Ú\0ğÇ‰¶—ß(#´…)ä'‡a7&=6³ñ^ãdó€¶5ã´ˆ%×Ú«€ûxghš^T†EDGbjESÂ¯r}ı–AâÛÅ weã1:álx:áLÂØ‚¥İU¸jG{/ç“]Ù÷aä^Ü*ù\nf¯Š\0;ö&¶ƒ³ñ19Èò‰Ù(f¨T}„ÔïÃˆ¼\\9¸#ãÏv½¨)VÙSCaöfáOI@Ö˜‡4Ë`</p¸ØX{}˜OR¬Âg„d!QxÒ…¨b'QR!í¨\r~mf\rŒ‚)[w/®ÖfEwá¶£TÔ0wßĞ°Å[¯§Ş¿O»]™Øxf>xuØzåËQ²2ÆàÜQ\n.éÿëøş`u1â¬	ckòöû;÷ªÏ˜`ŠG´¡=’wÜdÅàbò®W ¾‹Ş'¿ıööIÅ¯]dŞÅñd ukÒœáäPiÂïQvõ²ğIğ¥@¦–ÛyMPc†75reÖ´ä»‘;ÂˆošÑ+»ğµ}şkï¿©\\:„¹¼~übXx9ñ5Ü.‚#ãÌœäŠğÂïW”×êÑ5Êkz!ÔQw•“Nçp­ËB	¯;ÒÃØH®ó](ÈoÒ¨Ò„¹®dŠeMvïgß®Şˆ0/ØŞ¾º9ëùßÀ™FL'wäØ÷¥sWªô…{;ÚcİXø5cW£AmÙÅÇRûe¹ÔàxTw³q;Æjö8zbm–ŒwÔo¾Ñw³Áõ€~ÏsÕÁ;i>Ñ—û¶¶R…L\n~×›¬Kùí•‰î	…_Ïµİ;ƒce¯ÔûƒyNÈoûeĞO¸á,Ô×í#İRûí“A7Uí•ªádÌ!í’Ø.ößî9\"›}ûeÃ”ä×]U=¼`‹ƒòúŞãû­ïp<ÎÍÁÜÕLŞø_·Éø²ñÊ#(o¾õ›v«<¾ÛbÖméİÿ\0úd×ÊçQïçÀ_xM#ç¦Á¼rA¶'´>£éé¦6Wõ/[İHCuèÀ>y…ğÏQŞavÒ—\0+Á)„_ŒÀvŞNu†¿‹áî÷fGƒ{ãki ¬uKAGQÄvúŒ”¥\0n)D½>†LZ@Y÷ú«Õë5Óãyã\"NÄ>Ä*ë®ŞWwÕˆO¼5…Iò¯»Á™ôÔm{^)Ø¨ßÄ×6^/~º—Š{şçv¿1t®G¦évåÖÿ¬u©óp`²,v¡ÔàD š|¿Û„ÔNp»êÏªŸ;MóÇ¤=E{8_üa>í§ÕÿS‚?P`döÃ=Ş):ˆ>ıx§›àŠ}6Îdòõ†÷'S^wzÃmÍ@ÖĞŸóŸ–¿?üñÃOÂ6d|ç\n@Á…¹7!ö¹\\hô[Äoİµır¯ßQ¹Ò/Ñ__‚YÓa—º“õÇ¹ßc…_/Ğİ\$ı™õ×Ø\\ïôö/Ù¦ë<Ä©u¾}³×¹\"P1ó0*Tüñ#Üœ?n@—ÂÔq}ÕŞ¯\\ÜYIßğGÖ®ıç)ô\\\nà5•âÑÓ¶P}xÚÖ'ÃV&t¥õT­õï}øì™ó…Q‰9²ùˆı·WÄE}\$~Òª}åŞBa5…ğå*(5 2¬™Ò?Ze{»Ø¯Y¸SÔw»¿‚[ãÏå¿û¾—\r½ÜK—İÿåİE~cÙ—&_öÔ¿Æ ‰â Ô´îˆıùGQÎLö@oÆL~“Ößâ½¸şKGä¡’ƒtMwê3R}1Ø?T€¯q*H¯`óŸûãÈ ßŒt#òl¡²ªçû?šäç\r£òÀŞTõL/J[å^îïO¾Ëı«úÏ•W5Áù}@Tíø6 uÄT—|JŠ'uñ?ñÀ‡ˆ#åÍ_ÈuŸñjvë¬?ÙA\n|h/ò!)Ï)Ö×İŸœöãı'Z³mtMÖÇõõƒY¹ïõ¡?LÅ;ª2`iüg–‚]±˜I2íı[ÖQ]èƒw_uÙ_¤~T}§ø5öˆT×èêqƒ·Ôs9{lËÂ…áÎW™\nÜ_ÀqIøùS]Ÿ¡T-ÙJFt`\$/c&jÿÁşé2]SèŸMıÖîZhl2ÿá×±Í}{­Û`% æeA0\0œïˆÿìèøBî€Aå¨\0ça{\n²q¢ªØ•vgD\$0›XRma&OT€iVLBì’´'AÊÑ…ÉCñ\0`„Â'5iH—ä«\0%(-Ud\0Ø‚#øaè«D «Pu€\\‘’…%`­hNÉ	Ñ¹ˆ/à\rî'ğOÁ:KÜÅ¢’ËB©\0^èùâ`È| ši\0hQÚc€ĞrÀ \0ˆÂ´¸Ä¥UV@5ö†\rX˜:¥lÄ\$`'+*VÈ­}[b+ˆ	ªÛÑ]¯4F†ŠxèFèªİ‘¤Eè€ğ+°¿CôÕÈ…ğEm¥L„WHëQ¢€ä_Iq¬TQ¨ëŸÕÀyñqØeåÃªü@“W÷U\\Ğò3P%>Ô4	~âÄ4ïĞ'ÂÜ®#Y6´I·øQÈË2)ƒºV·¹eÊ›Xë¨V¸ÈfLhhvµĞ*ñÜ)Lsäná#% öK©«Ä[¡\nÉ¬ø0)É@ËXã]wá(Ë{›À§\0ÏÜ\rÜ\n€?P* i®¡smad+‡èÊk\"ÖÆ\\XH´\rØ\0W[¢¨KªÈ0@ q­ =éÁd(#£ŠéQè…Dñ\0¬W´ÔW¨ïà8)\nJ(˜Ğ#kÀ€ö­èÀ;e \"Ó\0×­PE”²°8Pk‡UÌ“\0 ªÃ”½`w–¬Y8Sš	)é\0©+D.E~Š'rİd\0Ù‰Ypâ`Dˆ)¤‡nÎÉ—&îè\nh¶ì6ââyÂ®¯€öÔ~œê(Ã/ÀıöZZõxH¬‰+˜^¼«±Pü';g\0&”FcPÂÀ=1[æK¥Ü\0!³Öb²NH=»B–5°Y ³2‚‚Ğq5\0{†³­FUª¾dÃ1VË\0Rw¬Xd2¹ËÉ¡ğzf¥ì©Ù…³XÌŠDø.°XUh€C_~Ùğ>I4\r ™c´	d‰Mœ“33 ¾¶z\0VĞå>Zù€Aê¯3U®Ã6Ñ\\WŒã`ÓÁycLy™s¦(EÜ&3“*ÿÍ’ã/¸4„ï`Ü4ebÉF\rÃA²xlMØ´`hu}3-¦S0pÙ\0+FVMNcÅî«İØV°Kcàš@z”po â0_ùJøà\\«ö€ €PV@ÅëG8<ÄØ±Á¸cI¦‹Ö/nMA¸fye­üŠ¯™ /}\0„(šÌ¦PDß˜“|_ªÇÕ4†ŸíSàôAÍ`vMğ?H7jZ“í„&U}iZ˜8w`Ã‡´ƒpOÀ®¬!èCŒEáAÍdèÕ\rÀ|Æhåu¡±\\&dÆK(fˆŠk!/¹„0ğL{UØ;‰šZ¯©„jÈ:\rÃ	v7P‹Xµ‘cm	Ê²F­“¾ÀƒÌO\nd#XA,®—ê´eêÇ)ŠšV– Ù4áh®Ğ™ŸÙ;ö=ğW©´eğP°â`}ì™ ÏÁµ\0ŠŸü&Ætm!9AºcM\rÈæ(2XÁ~cäĞeŒ;&¸9Äé èBxƒ¦]‚«æ› ì³äh\r	²Q6(Oğx€«AäƒÍ\n.)QŒƒ¡<Aï„ï	±‹ä'†“P û³>`Ë	á¦Ğz 	!çÚ°_šÉ¶ë¶QAèƒÔ4„ñ<L„&¸PP…ÕÂ±bşBÄ!7m\0¡6B…|ÁR\$,p÷l‚ae0t„VÅÄ\"øSŸƒİÂ2h•ÒŒ#v1ğ¡bB‚a.½ğ»Œ,ØHLQa@ÂÔb»á£€Ø\\Pu¡iÂ²b»	&,.èI‹î!'\$'›	FÃbgáçØU±hkÙŒY:6‰+ù™±“cˆÌüŸ|&ò¯ìq!9±ÀgüÆša7ˆOl”XâAÂ†+\nSHPğk!v£†4À®q;Ö8¦¡I‡Á…*ªş¼1è-pwƒÓAëd€\$1ell¡•´šc‰\n–ËHU0aVÂ…`Ç\\+XeLg™°Ó…rÇãQÈi°a\rC\"†W\$\"Ôad±Ä„NÇQ7HEÖkCCe„Ê¢šgèjĞ¶¡Ã2†‰±d6x\\la˜àA­…Ó\r¶ì6Ø^p!y°Q\0WÖ€Ö8Œq•Œ4]\0°¾ø=I59%pZ1´ÛcVñ¥\nú¢lŒãa’BËj€Çl¬sgÖl,yša°\0–ÙY}0{Ffl‡™µ4Ô‚èÕ9¨ùwvBÀö¯g…Ws&ÅùêÇšM2jr½Õ~KQö_lÁ˜€O„¢«e{´%ÖElŠ•h”<gÒÅ’cƒ^€™u©_AH®	ZÒnPc˜r5£&æMÕ‰DØh,:Ù#“KaÀŞ4=6\nl“¡ë1†=UX©ú F‚\\né•ë¬ñ\rüÓ±¾Ekñº`d@õ+YÚ‚å\0´Má„Ä>§\rñ¸hpàá¥›q[æmE	™Ô3ffÀV+9¦W\0Z0­‡l¿±ŒÉ:Æ±kš*´mDİOûj”›mÑEK>9çH+7VT…×ÙÎ³p'M‰¤%Ecj¯•_ÂgÀÑ\0>X˜+¤ä’R“ÃdkŠE±\rF—'ËbšOa‹#?¨aìZa;4mÃ•—¹]fvà„ÄIhMN¢şæ‚M Ç6Igf¿™–C\n˜Š­’™ØD[gg\"ó;ˆjñY×3ÉˆÈÏ1zèŒí“ÅÁÆAµ“Û\"f1ÍJê“,cÕq¢ûFö7C-&ÄÑÙ›=â¯7Ú=—ƒhüO	‹Ë8òëía~ô(Òy‹#x@-,Y«×iô•¤»ã\rHVŒyZ“4¹.îÒí¨K¶èÚ³½^ÌÏ	rFÆ,1YµD—HØÍh¤OÖ Ì!š…AjÔ8ŸÓi`?0V'òÔYê±æ¥m˜}µ:Õ”={X5Z0g@'6KcO cKà\nqƒÙ²Vj¯ô=üLæ6Ía˜²&”¾ «ÛXâ-dÙb²Qk.Àš#a;ò,IaU´¸gÄÅ™r±¶¶Ñ\nŞ´MfúÚTôÂ±P°Àö‡?âJ\"Üèsós¤Â\rÑÌ7Gˆİ%!Ó›¥Ë%=7S5æ\$Pƒ½&ëi!oŒ•€Ë|g˜	šäA¨¡qÎû¢¤òúÁ6¤IZ&²):Cı`ÂÓª\$InÈzUÅ{káuîR··nòùU·óàƒÜ<ÒÜ¬PÜ}¸šU`îíÍDÀ6ºn&àJ7áWtƒVÖÛ 'ÈIâ\"([’¸+pZµïË·ô•‰„~„ óô—@‘@UB©E[‡BMt‰5Ğ1Gwm¼Â77%RF	qi°ÈâúÕ·çÛ¿cr§øCyF€(o@×ÃºÉ\$ØÏ¹Õ>çrb3V*²MàÃ­€Ûõ% oÎJÁ#{~vûd^[˜¿^4<ü®,šv6ïq\\[½¬'¨ìá(rs ‰ã38o\$Ş™[¨è@% †\";S;–-cr 7*\$Ü(rwe¾ĞR Íşb½¿ËzŒße¹ˆYx &¼Ñ!†kÍBŠw`*ä’ü‰åQr1a’UE„G\n7Wl.Bã\\äÆ‚`ó¼7f\nœMóğç9.b\\å¤ùN0­8y AºÚî¼hS”Şµ;¹¥Õ©¸\"ù½pıÍ÷šİ·“Ê^—‰»±{:ØF,ô>A\"qmû¨ßô/Æ.‘¡¡Gâœ©‰~À¤¦C‹QÒBúã>™_R0Sé EÑ†°7çluF*|A´íó t71qPâ±Å[€ô‘Ï^ö¨ ŒXâÅõ¹¤ôøÉR¸Ì)4\$L¤ÓŒäÄ®˜×T8Ñˆ™exÀÀé# íqPıTSóågŞQ‘\\w'ŒQˆ¶ÀMzAÈ¯²ÆS^’“22€0’#,€y~ÒãûŒ¶Î1Ü_¸ÉŒ|Ø@Š§èÇA‘hE”\n¼©î#mEÀQ€ ¾J5¡Ã×–îPCFy®wÚ“¥ğB^ö÷uF‚w9\0gšïÈ#t#B>â5\"\\U…o€”Ü}Œü±ˆq¹€‰]¤Æ•Œò4ÃˆĞ>««_ŠzêÿìóÖğˆÛŞ¹‚ıy4Snµ£Ñ«^”½ª\"Œ-ñLkHÕñ­£K8dzO¸UÄj—Ïñ¯ãZÆÂ*L€Æ6\$k¸Ô1¬S\$ÔCÃÙåÊì§âîC§Š°±¶6˜»¨ÚÈxVj?Ò=ht´mÀf£oFÔK³†6m˜Ú±º•ÆåÀ“!nóú\$ì±VĞ*B0jã\rtjôlT!áo@®ü\"to§Á	ğáÍé8@ˆÉ€fp9TÿÇ¯'tpáĞşÀ9,’yùğB¼ÈâNüKÆÈŒìÛ4dÁdÕI‰QVûEŸq_‰N¨äÑÈÈ6' Œğê´_È\"€ãP§Olı¹ÑRŠ«\0|O ?©\r„@dÇJoeâ¬&p­7”tUQÑã¤¡á\r“9Ò‡OqV!ıGRuÛ¹@|uH}Õ£L¼Ìm{Ö:Êë‘\\I	_CÑ‚¦hê…S]GEXŒíµ\nŞÕÍOÅ£§vVSŒqÂéq²#º7È¨2b;’ì¨ï£E]@G;u·:p_§²QV	FÔ~Úñ<`HñÏ”ß¿jT•a›wÈòq…“1G˜|§5bÜyGéÑå›ŞºT%„b\"Ç¨cĞ>¢Réê=ºøòñé£Í¿5|bVÎš¡¥_îu×ª@	‚\0½ÎÙ~x#î'_-º‚„z> TGâ ™\\(GÇ‚@±Ôàït³ƒzàGÈêù\0Ó‚x¨ú±óÑGÜu´ø±Êœ}¸ù VAl+¨}í<~%QğãëÇìX*yÔhHúãçGãWNk4²›¦İiC’~Çâ®HÌ‡S‰cúÇí¬¬3çD¡r@–Gî\n	Iìi´‘ùk‰İ¥òzœú¹Šı2Ì¾vh±µ(JÇ•H¥s)¿Ê[L±l\nÂz\$£â(d»Ò‹|ıMÓÓö˜«²ó®j›Ø3ù¦ÀÑ÷\$\0\0eğë=#L€é²\0„æ¤iS@@SœïR¦MqÜ/Äÿ#Ğ¹Å&S°?¸Ûœg@Q.U]œµŞ‡ÌYR\$h(«i=WÚ•C)*’ÛÉBß,õkâÛ‘ë¬‚±ñ\0ıÛjHf\ní³Æ‡Y\0DDç¾S‹Â°Nh@@l?´ AËñ8ç Kd>¹knĞşíÉëÍFóq‡\"6©Jl´o(a€ä9Hš¼ ÒCQè‚¤H¸8x¬—ğS»nµÖé#†E8\rY'óÊ„Š;ÂÁ[BóP†Xé4ƒ‡Gø*yºÔW´fÄ¥\rrr4UÚºe‘SCAœ¥TQäêqóY§óAİßX']¿ùó‹u¦ó¯£¢‘:7OôŸFØY®«^‡7ñ‘Äù TØNåµ\nw‚È\n	•ûƒË@a‰·\"fÁÔˆ@Ç,dz8›*Á#â-“jÀUR?öHş‘î4%õ²K‘@ÕWHÈ:{Ş÷XU¸EÆ*tÖÛHô2aÿìiÄªn^È¸)OD®8¬g\$±òA\\¿¾H’@^eLÒIß. wQÀèAwÖ˜PÃä~¼xiø%ğP\rÌ_ç\0w|²º6ÉG DÕºTrµ\ró“ÌEòOÓO9í	I#8“È©IIp*5’¦ïD)A¹‘k’™}‘.\n`ÄY( ‚QØ”ıŞ:GD›DäoGópœ‘íÙp\r¼ÉE^vTö]T`©.([v\$ØLøÅ÷³ÙÈ':]¤È&’í]ÔdÑtî ¼Ôl‡#}ºd–Õ\0ÒË TÌéö:ÓT2fæ=5uéö,ğY‰’gSÑ.†~òùÂkøgŠ¦^cE-„öm,´7³bsd3<<túöM¬†‡qO’ÇŸ?0Y&QØ7—¡„]\0É·–²ö;šÁ„–.Ú49Ix°ê\nF‹ç'O§L¤è·Z“§1=§×¾‰x]>¶,vV×ÎL‹ù‡sãÄ¬¡&‚4«©	22IT¸*íÅµZ¼†ù°6Ş†HpzrÛµÖ@dò§6†‘|ñ%Ú-1ÿ¥•ï©Š+¿’H©íQÏ7n/\0\\y3>5D Çüò1RIaê:K]ù.¾\0Â‚8×\$(”âiäŸÎ-LÙ(§3° Î9å:W#Qh÷º W¥J”RJTÚºbYFÎ£d_Ê#”v2AG”£öşA}%Çtbø¥!JyÔ·‚CJIü'hšUgñğA¥'€u’œßÄ!HÙéDe(Fİ”œ¸«Šq§ï‹İE^\0âòS3ò@PúRÓJg\nÅG”¦—C™P‹†Œá'!ó”§#]Kš–ÉÆ‘;>\n@Â éÔÒcRîï1sƒÓƒ\r«*%,Çñ|(•¼Ø`TX¯ÈdC½!zi*R@‹rØöOÖv%\0M<‘ğËç‡âïÚâŞ¾²¤ıOÃj°b(µÂ´TÄú„£ò·á	Šdş<%“¼ÛÚØôÊÛuH|(ïZ@t¤™*r°d¾<bâ’@<¥¹HòŒœÈ;“+	ñàıd²±\$>8O`…\re{èIZãO¥z>ê~VØ‚y[²•œâ*5ç)€ÍK HN‡àFºåI¼;l}¢¾áw GßJğÚ‚Sİ×ù&Ë]’%a•ì²•ÀXY…€@››ùõ‘d·&4¤´ÿ2[¢ƒ2uş‹Äßñ<Êûq&QN9Ya€D=1*†Xô±ÆØ²¶,Cæz¾á8ğÛŠøİoÒ† pr’ÛRPLã…2Ëß{ o–f°ÉŞ!~§§ÉÉå–Ë7Q\"™èLaAbzYËBu4ı.t`Ùhqùß÷!\rU)ñí0!Ñ+ÀSCğKàš‚^*Ò`,À\$\0Z¬U`È¥aˆ™À \0\$\$—\0t-Xí8vÎ(3?be\0È\r\$äà`@S–Õ>Z¤ÉjÊ½íËXã-öZâ¯‰l\nÂe¹@`\$Ş­^[©-ğlJ×@@hD'-ª\0aukÊ»¶’ßR ğ¤\$Sˆ§‡i¢¡€ú‹-yt\0¡ğ ÕÃA\n^K~\\ô	Yk–E|@	ü°Oux’êåR‚É—Zí.¢¿å2êÂê+çX.ğà\\àêò%ÛÆÏø¯1]²Ë‰uÑ¿V#)—x(¼|¼ÕòêÖtKÑYg/:]tÉzË]–£¨^_.*Is’ê¤º¹â^ºóÉ|ÈóKİ/|—BÀ‰t‹ŒeÓK¢/ÖŠ%ÿ½y{‹Ï%÷…­-Ú_²£ùòùÏ°ÁV^½~\0@EìDº ¡„‚È™~“P–{ÁêÇ4S„ÒOĞ9&2Dä˜§1À†m®|Ân0ŞáXC‚ƒ Çf0æ9‚d!×Ã­V>ÈQ‘L;âìLì Ö²ó+ƒ,¬<ÖtP›	ü3h Ë°,%v€P™ÚœÃ³b‘a€ÊûˆœëâØ¨05˜¿¾³¥ğ,˜I°•˜0í‚ÅóØI*¿gTÃ.S:.¬\$YŞ0­˜ºÀõ€ò0lJ#Ì6'†ÓxA8HìUå³ßgÅ€ zşØL©»³ê(†ÏÁ;÷Ñ¢#ÌCƒ+B\"šgèŒñb%³Ìh*Ù)üF–ƒëÌ´8jØ½®#‹É“š4@ipÑ)¢CE&}°ßb;«(M!¢¼N¦CÍ¼Ä}ˆúÑÒ#üH8–_\"C•yeºÒ`ŸiDPöm.\n¿°ú+|LÂ\$c˜‘À‹½1ÛBÇ¥c+’eØ\n½³VcÄÒÍ¥Ñx‚…ŒÎÇD¬*÷%lK•ÅâX”Z+‚Ô.%Á5\"¸2Û@#‡Àj2Q&4v£\rZ˜w'[±©,6AíLš›Ì,‰’©ğzÆMĞî³€`ÁšTÅâ~…ØYfÃÉejÖb'QBV´Œ>ËÀHÉn6É°pªU?²ÒYDmPäpÈ¹fÖkv–î%¡Y“D¸Gˆº¨«Ò™\r0š@¨õßó¡¡}j(G–m?é¹”AqÍC5fˆ9ä\0ÌÚÍ¹P2ç¦ÒÛ}ä\"Üm·SŸ×%qN\"\rÍn\rL¤ÚD0%m‡âŠ¤Gn.Üœ¡-­¸Ò•¤îŒB­Xå2ORÓ*Ô±I 5!”aG#§’ù¥1¤cVÄ J£¿Ç¹%çV\0¬ÀüÒİãà*“_>ì|DœÌ™¿ofìÂ©§éFœY:g03%\r?«µILÜ]ÅŒD2ú÷x§«T†®\0è%üy~(¶P<±™#Û0Íš–	'K·ãŠÂä©*çÍ(¬ù/QÂ£»I^xb#¤W‹\r^jKE|xúm¿Œ¤ØÉn¶ÜK)~á}ä;Ë—=Rğ•ä(ÈŒdùx\rZ{Gífº•=¼Œu¾R9Á«m2³Ò<d]K\$(¹7D©ñR*—fí-0\$©ŞFû„çÏÛSÇoZ€Ûq@KÌÆsyä­@v¡(²N‘á€K[SÍº}në>oŒi÷EÑª%ü:äm©fÉ¸;?+œ“çl•‡üÂãóÛœRo¤\riiÓ\\cŞÌ\n—ì-yŒ\0Ò»€`Ğ‡€gVL;`˜t¡Ù2ÕeË!ø—­D¥p]¹ÄÇĞëKYV°‰\0¬‰Åhx¥Æ\0/\0»-Š'tWŠåùÀnVöÌ!Î¶õ®@˜ bV\0¾Uó`™sàŠÁ^’4:Hˆ€ \rÀ’“.œ \0ÜÀP€ç+\0sœ¢\0à˜pÊS“ç/w\0j\0È´åÙÊ \r€\0006\0k9ªrÀYËœ'-€8\0o9¨Ûh9Ì3\0\0004œí9ªs°‰Ï³ \0Îaœï9’rø9Í³ \r»€2\0s:È)Ì`	ç(\08œÀ\0Útœåpà\r€N\\9Hu®ó™gKÎo?9Nstè@s›'8Nœ6ï:0¼çéÎR€Î¦):’tTèyÖó”À€9:rØ™Ôs”ç6€2\0q9òttëiÒ³”ÀNÇ:Æu\\æSIÃ@\0003;9t\$å)Ñó´À1\0006\0o:uªÙ)Û³µ§cNÛw:ZrÄé™Ì³·ÀNÒÏ:6uÔéP\0@Næ*\0æsì©×¯çO€9c;:w8 ³ŸçOÎ¡\0a;ºr¼éÉÚÓœ\0Îg‡:X€YßS¢[¡N€\0c:®wTñIá™gyÎØŸ:tôë¹Ğ³¨'†NÕ\0h°äì`¦çwğU9úwŒåéË¦'“ÎŸ9îtüï9ÒÓ¤'TÎÏ7:tï03¹§jNßG¶tüíéá³ 'PÎSJ\0âxÌí	Ü3§çvO!::u€™âs¡gBÏHÏ;x´óYÜàIgzNpğ\0Öz   I'LO[ñ=Bs¤ï‰æ3šç_NöœÊ\0ä¤ñ)Ò¯§>J½9\\¤å¹ä3”\r¶€4‡9ñºñ™ÒÓªç‡Î`œ³<0¬öĞŞ\0NÃ79ö{œç™ÌS 'jÎ«!>nv|øißœ'6Ï©9Šxì¹ÑÓİ§)Ï†<vüñ¹Ñ3œç~Ï¢\0Òy\\ø‰ò3¬'oOL\0i9FuœğyìS•çEOX¹=(”å¹òs»gÁÏ2Ñ:n|„åIè '4N\0i=Fxh)ù³”gBÎ²Ÿj\0Ú}”ø¹üç'£Îª\0g>6|ÄúIì3ÔgçÏØ9=Îsløéòs¯gøN¡Ÿ<Z|4ğùëSòg:N[¼œ6uôûùúSéçÑÎÏœ³;|œæyÒsÑgQ\08Ÿ‡<ÊtäùYåËçAÏÇ?ŞxüùêÀg´Oúó<†€dîZ'g§UONW[<¦tû‰êÓ·§½O6œ±?lÛ¼ğÙòÓgÅNì ?rtõìô\rç;N‘Ÿe>zÔúIú“Ø“†Îfn…>²ìùéé3¬gÅN‚Ÿ%?~tœòê3÷•ÛĞ(W>ötDç™ôÓ»çNÏ©q9Ê|„øî3¯gùÏ‡Ÿ=ú{œıÙÚ³¤'İÎÜ \0ÆwlêĞ\$´'6\0004 Ó@Êƒ¼öº“ã§´€9Ã>¢{1¶Úóæ'«Ox*•;ŠzTíiõ“Ìç‹Ğ SA’~<îJ\r³»çXÏğ 9:.}ûú“¸çêP`m>â„ìä4§>Ïp ³?PÛlêÕT§/ÎóŸ–uùÊéç´OŸ“B.|4ïYÏ“Ÿ§¿Ï2FÂwëéĞÔ.'·ÏËŸ\rBÊv)ít#§fĞf «<ªxT÷3ÒgPi*•=.x,ïZ“¬g¡Ï²¹CNy9õÓÿgÜÏµùC†|5\0°T0§6ÏğñARt©·yÊ´ÔP%:…­éÏS·èVNf¡ë=Rt	Û³·ç=PŞŸ¯Bê=\r	ï4\"'kNŠ é9ê‡TíYÓ³«'gÎœ ƒ<Úu¬îj“§‡Îõ ±;¾tUÙß(jO8Ï?v\\íió3§h;Oó1@}dçš\rt>§ÜP¢iA\rŠ\$ó­gíÏİŸµC2|\n:\nÓ›'¨Ï\nŸÃCò‰ø M¶Î¡¢{?¾Š,ğz)sÈè8ÎÜ @JwíÉòs ¨©ÎÅ¡İCrUÚ+4\0ç‘O\r¢:xøÙá´Rç3NÍQ:bt,÷ÉÔó¬hoNŠœµ<–vüç	Ì4§HQ\$Ï;â~õFèT'yÏ×UDşzKt*¿§Ï6#E.sÍzóµ§«Nv ·>wõz”E'¯ĞGÅ9š~MÊ”WçNOA\0c<\n€M)Ñ“°¨\$NŸ-=¢wôøŠÔh¸O¸¡ûCâ‚m*T!çŸQ KB.tå)ëóÔ(Ğâ¡1GŠeZ3S°g­Ï IDVxŒøª6³üÀĞ0 {?†zÕYäS›gyÏ£¹9vu\\åª9óÀg‰Q­…Ev|Yêeh_Ïœ£³:2x=9ñ“î¨4ÑÜ£!;^†ÙTª!H(ÑÛŸ#?À	,şz/S–'ËOgŸë<…\ndl(:Nø¢§:º‡íê5´MgÒQÕ¢;9b†Üşús¦çN»Ÿ5E²€u4:èqQ…£½F¢}\rêD¢¨Ï¥·9fƒíÉísÁçşO§¤E;ŞÌÿÚ“ÉgˆÒ£û9\\ªU#ùîó¯hBÎ` yB&t\$æ*tŒ(ÏM¢ß?Úí\nGt`¨±Nçœ·H¢züê´]§åĞ£i=æ‘U#ÉØsgãR;G&ƒ=yÔt8§-Q§¡ÉENwLÿ	Ûô2çgÏCËEX…©Û4M§€ÎŞŸ·FF‰ó9ÖtièoO\r¢_GB‡øIÓ´_§JÑXŞœ5ºæêÓÂ@’Q•#aEN{<şÓ¨hÙQÒ…<‚,õ¦èTRgHÑğF~İ\n#Eç¯ÎÂ¡=®zŒıyú¿g0O^¡=Bu\r*PST­çIN½Ÿ->Vƒ„ÿ:0Ô§´OîIBª~¤ê©ç´«h™Ï\" #AÒv,çJE“ôçÜO£/?:\r*5S©çÜPŞœÛF–uóÁèVNy¤§<Šv\r(Yàt5§²Oä¥Å;¾~½)ØT'^Ñã¡ã:uUÊ o(ÿRü¥)Eº€ÍºQS¨ûĞv£5:º‹dÿ¹ş\nizNÿ¤÷H>síZ\0003Ù§KP¸¥eB\"€½	º\"TO)ŒNàÄÂ{ğ:ÔˆéÏE=x]:ô£gKR¿¥ÉK*ˆÙêsÀg®O1J—ôêI÷ô\0’Ğù¢w?VyÌñsÈçüOãmHšu39Êóéh{Ò¾ŸÍDÆ“<şÚ´#`§{€8ñÎÛ=•ï‡Ğ/gb\"Êå§ˆ{Eï1\n@\"3n`}1ŠC8m¿˜¥2N&ÒÕX›ó:Šm,°™A±\\m=M­ŠL5özBd¿Óub{Mİšä&öm³iÁ2Œƒ|ÈŞ?Eô±Xğ1ò‡NZœc3eíEuÇ€Bh\\ÇÔ\nDÃX>iÁ[S„§ÃÚœÓjs‘Y¯DŠ ¨È«3¨`ËşbvbS\nü±.§jÓn8Šu#¹Å×¯ù™	q˜bÿ–‹ğš2¦ìN\n |Æ-‘4âÄÖÊV®:y‘5RRÓÏ'Ãùd'f™\n/ù˜ˆÅ²%ttöWÕ4.§¼]§	…ZàT™JkëO´( z}ò}\0»‡aKÂì‰9„u¦¢kŞ÷D~›è_ì©û´W‚ìÉLrGöt3iıÓ÷fŞÎ•VËøQ,¬Ğ«a0¬aUA6	 Ña¾Ô+{RÜ,:ƒVæSLÉ‰\$W^!7ø1ĞàjS¹\0tM\"\0{J†	WÊ±ˆDQˆÜ8P÷²ÃR_¨Ä°\r£…Dh®[Ø‰r“–+óÜ’ `ÑWÓ³§«e´\0¶'U\"€(_.LŞ¢c\n‹-:E‰\rŠÆ¡<àå\$ø\0P\$™ı>…ø\0ÑYp³Ğ¨ØM ™Bç~Ğé6ê\0ıFFú‹Õ@)° (ÀÊ\$¸4V<lF\0ÂÔƒ`ç3©šL9Õí‘C‡»†È&åH&<Œ«Â“±\0„Î%œ@VÌŒ&\0¤\0sk¤1¼Y¯š“@×»T ecP‰×{`5·‹‰Úÿ…fÔëæ0b6½†o@Â´ÿˆNñ”]<joôò*ZÔ¹wR! ™\nzÔÙê\\0Õa6Î„£ÕEÚ–ªU¡`3\"©H‰³€˜öj€\n¯ŠeÄOa|UKª˜u5Ù1´'ğÀN¦Ğjp1š²2âhSš‹\nùˆÌ¿ ê²òƒSSªENÈR¤Ù)Ø\n‚\n.ÿ˜JV^0j*c3‡|Ãn ë1vdAí”«\"\0¯RŞ¤ñXY«‰Â¸Â¨6–rïS€aS°EeƒTYS.ˆx…u¡s0®4)RĞ#2€¤»İ\0PVˆ)Z&UØj”³+hòÌ‰–€V\$¬ß	¦'×&ÈÍğ ÍS†[LßÌÕ<füY[1¨\\ŒFØZ‡¡ª^¤9VÓMPÔöá6CƒmP6ÄVdÕ# ÏB˜ŸPÔ>`Õõµ1*¨ÓÓ©‚¦Ûø]ÀƒÖ/ÊƒÓ.ªä'*mõ\"é¸ÕbbşĞ-˜ıUvIĞXW»Ã§ªäÉ–I3r}ğyZ@U`dZ°ŸÁ?Ê®íêÁÂ€dhÒáœkæ¢u[*~ÌX…ÌĞm“¸zZ¯5#@Ó€„ñ2X=Wxo°îªÍ/÷gR*mKxPÕg˜ÄÌªîÑâË\$pùøªÓ³Zª_15„mVæ=ukªÔÂÕZÙö­kViµc¦\$Ãv˜–Â-ŠWuüTàêŞS}iRî«L(%WJ¶`ş¯ÀVAUİ´4èSÕná[¶¨¢ÉÒªd+¶£Ljê»³G&éUİš“66TôŞêp2@`i	B­“9\"n­\"*ò²f^ÍnÅUj½ \náUÙ…Í‚\r\\V¬˜ªùÂÒbzĞæ¬EWz¿ÕaÊ0-j©MÂ¬l-¼•}¡wC`…®ÄnL\nyÕ8ª^Uú©~M›{fú¾uG«Bç˜ÃUŞ®u`Æ/í“*»Ìj«æÈÀ\\9FOí*»£K«öÕ´’k5náƒÂn„áP6<hXUjù0‚†V«=ıU¶Õê¯V«+XU%VHv,OZBUh¬‡\r¦Á=š±Œ&!ˆ—`ªâeWJ}ùë,C+«\$¾­ ´2Š¯°WêRTh©¡Yv=XJÍ°ªÈp\0«V\"°5Xº´5“ë6U†¬ÑY„¤-Ê¬5«4—whsVf³['p•g+@Öw«HÌe›`ŠÆÊáÕ¥a“YÒ­›šµì“ªÕÓ­\nÀ\n­£º¶\r«KÕ¨­5VÙ™3ŠÓ°ÌkEÃgañZA‹»\nÕİêãÖ{†t«I­\nØ^ªêè²Œ«¦À´½kZÅáïë*C+†“P%]ê×°ÙêğÖ\r¬Èê¯</zÒì…ªöB¿«ßZ®±ì5”°ÙaUø­©ZíŒågÊoLmjÿÖo§»Z^°fêÎîëÕŒ­K[n¦ımp÷tØœÖê_sXÊšÊÕu‘iêÖ©‰\rXÍaˆ†Uªë1Ä„P.ak:Òõ‰+KÖ&­µ§¥b¦/düç×ª€2¶Ä2h75Œªw¯Ñƒ»UJ±İYJÇ°n+ UZjEŠäCyµ½k#ÔÄ®9ÜL|:±°n+%UfªşÒ rh;µ”k0˜ƒ{Yiª•r'µ—ê½Vb©…Uş³-X*âĞsk5/y®‰YÚımê±5¹k9×Cb¹YµšQ]jÏ5iÀAØ+{Zº4*Ğğg+G×Nb{Zz¢uiR²5×«H°*‡W»#:Ó5bY`Õ¸®ÓZb­ÕkŠë,OkS×o«‡RŞ\rÅ7êÏuŞkXUÍ®]ÍE]j²•­ë¼Aİ­u:µåsªÌŒjšWŠ­[\n¼u_FÊõ±˜ŸÓ|­Ğ¶Ux\n½IÄÇUí_.Å\0›:CõzëÎÖÎa§^F¸Ü!ÊÚuäa×¬†»X™\"šõõ¸«Öä¬åUR¼,\"jÁuë«ÊÕí®õ[Ú½ea*ä•Õ`îÖ„ƒv¼pJóLqY6×‘¬Q\\*¢•pÊò5‹«ÄÀ¬cùˆ„O’±AòS?8˜Än¸Ä9f…lÍÈÖ:¯Ä†µyè<ÀÙ/‘x\$M~ê¸•Çj­°ƒÏQ­~<ÂµlÔ)²×ùˆdV®¿±6šäËã«¼\0TH ¾Dµv6k‘ÚB[bÌN¬D\npìÈ˜	Ä‰¯2Ø\nwÔX o×ü¼É¨ÑNùUõ‚\nšS—öS¿°Nù‹+@ğÂ)ïŸhµOn\")7‹\0\$Ìê®0Ó\0¾L¨j|º{À+ö2°vĞÁ†tS!@)¶KfW_öÂÌJÆv˜ÊØ§Î¾8ší„òjlD˜_ØOƒ²ÍBaUE[t÷«3c§½MvÂû;…ü«\0__O5†b2¶É\r’ƒÓ3G'±_òÃ[;9’,ó¬8³¸¯•V UT¦Sì#¡@4É'µR@<ı‡\nË , Ö÷±	a6Á¥[UjØwØ:±3¦õ‡Àô-.\nX’¨0ÍÊÄ|¶dMXG\0B±Â\nÄõ~ÚË\0 ²Ô®xOÎ¹8gö)À,ÖQ±M]LŸŠ¢ev,\0,•«˜ñb¦²E‹:¯6,IÁLx±]YbÅÒú²~l‹˜\0HU~Î’Æ0˜z0	´5j+ySÖÄ¤L8E•¬VX¸©‰YâÅİŠÒm65,ZS€±±c\nJ¬P\n1©ÛD!l’M’Á»«ÌŸ×}¨/c¤KBÚ«U™Í Øği’ÀJ­ûóiØBd÷c¡«AÊ³ÌK*×\rcab~È=fjÓ…]ÙmX=±1d1”ÅpË!r¬‰X¢²!	¾¸eˆZˆ¶¬…hÍTŞÈñ6F›UrÚĞTBfÁUÈğziƒL\nëW`ˆ°} µ‘Ò®õÚáÓ|²eQ4ûéÀ&¯İ‚ñ0Ù«s%X;5	ÉY>U°P&e‡¦U6Q,Ìfµe0œ5”Å2•[j+W«ÑeU£á<í¢™×²ıg­Qx>[H0ô@l\"“ó²Âa´P‚~uú*/4g]`|+*6uÖE«33¸ˆÉQ²£M–úêV^Ù‡Ì`)Q½­»&L>,Á±Q²‚Íl½Ûm“l\"ªÍ„\0L±¬å—ú4ŸÂgf.£`;*5*@ÙœaÉ\n© MHWÆpFl¸\0(4Ãb~¿İ‘f—L´UŒ2Ñ­,Qa Ğ6ø)‰˜Y2icĞ=\0˜úÃ@\nàYhXÙ¤™…‚¶\0«İš¹€W†']É­İˆàu™EUÅf•W5š®°«,\$Sè«ó^£i;È?äÖY×¯²mÂnÎÙ2Ëöc¬îY³ÉgŠ=–{<cÀ*Y2³Ïg¶ÄBüU-˜–WœhâÂb¥µH†l¢æ}0©z¬a]Luñ\rb,6P³ÕRÕ‚] Köm*[µNj•`ø)ªàI¦1mÁ¤®Ğ\0¢=”†-¡køÚqcÒ!öÌ­¡®Ù\0 \\\$Èt\rœ-XßfDÀ–¯‘eWó/s…™Ùcı\r†–ö3Ùq1ç²QSP™[GEîÌ¸š¯l‚3P›f\\UTìª¦„öÂa©ë2\"~&­¸¯ØÃ‘Œ£÷×,1/ˆa^ÉZÍİ›4¶zl‹¼iÿ_ò«­„*¿ÖL˜c°É‰´Î†ÓkûM„ä«å5œa¶ó\0è\$†™ØyÚx6øl†İ\\Jó–…á!°Ûjí²Ezşõ1İTqaæÀ–³.àõlÍ£ù\0¨Lı;;Rëİk†´eÜVÄ«8Fì•«ÌØ'µDLÊÄûkU™[&4Ğ´Çj´¦Vo¡ïZ~UijNû\r»Väß™ßÚµµt6ÀŸQ-l©ÕFgkjéŠİ“ÚùV*‹So`z¿fe€L¦#uK×Õ³¡ª^ÏNÍ)3{Wím_U˜Ã_±k\r‚hE¡xXŸ¬ChEb&LÎ&?ª[jT™Ôz¥\$Ì­y´Ï³Kj¶È³Zá –{7’7è(´\rˆ!Û¢Úü«kùæ°6øÚ|\0Pà4(*˜×«^fêI5\nÖâÁ\nØ÷Nu  ´*…[Ê Räk@¹K{V\0Œ	äYÅ?Ô’¬`B –á6À1ªª\0É€.#æ4ĞÀ3ÖÍÀÇE»<¬–^ò(€Æá„—™Á4š–ÒÚÍEç\"À1€d\0^\nŠdí`Ä¸ƒm<\0\\ğTÏ\0000—>²‚O0W2MÔ€D¾Û`XGmˆ)ÈKl°Q­°† ¶Ç<FÛXI9VÛ‚œ€6¶ã'7Í¶ğöİí¹…õ\nr\0âÛå¶ÛoÔíÀÛg¶é;Ì…¸Uëa}m®‡s	‡m²Ü5¸À\$¤­ÇÛr·A’Û%¹[oØ­®Ûj·5möÜÅ·söåí®Ûv·GnFİ\r·»töâíĞÛ€·Vnİ›qVìÏ\$·gmæİ­¸áföä-Ö[µ·)nîÜµº‹v¨¨-×Ûy­;Îİ½¶+xöïÑäÛÁ·Y>âŞE¼[lVô-æ[f·ŸnfŞ¹»mÖô-Ï[Ù·Ao@­¼›{`ImÓ[Ş·/oBİU¾kuô-×[ê·®\0Âİ•½+|ví­ùõınêŞm½«~Öäíñ[û;oôü÷ë{¶şmĞO~·ÃoÖà\r¾K€.%·ÛpÜ¼÷ë~6û­ØJ·épRÛÍÁkõ®\nĞ®¸:rŞÁ‹lW-êÛ¼·ap‚Ş%Â+m×.Ü¶óp‚à=Â ¾·-òÜ·Qp‚ßMÃku—.Ü.¸GnàDéË‚÷®Îó¸7pšá}Áëˆ3¼îÜD¸“AÂàüñÛŠ3½.\0NT¸Cq:ÛÅk„¶ó­Ø\\V¸›púÛuÅk…·-±Q\\¸qrÛÍË‹vş€/Ñ\\¸¿q’ÛuËŒw.2Ó=¸Erá­Æ\0¾´W.Üz¸Ûp:ã½Ä†Wî!Ü~ êä\r{‘7î:\\q£r&áÅ‹‘×.\\‡¸ûqÂãÜó«‘7îI\\™;=r•Æ{”n5Û  sr2å;”\$nCOÔ¸órbãüê”nXm¸¯qúåµÊ[r÷-®T[ ¹mqÎãeËk•—-n\\¹mr^äh_[–×-.d€_¹mrråÅ¸‹€ÛnD\\Æ¸•ræ¥ËÛ—w#îkP¹§;Îå}ÌÛš—2.`Üš¹»sBæ%Éûš×(n\09¹orÂçuË«u;®m\\óK9öçmÏ{˜w3nw\\á¸ÙsºæBÊ[–ån[ÿº		-Ç; ·>î‚ÜáºtèDìÛn7Ant\0'ºro%Ğ‹š¶ğ.‹Ü[º/r2è½Ğ«£WC.]º5tAdİÎk£—F®Šİ¹Õt®éMÎË ÷Ln]ŸµtNéµÒ+¦7I.˜İ&ºct¡fDıK™—K®N\0#\0ÓFuí2Ø÷Ln¢İG;Ò{ÅÔ»¨3µ®œ])ŸÏuVêÓ[ª×H.„Pâº°j&v½ÕóN4Dî±;uëè«¬ nçNİ-º>âëX9Ï·îO×ºã9öêåÒ™ğ7]ç¼İ|º}uƒµ×Ë¨w[î·])*•uŞs=Ø‹¯7Pg-]ˆºyv2ëıÖ	ÎWb.Áİ†»utå×yò·f®­]˜»u‚{\rÙ«±÷h.Èİc½vjì­Ù‹²÷X'*]wEvºìíÚË³÷XçXİ®»GvÒí-ÖiÛ÷k®Ôİ¬»Wut×yÊWp®ØİÀ»guš€uÜ+¶÷r.Üİlœíw\nííÜ·÷Y¶İw…wZî-İK¸÷['¼]Ö»—wrîm×)ß×u®èİÔ»¦ì)Í×\$—] a[X»Õté‚Ì€\$—N®ù[¢»âj&{eŞû­œ@’]å»ÑoÂïUÑ‹¢×{nÛÍYJ êîÍŞ‰â÷€nú]ø¹½tÎï•ÉK¾÷')Ş»ı9zŠeŞ«¥÷®öİ2¼&Lj=à™Ë×Mï]øŸ“xjîıâ;¼7R/\r^£OwrõÕKÄ·.«Ş3¼@İ\nïTç«Ç7ƒo^)/wîñ½ââ6z®¸^6»Ñ>şò%â*vç/]{¼y6ñôîÛÈ—/#Ş.Ÿ\rwªì=äú—{MĞ¼•GVòÅãËÄÄ¯,^+»&PA÷(i,^¼Î\nóK­ ×›'s^n¸syÆó\\ñyÒw›¯Oh»ÕvfòÕÙ»Ğ7’§jŞ¼Åw¢suçûÅwiï@Ş.ëwªímå«µ÷¤¯%N…½!z~uéÅwnïI^.{wªz…êÉ“”oLNQ¼}9¶õåIË×uo-]×½[y*uõŞ«»·«oP~½exºz×k·®§ê^¼¼˜vzôÁÙëÅ'g®ğ½Tvzğ¹ÙëÆ(mÄŞO¹CyqfEæË×W‚ïg\\P½«{ğ•áÅ“w(ocÎQ½§{.ğİÕ#Q3¤¯h^ã%zêñ%áğ7s¤¯`Ï½Ñ{å•ìéØ×º.hSUT?l~`j?ˆ7¬sX÷×Ë®‹¬º÷5ÓÉë€K¨§]¾¼%yîµòØ|V!¯\"(Ÿ» ªùpÎáÓÃ=…^¿>¼ñHUï‹²[«Y]¾Õz(;­(×obŠ.É-òj»uão•[ªw]¾¶ezVĞUúa³0¯QS­˜|Éœ«İë×BÏ®mvÓw(E16,İÌ°«]¾[{éò/¤Wƒq_*\rÅpi‚0•k÷\$j«š°uoüÒ©ÚÔ„°‡Î¦=Ëœ¯~Xô„ğĞ.Ê_:®4ØØU…™)c\"°íuïõ=jËß®»}Å©©5–Ë`ï‚ß¬;|&¯ğ¸TLTo‡Uó¾ MÊ®	 U]ïC¨†}|b¯øÛâĞê¯Y¯„ñ|†«½òKU‡o“ß)¿8Å\"µ‹\$‰µras\0P¾ÛWÎµıb*Ì—éëÔÕLÆ¢\r_:Çµ|oœ1!¾w}†ezÚ¿ oŸV¿M|¾°ìM:ßVná<V¿ŠÅÒUa»é•‡oÂßÌ…àÈêıW{ó+æØÀ'GPAV+(Uuƒkx	‡½Yzû-p\nÊP˜Ö«®‹VºE÷;ü×İ«sÓLL²ûİiy“÷Á«éV£®ãRİ%ğ»ÿ7á+¿±Ä¿|J½ø©¥ïÉ_¿)Z¯\0,:šù‹óØ×V«¿1Z^üÑ@ÊÖ—èvÖ¹¾W[µó[ûøÔYæÔÙ†ŠLÚ¶…ójÙwíWŞß·±7®Î•:Ü¾I¼Vû­/[öaü‹éUÃ«T­ùö¸\n²ú\0+{Ã+¯•0’ü´9ÔØï¼¨ZËT˜<,Ko¸1Ìm(Ìb½Wºští,ÙÔò†ĞÙ¥ƒ#9ºÀ·í5eÅhÊ…Išš—Î*|ÌıÀASøšÍP\n‰ÄŞ\"'“6f+©›ı_–E¹êwUeÓTr mRˆ¼Î)o_‘èLnÁ\0k[Úàı(™vÖñQ\\j»eÀ‘5.¸š~N)È¸¤ÄmØ b`©¨±à]iÖH³pë È;w\0q·:l\0S Qw}©è\0å§è\\öó·XÃ¼ï¿Iòp™‚‰=6ß)ú DN&<6Ç1\n…ÓĞ8»—ƒmå¨¢Dğ^	ûƒ5ex¼)’„‚ÁŠàG,f¼‘›[˜G.u2tË8Ã¸}úã—F|‹~ßq?A‹ôœÀ¤öP’üszJü	…à†yŒùxÀö\r*Šä¿ˆIÌ¢q<2Œ1ˆ€À1\0c\0º‹iÀ:ÈCˆR‡@Ô8’±ƒ†-\0c®`aJ¸ˆLs½‚Ç¨—1’	çÌâQ¬Üe¶ENø)L‚RÂ&`QÎ£\0Š0›¸6'mMƒ…Õk…ì%Î.GÆoY óS‹m÷ğIùÀÂ¤|ÖîÜìP(¸P\r€ÈG+Ñ…\0U–Ğ7/MbÈU&.Rí\\|>\0JMá°©+@L2í\\6Œ**Eˆ)ZÔòòV¹1\nmbÜIˆÁ*Ô9{µtÎÕÛz¹ÎmñƒøFˆ‡\0l[¬¸àÁÙ²2””Kq«:ù³…Š¥N\n×]ä(£:ùé…Q×|xG	rŞŒuó†(càŒ.‚úá‹Á1†%Ô‹»Œ'øf^‰Ã	¹-÷aİêX uòkövÃ`ÃÅ³¨~ë¼¨§¾2ğ×… Ã†Øf	0ÆÒŠ°k¦\"\r;¸)eX>pà8J«)N¨˜Jl8ÀíFÉÔÃ…¤+uYM³áÑ^çY²É'»Ø(\n°XÃ¢UÍs#‡0ÀtÓJIÃ‡m2r‘Óî)°ñáá]”0Ÿ³Í<;Æpò-¶Ãª`6køL,8yÁ@ÁÔ`4]ÊoÇ9€Vp}°ß1İ.\"€À•6ßÄˆ0F à0¸\\\0æb\rç0…£Ô,)%¤E•Ee)ˆcèğ<D+êNÁ0|'8¤ÓëpI	œT&4Œ„ı¦2ºä\\F\0İq%1GÕˆ`:œÈ¢Ø¤ŸaMÂU4ğ Øºw¸„ğUbA–#R´T¼Bx[FÃ…o™…”l4ƒ8\"RğwbQÁÂİ}¹nä…xof§wİll0_¤@D€•ˆì)‚‘ÕÛĞ\0e ŸœmˆÂê1ô2SrÜLO{úâqÄÃ,!å',OO±;bnD¶â<b “5àt±;Ûnæ	JOn(\\N¸™â¸+åc‰ë6(É)Âv™‹b~\0ïËf''¸¦œd‘Å86Ú1órÙªxJœf7-VêúÅë¸ÄH¼8©°ÊX680L ğÇb°ë„DàÍx­—”¸ÅYÉ\0EFeĞËÒ\0¢ÀìÅ~\n¥yV*Ì€ìW~CñI½‚@3œ7±næPÂ¾ßƒ£¢øEâÙ½*l6¦üæ‡±6ÅŠúß«\rl,\\B1nDlh':,>.Hø¸ƒœ'D+‹ÉuRhÃÁ\nFÅ‚NKv.ÜZ€\0=bàÃg0EQE}’\nq…ª•ŞvWxT›ÀÃtªC‚Ä¨8•Ğ0®À0CMWªBp»ú/l?øIğm…Á¸/5Q R\0Ñ@;JB%Òš¸¤@ÑATâÆ¶\"ÚjÑ<f˜Î\ny¸Æl?áá³JÍÄvàÆÆv,4r•x)T¤Áç4ş4Bé¸Ñ	\rÌÆ,ËLh‡„v'ÆˆTG<Œ%´.tå§Û–¢t¤h9jrå§ –®­&qÒâìm\0pqµ[EVäŠ4^)pEa!_Æ.6\0ƒğA¿C‚M”'\$DEi>	€ î'‘kÁWˆD0ä\r`\nB(@&\\w>±örÀ¦€)¥±Uhà9Ìn`qÈ,öÇ+„8W‰G@‘,ÊÄL¸ÍÒa€#8éWc›‰5Èë:<mNvÔƒÉvâÈ,Ü \\«\$¢¢*K‹4ˆãã„5¾ufÁ§:IÒÓ&<şĞêH“mfã¹L4˜yÂpÀ");}elseif($_GET["file"]=="logo.png"){header("Content-Type: image/png");echo"‰PNG\r\n\n\0\0\0\rIHDR\0\0\09\0\0\09\0\0\0~6¶\0\0\0000PLTE\0\0\0ƒ—­+NvYt“s‰£®¾´¾ÌÈÒÚü‘üsuüIJ÷ÓÔü/.üü¯±úüúC¥×\0\0\0tRNS\0@æØf\0\0\0	pHYs\0\0\0\0\0šœ\0\0´IDAT8Õ”ÍNÂ@ÇûEáìlÏ¶õ¤p6ˆG.\$=£¥Ç>á	w5r}‚z7²>€‘På#\$Œ³K¡j«7üİ¶¿ÌÎÌ?4m•„ˆÑ÷t&î~À3!0“0Šš^„½Af0Ş\"å½í,Êğ* ç4¼Œâo¥Eè³è×X(*YÓó¼¸	6	ïPcOW¢ÉÎÜŠm’¬rƒ0Ã~/ áL¨\rXj#ÖmÊÁújÀC€]G¦mæ\0¶}ŞË¬ß‘u¼A9ÀX£\nÔØ8¼V±YÄ+ÇD#¨iqŞnKQ8Jà1Q6²æY0§`•ŸP³bQ\\h”~>ó:pSÉ€£¦¼¢ØóGEõQ=îIÏ{’*Ÿ3ë2£7÷\neÊLèBŠ~Ğ/R(\$°)Êç‹ —ÁHQn€i•6J¶	<×-.–wÇÉªjêVm«êüm¿?SŞH ›vÃÌûñÆ©§İ\0àÖ^Õq«¶)ª—Û]÷‹U¹92Ñ,;ÿÇî'pøµ£!XËƒäÚÜÿLñD.»tÃ¦—ı/wÃÓäìR÷	w­dÓÖr2ïÆ¤ª4[=½E5÷S+ñ—c\0\0\0\0IEND®B`‚";}exit;}if($_GET["script"]=="version"){$o=get_temp_dir()."/adminer.version";@unlink($o);$q=file_open_lock($o);if($q)file_write_unlock($q,serialize(array("signature"=>$_POST["signature"],"version"=>$_POST["version"])));exit;}if(!$_SERVER["REQUEST_URI"])$_SERVER["REQUEST_URI"]=$_SERVER["ORIG_PATH_INFO"];if(!strpos($_SERVER["REQUEST_URI"],'?')&&$_SERVER["QUERY_STRING"]!="")$_SERVER["REQUEST_URI"].="?$_SERVER[QUERY_STRING]";if($_SERVER["HTTP_X_FORWARDED_PREFIX"])$_SERVER["REQUEST_URI"]=$_SERVER["HTTP_X_FORWARDED_PREFIX"].$_SERVER["REQUEST_URI"];define('Adminer\HTTPS',($_SERVER["HTTPS"]&&strcasecmp($_SERVER["HTTPS"],"off"))||ini_bool("session.cookie_secure"));@ini_set("session.use_trans_sid",'0');if(!defined("SID")){session_cache_limiter("");session_name("adminer_sid");session_set_cookie_params(0,preg_replace('~\?.*~','',$_SERVER["REQUEST_URI"]),"",HTTPS,true);session_start();}remove_slashes(array(&$_GET,&$_POST,&$_COOKIE),$bd);if(function_exists("get_magic_quotes_runtime")&&get_magic_quotes_runtime())set_magic_quotes_runtime(false);@set_time_limit(0);@ini_set("precision",'15');function
lang($u,$Kf=null){$ua=func_get_args();$ua[0]=$u;return
call_user_func_array('Adminer\lang_format',$ua);}function
lang_format($ej,$Kf=null){if(is_array($ej)){$Og=($Kf==1?0:1);$ej=$ej[$Og];}$ej=str_replace("'",'â€™',$ej);$ua=func_get_args();array_shift($ua);$nd=str_replace("%d","%s",$ej);if($nd!=$ej)$ua[0]=format_number($Kf);return
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
query($H,$oj=false){$I=$this->pdo->query($H);$this->error="";if(!$I){list(,$this->errno,$this->error)=$this->pdo->errorInfo();if(!$this->error)$this->error='æœªçŸ¥é”™è¯¯ã€‚';return
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
connect($N,$V,$F){if($F!="")return'æ•°æ®åº“ä¸æ”¯æŒå¯†ç ã€‚';return
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
check_sqlite_name($B){$Qc="db|sdb|sqlite";if(!preg_match("~^[^\\0]*\\.($Qc)\$~",$B)){connection()->error=sprintf('è¯·ä½¿ç”¨å…¶ä¸­ä¸€ä¸ªæ‰©å±•ï¼š%sã€‚',str_replace("|",", ",$Qc));return
false;}return
true;}function
create_database($j,$c){if(file_exists($j)){connection()->error='æ–‡ä»¶å·²å­˜åœ¨ã€‚';return
false;}if(!check_sqlite_name($j))return
false;try{$_=new
Db();$_->attach($j,'','');}catch(\Exception$Ic){connection()->error=$Ic->getMessage();return
false;}$_->query('PRAGMA encoding = "UTF-8"');$_->query('CREATE TABLE adminer (i)');$_->query('DROP TABLE adminer');return
true;}function
drop_databases($i){connection()->attach(":memory:",'','');foreach($i
as$j){if(!@unlink($j)){connection()->error='æ–‡ä»¶å·²å­˜åœ¨ã€‚';return
false;}}return
true;}function
rename_database($B,$c){if(!check_sqlite_name($B))return
false;connection()->attach(":memory:",'','');connection()->error='æ–‡ä»¶å·²å­˜åœ¨ã€‚';return@rename(DB,$B);}function
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
__construct(Db$f){parent::__construct($f);$this->types=array('æ•°å­—'=>array("smallint"=>5,"integer"=>10,"bigint"=>19,"boolean"=>1,"numeric"=>0,"real"=>7,"double precision"=>16,"money"=>20),'æ—¥æœŸæ—¶é—´'=>array("date"=>13,"time"=>17,"timestamp"=>20,"timestamptz"=>21,"interval"=>0),'å­—ç¬¦ä¸²'=>array("character"=>0,"character varying"=>0,"text"=>0,"tsquery"=>0,"tsvector"=>0,"uuid"=>0,"xml"=>0),'äºŒè¿›åˆ¶'=>array("bit"=>0,"bit varying"=>0,"bytea"=>0),'ç½‘ç»œ'=>array("cidr"=>43,"inet"=>43,"macaddr"=>17,"macaddr8"=>23,"txid_snapshot"=>0),'å‡ ä½•å›¾å½¢'=>array("box"=>0,"circle"=>0,"line"=>0,"lseg"=>0,"path"=>0,"point"=>0,"polygon"=>0),);if(min_version(9.2,0,$f)){$this->types['å­—ç¬¦ä¸²']["json"]=4294967295;if(min_version(9.4,0,$f))$this->types['å­—ç¬¦ä¸²']["jsonb"]=4294967295;}$this->insertFunctions=array("char"=>"md5","date|time"=>"now",);$this->editFunctions=array(number_type()=>"+/-","date|time"=>"+ interval/- interval","char|text"=>"||",);if(min_version(12,0,$f))$this->generated=array("STORED");$this->partitionBy=array("RANGE","LIST");if(!$f->flavor)$this->partitionBy[]="HASH";}function
enumLength(array$m){$_c=$this->types['ç”¨æˆ·ç±»å‹'][$m["type"]];return($_c?type_values($_c):"");}function
setUserTypes($nj){$this->types['ç”¨æˆ·ç±»å‹']=array_flip($nj);}function
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
__construct(Db$f){parent::__construct($f);$this->types=array('æ•°å­—'=>array("number"=>38,"binary_float"=>12,"binary_double"=>21),'æ—¥æœŸæ—¶é—´'=>array("date"=>10,"timestamp"=>29,"interval year"=>12,"interval day"=>28),'å­—ç¬¦ä¸²'=>array("char"=>2000,"varchar2"=>4000,"nchar"=>2000,"nvarchar2"=>4000,"clob"=>4294967295,"nclob"=>4294967295),'äºŒè¿›åˆ¶'=>array("raw"=>2000,"long raw"=>2147483648,"blob"=>4294967295,"bfile"=>4294967296),);}function
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
__construct(Db$f){parent::__construct($f);$this->types=array('æ•°å­—'=>array("tinyint"=>3,"smallint"=>5,"int"=>10,"bigint"=>20,"bit"=>1,"decimal"=>0,"real"=>12,"float"=>53,"smallmoney"=>10,"money"=>20),'æ—¥æœŸæ—¶é—´'=>array("date"=>10,"smalldatetime"=>19,"datetime"=>19,"datetime2"=>19,"time"=>8,"datetimeoffset"=>10),'å­—ç¬¦ä¸²'=>array("char"=>8000,"varchar"=>8000,"text"=>2147483647,"nchar"=>4000,"nvarchar"=>4000,"ntext"=>1073741823),'äºŒè¿›åˆ¶'=>array("binary"=>8000,"varbinary"=>8000,"image"=>2147483647),);}function
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
loginForm(){echo"<table class='layout'>\n",adminer()->loginFormField('driver','<tr><th>'.'ç³»ç»Ÿ'.'<td>',html_select("auth[driver]",SqlDriver::$drivers,DRIVER,"loginDriver(this);")),adminer()->loginFormField('server','<tr><th>'.'æœåŠ¡å™¨'.'<td>','<input name="auth[server]" value="'.h(SERVER).'" title="hostname[:port]" placeholder="localhost" autocapitalize="off">'),adminer()->loginFormField('username','<tr><th>'.'ç”¨æˆ·å'.'<td>','<input name="auth[username]" id="username" autofocus value="'.h($_GET["username"]).'" autocomplete="username" autocapitalize="off">'.script("const authDriver = qs('#username').form['auth[driver]']; authDriver && authDriver.onchange();")),adminer()->loginFormField('password','<tr><th>'.'å¯†ç '.'<td>','<input type="password" name="auth[password]" autocomplete="current-password">'),adminer()->loginFormField('db','<tr><th>'.'æ•°æ®åº“'.'<td>','<input name="auth[db]" value="'.h($_GET["db"]).'" autocapitalize="off">'),"</table>\n","<p><input type='submit' value='".'ç™»å½•'."'>\n",checkbox("auth[permanent]",1,$_COOKIE["adminer_permanent"],'ä¿æŒç™»å½•')."\n";}function
loginFormField($B,$Id,$Y){return$Id.$Y."\n";}function
login($Ue,$F){if($F=="")return
sprintf('Admineré»˜è®¤ä¸æ”¯æŒè®¿é—®æ²¡æœ‰å¯†ç çš„æ•°æ®åº“ï¼Œ<a href="https://www.adminer.org/en/password/"%s>è¯¦æƒ…è§è¿™é‡Œ</a>ã€‚',target_blank());return
true;}function
tableName(array$zi){return
h($zi["Name"]);}function
fieldName(array$m,$eg=0){$U=$m["full_type"];$ob=$m["comment"];$pb='<span style="white-space:pre-line;">'.h($ob).'</span>';return'<span title="'.h($U.($ob!=""?($U?": ":"").$ob:'')).'">'.h($m["field"]).($ob?'<br />'.$pb:'').'</span>';}function
selectLinks(array$zi,$O=""){$B=$zi["Name"];echo'<p class="links">';$Se=array("select"=>'é€‰æ‹©æ•°æ®');if(support("table")||support("indexes"))$Se["table"]='æ˜¾ç¤ºç»“æ„';$ze=false;if(support("table")){$ze=is_view($zi);if(!$ze)$Se["create"]='ä¿®æ”¹è¡¨';elseif(support("view"))$Se["view"]='ä¿®æ”¹è§†å›¾';}if($O!==null)$Se["edit"]='æ–°å»ºæ•°æ®';foreach($Se
as$x=>$X)echo" <a href='".h(ME)."$x=".urlencode($B).($x=="edit"?$O:"")."'".bold(isset($_GET[$x])).">$X</a>";echo
doc_link(array(JUSH=>driver()->tableHelp($B,$ze)),"?"),"\n";}function
foreignKeys($R){return
foreign_keys($R);}function
backwardKeys($R,$yi){return
array();}function
backwardKeysPrint(array$Da,array$K){}function
selectQuery($H,$pi,$Tc=false){$J="</p>\n";if(!$Tc&&($Qj=driver()->warnings())){$t="warnings";$J=", <a href='#$t'>".'è­¦å‘Š'."</a>".script("qsl('a').onclick = partial(toggle, '$t');","")."$J<div id='$t' class='hidden'>\n$Qj</div>\n";}return"<p><code class='jush-".JUSH."'>".h(str_replace("\n"," ",$H))."</code> <span class='time'>(".format_time($pi).")</span>".(support("sql")?" <a href='".h(ME)."sql=".urlencode($H)."'>".'ç¼–è¾‘'."</a>":"").$J;}function
sqlCommandQuery($H){return
shorten_utf8(trim($H),1000);}function
sqlPrintAfter(){}function
rowDescription($R){return"";}function
rowDescriptions(array$L,array$ld){return$L;}function
selectLink($X,array$m){}function
selectVal($X,$_,array$m,$og){$J=($X===null?"<i>NULL</i>":(preg_match("~char|binary|boolean~",$m["type"])&&!preg_match("~var~",$m["type"])?"<code>$X</code>":(preg_match('~json~',$m["type"])?"<code class='jush-js'>$X</code>":$X)));if(is_blob($m)&&!is_utf8($X))$J="<i>".sprintf('%d å­—èŠ‚',strlen($og))."</i>";return($_?"<a href='".h($_)."'".(is_url($_)?target_blank():"").">$J</a>":$J);}function
editVal($X,array$m){return$X;}function
config(){return
array();}function
tableStructurePrint(array$n,$zi=null){echo"<div class='scrollable'>\n","<table class='nowrap odds'>\n","<thead><tr><th>".'åˆ—'."<td>".'ç±»å‹'.(support("comment")?"<td>".'æ³¨é‡Š':"")."</thead>\n";$si=driver()->structuredTypes();foreach($n
as$m){echo"<tr><th>".h($m["field"]);$U=h($m["full_type"]);$c=h($m["collation"]);echo"<td><span title='$c'>".(in_array($U,(array)$si['ç”¨æˆ·ç±»å‹'])?"<a href='".h(ME.'type='.urlencode($U))."'>$U</a>":$U.($c&&isset($zi["Collation"])&&$c!=$zi["Collation"]?" $c":""))."</span>",($m["null"]?" <i>NULL</i>":""),($m["auto_increment"]?" <i>".'è‡ªåŠ¨å¢é‡'."</i>":"");$k=h($m["default"]);echo(isset($m["default"])?" <span title='".'é»˜è®¤å€¼'."'>[<b>".($m["generated"]?"<code class='jush-".JUSH."'>$k</code>":$k)."</b>]</span>":""),(support("comment")?"<td>".h($m["comment"]):""),"\n";}echo"</table>\n","</div>\n";}function
tableIndexesPrint(array$w,array$zi){$zg=false;foreach($w
as$B=>$v)$zg|=!!$v["partial"];echo"<table>\n";$Tb=first(driver()->indexAlgorithms($zi));foreach($w
as$B=>$v){ksort($v["columns"]);$Xg=array();foreach($v["columns"]as$x=>$X)$Xg[]="<i>".h($X)."</i>".($v["lengths"][$x]?"(".$v["lengths"][$x].")":"").($v["descs"][$x]?" DESC":"");echo"<tr title='".h($B)."'>","<th>$v[type]".($Tb&&$v['algorithm']!=$Tb?" ($v[algorithm])":""),"<td>".implode(", ",$Xg);if($zg)echo"<td>".($v['partial']?"<code class='jush-".JUSH."'>WHERE ".h($v['partial']):"");echo"\n";}echo"</table>\n";}function
selectColumnsPrint(array$M,array$e){print_fieldset("select",'é€‰æ‹©',$M);$s=0;$M[""]=array();foreach($M
as$x=>$X){$X=idx($_GET["columns"],$x,array());$d=select_input(" name='columns[$s][col]'",$e,$X["col"],($x!==""?"selectFieldChange":"selectAddRow"));echo"<div>".(driver()->functions||driver()->grouping?html_select("columns[$s][fun]",array(-1=>"")+array_filter(array('å‡½æ•°'=>driver()->functions,'é›†åˆ'=>driver()->grouping)),$X["fun"]).on_help("event.target.value && event.target.value.replace(/ |\$/, '(') + ')'",1).script("qsl('select').onchange = function () { helpClose();".($x!==""?"":" qsl('select, input', this.parentNode).onchange();")." };","")."($d)":$d)."</div>\n";$s++;}echo"</div></fieldset>\n";}function
selectSearchPrint(array$Z,array$e,array$w){print_fieldset("search",'æœç´¢',$Z);foreach($w
as$s=>$v){if($v["type"]=="FULLTEXT")echo"<div>(<i>".implode("</i>, <i>",array_map('Adminer\h',$v["columns"]))."</i>) AGAINST"," <input type='search' name='fulltext[$s]' value='".h(idx($_GET["fulltext"],$s))."'>",script("qsl('input').oninput = selectFieldChange;",""),checkbox("boolean[$s]",1,isset($_GET["boolean"][$s]),"BOOL"),"</div>\n";}$Ta="this.parentNode.firstChild.onchange();";foreach(array_merge((array)$_GET["where"],array(array()))as$s=>$X){if(!$X||("$X[col]$X[val]"!=""&&in_array($X["op"],adminer()->operators())))echo"<div>".select_input(" name='where[$s][col]'",$e,$X["col"],($X?"selectFieldChange":"selectAddRow"),"(".'ä»»æ„ä½ç½®'.")"),html_select("where[$s][op]",adminer()->operators(),$X["op"],$Ta),"<input type='search' name='where[$s][val]' value='".h($X["val"])."'>",script("mixin(qsl('input'), {oninput: function () { $Ta }, onkeydown: selectSearchKeydown, onsearch: selectSearchSearch});",""),"</div>\n";}echo"</div></fieldset>\n";}function
selectOrderPrint(array$eg,array$e,array$w){print_fieldset("sort",'æ’åº',$eg);$s=0;foreach((array)$_GET["order"]as$x=>$X){if($X!=""){echo"<div>".select_input(" name='order[$s]'",$e,$X,"selectFieldChange"),checkbox("desc[$s]",1,isset($_GET["desc"][$x]),'é™åº')."</div>\n";$s++;}}echo"<div>".select_input(" name='order[$s]'",$e,"","selectAddRow"),checkbox("desc[$s]",1,false,'é™åº')."</div>\n","</div></fieldset>\n";}function
selectLimitPrint($z){echo"<fieldset><legend>".'èŒƒå›´'."</legend><div>","<input type='number' name='limit' class='size' value='".intval($z)."'>",script("qsl('input').oninput = selectFieldChange;",""),"</div></fieldset>\n";}function
selectLengthPrint($Pi){if($Pi!==null)echo"<fieldset><legend>".'æ–‡æœ¬æ˜¾ç¤ºé™åˆ¶'."</legend><div>","<input type='number' name='text_length' class='size' value='".h($Pi)."'>","</div></fieldset>\n";}function
selectActionPrint(array$w){echo"<fieldset><legend>".'åŠ¨ä½œ'."</legend><div>","<input type='submit' value='".'é€‰æ‹©'."'>"," <span id='noindex' title='".'å…¨è¡¨æ‰«æ'."'></span>","<script".nonce().">\n","const indexColumns = ";$e=array();foreach($w
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
messageQuery($H,$Qi,$Tc=false){restart_session();$Kd=&get_session("queries");if(!idx($Kd,$_GET["db"]))$Kd[$_GET["db"]]=array();if(strlen($H)>1e6)$H=preg_replace('~[\x80-\xFF]+$~','',substr($H,0,1e6))."\nâ€¦";$Kd[$_GET["db"]][]=array($H,time(),$Qi);$li="sql-".count($Kd[$_GET["db"]]);$J="<a href='#$li' class='toggle'>".'SQLå‘½ä»¤'."</a> <a href='' class='jsonly copy'>ğŸ—</a>\n";if(!$Tc&&($Qj=driver()->warnings())){$t="warnings-".count($Kd[$_GET["db"]]);$J="<a href='#$t' class='toggle'>".'è­¦å‘Š'."</a>, $J<div id='$t' class='hidden'>\n$Qj</div>\n";}return" <span class='time'>".@date("H:i:s")."</span>"." $J<div id='$li' class='hidden'><pre><code class='jush-".JUSH."'>".shorten_utf8($H,1e4)."</code></pre>".($Qi?" <span class='time'>($Qi)</span>":'').(support("sql")?'<p><a href="'.h(str_replace("db=".urlencode(DB),"db=".urlencode($_GET["db"]),ME).'sql=&history='.(count($Kd[$_GET["db"]])-1)).'">'.'ç¼–è¾‘'.'</a>':'').'</div>';}function
editRowPrint($R,array$n,$K,$wj){}function
editFunctions(array$m){$J=($m["null"]?"NULL/":"");$wj=isset($_GET["select"])||where($_GET);foreach(array(driver()->insertFunctions,driver()->editFunctions)as$x=>$sd){if(!$x||(!isset($_GET["call"])&&$wj)){foreach($sd
as$Hg=>$X){if(!$Hg||preg_match("~$Hg~",$m["type"]))$J
.="/$X";}}if($x&&$sd&&!preg_match('~set|bool~',$m["type"])&&!is_blob($m))$J
.="/SQL";}if($m["auto_increment"]&&!$wj)$J='è‡ªåŠ¨å¢é‡';return
explode("/",$J);}function
editInput($R,array$m,$ya,$Y){if($m["type"]=="enum")return(isset($_GET["select"])?"<label><input type='radio'$ya value='orig' checked><i>".'åŸå§‹'."</i></label> ":"").enum_input("radio",$ya,$m,$Y,"NULL");return"";}function
editHint($R,array$m,$Y){return"";}function
processInput(array$m,$Y,$r=""){if($r=="SQL")return$Y;$B=$m["field"];$J=q($Y);if(preg_match('~^(now|getdate|uuid)$~',$r))$J="$r()";elseif(preg_match('~^current_(date|timestamp)$~',$r))$J=$r;elseif(preg_match('~^([+-]|\|\|)$~',$r))$J=idf_escape($B)." $r $J";elseif(preg_match('~^[+-] interval$~',$r))$J=idf_escape($B)." $r ".(preg_match("~^(\\d+|'[0-9.: -]') [A-Z_]+\$~i",$Y)&&JUSH!="pgsql"?$Y:$J);elseif(preg_match('~^(addtime|subtime|concat)$~',$r))$J="$r(".idf_escape($B).", $J)";elseif(preg_match('~^(md5|sha1|password|encrypt)$~',$r))$J="$r($J)";return
unconvert_field($m,$J);}function
dumpOutput(){$J=array('text'=>'æ‰“å¼€','file'=>'ä¿å­˜');if(function_exists('gzencode'))$J['gz']='gzip';return$J;}function
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
homepage(){echo'<p class="links">'.($_GET["ns"]==""&&support("database")?'<a href="'.h(ME).'database=">'.'ä¿®æ”¹æ•°æ®åº“'."</a>\n":""),(support("scheme")?"<a href='".h(ME)."scheme='>".($_GET["ns"]!=""?'ä¿®æ”¹æ¨¡å¼':'åˆ›å»ºæ¨¡å¼')."</a>\n":""),($_GET["ns"]!==""?'<a href="'.h(ME).'schema=">'.'æ•°æ®åº“æ¦‚è¦'."</a>\n":""),(support("privileges")?"<a href='".h(ME)."privileges='>".'æƒé™'."</a>\n":"");if($_GET["ns"]!=="")echo(support("routine")?"<a href='#routines'>".'å­ç¨‹åº'."</a>\n":""),(support("sequence")?"<a href='#sequences'>".'åºåˆ—'."</a>\n":""),(support("type")?"<a href='#user-types'>".'ç”¨æˆ·ç±»å‹'."</a>\n":""),(support("event")?"<a href='#events'>".'äº‹ä»¶'."</a>\n":"");return
true;}function
navigation($tf){echo"<h1>".adminer()->name()." <span class='version'>".VERSION;$Ef=$_COOKIE["adminer_version"];echo" <a href='https://www.adminer.org/#download'".target_blank()." id='version'>".(version_compare(VERSION,$Ef)<0?h($Ef):"")."</a>","</span></h1>\n";if($tf=="auth"){$rg="";foreach((array)$_SESSION["pwds"]as$Kj=>$Xh){foreach($Xh
as$N=>$Fj){$B=h(get_setting("vendor-$Kj-$N")?:get_driver($Kj));foreach($Fj
as$V=>$F){if($F!==null){$Rb=$_SESSION["db"][$Kj][$N][$V];foreach(($Rb?array_keys($Rb):array(""))as$j)$rg
.="<li><a href='".h(auth_url($Kj,$N,$V,$j))."'>($B) ".h("$V@".($N!=""?adminer()->serverName($N):"").($j!=""?" - $j":""))."</a>\n";}}}}if($rg)echo"<ul id='logins'>\n$rg</ul>\n".script("mixin(qs('#logins'), {onmouseover: menuOver, onmouseout: menuOut});");}else{$T=array();if($_GET["ns"]!==""&&!$tf&&DB!=""){connection()->select_db(DB);$T=table_status('',true);}adminer()->syntaxHighlighting($T);adminer()->databasesPrint($tf);$ia=array();if(DB==""||!$tf){if(support("sql")){$ia[]="<a href='".h(ME)."sql='".bold(isset($_GET["sql"])&&!isset($_GET["import"])).">".'SQLå‘½ä»¤'."</a>";$ia[]="<a href='".h(ME)."import='".bold(isset($_GET["import"])).">".'å¯¼å…¥'."</a>";}$ia[]="<a href='".h(ME)."dump=".urlencode(isset($_GET["table"])?$_GET["table"]:$_GET["select"])."' id='dump'".bold(isset($_GET["dump"])).">".'å¯¼å‡º'."</a>";}$Yd=$_GET["ns"]!==""&&!$tf&&DB!="";if($Yd)$ia[]='<a href="'.h(ME).'create="'.bold($_GET["create"]==="").">".'åˆ›å»ºè¡¨'."</a>";echo($ia?"<p class='links'>\n".implode("\n",$ia)."\n":"");if($Yd){if($T)adminer()->tablesPrint($T);else
echo"<p class='message'>".'æ²¡æœ‰è¡¨ã€‚'."</p>\n";}}}function
syntaxHighlighting(array$T){echo
script_src(preg_replace("~\\?.*~","",ME)."?file=jush.js&version=5.4.2-dev",true);if(support("sql")){echo"<script".nonce().">\n";if($T){$Se=array();foreach($T
as$R=>$U)$Se[]=preg_quote($R,'/');echo"var jushLinks = { ".JUSH.":";json_row(js_escape(ME).(support("table")?"table":"select").'=$&','/\b('.implode('|',$Se).')\b/g',false);if(support('routine')){foreach(routines()as$K)json_row(js_escape(ME).'function='.urlencode($K["SPECIFIC_NAME"]).'&name=$&','/\b'.preg_quote($K["ROUTINE_NAME"],'/').'(?=["`]?\()/g',false);}json_row('');echo"};\n";foreach(array("bac","bra","sqlite_quo","mssql_bra")as$X)echo"jushLinks.$X = jushLinks.".JUSH.";\n";if(isset($_GET["sql"])||isset($_GET["trigger"])||isset($_GET["check"])){$Fi=array_fill_keys(array_keys($T),array());foreach(driver()->allFields()as$R=>$n){foreach($n
as$m)$Fi[$R][]=$m["field"];}echo"addEventListener('DOMContentLoaded', () => { autocompleter = jush.autocompleteSql('".idf_escape("")."', ".json_encode($Fi)."); });\n";}}echo"</script>\n";}echo
script("syntaxHighlighting('".preg_replace('~^(\d\.?\d).*~s','\1',connection()->server_info)."', '".connection()->flavor."');");}function
databasesPrint($tf){$i=adminer()->databases();if(DB&&$i&&!in_array(DB,$i))array_unshift($i,DB);echo"<form action=''>\n<p id='dbs'>\n";hidden_fields_get();$Pb=script("mixin(qsl('select'), {onmousedown: dbMouseDown, onchange: dbChange});");echo"<label title='".'æ•°æ®åº“'."'>".'æ•°æ®åº“'.": ".($i?html_select("db",array(""=>"")+$i,DB).$Pb:"<input name='db' value='".h(DB)."' autocapitalize='off' size='19'>\n")."</label>","<input type='submit' value='".'ä½¿ç”¨'."'".($i?" class='hidden'":"").">\n";if(support("scheme")){if($tf!="db"&&DB!=""&&connection()->select_db(DB)){echo"<br><label>".'æ¨¡å¼'.": ".html_select("ns",array(""=>"")+adminer()->schemas(),$_GET["ns"])."$Pb</label>";if($_GET["ns"]!="")set_schema($_GET["ns"]);}}foreach(array("import","sql","schema","dump","privileges")as$X){if(isset($_GET[$X])){echo
input_hidden($X);break;}}echo"</p></form>\n";}function
tablesPrint(array$T){echo"<ul id='tables'>".script("mixin(qs('#tables'), {onmouseover: menuOver, onmouseout: menuOut});");foreach($T
as$R=>$P){$R="$R";$B=adminer()->tableName($P);if($B!=""&&!$P["partition"])echo'<li><a href="'.h(ME).'select='.urlencode($R).'"'.bold($_GET["select"]==$R||$_GET["edit"]==$R,"select")." title='".'é€‰æ‹©æ•°æ®'."'>".'é€‰æ‹©'."</a> ",(support("table")||support("indexes")?'<a href="'.h(ME).'table='.urlencode($R).'"'.bold(in_array($R,array($_GET["table"],$_GET["create"],$_GET["indexes"],$_GET["foreign"],$_GET["trigger"],$_GET["check"],$_GET["view"])),(is_view($P)?"view":"structure"))." title='".'æ˜¾ç¤ºç»“æ„'."'>$B</a>":"<span>$B</span>")."\n";}echo"</ul>\n";}function
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
sprintf('ç¦ç”¨ %s æˆ–å¯ç”¨ %s æˆ– %s æ‰©å±•ã€‚',"'mysql.allow_local_infile'","MySQLi","PDO_MySQL");$this->link=@mysql_connect(($N!=""?$N:ini_get("mysql.default_host")),($N.$V!=""?$V:ini_get("mysql.default_user")),($N.$V.$F!=""?$F:ini_get("mysql.default_password")),true,131072);if(!$this->link)return
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
__construct(Db$f){parent::__construct($f);$this->types=array('æ•°å­—'=>array("tinyint"=>3,"smallint"=>5,"mediumint"=>8,"int"=>10,"bigint"=>20,"decimal"=>66,"float"=>12,"double"=>21),'æ—¥æœŸæ—¶é—´'=>array("date"=>10,"datetime"=>19,"timestamp"=>19,"time"=>10,"year"=>4),'å­—ç¬¦ä¸²'=>array("char"=>255,"varchar"=>65535,"tinytext"=>255,"text"=>65535,"mediumtext"=>16777215,"longtext"=>4294967295),'åˆ—è¡¨'=>array("enum"=>65535,"set"=>64),'äºŒè¿›åˆ¶'=>array("bit"=>20,"binary"=>255,"varbinary"=>65535,"tinyblob"=>255,"blob"=>65535,"mediumblob"=>16777215,"longblob"=>4294967295),'å‡ ä½•å›¾å½¢'=>array("geometry"=>0,"point"=>0,"linestring"=>0,"polygon"=>0,"multipoint"=>0,"multilinestring"=>0,"multipolygon"=>0,"geometrycollection"=>0),);$this->insertFunctions=array("char"=>"md5/sha1/password/encrypt/uuid","binary"=>"md5/sha1","date|time"=>"now",);$this->editFunctions=array(number_type()=>"+/-","date"=>"+ interval/- interval","time"=>"addtime/subtime","char|text"=>"concat",);if(min_version('5.7.8',10.2,$f))$this->types['å­—ç¬¦ä¸²']["json"]=4294967295;if(min_version('',10.7,$f)){$this->types['å­—ç¬¦ä¸²']["uuid"]=128;$this->insertFunctions['uuid']='uuid';}if(min_version(9,'',$f)){$this->types['æ•°å­—']["vector"]=16383;$this->insertFunctions['vector']='string_to_vector';}if(min_version(5.1,'',$f))$this->partitionBy=array("HASH","LINEAR HASH","KEY","LINEAR KEY","RANGE","LIST");if(min_version(5.7,10.2,$f))$this->generated=array("STORED","VIRTUAL");}function
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
const offlineMessage = '".js_escape('æ‚¨ç¦»çº¿äº†ã€‚')."';
const thousandsSeparator = '".js_escape(',')."';"),"<div id='help' class='jush-".JUSH." jsonly hidden'></div>\n",script("mixin(qs('#help'), {onmouseover: () => { helpOpen = 1; }, onmouseout: helpMouseout});"),"<div id='content'>\n","<span id='menuopen' class='jsonly'>".icon("move","","menu","")."</span>".script("qs('#menuopen').onclick = event => { qs('#foot').classList.toggle('foot'); event.stopPropagation(); }");if($Ma!==null){$_=substr(preg_replace('~\b(username|db|ns)=[^&]*&~','',ME),0,-1);echo'<p id="breadcrumb"><a href="'.h($_?:".").'">'.get_driver(DRIVER).'</a> Â» ';$_=substr(preg_replace('~\b(db|ns)=[^&]*&~','',ME),0,-1);$N=adminer()->serverName(SERVER);$N=($N!=""?$N:'æœåŠ¡å™¨');if($Ma===false)echo"$N\n";else{echo"<a href='".h($_)."' accesskey='1' title='Alt+Shift+1'>$N</a> Â» ";if($_GET["ns"]!=""||(DB!=""&&is_array($Ma)))echo'<a href="'.h($_."&db=".urlencode(DB).(support("scheme")?"&ns=":"")).'">'.h(DB).'</a> Â» ';if(is_array($Ma)){if($_GET["ns"]!="")echo'<a href="'.h(substr(ME,0,-1)).'">'.h($_GET["ns"]).'</a> Â» ';foreach($Ma
as$x=>$X){$Zb=(is_array($X)?$X[1]:h($X));if($Zb!="")echo"<a href='".h(ME."$x=").urlencode(is_array($X)?$X[0]:$X)."'>$Zb</a> Â» ";}}echo"$Ti\n";}}echo"<h2>$Vi</h2>\n","<div id='ajaxstatus' class='jsonly hidden'></div>\n";restart_session();page_messages($l);$i=&get_session("dbs");if(DB!=""&&$i&&!in_array(DB,$i,true))$i=null;stop_session();define('Adminer\PAGE_HEADER',1);}function
page_headers(){header("Content-Type: text/html; charset=utf-8");header("Cache-Control: no-cache");header("X-Frame-Options: deny");header("X-XSS-Protection: 0");header("X-Content-Type-Options: nosniff");header("Referrer-Policy: origin-when-cross-origin");foreach(adminer()->csp(csp())as$Hb){$Hd=array();foreach($Hb
as$x=>$X)$Hd[]="$x $X";header("Content-Security-Policy: ".implode("; ",$Hd));}adminer()->headers();}function
csp(){return
array(array("script-src"=>"'self' 'unsafe-inline' 'nonce-".get_nonce()."' 'strict-dynamic'","connect-src"=>"'self'","frame-src"=>"https://www.adminer.org","object-src"=>"'none'","base-uri"=>"'none'","form-action"=>"'self'",),);}function
get_nonce(){static$Gf;if(!$Gf)$Gf=base64_encode(rand_string());return$Gf;}function
page_messages($l){$zj=preg_replace('~^[^?]*~','',$_SERVER["REQUEST_URI"]);$qf=idx($_SESSION["messages"],$zj);if($qf){echo"<div class='message'>".implode("</div>\n<div class='message'>",$qf)."</div>".script("messagesPrint();");unset($_SESSION["messages"][$zj]);}if($l)echo"<div class='error'>$l</div>\n";if(adminer()->error)echo"<div class='error'>".adminer()->error."</div>\n";}function
page_footer($tf=""){echo"</div>\n\n<div id='foot' class='foot'>\n<div id='menu'>\n";adminer()->navigation($tf);echo"</div>\n";if($tf!="auth")echo'<form action="" method="post">
<p class="logout">
<span>',h($_GET["username"])."\n",'</span>
<input type="submit" name="logout" value="ç™»å‡º" id="logout">
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
check_invalid_login(array&$Jg){$te=array();foreach(glob(get_temp_dir()."/adminer.invalid*")as$o){$q=file_open_lock($o);if($q){$te=unserialize(stream_get_contents($q));file_unlock($q);break;}}$se=idx($te,adminer()->bruteForceKey(),array());$Ff=($se[1]>29?$se[0]-time():0);if($Ff>0)auth_error(sprintf('ç™»å½•å¤±è´¥æ¬¡æ•°è¿‡å¤šï¼Œè¯· %d åˆ†é’Ÿåé‡è¯•ã€‚',ceil($Ff/60)),$Jg);}$za=$_POST["auth"];if($za){session_regenerate_id();$Kj=$za["driver"];$N=$za["server"];$V=$za["username"];$F=(string)$za["password"];$j=$za["db"];set_password($Kj,$N,$V,$F);$_SESSION["db"][$Kj][$N][$V][$j]=true;if($za["permanent"]){$x=implode("-",array_map('base64_encode',array($Kj,$N,$V,$j)));$Yg=adminer()->permanentLogin(true);$Jg[$x]="$x:".base64_encode($Yg?encrypt_string($F,$Yg):"");cookie("adminer_permanent",implode(" ",$Jg));}if(count($_POST)==1||DRIVER!=$Kj||SERVER!=$N||$_GET["username"]!==$V||DB!=$j)redirect(auth_url($Kj,$N,$V,$j));}elseif($_POST["logout"]&&(!$_SESSION["token"]||verify_token())){foreach(array("pwds","db","dbs","queries")as$x)set_session($x,null);unset_permanent($Jg);redirect(substr(preg_replace('~\b(username|db|ns)=[^&]*&~','',ME),0,-1),'æˆåŠŸç™»å‡ºã€‚'.' '.'æ„Ÿè°¢ä½¿ç”¨Adminerï¼Œè¯·è€ƒè™‘ä¸ºæˆ‘ä»¬<a href="https://www.adminer.org/en/donation/">ææ¬¾ï¼ˆè‹±æ–‡é¡µé¢ï¼‰</a>ã€‚');}elseif($Jg&&!$_SESSION["pwds"]){session_regenerate_id();$Yg=adminer()->permanentLogin();foreach($Jg
as$x=>$X){list(,$cb)=explode(":",$X);list($Kj,$N,$V,$j)=array_map('base64_decode',explode("-",$x));set_password($Kj,$N,$V,decrypt_string(base64_decode($cb),$Yg));$_SESSION["db"][$Kj][$N][$V][$j]=true;}}function
unset_permanent(array&$Jg){foreach($Jg
as$x=>$X){list($Kj,$N,$V,$j)=array_map('base64_decode',explode("-",$x));if($Kj==DRIVER&&$N==SERVER&&$V==$_GET["username"]&&$j==DB)unset($Jg[$x]);}cookie("adminer_permanent",implode(" ",$Jg));}function
auth_error($l,array&$Jg){$Yh=session_name();if(isset($_GET["username"])){header("HTTP/1.1 403 Forbidden");if(($_COOKIE[$Yh]||$_GET[$Yh])&&!$_SESSION["token"])$l='ä¼šè¯å·²è¿‡æœŸï¼Œè¯·é‡æ–°ç™»å½•ã€‚';else{restart_session();add_invalid_login();$F=get_password();if($F!==null){if($F===false)$l
.=($l?'<br>':'').sprintf('ä¸»å¯†ç å·²è¿‡æœŸã€‚<a href="https://www.adminer.org/en/extension/"%s>è¯·æ‰©å±•</a> %s æ–¹æ³•è®©å®ƒæ°¸ä¹…åŒ–ã€‚',target_blank(),'<code>permanentLogin()</code>');set_password(DRIVER,SERVER,$_GET["username"],null);}unset_permanent($Jg);}}if(!$_COOKIE[$Yh]&&$_GET[$Yh]&&ini_bool("session.use_only_cookies"))$l='å¿…é¡»å¯ç”¨ä¼šè¯æ”¯æŒã€‚';$wg=session_get_cookie_params();cookie("adminer_key",($_COOKIE["adminer_key"]?:rand_string()),$wg["lifetime"]);if(!$_SESSION["token"])$_SESSION["token"]=rand(1,1e6);page_header('ç™»å½•',$l,null);echo"<form action='' method='post'>\n","<div>";if(hidden_fields($_POST,array("auth")))echo"<p class='message'>".'æ­¤æ“ä½œå°†åœ¨æˆåŠŸä½¿ç”¨ç›¸åŒçš„å‡­æ®ç™»å½•åæ‰§è¡Œã€‚'."\n";echo"</div>\n";adminer()->loginForm();echo"</form>\n";page_footer("auth");exit;}if(isset($_GET["username"])&&!class_exists('Adminer\Db')){unset($_SESSION["pwds"][DRIVER]);unset_permanent($Jg);page_header('æ²¡æœ‰æ‰©å±•',sprintf('æ²¡æœ‰æ”¯æŒçš„ PHP æ‰©å±•å¯ç”¨ï¼ˆ%sï¼‰ã€‚',implode(", ",Driver::$extensions)),false);page_footer("auth");exit;}$f='';if(isset($_GET["username"])&&is_string(get_password())){list(,$Ng)=host_port(SERVER);if(preg_match('~^\s*([-+]?\d+)~',$Ng,$A)&&($A[1]<1024||$A[1]>65535))auth_error('ä¸å…è®¸è¿æ¥åˆ°ç‰¹æƒç«¯å£ã€‚',$Jg);check_invalid_login($Jg);$Gb=adminer()->credentials();$f=Driver::connect($Gb[0],$Gb[1],$Gb[2]);if(is_object($f)){Db::$instance=$f;Driver::$instance=new
Driver($f);if($f->flavor)save_settings(array("vendor-".DRIVER."-".SERVER=>get_driver(DRIVER)));}}$Ue=null;if(!is_object($f)||($Ue=adminer()->login($_GET["username"],get_password()))!==true){$l=(is_string($f)?nl_br(h($f)):(is_string($Ue)?$Ue:'æ— æ•ˆå‡­æ®ã€‚')).(preg_match('~^ | $~',get_password())?'<br>'.'æ‚¨è¾“å…¥çš„å¯†ç ä¸­æœ‰ä¸€ä¸ªç©ºæ ¼ï¼Œè¿™å¯èƒ½æ˜¯å¯¼è‡´é—®é¢˜çš„åŸå› ã€‚':'');auth_error($l,$Jg);}if($_POST["logout"]&&$_SESSION["token"]&&!verify_token()){page_header('ç™»å‡º','æ— æ•ˆ CSRF ä»¤ç‰Œã€‚è¯·é‡æ–°å‘é€è¡¨å•ã€‚');page_footer("db");exit;}if(!$_SESSION["token"])$_SESSION["token"]=rand(1,1e6);stop_session(true);if($za&&$_POST["token"])$_POST["token"]=get_token();$l='';if($_POST){if(!verify_token()){$le="max_input_vars";$if=ini_get($le);if(extension_loaded("suhosin")){foreach(array("suhosin.request.max_vars","suhosin.post.max_vars")as$x){$X=ini_get($x);if($X&&(!$if||$X<$if)){$le=$x;$if=$X;}}}$l=(!$_POST["token"]&&$if?sprintf('è¶…è¿‡æœ€å¤šå…è®¸çš„å­—æ®µæ•°é‡ã€‚è¯·å¢åŠ  %sã€‚',"'$le'"):'æ— æ•ˆ CSRF ä»¤ç‰Œã€‚è¯·é‡æ–°å‘é€è¡¨å•ã€‚'.' '.'å¦‚æœæ‚¨å¹¶æ²¡æœ‰ä»Adminerå‘é€è¯·æ±‚ï¼Œè¯·å…³é—­æ­¤é¡µé¢ã€‚');}}elseif($_SERVER["REQUEST_METHOD"]=="POST"){$l=sprintf('POST æ•°æ®å¤ªå¤§ã€‚è¯·å‡å°‘æ•°æ®æˆ–è€…å¢åŠ  %s é…ç½®å‘½ä»¤ã€‚',"'post_max_size'");if(isset($_GET["sql"]))$l
.=' '.'æ‚¨å¯ä»¥é€šè¿‡FTPä¸Šä¼ å¤§å‹SQLæ–‡ä»¶å¹¶ä»æœåŠ¡å™¨å¯¼å…¥ã€‚';}function
print_select_result($I,$g=null,array$ig=array(),$z=0){$Se=array();$w=array();$e=array();$Ka=array();$nj=array();$J=array();for($s=0;(!$z||$s<$z)&&($K=$I->fetch_row());$s++){if(!$s){echo"<div class='scrollable'>\n","<table class='nowrap odds'>\n","<thead><tr>";for($_e=0;$_e<count($K);$_e++){$m=$I->fetch_field();$B=$m->name;$hg=(isset($m->orgtable)?$m->orgtable:"");$gg=(isset($m->orgname)?$m->orgname:$B);if($ig&&JUSH=="sql")$Se[$_e]=($B=="table"?"table=":($B=="possible_keys"?"indexes=":null));elseif($hg!=""){if(isset($m->table))$J[$m->table]=$hg;if(!isset($w[$hg])){$w[$hg]=array();foreach(indexes($hg,$g)as$v){if($v["type"]=="PRIMARY"){$w[$hg]=array_flip($v["columns"]);break;}}$e[$hg]=$w[$hg];}if(isset($e[$hg][$gg])){unset($e[$hg][$gg]);$w[$hg][$gg]=$_e;$Se[$_e]=$hg;}}if($m->charsetnr==63)$Ka[$_e]=true;$nj[$_e]=$m->type;echo"<th".($hg!=""||$m->name!=$gg?" title='".h(($hg!=""?"$hg.":"").$gg)."'":"").">".h($B).($ig?doc_link(array('sql'=>"explain-output.html#explain_".strtolower($B),'mariadb'=>"explain/#the-columns-in-explain-select",)):"");}echo"</thead>\n";}echo"<tr>";foreach($K
as$x=>$X){$_="";if(isset($Se[$x])&&!$e[$Se[$x]]){if($ig&&JUSH=="sql"){$R=$K[array_search("table=",$Se)];$_=ME.$Se[$x].urlencode($ig[$R]!=""?$ig[$R]:$R);}else{$_=ME."edit=".urlencode($Se[$x]);foreach($w[$Se[$x]]as$hb=>$_e){if($K[$_e]===null){$_="";break;}$_
.="&where".urlencode("[".bracket_escape($hb)."]")."=".urlencode($K[$_e]);}}}elseif(is_url($X))$_=$X;if($X===null)$X="<i>NULL</i>";elseif($Ka[$x]&&!is_utf8($X))$X="<i>".sprintf('%d å­—èŠ‚',strlen($X))."</i>";else{$X=h($X);if($nj[$x]==254)$X="<code>$X</code>";}if($_)$X="<a href='".h($_)."'".(is_url($_)?target_blank():'').">$X</a>";echo"<td".($nj[$x]<=9||$nj[$x]==246?" class='number'":"").">$X";}}echo($s?"</table>\n</div>":"<p class='message'>".'æ— æ•°æ®ã€‚')."\n";return$J;}function
referencable_primary($Qh){$J=array();foreach(table_status('',true)as$Ai=>$R){if($Ai!=$Qh&&fk_support($R)){foreach(fields($Ai)as$m){if($m["primary"]){if($J[$Ai]){unset($J[$Ai]);break;}$J[$Ai]=$m;}}}}return$J;}function
textarea($B,$Y,$L=10,$kb=80){echo"<textarea name='".h($B)."' rows='$L' cols='$kb' class='sqlarea jush-".JUSH."' spellcheck='false' wrap='off'>";if(is_array($Y)){foreach($Y
as$X)echo
h($X[0])."\n\n\n";}else
echo
h($Y);echo"</textarea>";}function
select_input($ya,array$cg,$Y="",$Wf="",$Kg=""){$Ii=($cg?"select":"input");return"<$Ii$ya".($cg?"><option value=''>$Kg".optionlist($cg,$Y,true)."</select>":" size='10' value='".h($Y)."' placeholder='$Kg'>").($Wf?script("qsl('$Ii').onchange = $Wf;",""):"");}function
json_row($x,$X=null,$Gc=true){static$cd=true;if($cd)echo"{";if($x!=""){echo($cd?"":",")."\n\t\"".addcslashes($x,"\r\n\t\"\\/").'": '.($X!==null?($Gc?'"'.addcslashes($X,"\r\n\"\\/").'"':$X):'null');$cd=false;}else{echo"\n}\n";$cd=true;}}function
edit_type($x,array$m,array$jb,array$md=array(),array$Sc=array()){$U=$m["type"];echo"<td><select name='".h($x)."[type]' class='type' aria-labelledby='label-type'>";if($U&&!array_key_exists($U,driver()->types())&&!isset($md[$U])&&!in_array($U,$Sc))$Sc[]=$U;$si=driver()->structuredTypes();if($md)$si['å¤–é”®']=$md;echo
optionlist(array_merge($Sc,$si),$U),"</select><td>","<input name='".h($x)."[length]' value='".h($m["length"])."' size='3'".(!$m["length"]&&preg_match('~var(char|binary)$~',$U)?" class='required'":"")." aria-labelledby='label-length'>","<td class='options'>",($jb?"<input list='collations' name='".h($x)."[collation]'".(preg_match('~(char|text|enum|set)$~',$U)?"":" class='hidden'")." value='".h($m["collation"])."' placeholder='(".'æ ¡å¯¹'.")'>":''),(driver()->unsigned?"<select name='".h($x)."[unsigned]'".(!$U||preg_match(number_type(),$U)?"":" class='hidden'").'><option>'.optionlist(driver()->unsigned,$m["unsigned"]).'</select>':''),(isset($m['on_update'])?"<select name='".h($x)."[on_update]'".(preg_match('~timestamp|datetime~',$U)?"":" class='hidden'").'>'.optionlist(array(""=>"(".'ON UPDATE'.")","CURRENT_TIMESTAMP"),(preg_match('~^CURRENT_TIMESTAMP~i',$m["on_update"])?"CURRENT_TIMESTAMP":$m["on_update"])).'</select>':''),($md?"<select name='".h($x)."[on_delete]'".(preg_match("~`~",$U)?"":" class='hidden'")."><option value=''>(".'ON DELETE'.")".optionlist(explode("|",driver()->onActions),$m["on_delete"])."</select> ":" ");}function
process_length($y){$Bc=driver()->enumLength;return(preg_match("~^\\s*\\(?\\s*$Bc(?:\\s*,\\s*$Bc)*+\\s*\\)?\\s*\$~",$y)&&preg_match_all("~$Bc~",$y,$af)?"(".implode(",",$af[0]).")":preg_replace('~^[0-9].*~','(\0)',preg_replace('~[^-0-9,+()[\]]~','',$y)));}function
process_type(array$m,$ib="COLLATE"){return" $m[type]".process_length($m["length"]).(preg_match(number_type(),$m["type"])&&in_array($m["unsigned"],driver()->unsigned)?" $m[unsigned]":"").(preg_match('~char|text|enum|set~',$m["type"])&&$m["collation"]?" $ib ".(JUSH=="mssql"?$m["collation"]:q($m["collation"])):"");}function
process_field(array$m,array$mj){if($m["on_update"])$m["on_update"]=str_ireplace("current_timestamp()","CURRENT_TIMESTAMP",$m["on_update"]);return
array(idf_escape(trim($m["field"])),process_type($mj),($m["null"]?" NULL":" NOT NULL"),default_value($m),(preg_match('~timestamp|datetime~',$m["type"])&&$m["on_update"]?" ON UPDATE $m[on_update]":""),(support("comment")&&$m["comment"]!=""?" COMMENT ".q($m["comment"]):""),($m["auto_increment"]?auto_increment():null),);}function
default_value(array$m){$k=$m["default"];$td=$m["generated"];return($k===null?"":(in_array($td,driver()->generated)?(JUSH=="mssql"?" AS ($k)".($td=="VIRTUAL"?"":" $td")."":" GENERATED ALWAYS AS ($k) $td"):" DEFAULT ".(!preg_match('~^GENERATED ~i',$k)&&(preg_match('~char|binary|text|json|enum|set~',$m["type"])||preg_match('~^(?![a-z])~i',$k))?(JUSH=="sql"&&preg_match('~text|json~',$m["type"])?"(".q($k).")":q($k)):str_ireplace("current_timestamp()","CURRENT_TIMESTAMP",(JUSH=="sqlite"?"($k)":$k)))));}function
type_class($U){foreach(array('char'=>'text','date'=>'time|year','binary'=>'blob','enum'=>'set',)as$x=>$X){if(preg_match("~$x|$X~",$U))return" class='$x'";}}function
edit_fields(array$n,array$jb,$U="TABLE",array$md=array()){$n=array_values($n);$Ub=(($_POST?$_POST["defaults"]:get_setting("defaults"))?"":" class='hidden'");$qb=(($_POST?$_POST["comments"]:get_setting("comments"))?"":" class='hidden'");echo"<thead><tr>\n",($U=="PROCEDURE"?"<td>":""),"<th id='label-name'>".($U=="TABLE"?'å­—æ®µå':'å‚æ•°å'),"<td id='label-type'>".'ç±»å‹'."<textarea id='enum-edit' rows='4' cols='12' wrap='off' style='display: none;'></textarea>".script("qs('#enum-edit').onblur = editingLengthBlur;"),"<td id='label-length'>".'é•¿åº¦',"<td>".'é€‰é¡¹';if($U=="TABLE")echo"<td id='label-null'>NULL\n","<td><input type='radio' name='auto_increment_col' value=''><abbr id='label-ai' title='".'è‡ªåŠ¨å¢é‡'."'>AI</abbr>",doc_link(array('sql'=>"example-auto-increment.html",'mariadb'=>"auto_increment/",'sqlite'=>"autoinc.html",'pgsql'=>"datatype-numeric.html#DATATYPE-SERIAL",'mssql'=>"t-sql/statements/create-table-transact-sql-identity-property",)),"<td id='label-default'$Ub>".'é»˜è®¤å€¼',(support("comment")?"<td id='label-comment'$qb>".'æ³¨é‡Š':"");echo"<td>".icon("plus","add[".(support("move_col")?0:count($n))."]","+",'ä¸‹ä¸€è¡Œæ’å…¥'),"</thead>\n<tbody>\n",script("mixin(qsl('tbody'), {onclick: editingClick, onkeydown: editingKeydown, oninput: editingInput});");foreach($n
as$s=>$m){$s++;$jg=$m[($_POST?"orig":"field")];$fc=(isset($_POST["add"][$s-1])||(isset($m["field"])&&!idx($_POST["drop_col"],$s)))&&(support("drop_col")||$jg=="");echo"<tr".($fc?"":" style='display: none;'").">\n",($U=="PROCEDURE"?"<td>".html_select("fields[$s][inout]",explode("|",driver()->inout),$m["inout"]):"")."<th>";if($fc)echo"<input name='fields[$s][field]' value='".h($m["field"])."' data-maxlength='64' autocapitalize='off' aria-labelledby='label-name'".(isset($_POST["add"][$s-1])?" autofocus":"").">";echo
input_hidden("fields[$s][orig]",$jg);edit_type("fields[$s]",$m,$jb,$md);if($U=="TABLE")echo"<td>".checkbox("fields[$s][null]",1,$m["null"],"","","block","label-null"),"<td><label class='block'><input type='radio' name='auto_increment_col' value='$s'".($m["auto_increment"]?" checked":"")." aria-labelledby='label-ai'></label>","<td$Ub>".(driver()->generated?html_select("fields[$s][generated]",array_merge(array("","DEFAULT"),driver()->generated),$m["generated"])." ":checkbox("fields[$s][generated]",1,$m["generated"],"","","","label-default")),"<input name='fields[$s][default]' value='".h($m["default"])."' aria-labelledby='label-default'>",(support("comment")?"<td$qb><input name='fields[$s][comment]' value='".h($m["comment"])."' data-maxlength='".(min_version(5.5)?1024:255)."' aria-labelledby='label-comment'>":"");echo"<td>",(support("move_col")?icon("plus","add[$s]","+",'ä¸‹ä¸€è¡Œæ’å…¥')." ".icon("up","up[$s]","â†‘",'ä¸Šç§»')." ".icon("down","down[$s]","â†“",'ä¸‹ç§»')." ":""),($jg==""||support("drop_col")?icon("cross","drop_col[$s]","x",'ç§»é™¤'):"");}}function
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
set_utf8mb4($h){static$O=false;if(!$O&&preg_match('~\butf8mb4~i',$h)){$O=true;echo"SET NAMES ".charset(connection()).";\n\n";}}if(isset($_GET["status"]))$_GET["variables"]=$_GET["status"];if(isset($_GET["import"]))$_GET["sql"]=$_GET["import"];if(!(DB!=""?connection()->select_db(DB):isset($_GET["sql"])||isset($_GET["dump"])||isset($_GET["database"])||isset($_GET["processlist"])||isset($_GET["privileges"])||isset($_GET["user"])||isset($_GET["variables"])||$_GET["script"]=="connect"||$_GET["script"]=="kill")){if(DB!=""||$_GET["refresh"]){restart_session();set_session("dbs",null);}if(DB!=""){header("HTTP/1.1 404 Not Found");page_header('æ•°æ®åº“'.": ".h(DB),'æ— æ•ˆæ•°æ®åº“ã€‚',true);}else{if($_POST["db"]&&!$l)queries_redirect(substr(ME,0,-1),'å·²åˆ é™¤æ•°æ®åº“ã€‚',drop_databases($_POST["db"]));page_header('é€‰æ‹©æ•°æ®åº“',$l,false);echo"<p class='links'>\n";foreach(array('database'=>'åˆ›å»ºæ•°æ®åº“','privileges'=>'æƒé™','processlist'=>'è¿›ç¨‹åˆ—è¡¨','variables'=>'å˜é‡','status'=>'çŠ¶æ€',)as$x=>$X){if(support($x))echo"<a href='".h(ME)."$x='>$X</a>\n";}echo"<p>".sprintf('%s ç‰ˆæœ¬ï¼š%sï¼Œ ä½¿ç”¨PHPæ‰©å±• %s',get_driver(DRIVER),"<b>".h(connection()->server_info)."</b>","<b>".connection()->extension."</b>")."\n","<p>".sprintf('ç™»å½•ç”¨æˆ·ï¼š%s',"<b>".h(logged_user())."</b>")."\n";$i=adminer()->databases();if($i){$Ih=support("scheme");$jb=collations();echo"<form action='' method='post'>\n","<table class='checkable odds'>\n",script("mixin(qsl('table'), {onclick: tableClick, ondblclick: partialArg(tableClick, true)});"),"<thead><tr>".(support("database")?"<td>":"")."<th>".'æ•°æ®åº“'.(get_session("dbs")!==null?" - <a href='".h(ME)."refresh=1'>".'åˆ·æ–°'."</a>":"")."<td>".'æ ¡å¯¹'."<td>".'è¡¨'."<td>".'å¤§å°'." - <a href='".h(ME)."dbsize=1'>".'è®¡ç®—'."</a>".script("qsl('a').onclick = partial(ajaxSetHtml, '".js_escape(ME)."script=connect');","")."</thead>\n";$i=($_GET["dbsize"]?count_tables($i):array_flip($i));foreach($i
as$j=>$T){$_h=h(ME)."db=".urlencode($j);$t=h("Db-".$j);echo"<tr>".(support("database")?"<td>".checkbox("db[]",$j,in_array($j,(array)$_POST["db"]),"","","",$t):""),"<th><a href='$_h' id='$t'>".h($j)."</a>";$c=h(db_collation($j,$jb));echo"<td>".(support("database")?"<a href='$_h".($Ih?"&amp;ns=":"")."&amp;database=' title='".'ä¿®æ”¹æ•°æ®åº“'."'>$c</a>":$c),"<td align='right'><a href='$_h&amp;schema=' id='tables-".h($j)."' title='".'æ•°æ®åº“æ¦‚è¦'."'>".($_GET["dbsize"]?$T:"?")."</a>","<td align='right' id='size-".h($j)."'>".($_GET["dbsize"]?db_size($j):"?"),"\n";}echo"</table>\n",(support("database")?"<div class='footer'><div>\n"."<fieldset><legend>".'å·²é€‰ä¸­'." <span id='selected'></span></legend><div>\n".input_hidden("all").script("qsl('input').onclick = function () { selectCount('selected', formChecked(this, /^db/)); };")."<input type='submit' name='drop' value='".'åˆ é™¤'."'>".confirm()."\n"."</div></fieldset>\n"."</div></div>\n":""),input_token(),"</form>\n",script("tableCheck();");}if(!empty(adminer()->plugins)){echo"<div class='plugins'>\n","<h3>".'Loaded plugins'."</h3>\n<ul>\n";foreach(adminer()->plugins
as$Lg){$ac=(method_exists($Lg,'description')?$Lg->description():"");if(!$ac){$ph=new
\ReflectionObject($Lg);if(preg_match('~^/[\s*]+(.+)~',$ph->getDocComment(),$A))$ac=$A[1];}$Jh=(method_exists($Lg,'screenshot')?$Lg->screenshot():"");echo"<li><b>".get_class($Lg)."</b>".h($ac?": $ac":"").($Jh?" (<a href='".h($Jh)."'".target_blank().">".'screenshot'."</a>)":"")."\n";}echo"</ul>\n";adminer()->pluginsLinks();echo"</div>\n";}}page_footer("db");exit;}if(support("scheme")){if(DB!=""&&$_GET["ns"]!==""){if(!isset($_GET["ns"]))redirect(preg_replace('~ns=[^&]*&~','',ME)."ns=".get_schema());if(!set_schema($_GET["ns"])){header("HTTP/1.1 404 Not Found");page_header('æ¨¡å¼'.": ".h($_GET["ns"]),'éæ³•æ¨¡å¼ã€‚',true);page_footer("ns");exit;}}}adminer()->afterConnect();class
TmpFile{private$handler;var$size;function
__construct(){$this->handler=tmpfile();}function
write($_b){$this->size+=strlen($_b);fwrite($this->handler,$_b);}function
send(){fseek($this->handler,0);fpassthru($this->handler);fclose($this->handler);}}if(isset($_GET["select"])&&($_POST["edit"]||$_POST["clone"])&&!$_POST["save"])$_GET["edit"]=$_GET["select"];if(isset($_GET["callf"]))$_GET["call"]=$_GET["callf"];if(isset($_GET["function"]))$_GET["procedure"]=$_GET["function"];if(isset($_GET["download"])){$a=$_GET["download"];$n=fields($a);header("Content-Type: application/octet-stream");header("Content-Disposition: attachment; filename=".friendly_url("$a-".implode("_",$_GET["where"])).".".friendly_url($_GET["field"]));$M=array(idf_escape($_GET["field"]));$I=driver()->select($a,$M,array(where($_GET,$n)),$M);$K=($I?$I->fetch_row():array());echo
driver()->value($K[0],$n[$_GET["field"]]);exit;}elseif(isset($_GET["table"])){$a=$_GET["table"];$n=fields($a);if(!$n)$l=error()?:'æ²¡æœ‰è¡¨ã€‚';$S=table_status1($a);$B=adminer()->tableName($S);page_header(($n&&is_view($S)?$S['Engine']=='materialized view'?'ç‰©åŒ–è§†å›¾':'è§†å›¾':'è¡¨').": ".($B!=""?$B:h($a)),$l);$zh=array();foreach($n
as$x=>$m)$zh+=$m["privileges"];adminer()->selectLinks($S,(isset($zh["insert"])||!support("table")?"":null));$ob=$S["Comment"];if($ob!="")echo"<p class='nowrap'>".'æ³¨é‡Š'.": ".h($ob)."\n";if($n)adminer()->tableStructurePrint($n,$S);function
tables_links(array$T){echo"<ul>\n";foreach($T
as$R)echo"<li><a href='".h(ME."table=".urlencode($R))."'>".h($R)."</a>";echo"</ul>\n";}$ke=driver()->inheritsFrom($a);if($ke){echo"<h3>".'Inherits from'."</h3>\n";tables_links($ke);}if(support("indexes")&&driver()->supportsIndex($S)){echo"<h3 id='indexes'>".'ç´¢å¼•'."</h3>\n";$w=indexes($a);if($w)adminer()->tableIndexesPrint($w,$S);echo'<p class="links"><a href="'.h(ME).'indexes='.urlencode($a).'">'.'ä¿®æ”¹ç´¢å¼•'."</a>\n";}if(!is_view($S)){if(fk_support($S)){echo"<h3 id='foreign-keys'>".'å¤–é”®'."</h3>\n";$md=foreign_keys($a);if($md){echo"<table>\n","<thead><tr><th>".'æº'."<td>".'ç›®æ ‡'."<td>".'ON DELETE'."<td>".'ON UPDATE'."<td></thead>\n";foreach($md
as$B=>$p){echo"<tr title='".h($B)."'>","<th><i>".implode("</i>, <i>",array_map('Adminer\h',$p["source"]))."</i>";$_=($p["db"]!=""?preg_replace('~db=[^&]*~',"db=".urlencode($p["db"]),ME):($p["ns"]!=""?preg_replace('~ns=[^&]*~',"ns=".urlencode($p["ns"]),ME):ME));echo"<td><a href='".h($_."table=".urlencode($p["table"]))."'>".($p["db"]!=""&&$p["db"]!=DB?"<b>".h($p["db"])."</b>.":"").($p["ns"]!=""&&$p["ns"]!=$_GET["ns"]?"<b>".h($p["ns"])."</b>.":"").h($p["table"])."</a>","(<i>".implode("</i>, <i>",array_map('Adminer\h',$p["target"]))."</i>)","<td>".h($p["on_delete"]),"<td>".h($p["on_update"]),'<td><a href="'.h(ME.'foreign='.urlencode($a).'&name='.urlencode($B)).'">'.'ä¿®æ”¹'.'</a>',"\n";}echo"</table>\n";}echo'<p class="links"><a href="'.h(ME).'foreign='.urlencode($a).'">'.'æ·»åŠ å¤–é”®'."</a>\n";}if(support("check")){echo"<h3 id='checks'>".'Checks'."</h3>\n";$Xa=driver()->checkConstraints($a);if($Xa){echo"<table>\n";foreach($Xa
as$x=>$X)echo"<tr title='".h($x)."'>","<td><code class='jush-".JUSH."'>".h($X),"<td><a href='".h(ME.'check='.urlencode($a).'&name='.urlencode($x))."'>".'ä¿®æ”¹'."</a>","\n";echo"</table>\n";}echo'<p class="links"><a href="'.h(ME).'check='.urlencode($a).'">'.'Create check'."</a>\n";}}if(support(is_view($S)?"view_trigger":"trigger")){echo"<h3 id='triggers'>".'è§¦å‘å™¨'."</h3>\n";$lj=triggers($a);if($lj){echo"<table>\n";foreach($lj
as$x=>$X)echo"<tr valign='top'><td>".h($X[0])."<td>".h($X[1])."<th>".h($x)."<td><a href='".h(ME.'trigger='.urlencode($a).'&name='.urlencode($x))."'>".'ä¿®æ”¹'."</a>\n";echo"</table>\n";}echo'<p class="links"><a href="'.h(ME).'trigger='.urlencode($a).'">'.'åˆ›å»ºè§¦å‘å™¨'."</a>\n";}$je=driver()->inheritedTables($a);if($je){echo"<h3 id='partitions'>".'Inherited by'."</h3>\n";$_g=driver()->partitionsInfo($a);if($_g)echo"<p><code class='jush-".JUSH."'>BY ".h("$_g[partition_by]($_g[partition])")."</code>\n";tables_links($je);}}elseif(isset($_GET["schema"])){page_header('æ•°æ®åº“æ¦‚è¦',"",array(),h(DB.($_GET["ns"]?".$_GET[ns]":"")));$Ci=array();$Di=array();$ca=($_GET["schema"]?:$_COOKIE["adminer_schema-".str_replace(".","_",DB)]);preg_match_all('~([^:]+):([-0-9.]+)x([-0-9.]+)(_|$)~',$ca,$af,PREG_SET_ORDER);foreach($af
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
<p class="links"><a href="',h(ME."schema=".urlencode($ca)),'" id="schema-link">å›ºå®šé“¾æ¥</a>
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
pack("x512");}}}adminer()->dumpFooter();exit;}page_header('å¯¼å‡º',$l,($_GET["export"]!=""?array("table"=>$_GET["export"]):array()),h(DB));echo'
<form action="" method="post">
<table class="layout">
';$Qb=array('','USE','DROP+CREATE','CREATE');$Ei=array('','DROP+CREATE','CREATE');$Nb=array('','TRUNCATE+INSERT','INSERT');if(JUSH=="sql")$Nb[]='INSERT+UPDATE';$K=get_settings("adminer_export");if(!$K)$K=array("output"=>"text","format"=>"sql","db_style"=>(DB!=""?"":"CREATE"),"table_style"=>"DROP+CREATE","data_style"=>"INSERT");if(!isset($K["events"])){$K["routines"]=$K["events"]=($_GET["dump"]=="");$K["triggers"]=$K["table_style"];}echo"<tr><th>".'è¾“å‡º'."<td>".html_radios("output",adminer()->dumpOutput(),$K["output"])."\n","<tr><th>".'æ ¼å¼'."<td>".html_radios("format",adminer()->dumpFormat(),$K["format"])."\n",(JUSH=="sqlite"?"":"<tr><th>".'æ•°æ®åº“'."<td>".html_select('db_style',$Qb,$K["db_style"]).(support("type")?checkbox("types",1,$K["types"],'ç”¨æˆ·ç±»å‹'):"").(support("routine")?checkbox("routines",1,$K["routines"],'å­ç¨‹åº'):"").(support("event")?checkbox("events",1,$K["events"],'äº‹ä»¶'):"")),"<tr><th>".'è¡¨'."<td>".html_select('table_style',$Ei,$K["table_style"]).checkbox("auto_increment",1,$K["auto_increment"],'è‡ªåŠ¨å¢é‡').(support("trigger")?checkbox("triggers",1,$K["triggers"],'è§¦å‘å™¨'):""),"<tr><th>".'æ•°æ®'."<td>".html_select('data_style',$Nb,$K["data_style"]),'</table>
<p><input type="submit" value="å¯¼å‡º">
',input_token(),'
<table>
',script("qsl('table').onclick = dumpClick;");$Ug=array();if(DB!=""){$Za=($a!=""?"":" checked");echo"<thead><tr>","<th style='text-align: left;'><label class='block'><input type='checkbox' id='check-tables'$Za>".'è¡¨'."</label>".script("qs('#check-tables').onclick = partial(formCheck, /^tables\\[/);",""),"<th style='text-align: right;'><label class='block'>".'æ•°æ®'."<input type='checkbox' id='check-data'$Za></label>".script("qs('#check-data').onclick = partial(formCheck, /^data\\[/);",""),"</thead>\n";$Nj="";$Gi=tables_list();foreach($Gi
as$B=>$U){$Tg=preg_replace('~_.*~','',$B);$Za=($a==""||$a==(substr($a,-1)=="%"?"$Tg%":$B));$Xg="<tr><td>".checkbox("tables[]",$B,$Za,$B,"","block");if($U!==null&&!preg_match('~table~i',$U))$Nj
.="$Xg\n";else
echo"$Xg<td align='right'><label class='block'><span id='Rows-".h($B)."'></span>".checkbox("data[]",$B,$Za)."</label>\n";$Ug[$Tg]++;}echo$Nj;if($Gi)echo
script("ajaxSetHtml('".js_escape(ME)."script=db');");}else{echo"<thead><tr><th style='text-align: left;'>","<label class='block'><input type='checkbox' id='check-databases'".($a==""?" checked":"").">".'æ•°æ®åº“'."</label>",script("qs('#check-databases').onclick = partial(formCheck, /^databases\\[/);",""),"</thead>\n";$i=adminer()->databases();if($i){foreach($i
as$j){if(!information_schema($j)){$Tg=preg_replace('~_.*~','',$j);echo"<tr><td>".checkbox("databases[]",$j,$a==""||$a=="$Tg%",$j,"","block")."\n";$Ug[$Tg]++;}}}else
echo"<tr><td><textarea name='databases' rows='10' cols='20'></textarea>";}echo'</table>
</form>
';$cd=true;foreach($Ug
as$x=>$X){if($x!=""&&$X>1){echo($cd?"<p>":" ")."<a href='".h(ME)."dump=".urlencode("$x%")."'>".h($x)."</a>";$cd=false;}}}elseif(isset($_GET["privileges"])){page_header('æƒé™');echo'<p class="links"><a href="'.h(ME).'user=">'.'åˆ›å»ºç”¨æˆ·'."</a>";$I=connection()->query("SELECT User, Host FROM mysql.".(DB==""?"user":"db WHERE ".q(DB)." LIKE Db")." ORDER BY Host, User");$vd=$I;if(!$I)$I=connection()->query("SELECT SUBSTRING_INDEX(CURRENT_USER, '@', 1) AS User, SUBSTRING_INDEX(CURRENT_USER, '@', -1) AS Host");echo"<form action=''><p>\n";hidden_fields_get();echo
input_hidden("db",DB),($vd?"":input_hidden("grant")),"<table class='odds'>\n","<thead><tr><th>".'ç”¨æˆ·å'."<th>".'æœåŠ¡å™¨'."<th></thead>\n";while($K=$I->fetch_assoc())echo'<tr><td>'.h($K["User"])."<td>".h($K["Host"]).'<td><a href="'.h(ME.'user='.urlencode($K["User"]).'&host='.urlencode($K["Host"])).'">'.'ç¼–è¾‘'."</a>\n";if(!$vd||DB!="")echo"<tr><td><input name='user' autocapitalize='off'><td><input name='host' value='localhost' autocapitalize='off'><td><input type='submit' value='".'ç¼–è¾‘'."'>\n";echo"</table>\n","</form>\n";}elseif(isset($_GET["sql"])){if(!$l&&$_POST["export"]){save_settings(array("output"=>$_POST["output"],"format"=>$_POST["format"]),"adminer_import");dump_headers("sql");if($_POST["format"]=="sql")echo"$_POST[query]\n";else{adminer()->dumpTable("","");adminer()->dumpData("","table",$_POST["query"]);adminer()->dumpFooter();}exit;}restart_session();$Ld=&get_session("queries");$Kd=&$Ld[DB];if(!$l&&$_POST["clear"]){$Kd=array();redirect(remove_from_uri("history"));}stop_session();page_header((isset($_GET["import"])?'å¯¼å…¥':'SQLå‘½ä»¤'),$l);$Re='--'.(JUSH=='sql'?' ':'');if(!$l&&$_POST){$q=false;if(!isset($_GET["import"]))$H=$_POST["query"];elseif($_POST["webfile"]){$ki=adminer()->importServerPath();$q=@fopen((file_exists($ki)?$ki:"compress.zlib://$ki.gz"),"rb");$H=($q?fread($q,1e6):false);}else$H=get_file("sql_file",true,";");if(is_string($H)){if(function_exists('memory_get_usage')&&($lf=ini_bytes("memory_limit"))!="-1")@ini_set("memory_limit",max($lf,strval(2*strlen($H)+memory_get_usage()+8e6)));if($H!=""&&strlen($H)<1e6){$eh=$H.(preg_match("~;[ \t\r\n]*\$~",$H)?"":";");if(!$Kd||first(end($Kd))!=$eh){restart_session();$Kd[]=array($eh,time());set_session("queries",$Ld);stop_session();}}$hi="(?:\\s|/\\*[\s\S]*?\\*/|(?:#|$Re)[^\n]*\n?|--\r?\n)";$Yb=";";$C=0;$xc=true;$g=connect();if($g&&DB!=""){$g->select_db(DB);if($_GET["ns"]!="")set_schema($_GET["ns"],$g);}$nb=0;$Ec=array();$xg='[\'"'.(JUSH=="sql"?'`#':(JUSH=="sqlite"?'`[':(JUSH=="mssql"?'[':''))).']|/\*|'.$Re.'|$'.(JUSH=="pgsql"?'|\$([a-zA-Z]\w*)?\$':'');$bj=microtime(true);$ma=get_settings("adminer_import");while($H!=""){if(!$C&&preg_match("~^$hi*+DELIMITER\\s+(\\S+)~i",$H,$A)){$Yb=preg_quote($A[1]);$H=substr($H,strlen($A[0]));}elseif(!$C&&JUSH=='pgsql'&&preg_match("~^($hi*+COPY\\s+)[^;]+\\s+FROM\\s+stdin;~i",$H,$A)){$Yb="\n\\\\\\.\r?\n";$C=strlen($A[0]);}else{preg_match("($Yb\\s*|$xg)",$H,$A,PREG_OFFSET_CAPTURE,$C);list($od,$Og)=$A[0];if(!$od&&$q&&!feof($q))$H
.=fread($q,1e5);else{if(!$od&&rtrim($H)=="")break;$C=$Og+strlen($od);if($od&&!preg_match("(^$Yb)",$od)){$Ra=driver()->hasCStyleEscapes()||(JUSH=="pgsql"&&($Og>0&&strtolower($H[$Og-1])=="e"));$Hg=($od=='/*'?'\*/':($od=='['?']':(preg_match("~^$Re|^#~",$od)?"\n":preg_quote($od).($Ra?'|\\\\.':''))));while(preg_match("($Hg|\$)s",$H,$A,PREG_OFFSET_CAPTURE,$C)){$Eh=$A[0][0];if(!$Eh&&$q&&!feof($q))$H
.=fread($q,1e5);else{$C=$A[0][1]+strlen($Eh);if(!$Eh||$Eh[0]!="\\")break;}}}else{$xc=false;$eh=substr($H,0,$Og+($Yb[0]=="\n"?3:0));$nb++;$Xg="<pre id='sql-$nb'><code class='jush-".JUSH."'>".adminer()->sqlCommandQuery($eh)."</code></pre>\n";if(JUSH=="sqlite"&&preg_match("~^$hi*+ATTACH\\b~i",$eh,$A)){echo$Xg,"<p class='error'>".'ä¸æ”¯æŒATTACHæŸ¥è¯¢ã€‚'."\n";$Ec[]=" <a href='#sql-$nb'>$nb</a>";if($_POST["error_stops"])break;}else{if(!$_POST["only_errors"]){echo$Xg;ob_flush();flush();}$pi=microtime(true);if(connection()->multi_query($eh)&&$g&&preg_match("~^$hi*+USE\\b~i",$eh))$g->query($eh);do{$I=connection()->store_result();if(connection()->error){echo($_POST["only_errors"]?$Xg:""),"<p class='error'>".'æŸ¥è¯¢å‡ºé”™'.(connection()->errno?" (".connection()->errno.")":"").": ".error()."\n";$Ec[]=" <a href='#sql-$nb'>$nb</a>";if($_POST["error_stops"])break
2;}else{$Qi=" <span class='time'>(".format_time($pi).")</span>".(strlen($eh)<1000?" <a href='".h(ME)."sql=".urlencode(trim($eh))."'>".'ç¼–è¾‘'."</a>":"");$oa=connection()->affected_rows;$Qj=($_POST["only_errors"]?"":driver()->warnings());$Rj="warnings-$nb";if($Qj)$Qi
.=", <a href='#$Rj'>".'è­¦å‘Š'."</a>".script("qsl('a').onclick = partial(toggle, '$Rj');","");$Mc=null;$ig=null;$Nc="explain-$nb";if(is_object($I)){$z=$_POST["limit"];$ig=print_select_result($I,$g,array(),$z);if(!$_POST["only_errors"]){echo"<form action='' method='post'>\n";$Jf=$I->num_rows;echo"<p class='sql-footer'>".($Jf?($z&&$Jf>$z?sprintf('%d / ',$z):"").sprintf('%d è¡Œ',$Jf):""),$Qi;if($g&&preg_match("~^($hi|\\()*+SELECT\\b~i",$eh)&&($Mc=explain($g,$eh)))echo", <a href='#$Nc'>Explain</a>".script("qsl('a').onclick = partial(toggle, '$Nc');","");$t="export-$nb";echo", <a href='#$t'>".'å¯¼å‡º'."</a>".script("qsl('a').onclick = partial(toggle, '$t');","")."<span id='$t' class='hidden'>: ".html_select("output",adminer()->dumpOutput(),$ma["output"])." ".html_select("format",adminer()->dumpFormat(),$ma["format"]).input_hidden("query",$eh)."<input type='submit' name='export' value='".'å¯¼å‡º'."'>".input_token()."</span>\n"."</form>\n";}}else{if(preg_match("~^$hi*+(CREATE|DROP|ALTER)$hi++(DATABASE|SCHEMA)\\b~i",$eh)){restart_session();set_session("dbs",null);stop_session();}if(!$_POST["only_errors"])echo"<p class='message' title='".h(connection()->info)."'>".sprintf('æŸ¥è¯¢æ‰§è¡Œå®Œæ¯•ï¼Œ%d è¡Œå—å½±å“ã€‚',$oa)."$Qi\n";}echo($Qj?"<div id='$Rj' class='hidden'>\n$Qj</div>\n":"");if($Mc){echo"<div id='$Nc' class='hidden explain'>\n";print_select_result($Mc,$g,$ig);echo"</div>\n";}}$pi=microtime(true);}while(connection()->next_result());}$H=substr($H,$C);$C=0;}}}}if($xc)echo"<p class='message'>".'æ²¡æœ‰å‘½ä»¤è¢«æ‰§è¡Œã€‚'."\n";elseif($_POST["only_errors"])echo"<p class='message'>".sprintf('%d æ¡æŸ¥è¯¢å·²æˆåŠŸæ‰§è¡Œã€‚',$nb-count($Ec))," <span class='time'>(".format_time($bj).")</span>\n";elseif($Ec&&$nb>1)echo"<p class='error'>".'æŸ¥è¯¢å‡ºé”™'.": ".implode("",$Ec)."\n";}else
echo"<p class='error'>".upload_error($H)."\n";}echo'
<form action="" method="post" enctype="multipart/form-data" id="form">
';$Kc="<input type='submit' value='".'æ‰§è¡Œ'."' title='Ctrl+Enter'>";if(!isset($_GET["import"])){$eh=$_GET["sql"];if($_POST)$eh=$_POST["query"];elseif($_GET["history"]=="all")$eh=$Kd;elseif($_GET["history"]!="")$eh=idx($Kd[$_GET["history"]],0);echo"<p>";textarea("query",$eh,20);echo
script(($_POST?"":"qs('textarea').focus();\n")."qs('#form').onsubmit = partial(sqlSubmit, qs('#form'), '".js_escape(remove_from_uri("sql|limit|error_stops|only_errors|history"))."');"),"<p>";adminer()->sqlPrintAfter();echo"$Kc\n",'é™åˆ¶è¡Œæ•°'.": <input type='number' name='limit' class='size' value='".h($_POST?$_POST["limit"]:$_GET["limit"])."'>\n";}else{$Ad=(extension_loaded("zlib")?"[.gz]":"");echo"<fieldset><legend>".'æ–‡ä»¶ä¸Šä¼ '."</legend><div>",file_input("SQL$Ad: <input type='file' name='sql_file[]' multiple>\n$Kc"),"</div></fieldset>\n";$Wd=adminer()->importServerPath();if($Wd)echo"<fieldset><legend>".'æ¥è‡ªæœåŠ¡å™¨'."</legend><div>",sprintf('WebæœåŠ¡å™¨æ–‡ä»¶ %s',"<code>".h($Wd)."$Ad</code>"),' <input type="submit" name="webfile" value="'.'è¿è¡Œæ–‡ä»¶'.'">',"</div></fieldset>\n";echo"<p>";}echo
checkbox("error_stops",1,($_POST?$_POST["error_stops"]:isset($_GET["import"])||$_GET["error_stops"]),'å‡ºé”™æ—¶åœæ­¢')."\n",checkbox("only_errors",1,($_POST?$_POST["only_errors"]:isset($_GET["import"])||$_GET["only_errors"]),'ä»…æ˜¾ç¤ºé”™è¯¯')."\n",input_token();if(!isset($_GET["import"])&&$Kd){print_fieldset("history",'å†å²',$_GET["history"]!="");for($X=end($Kd);$X;$X=prev($Kd)){$x=key($Kd);list($eh,$Qi,$sc)=$X;echo'<a href="'.h(ME."sql=&history=$x").'">'.'ç¼–è¾‘'."</a>"." <span class='time' title='".@date('Y-m-d',$Qi)."'>".@date("H:i:s",$Qi)."</span>"." <code class='jush-".JUSH."'>".shorten_utf8(ltrim(str_replace("\n"," ",str_replace("\r","",preg_replace("~^(#|$Re).*~m",'',$eh)))),80,"</code>").($sc?" <span class='time'>($sc)</span>":"")."<br>\n";}echo"<input type='submit' name='clear' value='".'æ¸…é™¤'."'>\n","<a href='".h(ME."sql=&history=all")."'>".'ç¼–è¾‘å…¨éƒ¨'."</a>\n","</div></fieldset>\n";}echo'</form>
';}elseif(isset($_GET["edit"])){$a=$_GET["edit"];$n=fields($a);$Z=(isset($_GET["select"])?($_POST["check"]&&count($_POST["check"])==1?where_check($_POST["check"][0],$n):""):where($_GET,$n));$wj=(isset($_GET["select"])?$_POST["edit"]:$Z);foreach($n
as$B=>$m){if(!isset($m["privileges"][$wj?"update":"insert"])||adminer()->fieldName($m)==""||$m["generated"])unset($n[$B]);}if($_POST&&!$l&&!isset($_GET["select"])){$Te=$_POST["referer"];if($_POST["insert"])$Te=($wj?null:$_SERVER["REQUEST_URI"]);elseif(!preg_match('~^.+&select=.+$~',$Te))$Te=ME."select=".urlencode($a);$w=indexes($a);$rj=unique_array($_GET["where"],$w);$hh="\nWHERE $Z";if(isset($_POST["delete"]))queries_redirect($Te,'å·²åˆ é™¤é¡¹ç›®ã€‚',driver()->delete($a,$hh,$rj?0:1));else{$O=array();foreach($n
as$B=>$m){$X=process_input($m);if($X!==false&&$X!==null)$O[idf_escape($B)]=$X;}if($wj){if(!$O)redirect($Te);queries_redirect($Te,'å·²æ›´æ–°é¡¹ç›®ã€‚',driver()->update($a,$O,$hh,$rj?0:1));if(is_ajax()){page_headers();page_messages($l);exit;}}else{$I=driver()->insert($a,$O);$Ke=($I?last_id($I):0);queries_redirect($Te,sprintf('å·²æ’å…¥é¡¹ç›®%sã€‚',($Ke?" $Ke":"")),$I);}}}$K=null;if($_POST["save"])$K=(array)$_POST["fields"];elseif($Z){$M=array();foreach($n
as$B=>$m){if(isset($m["privileges"]["select"])){$wa=($_POST["clone"]&&$m["auto_increment"]?"''":convert_field($m));$M[]=($wa?"$wa AS ":"").idf_escape($B);}}$K=array();if(!support("table"))$M=array("*");if($M){$I=driver()->select($a,$M,array($Z),$M,array(),(isset($_GET["select"])?2:1));if(!$I)$l=error();else{$K=$I->fetch_assoc();if(!$K)$K=false;}if(isset($_GET["select"])&&(!$K||$I->fetch_assoc()))$K=null;}}if(!support("table")&&!$n){if(!$Z){$I=driver()->select($a,array("*"),array(),array("*"));$K=($I?$I->fetch_assoc():false);if(!$K)$K=array(driver()->primary=>"");}if($K){foreach($K
as$x=>$X){if(!$Z)$K[$x]=null;$n[$x]=array("field"=>$x,"null"=>($x!=driver()->primary),"auto_increment"=>($x==driver()->primary));}}}edit_form($a,$n,$K,$wj,$l);}elseif(isset($_GET["create"])){$a=$_GET["create"];$Bg=driver()->partitionBy;$Eg=($Bg?driver()->partitionsInfo($a):array());$nh=referencable_primary($a);$md=array();foreach($nh
as$Ai=>$m)$md[str_replace("`","``",$Ai)."`".str_replace("`","``",$m["field"])]=$Ai;$lg=array();$S=array();if($a!=""){$lg=fields($a);$S=table_status1($a);if(count($S)<2)$l='æ²¡æœ‰è¡¨ã€‚';}$K=$_POST;$K["fields"]=(array)$K["fields"];if($K["auto_increment_col"])$K["fields"][$K["auto_increment_col"]]["auto_increment"]=true;if($_POST)save_settings(array("comments"=>$_POST["comments"],"defaults"=>$_POST["defaults"]));if($_POST&&!process_fields($K["fields"])&&!$l){if($_POST["drop"])queries_redirect(substr(ME,0,-1),'å·²åˆ é™¤è¡¨ã€‚',drop_tables(array($a)));else{$n=array();$sa=array();$Bj=false;$kd=array();$kg=reset($lg);$qa=" FIRST";foreach($K["fields"]as$x=>$m){$p=$md[$m["type"]];$mj=($p!==null?$nh[$p]:$m);if($m["field"]!=""){if(!$m["generated"])$m["default"]=null;$ch=process_field($m,$mj);$sa[]=array($m["orig"],$ch,$qa);if(!$kg||$ch!==process_field($kg,$kg)){$n[]=array($m["orig"],$ch,$qa);if($m["orig"]!=""||$qa)$Bj=true;}if($p!==null)$kd[idf_escape($m["field"])]=($a!=""&&JUSH!="sqlite"?"ADD":" ").format_foreign_key(array('table'=>$md[$m["type"]],'source'=>array($m["field"]),'target'=>array($mj["field"]),'on_delete'=>$m["on_delete"],));$qa=" AFTER ".idf_escape($m["field"]);}elseif($m["orig"]!=""){$Bj=true;$n[]=array($m["orig"]);}if($m["orig"]!=""){$kg=next($lg);if(!$kg)$qa="";}}$E=array();if(in_array($K["partition_by"],$Bg)){foreach($K
as$x=>$X){if(preg_match('~^partition~',$x))$E[$x]=$X;}foreach($E["partition_names"]as$x=>$B){if($B==""){unset($E["partition_names"][$x]);unset($E["partition_values"][$x]);}}$E["partition_names"]=array_values($E["partition_names"]);$E["partition_values"]=array_values($E["partition_values"]);if($E==$Eg)$E=array();}elseif(preg_match("~partitioned~",$S["Create_options"]))$E=null;$mf='å·²ä¿®æ”¹è¡¨ã€‚';if($a==""){cookie("adminer_engine",$K["Engine"]);$mf='å·²åˆ›å»ºè¡¨ã€‚';}$B=trim($K["name"]);queries_redirect(ME.(support("table")?"table=":"select=").urlencode($B),$mf,alter_table($a,$B,(JUSH=="sqlite"&&($Bj||$kd)?$sa:$n),$kd,($K["Comment"]!=$S["Comment"]?$K["Comment"]:null),($K["Engine"]&&$K["Engine"]!=$S["Engine"]?$K["Engine"]:""),($K["Collation"]&&$K["Collation"]!=$S["Collation"]?$K["Collation"]:""),($K["Auto_increment"]!=""?number($K["Auto_increment"]):""),$E));}}page_header(($a!=""?'ä¿®æ”¹è¡¨':'åˆ›å»ºè¡¨'),$l,array("table"=>$a),h($a));if(!$_POST){$nj=driver()->types();$K=array("Engine"=>$_COOKIE["adminer_engine"],"fields"=>array(array("field"=>"","type"=>(isset($nj["int"])?"int":(isset($nj["integer"])?"integer":"")),"on_update"=>"")),"partition_names"=>array(""),);if($a!=""){$K=$S;$K["name"]=$a;$K["fields"]=array();if(!$_GET["auto_increment"])$K["Auto_increment"]="";foreach($lg
as$m){$m["generated"]=$m["generated"]?:(isset($m["default"])?"DEFAULT":"");$K["fields"][]=$m;}if($Bg){$K+=$Eg;$K["partition_names"][]="";$K["partition_values"][]="";}}}$jb=collations();if(is_array(reset($jb)))$jb=call_user_func_array('array_merge',array_values($jb));$zc=driver()->engines();foreach($zc
as$yc){if(!strcasecmp($yc,$K["Engine"])){$K["Engine"]=$yc;break;}}echo'
<form action="" method="post" id="form">
<p>
';if(support("columns")||$a==""){echo'è¡¨å'.": <input name='name'".($a==""&&!$_POST?" autofocus":"")." data-maxlength='64' value='".h($K["name"])."' autocapitalize='off'>\n",($zc?html_select("Engine",array(""=>"(".'å¼•æ“'.")")+$zc,$K["Engine"]).on_help("event.target.value",1).script("qsl('select').onchange = helpClose;")."\n":"");if($jb)echo"<datalist id='collations'>".optionlist($jb)."</datalist>\n",(preg_match("~sqlite|mssql~",JUSH)?"":"<input list='collations' name='Collation' value='".h($K["Collation"])."' placeholder='(".'æ ¡å¯¹'.")'>\n");echo"<input type='submit' value='".'ä¿å­˜'."'>\n";}if(support("columns")){echo"<div class='scrollable'>\n","<table id='edit-fields' class='nowrap'>\n";edit_fields($K["fields"],$jb,"TABLE",$md);echo"</table>\n",script("editFields();"),"</div>\n<p>\n",'è‡ªåŠ¨å¢é‡'.": <input type='number' name='Auto_increment' class='size' value='".h($K["Auto_increment"])."'>\n",checkbox("defaults",1,($_POST?$_POST["defaults"]:get_setting("defaults")),'é»˜è®¤å€¼',"columnShow(this.checked, 5)","jsonly");$rb=($_POST?$_POST["comments"]:get_setting("comments"));echo(support("comment")?checkbox("comments",1,$rb,'æ³¨é‡Š',"editingCommentsClick(this, true);","jsonly").' '.(preg_match('~\n~',$K["Comment"])?"<textarea name='Comment' rows='2' cols='20'".($rb?"":" class='hidden'").">".h($K["Comment"])."</textarea>":'<input name="Comment" value="'.h($K["Comment"]).'" data-maxlength="'.(min_version(5.5)?2048:60).'"'.($rb?"":" class='hidden'").'>'):''),'<p>
<input type="submit" value="ä¿å­˜">
';}echo'
';if($a!="")echo'<input type="submit" name="drop" value="åˆ é™¤">',confirm(sprintf('åˆ é™¤ %s?',$a));if($Bg&&(JUSH=='sql'||$a=="")){$Cg=preg_match('~RANGE|LIST~',$K["partition_by"]);print_fieldset("partition",'åˆ†åŒºç±»å‹',$K["partition_by"]);echo"<p>".html_select("partition_by",array_merge(array(""),$Bg),$K["partition_by"]).on_help("event.target.value.replace(/./, 'PARTITION BY \$&')",1).script("qsl('select').onchange = partitionByChange;"),"(<input name='partition' value='".h($K["partition"])."'>)\n",'åˆ†åŒº'.": <input type='number' name='partitions' class='size".($Cg||!$K["partition_by"]?" hidden":"")."' value='".h($K["partitions"])."'>\n","<table id='partition-table'".($Cg?"":" class='hidden'").">\n","<thead><tr><th>".'åˆ†åŒºå'."<th>".'å€¼'."</thead>\n";foreach($K["partition_names"]as$x=>$X)echo'<tr>','<td><input name="partition_names[]" value="'.h($X).'" autocapitalize="off">',($x==count($K["partition_names"])-1?script("qsl('input').oninput = partitionNameChange;"):''),'<td><input name="partition_values[]" value="'.h(idx($K["partition_values"],$x)).'">';echo"</table>\n</div></fieldset>\n";}echo
input_token(),'</form>
';}elseif(isset($_GET["indexes"])){$a=$_GET["indexes"];$ee=array("PRIMARY","UNIQUE","INDEX");$S=table_status1($a,true);$be=driver()->indexAlgorithms($S);if(preg_match('~MyISAM|M?aria'.(min_version(5.6,'10.0.5')?'|InnoDB':'').'~i',$S["Engine"]))$ee[]="FULLTEXT";if(preg_match('~MyISAM|M?aria'.(min_version(5.7,'10.2.2')?'|InnoDB':'').'~i',$S["Engine"]))$ee[]="SPATIAL";$w=indexes($a);$n=fields($a);$G=array();if(JUSH=="mongo"){$G=$w["_id_"];unset($ee[0]);unset($w["_id_"]);}$K=$_POST;if($K)save_settings(array("index_options"=>$K["options"]));if($_POST&&!$l&&!$_POST["add"]&&!$_POST["drop_col"]){$b=array();foreach($K["indexes"]as$v){$B=$v["name"];if(in_array($v["type"],$ee)){$e=array();$Pe=array();$bc=array();$ce=(support("partial_indexes")?$v["partial"]:"");$ae=(in_array($v["algorithm"],$be)?$v["algorithm"]:"");$O=array();ksort($v["columns"]);foreach($v["columns"]as$x=>$d){if($d!=""){$y=idx($v["lengths"],$x);$Zb=idx($v["descs"],$x);$O[]=($n[$d]?idf_escape($d):$d).($y?"(".(+$y).")":"").($Zb?" DESC":"");$e[]=$d;$Pe[]=($y?:null);$bc[]=$Zb;}}$Lc=$w[$B];if($Lc){ksort($Lc["columns"]);ksort($Lc["lengths"]);ksort($Lc["descs"]);if($v["type"]==$Lc["type"]&&array_values($Lc["columns"])===$e&&(!$Lc["lengths"]||array_values($Lc["lengths"])===$Pe)&&array_values($Lc["descs"])===$bc&&$Lc["partial"]==$ce&&(!$be||$Lc["algorithm"]==$ae)){unset($w[$B]);continue;}}if($e)$b[]=array($v["type"],$B,$O,$ae,$ce);}}foreach($w
as$B=>$Lc)$b[]=array($Lc["type"],$B,"DROP");if(!$b)redirect(ME."table=".urlencode($a));queries_redirect(ME."table=".urlencode($a),'å·²ä¿®æ”¹ç´¢å¼•ã€‚',alter_indexes($a,$b));}page_header('ç´¢å¼•',$l,array("table"=>$a),h($a));$Zc=array_keys($n);if($_POST["add"]){foreach($K["indexes"]as$x=>$v){if($v["columns"][count($v["columns"])]!="")$K["indexes"][$x]["columns"][]="";}$v=end($K["indexes"]);if($v["type"]||array_filter($v["columns"],'strlen'))$K["indexes"][]=array("columns"=>array(1=>""));}if(!$K){foreach($w
as$x=>$v){$w[$x]["name"]=$x;$w[$x]["columns"][]="";}$w[]=array("columns"=>array(1=>""));$K["indexes"]=$w;}$Pe=(JUSH=="sql"||JUSH=="mssql");$bi=($_POST?$_POST["options"]:get_setting("index_options"));echo'
<form action="" method="post">
<div class="scrollable">
<table class="nowrap">
<thead><tr>
<th id="label-type">ç´¢å¼•ç±»å‹
';$Ud=" class='idxopts".($bi?"":" hidden")."'";if($be)echo"<th id='label-algorithm'$Ud>".'Algorithm'.doc_link(array('sql'=>'create-index.html#create-index-storage-engine-index-types','mariadb'=>'storage-engine-index-types/','pgsql'=>'indexes-types.html',));echo'<th><input type="submit" class="wayoff">','Columns'.($Pe?"<span$Ud> (".'length'.")</span>":"");if($Pe||support("descidx"))echo
checkbox("options",1,$bi,'é€‰é¡¹',"indexOptionsShow(this.checked)","jsonly")."\n";echo'<th id="label-name">åç§°
';if(support("partial_indexes"))echo"<th id='label-condition'$Ud>".'Condition';echo'<th><noscript>',icon("plus","add[0]","+",'ä¸‹ä¸€è¡Œæ’å…¥'),'</noscript>
</thead>
';if($G){echo"<tr><td>PRIMARY<td>";foreach($G["columns"]as$x=>$d)echo
select_input(" disabled",$Zc,$d),"<label><input disabled type='checkbox'>".'é™åº'."</label> ";echo"<td><td>\n";}$_e=1;foreach($K["indexes"]as$v){if(!$_POST["drop_col"]||$_e!=key($_POST["drop_col"])){echo"<tr><td>".html_select("indexes[$_e][type]",array(-1=>"")+$ee,$v["type"],($_e==count($K["indexes"])?"indexesAddRow.call(this);":""),"label-type");if($be)echo"<td$Ud>".html_select("indexes[$_e][algorithm]",array_merge(array(""),$be),$v['algorithm'],"label-algorithm");echo"<td>";ksort($v["columns"]);$s=1;foreach($v["columns"]as$x=>$d){echo"<span>".select_input(" name='indexes[$_e][columns][$s]' title='".'åˆ—'."'",($n&&($d==""||$n[$d])?array_combine($Zc,$Zc):array()),$d,"partial(".($s==count($v["columns"])?"indexesAddColumn":"indexesChangeColumn").", '".js_escape(JUSH=="sql"?"":$_GET["indexes"]."_")."')"),"<span$Ud>",($Pe?"<input type='number' name='indexes[$_e][lengths][$s]' class='size' value='".h(idx($v["lengths"],$x))."' title='".'é•¿åº¦'."'>":""),(support("descidx")?checkbox("indexes[$_e][descs][$s]",1,idx($v["descs"],$x),'é™åº'):""),"</span> </span>";$s++;}echo"<td><input name='indexes[$_e][name]' value='".h($v["name"])."' autocapitalize='off' aria-labelledby='label-name'>\n";if(support("partial_indexes"))echo"<td$Ud><input name='indexes[$_e][partial]' value='".h($v["partial"])."' autocapitalize='off' aria-labelledby='label-condition'>\n";echo"<td>".icon("cross","drop_col[$_e]","x",'ç§»é™¤').script("qsl('button').onclick = partial(editingRemoveRow, 'indexes\$1[type]');");}$_e++;}echo'</table>
</div>
<p>
<input type="submit" value="ä¿å­˜">
',input_token(),'</form>
';}elseif(isset($_GET["database"])){$K=$_POST;if($_POST&&!$l&&!$_POST["add"]){$B=trim($K["name"]);if($_POST["drop"]){$_GET["db"]="";queries_redirect(remove_from_uri("db|database"),'å·²åˆ é™¤æ•°æ®åº“ã€‚',drop_databases(array(DB)));}elseif(DB!==$B){if(DB!=""){$_GET["db"]=$B;queries_redirect(preg_replace('~\bdb=[^&]*&~','',ME)."db=".urlencode($B),'å·²é‡å‘½åæ•°æ®åº“ã€‚',rename_database($B,$K["collation"]));}else{$i=explode("\n",str_replace("\r","",$B));$ui=true;$Je="";foreach($i
as$j){if(count($i)==1||$j!=""){if(!create_database($j,$K["collation"]))$ui=false;$Je=$j;}}restart_session();set_session("dbs",null);queries_redirect(ME."db=".urlencode($Je),'å·²åˆ›å»ºæ•°æ®åº“ã€‚',$ui);}}else{if(!$K["collation"])redirect(substr(ME,0,-1));query_redirect("ALTER DATABASE ".idf_escape($B).(preg_match('~^[a-z0-9_]+$~i',$K["collation"])?" COLLATE $K[collation]":""),substr(ME,0,-1),'å·²ä¿®æ”¹æ•°æ®åº“ã€‚');}}page_header(DB!=""?'ä¿®æ”¹æ•°æ®åº“':'åˆ›å»ºæ•°æ®åº“',$l,array(),h(DB));$jb=collations();$B=DB;if($_POST)$B=$K["name"];elseif(DB!="")$K["collation"]=db_collation(DB,$jb);elseif(JUSH=="sql"){foreach(get_vals("SHOW GRANTS")as$vd){if(preg_match('~ ON (`(([^\\\\`]|``|\\\\.)*)%`\.\*)?~',$vd,$A)&&$A[1]){$B=stripcslashes(idf_unescape("`$A[2]`"));break;}}}echo'
<form action="" method="post">
<p>
',($_POST["add"]||strpos($B,"\n")?'<textarea autofocus name="name" rows="10" cols="40">'.h($B).'</textarea><br>':'<input name="name" autofocus value="'.h($B).'" data-maxlength="64" autocapitalize="off">')."\n".($jb?html_select("collation",array(""=>"(".'æ ¡å¯¹'.")")+$jb,$K["collation"]).doc_link(array('sql'=>"charset-charsets.html",'mariadb'=>"supported-character-sets-and-collations/",'mssql'=>"relational-databases/system-functions/sys-fn-helpcollations-transact-sql",)):""),'<input type="submit" value="ä¿å­˜">
';if(DB!="")echo"<input type='submit' name='drop' value='".'åˆ é™¤'."'>".confirm(sprintf('åˆ é™¤ %s?',DB))."\n";elseif(!$_POST["add"]&&$_GET["db"]=="")echo
icon("plus","add[0]","+",'ä¸‹ä¸€è¡Œæ’å…¥')."\n";echo
input_token(),'</form>
';}elseif(isset($_GET["scheme"])){$K=$_POST;if($_POST&&!$l){$_=preg_replace('~ns=[^&]*&~','',ME)."ns=";if($_POST["drop"])query_redirect("DROP SCHEMA ".idf_escape($_GET["ns"]),$_,'å·²åˆ é™¤æ¨¡å¼ã€‚');else{$B=trim($K["name"]);$_
.=urlencode($B);if($_GET["ns"]=="")query_redirect("CREATE SCHEMA ".idf_escape($B),$_,'å·²åˆ›å»ºæ¨¡å¼ã€‚');elseif($_GET["ns"]!=$B)query_redirect("ALTER SCHEMA ".idf_escape($_GET["ns"])." RENAME TO ".idf_escape($B),$_,'å·²ä¿®æ”¹æ¨¡å¼ã€‚');else
redirect($_);}}page_header($_GET["ns"]!=""?'ä¿®æ”¹æ¨¡å¼':'åˆ›å»ºæ¨¡å¼',$l);if(!$K)$K["name"]=$_GET["ns"];echo'
<form action="" method="post">
<p><input name="name" autofocus value="',h($K["name"]),'" autocapitalize="off">
<input type="submit" value="ä¿å­˜">
';if($_GET["ns"]!="")echo"<input type='submit' name='drop' value='".'åˆ é™¤'."'>".confirm(sprintf('åˆ é™¤ %s?',$_GET["ns"]))."\n";echo
input_token(),'</form>
';}elseif(isset($_GET["call"])){$ba=($_GET["name"]?:$_GET["call"]);page_header('è°ƒç”¨'.": ".h($ba),$l);$Ah=routine($_GET["call"],(isset($_GET["callf"])?"FUNCTION":"PROCEDURE"));$Xd=array();$qg=array();foreach($Ah["fields"]as$s=>$m){if(substr($m["inout"],-3)=="OUT"&&JUSH=='sql')$qg[$s]="@".idf_escape($m["field"])." AS ".idf_escape($m["field"]);if(!$m["inout"]||substr($m["inout"],0,2)=="IN")$Xd[]=$s;}if(!$l&&$_POST){$Sa=array();foreach($Ah["fields"]as$x=>$m){$X="";if(in_array($x,$Xd)){$X=process_input($m);if($X===false)$X="''";if(isset($qg[$x]))connection()->query("SET @".idf_escape($m["field"])." = $X");}if(isset($qg[$x]))$Sa[]="@".idf_escape($m["field"]);elseif(in_array($x,$Xd))$Sa[]=$X;}$H=(isset($_GET["callf"])?"SELECT ":"CALL ").(idx($Ah["returns"],"type")=="record"?"* FROM ":"").table($ba)."(".implode(", ",$Sa).")";$pi=microtime(true);$I=connection()->multi_query($H);$oa=connection()->affected_rows;echo
adminer()->selectQuery($H,$pi,!$I);if(!$I)echo"<p class='error'>".error()."\n";else{$g=connect();if($g)$g->select_db(DB);do{$I=connection()->store_result();if(is_object($I))print_select_result($I,$g);else
echo"<p class='message'>".sprintf('å­ç¨‹åºè¢«è°ƒç”¨ï¼Œ%d è¡Œè¢«å½±å“ã€‚',$oa)." <span class='time'>".@date("H:i:s")."</span>\n";}while(connection()->next_result());if($qg)print_select_result(connection()->query("SELECT ".implode(", ",$qg)));}}echo'
<form action="" method="post">
';if($Xd){echo"<table class='layout'>\n";foreach($Xd
as$x){$m=$Ah["fields"][$x];$B=$m["field"];echo"<tr><th>".adminer()->fieldName($m);$Y=idx($_POST["fields"],$B);if($Y!=""){if($m["type"]=="set")$Y=implode(",",$Y);}input($m,$Y,idx($_POST["function"],$B,""));echo"\n";}echo"</table>\n";}echo'<p>
<input type="submit" value="è°ƒç”¨">
',input_token(),'</form>

<pre>
';function
pre_tr($Eh){return
preg_replace('~^~m','<tr>',preg_replace('~\|~','<td>',preg_replace('~\|$~m',"",rtrim($Eh))));}$R='(\+--[-+]+\+\n)';$K='(\| .* \|\n)';echo
preg_replace_callback("~^$R?$K$R?($K*)$R?~m",function($A){$dd=pre_tr($A[2]);return"<table>\n".($A[1]?"<thead>$dd</thead>\n":$dd).pre_tr($A[4])."\n</table>";},preg_replace('~(\n(    -|mysql)&gt; )(.+)~',"\\1<code class='jush-sql'>\\3</code>",preg_replace('~(.+)\n---+\n~',"<b>\\1</b>\n",h($Ah['comment']))));echo'</pre>
';}elseif(isset($_GET["foreign"])){$a=$_GET["foreign"];$B=$_GET["name"];$K=$_POST;if($_POST&&!$l&&!$_POST["add"]&&!$_POST["change"]&&!$_POST["change-js"]){if(!$_POST["drop"]){$K["source"]=array_filter($K["source"],'strlen');ksort($K["source"]);$Ji=array();foreach($K["source"]as$x=>$X)$Ji[$x]=$K["target"][$x];$K["target"]=$Ji;}if(JUSH=="sqlite")$I=recreate_table($a,$a,array(),array(),array(" $B"=>($K["drop"]?"":" ".format_foreign_key($K))));else{$b="ALTER TABLE ".table($a);$I=($B==""||queries("$b DROP ".(JUSH=="sql"?"FOREIGN KEY ":"CONSTRAINT ").idf_escape($B)));if(!$K["drop"])$I=queries("$b ADD".format_foreign_key($K));}queries_redirect(ME."table=".urlencode($a),($K["drop"]?'å·²åˆ é™¤å¤–é”®ã€‚':($B!=""?'å·²ä¿®æ”¹å¤–é”®ã€‚':'å·²åˆ›å»ºå¤–é”®ã€‚')),$I);if(!$K["drop"])$l='æºåˆ—å’Œç›®æ ‡åˆ—å¿…é¡»å…·æœ‰ç›¸åŒçš„æ•°æ®ç±»å‹ï¼Œåœ¨ç›®æ ‡åˆ—ä¸Šå¿…é¡»æœ‰ä¸€ä¸ªç´¢å¼•å¹¶ä¸”å¼•ç”¨çš„æ•°æ®å¿…é¡»å­˜åœ¨ã€‚';}page_header('å¤–é”®',$l,array("table"=>$a),h($a));if($_POST){ksort($K["source"]);if($_POST["add"])$K["source"][]="";elseif($_POST["change"]||$_POST["change-js"])$K["target"]=array();}elseif($B!=""){$md=foreign_keys($a);$K=$md[$B];$K["source"][]="";}else{$K["table"]=$a;$K["source"]=array("");}echo'
<form action="" method="post">
';$gi=array_keys(fields($a));if($K["db"]!="")connection()->select_db($K["db"]);if($K["ns"]!=""){$mg=get_schema();set_schema($K["ns"]);}$mh=array_keys(array_filter(table_status('',true),'Adminer\fk_support'));$Ji=array_keys(fields(in_array($K["table"],$mh)?$K["table"]:reset($mh)));$Wf="this.form['change-js'].value = '1'; this.form.submit();";echo"<p><label>".'ç›®æ ‡è¡¨'.": ".html_select("table",$mh,$K["table"],$Wf)."</label>\n";if(support("scheme")){$Hh=array_filter(adminer()->schemas(),function($Gh){return!preg_match('~^information_schema$~i',$Gh);});echo"<label>".'æ¨¡å¼'.": ".html_select("ns",$Hh,$K["ns"]!=""?$K["ns"]:$_GET["ns"],$Wf)."</label>";if($K["ns"]!="")set_schema($mg);}elseif(JUSH!="sqlite"){$Rb=array();foreach(adminer()->databases()as$j){if(!information_schema($j))$Rb[]=$j;}echo"<label>".'æ•°æ®åº“'.": ".html_select("db",$Rb,$K["db"]!=""?$K["db"]:$_GET["db"],$Wf)."</label>";}echo
input_hidden("change-js"),'<noscript><p><input type="submit" name="change" value="ä¿®æ”¹"></noscript>
<table>
<thead><tr><th id="label-source">æº<th id="label-target">ç›®æ ‡</thead>
';$_e=0;foreach($K["source"]as$x=>$X){echo"<tr>","<td>".html_select("source[".(+$x)."]",array(-1=>"")+$gi,$X,($_e==count($K["source"])-1?"foreignAddRow.call(this);":""),"label-source"),"<td>".html_select("target[".(+$x)."]",$Ji,idx($K["target"],$x),"","label-target");$_e++;}echo'</table>
<p>
<label>ON DELETE: ',html_select("on_delete",array(-1=>"")+explode("|",driver()->onActions),$K["on_delete"]),'</label>
<label>ON UPDATE: ',html_select("on_update",array(-1=>"")+explode("|",driver()->onActions),$K["on_update"]),'</label>
',doc_link(array('sql'=>"innodb-foreign-key-constraints.html",'mariadb'=>"foreign-keys/",'pgsql'=>"sql-createtable.html#SQL-CREATETABLE-REFERENCES",'mssql'=>"t-sql/statements/create-table-transact-sql",'oracle'=>"SQLRF01111",)),'<p>
<input type="submit" value="ä¿å­˜">
<noscript><p><input type="submit" name="add" value="å¢åŠ åˆ—"></noscript>
';if($B!="")echo'<input type="submit" name="drop" value="åˆ é™¤">',confirm(sprintf('åˆ é™¤ %s?',$B));echo
input_token(),'</form>
';}elseif(isset($_GET["view"])){$a=$_GET["view"];$K=$_POST;$ng="VIEW";if(JUSH=="pgsql"&&$a!=""){$P=table_status1($a);$ng=strtoupper($P["Engine"]);}if($_POST&&!$l){$B=trim($K["name"]);$wa=" AS\n$K[select]";$Te=ME."table=".urlencode($B);$mf='å·²ä¿®æ”¹è§†å›¾ã€‚';$U=($_POST["materialized"]?"MATERIALIZED VIEW":"VIEW");if(!$_POST["drop"]&&$a==$B&&JUSH!="sqlite"&&$U=="VIEW"&&$ng=="VIEW")query_redirect((JUSH=="mssql"?"ALTER":"CREATE OR REPLACE")." VIEW ".table($B).$wa,$Te,$mf);else{$Li=$B."_adminer_".uniqid();drop_create("DROP $ng ".table($a),"CREATE $U ".table($B).$wa,"DROP $U ".table($B),"CREATE $U ".table($Li).$wa,"DROP $U ".table($Li),($_POST["drop"]?substr(ME,0,-1):$Te),'å·²åˆ é™¤è§†å›¾ã€‚',$mf,'å·²åˆ›å»ºè§†å›¾ã€‚',$a,$B);}}if(!$_POST&&$a!=""){$K=view($a);$K["name"]=$a;$K["materialized"]=($ng!="VIEW");if(!$l)$l=error();}page_header(($a!=""?'ä¿®æ”¹è§†å›¾':'åˆ›å»ºè§†å›¾'),$l,array("table"=>$a),h($a));echo'
<form action="" method="post">
<p>åç§°: <input name="name" value="',h($K["name"]),'" data-maxlength="64" autocapitalize="off">
',(support("materializedview")?" ".checkbox("materialized",1,$K["materialized"],'ç‰©åŒ–è§†å›¾'):""),'<p>';textarea("select",$K["select"]);echo'<p>
<input type="submit" value="ä¿å­˜">
';if($a!="")echo'<input type="submit" name="drop" value="åˆ é™¤">',confirm(sprintf('åˆ é™¤ %s?',$a));echo
input_token(),'</form>
';}elseif(isset($_GET["event"])){$aa=$_GET["event"];$re=array("YEAR","QUARTER","MONTH","DAY","HOUR","MINUTE","WEEK","SECOND","YEAR_MONTH","DAY_HOUR","DAY_MINUTE","DAY_SECOND","HOUR_MINUTE","HOUR_SECOND","MINUTE_SECOND");$qi=array("ENABLED"=>"ENABLE","DISABLED"=>"DISABLE","SLAVESIDE_DISABLED"=>"DISABLE ON SLAVE");$K=$_POST;if($_POST&&!$l){if($_POST["drop"])query_redirect("DROP EVENT ".idf_escape($aa),substr(ME,0,-1),'å·²åˆ é™¤äº‹ä»¶ã€‚');elseif(in_array($K["INTERVAL_FIELD"],$re)&&isset($qi[$K["STATUS"]])){$Fh="\nON SCHEDULE ".($K["INTERVAL_VALUE"]?"EVERY ".q($K["INTERVAL_VALUE"])." $K[INTERVAL_FIELD]".($K["STARTS"]?" STARTS ".q($K["STARTS"]):"").($K["ENDS"]?" ENDS ".q($K["ENDS"]):""):"AT ".q($K["STARTS"]))." ON COMPLETION".($K["ON_COMPLETION"]?"":" NOT")." PRESERVE";queries_redirect(substr(ME,0,-1),($aa!=""?'å·²ä¿®æ”¹äº‹ä»¶ã€‚':'å·²åˆ›å»ºäº‹ä»¶ã€‚'),queries(($aa!=""?"ALTER EVENT ".idf_escape($aa).$Fh.($aa!=$K["EVENT_NAME"]?"\nRENAME TO ".idf_escape($K["EVENT_NAME"]):""):"CREATE EVENT ".idf_escape($K["EVENT_NAME"]).$Fh)."\n".$qi[$K["STATUS"]]." COMMENT ".q($K["EVENT_COMMENT"]).rtrim(" DO\n$K[EVENT_DEFINITION]",";").";"));}}page_header(($aa!=""?'ä¿®æ”¹äº‹ä»¶'.": ".h($aa):'åˆ›å»ºäº‹ä»¶'),$l);if(!$K&&$aa!=""){$L=get_rows("SELECT * FROM information_schema.EVENTS WHERE EVENT_SCHEMA = ".q(DB)." AND EVENT_NAME = ".q($aa));$K=reset($L);}echo'
<form action="" method="post">
<table class="layout">
<tr><th>åç§°<td><input name="EVENT_NAME" value="',h($K["EVENT_NAME"]),'" data-maxlength="64" autocapitalize="off">
<tr><th title="datetime">å¼€å§‹<td><input name="STARTS" value="',h("$K[EXECUTE_AT]$K[STARTS]"),'">
<tr><th title="datetime">ç»“æŸ<td><input name="ENDS" value="',h($K["ENDS"]),'">
<tr><th>æ¯<td><input type="number" name="INTERVAL_VALUE" value="',h($K["INTERVAL_VALUE"]),'" class="size"> ',html_select("INTERVAL_FIELD",$re,$K["INTERVAL_FIELD"]),'<tr><th>çŠ¶æ€<td>',html_select("STATUS",$qi,$K["STATUS"]),'<tr><th>æ³¨é‡Š<td><input name="EVENT_COMMENT" value="',h($K["EVENT_COMMENT"]),'" data-maxlength="64">
<tr><th><td>',checkbox("ON_COMPLETION","PRESERVE",$K["ON_COMPLETION"]=="PRESERVE",'å®Œæˆåä»ä¿ç•™'),'</table>
<p>';textarea("EVENT_DEFINITION",$K["EVENT_DEFINITION"]);echo'<p>
<input type="submit" value="ä¿å­˜">
';if($aa!="")echo'<input type="submit" name="drop" value="åˆ é™¤">',confirm(sprintf('åˆ é™¤ %s?',$aa));echo
input_token(),'</form>
';}elseif(isset($_GET["procedure"])){$ba=($_GET["name"]?:$_GET["procedure"]);$Ah=(isset($_GET["function"])?"FUNCTION":"PROCEDURE");$K=$_POST;$K["fields"]=(array)$K["fields"];if($_POST&&!process_fields($K["fields"])&&!$l){$jg=routine($_GET["procedure"],$Ah);$Li="$K[name]_adminer_".uniqid();foreach($K["fields"]as$x=>$m){if($m["field"]=="")unset($K["fields"][$x]);}drop_create("DROP $Ah ".routine_id($ba,$jg),create_routine($Ah,$K),"DROP $Ah ".routine_id($K["name"],$K),create_routine($Ah,array("name"=>$Li)+$K),"DROP $Ah ".routine_id($Li,$K),substr(ME,0,-1),'å·²åˆ é™¤å­ç¨‹åºã€‚','å·²ä¿®æ”¹å­ç¨‹åºã€‚','å·²åˆ›å»ºå­ç¨‹åºã€‚',$ba,$K["name"]);}page_header(($ba!=""?(isset($_GET["function"])?'ä¿®æ”¹å‡½æ•°':'ä¿®æ”¹è¿‡ç¨‹').": ".h($ba):(isset($_GET["function"])?'åˆ›å»ºå‡½æ•°':'åˆ›å»ºè¿‡ç¨‹')),$l);if(!$_POST){if($ba=="")$K["language"]="sql";else{$K=routine($_GET["procedure"],$Ah);$K["name"]=$ba;}}$jb=get_vals("SHOW CHARACTER SET");sort($jb);$Bh=routine_languages();echo($jb?"<datalist id='collations'>".optionlist($jb)."</datalist>":""),'
<form action="" method="post" id="form">
<p>åç§°: <input name="name" value="',h($K["name"]),'" data-maxlength="64" autocapitalize="off">
',($Bh?"<label>".'è¯­è¨€'.": ".html_select("language",$Bh,$K["language"])."</label>\n":""),'<input type="submit" value="ä¿å­˜">
<div class="scrollable">
<table class="nowrap">
';edit_fields($K["fields"],$jb,$Ah);if(isset($_GET["function"])){echo"<tr><td>".'è¿”å›ç±»å‹';edit_type("returns",(array)$K["returns"],$jb,array(),(JUSH=="pgsql"?array("void","trigger"):array()));}echo'</table>
',script("editFields();"),'</div>
<p>';textarea("definition",$K["definition"],20);echo'<p>
<input type="submit" value="ä¿å­˜">
';if($ba!="")echo'<input type="submit" name="drop" value="åˆ é™¤">',confirm(sprintf('åˆ é™¤ %s?',$ba));echo
input_token(),'</form>
';}elseif(isset($_GET["sequence"])){$da=$_GET["sequence"];$K=$_POST;if($_POST&&!$l){$_=substr(ME,0,-1);$B=trim($K["name"]);if($_POST["drop"])query_redirect("DROP SEQUENCE ".idf_escape($da),$_,'å·²åˆ é™¤åºåˆ—ã€‚');elseif($da=="")query_redirect("CREATE SEQUENCE ".idf_escape($B),$_,'å·²åˆ›å»ºåºåˆ—ã€‚');elseif($da!=$B)query_redirect("ALTER SEQUENCE ".idf_escape($da)." RENAME TO ".idf_escape($B),$_,'å·²ä¿®æ”¹åºåˆ—ã€‚');else
redirect($_);}page_header($da!=""?'ä¿®æ”¹åºåˆ—'.": ".h($da):'åˆ›å»ºåºåˆ—',$l);if(!$K)$K["name"]=$da;echo'
<form action="" method="post">
<p><input name="name" value="',h($K["name"]),'" autocapitalize="off">
<input type="submit" value="ä¿å­˜">
';if($da!="")echo"<input type='submit' name='drop' value='".'åˆ é™¤'."'>".confirm(sprintf('åˆ é™¤ %s?',$da))."\n";echo
input_token(),'</form>
';}elseif(isset($_GET["type"])){$ea=$_GET["type"];$K=$_POST;if($_POST&&!$l){$_=substr(ME,0,-1);if($_POST["drop"])query_redirect("DROP TYPE ".idf_escape($ea),$_,'å·²åˆ é™¤ç±»å‹ã€‚');else
query_redirect("CREATE TYPE ".idf_escape(trim($K["name"]))." $K[as]",$_,'å·²åˆ›å»ºç±»å‹ã€‚');}page_header($ea!=""?'ä¿®æ”¹ç±»å‹'.": ".h($ea):'åˆ›å»ºç±»å‹',$l);if(!$K)$K["as"]="AS ";echo'
<form action="" method="post">
<p>
';if($ea!=""){$nj=driver()->types();$Cc=type_values($nj[$ea]);if($Cc)echo"<code class='jush-".JUSH."'>ENUM (".h($Cc).")</code>\n<p>";echo"<input type='submit' name='drop' value='".'åˆ é™¤'."'>".confirm(sprintf('åˆ é™¤ %s?',$ea))."\n";}else{echo'åç§°'.": <input name='name' value='".h($K['name'])."' autocapitalize='off'>\n",doc_link(array('pgsql'=>"datatype-enum.html",),"?");textarea("as",$K["as"]);echo"<p><input type='submit' value='".'ä¿å­˜'."'>\n";}echo
input_token(),'</form>
';}elseif(isset($_GET["check"])){$a=$_GET["check"];$B=$_GET["name"];$K=$_POST;if($K&&!$l){if(JUSH=="sqlite")$I=recreate_table($a,$a,array(),array(),array(),"",array(),"$B",($K["drop"]?"":$K["clause"]));else{$I=($B==""||queries("ALTER TABLE ".table($a)." DROP CONSTRAINT ".idf_escape($B)));if(!$K["drop"])$I=queries("ALTER TABLE ".table($a)." ADD".($K["name"]!=""?" CONSTRAINT ".idf_escape($K["name"]):"")." CHECK ($K[clause])");}queries_redirect(ME."table=".urlencode($a),($K["drop"]?'Check has been dropped.':($B!=""?'Check has been altered.':'Check has been created.')),$I);}page_header(($B!=""?'Alter check'.": ".h($B):'Create check'),$l,array("table"=>$a));if(!$K){$ab=driver()->checkConstraints($a);$K=array("name"=>$B,"clause"=>$ab[$B]);}echo'
<form action="" method="post">
<p>';if(JUSH!="sqlite")echo'åç§°'.': <input name="name" value="'.h($K["name"]).'" data-maxlength="64" autocapitalize="off"> ';echo
doc_link(array('sql'=>"create-table-check-constraints.html",'mariadb'=>"constraint/",'pgsql'=>"ddl-constraints.html#DDL-CONSTRAINTS-CHECK-CONSTRAINTS",'mssql'=>"relational-databases/tables/create-check-constraints",'sqlite'=>"lang_createtable.html#check_constraints",),"?"),'<p>';textarea("clause",$K["clause"]);echo'<p><input type="submit" value="ä¿å­˜">
';if($B!="")echo'<input type="submit" name="drop" value="åˆ é™¤">',confirm(sprintf('åˆ é™¤ %s?',$B));echo
input_token(),'</form>
';}elseif(isset($_GET["trigger"])){$a=$_GET["trigger"];$B="$_GET[name]";$kj=trigger_options();$K=(array)trigger($B,$a)+array("Trigger"=>$a."_bi");if($_POST){if(!$l&&in_array($_POST["Timing"],$kj["Timing"])&&in_array($_POST["Event"],$kj["Event"])&&in_array($_POST["Type"],$kj["Type"])){$Tf=" ON ".table($a);$jc="DROP TRIGGER ".idf_escape($B).(JUSH=="pgsql"?$Tf:"");$Te=ME."table=".urlencode($a);if($_POST["drop"])query_redirect($jc,$Te,'å·²åˆ é™¤è§¦å‘å™¨ã€‚');else{if($B!="")queries($jc);queries_redirect($Te,($B!=""?'å·²ä¿®æ”¹è§¦å‘å™¨ã€‚':'å·²åˆ›å»ºè§¦å‘å™¨ã€‚'),queries(create_trigger($Tf,$_POST)));if($B!="")queries(create_trigger($Tf,$K+array("Type"=>reset($kj["Type"]))));}}$K=$_POST;}page_header(($B!=""?'ä¿®æ”¹è§¦å‘å™¨'.": ".h($B):'åˆ›å»ºè§¦å‘å™¨'),$l,array("table"=>$a));echo'
<form action="" method="post" id="form">
<table class="layout">
<tr><th>æ—¶é—´<td>',html_select("Timing",$kj["Timing"],$K["Timing"],"triggerChange(/^".preg_quote($a,"/")."_[ba][iud]$/, '".js_escape($a)."', this.form);"),'<tr><th>äº‹ä»¶<td>',html_select("Event",$kj["Event"],$K["Event"],"this.form['Timing'].onchange();"),(in_array("UPDATE OF",$kj["Event"])?" <input name='Of' value='".h($K["Of"])."' class='hidden'>":""),'<tr><th>ç±»å‹<td>',html_select("Type",$kj["Type"],$K["Type"]),'</table>
<p>åç§°: <input name="Trigger" value="',h($K["Trigger"]),'" data-maxlength="64" autocapitalize="off">
',script("qs('#form')['Timing'].onchange();"),'<p>';textarea("Statement",$K["Statement"]);echo'<p>
<input type="submit" value="ä¿å­˜">
';if($B!="")echo'<input type="submit" name="drop" value="åˆ é™¤">',confirm(sprintf('åˆ é™¤ %s?',$B));echo
input_token(),'</form>
';}elseif(isset($_GET["user"])){$fa=$_GET["user"];$ah=array(""=>array("All privileges"=>""));foreach(get_rows("SHOW PRIVILEGES")as$K){foreach(explode(",",($K["Privilege"]=="Grant option"?"":$K["Context"]))as$Ab)$ah[$Ab][$K["Privilege"]]=$K["Comment"];}$ah["Server Admin"]+=$ah["File access on server"];$ah["Databases"]["Create routine"]=$ah["Procedures"]["Create routine"];unset($ah["Procedures"]["Create routine"]);$ah["Columns"]=array();foreach(array("Select","Insert","Update","References")as$X)$ah["Columns"][$X]=$ah["Tables"][$X];unset($ah["Server Admin"]["Usage"]);foreach($ah["Tables"]as$x=>$X)unset($ah["Databases"][$x]);$Bf=array();if($_POST){foreach($_POST["objects"]as$x=>$X)$Bf[$X]=(array)$Bf[$X]+idx($_POST["grants"],$x,array());}$wd=array();$Rf="";if(isset($_GET["host"])&&($I=connection()->query("SHOW GRANTS FOR ".q($fa)."@".q($_GET["host"])))){while($K=$I->fetch_row()){if(preg_match('~GRANT (.*) ON (.*) TO ~',$K[0],$A)&&preg_match_all('~ *([^(,]*[^ ,(])( *\([^)]+\))?~',$A[1],$af,PREG_SET_ORDER)){foreach($af
as$X){if($X[1]!="USAGE")$wd["$A[2]$X[2]"][$X[1]]=true;if(preg_match('~ WITH GRANT OPTION~',$K[0]))$wd["$A[2]$X[2]"]["GRANT OPTION"]=true;}}if(preg_match("~ IDENTIFIED BY PASSWORD '([^']+)~",$K[0],$A))$Rf=$A[1];}}if($_POST&&!$l){$Sf=(isset($_GET["host"])?q($fa)."@".q($_GET["host"]):"''");if($_POST["drop"])query_redirect("DROP USER $Sf",ME."privileges=",'å·²åˆ é™¤ç”¨æˆ·ã€‚');else{$Df=q($_POST["user"])."@".q($_POST["host"]);$Fg=$_POST["pass"];if($Fg!=''&&!$_POST["hashed"]&&!min_version(8)){$Fg=get_val("SELECT PASSWORD(".q($Fg).")");$l=!$Fg;}$Fb=false;if(!$l){if($Sf!=$Df){$Fb=queries((min_version(5)?"CREATE USER":"GRANT USAGE ON *.* TO")." $Df IDENTIFIED BY ".(min_version(8)?"":"PASSWORD ").q($Fg));$l=!$Fb;}elseif($Fg!=$Rf)queries("SET PASSWORD FOR $Df = ".q($Fg));}if(!$l){$yh=array();foreach($Bf
as$Lf=>$vd){if(isset($_GET["grant"]))$vd=array_filter($vd);$vd=array_keys($vd);if(isset($_GET["grant"]))$yh=array_diff(array_keys(array_filter($Bf[$Lf],'strlen')),$vd);elseif($Sf==$Df){$Pf=array_keys((array)$wd[$Lf]);$yh=array_diff($Pf,$vd);$vd=array_diff($vd,$Pf);unset($wd[$Lf]);}if(preg_match('~^(.+)\s*(\(.*\))?$~U',$Lf,$A)&&(!grant("REVOKE",$yh,$A[2]," ON $A[1] FROM $Df")||!grant("GRANT",$vd,$A[2]," ON $A[1] TO $Df"))){$l=true;break;}}}if(!$l&&isset($_GET["host"])){if($Sf!=$Df)queries("DROP USER $Sf");elseif(!isset($_GET["grant"])){foreach($wd
as$Lf=>$yh){if(preg_match('~^(.+)(\(.*\))?$~U',$Lf,$A))grant("REVOKE",array_keys($yh),$A[2]," ON $A[1] FROM $Df");}}}queries_redirect(ME."privileges=",(isset($_GET["host"])?'å·²ä¿®æ”¹ç”¨æˆ·ã€‚':'å·²åˆ›å»ºç”¨æˆ·ã€‚'),!$l);if($Fb)connection()->query("DROP USER $Df");}}page_header((isset($_GET["host"])?'ç”¨æˆ·å'.": ".h("$fa@$_GET[host]"):'åˆ›å»ºç”¨æˆ·'),$l,array("privileges"=>array('','æƒé™')));$K=$_POST;if($K)$wd=$Bf;else{$K=$_GET+array("host"=>get_val("SELECT SUBSTRING_INDEX(CURRENT_USER, '@', -1)"));$K["pass"]=$Rf;if($Rf!="")$K["hashed"]=true;$wd[(DB==""||$wd?"":idf_escape(addcslashes(DB,"%_\\"))).".*"]=array();}echo'<form action="" method="post">
<table class="layout">
<tr><th>æœåŠ¡å™¨<td><input name="host" data-maxlength="60" value="',h($K["host"]),'" autocapitalize="off">
<tr><th>ç”¨æˆ·å<td><input name="user" data-maxlength="80" value="',h($K["user"]),'" autocapitalize="off">
<tr><th>å¯†ç <td><input name="pass" id="pass" value="',h($K["pass"]),'" autocomplete="new-password">
',($K["hashed"]?"":script("typePassword(qs('#pass'));")),(min_version(8)?"":checkbox("hashed",1,$K["hashed"],'Hashed',"typePassword(this.form['pass'], this.checked);")),'</table>

',"<table class='odds'>\n","<thead><tr><th colspan='2'>".'æƒé™'.doc_link(array('sql'=>"grant.html#priv_level"));$s=0;foreach($wd
as$Lf=>$vd){echo'<th>'.($Lf!="*.*"?"<input name='objects[$s]' value='".h($Lf)."' size='10' autocapitalize='off'>":input_hidden("objects[$s]","*.*")."*.*");$s++;}echo"</thead>\n";foreach(array(""=>"","Server Admin"=>'æœåŠ¡å™¨',"Databases"=>'æ•°æ®åº“',"Tables"=>'è¡¨',"Columns"=>'åˆ—',"Procedures"=>'å­ç¨‹åº',)as$Ab=>$Zb){foreach((array)$ah[$Ab]as$Zg=>$ob){echo"<tr><td".($Zb?">$Zb<td":" colspan='2'").' lang="en" title="'.h($ob).'">'.h($Zg);$s=0;foreach($wd
as$Lf=>$vd){$B="'grants[$s][".h(strtoupper($Zg))."]'";$Y=$vd[strtoupper($Zg)];if($Ab=="Server Admin"&&$Lf!=(isset($wd["*.*"])?"*.*":".*"))echo"<td>";elseif(isset($_GET["grant"]))echo"<td><select name=$B><option><option value='1'".($Y?" selected":"").">".'æˆæƒ'."<option value='0'".($Y=="0"?" selected":"").">".'åºŸé™¤'."</select>";else
echo"<td align='center'><label class='block'>","<input type='checkbox' name=$B value='1'".($Y?" checked":"").($Zg=="All privileges"?" id='grants-$s-all'>":">".($Zg=="Grant option"?"":script("qsl('input').onclick = function () { if (this.checked) formUncheck('grants-$s-all'); };"))),"</label>";$s++;}}}echo"</table>\n",'<p>
<input type="submit" value="ä¿å­˜">
';if(isset($_GET["host"]))echo'<input type="submit" name="drop" value="åˆ é™¤">',confirm(sprintf('åˆ é™¤ %s?',"$fa@$_GET[host]"));echo
input_token(),'</form>
';}elseif(isset($_GET["processlist"])){if(support("kill")){if($_POST&&!$l){$Fe=0;foreach((array)$_POST["kill"]as$X){if(adminer()->killProcess($X))$Fe++;}queries_redirect(ME."processlist=",sprintf('%d ä¸ªè¿›ç¨‹è¢«ç»ˆæ­¢ã€‚',$Fe),$Fe||!$_POST["kill"]);}}page_header('è¿›ç¨‹åˆ—è¡¨',$l);echo'
<form action="" method="post">
<div class="scrollable">
<table class="nowrap checkable odds">
',script("mixin(qsl('table'), {onclick: tableClick, ondblclick: partialArg(tableClick, true)});");$s=-1;foreach(adminer()->processList()as$s=>$K){if(!$s){echo"<thead><tr lang='en'>".(support("kill")?"<th>":"");foreach($K
as$x=>$X)echo"<th>$x".doc_link(array('sql'=>"show-processlist.html#processlist_".strtolower($x),'pgsql'=>"monitoring-stats.html#PG-STAT-ACTIVITY-VIEW",'oracle'=>"REFRN30223",));echo"</thead>\n";}echo"<tr>".(support("kill")?"<td>".checkbox("kill[]",$K[JUSH=="sql"?"Id":"pid"],0):"");foreach($K
as$x=>$X)echo"<td>".((JUSH=="sql"&&$x=="Info"&&preg_match("~Query|Killed~",$K["Command"])&&$X!="")||(JUSH=="pgsql"&&$x=="current_query"&&$X!="<IDLE>")||(JUSH=="oracle"&&$x=="sql_text"&&$X!="")?"<code class='jush-".JUSH."'>".shorten_utf8($X,100,"</code>").' <a href="'.h(ME.($K["db"]!=""?"db=".urlencode($K["db"])."&":"")."sql=".urlencode($X)).'">'.'å¤åˆ¶'.'</a>':h($X));echo"\n";}echo'</table>
</div>
<p>
';if(support("kill"))echo($s+1)."/".sprintf('å…±è®¡ %d',max_connections()),"<p><input type='submit' value='".'ç»ˆæ­¢'."'>\n";echo
input_token(),'</form>
',script("tableCheck();");}elseif(isset($_GET["select"])){$a=$_GET["select"];$S=table_status1($a);$w=indexes($a);$n=fields($a);$md=column_foreign_keys($a);$Nf=$S["Oid"];$na=get_settings("adminer_import");$zh=array();$e=array();$Mh=array();$fg=array();$Pi="";foreach($n
as$x=>$m){$B=adminer()->fieldName($m);$_f=html_entity_decode(strip_tags($B),ENT_QUOTES);if(isset($m["privileges"]["select"])&&$B!=""){$e[$x]=$_f;if(is_shortable($m))$Pi=adminer()->selectLengthProcess();}if(isset($m["privileges"]["where"])&&$B!="")$Mh[$x]=$_f;if(isset($m["privileges"]["order"])&&$B!="")$fg[$x]=$_f;$zh+=$m["privileges"];}list($M,$xd)=adminer()->selectColumnsProcess($e,$w);$M=array_unique($M);$xd=array_unique($xd);$ve=count($xd)<count($M);$Z=adminer()->selectSearchProcess($n,$w);$eg=adminer()->selectOrderProcess($n,$w);$z=adminer()->selectLimitProcess();if($_GET["val"]&&is_ajax()){header("Content-Type: text/plain; charset=utf-8");foreach($_GET["val"]as$sj=>$K){$wa=convert_field($n[key($K)]);$M=array($wa?:idf_escape(key($K)));$Z[]=where_check($sj,$n);$J=driver()->select($a,$M,$Z,$M);if($J)echo
first($J->fetch_row());}exit;}$G=$uj=array();foreach($w
as$v){if($v["type"]=="PRIMARY"){$G=array_flip($v["columns"]);$uj=($M?$G:array());foreach($uj
as$x=>$X){if(in_array(idf_escape($x),$M))unset($uj[$x]);}break;}}if($Nf&&!$G){$G=$uj=array($Nf=>0);$w[]=array("type"=>"PRIMARY","columns"=>array($Nf));}if($_POST&&!$l){$Tj=$Z;if(!$_POST["all"]&&is_array($_POST["check"])){$ab=array();foreach($_POST["check"]as$Wa)$ab[]=where_check($Wa,$n);$Tj[]="((".implode(") OR (",$ab)."))";}$Tj=($Tj?"\nWHERE ".implode(" AND ",$Tj):"");if($_POST["export"]){save_settings(array("output"=>$_POST["output"],"format"=>$_POST["format"]),"adminer_import");dump_headers($a);adminer()->dumpTable($a,"");$qd=($M?implode(", ",$M):"*").convert_fields($e,$n,$M)."\nFROM ".table($a);$zd=($xd&&$ve?"\nGROUP BY ".implode(", ",$xd):"").($eg?"\nORDER BY ".implode(", ",$eg):"");$H="SELECT $qd$Tj$zd";if(is_array($_POST["check"])&&!$G){$qj=array();foreach($_POST["check"]as$X)$qj[]="(SELECT".limit($qd,"\nWHERE ".($Z?implode(" AND ",$Z)." AND ":"").where_check($X,$n).$zd,1).")";$H=implode(" UNION ALL ",$qj);}adminer()->dumpData($a,"table",$H);adminer()->dumpFooter();exit;}if(!adminer()->selectEmailProcess($Z,$md)){if($_POST["save"]||$_POST["delete"]){$I=true;$oa=0;$O=array();if(!$_POST["delete"]){foreach($_POST["fields"]as$B=>$X){$X=process_input($n[$B]);if($X!==null&&($_POST["clone"]||$X!==false))$O[idf_escape($B)]=($X!==false?$X:idf_escape($B));}}if($_POST["delete"]||$O){$H=($_POST["clone"]?"INTO ".table($a)." (".implode(", ",array_keys($O)).")\nSELECT ".implode(", ",$O)."\nFROM ".table($a):"");if($_POST["all"]||($G&&is_array($_POST["check"]))||$ve){$I=($_POST["delete"]?driver()->delete($a,$Tj):($_POST["clone"]?queries("INSERT $H$Tj".driver()->insertReturning($a)):driver()->update($a,$O,$Tj)));$oa=connection()->affected_rows;if(is_object($I))$oa+=$I->num_rows;}else{foreach((array)$_POST["check"]as$X){$Sj="\nWHERE ".($Z?implode(" AND ",$Z)." AND ":"").where_check($X,$n);$I=($_POST["delete"]?driver()->delete($a,$Sj,1):($_POST["clone"]?queries("INSERT".limit1($a,$H,$Sj)):driver()->update($a,$O,$Sj,1)));if(!$I)break;$oa+=connection()->affected_rows;}}}$mf=sprintf('%d ä¸ªé¡¹ç›®å—åˆ°å½±å“ã€‚',$oa);if($_POST["clone"]&&$I&&$oa==1){$Ke=last_id($I);if($Ke)$mf=sprintf('å·²æ’å…¥é¡¹ç›®%sã€‚'," $Ke");}queries_redirect(remove_from_uri($_POST["all"]&&$_POST["delete"]?"page":""),$mf,$I);if(!$_POST["delete"]){$Rg=(array)$_POST["fields"];edit_form($a,array_intersect_key($n,$Rg),$Rg,!$_POST["clone"],$l);page_footer();exit;}}elseif(!$_POST["import"]){if(!$_POST["val"])$l='æŒ‰ä½Ctrlå¹¶å•å‡»æŸä¸ªå€¼è¿›è¡Œä¿®æ”¹ã€‚';else{$I=true;$oa=0;foreach($_POST["val"]as$sj=>$K){$O=array();foreach($K
as$x=>$X){$x=bracket_escape($x,true);$O[idf_escape($x)]=(preg_match('~char|text~',$n[$x]["type"])||$X!=""?adminer()->processInput($n[$x],$X):"NULL");}$I=driver()->update($a,$O," WHERE ".($Z?implode(" AND ",$Z)." AND ":"").where_check($sj,$n),($ve||$G?0:1)," ");if(!$I)break;$oa+=connection()->affected_rows;}queries_redirect(remove_from_uri(),sprintf('%d ä¸ªé¡¹ç›®å—åˆ°å½±å“ã€‚',$oa),$I);}}elseif(!is_string($ad=get_file("csv_file",true)))$l=upload_error($ad);elseif(!preg_match('~~u',$ad))$l='æ–‡ä»¶å¿…é¡»ä½¿ç”¨UTF-8ç¼–ç ã€‚';else{save_settings(array("output"=>$na["output"],"format"=>$_POST["separator"]),"adminer_import");$I=true;$kb=array_keys($n);preg_match_all('~(?>"[^"]*"|[^"\r\n]+)+~',$ad,$af);$oa=count($af[0]);driver()->begin();$Sh=($_POST["separator"]=="csv"?",":($_POST["separator"]=="tsv"?"\t":";"));$L=array();foreach($af[0]as$x=>$X){preg_match_all("~((?>\"[^\"]*\")+|[^$Sh]*)$Sh~",$X.$Sh,$bf);if(!$x&&!array_diff($bf[1],$kb)){$kb=$bf[1];$oa--;}else{$O=array();foreach($bf[1]as$s=>$hb)$O[idf_escape($kb[$s])]=($hb==""&&$n[$kb[$s]]["null"]?"NULL":q(preg_match('~^".*"$~s',$hb)?str_replace('""','"',substr($hb,1,-1)):$hb));$L[]=$O;}}$I=(!$L||driver()->insertUpdate($a,$L,$G));if($I)driver()->commit();queries_redirect(remove_from_uri("page"),sprintf('%d è¡Œå·²å¯¼å…¥ã€‚',$oa),$I);driver()->rollback();}}}$Ai=adminer()->tableName($S);if(is_ajax()){page_headers();ob_start();}else
page_header('é€‰æ‹©'.": $Ai",$l);$O=null;if(isset($zh["insert"])||!support("table")){$wg=array();foreach((array)$_GET["where"]as$X){if(isset($md[$X["col"]])&&count($md[$X["col"]])==1&&($X["op"]=="="||(!$X["op"]&&(is_array($X["val"])||!preg_match('~[_%]~',$X["val"])))))$wg["set"."[".bracket_escape($X["col"])."]"]=$X["val"];}$O=$wg?"&".http_build_query($wg):"";}adminer()->selectLinks($S,$O);if(!$e&&support("table"))echo"<p class='error'>".'ä¸èƒ½é€‰æ‹©è¯¥è¡¨'.($n?".":": ".error())."\n";else{echo"<form action='' id='form'>\n","<div style='display: none;'>";hidden_fields_get();echo(DB!=""?input_hidden("db",DB).(isset($_GET["ns"])?input_hidden("ns",$_GET["ns"]):""):""),input_hidden("select",$a),"</div>\n";adminer()->selectColumnsPrint($M,$e);adminer()->selectSearchPrint($Z,$Mh,$w);adminer()->selectOrderPrint($eg,$fg,$w);adminer()->selectLimitPrint($z);adminer()->selectLengthPrint($Pi);adminer()->selectActionPrint($w);echo"</form>\n";$D=$_GET["page"];$pd=null;if($D=="last"){$pd=get_val(count_rows($a,$Z,$ve,$xd));$D=floor(max(0,intval($pd)-1)/$z);}$Nh=$M;$yd=$xd;if(!$Nh){$Nh[]="*";$Bb=convert_fields($e,$n,$M);if($Bb)$Nh[]=substr($Bb,2);}foreach($M
as$x=>$X){$m=$n[idf_unescape($X)];if($m&&($wa=convert_field($m)))$Nh[$x]="$wa AS $X";}if(!$ve&&$uj){foreach($uj
as$x=>$X){$Nh[]=idf_escape($x);if($yd)$yd[]=idf_escape($x);}}$I=driver()->select($a,$Nh,$Z,$yd,$eg,$z,$D,true);if(!$I)echo"<p class='error'>".error()."\n";else{if(JUSH=="mssql"&&$D)$I->seek($z*$D);$wc=array();echo"<form action='' method='post' enctype='multipart/form-data'>\n";$L=array();while($K=$I->fetch_assoc()){if($D&&JUSH=="oracle")unset($K["RNUM"]);$L[]=$K;}if($_GET["page"]!="last"&&$z&&$xd&&$ve&&JUSH=="sql")$pd=get_val(" SELECT FOUND_ROWS()");if(!$L)echo"<p class='message'>".'æ— æ•°æ®ã€‚'."\n";else{$Ea=adminer()->backwardKeys($a,$Ai);echo"<div class='scrollable'>","<table id='table' class='nowrap checkable odds'>",script("mixin(qs('#table'), {onclick: tableClick, ondblclick: partialArg(tableClick, true), onkeydown: editingKeydown});"),"<thead><tr>".(!$xd&&$M?"":"<td><input type='checkbox' id='all-page' class='jsonly'>".script("qs('#all-page').onclick = partial(formCheck, /check/);","")." <a href='".h($_GET["modify"]?remove_from_uri("modify"):$_SERVER["REQUEST_URI"]."&modify=1")."'>".'ä¿®æ”¹'."</a>");$Af=array();$sd=array();reset($M);$jh=1;foreach($L[0]as$x=>$X){if(!isset($uj[$x])){$X=idx($_GET["columns"],key($M))?:array();$m=$n[$M?($X?$X["col"]:current($M)):$x];$B=($m?adminer()->fieldName($m,$jh):($X["fun"]?"*":h($x)));if($B!=""){$jh++;$Af[$x]=$B;$d=idf_escape($x);$Od=remove_from_uri('(order|desc)[^=]*|page').'&order%5B0%5D='.urlencode($x);$Zb="&desc%5B0%5D=1";echo"<th id='th[".h(bracket_escape($x))."]'>".script("mixin(qsl('th'), {onmouseover: partial(columnMouse), onmouseout: partial(columnMouse, ' hidden')});","");$rd=apply_sql_function($X["fun"],$B);$fi=isset($m["privileges"]["order"])||$rd;echo($fi?"<a href='".h($Od.($eg[0]==$d||$eg[0]==$x?$Zb:''))."'>$rd</a>":$rd),"<span class='column hidden'>";if($fi)echo"<a href='".h($Od.$Zb)."' title='".'é™åº'."' class='text'> â†“</a>";if(!$X["fun"]&&isset($m["privileges"]["where"]))echo'<a href="#fieldset-search" title="'.'æœç´¢'.'" class="text jsonly"> =</a>',script("qsl('a').onclick = partial(selectSearch, '".js_escape($x)."');");echo"</span>";}$sd[$x]=$X["fun"];next($M);}}$Pe=array();if($_GET["modify"]){foreach($L
as$K){foreach($K
as$x=>$X)$Pe[$x]=max($Pe[$x],min(40,strlen(utf8_decode($X))));}}echo($Ea?"<th>".'å…³è”ä¿¡æ¯':"")."</thead>\n";if(is_ajax())ob_end_clean();foreach(adminer()->rowDescriptions($L,$md)as$zf=>$K){$rj=unique_array($L[$zf],$w);if(!$rj){$rj=array();reset($M);foreach($L[$zf]as$x=>$X){if(!preg_match('~^(COUNT|AVG|GROUP_CONCAT|MAX|MIN|SUM)\(~',current($M)))$rj[$x]=$X;next($M);}}$sj="";foreach($rj
as$x=>$X){$m=(array)$n[$x];if((JUSH=="sql"||JUSH=="pgsql")&&preg_match('~char|text|enum|set~',$m["type"])&&strlen($X)>64){$x=(strpos($x,'(')?$x:idf_escape($x));$x="MD5(".(JUSH!='sql'||preg_match("~^utf8~",$m["collation"])?$x:"CONVERT($x USING ".charset(connection()).")").")";$X=md5($X);}$sj
.="&".($X!==null?urlencode("where[".bracket_escape($x)."]")."=".urlencode($X===false?"f":$X):"null%5B%5D=".urlencode($x));}echo"<tr>".(!$xd&&$M?"":"<td>".checkbox("check[]",substr($sj,1),in_array(substr($sj,1),(array)$_POST["check"])).($ve||information_schema(DB)?"":" <a href='".h(ME."edit=".urlencode($a).$sj)."' class='edit'>".'ç¼–è¾‘'."</a>"));reset($M);foreach($K
as$x=>$X){if(isset($Af[$x])){$d=current($M);$m=(array)$n[$x];$X=driver()->value($X,$m);if($X!=""&&(!isset($wc[$x])||$wc[$x]!=""))$wc[$x]=(is_mail($X)?$Af[$x]:"");$_="";if(is_blob($m)&&$X!="")$_=ME.'download='.urlencode($a).'&field='.urlencode($x).$sj;if(!$_&&$X!==null){foreach((array)$md[$x]as$p){if(count($md[$x])==1||end($p["source"])==$x){$_="";foreach($p["source"]as$s=>$gi)$_
.=where_link($s,$p["target"][$s],$L[$zf][$gi]);$_=($p["db"]!=""?preg_replace('~([?&]db=)[^&]+~','\1'.urlencode($p["db"]),ME):ME).'select='.urlencode($p["table"]).$_;if($p["ns"])$_=preg_replace('~([?&]ns=)[^&]+~','\1'.urlencode($p["ns"]),$_);if(count($p["source"])==1)break;}}}if($d=="COUNT(*)"){$_=ME."select=".urlencode($a);$s=0;foreach((array)$_GET["where"]as$W){if(!array_key_exists($W["col"],$rj))$_
.=where_link($s++,$W["col"],$W["val"],$W["op"]);}foreach($rj
as$Be=>$W)$_
.=where_link($s++,$Be,$W);}$Pd=select_value($X,$_,$m,$Pi);$t=h("val[$sj][".bracket_escape($x)."]");$Sg=idx(idx($_POST["val"],$sj),bracket_escape($x));$rc=!is_array($K[$x])&&is_utf8($Pd)&&$L[$zf][$x]==$K[$x]&&!$sd[$x]&&!$m["generated"];$U=(preg_match('~^(AVG|MIN|MAX)\((.+)\)~',$d,$A)?$n[idf_unescape($A[2])]["type"]:$m["type"]);$Ni=preg_match('~text|json|lob~',$U);$we=preg_match(number_type(),$U)||preg_match('~^(CHAR_LENGTH|ROUND|FLOOR|CEIL|TIME_TO_SEC|COUNT|SUM)\(~',$d);echo"<td id='$t'".($we&&($X===null||is_numeric(strip_tags($Pd))||$U=="money")?" class='number'":"");if(($_GET["modify"]&&$rc&&$X!==null)||$Sg!==null){$Bd=h($Sg!==null?$Sg:$K[$x]);echo">".($Ni?"<textarea name='$t' cols='30' rows='".(substr_count($K[$x],"\n")+1)."'>$Bd</textarea>":"<input name='$t' value='$Bd' size='$Pe[$x]'>");}else{$Ve=strpos($Pd,"<i>â€¦</i>");echo" data-text='".($Ve?2:($Ni?1:0))."'".($rc?"":" data-warning='".h('ä½¿ç”¨ç¼–è¾‘é“¾æ¥ä¿®æ”¹è¯¥å€¼ã€‚')."'").">$Pd";}}next($M);}if($Ea)echo"<td>";adminer()->backwardKeysPrint($Ea,$L[$zf]);echo"</tr>\n";}if(is_ajax())exit;echo"</table>\n","</div>\n";}if(!is_ajax()){if($L||$D){$Jc=true;if($_GET["page"]!="last"){if(!$z||(count($L)<$z&&($L||!$D)))$pd=($D?$D*$z:0)+count($L);elseif(JUSH!="sql"||!$ve){$pd=($ve?false:found_rows($S,$Z));if(intval($pd)<max(1e4,2*($D+1)*$z))$pd=first(slow_query(count_rows($a,$Z,$ve,$xd)));else$Jc=false;}}$ug=($z&&($pd===false||$pd>$z||$D));if($ug)echo(($pd===false?count($L)+1:$pd-$D*$z)>$z?'<p><a href="'.h(remove_from_uri("page")."&page=".($D+1)).'" class="loadmore">'.'åŠ è½½æ›´å¤šæ•°æ®'.'</a>'.script("qsl('a').onclick = partial(selectLoadMore, $z, '".'åŠ è½½ä¸­'."â€¦');",""):''),"\n";echo"<div class='footer'><div>\n";if($ug){$ff=($pd===false?$D+(count($L)>=$z?2:1):floor(($pd-1)/$z));echo"<fieldset>";if(JUSH!="simpledb"){echo"<legend><a href='".h(remove_from_uri("page"))."'>".'é¡µé¢'."</a></legend>",script("qsl('a').onclick = function () { pageClick(this.href, +prompt('".'é¡µé¢'."', '".($D+1)."')); return false; };"),pagination(0,$D).($D>5?" â€¦":"");for($s=max(1,$D-4);$s<min($ff,$D+5);$s++)echo
pagination($s,$D);if($ff>0)echo($D+5<$ff?" â€¦":""),($Jc&&$pd!==false?pagination($ff,$D):" <a href='".h(remove_from_uri("page")."&page=last")."' title='~$ff'>".'æœ€å'."</a>");}else
echo"<legend>".'é¡µé¢'."</legend>",pagination(0,$D).($D>1?" â€¦":""),($D?pagination($D,$D):""),($ff>$D?pagination($D+1,$D).($ff>$D+1?" â€¦":""):"");echo"</fieldset>\n";}echo"<fieldset>","<legend>".'æ‰€æœ‰ç»“æœ'."</legend>";$gc=($Jc?"":"~ ").$pd;$Xf="const checked = formChecked(this, /check/); selectCount('selected', this.checked ? '$gc' : checked); selectCount('selected2', this.checked || !checked ? '$gc' : checked);";echo
checkbox("all",1,0,($pd!==false?($Jc?"":"~ ").sprintf('%d è¡Œ',$pd):""),$Xf)."\n","</fieldset>\n";if(adminer()->selectCommandPrint())echo'<fieldset',($_GET["modify"]?'':' class="jsonly"'),'><legend>ä¿®æ”¹</legend><div>
<input type="submit" value="ä¿å­˜"',($_GET["modify"]?'':' title="'.'æŒ‰ä½Ctrlå¹¶å•å‡»æŸä¸ªå€¼è¿›è¡Œä¿®æ”¹ã€‚'.'"'),'>
</div></fieldset>
<fieldset><legend>å·²é€‰ä¸­ <span id="selected"></span></legend><div>
<input type="submit" name="edit" value="ç¼–è¾‘">
<input type="submit" name="clone" value="å¤åˆ¶">
<input type="submit" name="delete" value="åˆ é™¤">',confirm(),'</div></fieldset>
';$nd=adminer()->dumpFormat();foreach((array)$_GET["columns"]as$d){if($d["fun"]){unset($nd['sql']);break;}}if($nd){print_fieldset("export",'å¯¼å‡º'." <span id='selected2'></span>");$rg=adminer()->dumpOutput();echo($rg?html_select("output",$rg,$na["output"])." ":""),html_select("format",$nd,$na["format"])," <input type='submit' name='export' value='".'å¯¼å‡º'."'>\n","</div></fieldset>\n";}adminer()->selectEmailPrint(array_filter($wc,'strlen'),$e);echo"</div></div>\n";}if(adminer()->selectImportPrint())echo"<p>","<a href='#import'>".'å¯¼å…¥'."</a>",script("qsl('a').onclick = partial(toggle, 'import');",""),"<span id='import'".($_POST["import"]?"":" class='hidden'").">: ",file_input("<input type='file' name='csv_file'> ".html_select("separator",array("csv"=>"CSV,","csv;"=>"CSV;","tsv"=>"TSV"),$na["format"])." <input type='submit' name='import' value='".'å¯¼å…¥'."'>"),"</span>";echo
input_token(),"</form>\n",(!$xd&&$M?"":script("tableCheck();"));}}}if(is_ajax()){ob_end_clean();exit;}}elseif(isset($_GET["variables"])){$P=isset($_GET["status"]);page_header($P?'çŠ¶æ€':'å˜é‡');$Jj=($P?adminer()->showStatus():adminer()->showVariables());if(!$Jj)echo"<p class='message'>".'æ— æ•°æ®ã€‚'."\n";else{echo"<table>\n";foreach($Jj
as$K){echo"<tr>";$x=array_shift($K);echo"<th><code class='jush-".JUSH.($P?"status":"set")."'>".h($x)."</code>";foreach($K
as$X)echo"<td>".nl_br(h($X));}echo"</table>\n";}}elseif(isset($_GET["script"])){header("Content-Type: text/javascript; charset=utf-8");if($_GET["script"]=="db"){$xi=array("Data_length"=>0,"Index_length"=>0,"Data_free"=>0);foreach(table_status()as$B=>$S){json_row("Comment-$B",h($S["Comment"]));if(!is_view($S)||preg_match('~materialized~i',$S["Engine"])){foreach(array("Engine","Collation")as$x)json_row("$x-$B",h($S[$x]));foreach($xi+array("Auto_increment"=>0,"Rows"=>0)as$x=>$X){if($S[$x]!=""){$X=format_number($S[$x]);if($X>=0)json_row("$x-$B",($x=="Rows"&&$X&&$S["Engine"]==(JUSH=="pgsql"?"table":"InnoDB")?"~ $X":$X));if(isset($xi[$x]))$xi[$x]+=($S["Engine"]!="InnoDB"||$x!="Data_free"?$S[$x]:0);}elseif(array_key_exists($x,$S))json_row("$x-$B","?");}}}foreach($xi
as$x=>$X)json_row("sum-$x",format_number($X));json_row("");}elseif($_GET["script"]=="kill")connection()->query("KILL ".number($_POST["kill"]));else{foreach(count_tables(adminer()->databases())as$j=>$X){json_row("tables-$j",$X);json_row("size-$j",db_size($j));}json_row("");}exit;}else{$Hi=array_merge((array)$_POST["tables"],(array)$_POST["views"]);if($Hi&&!$l&&!$_POST["search"]){$I=true;$mf="";if(JUSH=="sql"&&$_POST["tables"]&&count($_POST["tables"])>1&&($_POST["drop"]||$_POST["truncate"]||$_POST["copy"]))queries("SET foreign_key_checks = 0");if($_POST["truncate"]){if($_POST["tables"])$I=truncate_tables($_POST["tables"]);$mf='å·²æ¸…ç©ºè¡¨ã€‚';}elseif($_POST["move"]){$I=move_tables((array)$_POST["tables"],(array)$_POST["views"],$_POST["target"]);$mf='å·²è½¬ç§»è¡¨ã€‚';}elseif($_POST["copy"]){$I=copy_tables((array)$_POST["tables"],(array)$_POST["views"],$_POST["target"]);$mf='å·²å¤åˆ¶è¡¨ã€‚';}elseif($_POST["drop"]){if($_POST["views"])$I=drop_views($_POST["views"]);if($I&&$_POST["tables"])$I=drop_tables($_POST["tables"]);$mf='å·²åˆ é™¤è¡¨ã€‚';}elseif(JUSH=="sqlite"&&$_POST["check"]){foreach((array)$_POST["tables"]as$R){foreach(get_rows("PRAGMA integrity_check(".q($R).")")as$K)$mf
.="<b>".h($R)."</b>: ".h($K["integrity_check"])."<br>";}}elseif(JUSH!="sql"){$I=(JUSH=="sqlite"?queries("VACUUM"):apply_queries("VACUUM".($_POST["optimize"]?"":" ANALYZE"),$_POST["tables"]));$mf='å·²ä¼˜åŒ–è¡¨ã€‚';}elseif(!$_POST["tables"])$mf='æ²¡æœ‰è¡¨ã€‚';elseif($I=queries(($_POST["optimize"]?"OPTIMIZE":($_POST["check"]?"CHECK":($_POST["repair"]?"REPAIR":"ANALYZE")))." TABLE ".implode(", ",array_map('Adminer\idf_escape',$_POST["tables"])))){while($K=$I->fetch_assoc())$mf
.="<b>".h($K["Table"])."</b>: ".h($K["Msg_text"])."<br>";}queries_redirect(substr(ME,0,-1),$mf,$I);}page_header(($_GET["ns"]==""?'æ•°æ®åº“'.": ".h(DB):'æ¨¡å¼'.": ".h($_GET["ns"])),$l,true);if(adminer()->homepage()){if($_GET["ns"]!==""){echo"<h3 id='tables-views'>".'è¡¨å’Œè§†å›¾'."</h3>\n";$Gi=tables_list();if(!$Gi)echo"<p class='message'>".'æ²¡æœ‰è¡¨ã€‚'."\n";else{echo"<form action='' method='post'>\n";if(support("table")){echo"<fieldset><legend>".'åœ¨è¡¨ä¸­æœç´¢æ•°æ®'." <span id='selected2'></span></legend><div>",html_select("op",adminer()->operators(),idx($_POST,"op",JUSH=="elastic"?"should":"LIKE %%"))," <input type='search' name='query' value='".h($_POST["query"])."'>",script("qsl('input').onkeydown = partialArg(bodyKeydown, 'search');","")," <input type='submit' name='search' value='".'æœç´¢'."'>\n","</div></fieldset>\n";if($_POST["search"]&&$_POST["query"]!=""){$_GET["where"][0]["op"]=$_POST["op"];search_tables();}}echo"<div class='scrollable'>\n","<table class='nowrap checkable odds'>\n",script("mixin(qsl('table'), {onclick: tableClick, ondblclick: partialArg(tableClick, true)});"),'<thead><tr class="wrap">','<td><input id="check-all" type="checkbox" class="jsonly">'.script("qs('#check-all').onclick = partial(formCheck, /^(tables|views)\[/);",""),'<th>'.'è¡¨','<td>'.'å¼•æ“'.doc_link(array('sql'=>'storage-engines.html')),'<td>'.'æ ¡å¯¹'.doc_link(array('sql'=>'charset-charsets.html','mariadb'=>'supported-character-sets-and-collations/')),'<td>'.'æ•°æ®é•¿åº¦'.doc_link(array('sql'=>'show-table-status.html','pgsql'=>'functions-admin.html#FUNCTIONS-ADMIN-DBOBJECT','oracle'=>'REFRN20286')),'<td>'.'ç´¢å¼•é•¿åº¦'.doc_link(array('sql'=>'show-table-status.html','pgsql'=>'functions-admin.html#FUNCTIONS-ADMIN-DBOBJECT')),'<td>'.'æ•°æ®ç©ºé—²'.doc_link(array('sql'=>'show-table-status.html')),'<td>'.'è‡ªåŠ¨å¢é‡'.doc_link(array('sql'=>'example-auto-increment.html','mariadb'=>'auto_increment/')),'<td>'.'è¡Œæ•°'.doc_link(array('sql'=>'show-table-status.html','pgsql'=>'catalog-pg-class.html#CATALOG-PG-CLASS','oracle'=>'REFRN20286')),(support("comment")?'<td>'.'æ³¨é‡Š'.doc_link(array('sql'=>'show-table-status.html','pgsql'=>'functions-info.html#FUNCTIONS-INFO-COMMENT-TABLE')):''),"</thead>\n";$T=0;foreach($Gi
as$B=>$U){$Mj=($U!==null&&!preg_match('~table|sequence~i',$U));$t=h("Table-".$B);echo'<tr><td>'.checkbox(($Mj?"views[]":"tables[]"),$B,in_array("$B",$Hi,true),"","","",$t),'<th>'.(support("table")||support("indexes")?"<a href='".h(ME)."table=".urlencode($B)."' title='".'æ˜¾ç¤ºç»“æ„'."' id='$t'>".h($B).'</a>':h($B));if($Mj&&!preg_match('~materialized~i',$U)){$Ti='è§†å›¾';echo'<td colspan="6">'.(support("view")?"<a href='".h(ME)."view=".urlencode($B)."' title='".'ä¿®æ”¹è§†å›¾'."'>$Ti</a>":$Ti),'<td align="right"><a href="'.h(ME)."select=".urlencode($B).'" title="'.'é€‰æ‹©æ•°æ®'.'">?</a>';}else{foreach(array("Engine"=>array(),"Collation"=>array(),"Data_length"=>array("create",'ä¿®æ”¹è¡¨'),"Index_length"=>array("indexes",'ä¿®æ”¹ç´¢å¼•'),"Data_free"=>array("edit",'æ–°å»ºæ•°æ®'),"Auto_increment"=>array("auto_increment=1&create",'ä¿®æ”¹è¡¨'),"Rows"=>array("select",'é€‰æ‹©æ•°æ®'),)as$x=>$_){$t=" id='$x-".h($B)."'";echo($_?"<td align='right'>".(support("table")||$x=="Rows"||(support("indexes")&&$x!="Data_length")?"<a href='".h(ME."$_[0]=").urlencode($B)."'$t title='$_[1]'>?</a>":"<span$t>?</span>"):"<td id='$x-".h($B)."'>");}$T++;}echo(support("comment")?"<td id='Comment-".h($B)."'>":""),"\n";}echo"<tr><td><th>".sprintf('å…±è®¡ %d',count($Gi)),"<td>".h(JUSH=="sql"?get_val("SELECT @@default_storage_engine"):""),"<td>".h(db_collation(DB,collations()));foreach(array("Data_length","Index_length","Data_free")as$x)echo"<td align='right' id='sum-$x'>";echo"\n","</table>\n",script("ajaxSetHtml('".js_escape(ME)."script=db');"),"</div>\n";if(!information_schema(DB)){echo"<div class='footer'><div>\n";$Gj="<input type='submit' value='".'æ•´ç†ï¼ˆVacuumï¼‰'."'> ".on_help("'VACUUM'");$ag="<input type='submit' name='optimize' value='".'ä¼˜åŒ–'."'> ".on_help(JUSH=="sql"?"'OPTIMIZE TABLE'":"'VACUUM OPTIMIZE'");echo"<fieldset><legend>".'å·²é€‰ä¸­'." <span id='selected'></span></legend><div>".(JUSH=="sqlite"?$Gj."<input type='submit' name='check' value='".'æ£€æŸ¥'."'> ".on_help("'PRAGMA integrity_check'"):(JUSH=="pgsql"?$Gj.$ag:(JUSH=="sql"?"<input type='submit' value='".'åˆ†æ'."'> ".on_help("'ANALYZE TABLE'").$ag."<input type='submit' name='check' value='".'æ£€æŸ¥'."'> ".on_help("'CHECK TABLE'")."<input type='submit' name='repair' value='".'ä¿®å¤'."'> ".on_help("'REPAIR TABLE'"):"")))."<input type='submit' name='truncate' value='".'æ¸…ç©º'."'> ".on_help(JUSH=="sqlite"?"'DELETE'":"'TRUNCATE".(JUSH=="pgsql"?"'":" TABLE'")).confirm()."<input type='submit' name='drop' value='".'åˆ é™¤'."'>".on_help("'DROP TABLE'").confirm()."\n";$i=(support("scheme")?adminer()->schemas():adminer()->databases());echo"</div></fieldset>\n";$Kh="";if(count($i)!=1&&JUSH!="sqlite"){echo"<fieldset><legend>".'è½¬ç§»åˆ°å…¶å®ƒæ•°æ®åº“'." <span id='selected3'></span></legend><div>";$j=(isset($_POST["target"])?$_POST["target"]:(support("scheme")?$_GET["ns"]:DB));echo($i?html_select("target",$i,$j):'<input name="target" value="'.h($j).'" autocapitalize="off">'),"</label> <input type='submit' name='move' value='".'è½¬ç§»'."'>",(support("copy")?" <input type='submit' name='copy' value='".'å¤åˆ¶'."'> ".checkbox("overwrite",1,$_POST["overwrite"],'è¦†ç›–'):""),"</div></fieldset>\n";$Kh=" selectCount('selected3', formChecked(this, /^(tables|views)\[/));";}echo"<input type='hidden' name='all' value=''>",script("qsl('input').onclick = function () { selectCount('selected', formChecked(this, /^(tables|views)\[/));".(support("table")?" selectCount('selected2', formChecked(this, /^tables\[/) || $T);":"")."$Kh }"),input_token(),"</div></div>\n";}echo"</form>\n",script("tableCheck();");}echo"<p class='links'><a href='".h(ME)."create='>".'åˆ›å»ºè¡¨'."</a>\n",(support("view")?"<a href='".h(ME)."view='>".'åˆ›å»ºè§†å›¾'."</a>\n":"");if(support("routine")){echo"<h3 id='routines'>".'å­ç¨‹åº'."</h3>\n";$Ch=routines();if($Ch){echo"<table class='odds'>\n",'<thead><tr><th>'.'åç§°'.'<td>'.'ç±»å‹'.'<td>'.'è¿”å›ç±»å‹'."<td></thead>\n";foreach($Ch
as$K){$B=($K["SPECIFIC_NAME"]==$K["ROUTINE_NAME"]?"":"&name=".urlencode($K["ROUTINE_NAME"]));echo'<tr>','<th><a href="'.h(ME.($K["ROUTINE_TYPE"]!="PROCEDURE"?'callf=':'call=').urlencode($K["SPECIFIC_NAME"]).$B).'">'.h($K["ROUTINE_NAME"]).'</a>','<td>'.h($K["ROUTINE_TYPE"]),'<td>'.h($K["DTD_IDENTIFIER"]),'<td><a href="'.h(ME.($K["ROUTINE_TYPE"]!="PROCEDURE"?'function=':'procedure=').urlencode($K["SPECIFIC_NAME"]).$B).'">'.'ä¿®æ”¹'."</a>";}echo"</table>\n";}echo'<p class="links">'.(support("procedure")?'<a href="'.h(ME).'procedure=">'.'åˆ›å»ºè¿‡ç¨‹'.'</a>':'').'<a href="'.h(ME).'function=">'.'åˆ›å»ºå‡½æ•°'."</a>\n";}if(support("sequence")){echo"<h3 id='sequences'>".'åºåˆ—'."</h3>\n";$Vh=get_vals("SELECT sequence_name FROM information_schema.sequences WHERE sequence_schema = current_schema() ORDER BY sequence_name");if($Vh){echo"<table class='odds'>\n","<thead><tr><th>".'åç§°'."</thead>\n";foreach($Vh
as$X)echo"<tr><th><a href='".h(ME)."sequence=".urlencode($X)."'>".h($X)."</a>\n";echo"</table>\n";}echo"<p class='links'><a href='".h(ME)."sequence='>".'åˆ›å»ºåºåˆ—'."</a>\n";}if(support("type")){echo"<h3 id='user-types'>".'ç”¨æˆ·ç±»å‹'."</h3>\n";$Ej=types();if($Ej){echo"<table class='odds'>\n","<thead><tr><th>".'åç§°'."</thead>\n";foreach($Ej
as$X)echo"<tr><th><a href='".h(ME)."type=".urlencode($X)."'>".h($X)."</a>\n";echo"</table>\n";}echo"<p class='links'><a href='".h(ME)."type='>".'åˆ›å»ºç±»å‹'."</a>\n";}if(support("event")){echo"<h3 id='events'>".'äº‹ä»¶'."</h3>\n";$L=get_rows("SHOW EVENTS");if($L){echo"<table>\n","<thead><tr><th>".'åç§°'."<td>".'è°ƒåº¦'."<td>".'å¼€å§‹'."<td>".'ç»“æŸ'."<td></thead>\n";foreach($L
as$K)echo"<tr>","<th>".h($K["Name"]),"<td>".($K["Execute at"]?'åœ¨æŒ‡å®šæ—¶é—´'."<td>".$K["Execute at"]:'æ¯'." ".$K["Interval value"]." ".$K["Interval field"]."<td>$K[Starts]"),"<td>$K[Ends]",'<td><a href="'.h(ME).'event='.urlencode($K["Name"]).'">'.'ä¿®æ”¹'.'</a>';echo"</table>\n";$Hc=get_val("SELECT @@event_scheduler");if($Hc&&$Hc!="ON")echo"<p class='error'><code class='jush-sqlset'>event_scheduler</code>: ".h($Hc)."\n";}echo'<p class="links"><a href="'.h(ME).'event=">'.'åˆ›å»ºäº‹ä»¶'."</a>\n";}}}}page_footer();