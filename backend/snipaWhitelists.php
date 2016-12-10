<?php

$whitelist_inputType = array(	'file' => 'File',
				'text' => 'Text Entry',
				'locus' => 'Genomic Locus'
				);

$whitelist_cohort = array(	'2011' => array(	'kora' => 'KORA',
							'twinsuk' => 'TwinsUK'
							),
				'2013' => array(	'twinsuk' => 'TwinsUK',
							'meta' => 'KORA+TwinsUK (Meta)'
							)
				);

$whitelist_type = array(	'2011' => array(	'kora' => array(	'single_metabolites' => 'Single Metabolites', 
										'ratios' => 'Metabolite Ratios'
										),
							'twinsuk' => array(	'single_metabolites' => 'Single Metabolites',
										'ratios' => 'Metabolite Ratios'
										)
							),
				'2013' => array(	'twinsuk' => array(	'ratios' => 'Metabolite Ratios'
										),
							'meta' => array(	'single_metabolites' => 'Single Metabolites',
										'ratios' => 'Metabolite Ratios'
										)
							)
				);

$whitelist_input = array(	'rsnumber' => 'rsID',
				'hgnc' => 'Gene Symbol',
#				'ensembl' => 'Ensembl Gene ID',
#				'metabolon' => 'Metabolon ID',
				'metname' => 'Metabolite Name',
#				'disease' => 'Disease',
#				'range' => 'Genomic Region'
				);

$whitelist_ld = array(	'1.0' => '1.0',
			'0.9' => '0.9',
			'0.8' => '0.8',
			'0.7' => '0.7',
			'0.6' => '0.6',
			'0.5' => '0.5');

$whitelist_tables = array(	'2011_ratios_kora' => 'ratios',
				'2011_ratios_twinsuk' => 'ratios',
				'2011_single_metabolites_kora' => 'single_metabolites',
				'2011_single_metabolites_twinsuk' => 'single_metabolites',
				'2013_ratios_meta' => 'ratios',
				'2013_ratios_twinsuk' => 'ratios',
				'2013_single_metabolites_meta' => 'single_metabolites');
?>
