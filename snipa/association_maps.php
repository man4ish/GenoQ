<?php
	include("backend/snipaWhitelists.php");
	require("backend/snipaConfig.php");

?>
			<div class="helptext" id="helptext"><span id="helptext-close" class="helptext-close">close</span>
				<div id="helptext-content" class="helptext-content">
					Here you can create association maps (karyograms) containing all genome-wide significant (P&nbsp;&lt;&nbsp;5&times;10<sup>-8</sup>) SNP associations as listed in the GWAS Catalog. Please refer to <a href="http://www.genome.gov/gwastudies/" target="_blank">www.genome.gov/gwastudies/</a> for further information including citation guide. Last updated: <?php $vdate = file($path_to_gwascatalog."/date.txt"); echo $vdate[0]; ?>.
				</div>
			</div>

			<div id="tabs" class="plots-tabs" style="padding-top: 10px; padding-bottom: 10px">
				<ul id="tabs-header" class="plots-tabs-header">
					<li class="plots-tabs-tab"><a href="#tabs-results">Association Map</a></li>
					<li class="plots-tabs-tab"><a href="#tabs-snpinfo">Variant annotations</a></li>
				</ul>
				<div id="snpdetails-container" class= "plot-snpdetails"><div id="snpdetails-menu"></div></div>
				<div id="tabs-body" class="plots-tabs-body">
					<div id="tabs-results" class="plots-tabs-body-content" style="margin: 3px; padding: 3px; overflow: auto;">
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
							<table class="inputbox" style="width: 100%;">
								<thead>
									<tr>
										<th colspan="3" class="inputbox">Association map settings</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td style="width: 130px;">Input Type:</td>
										<td style="width: 280px;">
											<div id="selection_container">
												<input type="radio" id="selection_gwas_catalog" value="gwas" name="selection_maps"  checked="checked" /><label for="selection_gwas_catalog">GWAS Catalog</label>
												<input type="radio" id="selection_own_map" value="gene" name="selection_maps" /><label for="selection_own_map">Create own map</label>
											</div>
										</td>
										<td class="description">
											Select a trait from the NHGRI GWAS Catalog or create your own association map.
										</td>
									</tr>
									<tr class="gwas_catalog">
										<td style="width: 130px;">Phenotypes:</td>
										<td style="width: 280px;">
											<select id="traitlist" size="10" class="inputbox" multiple="multiple"></select>
										</td>
										<td class="description">
											Choose as many traits as you like (the more, the longer it will take to render the plot). The variants with significant associations to these traits are plotted in the karyogram.<br /><br />
											To select multiple traits, hold the &quot;CTRL&quot; key (Apple: &quot;cmd &#8984;&quot;) while clicking on the entries.
										</td>
									</tr>
									<tr class="gwas_catalog">
										<td style="width: 130px;">Filter:</td>
										<td style="width: 280px;">
											<input id="trait" class="inputbox" />
										</td>
										<td class="description">
											Use this field to search for traits in the list above or to display a set of related traits.
										</td>
									</tr>
									<tr class="own_map">
										<td style="width: 130px;">Variants:</td>
										<td style="width: 280px;">
											<textarea id="snps_input" class="inputbox" rows="" cols="" style="height: 155px"></textarea>
										</td>
										<td class="description">
											Please input one rs-identifier per line. Optionally, you can also enter your association p-values together with the rs-identifiers in the following format: 
											<br />{rs-number}[space]{p-value}.<br />For example:<br /><br />
											rs12345 0.0001<br />
											rs241456 3.21e-12<br />
											rs34567<br />
											rs543221 1e-82<br /><br />
											Mixing lines of rs-numbers with and without p-values is also possible. Where a p-value is given, it will show up in the association map if you hover over the respective variant.
										</td>
									</tr>
									<tr class="own_map">
										<td style="width: 130px;">Trait:</td>
										<td style="width: 280px;">
											<input id="own_trait" class="inputbox" />
										</td>
										<td class="description">
											Use this field to enter the trait in your study (optional).
										</td>
									</tr>
									<tr class="own_map">
										<td style="width: 130px;"></td>
										<td style="width: 280px;">
										</td>
										<td class="description" style="text-align: right;">
											<button type="button" id="submit-button">Plot associations</button>
										</td>
									</tr>
								</tbody>
							</table>
							<table class="inputbox" style="width: 100%;">
								<thead>
									<tr>
										<th class="inputbox">Association map</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td style="width: 100%; position: relative;"><div id="assoc_load" style="z-index: 2000; position: absolute; top: 150px; left: 0px; height: 50px; width: 100%; margin: auto;"><p style="background-color: white; border: 2px solid #AAA; height: 50px; margin: auto; width: 200px; text-align: center; font-size: 150%; padding-top: 30px;"><img src="frontend/img/loading.gif" alt="Loading..." height="20px" width="90px" /></p></div><div id="assoc_map" style="height: 800px; width: 100%; margin: 0 auto"></div></div></td>
									</tr>
								</tbody>
							</table>

					</div>
					<div id="tabs-snpinfo" class="plots-tabs-body-content">
						<span id="nosnpinfo">Click on a GWAS hit on the association map. Detailed annotations for this variant will be displayed in this section.</span> 
						<div id="snpinfo-accordion"></div> 
					</div>
				</div>
			</div>
			
			
			
			
			
			
			
			
