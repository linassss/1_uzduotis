<?php

//Nurodomas pilnas kelias iki katalogo, kuriame yra kodo bylos
$folder_path = "C:\Users\user\Desktop\ReadmeBA-master\ReadmeBA";

//Nurodomas kelias iki katalogo, kuriame bus išsaugoma sugeneruota tekstinė byla
$text_path = "";

if ($text_path === "") {
  $text_path = $folder_path;
}

//Bylos, kurioje bus saugomi komentarai, full path.
$text_file = $text_path . "\\komentarai.txt";

//Funkcija, kuri sugeneruoja kataloge esančių bylų full path array
function findFiles($folder)
{
  $files = array_diff(scandir($folder), array(".", "..", "komentarai.txt"));
  $file_array = array();
  foreach($files as $file) {
    $full_path = $folder . "\\" . $file;
    if(!is_dir($full_path)) {
      array_push($file_array, $full_path);
    }
  }
  return $file_array;
}

//Pagrindinė funkcija.
function findComments($folder_path, $text_file)
{
  $comment_file = fopen($text_file, "w"); //Sukuriama tekstinė byla pavadinimu "komentarai.txt". Jeigu tokia byla jau egzistuoja, jos turinys ištrinamas.
  fclose($comment_file);
  $file_list = findFiles($folder_path); //gaunamas visų bylų full path array
  $match_phrase = "/\"[^\"]*\"|(\/\/.*|\/\*(.|\s)*?\*\/)/";
  foreach($file_list as $file) {
    $my_file = fopen($file, "r") or die("Can't open the file.");
    $file_contents = fread($my_file, filesize($file));
    preg_match_all($match_phrase, $file_contents, $comms, PREG_SET_ORDER); //byloje esantys komentarai įrašomi į $comms array
    fclose($my_file);
    $comment_file = fopen($text_file, "a");
    $bylos_pavadinimas = str_replace($folder_path . "\\", "", $file);
    $head = "==========" . $bylos_pavadinimas . "=========="; //sugeneruojamas "headeris" su bylos pavadinimu
    fwrite($comment_file, PHP_EOL);
    fwrite($comment_file, $head);
    $n = 1;
    foreach($comms as $nr => $com) { //ciklas, kuris iš preg_match_all sugeneruoto array su atitikmenimis ištraukia reikiamus duomenis ir įrašo į komentarai.txt bylą
      if ($com[1] != NULL) {
      fwrite($comment_file, PHP_EOL);
      fwrite($comment_file, $n . ". " . $com[1]);
      $n++;
      }
    }
    fclose($my_file);
  }
}

findComments($folder_path, $text_file);

?>
