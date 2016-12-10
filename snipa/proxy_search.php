<?php
	include("backend/snipaWhitelists.php");

?>
			<div class="helptext" id="helptext"><span id="helptext-close" class="helptext-close">close</span>
				<div id="helptext-content" class="helptext-content">
					Here you can query for variants that are in linkage disequilibriums to other variants within a 500 kb window. To get functional annotations for the variants listed in the results table, click on the <img src="frontend/img/common_link_annotation.png" alt="" /> symbol.
				</div>
			</div>
		
			<div id="proxy-form-container" style="padding-top: 10px; padding-bottom: 10px;">
			
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
							<th colspan="3" class="inputbox">Sentinel variants / Genetic Locus / Chromosomal Region</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td style="width: 130px;">Input Type:</td>
							<td style="width: 280px;">
								<div id="selection_snps_container">
									<input type="radio" id="selection_snps_sentinels" value="snps" name="selection_snps"  checked="checked" /><label for="selection_snps_sentinels">Variants</label>
									<input type="radio" id="selection_snps_gene" value="gene" name="selection_snps" /><label for="selection_snps_gene">Gene</label>
									<input type="radio" id="selection_snps_region" value="region" name="selection_snps" /><label for="selection_snps_region">Region</label>
								</div>
							</td>
							<td class="description">
								You can either manually input one or more sentinels or use all available variants in a gene or a chromosomal region. 
							</td>
						</tr>
						<tr id="snps_sentinels">
							<td style="width: 130px;">Variants:</td>
							<td style="width: 280px;">
								<textarea id="snps_sentinels_input" class="inputbox" cols="" rows="" style="height: 100px;"></textarea>
							</td>
							<td class="description">
								Enter one rs-identifier per line.<br /><br />
								If you have already added variants to GenoQ's clipboard, these are available as a preselection.<br />
								
								<br /><br />
								<button type="button" class="example-button"  onclick="$('#snps_sentinels_input').val('rs2066938\nrs13391552\nrs174547\nrs887829\nrs211718\nrs6558295\nrs603424\nrs780094\nrs1495743\nrs17277546\nrs612169\nrs4481233\nrs9332998\nrs2216405\nrs2652822\nrs662138\nrs4149081\nrs503279\nrs4329\nrs477992\nrs2087160\nrs2518049\nrs494562\nrs2023634\nrs2403254\nrs10799701\nrs6499165\nrs4253252\nrs2657879\nrs7200543\nrs272889\nrs12670403\nrs8396\nrs9393903\nrs7094971\nrs10518693\nrs7760535')">Load example</button>
							</td>
						</tr>
						<tr id="snps_gene">
							<td style="width: 130px;">Gene locus:</td>
							<td style="width: 280px;">
								<input id="gene_symbol" type="text" class="inputbox" />
							</td>
							<td class="description">
								Enter an ENSEMBL gene identifier (e.g. ENSG00000015532).<br />You may also enter identifiers from other gene resources and then select the appropriate ENSEMBL gene identifier from the list.<br /> GenoQ will perform a proxy search for all sentinel SNPs covered by that gene. 
								<br /><br />
								<button type="button" class="example-button" onclick="$('#gene_symbol').val('ENSG00000149485')">Load example</button>
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
								The maximum range is 1 Mb.
								<br /><br />
								<button type="button" class="example-button" onclick="$('#snps_region_chr').val('11'); $('#snps_region_begin').val('61567099'); $('#snps_region_end').val('61596790')">Load example</button>
							</td>
						</tr>
					</tbody>
				</table>
				
				<table class="inputbox">
					<thead>
						<tr>
							<th colspan="3" class="inputbox">Output options</th>
						</tr>
					</thead>
					<tbody>
						<tr>
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
								Set the threshold for the minimum correlation coefficient between sentinels and proxies. Lower values will give you more proxies, but will also slow down the processing of your job. We suggest that you keep the threshold above 0.5.
							</td>
						</tr>
						<tr>
							<td style="width: 130px;">Annotations:</td>
							<td style="width: 280px;">
								<div id="incl_funcann_container">
									<input type="radio" id="incl_funcann_0" value="0" name="incl_funcann" checked="checked" /><label for="incl_funcann_0">no</label>
									<input type="radio" id="incl_funcann_1" value="1" name="incl_funcann" /><label for="incl_funcann_1">yes</label>
								</div>
							</td>
							<td class="description">
								Select &quot;Yes&quot; if you want to include functional annotations in the query results.<br />This option will significantly increase the amount of time it takes to process your job, especially when you input many individual sentinels.
							</td>
						</tr>
						<tr>
							<td style="width: 130px;">Include sentinels:</td>
							<td style="width: 280px;">
								<div id="incl_sentinel_container">
									<input type="radio" id="incl_sentinel_0" value="0" name="incl_sentinel" /><label for="incl_sentinel_0">no</label>
									<input type="radio" id="incl_sentinel_1" value="1" name="incl_sentinel" checked="checked" /><label for="incl_sentinel_1">yes</label>
								</div>
							</td>
							<td class="description">
								When enabled, the sentinel variants are included in the results table. 
							</td>
						</tr>
						<tr>
							<td style="width: 130px;">Interactive table:</td>
							<td style="width: 280px;">
								<div id="dynamic_tables_container">
									<input type="radio" id="dynamic_tables_0" value="0" name="dynamic_tables" /><label for="dynamic_tables_0">no</label>
									<input type="radio" id="dynamic_tables_1" value="1" name="dynamic_tables" checked="checked" /><label for="dynamic_tables_1">yes</label>
								</div>
							</td>
							<td class="description">
								By default, GenoQ presents the results in sortable and searchable tables. Select &quot;no&quot; if you experience performance issues. If the result of your query exceeds 25,000 lines, this option will automatically be disabled.
							</td>
						</tr>
						<tr>
							<td style="width: 130px;">Download:</td>
							<td style="width: 280px;">
								<div id="enable_download_container">
									<input type="radio" id="enable_download_0" value="0" name="enable_download" /><label for="enable_download_0">no</label>
									<input type="radio" id="enable_download_1" value="1" name="enable_download" checked="checked" /><label for="enable_download_1">yes</label>
								</div>
							</td>
							<td class="description">
								Select &quot;Yes&quot; if you want GenoQ to provide the results as a comma-separated text file. You can download the file in the &quot;Report&quot; tab.
							</td>
						</tr>
				</tbody>
				</table>
				<input type="hidden" id="pairwise" value="0" />
				<p style="width: 100%; text-align: right;"><button type="button" id="submit-button">Start processing this job</button></p>
			
			</div>			
			<div id="progress-dialog">
				<div id="progressbar"><div class="progress-label"></div></div>
				<div id="message"></div>
			</div>
			
			<div id="proxy-tabs" class="plots-tabs" style="padding-top: 10px; padding-bottom: 10px">
				<ul id="proxy-tabs-header" class="plots-tabs-header">
					<li class="plots-tabs-tab"><a href="#proxy-tabs-results">Proxies</a></li>
					<li class="plots-tabs-tab"><a href="#proxy-tabs-snpinfo">SNP annotations</a></li>
					<li class="plots-tabs-tab"><a href="#proxy-tabs-report">Report</a></li>
				</ul>
				<div id="proxy-snpdetails-container" class= "plot-snpdetails"><div id="proxy-snpdetails-menu"></div></div>
				<div id="proxy-tabs-body" class="plots-tabs-body">
					<div id="proxy-tabs-results" class="plots-tabs-body-content" style="overflow: auto;">
						<div id="table-coltoggle-header"></div>
						<table id='proxy-results-table'><tr><td></td></tr></table>
					</div>
					<div id="proxy-tabs-snpinfo" class="plots-tabs-body-content">
						<span id="nosnpinfo">Click on the arrow (<img src="frontend/img/common_link_annotation.png" alt="" />) next to a proxy SNP in the results table. Detailed annotations for this SNP will then be displayed in this section.</span> 
						<div id="proxy-snpinfo-accordion"></div> 
					</div>
					<div id="proxy-tabs-report" class="plots-tabs-body-content"></div>
				</div>
			</div>
			
			
