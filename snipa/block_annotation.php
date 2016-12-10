<?php
	include("backend/snipaWhitelists.php");

?>
		
		<div class="helptext" id="helptext"><span id="helptext-close" class="helptext-close">close</span>
			<div id="helptext-content" class="helptext-content">
				The block annotation provides a summary of the annotations for a set of variants within a chromosomal region.
			</div>
		</div>
	
		<div id="form-container" style="padding-top: 10px; padding-bottom: 10px;">
			<table class="inputbox">
				<thead>
					<tr>
						<th colspan="3" class="inputbox">Genome Assembly, Variant Set, Population, and Genome Annotation</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td style="width: 130px;">Genome assembly:</td>
						<td style="width: 280px;">
							<select id="dataset-genomerelease" class="inputbox">
								<option value="">--</option>
							</select>
						</td>
						<td class="description">
							Chromosome coordinates (and thus all genetic elements) are mapped to the selected human reference assembly.
						</td>
					</tr>
					<tr>
						<td style="width: 130px;">Variant set:</td>
						<td style="width: 280px;">
							<select id="dataset-referenceset" class="inputbox">
								<option value="">--</option>
							</select>
						</td>
						<td class="description">
							Linkage disequilibrium data and allele frequencies are computed for the selected variant set (and population where applicable).
						</td>
					</tr>
					<tr>
						<td style="width: 130px;">Population:</td>
						<td style="width: 280px;">
							<select id="dataset-population" class="inputbox">
								<option value="">--</option>
							</select>
						</td>
						<td class="description">
							If a variant set contains more than one population, select the one that fits your study population best. 
						</td>
					</tr>
					<tr>
						<td style="width: 130px;">Genome annotation:</td>
						<td style="width: 280px;">
							<select id="dataset-annotation" class="inputbox">
								<option value="">--</option>
							</select>
						</td>
						<td class="description">
							Genetic elements are annotated based on data of the selected annotation dataset. 
						</td>
					</tr>
				</tbody>
			</table>
			
			<table class="inputbox">
				<thead>
					<tr>
						<th colspan="3" class="inputbox">Variants / LD / Genetic Locus / Chromosomal Region</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td style="width: 130px;">Input Type:</td>
						<td style="width: 280px;">
							<div id="selection_snps_container">
								<input type="radio" id="selection_snps_variants" value="variants" name="selection_snps" /><label for="selection_snps_variants">Variants</label>
								<input type="radio" id="selection_snps_ld" value="ld" name="selection_snps" checked="checked" /><label for="selection_snps_ld">LD</label>
								<input type="radio" id="selection_snps_gene" value="gene" name="selection_snps" /><label for="selection_snps_gene">Gene</label>
								<input type="radio" id="selection_snps_region" value="region" name="selection_snps" /><label for="selection_snps_region">Region</label>
							</div>
						</td>
						<td class="description">
							Enter a variant's rs-identifier to get a summary for this variant and all variants in linkage disequilibrium.<br />
							Alternatively, enter a list of variants, a gene identifier or a chromosomal region.
						</td>
					</tr>
					<tr id="snps_variants">
						<td style="width: 130px;">Variants:</td>
						<td style="width: 280px;">
							<textarea id="snps_variants_input" class="inputbox" cols="" rows="" style="height: 100px"></textarea>
						</td>
						<td class="description">
							Note that all variants have to be on the <strong>same chromosome</strong>. Other variants will be ingnored for annotation.<br /><br />
							Enter one rs-identifier per line. 
							<br /><br />
							If you have already added variants to GenoQ's clipboard, these are available as a preselection.<br /><br />
							<button type="button" class="example-button"  onclick="$('#snps_variants_input').val('rs174564\nrs174566\nrs174567\nrs174568\nrs99780\nrs1535\nrs174574\nrs174576\nrs174577\nrs174578\nrs174580\nrs174581\nrs174583\n')">Load example</button>
						</td>
					</tr>					
					<tr id="snps_ld">
						<td style="width: 130px;">Sentinel variant:</td>
						<td style="width: 280px;">
							<input id="ld_sentinel" class="inputbox" />
						</td>
						<td class="description">
							Enter a variant's rs-identifier (e.g. rs174547).<br />If you have already added variants to SNiPA's clipboard, these are available as a preselection.
							<br /><br />
							<button type="button" class="example-button" onclick="$('#ld_sentinel').val('rs174547')">Load example</button>
						</td>
					</tr>
					<tr id="snps_ld_r2">
						<td style="width: 130px;">LD threshold:</td>
						<td style="width: 280px;">
							<div style="width: 95%;">
									<select name="ld_threshold" id="ld_threshold">
										<option>0.1</option>
										<option>0.2</option>
										<option>0.3</option>
										<option>0.4</option>
										<option>0.5</option>
										<option>0.6</option>
										<option>0.7</option>
										<option selected="selected">0.8</option>
										<option>0.9</option>
										<option>1.0</option>
									</select>
							</div>
						</td>
						<td class="description">
							Set the threshold for the minimum correlation coefficient between the variant given above and its proxy variants. Lower values will give you more proxies, but will also slow down the processing of your job. We recommend a value above 0.5.
						</td>
					</tr>
					<tr id="snps_gene">
						<td style="width: 130px;">Gene:</td>
						<td style="width: 280px;">
							<input id="gene_symbol" type="text" class="inputbox" />
						</td>
						<td class="description">
							Enter an ENSEMBL gene identifier (e.g. ENSG00000015532).<br />You may also enter identifiers from other gene resources and then select the appropriate ENSEMBL gene identifier from the list.
							<br /><br />
							<button type="button" class="example-button"  onclick="$('#gene_symbol').val('ENSG00000149485')">Load example</button>
						</td>
					</tr>
					<tr id="snps_region">
						<td style="width: 130px;">Chromosomal region:</td>
						<td style="width: 280px;">
								<table style="margin: 0; background: none; border: none;">
									<tr>
										<td>Chromosome:</td>
										<td><select id="snps_region_chr" name="snps_region_chr" class="inputbox">
											<option>1</option>
											<option>2</option>
											<option>3</option>
											<option>4</option>
											<option>5</option>
											<option>6</option>
											<option>7</option>
											<option>8</option>
											<option>9</option>
											<option>10</option>
											<option>11</option>
											<option>12</option>
											<option>13</option>
											<option>14</option>
											<option>15</option>
											<option>16</option>
											<option>17</option>
											<option>18</option>
											<option>19</option>
											<option>20</option>
											<option>21</option>
											<option>22</option>
											<option>X</option>
										</select>
							  		</td>
								  </tr>
								  <tr>
									<td>Begin:</td>
									<td><input id="snps_region_begin" type="text" class="inputbox" /></td>
								  </tr>
								  <tr>
									<td>End:</td>
									<td><input id="snps_region_end" type="text" class="inputbox" /></td>
								  </tr>
								</table>
						</td>
						<td class="description">
							Maximum window size is 1 Mb.<br /><br />
							<button type="button" class="example-button" onclick="$('#snps_region_chr').val('11'); $('#snps_region_begin').val('61496790'); $('#snps_region_end').val('61596790')">Load example</button>
						</td>
					</tr>
				</tbody>
			</table>
			<table class="inputbox" style="display: none;">
				<thead>
					<tr>
						<th colspan="3" class="inputbox">Output options</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td style="width: 130px;">SNP annotations:</td>
						<td style="width: 280px;">
							<div id="incl_funcann_container">
								<input type="radio" id="incl_funcann_0" value="0" name="incl_funcann" /><label for="incl_funcann_0">no</label>
								<input type="radio" id="incl_funcann_1" value="1" name="incl_funcann" checked="checked" /><label for="incl_funcann_1">yes</label>
							</div>
						</td>
						<td class="description">
							Select &quot;Yes&quot; if you want the results to include functional annotations for each SNP. Note that this could significantly increase the amount of time it takes to process your job!
						</td>
					</tr>
			</tbody>
			</table>
			<p style="width: 100%; text-align: right;"><button type="button" id="submit-button">Start processing this job</button></p>
		</div>
		
		
		<div id="tabs" class="plots-tabs" style="padding-top: 10px; padding-bottom: 10px">
			<ul id="tabs-header" class="plots-tabs-header">
				<li class="plots-tabs-tab"><a href="#tabs-annotation">Block annotation</a></li>
			</ul>
			<div id="tabs-body" class="plots-tabs-body">
				<div id="tabs-annotation" class="plots-tabs-body-content" style="overflow: auto;">
					<div id='annotationcontainer'>
						
					</div>
				</div>
			</div>
		</div>

		<div id="snpdetails-container" class= "plot-snpdetails"><div id="snpdetails-menu"></div></div>
		<div id="progress-dialog">
			<div id="progressbar"><div class="progress-label"></div></div>
			<div id="message"></div>
		</div>
			
			
