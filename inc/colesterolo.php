<?php
/** Colesterolo.php 
Author : Mauro Lobosky Donadello
Description : Open a vcf file and add a AD fields, calculating the values of SAF SAR SRF SRR to obtain 
AD = X , Y 
where 
X = SRF+SRR 
Y = SAF+SAR  

the output is a file with the same name of original file but with the prefix "modified_"

v0.1
Mauro Lobosky Donadello lobosky@gmail.com


*/
if($argc < 2 ) {
usage();
}


$rFiledir = "../VCF/";
$wFiledir = "../modified_VCF/";
$rFilename = $argv[1];
$wFilename = "modified_" . $rFilename;
 

$rF = fopen($rFiledir.$rFilename,'r');
if(!$rF){
 die("Unable to open $rFilename");
}

$wF = fopen($wFiledir.$wFilename,'w');
if(!$wF){
 die("Unable to open $wFilename");
}


$header = [];
$data = [];
$debug = false;


//leggo le righe del file. 
while(!feof($rF)){
 //skippo le righe con ## all'inizio e le metto da parte
 $line = fgets($rF);

 if(preg_match_all("/^##(.*)$/m",$line,$m)) {
	$header[] = $line;
	continue;
 }
 if(preg_match_all("/^#[^#](.*)$/m",$line,$m)){
	 $fields = explode("\t",trim(substr($line,1)));
	 $header[] = $line;
	 fwrite($wF,implode($header));
	 continue;
 }
  //se e' arrivato qui vuol dire che sono finite le righe header
 //quindi scrivo l'header nel nuovo file. 

  
  $data = [];
  $info = []; 
  //divido per colonne la riga
  $line = trim($line);
  if($line ==='') continue;
  
  $colonne = explode("\t",$line);
  
  foreach($fields as $index=>$fieldName){
          $data[$fieldName] = $colonne[$index];
  }
 
  //devo estrarre i valori di DP dalla colonna INFO
  $tempinfo  = explode(";",$data['INFO']);
  foreach($tempinfo as $index=>$value){
        $t = explode("=",$value);
   	$info[$t[0]]=$t[1];
}
   

  //ora divido per sigle la colonna FORMAT
  $x = intval($info['SRF'] + $info['SRR']) ;
  $y = intval($info['SAF'] + $info['SAR']) ;
 
  //estraggo l'ultima chiave dell'array data, non sapendo il nome della stessa pero'. 
  $lastFieldName = end(array_keys($data));
  $lastFieldValue = end($data)  . ':' . $x . ',' . $y;
  
  $data[$lastFieldName] = $lastFieldValue;
  $data['FORMAT'].=':AD'; 
  //print_R($format) & $debug;	
  //print_r($data) & $debug;
   

  print '***********' . "\n"; 
  print "Chrom " . $data['CHROM'] . "\n";
  print "DP = " . $info['DP'] . "\n";
  print 'SRF = '. $info['SRF'] . "\n";
  print 'SRR = '. $info['SRR'] . "\n";  
  print 'SAF = '. $info['SAF'] . "\n";   
  print 'SAR = '. $info['SAR'] . "\n";   
  print 'INFO = ' . $data['INFO'] . "\n";
  print 'FORMAT = ' . $data['FORMAT'] . "\n";
  print $lastFieldName . ' = ' . $lastFieldValue . "\n"; 
  print '***********' . "\n\n"; 

  
  fwrite($wF,implode("\t",array_values($data)) . "\n");

}

 fclose($rF);
 fclose($wF);

function debug($d="None",$m="Empty message"){
  GLOBAL $debug;
  print "Description:$d, $m\n" && $debug;

}
function usage(){
  global $argv;
  print "\nUSAGE: php " . $argv[0] . " filename.vcf\n\n" ;
  exit;

}

?>
