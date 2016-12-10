<?php
	include("backend/snipaWhitelists.php");

?>
			<div class="helptext" id="helptext"><span id="helptext-close" class="helptext-close">close</span>
				<div id="helptext-content" class="helptext-content">
					You can check whether two or more neighbouring variants (distance &lt; 250 kb) are in linkage disequilibrium. To get functional annotations for the variants listed in the results table, click on the <img src="frontend/img/common_link_annotation.png" alt="" /> symbol.
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
							<th colspan="3" class="inputbox">Sentinels</th>
						</tr>
					</thead>
					<tbody>
						<tr style="display: none;">
							<td style="width: 130px;"></td>
							<td style="width: 280px;">
								<div id="selection_snps_container">
									<input type="radio" id="selection_snps_sentinels" value="snps" name="selection_snps"  checked="checked" /><label for="selection_snps_sentinels">SNPs</label>
								</div>
							</td>
							<td class="description"></td>
						</tr>
						<tr id="snps_sentinels">
							<td style="width: 130px;">Sentinels:</td>
							<td style="width: 280px;">
								<textarea id="snps_sentinels_input" class="inputbox" cols="" rows="" style="height: 100px"></textarea>
							</td>
							<td class="description">
								Enter one rs-identifier per line. 
								<br /><br />
								If you have already added variants to GenoQ's clipboard, these are available as a preselection.<br /><br />
								<button type="button" class="example-button"  onclick="$('#snps_sentinels_input').val('rs174564\nrs174566\nrs174567\nrs174568\nrs99780\nrs1535\nrs174574\nrs174576\nrs174577\nrs174578\nrs174580\nrs174581\nrs174583\n')">Load example</button>
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
								Set the threshold for the minimum correlation coefficient between variants.
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
								Select &quot;Yes&quot; if you want the query results to include functional annotations.<br />WARNING: This option will significantly increase the amount of time it takes to process your job, especially when you input many individual sentinels!
							</td>
						</tr>
						<tr>
							<td style="width: 130px;">Include sentinel(s):</td>
							<td style="width: 280px;">
								<div id="incl_sentinel_container">
									<input type="radio" id="incl_sentinel_0" value="0" name="incl_sentinel" /><label for="incl_sentinel_0">no</label>
									<input type="radio" id="incl_sentinel_1" value="1" name="incl_sentinel" checked="checked" /><label for="incl_sentinel_1">yes</label>
								</div>
							</td>
							<td class="description">
								When enabled, the sentinels are included as a proxy for themselves in the results list. 
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
								By default, GenoQ presents the results in tables that are sortable and searchable. Select &quot;no&quot; if you experience performance issues. If the result of your query exceeds 25,000 lines, this option will automatically be disabled.
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
								Select &quot;Yes&quot; if you want GenoQ to provide the results as a comma-separated text file. You can download the files in the &quot;Report&quot; tab.
							</td>
						</tr>
				</tbody>
				</table>
			
				<input type="hidden" id="pairwise" value="1" />
					<p style="width: 100%; text-align: right;"><button type="button" id="submit-button">Start processing this job</button></p>
			</div>			
			<div id="progress-dialog">
				<div id="progressbar"><div class="progress-label"></div></div>
				<div id="message"></div>
			</div>
			
			<div id="proxy-tabs" class="plots-tabs" style="padding-top: 10px; padding-bottom: 10px">
				<ul id="proxy-tabs-header" class="plots-tabs-header">
					<li class="plots-tabs-tab"><a href="#proxy-tabs-results">Proxies</a></li>
					<li class="plots-tabs-tab"><a href="#proxy-tabs-snpinfo">Variant annotations</a></li>
					<li class="plots-tabs-tab"><a href="#proxy-tabs-report">Report</a></li>
				</ul>
				<div id="proxy-snpdetails-container" class= "plot-snpdetails"><div id="proxy-snpdetails-menu"></div></div>
				<div id="proxy-tabs-body" class="plots-tabs-body">
					<div id="proxy-tabs-results" class="plots-tabs-body-content" style="margin: 3px; padding: 3px; overflow: auto;">
						<table id='proxy-results-table'><tr><td></td></tr></table>
					</div>
					<div id="proxy-tabs-snpinfo" class="plots-tabs-body-content">
						<span id="nosnpinfo">Click on a variant in the results table. Detailed annotations for this variant will then be shown in this tab.</span> 
						<div id="proxy-snpinfo-accordion"></div> 
					</div>
					<div id="proxy-tabs-report" class="plots-tabs-body-content"></div>
				</div>
			</div>
			
			
