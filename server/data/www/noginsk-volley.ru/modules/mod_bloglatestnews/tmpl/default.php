<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_bloglatestnews
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
$imgas=$params->get('imgas');
$imgasheight=$params->get('imgasheight');
$blogcount=$params->get('blogcount');
$blogimgon=$params->get('blogimgon');
$blogzagon=$params->get('blogzagon');
$blogzagdlina=$params->get('blogzagdlina');
$blogopion=$params->get('blogopion');
$blogopidlina=$params->get('blogopidlina');
$blogdataon=$params->get('blogdataon');
$blogextrimon=$params->get('blogextrimon');
$blogii = 0;
$blogiista = 1;
$blogiiend = 0;
?>
<div class="bloglatestnews1">
<?php foreach ($list as $item) :
$blogii++; ?>
<?php if($blogextrimon == 0) { if($blogii == $blogiista) {echo '<div class="bloglatestnews7">'; $blogiista = $blogiista + $blogcount;} } //планка МОЖНО УДАЛИТЬ ЭТУ СТРОКУ - ЧТОБЫ БЫЛО БЕЗ ПЛАНОК ?>
<div class="bloglatestnews2">
<?php if($blogimgon == 1) { //Вкл/Выкл. отображение картинок ?>
<div class="bloglatestnews3">
<?php $infoimages = preg_replace("/.*image_intro\"\:\"([^,\"]*).*/", "$1", $item->images);  //Картинка материала
$infoimages = str_replace("\\", "", $infoimages);
if(!empty($infoimages)) echo '<a href="'.$item->link.'"><img src="'.$infoimages.'"/></a>';  //Картинка есть
else echo '<a href="'.$item->link.'"><img src="'.$imgas.'"/></a>';                          //Картинка по умолчанию
?>
</div>
<?php  } ?>

<?php
if($blogzagon == 1) {                                     //Вкл/Выкл. отображение заголовка
$ftexts2 = strip_tags($item->title);                      //Длина заголовка
$ftexts2 = preg_replace("/\n/", " ", $ftexts2);
$ftexts2 = mb_substr($ftexts2, 0, $blogzagdlina, 'UTF-8');
echo '<div class="bloglatestnews4"><a href="'.$item->link.'">'.$ftexts2.'</a></div>';
}
if($blogopion == 1) {                                     //Вкл/Выкл. отображение описания
$ftexts = strip_tags($item->introtext);                   //Длина описания
$ftexts = preg_replace("/\n/", " ", $ftexts);
$ftexts = mb_substr($ftexts, 0, $blogopidlina, 'UTF-8');
echo '<div class="bloglatestnews5">'.$ftexts.'</div>';

}
if($blogdataon == 1) {                                    //Вкл/Выкл. отображение даты создания материала
$originalDate = $item->publish_up;
$newDate = date("d-m-Y H:i", strtotime($originalDate));
echo '<div class="bloglatestnews6">'.$newDate.'</div>';
}
?>
</div>
<?php if($blogextrimon == 0) { if($blogii == ($blogcount + $blogiiend)) {echo '</div>'; $blogiiend = $blogcount + $blogiiend;} } //endпланка МОЖНО УДАЛИТЬ ЭТУ СТРОКУ - ЧТОБЫ БЫЛО БЕЗ ПЛАНОК ?>
<?php endforeach; ?>
</div>
<div style="text-align: right;" class="blavtors"><a target="_blank" style="text-decoration:none; color: #c0c0c0!important; font-family: arial,helvetica,sans-serif;  font-size: 7px!important; " title="Программирование на PHP, Jquery, Joomla и Wordpress" href=""></a></div>

<style>
.bloglatestnews1 {
overflow: hidden!important;
padding: 0px!important;
margin: 10px 0px 10px!important;
}
.bloglatestnews2 {
margin: 0px!important;
padding: 0px!important;
<?php if ($blogcount == 1) {echo 'float: none!important;  margin-right: 0%!important; width: 100%!important;overflow: hidden!important;';} ?>
<?php if ($blogcount == 2) {echo 'float: left!important;  margin-right: 5%!important; width: 45%!important;';} ?>
<?php if ($blogcount == 3) {echo 'float: left!important;  margin-right: 3%!important; width: 30.3%!important;';} ?>
<?php if ($blogcount == 4) {echo 'float: left!important;  margin-right: 3%!important; width: 22%!important;';} ?>
<?php if ($blogcount == 5) {echo 'float: left!important;  margin-right: 2%!important; width: 18%!important;';} ?>
<?php if ($blogcount == 6) {echo 'float: left!important;  margin-right: 2%!important; width: 14.6%!important;';} ?>
margin-bottom: 34px!important;
}
.bloglatestnews3 {
margin: 0px!important;
padding: 0px!important;
height: <?php echo $imgasheight; ?>px!important;
<?php if ($blogcount == 1) {echo 'float: left;    margin-right: 20px;';} ?>
overflow: hidden;
}
.bloglatestnews3 img {
width: 100%!important;
<?php if ($blogcount == 1) {echo 'width: 150px!important;';} ?>
border: 0px solid #D2D2D2!important;
margin: 7px!important;
padding: 0px!important;
float: none!important;
display: block!important;
opacity: 1!important;
max-width: none!important;
height: auto!important;
box-shadow: none!important;
border-radius: 0px!important;
}
.bloglatestnews3 img:hover {
opacity: 0.8!important;
}
.bloglatestnews4 {
margin: 0px!important;
padding: 0px!important;
font-size: 14px!important;
line-height: 18px!important;
margin-top: 10px!important;
margin-bottom: 10px!important;
text-align: left!important;
text-shadow: none!important;
}
.bloglatestnews4 a {
font-size: 16px!important;
line-height: 18px!important;
color: #2282D6!important;
font-weight: bold!important;
text-decoration: none!important;
text-align: left!important;
text-shadow: none!important;
-moz-transition: 0.3s;
-o-transition: 0.3s;
-webkit-transition: 0.3s;
transition: 0.3s;
}
.bloglatestnews4 a:hover {
color: #000!important;
}

.bloglatestnews5 {
margin: 0px!important;
padding: 0px!important;
font-size: 12px!important;
line-height: 15px!important;
text-align: left!important;
font-weight: normal!important;
text-shadow: none!important;
color: #222!important;
}
.bloglatestnews6 {
margin: 0px!important;
padding: 0px!important;
font-size: 10px!important;
line-height: 10px!important;
text-align: right!important;
color: #5A5A5A!important;
text-shadow: none!important;
font-weight: normal!important;
margin-top: 5px!important;
}
.bloglatestnews7 {
margin: 0px!important;
padding: 0px!important;
overflow: hidden!important;
}

</style>
