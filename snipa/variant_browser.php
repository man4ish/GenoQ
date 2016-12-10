<?php
	include("backend/snipaWhitelists.php");

?>
		
		<div class="helptext" id="helptext"><span id="helptext-close" class="helptext-close">close</span>
			<div id="helptext-content" class="helptext-content">
				The variant browser visualizes the functional annotation of variants within a 200 kb window. The position on the y-axis represents the estimated function of a variant, ranging from unknown (bottom) to direct transcript effects (top). 
				Use the karyogram to quickly navigate to a chromosomal region. Blue spikes in the karyogram indicate regions containing a high proportion of functional variants.
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
						<th colspan="3" class="inputbox">Variant / Transcript / Chromosomal Position</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td style="width: 130px;">Input Type:</td>
						<td style="width: 280px;">
							<div id="selection_snps_container">
								<input type="radio" id="selection_snps_sentinels" value="snps" name="selection_snps"  checked="checked" /><label for="selection_snps_sentinels">Variant</label>
								<input type="radio" id="selection_snps_gene" value="gene" name="selection_snps" /><label for="selection_snps_gene">Gene</label>
								<input type="radio" id="selection_snps_region" value="region" name="selection_snps" /><label for="selection_snps_region">Position</label>
							</div>
						</td>
						<td class="description">
							Enter the variant, gene or chromosomal position, to which the variant browser window should be centered.
						</td>
					</tr>
					<tr id="snps_sentinels">
						<td style="width: 130px;">Variant:</td>
						<td style="width: 280px;">
							<input id="sentinel" class="inputbox" />
						</td>
						<td class="description">
							Enter a variant's rs-identifier (e.g. rs174547).<br />If you have already added variants to GenoQ's clipboard, these are available as a preselection.
							<br /><br />
							<button type="button" class="example-button" onclick="$('#sentinel').val('rs174583')">Load example</button>
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
						<td style="width: 130px;">Chromosomal position:</td>
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
								<td>Position:</td>
								<td><input id="snps_region_position" type="text" class="inputbox" /></td>
							  </tr>
							</table>
						</td>
						<td class="description">
							<br />
							<button type="button" class="example-button" onclick="$('#snps_region_chr').val('11'); $('#snps_region_position').val('61496790')">Load example</button>
						</td>
					</tr>
				</tbody>
			</table>
			<p style="width: 100%; text-align: right;"><button type="button" id="submit-button">Start processing this job</button></p>
		</div>
		
		
		<div id="tabs" class="plots-tabs" style="padding-top: 10px; padding-bottom: 10px">
			<ul id="tabs-header" class="plots-tabs-header">
				<li class="plots-tabs-tab"><a href="#tabs-browser">Variant browser</a></li>
				<li class="plots-tabs-tab"><a href="#tabs-snpinfo">Variant annotations</a></li>
			</ul>
			<div id="snpdetails-container" class="plot-snpdetails"><div id="snpdetails-menu"></div></div>
			<div id="loading-dialog">
				<div id="loading-bar"></div><div id="loading-message"></div>
			</div>
			<div id="tabs-body" class="plots-tabs-body">
				<div id="tabs-browser" class="plots-tabs-body-content" style="overflow: auto;">
					<div id="karyogramcontainer" style="width: 100%; height: 100px;"></div>
					<div style="width: 100%;">
						<div style="float: left;"><button type="button" class="scroll-left" id="scroll-upstream-100"></button>&nbsp;<button type="button" class="scroll-left" id="scroll-upstream-50"></button></div>
						<div style="float: right;"><button type="button" class="scroll-right" id="scroll-downstream-50"></button>&nbsp;<button type="button" class="scroll-right" id="scroll-downstream-100"></button></div>
						<div style="clear: both;"></div>
					</div>
					<div id='detailcontainer' style="width: 100%; height: 600px;"></div>
				</div>
				<div id="tabs-snpinfo" class="plots-tabs-body-content">
					<span id="nosnpinfo">Please click on a variant in the browser window to get detailed annotations.</span> 
					<div id="snpinfo-accordion"></div> 
				</div>
			</div>
		</div>

		
		<div id="progress-dialog">
			<div id="progressbar"><div class="progress-label"></div></div>
			<div id="message"></div>
		</div>
			
			
