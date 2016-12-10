			<h3>Contained data</h3>
			<p>Please refer to the <a href="index.php?task=documentation">documentation</a> for a full list of included data.
			</p>
			<h3>SNiPA features</h3>
			<p><b><i>General remarks</i></b></p>
			<p>SNiPA is a variant-centered resource. There are only two additional annotation tracks available: a gene annotation track and a track consisting of regulatory elements. The latter are linked to EnsEMBL or to their primary sources, if available. The central content of SNiPA are the “SNiPA cards” containing the annotation of individual variants. As “SNiPA cards” are very detailed and thus cannot be compressed enough to be manageable when investigating large variant sets, we also provide two other displays of annotations: first, the Block Annotation which is basically a “SNiPA card” except that it merges the annotation of all variants specified by the user. And second, a tabular format that contains top-level annotations for the variants (one row per variant). These tables can be sorted and filtered by keywords and individual “SNiPA cards” can then directly be accessed. Tables (as CSV) and “SNiPA cards” (as PDF) can be downloaded for later use.
			<br/><br/>In most SNiPA modules, one of the following input types is required by the user: dbSNP rs-identifier(s), a gene identifier, or a chromosomal position. For convenience, we have collected various sets of gene identifiers (such as UniGene IDs, Entrez Gene IDs, HGNC gene symbols) which makes previous mapping to EnsEMBL gene IDs (the ID scheme used by SNiPA) unnecessary in most cases.
			<br/><br/>The approaches behind SNiPA’s modules are logically separated by design. However, it is often necessary to analyze variants from different points of view. To simplify that, we have implemented a global interface (“Variant clipboard”) that can be used to store variants of interest. The input forms of all SNiPA modules provide functionality to paste variants from the clipboard into the form. This enables, for instance, scrolling through the genome using the Variant Browser, selecting variants of interest, and afterwards switching to the Variant Annotation module (or the Block Annotation if all variants are located on the same chromosome) to retrieve the “SNiPA cards” for all variants at once.</p>
			
			<p><b><i>SNiPA effect categories</i></b></p>
			<p>SNiPA uses the <a href="http://www.ensembl.org/Homo_sapiens/Tools/VEP" target="_blank">Ensembl VEP tool</a> for primary variant effect predictions. VEP provides Sequence Ontology (SO) terms with its predictions that we have categorized into several groups. For all additional annotations that SNiPA provides, we also use SO terms internally (e.g. level_of_transcript_variant for eQTL associations) if possible. For all other variants (e.g. variants within RBP binding sites), we used custom terms but did this in an SO-like way (e.g. miRNA_target_site_variant). Details on SO terms can be accessed at the SO website (<a href="http://www.sequenceontology.org/" target="_blank">www.sequenceontology.org/</a>). The effect categories in SNiPA are defined as follows (from most severe to least severe):
			<br/><br/><i>1. Category: "Direct transcript effect"</i><br>SO terms: <span style="font-size: smaller;">coding_sequence_variant, frameshift_variant, incomplete_terminal_codon_variant, initiator_codon_variant, mature_miRNA_variant, missense_variant, splice_acceptor_variant, splice_donor_variant, stop_gained, stop_lost</span>
			<br/><br/><i>2. Category: "Direct regulatory effect"</i><br>SO terms: <span style="font-size: smaller;">level_of_transcript_variant</span>
			<br/><br/><i>3. Category: "Putative regulatory effect"</i><br>SO terms: <span style="font-size: smaller;">regulatory_region_variant, TF_binding_site_variant, downstream_gene_variant (subcategory “Proximal to gene variant”), upstream_gene_variant (subcategory “Proximal to gene variant”)</span>
			<br>Non-SO terms: <span style="font-size: smaller;">miRNA_target_site_variant</span>
			<br/><br/><i>4. Category: "Putative transcript effect"</i><br>SO terms: <span style="font-size: smaller;">3_prime_UTR_variant, 5_prime_UTR_variant, intron_variant, nc_transcript_variant, NMD_transcript_variant, non_coding_exon_variant, splice_region_variant, stop_retained_variant, synonymous_variant</span>
			<br/><br/><i>5. Category “Unknown effect”</i><br>SO terms: <span style="font-size: smaller;">intergenic_variant</span>
			<br/><br/>Annotations contained in “SNiPA cards” are grouped in the first four categories. In addition, there is another section holding information on trait annotations for variants and genes as well as one section on general information on the variant.
			<br/><br/>The categories are encoded in all visualizations via the symbol used for the single variants (symbol keys are always listed in legends). 
			</p>
			
			<h3>SNiPA modules</h3>
			<p>Currently, there are eight modules that allow for retrieval of the data contained in SNiPA. In the following, we will shortly introduce them to emphasize their underlying concepts. Detailed usage instructions are given in the <a href="index.php?task=documentation">documentation</a>.
			</p>
			<p><b><i>Variant Browser</i></b></p>
			<p>The SNiPA variant browser is our version of a genome browser with a variant-centered point of view. Our main focus here was to enable the user to visually assess how well the variants in a locus are characterized by evidences. To achieve that, variants are plotted according to their highest effect category (see 2.2 SNiPA effect categories) meaning that the higher a variant is located in the plot, the more evidence exists for it to feature strong effects. Variants that are assigned to more than one effect category are highlighted in green, variants that have trait annotations available are highlighted in blue. Here, the symbols used for the variants and their location in the plot are redundant information. This is because the two other interactive plotting modules of SNiPA (LD plot (Figure 1B) and regional association plot) implement the interface of the browser and use other means of variant positioning, and there the used symbol is the only visual hint at the assigned effect categories.
			<br/><br/>The variant browser is intended to provide inspection of genomic loci without a background hypothesis. For these, other modules are better suited (see below). 
			<br/><br/>An additional feature of the browser (and of all visualizations implementing the browser’s interface) is that the plot can be exported as PDF or PNG.
			</p>
			<p><b><i>Association Maps</i></b></p>
			<p>To inspect variants or sets of variants that are associated with a specific trait (or a set of traits), we have implemented this module that allows for access to the data in SNiPA for variants with published associations (Figure 1D). “SNiPA cards” of the variants can be directly accessed from the karyogram. Furthermore, variants can be added to the Variant clipboard and serve as input for other modules such as the linkage disequilibrium plot for an LD-based locus inspection, the LD-based block annotation to get a summary of annotations for all correlating variants, the proxy search to retrieve a table of these variants with or without dense annotations, or the variant browser for further inspection of flanking regions of the locus.
			</p>
			<p><b><i>Variant Annotation</i></b></p>
			<p>This module provides direct access to variant annotations contained in SNiPA. Given a user-specified list of rs-identifiers, SNiPA returns a list of “SNiPA cards”.
			</p>
			<p><b><i>Block Annotation</i></b></p>
			<p>SNiPA’s block annotation module enables retrieval of merged annotations of a set of variants that can be specified by four different ways: a list of rs-identifiers, one rs-identifier that is first used to obtain a list of correlating variants (user-specified LD-threshold), a gene identifier, or a chromosomal region. Currently, only variants located on the same chromosome can be processed by block annotation. The merged annotation can be used to characterize a whole locus and thus may also be useful for characterizing rare variants for which no annotations are available.
			</p>
			<p><b><i>Regional Association Plot</i></b></p>
			<p>This is the classical plot for visualizing association results (locus-based Manhattan plot). Input is a user-specified list of variant/association p-value pairs. Variants are plotted by their position on the x-axis and –log<sub>10</sub>(p-value) on the y-axis. In addition, variants are colored by their correlation with the sentinel variant (by default, this is the variant with the lowest p-value, but optionally it can also be specified by the user). This plot implements the interface of the variant browser, meaning that all functionalities of the variant browser are provided except for navigating to other loci.
			</p>
			<p><b><i>Linkage Disequilibrium Plot</i></b></p>
			<p>This plot is very useful for instance to inspect a published GWAS hit. It is common practice to select a single variant (e.g. the one with the lowest p-value) as published representative for an association signal. LD data can be used to reproduce the reported locus albeit there always will be differences as the study populations will not be perfectly resembled by 1000 genomes individuals. Input is a single rs-identifier. Variants are plotted by their position on the x-axis and their correlation (<i>r<sup>2</sup></i>) to the specified variant on the y-axis. This plot implements the interface of the variant browser, meaning that all functionalities of the variant browser are provided except for navigating to other loci. Instead, the plot can be updated by selecting any contained variant as locus representative.
			</p>
			<p><b><i>Proxy Search</i></b></p>
			<p>This module allows for tabular retrieval of variants in LD with input variants. Dense annotation of the resulting variant set is possible.
			</p>
			<p><b><i>Pairwise LD</i></b></p>
			<p>A common challenge of association studies is to find out if one locus contains more than one association signal. One possible approach to do so is to check the LD pattern of the variants contained in the locus which can be done using this module. 
			</p>
			
			
			<h3>Documentation and Feedback</h3>
			<p>SNiPA is a new resource and thus we are dependent on input from external users with respect to improvements of the resource (such as inclusion of additional datasets), development of new modules, as well as the usefulness of the help texts in input forms or the documentation.
			<br/><br/>We have already created a rudimental FAQ-like documentation. However, we want to extend this as well as we are open for any suggestions for improvement of SNiPA. Therefore, we would be very thankful for all hints, bug reports, questions, and suggestions. These can be sent to either the corresponding author or directly to <a href="mailto:feedback@snipa.org">feedback@snipa.org</a>.
			</p>
