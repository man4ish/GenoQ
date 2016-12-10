<?php
	include("backend/snipaWhitelists.php");

?>
			<div class="helptext" id="helptext"><span id="helptext-close" class="helptext-close">close</span>
				<div id="helptext-content" class="helptext-content">
					Regional association plots visualize results from association studies within a limited genomic region (500 kB). 
					For each trait-associated variant, the plot displays the strength of association (-log<sub>10</sub>(P)). The variants' symbols represent the functional annotations, the colors show the pairwise LD correlations to the sentinel variant. Furthermore, the plot shows the estimated recombination rate, genes, and regulatory elements.<br />
					GenoQ's interactive plots offer a lot of features such as zooming and tooltips containing summarized information for each variant. Check the <a href="?task=documentation">documentation</a> for a guided tour. 
		
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
							<th colspan="3" class="inputbox">Association data</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td style="width: 130px;">Associations:</td>
							<td style="width: 280px;">
								<textarea id="assocs" rows="" cols="" class="inputbox" style="height: 200px"></textarea>
							</td>
							<td class="description">
								Paste your association results here in the following format:<br />First column: rs-identifiers. Second column: P-values.<br />Valid delimiters: tabulator, whitespace, semicolon, and comma.<br /><br />
								Only variants that meet these criteria will be plotted: <ol><li>present in the variant set and population you selected above</li><li>on the same chromosome as the sentinel variant</li><li>within a 250 kb window around the sentinel</li></ol><br />
								For best plotting results, the variants should cover a chromosomal range of at least 100 kb.
								<br /><br />
								<button type="button" class="example-button"  onclick="$('#assocs').val('rs12794220 0.0222\nrs3931642 0.005004\nrs198456 0.01203\nrs17827816 0.7537\nrs198453 0.09957\nrs198452 0.6377\nrs17827918 0.02414\nrs2511229 0.1323\nrs516722 0.3106\nrs879486 0.003343\nrs12274157 0.1351\nrs11604261 0.1544\nrs198437 0.7964\nrs580089 0.1064\nrs198436 0.01481\nrs198435 8.718e-06\nrs198432 0.05182\nrs10488693 0.9732\nrs198430 0.04639\nrs81658 0.01537\nrs198428 0.002134\nrs198426 0.0001016\nrs9735635 0.002776\nrs198425 0.01423\nrs12285167 0.001865\nrs4963243 0.004755\nrs198418 0.01113\nrs198448 0.8155\nrs198446 0.01552\nrs198444 0.25\nrs2240287 0.002518\nrs2852786 0.6675\nrs3825036 0.0006968\nrs3741255 0.1788\nrs198421 0.0001214\nrs569258 0.0001222\nrs198464 3.69e-09\nrs2238003 0.001999\nrs198462 6.055e-09\nrs2238001 0.005669\nrs198476 6.015e-09\nrs198475 0.001761\nrs198473 0.001718\nrs2071212 0.597\nrs198467 0.02648\nrs2071213 0.002769\nrs650436 0.001344\nrs579383 0.0001179\nrs2269928 1.902e-12\nrs149803 2.917e-15\nrs174528 1.18e-86\nrs108499 1.678e-75\nrs509360 5.201e-29\nrs174532 1.596e-17\nrs174534 5.371e-71\nrs174536 9.18e-92\nrs174537 9.18e-92\nrs17762402 0.001096\nrs395346 0.0385\nrs102275 1.87e-94\nrs740006 0.2665\nrs7102974 0.001432\nrs174538 9.833e-80\nrs412334 8.924e-09\nrs695867 1.933e-07\nrs4246215 1.141e-83\nrs174541 1.1e-86\nrs174545 3.72e-94\nrs174546 4.10e-94\nrs174547 2.88e-94\nrs174548 3.083e-82\nrs174549 9.358e-83\nrs174550 5.01e-94\nrs174555 9.532e-83\nrs174556 9.506e-83\nrs968567 3.289e-21\nrs174570 2.103e-35\nrs1535 2.25e-91\nrs174574 1.97e-91\nrs2845573 7.328e-25\nrs174575 3.035e-38\nrs2727270 6.791e-35\nrs2727271 7.015e-35\nrs174576 7.46e-91\nrs2524299 7.991e-34\nrs174577 4.61e-91\nrs2072114 4.537e-31\nrs174578 3.379e-88\nrs174579 6.846e-30\nrs17156426 7.227e-08\nrs174583 4.645e-19\nrs174585 8.582e-32\nrs17156442 0.0007857\nrs7935946 5.988e-05\nrs174589 1.831e-29\nrs2851682 2.984e-21\nrs174591 2.025e-36\nrs174593 4.777e-11\nrs916924 0.01323\nrs174597 4.982e-11\nrs174601 1.525e-57\nrs2526678 1.848e-24\nrs174602 9.113e-15\nrs498793 2.23e-05\nrs526126 2.321e-17\nrs174605 3.932e-22\nrs174611 1.124e-28\nrs174616 7.375e-19\nrs174618 0.000602\nrs482548 6.261e-06\nrs17764324 2.026e-10\nrs17831757 2.111e-08\nrs11230815 2.141e-08\nrs174626 7.016e-18\nrs174627 6.494e-10\nrs7104849 3.261e-11\nrs472031 9.184e-06\nrs422249 1.058e-17\nrs174448 7.7e-35\nrs7482316 9.557e-08\nrs174449 2.708e-29\nrs174450 1.128e-15\nrs7115739 0.8846\nrs7942717 0.9779\nrs174634 2.366e-12\nrs174454 0.0309\nrs7394871 2.562e-07\nrs1000778 1.14e-11\nrs174455 2.463e-28\nrs174456 1.837e-11\nrs174464 1.146e-11\nrs528285 0.5787\nrs174468 9.299e-12\nrs17764935 3.567e-08\nrs13966 0.05607\nrs2235093 0.05604\nrs174469 3.112e-08\nrs3815045 0.01008\nrs174472 0.1266\nrs174476 1.288e-11\nrs666870 8.03e-10\nrs174478 8.085e-10\nrs174479 1.968e-09\nrs540613 1.922e-06\nrs2524287 0.07829\nrs2521561 0.2853\nrs2524289 0.3487\nrs11230827 0.97\nrs2524290 0.5224\nrs12420625 0.8576\nrs741887 0.4044\nrs7927548 0.01214\nrs882169 0.04393\nrs17156580 0.6346\nrs7928792 0.273\nrs2521567 0.0483\nrs2521568 0.001329\nrs2727266 0.0003745\nrs2158027 0.04803\nrs2736602 0.5628\nrs2736601 0.03212\nrs2727267 0.0455\nrs2727269 0.001284\nrs2521572 0.0554\nrs12273597 0.4056\nrs2727261 0.001373\nrs3758976 0.4471\nrs972355 0.05063\nrs1800007 0.05182\nrs168991 0.2559\nrs1109748 5.162e-05\nrs195165 0.1394\nrs760306 0.04443\nrs195162 0.07584\nrs741886 0.0544\nrs2668898 0.04418\nrs195160 0.627\nrs1801390 0.05855\nrs195157 0.1522\nrs195156 0.2168\nrs1800009 0.008778\nrs1801621 0.7326\nrs17156609 0.001507\nrs1052603 0.9129\nrs2073588 0.9636\nrs3758977 0.006579\nrs10897192 0.5032\nrs195446 0.04841\nrs17156616 0.6488\nrs195445 0.355\nrs17156618 0.01361\nrs17633020 0.1126\nrs17185574 0.1174\nrs2028062 0.006513\nrs10897193 0.6327\nrs10792320 0.00638\nrs12575518 0.9903\nrs10736714 0.8263\nrs10736715 0.6216\nrs10897194 0.5866\nrs7105652 0.5507\nrs11230849 0.5301\nrs11230851 0.803\nrs398853 0.9301\nrs1292934 0.7386\nrs7115285 0.736\nrs7115826 0.6861\nrs7115654 0.7818\n')">Load example</button>
							</td>
						</tr>
						<tr>
							<td style="width: 130px;">Sentinel:</td>
							<td style="width: 280px;">
								<input id="sentinel" class="inputbox" />
							</td>
							<td class="description">
								You can define a variant that should be regarded as the sentinel. By default, the variant that displays the strongest association signal is selected.<br />
								If you have added variants to GenoQ's clipboard, these are available as a preselection.
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
								When enabled, GenoQ generates high-resolution PNG and PDF versions of the static plot. You can find and download the plots in the &quot;Report&quot; section. Disabling this option slightly speeds up data processing.
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
				</ul>
				<div id="plots-tabs-body" class="plots-tabs-body">
					<div id="plots-tabs-static" class="plots-tabs-body-content" style="margin: 0px; padding: 0px;"></div>
					<div id="plots-tabs-report" class="plots-tabs-body-content"></div>
				</div>
			</div>
			
			
