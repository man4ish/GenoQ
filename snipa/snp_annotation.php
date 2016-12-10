<?php
	include("backend/snipaWhitelists.php");

?>
			<div class="helptext" id="helptext"><span id="helptext-close" class="helptext-close">close</span>
				<div id="helptext-content" class="helptext-content">
					This module allows you to get detailed annotations for one or more variants. If the results are not what you have expected, please check the &quot;Report&quot; tab for details.
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
							<th colspan="3" class="inputbox">Variants</th>
						</tr>
					</thead>
					<tbody>
						<tr id="snps_sentinels">
							<td style="width: 130px;">Variants:</td>
							<td style="width: 280px;">
								<textarea id="snps_sentinels_input" class="inputbox" rows="" cols="" style="height: 100px"></textarea>
							</td>
							<td class="description">
								For variants entered here, GenoQ provides functional annotations if available.<br />In case you have already added variants to GenoQ's clipboard, these are available as a preselection.<br /><br />
								Please input one rs-identifier per line.
								<br /><br />
								<button type="button" class="example-button"  onclick="$('#snps_sentinels_input').val('rs2066938\nrs13391552\nrs174547\nrs887829\nrs211718\nrs6558295\nrs603424\nrs780094\nrs1495743\nrs17277546\nrs612169\nrs4481233\nrs9332998\nrs2216405\nrs2652822\nrs662138\nrs4149081\nrs503279\nrs4329\nrs477992\nrs2087160\nrs2518049\nrs494562\nrs2023634\nrs2403254\nrs10799701\nrs6499165\nrs4253252\nrs2657879\nrs7200543\nrs272889\nrs12670403\nrs8396\nrs9393903\nrs7094971\nrs10518693\nrs7760535')">Load example</button>
								
							</td>
						</tr>
					</tbody>
				</table>
			
				<p style="width: 100%; text-align: right;"><button type="button" id="submit-button">Start processing this job</button></p>
			</div>			
			<div id="progress-dialog">
				<div id="progressbar"><div class="progress-label"></div></div>
				<div id="message"></div>
			</div>
			
			<div id="tabs" class="plots-tabs" style="padding-top: 10px; padding-bottom: 10px">
				<ul id="tabs-header" class="plots-tabs-header">
					<li class="plots-tabs-tab"><a href="#tabs-snpinfo">Variant annotations</a></li>
					<li class="plots-tabs-tab"><a href="#tabs-report">Report</a></li>
				</ul>
				<div id="snpdetails-container" class= "plot-snpdetails"><div id="snpdetails-menu"></div></div>
				<div id="tabs-body" class="plots-tabs-body">
					<div id="tabs-snpinfo" class="plots-tabs-body-content">
						<span id="nosnpinfo">There are no annotations to be shown here or your browser is busy downloading the results. Check the reports section if your input could be processed.</span> 
						<div id="snpinfo-accordion"></div> 
					</div>
					<div id="tabs-report" class="plots-tabs-body-content"></div>
				</div>
			</div>
			
			
