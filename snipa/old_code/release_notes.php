			<h2><i>SNiPA</i> v3.1 (October/November 2015)</h2>
			<p><strong>Genome assembly:</strong> GRCh37.p13<br/><strong>Ensembl version:</strong> 82<br/><strong>1000 genomes:</strong> phase 3 version 5<br/><br/>
			</p>
			<p>This is only a minor update. It comprises updated variant-phenotype associations and annotations as contained in the Ensembl 82 release, as well as the new release of GTEx (see below). As of this version, we include the association data of the new <a href="http://www.ebi.ac.uk/gwas/" target="_blank">GWAS Catalog at EMBL-EBI</a>.</p>
			<h3>Element associations/annotations</h3>
			<h4>GTEx eQTL associations (V6)</h4>
			<p>We updated <i>SNiPA</i> to contain the new V6 release of the <a target="_blank" href="http://gtexportal.org">GTEx project</a>. <i>SNiPA</i> version 3.1 now includes about 20 mio. significant associations from GTEx across 44 tissues. Please refer to <a target="_blank" href="http://gtexportal.org/home/documentationPage">http://gtexportal.org/home/documentationPage</a> for information on GTEx data release and publication policy.</p>
			<h4>Variant associations</h4>
			<table class="docutable">
				<tr>
					<td><b>Source</b></td>
					<td><b>N (unique)</b></td>
					<td><b>Reference</b></td>
				</tr>
				<tr>
					<td><b>HGMD - 2015</b></td>
					<td>53,420 (48,305)</td>
					<td>PMID: 24077912</td>
				</tr>
				<tr>
					<td><b>dbGaP - October, 9<sup>th</sup>, 2015</b></td>
					<td>40,254 (28,767)</td>
					<td>PMID: 17898773</td>
				</tr>
				<tr>
					<td><b>ClinVar - October, 9<sup>th</sup>, 2015</b></td>
					<td>156,160 (139,160)</td>
					<td>PMID: 24234437</td>
				</tr>
				<tr>
					<td><b>OMIM variation - October, 9<sup>th</sup>, 2015</b></td>
					<td>19,878 (18,442)</td>
					<td><a href="http://omim.org/">http://omim.org/</a></td>
				</tr>
				<tr>
					<td><b>UniProt - October, 9<sup>th</sup>, 2015</b></td>
					<td>3,484 (3,219)</td>
					<td>PMID: 24253303</td>
				</tr>
				<tr>
					<td><b>GWAS Catalog - October, 9<sup>th</sup>, 2015</b></td>
					<td>19,950 (18,769)</td>
					<td>PMID: 19474294</td>
				</tr>
				<tr>
					<td><b>DrugBank - 4.2</b></td>
					<td>179 (169)</td>
					<td>PMID: 24203711</td>
				</tr>
			</table>
			
			<p><br /><hr /><br /></p>
			<h2><i>SNiPA</i> v3 (June 2015)</h2>
			<p><strong>Genome assembly:</strong> GRCh37.p13<br/><strong>Ensembl version:</strong> 80<br/><strong>1000 genomes:</strong> phase 3 version 5<br/><br/>
			</p>
			<h3>Bug fixes</h3>
			<h4>eQTL mapping</h4>
			<p>There was a minor bug in our probe mapping script that mapped array address IDs to probe IDs. Affected data sets were the associations reported by Westra et al. and Innocenti et al. The bug was fixed and as of Ensembl version 80, the associations should all be reported correctly. We thank ME Reyes for reporting this bug!</p>
			<h4>CADD scores</h4>
			<p>As the development of <i>SNiPA</i> started with 1000 genomes phase 1 version 3 data, we then used the precompiled CADD data set for 1000 genomes. With 1000 genomes phase 3 version 5, the variant count was more than doubled and the "new" variants were not contained in the provided CADD data file leading to a large amount of variants for which no CADD scores where available. In <i>SNiPA</i> version 3, we downloaded the genome-wide data set from the CADD website and retrieved allele-matched scores for all variants contained in <i>SNiPA</i>. We thank AM Nissen for reporting this bug!</p>
			<h3>New annotation data</h3>
			<h4>GTEx eQTL associations</h4>
			<p>Several <i>SNiPA</i>-users asked if we could include the eQTL associations from the <a target="_blank" href="http://gtexportal.org">GTEx project</a>. In <i>SNiPA</i> version 3, we included significant associations from GTEx release 4 data for 13 tissues. Please refer to <a target="_blank" href="http://gtexportal.org/home/documentationPage">http://gtexportal.org/home/documentationPage</a> for information on GTEx data release and publication policy.</p>
			<h4>SnpEff effect impact</h4>
			<p>Ensembl's <a target="_blank" href="http://www.ensembl.org/info/docs/tools/vep/">variant effect predictor (VEP)</a> now includes SnpEff effect impact predictions (<a target="_blank" href="http://snpeff.sourceforge.net/SnpEff_manual.html">details</a>). We added the prediction denoted as "effect impact" to the basic features table contained in <i>SNiPA</i>cards.</p>
			<h3>New features</h3>
			<h4>Custom association maps</h4>
			<p>We were asked if it would be possible to customize the association maps feature to be able to create own figures of association results. Therefore, we added this as a new feature in the <a href="index.php?task=association_maps">Association Maps</a> module, including a howto and example input.</p>
			<h3><i>SNiPA</i> annotation updates</h3>
			<p><strong><i>SNiPA</i> GeneBuild:</strong> N<sub>genes</sub> = 59,413 (based on GENCODE 22)<br />
			<strong><i>SNiPA</i> RegBuild:</strong> N<sub>elements</sub> = 1,471,812 (now includes <a target="_blank" href="http://fantom.gsc.riken.jp/5/">FANTOM5</a> permissive promoters)<br />
			</p>
			<h3>VEP-included utility versions:</h3>
			<p><strong>PolyPhen:</strong> v2.2.2<br />
			<strong>SIFT:</strong> v5.2.2<br />
			</p>
			<h3>Element associations/annotations</h3>
			<h4>Gene associations/annotations - updated on June 3<sup>rd</sup>, 2015</h4>
			<table class="docutable">
				<tr>
					<td><b>Source</b></td>
					<td><b>N (unique)</b></td>
					<td><b>Reference</b></td>
				</tr>
				<tr>
					<td><b>DECIPHER</b></td>
					<td>1,829 (1,829)</td>
					<td><a href="http://decipher.sanger.ac.uk/">http://decipher.sanger.ac.uk/</a></td>
				</tr>
				<tr>
					<td><b>OMIM gene</b></td>
					<td>4,886 (4,882)</td>
					<td><a href="http://omim.org/">http://omim.org/</a></td>
				</tr>
				<tr>
					<td><b>OrphaNet</b></td>
					<td>5,684 (5,684)</td>
					<td><a href="http://orpha.net/">http://orpha.net/</a></td>
				</tr>
			</table>
			<h4>Variant associations</h4>
			<table class="docutable">
				<tr>
					<td><b>Source</b></td>
					<td><b>N (unique)</b></td>
					<td><b>Reference</b></td>
				</tr>
				<tr>
					<td><b>HGMD - 2014.4</b></td>
					<td>41,077 (36,807)</td>
					<td>PMID: 24077912</td>
				</tr>
				<tr>
					<td><b>dbGaP - June 12<sup>th</sup>, 2015</b></td>
					<td>41,426 (28,824)</td>
					<td>PMID: 17898773</td>
				</tr>
				<tr>
					<td><b>ClinVar - June 12<sup>th</sup>, 2015</b></td>
					<td>98,289 (85,835)</td>
					<td>PMID: 24234437</td>
				</tr>
				<tr>
					<td><b>OMIM variation - June 12<sup>th</sup>, 2015</b></td>
					<td>18,854 (17,586)</td>
					<td><a href="http://omim.org/">http://omim.org/</a></td>
				</tr>
				<tr>
					<td><b>UniProt - June 12<sup>th</sup>, 2015</b></td>
					<td>3,399 (3,210)</td>
					<td>PMID: 24253303</td>
				</tr>
				<tr>
					<td><b>GWAS Catalog - June 12<sup>th</sup>, 2015</b></td>
					<td>17,703 (16,635)</td>
					<td>PMID: 19474294</td>
				</tr>
				<tr>
					<td><b>DrugBank - 4.2</b></td>
					<td>179 (169)</td>
					<td>PMID: 24203711</td>
				</tr>
			</table>
			
			<p><br /><hr /><br /></p>

			<h2><i>SNiPA</i> v2.1 (January 2015)</h2>
			<h3>Important notes for this release</h3>
			<h4>Merged rs identifiers</h4>
			<p>We have included the variant identifiers contained in dbSNP's <a href="http://www.ncbi.nlm.nih.gov/projects/SNP/snp_db_table_description.cgi?t=RsMergeArch">rsMergeArch</a> table, so users can search for rs identifiers that were merged into newer rs numbers. <i>SNiPA cards</i> and the tooltips in the interactive plots now list these alias rs identifiers in addition to the one currently assigned to each variant. Also, an column labeled &quot;RSALIAS&quot; was added to the genomic data sets (available for download <a style="color: rgb(228, 0, 58);" href="data">here</a>).</p>
			<h4>Linkage Disequilibrium Plot</h4>
			<p>Users may optionally specify variants that should be highlighted in the linkage disequilibrium plots.</p>

			<p><br /><hr /><br /></p>
			
			<h2><i>SNiPA</i> v2 (November 2014)</h2>
			<p><strong>Genome assembly:</strong> GRCh37<br/><strong>Ensembl version:</strong> 77<br/><strong>1000 genomes:</strong> phase 3 version 5<br/><br/>
			</p>
			<h3>Important notes for this release</h3>
			<h4>Genome assembly</h4>
			<p><i>SNiPA</i>'s data model is fully position-based. It would therefore be possible to update to the new GRCh38 genome assembly. However, as most annotation sets contained in <i>SNiPA</i> are not yet available for this assembly, we decided to stick to the old but fully annotated GRCh37 assembly in this version. This has some implications which are referred to in the following sections.
			</p>
			<h4><i>SNiPA</i> gene build</h4>
			<p>The current Ensembl gene build (GENCODE 21) is based on GRCh38, a mapping to GRCh37 is not provided. We updated gene information as known from the previous <i>SNiPA</i> version, used the <a href="https://genome.ucsc.edu/cgi-bin/hgLiftOver">UCSC liftOver tool</a> (command line executable) to convert genome coordinates, and retained all genes that could be mapped to the old genome assembly. The new <i>SNiPA</i> gene build contains 59,006 entries (N<sub>diff</sub> to <i>SNiPA</i> v1 = +1,764).
			</p>
			<h4><i>SNiPA</i> regulatory build</h4>
			<p>The current Ensembl regulatory build (ENCODE) is based on GRCh38, a mapping to GRCh37 is not provided for batch retrieval. We updated information on regulatory elements from ENCODE as known from the previous <i>SNiPA</i> version, used the <a href="https://genome.ucsc.edu/cgi-bin/hgLiftOver">UCSC liftOver tool</a> (command line executable) to convert genome coordinates, and retained all elements that could be mapped to the old genome assembly. Combined with the known datasets for promoter and enhancer regions, the new <i>SNiPA</i> regulatory build contains 1,127,068 elements (N<sub>diff</sub> to <i>SNiPA</i> v1 = -109,063). The lower number of regulatory elements is due to the new regulatory build of Ensembl where several regulatory clusters have been merged.
			</p>
			<h4><i>SNiPA</i> variant set</h4>
			<p>We have used the new 1000 genomes release (phase 3 version 5) in <i>SNiPA</i> v2. This release has more than doubled the number of available variants. We want to point the user to the <a href="http://www.1000genomes.org/data#DataUse">data usage policy of the 1000 genomes project</a>. 
			</p><p>dbSNP identifiers are not yet completely included in the 1000 genomes data files, but autosomal variants have already been integrated in <a href="http://www.ncbi.nlm.nih.gov/mailman/htdig/dbsnp-announce/2014q4/000147.html">dbSNP build 142</a>. We downloaded dbSNP mapped to GRCh37 and merged both data sets. As X-chromosomal 1000 genomes markers were released after dbSNP 142 was created, we only supply those variants which could be mapped to a dbSNP rs-identifier.
			</p><table class="docutable" style="width: 40%;">
				<tr><td style="text-align: center; font-weight: bold;">population</td><td style="text-align: center; font-weight: bold;">rs-count</td></tr>
				<tr><td>African (AFR)</td><td>39,581,182</td></tr>
				<tr><td>American (AMR)</td><td>26,474,088</td></tr>
				<tr><td>East Asian (EAS)</td><td>22,128,163</td></tr>
				<tr><td>European (EUR)</td><td>22,541,970</td></tr>
				<tr><td>South Asian (SAS)</td><td>24,854,259</td></tr>
				<tr><td><strong>total</strong> (unique)</td><td><strong>78,471,927</strong></td></tr>
			</table>
			<p><i>SNiPA</i> allows to combine all annotation releases with all variant sets. However, annotation data of <i>SNiPA</i> v1 (Ensembl v. 75) is not available for the new variant set. Therefore, if you use 1000 genomes phase 3 version 5 data and combine it with Ensembl 75 annotations, then variants not contained in phase 1 version 3 will show no annotations.</p>
			<h4><i>SNiPA</i> annotation updates</h4>
			<p>The Ensembl VEP tool only features full annotation data for the new GRCh38 genome assembly. Therefore, <i>SNiPA</i>'s annotation workflow had to be adjusted to provide both all the annotation data from the previous release and simultaneously a mapping to the newest gene and regulatory builds of Ensembl. To achieve that, we did effect predictions for both assemblies. Our custom annotation program then merged the VEP output for both assemblies using the variant mapping provided by dbSNP build 142 to again yield a full <i>SNiPA</i> build for GRCh37.</p>
			<p>All phenotype annotation sets have been updated to Ensembl version 77. OrphaData and the GWAS Catalog have been accessed at October 20<sup>th</sup> 2014.</p>
			
			<h5>Variant
				associations &amp; annotations:</h5>
			<br/>
			<table class="docutable">
				<tr>
					<td><b>Source</b></td>
					<td><b>N (unique)</b></td>
					<td><b>Reference</b></td>
				</tr>
				<tr>
					<td><b>HGMD</b></td>
					<td>35,326 (31,770)</td>
					<td>PMID: 24077912</td>
				</tr>
				<tr>
					<td><b>dbGaP</b></td>
					<td>41,426 (28,824)</td>
					<td>PMID: 17898773</td>
				</tr>
				<tr>
					<td><b>ClinVar</b></td>
					<td>89,522 (87,923)</td>
					<td>PMID: 24234437</td>
				</tr>
				<tr>
					<td><b>OMIM variation</b></td>
					<td>9,595 (8,968)</td>
					<td><a href="http://omim.org/">http://omim.org/</a></td>
				</tr>
				<tr>
					<td><b>UniProt</b></td>
					<td>3,573 (3,366)</td>
					<td>PMID: 24253303</td>
				</tr>
				<tr>
					<td><b>GWAS Catalog</b></td>
					<td>16,342 (15,343)</td>
					<td>PMID: 19474294</td>
				</tr>
				<tr>
					<td><b>DrugBank 4.0</b></td>
					<td>179 (169)</td>
					<td>PMID: 24203711</td>
				</tr>
			</table>
			
			<h5>Gene associations: </h5>
			<br/>
			<table class="docutable">
				<tr>
					<td><b>Source</b></td>
					<td><b>N (unique)</b></td>
					<td><b>Reference</b></td>
				</tr>
				<tr>
					<td><b>DECIPHER</b></td>
					<td>1,795 (1,795)</td>
					<td><a href="http://decipher.sanger.ac.uk/">http://decipher.sanger.ac.uk/</a></td>
				</tr>
				<tr>
					<td><b>OMIM gene</b></td>
					<td>5,055 (5,051)</td>
					<td><a href="http://omim.org/">http://omim.org/</a></td>
				</tr>
				<tr>
					<td><b>OrphaNet</b></td>
					<td>5,684 (5,684)</td>
					<td><a href="http://orpha.net/">http://orpha.net/</a></td>
				</tr>
			</table>
			
			<h4><i>SNiPA</i> data release</h4>
			<p>We have decided to make all data contained in <i>SNiPA</i> available to the community. Please refer to the <a href="data/README.txt">README</a> for details on folder structure and data formats.<br/><br/>&rarr; <a style="color: rgb(228, 0, 58);" href="data">Data access</a></p>
			
			<br/><hr /><br/>
			<h2><i>SNiPA</i> v1 (July 2014)</h2>
			<p><strong>Genome assembly: </strong>GRCh37<br/><strong>Ensembl version:</strong> 75<br/><strong>1000 genomes:</strong> phase 1 version 3<br/><br/>
			This is the original release of <i>SNiPA</i> as described in the original publication and the <a href="?task=documentation" style="color: rgb(228,0,58);">documentation</a>. 
			
			<h4><i>SNiPA</i> variant set</h4>
			<p>This version contains all bi-allelic variants present in 1000 genomes project, phase 1 version 3. These are the variant counts for the individual superpopulations:</p>
			<table class="docutable" style="width: 40%;">
				<tr><td style="text-align: center; font-weight: bold;">population</td><td style="text-align: center; font-weight: bold;">rs-count</td></tr>
				<tr><td>African (AFR)</td><td>25,837,142</td></tr>
				<tr><td>American (AMR)</td><td>20,097,916</td></tr>
				<tr><td>Asian (ASN)</td><td>15,012,236</td></tr>
				<tr><td>European (EUR)</td><td>17,361,202</td></tr>
			</table>
			<h4>EnsEMBL release 75</h4>
			SNiPA includes a total count of 57,241 genes with 195,586 associated transcripts and 104,763
				protein products and &gt;500,000 regulatory feature clusters, some of which are
				associated with JASPAR motifs.
			
			<h5>Variant
				associations &amp; annotations:</h5>
				
				<p>
				<table class="docutable">
				 <tr>
				  <td>
				  <p><b>Source</b></p>
				  </td>
				  <td>
				  <p><b>N (unique)</b></p>
				  </td>
				  <td>
				  <p><b>Reference</b></p>
				  </td>
				 </tr>
				 <tr>
				  <td>
				  <p><b>HGMD</b></p>
				  </td>
				  <td>
				  <p>93,758
				  (86,491)</p>
				  </td>
				  <td>
				  <p>PMID: 24077912</p>
				  </td>
				 </tr>
				 <tr>
				  <td>
				  <p><b>dbGaP</b></p>
				  </td>
				  <td>
				  <p>41,181
				  (33,819)</p>
				  </td>
				  <td>
				  <p>PMID: 17898773</p>
				  </td>
				 </tr>
				 <tr>
				  <td>
				  <p><b>ClinVar</b></p>
				  </td>
				  <td>
				  <p>47,315
				  (44,141)</p>
				  </td>
				  <td>
				  <p>PMID: 24234437</p>
				  </td>
				 </tr>
				 <tr>
				  <td>
				  <p><b>OMIM
				  variation</b></p>
				  </td>
				  <td>
				  <p>19,911 (19,184)</p>
				  </td>
				  <td>
				  <p><a
				  href="http://omim.org/">http://omim.org/</a></p>
				  </td>
				 </tr>
				 <tr>
				  <td>
				  <p><b>UniProt</b></p>
				  </td>
				  <td>
				  <p>5,055
				  (4,850)</p>
				  </td>
				  <td>
				  <p>PMID: 24253303</p>
				  </td>
				 </tr>
				 <tr>
				  <td>
				  <p><b>GWAS Catalog</b></p>
				  </td>
				  <td>
				  <p>15,500
				  (14,513)</p>
				  </td>
				  <td>
				  <p>PMID: 19474294</p>
				  </td>
				 </tr>
				 <tr>
				  <td>
				  <p><b>DrugBank 4.0</b></p>
				  </td>
				  <td>
				  <p>179 (169)</p>
				  </td>
				  <td>
				  <p>PMID: 24203711</p>
				  </td>
				 </tr>
				</table>
				</p>
				
				<h5>Gene
				associations: </h5>
				
				<p>
				<table class="docutable">
					 <tr>
					  <td>
					  <p><b>Source</b></p>
					  </td>
					  <td>
					  <p><b>N (unique)</b></p>
					  </td>
					  <td>
					  <p><b>Reference</b></p>
					  </td>
					 </tr>
					 <tr>
					  <td>
					  <p><b>DECIPHER</b></p>
					  </td>
					  <td>
					  <p>2,144
					  (2,143)</p>
					  </td>
					  <td>
					  <p><a
					  href="http://decipher.sanger.ac.uk/">http://decipher.sanger.ac.uk/</a> </p>
					  </td>
					 </tr>
					 <tr>
					  <td>
					  <p><b>OMIM gene</b></p>
					  </td>
					  <td>
					  <p>5,775 (5,775)</p>
					  </td>
					  <td>
					  <p><a
					  href="http://omim.org/">http://omim.org/</a> </p>
					  </td>
					 </tr>
					 <tr>
					  <td>
					  <p><b>OrphaNet</b></p>
					  </td>
					  <td>
					  <p>5,705 (5,675)</p>
					  </td>
					  <td>
					  <p><a
					  href="http://orpha.net/">http://orpha.net/</a></p>
					  </td>
					 </tr>
					</table>
				</p>
				
			
			</p>
