<?php
// Available Datasets for LD, Plots, and Variant Browser

// Genome Release - Reference Set - Populations
//                                - Chromosomes
//                - Annotation  

$snipaDatasetsDefault = array("genomerelease" => "grch37", "referenceset" => "genoq", "population" => "qtr", "annotation" => "ensembl82");

$snipaDatasets = array();
$snipaDatasets['genomerelease'] = array(
        array(
                "id" => "grch37",
                "name" => "GRCh37",
                "referenceset" => array(
                        array(
                                "id" => "1kgpp1v3",
                                "name" => "1000 Genomes, Phase 1 v3",
                                "population" => array(
                                        array("id" => "afr", "name" => "African"),
                                        array("id" => "amr", "name" => "American"),
                                        array("id" => "asn", "name" => "Asian"),
                                        array("id" => "eur", "name" => "European")
                                ),
                                "chrs" => array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,"X")
                        )
                       ,
                        array(
                                "id" => "1kgpp3v5",
                                "name" => "1000 Genomes, Phase 3 v5",
                                "population" => array(
                                        array("id" => "afr", "name" => "African"),
                                                                                array("id" => "amr", "name" => "American"),
                                                                                array("id" => "eas", "name" => "East Asian"),
                                                                                array("id" => "sas", "name" => "South Asian"),
                                                                                array("id" => "eur", "name" => "European")
                                ),
                                "chrs" => array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,"X")
                        ),
                        array(
                                "id" => "genoq",
                                "name" => "Qatar Genome",
                                "population" => array( array("id" => "qtr", "name" => "Qatar")),
                                "chrs" => array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,"X")
                        ),

                ),
                "annotation" => array(
                        array("id" => "ensembl75", "name" => "Ensembl 75"),
                        array("id" => "ensembl77", "name" => "Ensembl 77"),
                        array("id" => "ensembl80", "name" => "Ensembl 80"),
                        array("id" => "ensembl82", "name" => "Ensembl 82")
                )
        )
);


$snipaDatasets['genomereleaseld'] = array(
        array(
                "id" => "grch37",
                "name" => "GRCh37",
                "referenceset" => array(
                        array(
                                "id" => "1kgpp1v3",
                                "name" => "1000 Genomes, Phase 1 v3",
                                "population" => array(
                                        array("id" => "afr", "name" => "African"),
                                        array("id" => "amr", "name" => "American"),
                                        array("id" => "asn", "name" => "Asian"),
                                        array("id" => "eur", "name" => "European")
                                ),
                                "chrs" => array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,"X")
                        ),
                        array(
                                "id" => "1kgpp3v5",
                                "name" => "1000 Genomes, Phase 3 v5",
                                "population" => array(
                                        array("id" => "afr", "name" => "African"),
                                                                                array("id" => "amr", "name" => "American"),
                                                                                array("id" => "eas", "name" => "East Asian"),
                                                                                array("id" => "sas", "name" => "South Asian"),
                                                                                array("id" => "eur", "name" => "European")
                                ),
                                "chrs" => array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,"X")
                        )

                ),
                "annotation" => array(
                        array("id" => "ensembl75", "name" => "Ensembl 75"),
                        array("id" => "ensembl77", "name" => "Ensembl 77"),
                        array("id" => "ensembl80", "name" => "Ensembl 80"),
                        array("id" => "ensembl82", "name" => "Ensembl 82")
                )
        )
);



/*
$snipaDatasets['genomerelease'] = array(
	array(
		"id" => "grch37",
		"name" => "GRCh37",
		"referenceset" => array(
			array(
				"id" => "1kgpp1v3",
				"name" => "1000 Genomes, Phase 1 v3",
				"population" => array(
					array("id" => "afr", "name" => "African"),
					array("id" => "amr", "name" => "American"),
					array("id" => "asn", "name" => "Asian"),
					array("id" => "eur", "name" => "European"),
                                        array("id" => "qtr", "name" => "Qatari")
				),
				"chrs" => array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,"X")
			),
                        array(
                                "id" => "1kgpp3v5",
                                "name" => "1000 Genomes, Phase 3 v5",
                                "population" => array(
                                        array("id" => "afr", "name" => "African"),
										array("id" => "amr", "name" => "American"),
										array("id" => "eas", "name" => "East Asian"),
										array("id" => "sas", "name" => "South Asian"),
										array("id" => "eur", "name" => "European"),
                                                                                array("id" => "qtr", "name" => "Qatar")							
                                ),
                                "chrs" => array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,"X")
                        )
		),
		"annotation" => array(
			array("id" => "ensembl75", "name" => "Ensembl 75"),
                        array("id" => "ensembl77", "name" => "Ensembl 77")
                        //array("id" => "ensembl77", "name" => "Ensembl 80"),
                        //array("id" => "ensembl77", "name" => "Ensembl 82")
		)
	),
         array(
                "id" => "grch37",
                "name" => "GRCh37",
                "referenceset" => array(
                        array(
                                "id" => "genoq",
                                "name" => "Qatar Genome",
                                "population" => array(
                                        array("id" => "qtr", "name" => "Qatari")
                                ),
                                "chrs" => array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,"X")
                        )
                ),
                "annotation" => array(
                        array("id" => "qtr", "name" => "Qatar")
                )
        )

);
*/

/*
$snipaDatasets['genomerelease'] = array(
	array(
		"id" => "grch37",
		"name" => "GRCh37",
		"referenceset" => array(
			array(
				"id" => "1kgpp1v3",
				"name" => "1000 Genomes, Phase 1 v3",
				"population" => array(
					array("id" => "afr", "name" => "African"),
					array("id" => "amr", "name" => "American"),
					array("id" => "asn", "name" => "Asian"),
					array("id" => "eur", "name" => "European")
				),
				"chrs" => array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,"X")
			),
			array(
				"id" => "quatar1",
				"name" => "Qatari 108 genomes",
				"population" => array(
					array("id" => "qur", "name" => "Qatar")
				)
			) 
		),
		"annotation" => array(
			array("id" => "ensembl75", "name" => "Ensembl 75")
		)
	), 
	array(
		"id" => "grch38",
		"name" => "GRCh38",
		"referenceset" => array(
			array(
				"id" => "1kgpp1v3",
				"name" => "1000 Genomes, Phase 1 v3",
				"population" => array(
					array("id" => "afr", "name" => "African"),
					array("id" => "amr", "name" => "American"),
					array("id" => "asn", "name" => "Asian"),
					array("id" => "eur", "name" => "European")
				),
				"chrs" => array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,"X")
			),
			array(
				"id" => "1kgpp3v1",
				"name" => "1000 Genomes, Phase 3 v1",
				"population" => array(
					array("id" => "afr", "name" => "African"),
					array("id" => "amr", "name" => "American"),
					array("id" => "asn", "name" => "Asian"),
					array("id" => "eur", "name" => "European"),
					array("id" => "san", "name" => "South Asian")
				),
				"chrs" => array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22)
			)
		),
		"annotation" => array(
			array("id" => "ensembl75", "name" => "Ensembl 75"),
			array("id" => "ensembl76", "name" => "Ensembl 76")
		)
	) 
);
*/

?>
