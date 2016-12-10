<?php
function genbindata($allelefreq) {

         $binhashmap=array('0.05'=>0,'0.1'=>0,'0.15'=>0,'0.2'=>0,'0.25'=>0,'0.3'=>0,'0.35'=>0,'0.4'=>0,'0.45'=>0,'0.5'=>0);
        
         for ($x=0; $x<count($allelefreq); $x++)
         {
                if($allelefreq[$x]>0.5) {$allelefreq[$x]=(1-$allelefreq[$x]);}
                if($allelefreq[$x] >= 0 && $allelefreq[$x] <= 0.05 ){
                        $binhashmap['0.05']++;
                } else  if($allelefreq[$x] > 0.05 && $allelefreq[$x] <= 0.1 ){
                        $binhashmap['0.1']++;
                }  else  if($allelefreq[$x] > 0.1 && $allelefreq[$x] <= 0.15 ){
                        $binhashmap['0.15']++;;
                }  else  if($allelefreq[$x] > 0.15 && $allelefreq[$x] <= 0.2 ){
                        $binhashmap['0.2']++;;
                }  else  if($allelefreq[$x] > 0.2 && $allelefreq[$x] <= 0.25 ){
                        $binhashmap['0.25']++;;
                }  else  if($allelefreq[$x] > 0.25 && $allelefreq[$x] <= 0.3 ){
                        $binhashmap['0.3']++;;
                }  else  if($allelefreq[$x] > 0.3 && $allelefreq[$x] <= 0.35 ){
                        $binhashmap['0.35']++;;
                }  else  if($allelefreq[$x] > 0.35 && $allelefreq[$x] <= 0.4 ){
                        $binhashmap['0.4']++;;
                }  else  if($allelefreq[$x] > 0.4 && $allelefreq[$x] <= 0.45 ){
                        $binhashmap['0.45']++;;
                }  else  if($allelefreq[$x] > 0.45 && $allelefreq[$x] <= 0.5 ){
                        $binhashmap['0.5']++;;
                }
         }

         $frequency = array();

         while (list($key, $value) = each($binhashmap)) {
               array_push($frequency,$value);
         }

         return $frequency;
}
   //$data=array('0.224014814201691','0.985017112199763','0.196495025228902','0.534410017754496','0.785637406030769','0.848990164356728','0.977253061763992','0.976965431440817');
   //$d=genbindata($data);
?>

