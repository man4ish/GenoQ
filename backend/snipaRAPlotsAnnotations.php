<?php

require_once("../backend/snipaConfig.php");
require_once("../backend/snipaTabix.php");
require_once("../backend/snipaMapGenes.php");

$txt ="";

$snpname = $_REQUEST['snpname'];
$snpchr = $_REQUEST['snpchr'];
$snppos = $_REQUEST['snppos'];
$sentinelpos = $_REQUEST['sentinelpos'];
$genomerelease = $_REQUEST['genomerelease'];
$referenceset = $_REQUEST['referenceset'];
$population = $_REQUEST['population'];
$annotation = $_REQUEST['annotation'];

// Descriptions of scores etc
$phyloP = "[b]phyloP[/b] is a conservation score represented as [b]-log(P)[/b] of a test for [b]neutral evolution[/b] of a nucleotide.[br/][br/][b][u]Positive score[/u][/b][br/]The position is predicted to be rather [b]conserved[/b].[br/][br/][b][u]Negative score[/u][/b][br/]The position is predicted to be rather [b]fast-evolving[/b].";
$phastCons = "[b]phastCons[/b] is a conservation score represented by the probability (i.e., range is 0 to 1) for a nucleotide to belong to a [b]conserved element[/b].[br/][br/][b][u]High score (max. 1)[/u][/b][br/]The position is predicted to be rather [b]conserved[/b].[br/][br/][b][u]Low score (min. 0)[/u][/b][br/]The position is predicted to be rather [b]fast-evolving[/b].";
$gerp = "[b]GERP++[/b] is a conservation score quantified in terms of \"rejected substitutions\" per nucleotide, defined as number of substitutions [b]expected under neutrality[/b] minus number of substitutions observed.[br/][br/][b][u]Positive score[/u][/b][br/]The position shows a substitution deficit (it is [b]conserved[/b]).[br/][br/][b][u]Negative score[/u][/b][br/]The position shows a substitution surplus (it is [b]fast-evolving[/b]).";
$cadd = "[b]CADD[/b] (Combined Annotation Dependent Depletion) integrates multiple annotations into one metric by contrasting variants that survived natural selection with simulated mutations. The scaled C-scores given here range from 1 to 99.[br/][br/][b][u]Score interpretation[/u][/b][br/]A score &ge; 10 indicates that this is predicted to be one of the 10% most deleterious substitutions that you can do to the human genome, a score &ge; 20 indicates the 1% most deleterious and so on.";
$snpeff = "Effects are categorized by impact {High, Moderate, Low, Modifier}. This are pre-defined categories: [br/][br/]
[table]
	[tbody][tr] [th] Impact [/th][th] Meaning [/th] [th] Example [/th] [/tr] 
	[tr] [td] HIGH		[/td] [td] The variant is assumed to have high (disruptive) impact in the protein, probably causing protein truncation, loss of function or triggering nonsense mediated decay. [/td] [td] stop_gained, frameshift_variant [/td][/tr] 
	[tr] [td] MODERATE	[/td] [td] A non-disruptive variant that might change protein effectiveness. [/td] [td] missense_variant, inframe_deletion [/td][/tr] 
	[tr] [td] LOW		[/td] [td] Assumed to be mostly harmless or unlikely to change protein behavior. [/td] [td] synonymous_variant [/td][/tr] 
	[tr] [td] MODIFIER	[/td] [td] Usually non-coding variants or variants affecting non-coding genes, where predictions are difficult or there is no evidence of impact. [/td] [td] exon_variant, downstream_gene_variant [/td][/tr] 
[/tbody][/table]";
// End of descriptions

// Hilfsfunktion (print-Ersatz) für die Erstellung der Block-Annotation
function add($mixed_var){
	global $txt;
	/*
	* *********************************************
	* IMPORTANT !!!
	* *********************************************
	* REMOVE THIS STR_REPLACE AFTER UPDATE TO GRCH38 !!! 
	* */
	$mixed_var = str_replace("http://www.ensembl", "http://grch37.ensembl", $mixed_var);
	/*
	 * The following str_replace, on the other hand, is well-intended :-)
	 * */
	$mixed_var = str_replace("rowspan='1'", "", $mixed_var);
	$txt = $txt."\n".$mixed_var;
}

$annotationbasic = snipaGetSelfinfo($genomerelease,$referenceset,$population,$snpchr,$snppos,$snppos);
$annotationsentinel = snipaGetSelfinfo($genomerelease,$referenceset,$population,$snpchr,$sentinelpos,$sentinelpos);

$annotationldtmp = snipaGetProxies($genomerelease,$referenceset,$population,$snpchr,$sentinelpos);
foreach ($annotationldtmp as $row) { 
	if (($row['POS1'] == $sentinelpos) && ($row['POS2'] == $snppos)) {
		$annotationld = $row;
	}
}


$snpnamealias = $snpname;
if ($annotationbasic[0]['RSALIAS'] != "NA") {
	$snpnamealias .= " (alias ".implode(', ',explode(',',$annotationbasic[0]['RSALIAS'])).")";
}

$annotationfunc = snipaGetSNPAnnotations($genomerelease,$annotation,$snpchr,$snppos,$snppos);
$functional = unserialize($annotationfunc[0]['PHPARRAY']);

$maaf = 1-$annotationbasic[0]['MAF'];

/**
 * Basic Annotation
 */

add("<div class='annotation-section'>");
add("<h2 class='efftype'>SNP properties &ndash; <span style='font-weight: normal; font-size: smaller;'>Genome Assembly: ".$genomerelease.", Variant set: ".$referenceset.", Population: ".strtoupper($population)."</span></h2>");
if ($snppos-$sentinelpos != 0) {
	add(sprintf("<table class='annotation top'>
	<tr><th class='snpinf' colspan='6'>$snpnamealias</th></tr>
	<tr><th class='super' colspan='2'>position / outlink</th><th class='super' colspan='2'>allele info</th><th class='super' colspan='2'>sentinel data ({$annotationsentinel[0]['RSID']})</th></tr>
	<tr><th>physical position</th><td>chr$snpchr: ".number_format($snppos)."</td><th>alleles</th><td>{$annotationbasic[0]['MAJOR']}/{$annotationbasic[0]['MINOR']}</td><th>distance [bp]</th><td>".number_format($snppos-$sentinelpos)."</td></tr>
	<tr><th>genetic position [cM]</th><td>%.2f</td><th>frequencies</th><td>%.3f/%.3f</td><th>r<sup>2</sup></th><td>%.2f</td></tr>
	<tr><th>outlink</th><td><a href='http://www.ensembl.org/Homo_sapiens/Variation/Summary?v=$snpname' target='_blank'><img class='annotation-icon' src='frontend/img/ens.png' alt='EnsEMBL' title='$snpname @ EnsEMBL' /></a></td><th>non-reference allele</th><td>".(isset($functional['effect_allele']) ? $functional['effect_allele'] : "?")."</td><th>D'</th><td>%.2f</td></tr>
	</table><br/>",$annotationbasic[0]['CM'],$maaf,$annotationbasic[0]['MAF'],$annotationld['R2'],$annotationld['DPRIME']));
} else {
	add(sprintf("<table class='annotation top'>
	<tr><th class='snpinf' colspan='4'>$snpnamealias</th></tr>
	<tr><th class='super' colspan='2'>position / outlink</th><th class='super' colspan='2'>allele info</th></tr>
	<tr><th>physical position</th><td>chr$snpchr: ".number_format($snppos)."</td><th>alleles</th><td>{$annotationbasic[0]['MAJOR']}/{$annotationbasic[0]['MINOR']}</td></tr>
	<tr><th>genetic position [cM]</th><td>%.2f</td><th>frequencies</th><td>%.3f/%.3f</td></tr>
	<tr><th>outlink</th><td><a href='http://www.ensembl.org/Homo_sapiens/Variation/Summary?v=$snpname' target='_blank'><img class='annotation-icon' src='frontend/img/ens.png' alt='EnsEMBL' title='$snpname @ EnsEMBL' /></a></td><th>non-reference allele</th><td>".(isset($functional['effect_allele']) ? $functional['effect_allele'] : "?")."</td></tr>
	</table><br/>",$annotationbasic[0]['CM'],$maaf,$annotationbasic[0]['MAF']));
}

add("<table class='annotation'>
<tr><th class='duper' colspan='4'>Basic features</th></tr>
<tr><th class='super' colspan='2'>Conservation/deleteriousness</th><th class='super' colspan='2'>Linked genes</th></tr>
<tr><th>phyloP&nbsp;<a class='whatsthis' target='_blank' title='{$phyloP}' href='http://compgen.bscb.cornell.edu/phast/'></a></th><td>{$functional['score']['phyloP']}</td><th>gene(s) hit or close-by</th><td>");
if(array_key_exists('genes', $functional)){
	asort($functional['genes']);
	$tmp = array();
	foreach($functional['genes'] as $key => $value){
		array_push($tmp, "{$functional['genes'][$key]} <a href='http://www.ensembl.org/Homo_sapiens/Gene/Summary?db=core;g={$key}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$functional['genes'][$key]} @ EnsEMBL' /></a>");
	}
	add(join(", ", $tmp));
}else{
	add("&ndash;");
}
add("</td></tr>
<tr><th>phastCons&nbsp;<a class='whatsthis' target='_blank' title='{$phastCons}' href='http://compgen.bscb.cornell.edu/phast/'></a></th><td>{$functional['score']['phastCons']}</td><th>eQTL gene(s)</th><td>");
if(array_key_exists('eQTL-genes', $functional)){
	asort($functional['eQTL-genes']);
	$tmp = array();
	foreach($functional['eQTL-genes'] as $key => $value){
		array_push($tmp, "{$functional['eQTL-genes'][$key]} <a href='http://www.ensembl.org/Homo_sapiens/Gene/Summary?db=core;g={$key}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$functional['eQTL-genes'][$key]} @ EnsEMBL' /></a>");
	}
	add(join(", ", $tmp));
}else{
	add("&ndash;");
}
add("</td></tr>
<tr><th>GERP++&nbsp;<a class='whatsthis' target='_blank' title='{$gerp}' href='http://mendel.stanford.edu/SidowLab/downloads/gerp/'></a></th><td>{$functional['score']['GERP++']}</td><th>potentially regulated gene(s)</th><td>");
if(array_key_exists('reg_genes', $functional)){
	asort($functional['reg_genes']);
	$tmp = array();
	foreach($functional['reg_genes'] as $key => $value){
		array_push($tmp, "{$functional['reg_genes'][$key]} <a href='http://www.ensembl.org/Homo_sapiens/Gene/Summary?db=core;g={$key}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$functional['reg_genes'][$key]} @ EnsEMBL' /></a>");
	}
	add(join(", ", $tmp));
}else{
	add("&ndash;");
}
add("</td></tr>
<tr><th>CADD score&nbsp;<a class='whatsthis' target='_blank' title='{$cadd}' href='http://cadd.gs.washington.edu/'></a></th><td>{$functional['score']['CADD']}</td><th>disease gene(s)</th><td>");
if(array_key_exists('gene_associations', $functional)){
	$tmp = array();
	foreach($functional['gene_associations'] as $source => $genes){
		foreach($genes as $key => $value){
			foreach($value as $trait => $info){
				$tmp["{$info['gene_symbol']} <a href='http://www.ensembl.org/Homo_sapiens/Gene/Summary?db=core;g={$key}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$info['gene_symbol']} @ EnsEMBL' /></a>"] = 1;
			}
		}
	}
	add(join(", ", array_keys($tmp)));
}else{
	add("&ndash;");
}
add("</td></tr>
<tr><th>SnpEff effect impact&nbsp;<a class='whatsthis' target='_blank' title='{$snpeff}' href='http://snpeff.sourceforge.net/SnpEff_manual.html'></a></th><td>{$functional['impact']}</td><th>&nbsp;</th><td>&nbsp;</td></tr>
</table><br/>");

add("</div>");
	
/**
 * Disease Annotations
 */
if(array_key_exists('gene_associations', $functional) || array_key_exists('variant_association', $functional)){
	add("<div class='annotation-section'>");
	add("<h2 class='efftype'>Trait annotations</h2>");
		
	if(array_key_exists('variant_association', $functional)){

		// associations
		if(array_key_exists('gwascatalog_variants', $functional['variant_association']) || array_key_exists('metabolomics_variants', $functional['variant_association']) || array_key_exists('dbgap_variants', $functional['variant_association'])){
			add("<table class='annotation'>
			<tr><th class='duper' colspan='5'>Variant association</th></tr>
			<tr><th class='super'>trait</th><th class='super'>p-value</th><th class='super'>source DB</th><th class='super' colspan='2'>source entry/link</th></tr>");
		
		
			// GWAS Catalog
			if(array_key_exists('gwascatalog_variants', $functional['variant_association'])){
				$temp = $functional['variant_association']['gwascatalog_variants'];
				krsort($temp);
				foreach($temp as $key => $value){
					foreach($value as $trait => $tarr){
						add(sprintf("<tr><td>$trait</td><td>&lt;".preg_replace('/([\d\.]+)e\-(\d+)/','\1&times;10<sup>-%d</sup>',$tarr['P-value'])."</td><td><a class='web' href='{$tarr['source_link']}' target='_blank'>{$tarr['source']}</a></td><td>$key</td><td><a href='{$tarr['link']}' target='_blank'><img src='frontend/img/pm.png' alt='PubMed' title='view in PubMed' /></a></td></tr>", preg_replace('/([\d\.]+)e\-(\d+)/','\2',$tarr['P-value'])));
					}
				}
			}
			
			// Metabolomics GWAS Server
			if(array_key_exists('metabolomics_variants', $functional['variant_association'])){
				$temp = $functional['variant_association']['metabolomics_variants'];
				krsort($temp);
				foreach($temp as $key => $value){
					foreach($value as $trait => $tarr){
						add(sprintf("<tr><td>$trait</td><td>".preg_replace('/([\d\.]+)e\-(\d+)/','\1&times;10<sup>-%d</sup>',$tarr['P-value'])."</td><td><a class='web' href='{$tarr['source_link']}' target='_blank'>{$tarr['source']}</a></td><td>$key</td><td><a href='{$tarr['link']}' target='_blank'><img src='frontend/img/pm.png' alt='PubMed' title='view in PubMed' /></a></td></tr>", preg_replace('/(<?[\d\.]+)e\-(\d+)/','\2',$tarr['P-value'])));
					}
				}
			}
			
			// dbGaP
			if(array_key_exists('dbgap_variants', $functional['variant_association'])){
				$temp = $functional['variant_association']['dbgap_variants'];
				krsort($temp);
				foreach($temp as $key => $value){
					foreach($value as $trait => $tarr){
						add(sprintf("<tr><td>$trait</td><td>".preg_replace('/([\d\.]+)e\-(\d+)/','\1&times;10<sup>-%d</sup>',$tarr['P-value'])."</td><td><a class='web' href='{$tarr['source_link']}' target='_blank'>{$tarr['source']}</a></td><td>$key</td><td><a href='{$tarr['link']}' target='_blank'><img src='frontend/img/dbgap.png' alt='dbGaP' title='view in dbGaP' /></a></td></tr>", preg_replace('/([\d\.]+)e\-(\d+)/','\2',$tarr['P-value'])));
					}
				}
			}
			add("</table><br/>");
		} // end associations
		
		// annotations
		if(array_key_exists('clinvar_variants', $functional['variant_association']) || array_key_exists('omim_variants', $functional['variant_association']) || array_key_exists('hgmd_variants', $functional['variant_association']) || array_key_exists('drugbank_fx_variants', $functional['variant_association']) || array_key_exists('drugbank_adr_variants', $functional['variant_association']) || array_key_exists('uniprot_variants', $functional['variant_association'])){
			add("<table class='annotation'>
			<tr><th class='duper' colspan='5'>Variant annotation</th></tr>
			<tr><th class='super'>trait</th><th class='super'>type</th><th class='super'>source DB</th><th class='super' colspan='2'>source entry/link</th></tr>");
		
			// ClinVar
			if(array_key_exists('clinvar_variants', $functional['variant_association'])){
				$temp = $functional['variant_association']['clinvar_variants'];
				krsort($temp);
				foreach($temp as $key => $value){
					foreach($value as $trait => $tarr){
						add("<tr><td>$trait</td><td>{$tarr['Annotated_as']}</td><td><a class='web' href='{$tarr['source_link']}' target='_blank'>{$tarr['source']}</a></td><td>$key</td><td><a href='{$tarr['link']}' target='_blank'><img src='frontend/img/clinvar.png' alt='ClinVar' title='view in ClinVar' /></a></td></tr>");
					}
				}
			}
			// HGMD
			if(array_key_exists('hgmd_variants', $functional['variant_association'])){
				$temp = $functional['variant_association']['hgmd_variants'];
				krsort($temp);
				foreach($temp as $key => $value){
					foreach($value as $trait => $tarr){
						add("<tr><td>$trait&nbsp;<a class='whatsthis' target='_blank' title='Only visible to registered users @ HGMD public' href='#'></a></td><td>{$tarr['type']}</td><td><a class='web' href='{$tarr['source_link']}' target='_blank'>{$tarr['source']}</a></td><td>$key</td><td><a href='{$tarr['link']}' target='_blank'><img src='frontend/img/hgmd.png' alt='HGMD' title='view in HGMD public' /></a></td></tr>");
					}
				}
			}
			// OMIM
			if(array_key_exists('omim_variants', $functional['variant_association'])){
				$temp = $functional['variant_association']['omim_variants'];
				krsort($temp);
				foreach($temp as $key => $value){
					foreach($value as $trait => $tarr){
						add("<tr><td>$trait</td><td>{$tarr['type']}</td><td><a class='web' href='{$tarr['source_link']}' target='_blank'>{$tarr['source']}</a></td><td>$key</td><td><a href='{$tarr['link']}' target='_blank'><img src='frontend/img/omim.png' alt='OMIM' title='view in OMIM' /></a></td></tr>");
					}
				}
			}
			// UniProt
			if(array_key_exists('uniprot_variants', $functional['variant_association'])){
				$temp = $functional['variant_association']['uniprot_variants'];
				krsort($temp);
				foreach($temp as $key => $value){
					foreach($value as $trait => $tarr){
						if(preg_match("/^MIM/",$tarr['external_id'])){
							add("<tr><td>$trait</td><td>{$tarr['type']}</td><td><a class='web' href='{$tarr['source_link']}' target='_blank'>{$tarr['source']}</a></td><td>$key</td><td><a href='{$tarr['link']}' target='_blank'><img src='frontend/img/omim.png' alt='OMIM' title='view in OMIM' /></a></td></tr>");
						}else{
							add("<tr><td>$trait</td><td>{$tarr['type']}</td><td><a class='web' href='{$tarr['source_link']}' target='_blank'>{$tarr['source']}</a></td><td>$key</td><td><a href='{$tarr['link']}' target='_blank'><img src='frontend/img/uniprot.png' alt='UniProt' title='view in UniProt' /></a></td></tr>");
						}
					}
				}
			}
			// DrugBank FX
			if(array_key_exists('drugbank_fx_variants', $functional['variant_association'])){
				$temp = $functional['variant_association']['drugbank_fx_variants'];
				krsort($temp);
				foreach($temp as $key => $value){
					foreach($value as $trait => $tarr){
						add("<tr><td>$trait</td><td>{$tarr['type']}</td><td><a class='web' href='{$tarr['source_link']}' target='_blank'>{$tarr['source']}</a></td><td>$key</td><td><a href='{$tarr['link']}' target='_blank'><img src='frontend/img/pm.png' alt='PubMed' title='view in PubMed' /></a></td></tr>");
					}
				}
			}
			// DrugBank ADR
			if(array_key_exists('drugbank_adr_variants', $functional['variant_association'])){
				$temp = $functional['variant_association']['drugbank_adr_variants'];
				krsort($temp);
				foreach($temp as $key => $value){
					foreach($value as $trait => $tarr){
						add("<tr><td>$trait</td><td>{$tarr['type']}</td><td><a class='web' href='{$tarr['source_link']}' target='_blank'>{$tarr['source']}</a></td><td>$key</td><td><a href='{$tarr['link']}' target='_blank'><img src='frontend/img/pm.png' alt='PubMed' title='view in PubMed' /></a></td></tr>");
					}
				}
			}
			
			add("</table><br/>");
		} // end annotations
	}
	
	// GENES
	if(array_key_exists('gene_associations', $functional)){
		add("<table class='annotation'>
		<tr><th class='duper' colspan='5'>Disease gene annotation</th></tr>
		<tr><th class='super'>gene</th><th class='super'>trait</th><th class='super'>source DB</th><th class='super' colspan='2'>source entry/link</th></tr>");
		foreach($functional['gene_associations'] as $source => $genes){
			foreach($genes as $key => $value){
				foreach($value as $trait => $info){
					$tr = str_replace('@','',$trait);
					if(preg_match("/^MIM/",$info['external_id'])){
						add("<tr><td>{$info['gene_symbol']} <a href='http://www.ensembl.org/Homo_sapiens/Gene/Summary?db=core;g={$key}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$info['gene_symbol']} @ EnsEMBL' /></a></td><td>$tr</td><td><a class='web' href='{$info['source_link']}' target='_blank'>{$info['source']}</a></td><td>{$info['external_id']}</td><td><a href='{$info['link']}' target='_blank'><img src='frontend/img/omim.png' alt='OMIM' title='view in OMIM' /></a></td></tr>");
					}else{
						add("<tr><td>{$info['gene_symbol']} <a href='http://www.ensembl.org/Homo_sapiens/Gene/Summary?db=core;g={$key}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$info['gene_symbol']} @ EnsEMBL' /></a></td><td>$tr</td><td><a class='web' href='{$info['source_link']}' target='_blank'>{$info['source']}</a></td><td>{$info['external_id']}</td><td><a href='{$info['link']}' target='_blank'><img src='frontend/img/".strtolower($info['source']).".png' alt='{$info['source']}' title='view in {$info['source']}' /></a></td></tr>");
					}
				}
			}
		}
		add("</table><br/>");
	}
	
	add("</div>");
}

/**
 * Functional Annotation
 */

// function for retrieval of sub-arrays
function getEffectArray($array, $effectType, $pattern){
	$result = array();
	foreach($array[$effectType] as $key => $value){
		if(preg_match($pattern, $value['effect'])){
			$result[$effectType][$key] = $value;
		}
	}
	if(empty($result)){
		return false;
	}
	return $result;
}

// function for retrieval of direct TS sub-arrays
function getDTSEffectArray($array){
	$effectType = 'direct_transcript_effect';
	$result = array();
	foreach($array[$effectType] as $key => $value){
		if(preg_match('/miRNA/', $value['effect'])){
			$result['miRNA'][$key] = $value;
		}else if(preg_match('/splice.+region/', $value['effect'])){
			$result['splice'][$key] = $value;
		}else{
			$result[$effectType][$key] = $value;
		}
	}
	if(empty($result)){
		return false;
	}
	return $result;
}

// direct transcript
if(array_key_exists('direct_transcript_effect', $functional)){
	uasort($functional['direct_transcript_effect'], 'sortByGene');
	// TODO: add mature mirna & splice site variants
	add("<div class='annotation-section'>");
	add("<h2 class='efftype'>Direct effect on transcript</h2>");
	if($subf = getDTSEffectArray($functional)){
		if(array_key_exists('direct_transcript_effect', $subf)){
			add("<table class='annotation'><tr><th class='duper' colspan='9'>Amino acid sequence alteration</th></tr>
	<tr><th class='super'>gene</th><th class='super'>effect type</th><th class='super'>affected transcript</th><th class='super'>RefSeq id</th><th class='super'>protein</th><th class='super'>amino acid</th><th class='super'>codon</th><th class='super'>SIFT prediction</th><th class='super'>PolyPhen prediction</th></tr>");
			foreach($subf['direct_transcript_effect'] as $key => $value){
				add("<tr>
				<td>{$value['gene_symbol']} <a href='http://www.ensembl.org/Homo_sapiens/Gene/Summary?db=core;g={$value['gene_id']}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$value['gene_symbol']} @ EnsEMBL' /></a></td>
				<td>{$value['effect']}</td>
				<td>{$key} <a href='http://www.ensembl.org/Homo_sapiens/Transcript/Summary?db=core;t={$key}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$key} @ EnsEMBL' /></a></td>
				<td>{$value['refseq']}</td>
				<td>{$value['ensp']}". (($value['ensp'] !== "?") ? " <a href='http://www.ensembl.org/Homo_sapiens/Transcript/ProteinSummary?db=core;t={$key}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$value['ensp']} @ EnsEMBL' /></a>" : "") . "</td>
				<td>{$value['amino']}</td>
				<td>{$value['codon']}</td>
				<td>{$value['sift']}</td>
				<td>{$value['polyphen']}</td></tr>");
			}
			add("</table><br/>");
		}
		if(array_key_exists('miRNA', $subf)){
			add("<table class='annotation'><tr><th class='duper' colspan='4'>Mature miRNA variant</th></tr>
	<tr><th class='super'>miRNA gene</th><th class='super'>effect</th><th class='super'>affected transcript</th><th class='super'>RefSeq id</th></tr>");
			foreach($subf['miRNA'] as $key => $value){
				add("<tr><td>{$value['gene_symbol']} <a href='http://www.ensembl.org/Homo_sapiens/Gene/Summary?db=core;g={$value['gene_id']}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$value['gene_symbol']} @ EnsEMBL' /></a></td><td>{$value['effect']}</td><td>{$key} <a href='http://www.ensembl.org/Homo_sapiens/Transcript/Summary?db=core;t={$key}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$key} @ EnsEMBL' /></a></td><td>{$value['refseq']}</td></tr>");
			}
			add("</table><br/>");
		}
		if(array_key_exists('splice', $subf)){
			add("<table class='annotation'><tr><th class='duper' colspan='9'>Amino acid sequence alteration (splice region)</th></tr>
	<tr><th class='super'>gene</th><th class='super'>effect type</th><th class='super'>affected transcript</th><th class='super'>RefSeq id</th><th class='super'>protein</th><th class='super'>amino acid</th><th class='super'>codon</th><th class='super'>SIFT prediction</th><th class='super'>PolyPhen prediction</th></tr>");
			foreach($subf['splice'] as $key => $value){
				$tmp = preg_split('/\s\(/', $value['effect']);
				add("<tr>
				<td>{$value['gene_symbol']} <a href='http://www.ensembl.org/Homo_sapiens/Gene/Summary?db=core;g={$value['gene_id']}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$value['gene_symbol']} @ EnsEMBL' /></a></td>
				<td>{$tmp[0]}</td>
				<td>{$key} <a href='http://www.ensembl.org/Homo_sapiens/Transcript/Summary?db=core;t={$key}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$key} @ EnsEMBL' /></a></td>
				<td>{$value['refseq']}</td>
				<td>{$value['ensp']}". (($value['ensp'] !== "?") ? " <a href='http://www.ensembl.org/Homo_sapiens/Transcript/ProteinSummary?db=core;t={$key}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$value['ensp']} @ EnsEMBL' /></a>" : "") . "</td>
				<td>{$value['amino']}</td>
				<td>{$value['codon']}</td>
				<td>{$value['sift']}</td>
				<td>{$value['polyphen']}</td></tr>");
			}
			add("</table><br/>");
		}
	}
	add("</div>");
}

// direct regulatory
if(array_key_exists('cis-eQTL', $functional) || array_key_exists('trans-eQTL', $functional)){
	add("<div class='annotation-section'>");
	add("<h2 class='efftype'>Direct effect on regulation</h2>");
	
	// cis-eQTL
	if(array_key_exists('cis-eQTL', $functional)){
		add("<table class='annotation'><tr><th class='duper' colspan='6'><i>cis</i>-eQTL</th></tr>
	<tr><th class='super'>gene</th><th class='super'>transcript</th><th class='super'>probe</th><th class='super'>tissue</th><th class='super'>statistic (type)</th><th class='super'>source</th></tr>");
		foreach($functional['cis-eQTL'] as $study => $starr){
						
			foreach($functional['cis-eQTL'][$study] as $key => $value){
				add("<tbody>");
				if($key==="link" || $key==="source"){
					continue;
				}
				$tsc = count($value['ProbeData']);
				$tic = count($value['ProbeStats']);
				$rsp = max($tsc, $tic);
				$probestring = "?";
				if($key != "?"){
					$probestring = $key." <a href='http://www.ensembl.org/Homo_sapiens/Location/Genome?fdb=funcgen;ftype=ProbeFeature;id=$key' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='$key @ EnsEMBL' /></a>";
				}
				if($tsc == 0){
					$tirsp = 1;
					$tik = array_keys($value['ProbeStats']);
					add(sprintf("<tr><td rowspan='$rsp'>?</td><td rowspan='$rsp'>?</td><td rowspan='$rsp'>$probestring</td><td rowspan='$tirsp'>{$tik[0]}</td><td rowspan='$tirsp'>".preg_replace('/([\d\.]+)e\-(\d+)/','\1&times;10<sup>-%d</sup>',$value['ProbeStats'][$tik[0]]['stat'])." ({$value['ProbeStats'][$tik[0]]['stattype']})</td><td rowspan=''$rsp'>{$functional['cis-eQTL'][$study]['source']} <a href='{$functional['cis-eQTL'][$study]['link']}' target='_blank'><img src='frontend/img/pm.png' alt='PubMed' title='view in PubMed' /></a></td></tr>", preg_replace('/<?([\d\.]+)e\-(\d+)/','\2',$value['ProbeStats'][$tik[0]]['stat'])));
					for($i=1; $i<$tic; $i++){
						add(sprintf("<td rowspan='$tirsp'>{$tik[$i]}</td><td rowspan='$tirsp'>".preg_replace('/([\d\.]+)e\-(\d+)/','\1&times;10<sup>-%d</sup>',$value['ProbeStats'][$tik[$i]]['stat'])." ({$value['ProbeStats'][$tik[$i]]['stattype']})</td>", preg_replace('/<?([\d\.]+)e\-(\d+)/','\2',$value['ProbeStats'][$tik[$i]]['stat'])));
					}
				}else{
					$tsrsp = 1;
					$tirsp = 1;
					$tik = array_keys($value['ProbeStats']);
					$tsk = array_keys($value['ProbeData']);
					for($i=0; ($i<$tic || $i<$tsc); $i++){
						add("<tr>");
						if($i<$tsc-1){
							add("<td rowspan='$tsrsp'>{$value['ProbeData'][$tsk[$i]]['gene_symbol']} <a href='http://www.ensembl.org/Homo_sapiens/Gene/Summary?db=core;g={$value['ProbeData'][$tsk[$i]]['gene_id']}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$value['ProbeData'][$tsk[$i]]['gene_symbol']} @ EnsEMBL' /></a></td><td rowspan='$tsrsp'>{$tsk[$i]} ".(($tsk[$i]=="?") ? "" : "<a href='http://www.ensembl.org/Homo_sapiens/Transcript/Summary?db=core;t={$tsk[$i]}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$tsk[$i]} @ EnsEMBL' /></a>")."</td>");
						}
						if($i==$tsc-1){
							add("<td rowspan='".($rsp-$i)."'>{$value['ProbeData'][$tsk[$i]]['gene_symbol']} <a href='http://www.ensembl.org/Homo_sapiens/Gene/Summary?db=core;g={$value['ProbeData'][$tsk[$i]]['gene_id']}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$value['ProbeData'][$tsk[$i]]['gene_symbol']} @ EnsEMBL' /></a></td><td rowspan='".($rsp-$i)."'>{$tsk[$i]} ".(($tsk[$i]=="?") ? "" : "<a href='http://www.ensembl.org/Homo_sapiens/Transcript/Summary?db=core;t={$tsk[$i]}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$tsk[$i]} @ EnsEMBL' /></a>")."</td>");
						}
						if($i==0){
							add("<td rowspan='$rsp'>$probestring</td>");
						}
						if($i<$tic-1){
							add(sprintf("<td rowspan='$tirsp'>{$tik[$i]}</td><td rowspan='$tirsp'>".preg_replace('/([\d\.]+)e\-(\d+)/','\1&times;10<sup>-%d</sup>',$value['ProbeStats'][$tik[$i]]['stat'])." ({$value['ProbeStats'][$tik[$i]]['stattype']})</td><td rowspan='".$tirsp."'>{$functional['cis-eQTL'][$study]['source']} <a href='{$functional['cis-eQTL'][$study]['link']}' target='_blank'><img src='frontend/img/pm.png' alt='PubMed' title='view in PubMed' /></a></td>", preg_replace('/<?([\d\.]+)e\-(\d+)/','\2',$value['ProbeStats'][$tik[$i]]['stat'])));
						}
						if($i==$tic-1){
							add(sprintf("<td rowspan='".($rsp-$i)."'>{$tik[$i]}</td><td rowspan='".($rsp-$i)."'>".preg_replace('/([\d\.]+)e\-(\d+)/','\1&times;10<sup>-%d</sup>',$value['ProbeStats'][$tik[$i]]['stat'])." ({$value['ProbeStats'][$tik[$i]]['stattype']})</td><td rowspan='".($rsp-$i)."'>{$functional['cis-eQTL'][$study]['source']} <a href='{$functional['cis-eQTL'][$study]['link']}' target='_blank'><img src='frontend/img/pm.png' alt='PubMed' title='view in PubMed' /></a></td>", preg_replace('/<?([\d\.]+)e\-(\d+)/','\2',$value['ProbeStats'][$tik[$i]]['stat'])));
						}
						add("</tr>");
					}
				}
				add("</tbody>");
			}
		}
		add("</table><br/>");
	}
	
	// trans-eQTL
	if(array_key_exists('trans-eQTL', $functional)){
		add("<table class='annotation'><tr><th class='duper' colspan='7'><i>trans</i>-eQTL</th></tr>
	<tr><th class='super'>gene</th><th class='super'>transcript</th><th class='super'>probe</th><th class='super'>chromosome</th><th class='super'>tissue</th><th class='super'>statistic (type)</th><th class='super'>source</th></tr>");
		foreach($functional['trans-eQTL'] as $study => $starr){
						
			foreach($functional['trans-eQTL'][$study] as $key => $value){
				add("<tbody>");
				if($key==="link" || $key==="source"){
					continue;
				}
				$tsc = count($value['ProbeData']);
				$tic = count($value['ProbeStats']);
				$rsp = max($tsc, $tic);
				$probestring = "?";
				if($key != "?"){
					$probestring = $key." <a href='http://www.ensembl.org/Homo_sapiens/Location/Genome?fdb=funcgen;ftype=ProbeFeature;id=$key' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='$key @ EnsEMBL' /></a>";
				}
				if($tsc == 0){
					$tirsp = 1;
					$tik = array_keys($value['ProbeStats']);
					add(sprintf("<tr><td rowspan='$rsp'>?</td><td rowspan='$rsp'>?</td><td rowspan='$rsp'>$probestring</td><td rowspan='$rsp'>{$value['ProbeStats'][$tik[0]]['chromosome']}</td><td rowspan='$tirsp'>{$tik[0]}</td><td rowspan='$tirsp'>".preg_replace('/([\d\.]+)e\-(\d+)/','\1&times;10<sup>-%d</sup>',$value['ProbeStats'][$tik[0]]['stat'])." ({$value['ProbeStats'][$tik[0]]['stattype']})</td><td rowspan='$rsp'>{$functional['trans-eQTL'][$study]['source']} <a href='{$functional['trans-eQTL'][$study]['link']}' target='_blank'><img src='frontend/img/pm.png' alt='PubMed' title='view in PubMed' /></a></td></tr>", preg_replace('/<?([\d\.]+)e\-(\d+)/','\2',$value['ProbeStats'][$tik[0]]['stat'])));
					for($i=1; $i<$tic; $i++){
						add(sprintf("<td rowspan='$tirsp'>{$tik[$i]}</td><td rowspan='$tirsp'>".preg_replace('/([\d\.]+)e\-(\d+)/','\1&times;10<sup>-%d</sup>',$value['ProbeStats'][$tik[$i]]['stat'])." ({$value['ProbeStats'][$tik[0]]['stattype']})</td><td rowspan='$rsp'>{$functional['trans-eQTL'][$study]['source']} <a href='{$functional['trans-eQTL'][$study]['link']}' target='_blank'><img src='frontend/img/pm.png' alt='PubMed' title='view in PubMed' /></a></td>", preg_replace('/<?([\d\.]+)e\-(\d+)/','\2',$value['ProbeStats'][$tik[$i]]['stat'])));
					}
				}else{
					$tsrsp = 1;
					$tirsp = 1;
					$tik = array_keys($value['ProbeStats']);
					$tsk = array_keys($value['ProbeData']);
					for($i=0; ($i<$tic || $i<$tsc); $i++){
						add("<tr>");
						if($i<$tsc-1){
							add("<td rowspan='$tsrsp'>{$value['ProbeData'][$tsk[$i]]['gene_symbol']} <a href='http://www.ensembl.org/Homo_sapiens/Gene/Summary?db=core;g={$value['ProbeData'][$tsk[$i]]['gene_id']}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$value['ProbeData'][$tsk[$i]]['gene_symbol']} @ EnsEMBL' /></a></td><td rowspan='$tsrsp'>{$tsk[$i]} ".(($tsk[$i]=="?") ? "" : "<a href='http://www.ensembl.org/Homo_sapiens/Transcript/Summary?db=core;t={$tsk[$i]}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$tsk[$i]} @ EnsEMBL' /></a>")."</td>");
						}
						if($i==$tsc-1){
							add("<td rowspan='".($rsp-$i)."'>{$value['ProbeData'][$tsk[$i]]['gene_symbol']} <a href='http://www.ensembl.org/Homo_sapiens/Gene/Summary?db=core;g={$value['ProbeData'][$tsk[$i]]['gene_id']}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$value['ProbeData'][$tsk[$i]]['gene_symbol']} @ EnsEMBL' /></a></td><td rowspan='".($rsp-$i)."'>{$tsk[$i]} ".(($tsk[$i]=="?") ? "" : "<a href='http://www.ensembl.org/Homo_sapiens/Transcript/Summary?db=core;t={$tsk[$i]}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$tsk[$i]} @ EnsEMBL' /></a>")."</td>");
						}
						if($i==0){
							add("<td rowspan='$rsp'>$probestring</td><td rowspan='$rsp'>{$value['ProbeStats'][$tik[0]]['chromosome']}</td>");
						}
						if($i<$tic-1){
							add(sprintf("<td rowspan='$tirsp'>{$tik[$i]}</td><td rowspan='$tirsp'>".preg_replace('/([\d\.]+)e\-(\d+)/','\1&times;10<sup>-%d</sup>',$value['ProbeStats'][$tik[$i]]['stat'])." ({$value['ProbeStats'][$tik[$i]]['stattype']})</td><td rowspan='".$tirsp."'>{$functional['trans-eQTL'][$study]['source']} <a href='{$functional['trans-eQTL'][$study]['link']}' target='_blank'><img src='frontend/img/pm.png' alt='PubMed' title='view in PubMed' /></a></td>", preg_replace('/<?([\d\.]+)e\-(\d+)/','\2',$value['ProbeStats'][$tik[$i]]['stat'])));
						}
						if($i==$tic-1){
							add(sprintf("<td rowspan='".($rsp-$i)."'>{$tik[$i]}</td><td rowspan='".($rsp-$i)."'>".preg_replace('/([\d\.]+)e\-(\d+)/','\1&times;10<sup>-%d</sup>',$value['ProbeStats'][$tik[$i]]['stat'])." ({$value['ProbeStats'][$tik[$i]]['stattype']})</td><td rowspan='".($rsp-$i)."'>{$functional['trans-eQTL'][$study]['source']} <a href='{$functional['trans-eQTL'][$study]['link']}' target='_blank'><img src='frontend/img/pm.png' alt='PubMed' title='view in PubMed' /></a></td>", preg_replace('/<?([\d\.]+)e\-(\d+)/','\2',$value['ProbeStats'][$tik[$i]]['stat'])));
						}
						add("</tr>");
					}
				}
				add("</tbody>");
			}
		}
		add("</table><br/>");
	}
	
	
	add("</div>");
}

// putative regulatory
if(array_key_exists('putative_regulatory_effect', $functional) || array_key_exists('variation_proximal_to_gene', $functional)){
	add("<div class='annotation-section'>");
	add("<h2 class='efftype'>Putative effect on regulation</h2>");
	
	// TFBS
	if(array_key_exists('TFBS_variant', $functional['putative_regulatory_effect'])){
		add("<table class='annotation'><tr><th class='duper' colspan='5'>Transcription factor binding site variation</th></tr>
		<tr><th class='super'>transcription factor</th><th class='super'>binding motif</th><th class='super'>motif position</th><th class='super'>highly informative position</th><th class='super'>score change</th></tr>");
		foreach($functional['putative_regulatory_effect']['TFBS_variant'] as $key => $value){
			add(sprintf("<tr><td>".str_replace(",", ", ", $value['TF'])."</td><td>$key</td><td>{$value['motif_position']}</td><td>{$value['HI_position']}</td><td>%.3f</td></tr>", $value['score_change']));
		}
		add("</table><br/>");
	}
	
	// FANTOM5
	if(!empty($functional['putative_regulatory_effect']['regulatory_fantom5'])){
		$ffcp = preg_grep("/FFCP/", array_keys($functional['putative_regulatory_effect']['regulatory_fantom5']));
		$ffce = preg_grep("/FFCE/", array_keys($functional['putative_regulatory_effect']['regulatory_fantom5']));
		
		if(!empty($ffcp)){
			add("<table class='annotation'><tr><th class='duper' colspan='3'>FANTOM5 expressed promoter</th></tr>
			<tr><th class='super'>SNiPA promoter id</th><th class='super'>associated transcript(s)</th><th class='super'>gene</th></tr>");
			foreach($ffcp as $key => $value){
				add("<tbody>");
				$cc = count($functional['putative_regulatory_effect']['regulatory_fantom5'][$value]['data']);
				$ckey = array_keys($functional['putative_regulatory_effect']['regulatory_fantom5'][$value]['data']);
				add("<tr><td rowspan='$cc'>$value <a href='{$functional['putative_regulatory_effect']['regulatory_fantom5'][$value]['link']}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='region @ EnsEMBL' /></a></td><td>");
				$tgen = array();
				foreach($functional['putative_regulatory_effect']['regulatory_fantom5'][$value]['data'][$ckey[0]] as $k => $v){
					array_push($tgen, $k." <a href='http://www.ensembl.org/Homo_sapiens/Transcript/Summary?db=core;t={$k}'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$k} @ EnsEMBL' /></a>");
				}
				add(join(", ",$tgen));
				add("</td><td>{$v} <a href='http://www.ensembl.org/Homo_sapiens/Gene/Summary?db=core;g={$ckey[0]}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$v} @ EnsEMBL' /></a></td></tr>");
				
				for($i = 1; $i < $cc; $i++){
					add("<tr><td>");
					$tgen = array();
					foreach($functional['putative_regulatory_effect']['regulatory_fantom5'][$value]['data'][$ckey[$i]] as $k => $v){
						array_push($tgen, $k." <a href='http://www.ensembl.org/Homo_sapiens/Transcript/Summary?db=core;t={$k}'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$k} @ EnsEMBL' /></a>");
					}
					add(join(", ",$tgen));
					add("</td><td>{$v} <a href='http://www.ensembl.org/Homo_sapiens/Gene/Summary?db=core;g={$ckey[$i]}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$v} @ EnsEMBL' /></a></td></tr>");
				}
				add("</tbody>");
			}
			add("</table><br/>");
		}		
		
		if(!empty($ffce)){
			add("<table class='annotation'><tr><th class='duper' colspan='3'>FANTOM5 expressed enhancer</th></tr>
			<tr><th class='super'>SNiPA enhancer id</th><th class='super'>associated transcript(s)</th><th class='super'>gene</th></tr>");
			foreach($ffce as $key => $value){
				add("<tbody>");
				$cc = count($functional['putative_regulatory_effect']['regulatory_fantom5'][$value]['data']);
				$ckey = array_keys($functional['putative_regulatory_effect']['regulatory_fantom5'][$value]['data']);
				add("<tr><td rowspan='$cc'>$value <a href='{$functional['putative_regulatory_effect']['regulatory_fantom5'][$value]['link']}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='region @ EnsEMBL' /></a></td><td>");
				$tgen = array();
				foreach($functional['putative_regulatory_effect']['regulatory_fantom5'][$value]['data'][$ckey[0]] as $k => $v){
					array_push($tgen, $k." <a href='http://www.ensembl.org/Homo_sapiens/Transcript/Summary?db=core;t={$k}'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$k} @ EnsEMBL' /></a>");
				}
				add(join(", ",$tgen));
				add("</td><td>{$v} <a href='http://www.ensembl.org/Homo_sapiens/Gene/Summary?db=core;g={$ckey[0]}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$v} @ EnsEMBL' /></a></td></tr>");
				
				for($i = 1; $i < $cc; $i++){
					add("<tr><td>");
					$tgen = array();
					foreach($functional['putative_regulatory_effect']['regulatory_fantom5'][$value]['data'][$ckey[$i]] as $k => $v){
						array_push($tgen, $k." <a href='http://www.ensembl.org/Homo_sapiens/Transcript/Summary?db=core;t={$k}'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$k} @ EnsEMBL' /></a>");
					}
					add(join(", ",$tgen));
					add("</td><td>{$v} <a href='http://www.ensembl.org/Homo_sapiens/Gene/Summary?db=core;g={$ckey[$i]}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$v} @ EnsEMBL' /></a></td></tr>");
				}
				add("</tbody>");
			}
			add("</table><br/>");
		}
	}

	// encode DHS
	if(!empty($functional['putative_regulatory_effect']['regulatory_encode'])){
		$encp = preg_grep("/ENCP/", array_keys($functional['putative_regulatory_effect']['regulatory_encode']));
		$ence = preg_grep("/ENCE/", array_keys($functional['putative_regulatory_effect']['regulatory_encode']));
		
		if(!empty($encp)){
			add("<table class='annotation'><tr><th class='duper' colspan='2'>ENCODE promoter-associated DHS</th></tr>
			<tr><th class='super'>SNiPA promoter id</th><th class='super'>associated gene(s)</th></tr>");
			foreach($encp as $key => $value){
				add("<tbody>");
				$cc = count($functional['putative_regulatory_effect']['regulatory_encode'][$value]['data']);
				$ckey = array_keys($functional['putative_regulatory_effect']['regulatory_encode'][$value]['data']);
				add("<tr><td rowspan='$cc'>$value <a href='{$functional['putative_regulatory_effect']['regulatory_encode'][$value]['link']}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='region @ EnsEMBL' /></a></td><td>{$functional['putative_regulatory_effect']['regulatory_encode'][$value]['data'][$ckey[0]]} <a href='http://www.ensembl.org/Homo_sapiens/Gene/Summary?db=core;g={$ckey[0]}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$functional['putative_regulatory_effect']['regulatory_encode'][$value]['data'][$ckey[0]]} @ EnsEMBL' /></a></td></tr>");
				for($i = 1; $i < $cc; $i++){
					add("<tr><td>{$functional['putative_regulatory_effect']['regulatory_encode'][$value]['data'][$ckey[$i]]} <a href='http://www.ensembl.org/Homo_sapiens/Gene/Summary?db=core;g={$ckey[$i]}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$functional['putative_regulatory_effect']['regulatory_encode'][$value]['data'][$ckey[$i]]} @ EnsEMBL' /></a></td></tr>");
				}
				add("</tbody>");
			}
			add("</table><br/>");
		}		
		
		if(!empty($ence)){
			add("<table class='annotation'><tr><th class='duper' colspan='3'>ENCODE promoter-associated distal DHS (Enhancer)</th></tr>
			<tr><th class='super'>SNiPA enhancer id</th><th class='super'>associated SNiPA promoter id</th><th class='super'>associated gene(s)</th></tr>");
			foreach($ence as $key => $value){
				add("<tbody>");
				$cc = count($functional['putative_regulatory_effect']['regulatory_encode'][$value]['data']);
				$ckey = array_keys($functional['putative_regulatory_effect']['regulatory_encode'][$value]['data']);
				add("<tr><td rowspan='$cc'>$value <a href='{$functional['putative_regulatory_effect']['regulatory_encode'][$value]['link']}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='region @ EnsEMBL' /></a></td><td>{$ckey[0]}</td><td>");
				$tgen = array();
				foreach($functional['putative_regulatory_effect']['regulatory_encode'][$value]['data'][$ckey[0]] as $k => $v){
					array_push($tgen, $v." <a href='http://www.ensembl.org/Homo_sapiens/Gene/Summary?db=core;g={$k}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$v} @ EnsEMBL' /></a>");
				}
				add(join("<br/>",$tgen));
				add("</td></tr>");
				
				for($i = 1; $i < $cc; $i++){
					add("<tr><td>$ckey[$i]</td><td>");
					$tgen = array();
					foreach($functional['putative_regulatory_effect']['regulatory_encode'][$value]['data'][$ckey[$i]] as $k => $v){
						array_push($tgen, $v." <a href='http://www.ensembl.org/Homo_sapiens/Gene/Summary?db=core;g={$k}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$v} @ EnsEMBL' /></a>");
					}
					add(join("<br/>",$tgen));
					add("</td></tr>");
					add("</td></tr>");
				}
				add("</tbody>");
			}
			add("</table><br/>");
		}
	}
	
	// regulatory cluster
	if(!empty($functional['putative_regulatory_effect']['regulatory'])){
		add("<table class='annotation'><tr><th class='duper' colspan='3'>Regulatory feature cluster</th></tr>
		<tr><th class='super'>element id</th><th class='super'>tissue/cell</th><th class='super'>factors</th></tr>");
		foreach($functional['putative_regulatory_effect']['regulatory'] as $value => $key){
			add("<tbody>");
			$cc = count($functional['putative_regulatory_effect']['regulatory'][$value]['data']);
			$ckey = array_keys($functional['putative_regulatory_effect']['regulatory'][$value]['data']);
			add("<tr><td rowspan='$cc'>$value  <a href='http://www.ensembl.org/Homo_sapiens/Regulation/Cell_line?rf={$value}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$value} @ EnsEMBL' /></a>".((array_key_exists("biotype", $functional['putative_regulatory_effect']['regulatory'][$value])) ? "<br/>(".str_replace("_", " ", $functional['putative_regulatory_effect']['regulatory'][$value]['biotype']).")" : "")."</td><td>{$ckey[0]}</td><td>{$functional['putative_regulatory_effect']['regulatory'][$value]['data'][$ckey[0]]}</td></tr>");
			for($i = 1; $i < $cc; $i++){
				add("<tr><td>$ckey[$i]</td><td>{$functional['putative_regulatory_effect']['regulatory'][$value]['data'][$ckey[$i]]}</td></tr>");
			}
			add("</tbody>");
		}
		add("</table><br/>");
	}
	
	// mirTS
	if(!empty($functional['putative_regulatory_effect']['mirTS'])){
		add("<table class='annotation'><tr><th class='duper' colspan='3'>Variation in RISC binding site</th></tr>
		<tr><th class='super'>gene</th><th class='super'>affected transcript(s)</th><th class='super'>targeting miRNA(s)</th></tr>");
		foreach($functional['putative_regulatory_effect']['mirTS'] as $key => $value){
			add("<tr><td>$key <a href='http://www.ensembl.org/Homo_sapiens/Gene/Summary?db=core;g={$value['gene_id']}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$key} @ EnsEMBL' /></a></td>");
			#add TS links
			$tarts = explode(", ", $value['transcripts']);
			array_walk($tarts, function(&$el){ $el = "{$el} <a href='http://www.ensembl.org/Homo_sapiens/Transcript/Summary?db=core;t={$el}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$el} @ EnsEMBL' /></a>";});
			$str = implode("<br/>",$tarts);
			add("<td>{$str}</td>");
			#add miRNA links
			$tarts = explode(", ", $value['mirnas']);
			array_walk($tarts, function(&$el){ $el = "{$el} <a href='http://mirbase.org/cgi-bin/query.pl?terms={$el}' target='_blank'><img src='frontend/img/mirbase.png' alt='miRBase' title='{$el} @ miRBase' /></a>";});
			$str = implode("<br/>",$tarts);
			add("<td>{$str}</td>");
		}
		add("</table><br/>");
	}
	
	// variation proximal to gene
	if(array_key_exists('variation_proximal_to_gene', $functional)){
		uasort($functional['variation_proximal_to_gene'], 'sortByGene');
		add("<table class='annotation'><tr><th class='duper' colspan='6'>Variation proximal to gene</th></tr>
		<tr>
		<th class='super'>gene</th>
		<th class='super'>variant type</th>
		<th class='super'>distance</th>
		<th class='super'>transcript</th>
		<th class='super'>RefSeq id</th>
		<th class='super'>protein</th></tr>");
		foreach($functional['variation_proximal_to_gene'] as $value => $key){
			add("<tr>
			<td>{$key['gene_symbol']} <a href='http://www.ensembl.org/Homo_sapiens/Gene/Summary?db=core;g={$key['gene_id']}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$key['gene_symbol']} @ EnsEMBL' /></a></td>
			<td>{$key['effect']}</td>
			<td>{$key['distance']}</td>
			<td>{$value} <a href='http://www.ensembl.org/Homo_sapiens/Transcript/Summary?db=core;t={$value}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$value} @ EnsEMBL' /></a></td>
			<td>{$key['refseq']}</td>
			<td>{$key['ensp']}". (($key['ensp'] !== "?") ? " <a href='http://www.ensembl.org/Homo_sapiens/Transcript/ProteinSummary?db=core;t={$value}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$key['ensp']} @ EnsEMBL' /></a>" : "" ) . "</td></tr>");
		}
		add("</table><br/>");
	}
	
	add("</div>");
}

//putative transcript
if(array_key_exists('putative_transcript_effect', $functional)){
	uasort($functional['putative_transcript_effect'], 'sortByGene');
	add("<div class='annotation-section'>");
	add("<h2 class='efftype'>Putative effect on transcript</h2>");
	$efftype = 'putative_transcript_effect';
	$subf = array();
	
	if($subf = getEffectArray($functional,$efftype,'/synonymous/')){
		add("<table class='annotation'><tr><th class='duper' colspan='7'>Synonymous coding variant</th></tr>
		<tr>
		<th class='super'>gene</th>
		<th class='super'>affected transcript</th>
		<th class='super'>RefSeq id</th>
		<th class='super'>protein</th>
		<th class='super'>amino acid</th>
		<th class='super'>codon</th></tr>");
		foreach($subf[$efftype] as $key => $value){
			add("<tr>
			<td>{$value['gene_symbol']} <a href='http://www.ensembl.org/Homo_sapiens/Gene/Summary?db=core;g={$value['gene_id']}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$value['gene_symbol']} @ EnsEMBL' /></a></td>
			<td>{$key} <a href='http://www.ensembl.org/Homo_sapiens/Transcript/Summary?db=core;t={$key}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$key} @ EnsEMBL' /></a></td>
			<td>{$value['refseq']}</td>
			<td>{$value['ensp']}". (($value['ensp'] !== "?") ? " <a href='http://www.ensembl.org/Homo_sapiens/Transcript/ProteinSummary?db=core;t={$key}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$value['ensp']} @ EnsEMBL' /></a>" : "") . "</td>
			<td>{$value['amino']}</td>
			<td>{$value['codon']}</td></tr>");
		}
		add("</table><br/>");
	}
	if($subf = getEffectArray($functional,$efftype,'/intron.+splice region/')){
		add("<table class='annotation'><tr><th class='duper' colspan='4'>Intron variant (splice region)</th></tr>
		<tr><th class='super'>gene</th>
		<th class='super'>affected transcript</th>
		<th class='super'>RefSeq id</th>
		<th class='super'>protein</th></tr>");
		foreach($subf[$efftype] as $key => $value){
			add("<tr>
			<td>{$value['gene_symbol']} <a href='http://www.ensembl.org/Homo_sapiens/Gene/Summary?db=core;g={$value['gene_id']}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$value['gene_symbol']} @ EnsEMBL' /></a></td>
			<td>{$key} <a href='http://www.ensembl.org/Homo_sapiens/Transcript/Summary?db=core;t={$key}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$key} @ EnsEMBL' /></a></td>
			<td>{$value['refseq']}</td>
			<td>".((array_key_exists('ensp', $value)) ? ("{$value['ensp']}".(($value['ensp'] !== "?") ? " <a href='http://www.ensembl.org/Homo_sapiens/Transcript/ProteinSummary?db=core;t={$value}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$value['ensp']} @ EnsEMBL' /></a>" : "" )) : "?"). "</td>
			</tr>");
		}
		add("</table><br/>");
	}
	if($subf = getEffectArray($functional,$efftype,'/3 prime.+splice region/')){
		add("<table class='annotation'><tr><th class='duper' colspan='4'>3'-UTR variant (splice region)</th></tr>
		<tr><th class='super'>gene</th>
		<th class='super'>affected transcript</th>
		<th class='super'>RefSeq id</th>
		<th class='super'>protein</th></tr>");
		foreach($subf[$efftype] as $key => $value){
			add("<tr>
			<td>{$value['gene_symbol']} <a href='http://www.ensembl.org/Homo_sapiens/Gene/Summary?db=core;g={$value['gene_id']}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$value['gene_symbol']} @ EnsEMBL' /></a></td>
			<td>{$key} <a href='http://www.ensembl.org/Homo_sapiens/Transcript/Summary?db=core;t={$key}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$key} @ EnsEMBL' /></a></td>
			<td>{$value['refseq']}</td>
			<td>".((array_key_exists('ensp', $value)) ? ("{$value['ensp']}".(($value['ensp'] !== "?") ? " <a href='http://www.ensembl.org/Homo_sapiens/Transcript/ProteinSummary?db=core;t={$value}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$value['ensp']} @ EnsEMBL' /></a>" : "" )) : "?"). "</td>
			</tr>");
		}
		add("</table><br/>");
	}
	if($subf = getEffectArray($functional,$efftype,'/5 prime.+splice region/')){
		add("<table class='annotation'><tr><th class='duper' colspan='4'>5'-UTR variant (splice region)</th></tr>
		<tr><th class='super'>gene</th>
		<th class='super'>affected transcript</th>
		<th class='super'>RefSeq id</th>
		<th class='super'>protein</th></tr>");
		foreach($subf[$efftype] as $key => $value){
			add("<tr>
			<td>{$value['gene_symbol']} <a href='http://www.ensembl.org/Homo_sapiens/Gene/Summary?db=core;g={$value['gene_id']}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$value['gene_symbol']} @ EnsEMBL' /></a></td>
			<td>{$key} <a href='http://www.ensembl.org/Homo_sapiens/Transcript/Summary?db=core;t={$key}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$key} @ EnsEMBL' /></a></td>
			<td>{$value['refseq']}</td>
			<td>".((array_key_exists('ensp', $value)) ? ("{$value['ensp']}".(($value['ensp'] !== "?") ? " <a href='http://www.ensembl.org/Homo_sapiens/Transcript/ProteinSummary?db=core;t={$value}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$value['ensp']} @ EnsEMBL' /></a>" : "" )) : "?"). "</td>
			</tr>");
		}
		add("</table><br/>");
	}
	if($subf = getEffectArray($functional,$efftype,'/non coding exon.+splice region/')){
		add("<table class='annotation'><tr><th class='duper' colspan='4'>Non-coding exon variant (splice region)</th></tr>
		<tr><th class='super'>gene</th>
		<th class='super'>affected transcript</th>
		<th class='super'>RefSeq id</th>
		<th class='super'>protein</th></tr>");
		foreach($subf[$efftype] as $key => $value){
			add("<tr>
			<td>{$value['gene_symbol']} <a href='http://www.ensembl.org/Homo_sapiens/Gene/Summary?db=core;g={$value['gene_id']}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$value['gene_symbol']} @ EnsEMBL' /></a></td>
			<td>{$key} <a href='http://www.ensembl.org/Homo_sapiens/Transcript/Summary?db=core;t={$key}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$key} @ EnsEMBL' /></a></td>
			<td>{$value['refseq']}</td>
			<td>".((array_key_exists('ensp', $value)) ? ("{$value['ensp']}".(($value['ensp'] !== "?") ? " <a href='http://www.ensembl.org/Homo_sapiens/Transcript/ProteinSummary?db=core;t={$value}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$value['ensp']} @ EnsEMBL' /></a>" : "" )) : "?"). "</td>
			</tr>");
		}
		add("</table><br/>");
	}
	if($subf = getEffectArray($functional,$efftype,'/intron variant$/')){
		add("<table class='annotation'><tr><th class='duper' colspan='4'>Intron variant</th></tr>
		<tr><th class='super'>gene</th>
		<th class='super'>affected transcript</th>
		<th class='super'>RefSeq id</th>
		<th class='super'>protein</th></tr>");
		foreach($subf[$efftype] as $key => $value){
			add("<tr>
			<td>{$value['gene_symbol']} <a href='http://www.ensembl.org/Homo_sapiens/Gene/Summary?db=core;g={$value['gene_id']}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$value['gene_symbol']} @ EnsEMBL' /></a></td>
			<td>{$key} <a href='http://www.ensembl.org/Homo_sapiens/Transcript/Summary?db=core;t={$key}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$key} @ EnsEMBL' /></a></td>
			<td>{$value['refseq']}</td>
			<td>{$value['ensp']}". (($value['ensp'] !== "?") ? " <a href='http://www.ensembl.org/Homo_sapiens/Transcript/ProteinSummary?db=core;t={$key}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$value['ensp']} @ EnsEMBL' /></a>" : "") . "</td>
			</tr>");
		}
		add("</table><br/>");
	}
	if($subf = getEffectArray($functional,$efftype,'/3 prime.+variant$/')){
		add("<table class='annotation'><tr><th class='duper' colspan='4'>3'-UTR variant</th></tr>
		<tr>
		<th class='super'>gene</th>
		<th class='super'>affected transcript</th>
		<th class='super'>RefSeq id</th>
		<th class='super'>protein</th></tr>");
		foreach($subf[$efftype] as $key => $value){
			add("<tr>
			<td>{$value['gene_symbol']} <a href='http://www.ensembl.org/Homo_sapiens/Gene/Summary?db=core;g={$value['gene_id']}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$value['gene_symbol']} @ EnsEMBL' /></a></td>
			<td>{$key} <a href='http://www.ensembl.org/Homo_sapiens/Transcript/Summary?db=core;t={$key}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$key} @ EnsEMBL' /></a></td>
			<td>{$value['refseq']}</td>
			<td>{$value['ensp']}". (($value['ensp'] !== "?") ? " <a href='http://www.ensembl.org/Homo_sapiens/Transcript/ProteinSummary?db=core;t={$key}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$value['ensp']} @ EnsEMBL' /></a>" : "") . "</td>
			</tr>");
		}
		add("</table><br/>");
	}
	if($subf = getEffectArray($functional,$efftype,'/5 prime.+variant$/')){
		add("<table class='annotation'><tr><th class='duper' colspan='4'>5'-UTR variant</th></tr>
		<tr>
		<th class='super'>gene</th>
		<th class='super'>affected transcript</th>
		<th class='super'>RefSeq id</th>
		<th class='super'>protein</th></tr>");
		foreach($subf[$efftype] as $key => $value){
			add("<tr>
			<td>{$value['gene_symbol']} <a href='http://www.ensembl.org/Homo_sapiens/Gene/Summary?db=core;g={$value['gene_id']}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$value['gene_symbol']} @ EnsEMBL' /></a></td>
			<td>{$key} <a href='http://www.ensembl.org/Homo_sapiens/Transcript/Summary?db=core;t={$key}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$key} @ EnsEMBL' /></a></td>
			<td>{$value['refseq']}</td>
			<td>{$value['ensp']}". (($value['ensp'] !== "?") ? " <a href='http://www.ensembl.org/Homo_sapiens/Transcript/ProteinSummary?db=core;t={$key}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$value['ensp']} @ EnsEMBL' /></a>" : "") . "</td>
			</tr>");
		}
		add("</table><br/>");
	}
	if($subf = getEffectArray($functional,$efftype,'/non coding exon variant$/')){
		add("<table class='annotation'><tr><th class='duper' colspan='4'>Non-coding exon variant</th></tr>
		<tr>
		<th class='super'>gene</th>
		<th class='super'>affected transcript</th>
		<th class='super'>RefSeq id</th>
		<th class='super'>protein</th></tr>");
		foreach($subf[$efftype] as $key => $value){
			add("<tr>
			<td>{$value['gene_symbol']} <a href='http://www.ensembl.org/Homo_sapiens/Gene/Summary?db=core;g={$value['gene_id']}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$value['gene_symbol']} @ EnsEMBL' /></a></td>
			<td>{$key} <a href='http://www.ensembl.org/Homo_sapiens/Transcript/Summary?db=core;t={$key}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$key} @ EnsEMBL' /></a></td>
			<td>{$value['refseq']}</td>
			<td>{$value['ensp']}". (($value['ensp'] !== "?") ? " <a href='http://www.ensembl.org/Homo_sapiens/Transcript/ProteinSummary?db=core;t={$key}' target='_blank'><img src='frontend/img/ens.png' alt='EnsEMBL' title='{$value['ensp']} @ EnsEMBL' /></a>" : "") . "</td>
			</tr>");
		}
		add("</table><br/>");
	}
	add("</div>");
}

print $txt;

// Hilfsfunktion um nach Genen zu sortieren
function sortByGene($a, $b){
	return (($a['gene_symbol'] < $b['gene_symbol']) ? -1 : 1);
}

//add("<h4>Functional annotation</h4>");
//add("<pre>");
//print_r(unserialize($annotationfunc[0]['PHPARRAY']));
//add("</pre>");

//add("<h4>Genes</h4>");
//add("<pre>");
//print_r($genes);
//add("</pre>");

//add("<h4>Regulatory Elements</h4>");
//add("<pre>");
//print_r($regel);
//add("</pre>");


?>
