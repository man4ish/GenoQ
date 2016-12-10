<?php
?>

		<p>We are compiling a list of frequently asked questions based on user feedback. <a href="?task=about_snipa">Please contact us</a> if you have any questions about our web service.</p>
		
		<h3>Data sources and tools</h3>
		<div class="documentation-accordion">
			<h3>Which <strong>data sources</strong> are used in GenoQ?</h3>
			<div>
				<h4>Variant set: 1000 Genomes Project Data</h2>
				<p>We annotated all bi-allelic single nucleotide variants contained in the 1000 Genomes Project phases 1 (version 3) and 3 (version 5) dataset <a href="#_ENREF_29"
				title="1000 Genomes Project Consortium, 2012"><sup>29</sup></a>. We calculated linkage disequilibrium data for an r<sup>2</sup> &ge; 0.1 for all super-populations. Please refer to the <a href="?task=release_notes" style="color: rgb(228,0,58);">release notes</a> for the variant counts.
				
				<h4>Conservation Scores: phyloP, phastCons and GERP++</h4>

				<p>We
				downloaded positional phyloP- as well as phastCons-100way-alignment PHAST
				conservation scores <a href="#_ENREF_1" title="Siepel, 2005"><sup>1</sup></a> in bigWig
				format from <a
				href="http://hgdownload.cse.ucsc.edu/goldenPath/hg19/phyloP100way/hg19.100way.phyloP100way.bw">http://hgdownload.cse.ucsc.edu/goldenPath/hg19/phyloP100way/hg19.100way.phyloP100way.bw</a> and <a
				href="http://hgdownload.cse.ucsc.edu/goldenPath/hg19/phastCons100way/hg19.100way.phastCons.bw">http://hgdownload.cse.ucsc.edu/goldenPath/hg19/phastCons100way/hg19.100way.phastCons.bw</a>. Further information on
				assemblies used in the 100way alignment can be obtained at <a
				href="http://hgdownload.cse.ucsc.edu/goldenPath/hg19/phyloP100way/">http://hgdownload.cse.ucsc.edu/goldenPath/hg19/phyloP100way/</a>. GERP++ positional RS (“rejected
				substitutions”) scores <a href="#_ENREF_2" title="Davydov, 2010"><sup>2</sup></a> were
				downloaded at <a
				href="http://hgdownload.cse.ucsc.edu/gbdb/hg19/bbi/All_hg19_RS.bw">http://hgdownload.cse.ucsc.edu/gbdb/hg19/bbi/All_hg19_RS.bw</a>. The three bigWig files were
				integrated into variant effect predictor (VEP) <a href="#_ENREF_3"
				title="McLaren, 2010"><sup>3</sup></a> annotation
				as custom annotation files. For VEP to be able to process bigWig files, we
				downloaded the bigWigToWig program provided by the University of California
				Santa Cruz <a href="#_ENREF_4" title="Kent, 2010"><sup>4</sup></a>.</p>

				<h4>Combined Annotation Dependent Depletion (CADD)</h4>

				<p>Kircher
				et al. provide an annotation-aided score for genotype pathogenicity called CADD
				<a href="#_ENREF_5" title="Kircher, 2014"><sup>5</sup></a>. We
				downloaded CADD-Scores for 1000 Genomes genotypes from <a
				href="http://cadd.gs.washington.edu/download">http://cadd.gs.washington.edu/download</a>. The downloaded file was
				parsed into one compressed Tabix-ready <a href="#_ENREF_6" title="Li, 2011"><sup>6</sup></a> file per
				chromosome (autosomes and X-chromosome) in General Feature Format (GFF, <a
				href="http://www.sanger.ac.uk/resources/software/gff/spec.html">http://www.sanger.ac.uk/resources/software/gff/spec.html</a>), Tabix-indexed and
				included in VEP annotation as custom annotation files. We used the PHRED-like
				transformation of the C score for variant annotation.</p>

				<h4>Thurman et al. – Promoters &amp; Distal Enhancers/Repressors</h4>

				<p>Simply
				put, Thurman et al. <a href="#_ENREF_7" title="Thurman, 2012"><sup>7</sup></a> used
				DNaseI hypersensitive sites (DHSs) and mapped them to transcription start sites
				(TSSs) of human transcripts. Accessible DHSs in proximity to the TSSs are
				classified as promoters. The accessibility patterns of more distal DHSs have
				been correlated with the accessibility patterns of promoters and are thus
				linked to the genes thought to be regulated by DHSs proximal to a TSS. After
				data processing, we obtained 412,798 distal elements (enhancers) and 23,749
				promoters.</p>

				<h4>FANTOM5 – Expressed Promoters &amp; Enhancers/Repressors</h4>

				<p>Two	papers of the FANTOM5 consortium <sup><a href="#_ENREF_8"
				title="Fantom Consortium and the Riken PMI and CLST (DGT), 2014">8</a>, <a href="#_ENREF_9"
				title="Andersson, 2014">9</a></sup> describe the properties,
				location and transcript associations of expressed regulatory elements
				(promoters and enhancers). We downloaded the datasets provided at <a
				href="http://fantom.gsc.riken.jp/data/">http://fantom.gsc.riken.jp/data/</a> 
				and <a href="http://enhancer.binf.ku.dk/">http://enhancer.binf.ku.dk/</a>,
				respectively. After data processing, we included 82,420 expressed promoters and
				43,002 expressed enhancers and their links to human transcripts in GenoQ.</p>

				<h4>StarBase v2.0: miRNA
				target sites (n=606,408)</h4>

				<p>We
				downloaded miRNA target sites located in RNA-binding protein (RBP) binding
				sites from the starBase v2.0 database (<a
				href="http://starbase.sysu.edu.cn/">http://starbase.sysu.edu.cn/</a>, released 09/2013, accessed
				16/01/2014) <a href="#_ENREF_10" title="Li, 2014"><sup>10</sup></a>. We
				included target predictions from 5 prediction tools at positions that are
				located in experimentally identified regions bound by RBPs. The downloaded file
				was parsed into one compressed Tabix-ready <a href="#_ENREF_6"
				title="Li, 2011"><sup>6</sup></a> file per
				chromosome (autosomes and X-chromosome) in General Feature Format (GFF, <a
				href="http://www.sanger.ac.uk/resources/software/gff/spec.html">http://www.sanger.ac.uk/resources/software/gff/spec.html</a>), Tabix-indexed and
				included in VEP annotation as custom annotation files.</p>

				<h4>eQTL data</h4>

				<h5>GTEx project,
				2015 (release V6) - Multiple tissues</h5>

				<p>For a detailed description of the Genotype-Tissue Expression project (GTEx)<a href="#_ENREF_30" title="GTEx consortium, 2015"><sup>30</sup></a>, please refer to the <a target="_blank" href="http://gtexportal.org/home/">GTex Portal</a>. 
				We downloaded significant associations from GTEx data release V6. In <i>GenoQ</i>, associations are provided for 44 tissues:
				adrenal gland, anterior cingulate cortex, aorta, atrial appendage, blood, breast, caudate basal ganglia, cerebellar hemisphere, cerebellum, coronary artery, cortex, EBV lymphocytes, esophagus mucosa, frontal cortex, gastroesophageal junction, hippocampus, hypothalamus, left ventricle, liver, lung, muscularis mucosae, nucleus accumbens, ovary, pancreas, pituitary, prostate, putamen, sigmoid colon, skeletal muscle, spleen, stomach, subcutaneous adipocytes, sun exposed skin, terminal ileum, testis, thyroid, tibial artery, tibial nerve, transformed fibroblasts, transverse colon, unexposed skin, uterus, vagina, and visceral adipocytes.
				In total, the dataset comprises 19,103,582 variant/gene expression <i>cis</i>-associations (1,981,375 unique variants).
								
				<h5>Zeller et al.,
				2010 - Monocytes</h5>

				<p>Zeller
				et al. investigated <i>cis- </i>and <i>trans-</i> associations of expression
				traits with &gt;675,000 SNPs (Affymetrix SNP Array 6.0) in human monocytes from
				1,490 unrelated individuals using the Illumina Human HT-12 v3 BeadChip. We
				downloaded the SQLite database dump containing the association results from <a
				href="http://genecanvas.ecgene.net/uploads/ForReview/ghs_probe_express030510.zip">http://genecanvas.ecgene.net/uploads/ForReview/ghs_probe_express030510.zip</a>. This database contains
				imputed association data on &gt;2 Mio. SNPs. We followed the protocol in <a
				href="#_ENREF_11" title="Zeller, 2010"><sup>11</sup></a> and
				filtered associations for genome-wide significance (P&gt;5.78x10<sup>-12</sup>).
				This filtered set was intersected with Kruskall-Wallis (KW) test results and
				filtered to feature a KW P&lt;10<sup>-10</sup> as described in <a
				href="#_ENREF_11" title="Zeller, 2010"><sup>11</sup></a>. SNPs
				were then split into <i>cis-</i>/<i>trans-</i>associations via distance to
				their associated expression target (up to 1MB apart: <i>cis</i>, else: <i>trans</i>).</p>

				<h5>Multiple Tissue
				Human Expression Resource (MuTHER) – LCL, adipose and skin tissue</h5>

				<p>The
				MuTHER Consortium collected samples from 856 female twins of the TwinsUK
				resource in three tissues (LCL, adipose tissue, skin tissue) <a
				href="#_ENREF_12" title="Grundberg, 2012"><sup>12</sup></a>. <i>cis-</i>eQTL
				associations comprising &gt;2 Mio. SNPs were calculated using the Illumina
				Human HT-12 v3 BeadChip. We downloaded the results files from <a
				href="http://www.muther.ac.uk/Data.html">http://www.muther.ac.uk/Data.html</a> and applied the P-value
				filters as described in <a href="#_ENREF_12" title="Grundberg, 2012"><sup>12</sup></a> (P<sub>lcl</sub>&lt;7.8x10<sup>-5</sup>,
				P<sub>adipose</sub>&lt;5x10<sup>-5</sup>, P<sub>skin</sub>&lt;3.8x10<sup>-5</sup>)
				corresponding to a per-tissue false discovery rate (FDR) of 1%.</p>

				<h5>Westra et al., 2013	– Peripheral blood</h5>

				<p>Westra
				et al. performed a meta-analysis of eQTL associations in peripheral blood
				samples from 5,311 individuals <a href="#_ENREF_13" title="Westra, 2013"><sup>13</sup></a>.
				Genotype data was imputed to HapMap2 CEU genotypes (&gt;2 Mio. SNPs),
				expression data from different Illumina platforms (Human HT-12 v3, HT-12 v4,
				and H8 v2 BeadChips) were harmonized by mapping probe sequences to Human HT-12
				v3 identifiers. We downloaded the association data from <a
				href="http://genenetwork.nl/bloodeqtlbrowser/">http://genenetwork.nl/bloodeqtlbrowser/</a> and mapped probes
				specified by Illumina array address IDs to Illumina probe IDs using the
				developer manifest file (<a
				href="http://www.illumina.com">http://www.illumina.com</a>). <i>Cis-</i> and <i>trans-</i>associations
				were filtered to have P&lt;1.31x10<sup>-4</sup> and P&lt;5.12x10<sup>-7</sup>,
				respectively, corresponding to an FDR of 5%. Here, eQTLs located less than 250
				KB away from the probe midpoint are defined as <i>cis</i> while eQTLs more than
				5 MB apart from the probe are defined as <i>trans</i> <a href="#_ENREF_13"
				title="Westra, 2013"><sup>13</sup></a>.</p>

				<h5>Fairfax et al.,
				2012 – B-cells and monocytes</h5>

				<p>Fairfax
				et al. investigated genotype associations with expression data from B-cells and
				monocytes from 288 individuals. For &gt;600,000 SNPs <i>cis-</i> (&lt;=2.5 MB
				away from the probe) and <i>trans-</i>associations were determined at
				permutation (n=1,000) P&lt;1x10<sup>-3</sup> and Bonferroni-corrected P&lt;1x10<sup>-11</sup>,
				respectively. We downloaded significant associations from the online supplement
				<a href="#_ENREF_14" title="Fairfax, 2012"><sup>14</sup></a> and
				mapped the associations to Illumina HumanHT-12 v4 probes using the genomic
				coordinates provided in the supplemental files to obtain an up-to-date mapping
				to the corresponding genes. For this, we converted hg18/NCBI36 coordinates to
				hg19/GRCh37 coordinates using the UCSC liftOver tool <a href="#_ENREF_15"
				title="Rhead, 2010"><sup>15</sup></a>. Probe
				mapping data was retrieved from the EnsEMBL public SQL database <a
				href="#_ENREF_16" title="Flicek, 2014"><sup>16</sup></a>.</p>

				<h5>seeQTL database
				– LCL and brain</h5>

				<p>The
				seeQTL database <a href="#_ENREF_17" title="Xia, 2012"><sup>17</sup></a> contains
				several eQTL association datasets. Most of these are based on samples from
				individuals contained in the HapMap populations. On the data website of the
				seeQTL browser (<a
				href="http://www.bios.unc.edu/research/genomic_software/seeQTL/data_source">http://www.bios.unc.edu/research/genomic_software/seeQTL/data_source</a>), Xia et al. provide a
				meta-analysis association set on all HapMap-based studies which we included in
				our annotations. In addition, association data from an eQTL study on human
				brain samples (Myers et al. <a href="#_ENREF_18" title="Myers, 2007"><sup>18</sup></a>) in the
				same file format is available and was also included.</p>

				<h5>Dixon et al.,
				2007 - LCL</h5>

				<p>Dixon
				et al. investigated genotype associations with expression data (using Affymetrix
				HG-U133 Plus 2.0 chip) from LCL cell lines of 400 individuals <a
				href="#_ENREF_19" title="Dixon, 2007"><sup>19</sup></a>. The
				threshold for genome-wide significance was set to be a LOD score &gt;6.076
				(equivalent to an FDR of 5%). We downloaded significant associations from the
				online supplement <a href="#_ENREF_19" title="Dixon, 2007"><sup>19</sup></a>.
				Associations with probes mapping to multiple locations in the genomes where
				removed (n=3,309). Associations were defined as <i>trans </i>if SNPs are
				located more than 1 MB apart from the probe center, and <i>cis</i> else.</p>

				<h5>Innocenti et al.,
				2011 - Hepatocytes</h5>

				<p>Innocenti
				et al investigated genotype associations with expression data (using Agilent
				4x44K arrays) from liver tissue of 266 individuals <a href="#_ENREF_20"
				title="Innocenti, 2011"><sup>20</sup></a>. The threshold
				for genome-wide significance was described to be a Bayes factor of &gt;5. We
				downloaded significant <i>cis-</i>associations from the online supplement <a
				href="#_ENREF_20" title="Innocenti, 2011"><sup>20</sup></a>. In
				GenoQ, we report P-values provided with the associations that, thus, may not
				always seem to be significant on a genome-wide level.</p>

				<h4>EnsEMBL</h4>

				<p>GenoQ
				makes extensive use of the EnsEMBL database <a href="#_ENREF_16"
				title="Flicek, 2014"><sup>16</sup></a>. For
				genome-annotation we downloaded gene data (including OMIM and DECIPHER
				annotations), regulatory feature clusters and regulatory motif data as well as
				linked information from the public MySQL database. We also used many of the variant
				annotations as they are provided with the VEP annotation. In addition, we
				downloaded trait annotations and associations from OMIM, HGMD, UniProt, dbGaP
				and ClinVar.<br />
				The number of genes, transcripts, and protein products as well as the number of regulatory feature clusters included in the genome annotation sets can be found in the <a href="?task=release_notes" style="color: rgb(228,0,58)">release notes</a>.</p>

				<h4>Phenotype data</h4>

				<p>In
				addition to the data obtained at EnsEMBL, we included the NHGRI GWAS Catalog
				and gene annotations from OrphaNet. Thus, GenoQ contains variant associations and annotations from these sources: HGMD (PMID: 24077912 <a href="#_ENREF_21" title="Stenson, 2014"><sup>21</sup></a>), dbGaP (PMID: 17898773 <a href="#_ENREF_22" title="Mailman, 2007"><sup>22</sup></a>), ClinVar (PMID: 24234437 <a href="#_ENREF_23" title="Landrum, 2014"><sup>23</sup></a>), OMIM variation (<a href="http://omim.org/">http://omim.org/</a> <a href="#_ENREF_24" title="Online Mendelian Inheritance in Man (OMIM®) [http://omim.org/ - accessed: 02/27/2014], 1966-2014"><sup>24</sup></a>), UniProt (PMID: 24253303 <a href="#_ENREF_25" title="UniProt Consortium, 2014"><sup>25</sup></a>), GWAS Catalog (PMID: 19474294 <a href="#_ENREF_26" title="Hindorff, 2009"><sup>26</sup></a>), and DrugBank 4.0 (PMID: 24203711 <a href="#_ENREF_27" title="Law, 2014"><sup>27</sup></a>).<br />
				Gene annotations are taken from DECIPHER (<a href="http://decipher.sanger.ac.uk/">http://decipher.sanger.ac.uk/</a>), OMIM gene (<a href="http://omim.org/">http://omim.org/</a> <a href="#_ENREF_24" title="Online Mendelian Inheritance in Man (OMIM®) [http://omim.org/ - accessed: 02/27/2014], 1966-2014"><sup>24</sup></a>), and OrphaNet (<a href="http://orpha.net/">http://orpha.net/</a> <a href="#_ENREF_28" title="Orphanet encyclopedia, "><sup>28</sup></a>).<br /><br />
				Detailed information about the number of variant associations/annotations and gene associations are provided in the <a href="?task=release_notes" style="color: rgb(228,0,58)">release notes</a>.</p>

		
				
				
				<p>&nbsp;</p>
				<h4>References</h4>
				
				<div style="font-size: smaller;">
				<p><a name="_ENREF_1">1. Siepel,
				A. et al. Evolutionarily conserved elements in vertebrate, insect, worm, and
				yeast genomes. <i>Genome research</i> <b>15</b>, 1034-1050 (2005).</a></p>

				<p><a
				name="_ENREF_2">2. Davydov,
				E.V. et al. Identifying a high fraction of the human genome to be under
				selective constraint using GERP++. <i>PLoS computational biology</i> <b>6</b>,
				e1001025 (2010).</a></p>

				<p><a
				name="_ENREF_3">3. McLaren,
				W. et al. Deriving the consequences of genomic variants with the Ensembl API
				and SNP Effect Predictor. <i>Bioinformatics</i> <b>26</b>, 2069-2070 (2010).</a></p>

				<p><a
				name="_ENREF_4">4. Kent,
				W.J., Zweig, A.S., Barber, G., Hinrichs, A.S. &amp; Karolchik, D. BigWig and
				BigBed: enabling browsing of large distributed datasets. <i>Bioinformatics</i> <b>26</b>,
				2204-2207 (2010).</a></p>

				<p><a
				name="_ENREF_5">5. Kircher,
				M. et al. A general framework for estimating the relative pathogenicity of
				human genetic variants. <i>Nature genetics</i> <b>46</b>, 310-315 (2014).</a></p>

				<p><a
				name="_ENREF_6">6. Li,
				H. Tabix: fast retrieval of sequence features from generic TAB-delimited files.
				<i>Bioinformatics</i> <b>27</b>, 718-719 (2011).</a></p>

				<p><a
				name="_ENREF_7">7. Thurman,
				R.E. et al. The accessible chromatin landscape of the human genome. <i>Nature</i>
				<b>489</b>, 75-82 (2012).</a></p>

				<p><a
				name="_ENREF_8">8. Fantom
				Consortium and the Riken PMI and CLST (DGT) et al. A promoter-level mammalian
				expression atlas. <i>Nature</i> <b>507</b>, 462-470 (2014).</a></p>

				<p><a
				name="_ENREF_9">9. Andersson,
				R. et al. An atlas of active enhancers across human cell types and tissues. <i>Nature</i>
				<b>507</b>, 455-461 (2014).</a></p>

				<p><a
				name="_ENREF_10">10. Li,
				J.H., Liu, S., Zhou, H., Qu, L.H. &amp; Yang, J.H. starBase v2.0: decoding
				miRNA-ceRNA, miRNA-ncRNA and protein-RNA interaction networks from large-scale
				CLIP-Seq data. <i>Nucleic acids research</i> <b>42</b>, D92-97 (2014).</a></p>

				<p><a
				name="_ENREF_11">11. Zeller,
				T. et al. Genetics and beyond--the transcriptome of human monocytes and disease
				susceptibility. <i>PloS one</i> <b>5</b>, e10693 (2010).</a></p>

				<p><a
				name="_ENREF_12">12. Grundberg,
				E. et al. Mapping cis- and trans-regulatory effects across multiple tissues in
				twins. <i>Nature genetics</i> <b>44</b>, 1084-1089 (2012).</a></p>

				<p><a
				name="_ENREF_13">13. Westra,
				H.J. et al. Systematic identification of trans eQTLs as putative drivers of
				known disease associations. <i>Nature genetics</i> <b>45</b>, 1238-1243 (2013).</a></p>

				<p><a
				name="_ENREF_14">14. Fairfax,
				B.P. et al. Genetics of gene expression in primary immune cells identifies cell
				type-specific master regulators and roles of HLA alleles. <i>Nature genetics</i>
				<b>44</b>, 502-510 (2012).</a></p>

				<p><a
				name="_ENREF_15">15. Rhead,
				B. et al. The UCSC Genome Browser database: update 2010. <i>Nucleic acids
				research</i> <b>38</b>, D613-619 (2010).</a></p>

				<p><a
				name="_ENREF_16">16. Flicek,
				P. et al. Ensembl 2014. <i>Nucleic acids research</i> <b>42</b>, D749-755
				(2014).</a></p>

				<p><a
				name="_ENREF_17">17. Xia,
				K. et al. seeQTL: a searchable database for human eQTLs. <i>Bioinformatics</i> <b>28</b>,
				451-452 (2012).</a></p>

				<p><a
				name="_ENREF_18">18. Myers,
				A.J. et al. A survey of genetic human cortical gene expression. <i>Nature
				genetics</i> <b>39</b>, 1494-1499 (2007).</a></p>

				<p><a
				name="_ENREF_19">19. Dixon,
				A.L. et al. A genome-wide association study of global gene expression. <i>Nature
				genetics</i> <b>39</b>, 1202-1207 (2007).</a></p>

				<p><a
				name="_ENREF_20">20. Innocenti,
				F. et al. Identification, replication, and functional fine-mapping of
				expression quantitative trait loci in primary human liver tissue. <i>PLoS
				genetics</i> <b>7</b>, e1002078 (2011).</a></p>

				<p><a
				name="_ENREF_21">21. Stenson,
				P.D. et al. The Human Gene Mutation Database: building a comprehensive mutation
				repository for clinical and molecular genetics, diagnostic testing and
				personalized genomic medicine. <i>Human genetics</i> <b>133</b>, 1-9 (2014).</a></p>

				<p><a
				name="_ENREF_22">22. Mailman,
				M.D. et al. The NCBI dbGaP database of genotypes and phenotypes. <i>Nature
				genetics</i> <b>39</b>, 1181-1186 (2007).</a></p>

				<p><a
				name="_ENREF_23">23. Landrum,
				M.J. et al. ClinVar: public archive of relationships among sequence variation
				and human phenotype. <i>Nucleic acids research</i> <b>42</b>, D980-985 (2014).</a></p>

				<p><a
				name="_ENREF_24">24. Online
				Mendelian Inheritance in Man (OMIM®) [</a><a href="http://omim.org/">http://omim.org/</a> - accessed: 02/27/2014] 
				(McKusick-Nathans Institute of Genetic Medicine, Johns Hopkins University,
				Baltimore, MD; 1966-2014).</p>

				<p><a
				name="_ENREF_25">25. UniProt
				Consortium Activities at the Universal Protein Resource (UniProt). <i>Nucleic
				acids research</i> <b>42</b>, D191-198 (2014).</a></p>

				<p><a
				name="_ENREF_26">26. Hindorff,
				L.A. et al. Potential etiologic and functional implications of genome-wide
				association loci for human diseases and traits. <i>Proceedings of the National
				Academy of Sciences of the United States of America</i> <b>106</b>, 9362-9367
				(2009).</a></p>

				<p><a
				name="_ENREF_27">27. Law,
				V. et al. DrugBank 4.0: shedding new light on drug metabolism. <i>Nucleic acids
				research</i> <b>42</b>, D1091-1097 (2014).</a></p>

				<p>
				<a name="_ENREF_28">28. Orphanet
				encyclopedia, Edn. 03/2014 (</a><a href="http://orpha.net/)">http://orpha.net/)</a>.</p>
				
				<p>
				<a name="_ENREF_29">29. 1000 Genomes Project Consortium et al. An integrated map of genetic variation from 1,092 human genomes. <i>Nature</i> <b>491</b>, 56-65 (2012).</a></p>
				<p>
				<a name="_ENREF_30">30. The GTEx Consortium. The Genotype-Tissue Expression (GTEx) pilot analysis: Multitissue gene regulation in humans. <i>Science</i> <b>348</b>, 648-660 (2015).</a></p>
				</div>
			</div>
	
			<h3>What <strong>tools</strong> and <strong>software packages</strong> are used in GenoQ?</h3>
			<div>
				<p>GenoQ is implemented in PHP (server) and HTML5/JavaScript (client). All tools used in GenoQ are publicly available and free for academic use. In particular, we used the following tools:</p>
					
					<h4>Annotation:</h4>
					<ul>
						<li>
							<strong>Variant Effect Predictor</strong>: McLaren W, Pritchard B, Rios D, Chen Y, Flicek P, Cunningham F. Deriving the consequences of genomic variants with the Ensembl API and SNP Effect Predictor. Bioinformatics 26(16):2069-70(2010). <a href="http://dx.doi.org/10.1093/bioinformatics/btq330" target="_blank">doi:10.1093/bioinformatics/btq330</a>
						</li>
						<li>
							<strong>GenomeGraphs</strong>: Durinck S, Bullard J, Spellman PT, and Dudoit S. GenomeGraphs: integrated genomic data visualization with R. BMC Bioinformatics 10:2 (2009). <a href="http://dx.doi.org/10.1186/1471-2105-10-2" target="_blank">doi:10.1186/1471-2105-10-2</a>
						</li>
					</ul>
					
					<h4>Server-side data processing</h4>
					<ul>
						<li>
							<strong>VCFtools</strong>: Danecek P et al. The Variant Call Format and VCFtools. Bioinformatics, 2011. <a href="http://dx.doi.org/10.1093/bioinformatics/btr330">doi:10.1093/bioinformatics/btr330</a> <a href="http://vcftools.sourceforge.net/" target="_blank">vcftools.sourceforge.net</a>
						</li>
						<li>
							<strong>Tabix</strong>: Li H. Tabix: fast retrieval of sequence features from generic TAB-delimited files. Bioinformatics 27(5):718-9. <a href="http://dx.doi.org/10.1093/bioinformatics/btq671" target="_blank">doi:10.1093/bioinformatics/btq671</a>
						</li>
						<li>
							<strong>R</strong>: R: A language and environment for statistical computing. R Foundation for Statistical Computing, Vienna, Austria. <a href="http://www.r-project.org/" target="_blank">www.r-project.org</a>
						</li>
						<li>
							<strong>rCharts</strong>: rCharts: an R package to create, customize and publish interactive javascript visualizations. Ramnath Vaidyanathan. <a href="http://www.rcharts.io" target="_blank">www.rcharts.io</a>
						</li>
						<li>
							<strong>Regional Association Plots</strong> and <strong>Linkage Disequilibrium Plots</strong>: Diabetes Genetics Initiative of Broad Institute of Harvard and MIT, Lund University, and Novartis Institutes of BioMedical Research. Genome-wide association analysis identifies loci for type 2 diabetes and triglyceride levels. Science 316:1331-1336 (2007). <a href="http://www.broadinstitute.org/diabetes/scandinavs/figures.html" target="_blank">www.broadinstitute.org/diabetes/scandinavs/figures.html</a>
						</li>
						
					</ul>

					<h4>Client-side data processing and rendering</h4>
					<ul>
						<li>
							<strong>jQuery</strong> and <strong>jQueryUI</strong>: The jQuery Foundation (2014). <a href="http://www.jquery.org" target="_blank">www.jquery.org</a>
						</li>
						<li>
							<strong>Highcharts</strong>: Highcharts JS: Interactive JavaScript charts for your web projects. Highsoft AS,  Vik i Sogn, Norway. <a href="http://www.highcharts.com/" target="_blank">www.highcharts.com</a>
						</li>
						
						<li>
							<strong>DataTables</strong>: DataTables: table plug-in for jQuery. SpryMedia. <a href="http://www.datatables.net" target="_blank">www.datatables.net</a>
						</li>
						<li>
							<strong>jQuery Chained</strong>: jquery_chained: chained selects for jQuery and Zepto. Mika Tuupola. <a href="http://www.appelsiini.net/projects/chained" target="_blank">www.appelsiini.net/projects/chained</a>
						</li>
						<li>
							<strong>Modernizr</strong>: Modernizr: the feature detection library for HTML5/CSS3. <a href="http://www.modernizr.com" target="_blank">www.modernizr.com</a>
						</li>
					</ul>
				
				 				
			</div>
		
			<h3>What about <strong>GRCh38</strong> genome coordinates?</h3>
			<div>
				<p>
					At the moment, all genetic elements are mapped to GRCh37. We will introduce GRCh38 coordinates as soon as all annotation data has been mapped to the new assembly. Further information on how we merge annotations accross both assemblies can be found in the <a href="?task=release_notes" style="color: rgb(228,0,58);">release notes</a> section.
				</p>
			</div>
		</div>
		
		<h3>How to use GenoQ</h3>
		<div class="documentation-accordion">
		
			<h3>How do I use the <strong>variant browser</strong> or the <strong>interactive plots?</strong></h3>
			<div>
				<p>This his how you can benefit from the interactive features offered by the Variant Browser and the interactive versions of Regional Association Plot and Linkage Disequilibrium Plot:</p>
				<div style="width: 600px; margin-left: auto; margin-right: auto;" >
					<h4>Tooltips</h4>
					<p><img src="snipa/documentation/plot-tooltip.jpg" alt="" /><br />Hover the cursor over a variant to get compressed functional annotations. This also works for genes and regulatory elements.</p>
					<hr />
					<h4>Context menu</h4>
					<p><img src="snipa/documentation/plot-contextmenu.jpg" alt="" /><br />Left-click on a variant to show a context menu. Here you can choose to show detailed annotations for this variant or copy it to GenoQ's clipboard so you can use it in other GenoQ modules.</p>
					<hr />
					<h4>Zooming</h4>
					<p><img src="snipa/documentation/plot-zoom.jpg" alt="" /><br />To zoom into the plot, left-click on an empty spot within the plotting region, keep the left mouse button pressed down, and move the cursor either to the left or right. Release the mouse button to zoom into the indicated region. To zoom out, hit the &quot;Reset zoom&quot; button.</p>
					<hr />
					<h4>Print and Download</h4>
					<p><img src="snipa/documentation/plot-download.jpg" alt="" /><br />Left-click on the icon in the plot's upper right corner to print or download the current plot.</p>
					<hr />
					<h4>Toggle plot elements</h4>
					<p><img src="snipa/documentation/plot-toggle.jpg" alt="" /><br />Left-click on a legend symbol to hide or show the corresponding elements. Note that this does currently not work for variants with multiple effects and trait-associated variants.</p>
					<hr />
				</div>
			</div>
			
			<h3>How do I use GenoQ's <strong>clipboard</strong>, and why can't I just use my computer's clipboard instead?</h3>
			<div>
				<p>
					For security reasons, web applications are not allowed to directly access your computer's clipboard (that is, not without using proprietary technologies like Adobe Flash). 
					This is why we integrated an &quot;in-site&quot; clipboard so you can copy variants from the output of any of GenoQ's modules and use them as input in other modules.
				</p>
				<p>You can use the clipboard like this:</p>
				<div style="width: 600px; margin-left: auto; margin-right: auto;" >
					<h4>Copy to clipboard</h4>
					<p><img src="snipa/documentation/clipboard-copy.jpg" alt="" /><br />In interactive plots, left-click on a variant to open the context menu. Select &quot;Copy to clipboard&quot;. If you want to copy a series of variants, tick the &quot;default action&quot; checkbox. Next time you click on a variant, GenoQ will automatically add it to its clipboard.</p>
					<hr />
					<h4>Manage the clipboard's content</h4>
					<p><img src="snipa/documentation/clipboard-manage.jpg" alt="" /><br />The clipboard is located below the site navigation area. It lists all added variants and their chromosomal location. Hit &quot;reset&quot; to delete all variants from the clipboard. To remove indivual variants, hover the cursor over the variant's identifier and click on the red &quot;&times;&quot.</p>
					<hr />
					<h4>Paste from clipboard</h4>
					<p><img src="snipa/documentation/clipboard-paste.jpg" alt="" /><br />You use the variants added to the clipboard as input for many of GenoQ's modules. For example, in the Variant Browser, click into the field where you would enter a variant's rs-identifier. A list of all variants will appear and you can select the appropriate one..</p>
					<hr />
				</div>
			</div>

			<h3>How can I <strong>switch between GenoQ's modules without</strong> the need for <strong>reentering</strong> the <strong>input data</strong>?</h3>
			<div>
				<p>
				Currently, the jobs run in individual GenoQ modules are not cached and thus have to be recomputed when switching between the modules within the same browser window/tab. For parallel use of the modules, you can use your browser's capabilities and run GenoQ in multiple instances in several windows or tabs.<br /><br />
				GenoQ's clipboard is synchronized across all windows/tabs and, thus, you can still use it to easily transfer variants between the modules.
			</div>
			
			<h3>What happens to <strong>my data</strong> when I <strong>upload it to GenoQ</strong>?</h3>
			<div>
				<p>To process your input data, GenoQ stores it in temporary files. These files can not be accessed by any other user.<br />All temporary files are irreversibly deleted within a 24 hours period.</p>
			</div>
			
			<h3>Is there any <strong>automated method</strong> or <strong>API</strong> to retrieve data from GenoQ?</h3>
			<div>
				<p>Currently, GenoQ does not offer API-based data access. However, we will integrate a REST / JSON interface in the near future.</p>
			</div>
			<h3>Can I <strong>download GenoQ's complete data</strong>?</h3>
			<div>
				<p>Yes. We provide the complete precomputed datasets as used by the GenoQ platform for each release. However, before you use the data, please make sure to be conform with the release policies of the providers of the primary data included in GenoQ as well as our <a style="color: rgb(228, 0, 58);" href="?task=about_snipa">disclaimer</a>.<br/><br/>Please refer to the <a style="color: rgb(228, 0, 58);" href="data/README.txt">README</a> for details on folder structure and data formats.<br/><br/>&rarr; <a style="color: rgb(228, 0, 58);" href="data">Data access</a></p>
			</div>
		</div>
		<br/><br/>
<?php
?>

		
		<?php
?>

