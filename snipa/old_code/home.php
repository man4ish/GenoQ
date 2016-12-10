
<?php
?>
			

			<h3>DWelcome to SN<span style="color: rgb(228,0,58);">i</span>PA!</h3>
			<p>SNiPA offers both functional annotations and linkage disequilibrium information for bi-allelic genomic variants (SNPs and SNVs). SNiPA combines LD data based on the <a href="http://www.1000genomes.org">1000 Genomes Project</a> with various annotation layers, such as gene annotations, phenotypic trait associations, and expression-/metabolic quantitative trait loci. See the <a href="?task=documentation">documentation</a> for all data sources integrated into SNiPA. For information on updates and new releases, see the <a href="?task=release_notes">Release Notes</a>.
		
			<table class="frontpage-button-container">
				<tr>
					<td>
						<a href="?task=variant_browser" id="home_icon_variantbrowser" class="frontpage-button" style="background-image: url(frontend/img/home_icon_variantbrowser.png);">Variant Browser<br />
							<span class="frontpage-button-description">Explore the functional annotations of variants.</span>
						</a>
					</td>
					<td>
						<a href="?task=association_maps" id="home_icon_gwas"  class="frontpage-button" style="background-image: url(frontend/img/home_icon_gwas.png);">Association Maps<br />
							<span class="frontpage-button-description">Map trait associations from NHGRI's GWAS Catalog to karyograms.</span>
						</a>
					</td>
				</tr>
				<tr>
					<td>
						<a href="?task=snp_annotation" id="home_icon_snpannotation" class="frontpage-button" style="background-image: url(frontend/img/home_icon_snpannotation.png);">Variant Annotation<br />
							<span class="frontpage-button-description">Access detailed variant annotations.</span>
						</a>
					</td>
					<td>
						<a href="?task=block_annotation" id="home_icon_ldannotation" class="frontpage-button" style="background-image: url(frontend/img/home_icon_ldannotation.png);">Block Annotation<br />
							<span class="frontpage-button-description">Summarize variant annotations within LD blocks or chromosomal regions.</span>
						</a>
					</td>
				</tr>
				<tr>
					<td>
						<a href="?task=regional_association_plot" id="home_icon_raplot" class="frontpage-button" style="background-image: url(frontend/img/home_icon_raplot.png);">Regional Association Plot<br />
							<span class="frontpage-button-description">Visualize and annotate the results of genetic association studies.</span>
						</a>
					</td>
					<td>
						<a href="?task=ld_plot" id="home_icon_ldplot" class="frontpage-button" style="background-image: url(frontend/img/home_icon_ldplot.png);">Linkage Disequilibrium Plot<br />
							<span class="frontpage-button-description">Combine LD data and annotations in an interactive plot.</span>
						</a>
					</td>
				</tr>
				<tr>
					<td>
						<a href="?task=proxy_search" id="home_icon_proxysearch" class="frontpage-button" style="background-image: url(frontend/img/home_icon_proxysearch.png);">Proxy Search<br />
							<span class="frontpage-button-description">Find variants that are linked to other variants by LD.</span>
						</a>
					</td>
					<td>
						<a href="?task=pairwise_ld" id="home_icon_pairwiseld" class="frontpage-button" style="background-image: url(frontend/img/home_icon_pairwiseld.png);">Pairwise Linkage Disequilibrium<br />
							<span class="frontpage-button-description">Determine the pairwise LD for two or more variants.</span>
						</a>
					</td>
				</tr>
				<tr>
					<td>
						<a href="?task=documentation" id="home_icon_documentation" class="frontpage-button" style="background-image: url(frontend/img/home_icon_documentation.png);">Documentation<br />
							<span class="frontpage-button-description">See how to use SNiPA and its interactive features.</span>
						</a>
					</td>
					<td>
					</td>
				</tr>
			</table>
			<!-- Replace icons with png versions if browser is capable -->
			<script>
				if (Modernizr.svgasimg == true) { 
					$('#home_icon_proxysearch').css('background-image','url(frontend/img/home_icon_proxysearch.svg)');
					$('#home_icon_pairwiseld').css('background-image','url(frontend/img/home_icon_pairwiseld.svg)');
					$('#home_icon_raplot').css('background-image','url(frontend/img/home_icon_raplot.svg)');
					$('#home_icon_ldplot').css('background-image','url(frontend/img/home_icon_ldplot.svg)');
					$('#home_icon_snpannotation').css('background-image','url(frontend/img/home_icon_snpannotation.svg)');
					$('#home_icon_ldannotation').css('background-image','url(frontend/img/home_icon_ldannotation.svg)');
					$('#home_icon_gwas').css('background-image','url(frontend/img/home_icon_gwas.svg)');
					$('#home_icon_variantbrowser').css('background-image','url(frontend/img/home_icon_variantbrowser.svg)');
					$('#home_icon_documentation').css('background-image','url(frontend/img/home_icon_documentation.svg)');

				}
			</script>
			<hr/>
			<h3>Current release</h3>
			<h4><i>SNiPA</i> v3.1 (released November 2015)</h4>
			<p><strong>Genome assembly:</strong> GRCh37.p13&nbsp;&nbsp;&nbsp;<strong>Ensembl version:</strong> 82&nbsp;&nbsp;&nbsp;<strong>1000 genomes:</strong> phase 3 version 5<br/>
			</p>
			<p>SNiPA now includes the new GTEx V6 data (released 2015-10-19). For further information, see the <a href="?task=release_notes">release notes</a>, <a href="?task=documentation">documentation</a>, or check out the <a target="_blank" href="http://gtexportal.org">GTEx portal</a>.</p>
			<hr/>
			<p>
			Did you use SNiPA for a publication? We would appreciate if you cite us:<br /><br />
			Arnold M<sup>&Dagger;</sup>, Raffler J<sup>&Dagger;</sup>, Pfeufer A, Suhre K, and Kastenm&uuml;ller, G. <a href="http://bioinformatics.oxfordjournals.org/content/31/8/1334">SNiPA: an interactive, genetic variant-centered annotation browser</a>. Bioinformatics first published online November 26, 2014 doi:10.1093/bioinformatics/btu779 <span style="font-size: smaller; font-weight: bold;"><a href="http://bioinformatics.oxfordjournals.org/content/31/8/1334">OPEN ACCESS</a></span>.<br />
			<span style="font-size: smaller;"><sup>&Dagger;</sup>These authors contributed equally.</span>
			</p>
			<div style="font-size: smaller;">
				<p>
				If you have feedback, problems or questions regarding SNiPA, feel free to <a href="http://metabolomics.helmholtz-muenchen.de/">contact us</a>. Some of SNiPA's features only work on modern browsers. SNiPA was successfully tested on Internet Explorer &ge; 9, Firefox &ge; 27, Safari &ge; 7, and Chrome &ge; 33.
				</p>
				<p>Please note that SNiPA stores a temporary cookie containing a <a href="http://en.wikipedia.org/wiki/Session_ID">session ID</a> on your computer. This session ID is needed for several SNiPA tools, such as the SNiPA clipboard. The cookie will be deleted from your computer when you close your browser. Any data that you may upload during your site visit will be stored in temporary files on our server. These files are inaccessible to other users and will be deleted irreversibly within 24 hours.</p>
			</div>
			
			
						
<?php
?>
