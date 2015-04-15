<?php

  error_reporting(E_ALL & ~E_NOTICE);

  // Устанавливаем соединение с базой данных
  require_once("../../config/config.php");
  // Подключаем блок авторизации
  require_once("../authorize.php");
  // Подключаем классы формы
  require_once("../../config/class.config.dmn.php");
  require_once("../../utils/utils.resizeimg.php");
  if(empty($_POST))
  {
    // Отмечаем флажок hide
    $_REQUEST['hide'] = true;
  }
  try
  {
  $query="SELECT * From $tbl_catalog";
  $cat=mysql_query($query);
  if(!$cat){
  exit($query);
  }
  $calalogs=array();
  while($cc=mysql_fetch_array($cat))
  {
  $calalogs[$cc['id']]=$cc['name'];
  }
  $urlpict=new field_file('urlpict','фото',false,$_FILES, '../../media/images/');
      $name        = new field_text("name",
                                  "Название",
                                  true,
                                  $_POST['name']);
	$editor1        = new field_textarea("editor1",
                                  "Описание категории",
                                  true,
                                  $_POST['editor1']);
    $hide        = new field_checkbox("hide",
                                      "Отображать",
                                      $_REQUEST['hide']);
  $razdel=new field_select('razdel', 'Категория', $calalogs, $_POST['razdel']);  
    $form = new form(array(
	                       "name" => $name, 
                           "editor1" => $editor1,
                           "hide" => $hide, 
						   "razdel"=>$razdel,
						   "urlpict"=>$urlpict
						   ), 
                     "Добавить",
                     "field");

    // Обработчик HTML-формы
    if(!empty($_POST))
    {
      // Проверяем корректность заполнения HTML-формы
      // и обрабатываем текстовые поля
      $error = $form->check();
	  if($form->fields['urltext']->value == "-")
	  {
	  $error[] = "Вы не выбрали раздел";
	  }
      if(empty($error))
      {
        // Скрытая или открытая директория
if($form->fields['hide']->value){
$showhide='show';
}else{
$showhide='hide';
}
$var=$form->fields['urlpict']->get_filename();
if($var){
$piche=date('y_m_d_h_i_').$var;
$pichesmal="s_".$piche;
resizeimg("../../media/images/".$piche,"../../media/images/".$pichesmal,200,200);
}else{
$piche='-';
$pichesmal='-';
}
$query="INSERT INTO $tbl_tovar 
VALUES(null,
'{$form->fields['name']->value}',
'{$form->fields['editorl']->value}',
'$piche',
'$pichesmal',
'{$form->fields[price]->value}',
'$showhide',
NOW(),
{$form->fields['razdel']->value})";
$cat=mysql_query($query);
if(!$cat){
exit($query);
}
      
	   ?>
		<script>
		 document.location.href="index.php";
		</script>
		<?
      }
    }
    // Начало страницы
    $title     = 'Добавление новой категории';
    $pageinfo  = '<p class=help></p>';
    // Включаем заголовок страницы
    require_once("../utils/top.php");
?>
<div align=left>
<FORM>
<INPUT class="button" TYPE="button" VALUE="На предыдущую страницу" 
onClick="history.back()">
</FORM> 
</div>
<?
    // Выводим сообщения об ошибках, если они имеются
    if(!empty($error))
    {
      foreach($error as $err)
      {
        echo "<span style=\"color:red\">$err</span><br>";
      }
    }
?>
<div class="table_user">
<?
    // Выводим HTML-форму 
    $form->print_form();
?>
</div>
<?
  }
  catch(ExceptionObject $exc) 
  {
    require("../utils/exception_object.php"); 
  }
  catch(ExceptionMySQL $exc)
  {
    require("../utils/exception_mysql.php"); 
  }
  catch(ExceptionMember $exc)
  {
    require("../utils/exception_member.php"); 
  }

  // Включаем завершение страницы
  require_once("../utils/bottom.php");
?>
