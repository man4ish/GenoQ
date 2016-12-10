<?php  include("backend/snipaWhitelists.php"); 

$id = time()*100000+rand(0,99999);
//$tmpdatadir = "/home/metabolomics/snipa/web/tmpdata/".$id."/";
//$tmpdatadir = "/home/metabolomics/snipa/web/snipa/tmp/".$id."/";
$tmpdatadir = "/home/metabolomics/snipa/web/snipa/tmp/";
$user=get_current_user();
print($user);
system('mkdir '.$tmpdatadir);
echo "$tmpdatadir\n";

/*if (mkdir($tmpdatadir)) {
        $status = array();
        $status['stepnum'] = 1;
        $status['totalstepnum'] = 100;
        $status['message'] = "";
        $status['errmessage'] = "";
        $status['ok'] = "";

        $statfilefh = fopen($tmpdatadir."/status.txt.1",'w');
        fwrite($statfilefh, utf8_encode(json_encode($status)));
        fclose($statfilefh);
        copy($tmpdatadir."/status.txt.1",$tmpdatadir."/status.txt");
        //print($id);
}
*/


//chown($tmpdatadir, $user);
//$stat = stat($tmpdatadir);
//print_r(posix_getpwuid($stat['uid']));

//exec("sudo chown -R ".$user.":".$user." ".$tmpdatadir ." < sudopass/sudopass.secret");
//echo("sudo chown -R ".$user.":".$user." ".$tmpdatadir ." < sudopass/sudopass.secret");
//shell_exec("sudo chown -R ".$user.":".$user." ".$tmpdatadir);
//exec("sudo chmod 777 ".$tmpdatadir);



$JobId =  preg_replace( '/[^0-9]/', '', $id);
if (strlen($JobId) != 15) { $allok = FALSE; }
/*
if ($allok) {
        $tmpdatadir = "tmpdata";
        $serverdir = "/home/metabolomics/snipa/web/";
        $JobDir = $serverdir."/".$tmpdatadir."/".$JobId;
        if (!file_exists($JobDir)) {
                $allok = FALSE;
        }
}
*/

$JobDir=$tmpdatadir;
#print($tmpdatadir);

include 'bindata.php';       
$value ="";
$value = ! empty($_GET['value']) ? $_GET['value'] : '';
echo "$value\n";

?>

<style>

hr{
  border-top: 1px silver;
}

clickabletext {
    background-color: silver;
    width: 300px;
    padding-right: .5cm; 
    padding-left: .5cm; 
    
}


buttontext {
    background-color: darkgray;
    width: 300px;  
    padding:1px;
}

p.spinner {
       margin-top: 5cm;
} 

html {overflow-y: scroll;}
table {   
}
.scroll {
    max-height: 200px;
    max-width: 1000px;
    overflow: auto;
}


  	.hoverTable{
		width:100%; 
		border-collapse:collapse; 
	}
	.hoverTable td{ 
		padding:7px; border:#FFFFFF 1px solid;
	}
	/*Define the default color for all the table rows */
	.hoverTable tr{
		background: #b8d1f3;
	}
        .hoverTable th{
		background: silver;
                color:white;
	}
       
	/* Define the hover highlight color for the table row */
        .hoverTable tr:hover {
          background-color: #ffff99;
        }
        #dataTable tr:nth-child(even){
		background: #F0F0F0;
	}

        #summary-table th,td,tr {
          border: 1px solid white;
        }
        #plottable tr th {
          border: 1px solid gray;
        }


</style>

<style TYPE="text/css">
td{font-family: Arial; font-size: 10pt;}
th{font-family: Arial; font-size: 10pt;}
</style>

<script>
if ($("#dataTable").empty()) {
    $("#submit-button").click();
}
</script>

      <meta charset="utf-8">
      <title>jQuery UI Tabs - Default functionality</title>
      <link rel="stylesheet" href="https://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
      <script src="https://code.jquery.com/jquery-1.10.2.js"></script>
      <script src="https://code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
      <link rel="stylesheet" href="/resources/demos/style.css">
      <style>
      #tabs{
  
}

#vartabs .ui-widget-header {
  background-color: white;
  background-image: none;
  border-top: none;
  border-right: none;
  border-left: none;
}

#vartabs .ui-widget-content {
  background-color: white;
  background-image: none;

}

#vartabs .ui-corner-top,#tabs .ui-corner-bottom,#tabs .ui-corner-all{
  border-top-left-radius: 0px;
  border-top-right-radius: 0px;
  border-bottom-left-radius: 0px;
  border-bottom-right-radius: 0px;
}



#vartabs .ui-state-default,
#vartabs .ui-state-default a {
  background-color: white;
  text-align: center;
}

#vartabs .ui-state-default a {
  width: herepx;
}


#vartabs .ui-tabs-active,
#vartabs .ui-tabs-active a {
  background-color: darkgray;
  text-align: center;
  
}

 </style>

 <script>
      $(function() {
        $( "#vartabs" ).tabs();
      });
 </script>


<script>
  $(function(){
      
      var imgpath='tmpdata/'+<?php echo $id ?>+'/venndiagram.png';
      //alert(imgpath);
      $("#includedContent").load("frontend/js/barplot.html"); 
    });
 
$(function(){
      jsonpath='tmpdata/'+<?php echo $id ?>+'/chart.json';
      $("#piechart").load('frontend/js/distributionchart.php'); 
      //$("#piechart").load("frontend/js/piechart2.html");
    });
    
    $(document).ready(function(){
     $('.showModal1').click(function(){
          $('#popup1').dialog({width: 450,height: 450});
      });
    });
     $(document).ready(function(){
     $('.showModal2').click(function(){
          $('#popup2').dialog({width: 450,height: 450});
      });
});    
	
function openPopUp(URL) {
new_window = window.open(URL, 'Popup', 'addressbar=no,width=437,height=448');
}

function closePopUp() {
new_window.close();
}
</script>
<script src="frontend/js/wo.js"></script>

<script src="frontend/js/formvalidator.js"></script>


<div id="popup1" title="Comparative Analysis" style="display:none;">  
         
         <img width=400 height=400 align="bottom" src="<?php echo 'snipa/tmp' ?>/venndiagram.png" alt="">    
       <!--  <img width=400 height=400 align="bottom" src="<?php //echo 'tmpdata/'.$id ?>/venndiagram.png" alt="">  -->  
</div>	
	
<div id="popup2" title="SNP Distribution" style="display:none;">          
         <div id="piechart" style="min-width: 200; height: 400; max-width: 400; margin: 0 auto"></div>
</div>	


<table style="width:100%">
<form name="myForm" id="testForm" action="?task=summary" onsubmit="return validateForm()" method="post">
     <tr><td width="68%"></td><td width="13%"><input id="seachbox" type="text" size=14 name="search" class="inputbox2" /></td><td width="25%"><input type="submit" value="Search"></td></tr>
		 
	<tr><td width="78%"></td><td width="13%">
        <font size=".2">Gene, Region</font></td></tr>
     <!--<font size=".2">Snp, Gene, Region</font></td></tr>-->
</form>
</table>
<br>



<div style="height:635px; width:925px; overflow-x:scroll ; overflow-y: scroll;">

<?php
echo "$par<br>";
if($par == "")
{
   $par=$_POST["search"];
}
$results="";
$chr="";
$start="";
$stop="";
$rsflag=0;

if($par[1]==':' || $par[2]==':') 
{
    $pieces = explode(":", $par);
    $chr=$pieces[0];
    $query = explode("-", $pieces[1]);
    $start=$query[0];
    $stop=$query[1];
    echo "<table border ='0' width='100%' cellspacing='0' cellpadding='4'><tr><td bgcolor='gray' align='center'><font color='white'>Chromosome : $chr\t Start : $start\tStop : $stop</font></td></tr></table><br><br>";    
} 
else 
{
    echo "<b>Search results for Gene/SNP&nbsp;&nbsp;:&nbsp;&nbsp; <font color=black>$par</font></b><br><br>";
    $servername = "localhost";
    $username = "snipadb";
    $password = "dbaccess";
    $dbname = "snipa";
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
       die("Connection failed: " . $conn->connect_error);
    } 
    if($par[0]=='r' && $par[1]=='s') 
    {
           $rsflag=1;
           $sql = "SELECT CHR, POS, RSID FROM `genomic-grch37-1kgpp1v3-qtr-snps` where RSID= '".$par."'";
           echo "<br>";
           $result = $conn->query($sql);

           if ($result->num_rows > 0) {
               while($row = $result->fetch_assoc()) {
                     //echo "CHR".$row['CHR']."Position" .$row['POS']."RSID".$row['RSID']."<br>";
                     $chr=$row['CHR'];
                     $start=$row['POS'];
                     $stop=$row['POS'];
               }
           } else {
               echo "0 results";
           }           
      } 
      else  
      {
          $sql = "SELECT CHR, START, STOP FROM `annotation-grch37-ensembl75-genes` where NAME ='".$par."'";
          $result = $conn->query($sql);
          echo "<br>";
          if ($result->num_rows > 0) {
              while($row = $result->fetch_assoc()) {
              $chr=$row['CHR'];
              $start=$row['START'];
              $stop=$row['STOP'];             
          }
        } 
        else 
        {
          echo "0 results";
        }
	  }
	  $conn->close();
}

    $totalsnps=0;
    #$cmd="tabix /home/metabolomics/snipa/data/genoq/qatar/Qatar_merge_set.file.hethom.gz ".$chr.":".$start."-".$stop;
    //$cmd="tabix /home/metabolomics/snipa/data/genoq/qatar/Qatar_merge_file.annotation.gz ".$chr.":".$start."-".$stop;
    //print($JobDir);
    $cmd="tabix /home/metabolomics/snipa/data/genomic/grch37/genoq/qtr/qtrdb/genoq/qatar/Qatar_merge_file.annotation.gz ".$chr.":".$start."-".$stop;;
    exec($cmd.">".$JobDir."qatar.txt");

 

    print($cmd.">".$JobDir."qatar.txt");
    //exec($cmd." >/home/metabolomics/snipa/web/snipa/tmp/qatar.txt");

    $dbsnpcommon=array();
    $thousandcommon=array();
    $allelefreq=array();
    $allelefreqgenoq=array();
    $alleleamrfreq=array();
    $alleleafrfreq=array();
    $alleleeasfreq=array();
    $allelesasfreq=array();
    $alleleeurfreq=array();
    
    $missense=0;$silent=0;$none=0;$utr3=0;$utr5=0;$upstream=0;$downstream=0;$intron=0;$nsn=0;$syn=0;$funclow=0;$funcmoderate=0;
    $inbreadingcoefficient=0;$dbsnp=0;$intersection=0;$nonsense=0;$het=0;$hom=0; $hethom=0;
    
    $downsteamdids=""; $intronids=""; $nsnids=""; $synids=""; $upstreamids="";$utr3ids="";$utr5ids="";

    $thousandgenoqmissense=0;$thousandgenoqsilent=0;$thousandgenoqnone=0;$thousandgenoqutr3=0;$thousandgenoqutr5=0;$thousandgenoqupstream=0;$thousandgenoqdownstream=0;
    $thousandgenoqintron=0;$thousandgenoqnsn=0;$thousandgenoqsyn=0;$thousandgenoqfunclow=0;$thousandgenoqfuncmoderate=0;
    $thousandgenoqinbreadingcoefficient=0;$thousandcommoncount=0;$thousandgenoqnonsense=0;$thousandgenoqhethom=0;
    
    $dbsnpmissense=0;$dbsnpgenoqsilent=0;$dbsnpgenoqnone=0;$dbsnpgenoqutr3=0;$dbsnpgenoqutr5=0;$dbsnpgenoqupstream=0;$dbsnpgenoqdownstream=0;
    $dbsnpgenoqintron=0;$dbsnpgenoqnsn=0;$dbsnpgenoqsyn=0;$dbsnpgenoqfunclow=0;$dbsnpgenoqfuncmoderate=0; $dbsnpgenoqhethom=0;
    $dbsnpgenoqinbreadingcoefficient=0;$dbsnpcommoncount=0;$dbsnpgenoqnonsense=0;$dbsnpgenoqmissense=0;
    
    //$filename = "/home/metabolomics/snipa/web/snipa/tmp/qatar.txt";
    $filename= $JobDir."/qatar.txt"; 
    $file_handle = fopen($filename, "r");

    

    if ($file_handle) {
    while (!feof($file_handle)) {
		
       $genoqresults = fgets($file_handle,9096);
       if($genoqresults != "")
       {
          $totalsnps++;
       
              

       $fields=explode("\t",$genoqresults); 
       $rsid= $fields[2];
       
       if($rsid[0] == 'r' && $rsid[1]=='s')
       {
          $dbsnpcommoncount++;
          if (preg_match("/EFF=(.*?)\((.*?)\|(.*?)\|/", $genoqresults, $matches_out))
          {
              if($matches_out[1]=="UPSTREAM") { $dbsnpgenoqupstream++;}  
              else if($matches_out[1]=="DOWNSTREAM"){ $dbsnpgenoqdownstream++;} 
              else if($matches_out[1]=="NON_SYNONYMOUS_CODING"){ if($matches_out[3]=="MISSENSE"){$dbsnpgenoqmissense++;}$dbsnpgenoqnsn++;}
              else if($matches_out[1]=="SYNONYMOUS_CODING"){if($matches_out[3]=="SILENT"){$dbsnpgenoqsilent++;} $dbsnpgenoqsyn++;}
              else if($matches_out[1]=="INTRON"){$dbsnpgenoqintron++;}
              else if($matches_out[1]=="UTR_3_PRIME"){$dbsnpgenoqutr3++;} else if($matches_out[1]=="UTR_5_PRIME"){$dbsnpgenoqutr5++;} 
              else if($matches_out[1]=="STOP_GAINED"){if($matches_out[3]=="NONSENSE"){$dbsnpgenoqnonsense++;}} 
          }
     
        
          if (preg_match("/InbreedingCoeff=(.*?);/", $genoqresults, $matches_out))
          {
	       if($matches_out[1] > 0.3) {$dbsnpgenoqinbreadingcoefficient++;}             
          }
      			 
	  if($fields[10]=="L"){$dbsnpgenoqfunclow++;} 
		else if($fields[10]=="M"){$dbsnpgenoqfuncmoderate++;}
          if($fields[11]/$fields[10] < 2){$dbsnpgenoqhethom++;}               
	}
	  
	if(is_numeric($fields[9]))
        {
		 $thousandcommoncount++;
                 if (preg_match("/EFF=(.*?)\((.*?)\|(.*?)\|/", $genoqresults, $matches_out))
                 {
                     if($matches_out[1]=="UPSTREAM") { $thousandgenoqupstream++;} 
                     else if($matches_out[1]=="DOWNSTREAM"){ $thousandgenoqdownstream++;} 
                     else if($matches_out[1]=="NON_SYNONYMOUS_CODING"){ if($matches_out[3]=="MISSENSE"){$thousandgenoqmissense++;}$thousandgenoqnsn++;}
                     else if($matches_out[1]=="SYNONYMOUS_CODING"){if($matches_out[3]=="SILENT"){$thousandgenoqsilent++;} $thousandgenoqsyn++;}
                     else if($matches_out[1]=="INTRON"){$thousandgenoqintron++;}
                     else if($matches_out[1]=="UTR_3_PRIME"){$thousandgenoqutr3++;} else if($matches_out[1]=="UTR_5_PRIME"){$thousandgenoqutr5++;} 
                     else if($matches_out[1]=="STOP_GAINED"){if($matches_out[3]=="NONSENSE"){$thousandgenoqnonsense++;}} 
                 }
     
        
		if (preg_match("/InbreedingCoeff=(.*?);/", $genoqresults, $matches_out))
		{
				if($matches_out[1] > 0.3) {$thousandgenoqinbreadingcoefficient++;}           
		}
      			 
		if($fields[10]=="L"){$thousandgenoqfunclow++;} 
			else if($fields[10]=="M"){$thousandgenoqfuncmoderate++;}
			
                if (preg_match("/AF=(.*?);/", $genoqresults, $matches_out4)){
				array_push($thousandcommon,$matches_out4[1]);				
                 }  
			if(is_numeric($fields[9])){ array_push($thousandaf,$fields[9]);}	
                        if($fields[11]/$fields[10] < 2){$thousandgenoqhethom++;} 
	 }
	   
	   
	  if (preg_match("/EFF=(.*?)\((.*?)\|(.*?)\|/", $genoqresults, $matches_out2))
          {
              if($matches_out2[1]=="UPSTREAM") { $upstream++;$upstreamids .= $fields[1];
			$upstreamids .= ",";} 
              else if($matches_out2[1]=="DOWNSTREAM"){ $downstream++;$downsteamdids .= $fields[1];
			$downsteamdids .= ",";} 
              else if($matches_out2[1]=="NON_SYNONYMOUS_CODING"){ if($matches_out2[3]=="MISSENSE"){$missense++;}$nsn++;$nsnids .= $fields[1];
			$nsnids .= ",";}
              else if($matches_out2[1]=="SYNONYMOUS_CODING"){if($matches_out2[3]=="SILENT"){$silent++;} $syn++;$synids .= $fields[1];
			$synids .= ",";}
              else if($matches_out2[1]=="INTRON"){$intron++;$intronids .= $fields[1];
			$intronids .= ",";}
              else if($matches_out2[1]=="UTR_3_PRIME"){$utr3++;$utr3ids .= $fields[1];
			$utr3ids .= ",";} else if($matches_out2[1]=="UTR_5_PRIME"){$utr5++;$utr5ids .= $fields[1];
			$utr5ids .= ",";}
	      else if($matches_out2[1]=="STOP_GAINED"){if($matches_out2[3]=="NONSENSE"){$nonsense++;}}  
          }
     
        
        if (preg_match("/InbreedingCoeff=(.*?);/", $genoqresults, $matches_out3))
        {
           if($matches_out3[1] > 0.3) {
               $inbreadingcoefficient++;
           }           
        }

        $regex4 = "/AF=(.*?);/";
        if (preg_match($regex4, $genoqresults, $matches_out4)){array_push($allelefreqgenoq,$matches_out4[1]);}
   
      
        $fields=explode("\t",$genoqresults); 
        if($fields[10]=="L")
        {
            $funclow++;
        } else if($fields[10]=="M")
        {
           $funcmoderate++;
        }

        $het += $fields[11]; 
        $hom += $fields[12];       
        
        if($fields[11]/$fields[10] < 2.0) {$hethom++;}     
	   
	if(($rsid[0]=='r' && $rsid[1]=='s') && (is_numeric($fields[9])) )
	{
		   $intersection++;
	}	  
        
        #AC=524;AF=0.104633;AN=5008;NS=2504;DP=18917;EAS_AF=0.0377;AMR_AF=0.062;AFR_AF=0.2716;EUR_AF=0.0358;SAS_AF=0.0491;AA=.|||;VT=SNP 
        $popfrequency=$fields[14];
                
        //echo($fields[14]."<br>");
        $regexeasfreq = "/EAS_AF=(.*?);/";
        $regexamrfreq = "/AMR_AF=(.*?);/";
        $regexafrfreq = "/AFR_AF=(.*?);/";
        $regexeurfreq = "/EUR_AF=(.*?);/";
        $regexsasfreq = "/SAS_AF=(.*?);/";

     
        if (preg_match($regexeasfreq, $popfrequency, $matches_results))
        {
           #echo($matches_results[1]."<br>");
           array_push($alleleeasfreq,$matches_results[1]);
        }

        if (preg_match($regexamrfreq, $popfrequency, $matches_results))
        {
           #echo($matches_results[1]."<br>");
           array_push($alleleamrfreq,$matches_results[1]);
        }
          
        if (preg_match($regexafrfreq, $popfrequency, $matches_results))
        {
           #echo($matches_results[1]."<br>");
           array_push($alleleafrfreq,$matches_results[1]);
        }
          
        if (preg_match($regexeurfreq, $popfrequency, $matches_results))
        {
           #echo($matches_results[1]."<br>");
           array_push($alleleeurfreq,$matches_results[1]);
        }

        if (preg_match($regexsasfreq, $popfrequency, $matches_results))
        {
           #echo($matches_results[1]."<br>");
           array_push($allelesasfreq,$matches_results[1]);
        }    
	          
       }  
    }
}
    
fclose($file_handle);
    

    /*$jobdir="/home/metabolomics/snipa/web/snipa";
    $Rplotcmd="Rscript --vanilla  ".$jobdir."/RCode/piechart.R ".$downstream." ".$intron." ".$nsn." ".$syn." ".$upstream." ".$utr3." ".$utr5;
    exec($Rplotcmd);*/
    
    $Rcodedir="/home/metabolomics/snipa/web/snipa";
    $Rplotcmd="Rscript --vanilla  ".$Rcodedir."/RCode/pie.R ".$downstream." ".$intron." ".$nsn." ".$syn." ".$upstream." ".$utr3." ".$utr5." ".$JobDir;
    //print($Rplotcmd);
    exec("sudo ".$Rplotcmd);
    
    $cmd="tabix /home/metabolomics/snipa/data/genoq/1000G/1000G_records_sorted.ann.file.vcf.gz ".$chr.":".$start."-".$stop;
    //$filename = "/home/metabolomics/snipa/web/snipa/tmp/1000G.txt";
    $filename = $JobDir."/1000G.txt";
    exec($cmd." >".$filename);
    
    $thousandtotalsnps=0;
    $thousandarrlength = count($thousandresults);
    $thousandtotalsnps =count($thousandresults);
   
    $thousanddownstream=0;$thousandupstream=0;$thousandsnpcount=0;$thousandintron=0;$thousandnsc=0;$thousandsc=0;
    $thousandupstream=0;$thousandutr3=0;$thousandutr5=0;$thousandinbreadingcoefficient=0;$thousandhethom=0;
    $thousandmoderate=0;$thousandlow=0;$thousandthousand=0;$thousandmissense=0;$thousandnone=0;$thousandsilent=0;
    $thousanddbsnp=0;$thousandmis=0;$thousandnsn=0;$thousandsyn=0;$commondbsnpthousand=0;$thousandnonsense=0;
    
    $thousandallelefreq=array();
    
    
    $file_handle = fopen($filename, "r");
    if ($file_handle) {
    while (!feof($file_handle)) {
	   
       $thousandresults = fgets($file_handle,9096);
        
      if($thousandresults != "")
     {
      $thousandtotalsnps++;
      if (preg_match("/EFF=(.*?)\((.*?)\|(.*?)\|/", $thousandresults, $matches_out2))
        {  
           if($matches_out2[1]=="UPSTREAM"){ $thousandupstream++;} 
           else if($matches_out2[1]=="DOWNSTREAM"){$thousanddownstream++;}
           else if($matches_out2[1]=="INTRON"){$thousandintron++;}
           else if($matches_out2[1]=="UTR_3_PRIME"){$thousandutr3++;} 
           else if($matches_out2[1]=="UTR_5_PRIME"){$thousandutr5++;}
           else if($matches_out2[1]=="NON_SYNONYMOUS_CODING") {$thousandnsn++;if($matches_out2[3]=="MISSENSE"){$thousandmissense++;}} 
           else if($matches_out2[1]=="SYNONYMOUS_CODING"){$thousandsyn++;if($matches_out2[3]=="SILENT"){$thousandsilent++;}}
           else if($matches_out2[1]=="STOP_GAINED"){if($matches_out2[3]=="NONSENSE"){$thousandnonsense++;}} 
        }
     
  

        $regex4 = "/AF=(.*?);/";
        if (preg_match($regex4, $thousandresults, $matches_out4))
        {
            array_push($thousandallelefreq,$matches_out4[1]);
        }
   
      
        $fields=explode("\t",$thousandresults); 
        if($fields[10]=="L")
        {
            $thousandlow++;
        } else if($fields[10]=="M")
        {
           $thousandmoderate++;
        }

       $fields=explode("\t",$thousandresults); 
       $rsid= $fields[4];
       
       if(is_numeric($fields[8]))  {$thousanddbsnp++;}
      }
    }
}
    fclose($file_handle);
    
    
    
    $dbsnpcmd="tabix /home/metabolomics/snipa/data/genoq/dbsnp/dbSNP.ann.file.gz ".$chr.":".$start."-".$stop;
    //$filename = "/home/metabolomics/snipa/web/snipa/tmp/dbsnp.txt";
    $filename=$JobDir."/dbsnp.txt";    
    exec($dbsnpcmd." >".$filename);
    
    

   
    $dbsnparrlength=0;
 

    $dbsnpdownstream=0;$dbsnpupstream=0;$dbsnpsnpcount=0;$dbsnpintron=0;$dbsnpnsc=0;$dbsnpsc=0;$dbsnpupstream=0;$dbsnputr3=0;
    $dbsnputr5=0;$dbsnpinbreadingcoefficient=0;$dbsnphethom=0;$dbsnpmoderate=0;$dbsnplow=0;$dbsnpthousand=0;$dbsnpmissense=0;
    $dbsnpnone=0;$dbsnpsilent=0;$dbsnpdbsnp=0;$dbsnpmis=0;$dbsnpnsn=0;$dbsnpsyn=0; $dbsnpnonsense=0;
    
    
    $file_handle = fopen($filename, "r");
    if ($file_handle) {
    while (!feof($file_handle)) {
	   
       $dbsnpresults = fgets($file_handle,4096);
       
       if($dbsnpresults != "") {
       $dbsnparrlength++;
       if (preg_match("/EFF=(.*?)\((.*?)\|(.*?)\|/", $dbsnpresults, $matches_out2))
        {  
           if($matches_out2[1]=="UPSTREAM"){ $dbsnpupstream++;} 
           else if($matches_out2[1]=="DOWNSTREAM"){$dbsnpdownstream++;}
           else if($matches_out2[1]=="INTRON"){$dbsnpintron++;}
           else if($matches_out2[1]=="UTR_3_PRIME"){$dbsnputr3++;} 
           else if($matches_out2[1]=="UTR_5_PRIME"){$dbsnputr5++;}
           else if($matches_out2[1]=="NON_SYNONYMOUS_CODING") {$dbsnpnsn++;if($matches_out2[3]=="MISSENSE"){$dbsnpmissense++;}} 
           else if($matches_out2[1]=="SYNONYMOUS_CODING"){$dbsnpsyn++;if($matches_out2[3]=="SILENT"){$dbsnpsilent++;}}
           else if($matches_out2[1]=="STOP_GAINED"){if($matches_out2[3]=="NONSENSE"){$dbsnpnonsense++;}} 
           
        }
        $fields=explode("\t",$dbsnpresults); 
        if($fields[14]=="L")
        {
            $dbsnplow++;
        } else if($fields[14]=="M")
        {
           $dbsnpmoderate++;
        }
      } 
    }
}
    fclose($file_handle);
    

    //print_r($allelefreqgenoq);
    
    //print_r($alleleamrfreq);
    //print_r($alleleafrfreq);
    //print_r($alleleeasfreq);
    //print_r($allelesasfreq);
    //print_r($alleleeurfreq); 
       
    $allemapgenoq=genbindata($allelefreqgenoq);
    $allemapthousnadgenoq=genbindata($thousandcommon);
    $allelemapthousand=genbindata($thousandallelefreq);
    $allemapamr=genbindata($alleleamrfreq);
    $allemapafr=genbindata($alleleafrfreq);
    $allemapeas=genbindata($alleleeasfreq);
    $allemapsas=genbindata($allelesasfreq);
    $allemapeur=genbindata($alleleeurfreq);
 
    
    //$file = fopen("/home/metabolomics/snipa/web/snipa/tmp/Allefreq.txt","w");
    $file = fopen($JobDir."/Allefreq.txt","w");
    for ($i=0; $i<count($allemapgenoq);$i++)
    {
         fwrite($file,$allemapgenoq[$i]."\t".$allemapthousnadgenoq[$i]."\t".$allelemapthousand[$i]."\t".$allemapamr[$i]."\t".$allemapafr[$i]."\t".$allemapeas[$i]."\t".$allemapsas[$i]."\t".$allemapeur[$i]."\n");
    }
    fclose($file);
    
    
    $Rcmd="Rscript --vanilla  ".$Rcodedir."/RCode/VennDiagram.R ".$totalsnps." ".$thousandtotalsnps." ".$dbsnparrlength." ".$thousandcommoncount."  ".$thousanddbsnp." ".$dbsnpcommoncount." ".$intersection." ".$JobDir;
    exec($Rcmd);
    //echo ($Rcmd);
    $Rbarplotcmd="Rscript --vanilla  ".$Rcodedir."/RCode/barplot.R ".$JobDir;
    exec($Rbarplotcmd);
   
 

  // if($rsflag==0) { 
?>





<div id="vartabs">
      <ul>
        <li><a href="#tabs-1" style="font-family: Arial; font-size: 10pt;">Summary</a></li>
        <li><a href="#tabs-2" style="font-family: Arial; font-size: 10pt;">Variant Table</a></li>
        <li><a href="#tabs-3" style="font-family: Arial; font-size: 10pt;">Frequency Plot</a></li>
      </ul>
      
      <div id="tabs-1">
        <br>
        <table id="summary-table"  cellpadding="0" cellspacing="0" border="1" style="width:100%;">
         <tr bgcolor="silver"  valign="top">
         <th width="60" height="40"></th>
         <th width="60" height="40" ><font color="white">SNP Effect</font></th>
         <th width="50" height="40" ><font color="white">Qatar</font></th>
         <th width="60" height="40"><font color="white">1000 Genome</font></th>
         <th width="80" height="40"><font color="white">Common between Qatar & 1000 Genome</font></th>
         <th width="40" height="40"><font color="white">dbSNP</font></th>
         <th width="70" height="40"><font color="white">Common between dbSNP & GenoQ</font></th>
        </tr>
       <?php if($rsflag==0) { ?>

        <tr BGCOLOR="#F0F0F0">
        <th bgcolor="silver" ><a HREF="javascript::void()" class="showModal1"><font color="white" ><buttontext>Number of Snps</buttontext></font></a></th>
        <td></td>
        <td><?php echo $totalsnps?></td>
        <td><?php echo $thousandtotalsnps?></td>
        <td><?php echo $thousandcommoncount?></td>
        <td><?php echo $dbsnparrlength?></td>    
        <td><?php echo $dbsnpcommoncount?></td>
      </tr>
      <tr BGCOLOR="#e5e5e5">
       <th bgcolor="silver" rowspan="7" ><a HREF="javascript::void()" class="showModal2"><font color="white"><buttontext>Region Based Statistics</buttontext></font></th>
       <td>Downstream</td>
       <td id="td-containing-ids" data-ids="<?php echo $downsteamdids ?>" ><clickabletext><a href="#tabs-2" id="movetovtable1"><font color="black"><?php echo $downstream ?></font></a></clickabletext></td>
       <td><?php echo $thousanddownstream?></td>
       <td><?php echo $thousandgenoqdownstream?></td>
       <td><?php echo $dbsnpdownstream?></td>    
       <td><?php echo $dbsnpgenoqdownstream?></td>
     </tr>
     <tr BGCOLOR="#e5e5e5">
     <td>Intron</td>  
    <td id="td-containing-ids" data-ids="<?php echo $intronids ?>"><clickabletext><a href="#tabs-2" id="movetovtable2"><font color="black"><?php echo $intron?></font></a></clickabletext></td>
    <td><?php echo $thousandintron?></td>
    <td><?php echo $thousandgenoqintron?></td>
    <td><?php echo $dbsnpintron?></td>    
    <td><?php echo $dbsnpgenoqintron?></td>
  </tr>
  <tr BGCOLOR="#e5e5e5">
    <td>Non Syno Coding</td>
    <td id="td-containing-ids" data-ids="<?php echo $nsnids ?>"><clickabletext><a href="#tabs-2" id="movetovtable3"><font color="black"><?php echo $nsn?></font></a></clickabletext></font></td>
    <td><?php echo $thousandnsn?></td>
    <td><?php echo $thousandgenoqnsn?></td>
    <td><?php echo $dbsnpnsn?></td>    
    <td><?php echo $dbsnpgenoqnsn?></td>
  </tr>
   <tr BGCOLOR="#e5e5e5">
    <td >Synonymous Coding</td>    
    <td id="td-containing-ids" data-ids="<?php echo $synids ?>"><clickabletext><a href="#tabs-2" id="movetovtable4"><font color="black"><?php echo $syn?></font></a></clickabletext><font></td>
    <td><?php echo $thousandsyn?></td>
    <td><?php echo $thousandgenoqsyn?></td>
    <td><?php echo $dbsnpsyn?></td>    
    <td><?php echo $dbsnpgenoqsyn?></td>
  </tr>
   <tr BGCOLOR="#e5e5e5">
    <td  >Upstream</td>
    
    <td id="td-containing-ids" data-ids="<?php echo $upstreamids ?>"><clickabletext><a href="#tabs-2" id="movetovtable5"><font color="black"><?php echo $upstream?></a></clickabletext></font></td>
    <td><?php echo $thousandupstream?></td>
    <td><?php echo $thousandgenoqupstream?></td>
    <td><?php echo $dbsnpupstream?></td>    
    <td><?php echo $dbsnpgenoqupstream?></td>
  </tr>
   <tr BGCOLOR="#e5e5e5">
    <td  >UTR 3 Prime</td>
   
    <td id="td-containing-ids" data-ids="<?php echo $utr3ids ?>"><clickabletext><a href="#tabs-2" id="movetovtable6"><font color="black"><?php echo $utr3?></font></a></clickabletext></font></td>
    <td><?php echo $thousandutr3?></td>
    <td><?php echo $thousandgenoqutr3?></td>
    <td><?php echo $dbsnputr3?></td>    
    <td><?php echo $dbsnpgenoqutr3?></td>
  </tr>
   <tr BGCOLOR="#e5e5e5">
    <td >UTR 5 Prime</td>
    <td id="td-containing-ids" data-ids="<?php echo $utr5ids ?>"><clickabletext><a href="#tabs-2" id="movetovtable7"><font color="black"><?php echo $utr5?></font></a></clickabletext></font></td>
    <td><?php echo $thousandutr5?></td>
    <td><?php echo $thousandgenoqutr5?></td>
    <td><?php echo $dbsnputr5?></td>    
    <td><?php echo $dbsnpgenoqutr5?></td>
  </tr>
  <tr BGCOLOR="#F0F0F0">
    <th bgcolor="silver" rowspan="2" ><font color="white">Consanguinity</font></th>
    <td>Inbreeding Coeff.>0.3</td>
    <td><?php echo $inbreadingcoefficient ?></td>
    <td>NA</td>
    <td><?php echo $thousandgenoqinbreadingcoefficient ?></td>
    <td>NA</td>    
    <td><?php echo $dbsnpgenoqinbreadingcoefficient ?></td>
  </tr>
  <tr BGCOLOR="#F0F0F0">
    <td>Het/Hom < 2</td>
	<td><?php echo /*$hethom.*/"(het:".$het.", hom: ".$hom.", ratio < 2(count): ",$hethom,")" ?></td>
    <td>NA</td>
    <td><?php echo $thousandgenoqhethom ?></td>
    <td>NA</td>    
    <td><?php echo $dbsnpgenoqhethom ?></td>    
  </tr>
  <tr BGCOLOR="e5e5e5">
    <th bgcolor="silver" rowspan="2"><font color="white">Function Impact</font></th>
    <td>Low</td>
    <td><?php echo $funclow ?></td>
    <td><?php echo $thousandlow ?></td>
    <td><?php echo $thousandgenoqfunclow ?></td>
    <td><?php echo $dbsnplow ?></td>    
    <td><?php echo $dbsnpgenoqfunclow ?></td>
  </tr>
  
   <tr BGCOLOR="e5e5e5">
    <td>Moderate</td>
    <td><?php echo $funcmoderate ?></td>
    <td><?php echo $thousandmoderate ?></td>
    <td><?php echo $thousandgenoqfuncmoderate ?></td>
    <td><?php echo $dbsnpmoderate ?></td>    
    <td><?php echo $dbsnpgenoqfuncmoderate ?></td>   
   </tr>

   <tr BGCOLOR="#F0F0F0">
    <th bgcolor="silver" rowspan="3" ><font color="white">Function Class</font></th>
    <td>Missense </td>
    <td><?php echo $missense?></td>
    <td><?php echo $thousandmissense?></td>
    <td><?php echo $thousandgenoqmissense?></td>
    <td><?php echo $dbsnpmissense?></td>    
    <td><?php echo $dbsnpgenoqmissense?></td>
  </tr>
  
  <tr BGCOLOR="#F0F0F0">
    <td>Nonsense</td>
    <td><?php echo $nonsense?></td>
    <td><?php echo $thousandnonsense?></td>
    <td><?php echo $thousandgenoqnonsense?></td>
    <td><?php echo $dbsnpnonsense?></td>    
    <td><?php echo $dbsnpgenoqnonsense?></td>    
  </tr>

  <tr BGCOLOR="#F0F0F0">
    <td>Silent</td>
    <td><?php echo $silent?></td>
    <td><?php echo $thousandsilent?></td>
    <td><?php echo $thousandgenoqsilent?></td>
    <td><?php echo $dbsnpsilent?></td>    
    <td><?php echo $dbsnpgenoqsilent?></td>    
  </tr>
<?php } ?>

</table><hr>


      </div>
      <div id="tabs-2">
      <br>
 <?php 
    
    echo "<table class=hoverTable border=1 style='width: 880px;' cellpadding=4 cellspacing=4>
         <tr><th width=55.88 >Chr</th><th width=87.98 >Pos</th><th width=91.8  >Rsid</th><th width=106.1 >
         Qatar Genome (Allele Freq)</th><th width=111.93 >1000 Genome (Allele Freq)</th><th width=72.79 >Inbreeding Coeff.</th><th width=195.67 >
         SNP Effect</th><th width=81.9>Function Class</th><th width=101.73 >Functional Impact</th></tr></table>";
              
    echo "<table id=dataTable style='overflow: auto;height: 10px; width: 880px;' border=0 cellpadding=4 cellspacing=0 >";    
    //$filename = "/home/metabolomics/snipa/web/snipa/tmp/qatar.txt";
    $filename=$JobDir."/qatar.txt";
    $file_handle = fopen($filename, "r");
    if ($file_handle) {
    while (!feof($file_handle)) {
		
    $genoqresults = fgets($file_handle,9096);
    
     if($genoqresults != "") 
     { 		
        $genoqrecords=explode("\t",$genoqresults); 
        $allefreq="";$inbcoeff="";
        $functionclass="";
        $snpeffect="None";
        $functionimpact="";
        if($genoqrecords[10]=="L")
        {
		   $functionimpact="Low";
        } else  if($genoqrecords[10]=="M")
        {
		   $functionimpact="Moderate";
        } else {
		   $functionimpact=$genoqrecords[10];
        }
       
        if (preg_match("/AF=(.*?);/", $genoqresults, $matches_out4)){$allefreq=$matches_out4[1];}
        if (preg_match("/InbreedingCoeff=(.*?);/", $genoqresults, $matches_out)){$inbcoeff=$matches_out[1];}
        if (preg_match("/EFF=(.*?)\((.*?)\|(.*?)\|/", $genoqresults, $matches_out2))
        {  
            if($matches_out2[1]=="UPSTREAM"){ $snpeffect="Upstream";} 
            else if($matches_out2[1]=="DOWNSTREAM"){$snpeffect="Downstream";}
            else if($matches_out2[1]=="INTRON"){$snpeffect="Intron";}
            else if($matches_out2[1]=="UTR_3_PRIME"){$snpeffect="UTR3 Prime";} 
            else if($matches_out2[1]=="UTR_5_PRIME"){$snpeffect="UTR5 Prime";}
            else if($matches_out2[1]=="NON_SYNONYMOUS_CODING") {$snpeffect="Non Synonymous Coding";if($matches_out2[3]=="MISSENSE"){$functionclass="Missense";}} 
            else if($matches_out2[1]=="SYNONYMOUS_CODING"){$snpeffect="Synonymous Coding";if($matches_out2[3]=="SILENT"){$functionclass="Silent";}}
            else if($matches_out2[1]=="STOP_GAINED"){$snpeffect="Stop Gained";if($matches_out2[3]=="NONSENSE"){$functionclass="Nonsense";}}                   
         }           
			                                         
        $col1=$genoqrecords[0];
        
        echo "<tr><td width=55.88>$col1</td><td width=84.98>$genoqrecords[1]</td><td width =91.8>$genoqrecords[2]</td><td width=106.1>".round($allefreq,2)."</td><td width=111.93>".round($genoqrecords[9],2)."</td><td width=72.79>".round($inbcoeff,2)."</td><td width=195.67>$snpeffect</td><td width=81.9>$functionclass</td><td width=101.73>$functionimpact</td></tr>"; 
         
       }
   }
}
echo "</table><hr>";

?>
      <script src="frontend/js/rowfilter.js"></script>		   	
      </div>
     
      <div id="tabs-3">
        <br><br>
        <div id=includedContent style=width: '100px'; height: '50px'; margin: 0 auto></div>
      </div>
</div>


<!----convert below into class ---->
<script>
      $("#movetovtable1").click(function() {
        $("#vartabs").tabs("option", "active", 1);
      });
      
       $("#movetovtable2").click(function() {
        $("#vartabs").tabs("option", "active", 1);
      });
       $("#movetovtable3").click(function() {
        $("#vartabs").tabs("option", "active", 1);
      });
      
       $("#movetovtable4").click(function() {
        $("#vartabs").tabs("option", "active", 1);
      });
       $("#movetovtable5").click(function() {
        $("#vartabs").tabs("option", "active", 1);
      });
      
       $("#movetovtable6").click(function() {
        $("#vartabs").tabs("option", "active", 1);
      });
       $("#movetovtable7").click(function() {
        $("#vartabs").tabs("option", "active", 1);
      });

</script>


			
