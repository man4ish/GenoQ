<?php
	include("backend/snipaWhitelists.php");

?>
			<div class="helptext" id="helptext"><span id="helptext-close" class="helptext-close">close</span>
				<div id="helptext-content" class="helptext-content">
					Linkage disequilibrium plots show the amount of correlation between a sentinel variant and its surrounding variants. The y-axis shows the correlation coefficient (r<sup>2</sup>), the x-axis shows chromosomal position of each SNP. The plot symbol of each variant indicates its functional annotation.<br />
					Interactive plots allow you to zoom into the plot and to get annotations when you hover your mouse pointer over them. Click on a variant to show detailed annotations or to copy it to GenoQ's clipboard. See the <a href="?task=documentation">documentation</a> for a list of features.
					
				</div>
			</div>

			
			<div id="raplot-form-container" style="padding-top: 10px; padding-bottom: 10px;">
			
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
							<th colspan="3" class="inputbox">Sentinel Variant</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td style="width: 130px;">Sentinel:</td>
							<td style="width: 280px;">
								<input id="sentinel" class="inputbox" />
							</td>
							<td class="description">
								GenoQ looks for genetic variants in linkage disequilibrium (r<sup>2</sup> &gt; <span id="helptext_ld_value">0.1</span>) with the sentinel. If you have already added variants to GenoQ's clipboard, these are available as a preselection.
								<br /><br />
								<button type="button" class="example-button" onclick="$('#sentinel').val('rs174547')">Load example</button>
							</td>
						</tr>
					</tbody>
				</table>
				
				<table class="inputbox">
					<thead>
						<tr>
							<th colspan="3" class="inputbox">Plot options</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td style="width: 130px;">Highlight variants:</td>
							<td style="width: 280px;">
								<textarea id="highlightvariants" rows="" cols="" class="inputbox" style="height: 100px"></textarea>
							</td>
							<td class="description">
								You can optionally enter variants that you want to highlight in the LD plot. Only variants that are in LD (r<sup>2</sup> &gt; 0.1) with the sentinel and present in the selected variant set and/or population will be highlighted.<br />
								Please enter one rs identifier per line.
							</td>
						</tr>
						<tr>
							<td style="width: 130px;">Highlight mode:</td>
							<td style="width: 280px;">
								<div id="highlightmode_container">
									<input type="radio" id="highlightmode_0" value="hide" name="highlightmode" /><label for="highlightmode_0">hide</label>
									<input type="radio" id="highlightmode_1" value="show" name="highlightmode" checked="checked" /><label for="highlightmode_1">show</label>
								</div>
							</td>
							<td class="description">
								If you have specified any variants to be highlighted, you can select how any other variants in LD with the sentinel should be plotted. Set this option to &quot;hide&quot; and GenoQ will omit those variants from the plot. If you chose &quot;show&quot;, GenoQ will plot those variants at reduced opacity.
							</td>
						</tr>
						<tr>
							<td style="width: 130px;">Interactive plot:</td>
							<td style="width: 280px;">
								<div id="highcharts_container">
									<input type="radio" id="highcharts_0" value="0" name="highcharts" /><label for="highcharts_0">disable</label>
									<input type="radio" id="highcharts_1" value="1" name="highcharts" checked="checked" /><label for="highcharts_1">enable</label>
								</div>
							</td>
							<td class="description">
								Interactive plots offer detailed variant annotations as well as zooming and other features. However, this will only work on modern browsers.
								<br />If you have no interest in these features or if you are using a legacy browser, disable this option to speed up data processing.
							</td>
						</tr>
						<tr>
							<td style="width: 130px;">Download plot:</td>
							<td style="width: 280px;">
								<div id="hires_container">
									<input type="radio" id="hires_0" value="0" name="hires" /><label for="hires_0">disable</label>
									<input type="radio" id="hires_1" value="1" name="hires" checked="checked" /><label for="hires_1">enable</label>
								</div>
							</td>
							<td class="description">
								When enabled, GenoQ generates high-resolution PNG and PDF versions of the static plot. You can download the plots in the &quot;Report&quot; section.
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
			
                        <div id="plots-tabs" class="plots-tabs" style="padding-top: 10px; padding-bottom: 10px">
                                <ul id="plots-tabs-header" class="plots-tabs-header">
                                        <li class="plots-tabs-tab"><a href="#plots-tabs-static">Static plot</a></li>
                                        <li class="plots-tabs-tab"><a href="#plots-tabs-report">Report</a></li>
                                        <li class="plots-tabs-tab" onclick="heatmap()"><a href="#plots-tabs-heatmap">LD Heat Map</a></li>
                                        <!--<li class="plots-tabs-tab"><a href="#plots-tabs-dummy">LD Heat Map(Dummy)</a></li>-->
                                </ul>
                                <div id="plots-tabs-body" class="plots-tabs-body">
                                        <div id="plots-tabs-static" class="plots-tabs-body-content" style="margin: 0px; padding: 0px;"></div>
                                        <div id="plots-tabs-report" class="plots-tabs-body-content"></div>
                                        <div id="plots-tabs-heatmap" class="plots-tabs-body-content"></div>
                                       <!-- <div id="plots-tabs-dummy" class="plots-tabs-body-content"></div>-->
                                </div>
                        </div>


		<!--	<div id="plots-tabs" class="plots-tabs" style="padding-top: 10px; padding-bottom: 10px">
				<ul id="plots-tabs-header" class="plots-tabs-header">
					<li class="plots-tabs-tab"><a href="#plots-tabs-static">Static plot</a></li>
					<li class="plots-tabs-tab"><a href="#plots-tabs-report">Report</a></li>
                                        <li class="plots-tabs-tab" onclick="heatmap()"><a href="#plots-tabs-heatmap">LD Heat Map</a></li>
				</ul>
				<div id="plots-tabs-body" class="plots-tabs-body">
					<div id="plots-tabs-static" class="plots-tabs-body-content" style="margin: 0px; padding: 0px;"></div>
					<div id="plots-tabs-report" class="plots-tabs-body-content"></div>
                                        <div id="plots-tabs-heatmap" class="plots-tabs-body-content"></div>
				</div>
			</div> -->
			
			
