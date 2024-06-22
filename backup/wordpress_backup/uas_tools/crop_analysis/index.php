<?php
// File containing System Variables
define("LOCAL_PATH_ROOT", $_SERVER["DOCUMENT_ROOT"]);
require LOCAL_PATH_ROOT . '/uas_tools/system_management/centralized_management.php';

//echo $admin_email;

// To check if User has the role required to access the page
require_once("Resources/PHP/SetDBConnection.php");
//require_once("../system_management/centralized.php");

$mysqli = SetDBConnection();

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
//$header_location = $_SESSION['header_location'];
//echo $header_location;

$userName = $_SESSION["email"] ?? '';

// SELECT the role_name for each users_roles for the logged on user
// ? is a place holder for our parameter `user_id`
$sql = "
    SELECT r.role_name FROM users_roles AS ur
        JOIN roles AS r ON r.role_id = ur.role_id
    WHERE ur.user_id = ?
";

$query = $mysqli->prepare($sql);                // Prepare the query
$query->bind_param("i", $_SESSION["user_id"]);  // Bind the parameter (wherever you store user_id in $_SESSION)
$query->execute();                              // Run the query
$query->store_result();                         // Store the result
$query->bind_result($role_name);                // Bind the result to a variable

$user_role_array = [];                          // Initialise the user roles array
while ($query->fetch()) {                         // Loop returned records
    $user_role_array[] = $role_name;            // Add user role to array
}

if (mysqli_connect_errno()) {
    echo "Failed to connect to database server: " . mysqli_connect_error();
} else {
    if (!$user_role_array) {
        $_SESSION["page"] = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        //header("Location: http://basfhub.gdslab.org");
        header("Location: " . 'http://' . $_SERVER['HTTP_HOST']);
        exit();
    } else {
        $pageName = basename(__DIR__);
        if ($pageName == "V2") {
            $pageName = basename(realpath(__DIR__ . "/.."));
        }

        $sql1 = "SELECT * FROM page_access WHERE Page = '$pageName'";
        $allowedGroups = array();
        if ($result1 = mysqli_query($mysqli, $sql1)) {
            if ($row1 = mysqli_fetch_assoc($result1)) {
                $allowedGroups = explode(";", $row1["Page_Groups"]);
                $accessGroupsStr = $row1["Page_Groups"];
            }
        }

        $intersect = array_intersect($user_role_array, $allowedGroups);

        if (sizeof($intersect) > 0) {// if match found
            ?>

            <!DOCTYPE html>
            <html lang="en">

            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
                <!--						<title>Crop Growth Analysis</title>-->
                <title>Crop Analysis</title>
                <!-- Styles -->
                <link rel="stylesheet" type="text/css" href="Resources/style.css">

                <script type="text/javascript" src="Resources/JS/jquery.min.js"></script>
                <script src="Resources/JS/JqueryUI/jquery-ui.min.js"></script>
                <link rel="stylesheet" type="text/css" href="Resources/JS/JqueryUI/jquery-ui.css">
                <script type="text/javascript" src="Resources/JS/main.js"></script>
                <script src="Resources/JS/FixedTable/fixed_table_rc.js"></script>
                <link rel="stylesheet" type="text/css" href="Resources/JS/FixedTable/fixed_table_rc.css">
                <script src="Resources/JS/d3/d3.min.js" charset="utf-8"></script>
                <script src="Resources/JS/d3/d3-tip.js" charset="utf-8"></script>
                <script src="Resources/JS/canvg/rgbcolor.js"></script>
                <script src="Resources/JS/canvg/StackBlur.js"></script>
                <script src="Resources/JS/canvg/canvg.js"></script>
            </head>


            <body>
            <div id="processing"></div>
            <form>
                <h2>Crop Growth Analysis</h2>
                <br>
                <div id="select-data-set">
                    <div style="clear:both; margin-bottom: 10px"></div>
                    <input id="next" type='button' class='button right-button' value='Next'
                           onclick='ShowSelectDataValues(); return false;' disabled/>
                    <div style="clear:both; margin-bottom: 10px"></div>
                    <fieldset style="width:93%; padding-top: 10px">
                        <legend>Search</legend>
                        <div class="one-third-width">
                            <div>
                                <div class="label">Crop</div>
                                <div class="input"><select id="crop"></select></div>
                            </div>
                            <div style="clear:both"></div>
                            <div>
                                <div class="label">Season</div>
                                <div class="input"><select id="season"></select></div>
                            </div>
                        </div>

                        <div class="one-third-width">
                            <div>
                                <div class="label">Type</div>
                                <div class="input"><select id="type"></select></div>
                            </div>
                            <div style="clear:both"></div>
                            <div>
                                <div class="label">Location</div>
                                <div class="input"><select id="location"></select></div>
                            </div>


                        </div>
                        <div class="one-third-width">
                            <div>
                                <div class="label">Year</div>
                                <div class="input"><select id="year"></select></div>
                            </div>
                            <div style="clear:both"></div>
                            <div>
                                <div class="label">Sub-location</div>
                                <div class="input"><select id="sublocation"></select></div>
                            </div>

                        </div>
                    </fieldset>

                    <div style="clear:both; margin-bottom: 10px"></div>
                    <input type='button' class='button right-button' value='Search' onclick='Search(); return false;'/>
                    <div style="clear:both; margin-bottom: 10px"></div>

                    <fieldset id="data-set-fs" style="width:93%; padding-top: 10px; display:none">
                        <legend>Data Set</legend>
                        <div id="imported-set-wrapper"></div>
                    </fieldset>


                </div>
                <div id="select-data-values" style="display:none">
                    <br>
                    <span style="margin:10px 30px">Selected Dataset</span>
                    <table style="width:95%; margin: 0 auto">
                        <thead>
                        <tr>
                            <th>Year</th>
                            <th>Season</th>
                            <th>Crop</th>
                            <th>Type</th>
                            <th>Location</th>
                            <th>Sub-location</th>
                        </tr>

                        </thead>
                        <tbody>
                        <tr id="selected-data-set">
                        </tr>
                        </tbody>
                    </table>

                    <div style="clear:both; margin-bottom: 10px"></div>
                    <input type='button' class='button right-button' value='Back'
                           onclick='ShowSelectDataSet(); return false;'/>
                    <div style="clear:both; margin-bottom: 10px"></div>

                    <fieldset style="width:93%; padding-top: 10px">
                        <legend>Search</legend>
                        <div id="search-criteria-wrapper">
                        </div>
                    </fieldset>
                    <div style="clear:both; margin-bottom: 10px"></div>
                    <input type='button' class='button right-button' value='Search'
                           onclick='GetImportedData(); return false;'/>
                    <div style="clear:both; margin-bottom: 10px"></div>
                    <div id="data-for-analysis" style="display:none">
                        <fieldset style="width:93%; padding-top: 10px;">
                            <legend>Data Values</legend>
                            <div style="clear:both"></div>
                            <span>Select/remove all row:</span><input id="all-row-data" type="checkbox" checked
                                                                      onchange="ToggleAllRowData();">
                            <div style="clear:both; margin-top:20px"></div>
                            <div id="imported-list-wrapper"></div>
                            <div id="page-control"
                                 style="width: 95%; margin: 20px auto; display: none; text-align: center">
                                <span>Row Per Page</span>
                                <select id="row-per-page" class="small">
                                    <option value="20" selected>20</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                    <option value="200">200</option>
                                </select>
                                <span class="label-small">Page</span>
                                <input type="text" id="page" class="tiny">
                                <span class="label-tiny">/</span>
                                <span class="label-tiny" id="page-num"></span>
                                <input type='button' id="prevPage" value='Prev' onclick='Prev(); return false;'
                                       style="margin: 7px 5px;"/>
                                <input type='button' id="nextPage" value='Next' onclick='Next(); return false;'
                                       style="margin: 7px 5px"/>

                            </div>
                            <div style="clear:both"></div>
                            <input id="export-data" type="button" class="button right-button" value="Export"
                                   onclick="ExportData('data'); return false;"/>

                        </fieldset>

                        <div style="clear:both; margin-bottom: 10px"></div>

                        <fieldset style="width:93%; padding-top: 10px;" id="values">
                            <legend>Fitting options</legend>

                            <div style="clear:both"></div>
                            <div class="label">Fitting Value</div>
                            <select class="small" id="value-type">
                                <option value="max">Max</option>
                                <option value="min">Min</option>
                                <option value="avg" selected="selected">Avg</option>
                            </select>

                            <div style="clear:both"></div>
                            <br>
                            <div id="value-wrapper">
                            </div>
                            <div style="clear:both"></div>
                            <input id="export-value" type="button" class="button right-button" value="Export"
                                   onclick="ExportData('value'); return false;"/>

                            <div style="clear:both"></div>
                            <br>

                            <div class="label">Starting Date</div>
                            <div class="input"><input type="text" id="starting-date" class="small" value="01/01/2016">
                            </div>

                            <div class="label">Last Date</div>
                            <div class="input"><input type="text" id="last-date" class="small" value="12/31/2016"></div>

                            <div class="label">Last Day</div>
                            <div class="input"><input type="text" id="last-day" class="tiny" value="140"></div>

                            <div style="clear:both"></div>

                            <div class="label">Fitting Type</div>
                            <select id="fitting-type">
                                <option value="sigmoid">Sigmoid</option>
                                <option value="logistic">Logistic</option>
                                <option value="richard4">Richard4</option>
                                <option value="richard5">Richard5</option>
                                <option value="svr">SVR</option>
                                <option value="polysimple">Poly (simple)</option>
                                <option value="polyzero">Poly (zero)</option>
                                <option value="polysklearn">Poly (sklearn)</option>
                                <option value="rbf">RBF</option>
                            </select>


                            <div style="clear:both"></div>

                            <div id="canopy-parameters">
                                <div id="init-sigmoid">
                                    <div class="equation">
                                        <img src="Resources/Images/sigmoid.png" alt="sigmoid">
                                    </div>
                                    <div style="clear:both"></div>
                                    <div class="label label-large">Initial Parameters:</div>

                                    <div class="parameters">
                                        <div class="label label-tiny">a</div>
                                        <div class="input"><input type="text" id="sigmoid-a" class="tiny" value="100">
                                        </div>
                                        <div class="label label-tiny">b</div>
                                        <div class="input"><input type="text" id="sigmoid-b" class="tiny" value="3">
                                        </div>
                                        <div class="label label-tiny">c</div>
                                        <div class="input"><input type="text" id="sigmoid-c" class="tiny" value="25">
                                        </div>
                                    </div>
                                </div>

                                <div id="init-logistic" style="display:none">
                                    <div class="equation">
                                        <img src="Resources/Images/logistic.png" alt="logistic">
                                    </div>
                                    <div style="clear:both"></div>
                                    <div class="label label-large">Initial Parameters:</div>

                                    <div class="parameters">
                                        <div class="label label-tiny">v</div>
                                        <div class="input"><input type="text" id="logistic-v" class="tiny" value="0">
                                        </div>
                                        <div class="label label-tiny">τ</div>
                                        <div class="input"><input type="text" id="logistic-tau" class="tiny" value="0">
                                        </div>
                                        <div class="label label-tiny">μ</div>
                                        <div class="input"><input type="text" id="logistic-mu" class="tiny" value="30">
                                        </div>
                                        <div class="label label-tiny">σ</div>
                                        <div class="input"><input type="text" id="logistic-sigma" class="tiny"
                                                                  value="3"></div>
                                        <div class="label label-tiny">ρ</div>
                                        <div class="input"><input type="text" id="logistic-rho" class="tiny" value="1">
                                        </div>
                                    </div>
                                </div>

                                <div id="init-richard4" style="display:none">
                                    <div class="equation">
                                        <img src="Resources/Images/richard4.png" alt="richard4">
                                    </div>
                                    <div style="clear:both"></div>
                                    <div class="label label-large">Initial Parameters:</div>
                                    <div class="parameters">
                                        <div class="label label-tiny">L<sub>∞</sub></div>
                                        <div class="input"><input type="text" id="richard4-li" class="tiny" value="0">
                                        </div>
                                        <div class="label label-tiny">k</div>
                                        <div class="input"><input type="text" id="richard4-k" class="tiny" value="3">
                                        </div>
                                        <div class="label label-tiny">γ</div>
                                        <div class="input"><input type="text" id="richard4-gamma" class="tiny"
                                                                  value="60"></div>
                                        <div class="label label-tiny">δ</div>
                                        <div class="input"><input type="text" id="richard4-delta" class="tiny"
                                                                  value="1"></div>
                                    </div>
                                </div>

                                <div id="init-richard5" style="display:none">
                                    <div class="equation">
                                        <img src="Resources/Images/richard5.png" alt="richard5">
                                    </div>
                                    <div style="clear:both"></div>
                                    <div class="label label-large">Initial Parameters:</div>
                                    <div class="label label-tiny">β</div>
                                    <div class="input"><input type="text" id="richard5-beta" class="tiny" value="0">
                                    </div>
                                    <div class="label label-tiny">L<sub>∞</sub></div>
                                    <div class="input"><input type="text" id="richard5-li" class="tiny" value="0"></div>
                                    <div class="label label-tiny">t<sub>m</sub></div>
                                    <div class="input"><input type="text" id="richard5-tm" class="tiny" value="60">
                                    </div>
                                    <div class="label label-tiny">k</div>
                                    <div class="input"><input type="text" id="richard5-k" class="tiny" value="3"></div>
                                    <div class="label label-tiny">T</div>
                                    <div class="input"><input type="text" id="richard5-T" class="tiny" value="1"></div>
                                </div>
                                <div style="clear:both"></div>
                                <div id="optimized-parameters" style="margin:5px; text-align:left">
                                </div>
                            </div>

                            <div id="ndvi-parameters">
                                <div id="ndvi-svr">
                                    <div class="label label-tiny">C</div>
                                    <div class="input"><input type="text" id="ndvi-c" class="small" value="10000"></div>
                                    <div class="label label-small">gamma</div>
                                    <div class="input"><input type="text" id="ndvi-gamma" class="small"
                                                              value="0.000002"></div>
                                </div>
                                <div id="ndvi-poly" style="display:none">
                                    <div class="label label-small">degree</div>
                                    <div class="input"><input type="text" id="ndvi-degree" class="small" value="7">
                                    </div>
                                </div>
                                <div id="ndvi-rbf" style="display:none">
                                    <div class="label label-small">epsilon</div>
                                    <div class="input"><input type="text" id="ndvi-epsilon" class="small" value="1">
                                    </div>
                                    <div class="label label-small">smooth</div>
                                    <div class="input"><input type="text" id="ndvi-smooth" class="small" value="0">
                                    </div>
                                </div>
                                <div class="label label-small">Add first 0</div>
                                <div class="input"><input type="checkbox" id="ndvi-first-zero" checked></div>
                                <!--
                                <div class="label label-small">Add last 0</div>
                                <div class="input"><input type="checkbox" id="ndvi-last-zero"></div>
                                -->
                                <div class="label label-extra-large">Force line go through the origin</div>
                                <div class="input"><input type="checkbox" id="ndvi-through-origin"></div>
                            </div>

                            <!--
                            <div id="fit-canopy-options" style="display:none">
                                <div class="label">Fitting Type</div>
                                <select id="fitting-type">
                                    <option value="sigmoid">Sigmoid</option>
                                    <option value="logistic">Logistic</option>
                                    <option value="richard4">Richard4</option>
                                    <option value="richard5">Richard5</option>

                                </select>

                                <div style="clear:both"></div>

                                <div id="init-sigmoid">
                                    <div class="equation">
                                        <img src="Resources/Images/sigmoid.png" alt="sigmoid">
                                    </div>
                                    <div style="clear:both"></div>
                                    <div class="label label-large">Initial Parameters:</div>

                                    <div class="parameters">
                                        <div class="label label-tiny">a</div>
                                        <div class="input"><input type="text" id="sigmoid-a" class="tiny" value="100"></div>
                                        <div class="label label-tiny">b</div>
                                        <div class="input"><input type="text" id="sigmoid-b" class="tiny" value="3"></div>
                                        <div class="label label-tiny">c</div>
                                        <div class="input"><input type="text" id="sigmoid-c" class="tiny" value="25"></div>
                                    </div>
                                </div>

                                <div id="init-logistic" style="display:none">
                                    <div class="equation">
                                        <img src="Resources/Images/logistic.png" alt="logistic">
                                    </div>
                                    <div style="clear:both"></div>
                                    <div class="label label-large">Initial Parameters:</div>

                                    <div class="parameters">
                                        <div class="label label-tiny">v</div>
                                        <div class="input"><input type="text" id="logistic-v" class="tiny" value="0"></div>
                                        <div class="label label-tiny">τ</div>
                                        <div class="input"><input type="text" id="logistic-tau" class="tiny" value="0"></div>
                                        <div class="label label-tiny">μ</div>
                                        <div class="input"><input type="text" id="logistic-mu" class="tiny" value="30"></div>
                                        <div class="label label-tiny">σ</div>
                                        <div class="input"><input type="text" id="logistic-sigma" class="tiny" value="3"></div>
                                        <div class="label label-tiny">ρ</div>
                                        <div class="input"><input type="text" id="logistic-rho" class="tiny" value="1"></div>
                                    </div>
                                </div>

                                <div id="init-richard4" style="display:none">
                                    <div class="equation">
                                        <img src="Resources/Images/richard4.png" alt="richard4">
                                    </div>
                                    <div style="clear:both"></div>
                                    <div class="label label-large">Initial Parameters:</div>
                                    <div class="parameters">
                                        <div class="label label-tiny">L<sub>∞</sub></div>
                                        <div class="input"><input type="text" id="richard4-li" class="tiny" value="0"></div>
                                        <div class="label label-tiny">k</div>
                                        <div class="input"><input type="text" id="richard4-k" class="tiny" value="3"></div>
                                        <div class="label label-tiny">γ</div>
                                        <div class="input"><input type="text" id="richard4-gamma" class="tiny" value="60"></div>
                                        <div class="label label-tiny">δ</div>
                                        <div class="input"><input type="text" id="richard4-delta" class="tiny" value="1"></div>
                                    </div>
                                </div>

                                <div id="init-richard5" style="display:none">
                                    <div class="equation">
                                        <img src="Resources/Images/richard5.png" alt="richard5">
                                    </div>
                                    <div style="clear:both"></div>
                                    <div class="label label-large">Initial Parameters:</div>
                                    <div class="label label-tiny">β</div>
                                    <div class="input"><input type="text" id="richard5-beta" class="tiny" value="0"></div>
                                    <div class="label label-tiny">L<sub>∞</sub></div>
                                    <div class="input"><input type="text" id="richard5-li" class="tiny" value="0"></div>
                                    <div class="label label-tiny">t<sub>m</sub></div>
                                    <div class="input"><input type="text" id="richard5-tm" class="tiny" value="60"></div>
                                    <div class="label label-tiny">k</div>
                                    <div class="input"><input type="text" id="richard5-k" class="tiny" value="3"></div>
                                    <div class="label label-tiny">T</div>
                                    <div class="input"><input type="text" id="richard5-T" class="tiny" value="1"></div>
                                </div>
                                <div style="clear:both"></div>
                                <div id="optimized-parameters" style="margin:5px; text-align:left">
                                </div>
                            </div>

                            <div id="fit-ndvi-options" style="display:none">
                                <div class="label">Fitting Type</div>
                                <select id="ndvi-fitting-type">
                                    <option value="svr">SVR</option>
                                    <option value="polysimple">Poly (simple)</option>
                                    <option value="polyzero">Poly (zero)</option>
                                    <option value="polysklearn">Poly (sklearn)</option>
                                    <option value="rbf">RBF</option>
                                </select>

                                <div style="clear:both"></div>

                                <div class="label label-large">Initial Parameters:</div>

                                <div class="ndvi-parameters">
                                    <div id="ndvi-svr">
                                        <div class="label label-tiny">C</div>
                                        <div class="input"><input type="text" id="ndvi-c" class="small" value="10000"></div>
                                        <div class="label label-small">gamma</div>
                                        <div class="input"><input type="text" id="ndvi-gamma" class="small" value="0.000002"></div>
                                    </div>
                                    <div id="ndvi-poly" style="display:none">
                                        <div class="label label-small">degree</div>
                                        <div class="input"><input type="text" id="ndvi-degree" class="small" value="7"></div>
                                    </div>
                                    <div id="ndvi-rbf" style="display:none">
                                        <div class="label label-small">epsilon</div>
                                        <div class="input"><input type="text" id="ndvi-epsilon" class="small" value="1"></div>
                                        <div class="label label-small">smooth</div>
                                        <div class="input"><input type="text" id="ndvi-smooth" class="small" value="0"></div>
                                    </div>
                                    <div class="label label-small">Add first 0</div>
                                    <div class="input"><input type="checkbox" id="ndvi-first-zero" checked></div>
                                    <div class="label label-extra-large">Force line go through the origin</div>
                                    <div class="input"><input type="checkbox" id="ndvi-through-origin"></div>
                                </div>
                            </div>
                            -->
                            <div style="clear:both"></div>

                            <br>
                            <input type='button' class='button right-button' value='Show Chart'
                                   onclick='GenerateChartValues(); return false;'/>
                            <!--
                            <input type='button' class='button right-button' value='Show Poly' onclick='GenerateNDVIChartPolyValues(); return false;' />
                            <input type='button' class='button right-button' value='Show Poly (scikit)' onclick='GenerateNDVIChartPolySKLValues(); return false;' />
                            <input type='button' class='button right-button' value='Show RBF' onclick='GenerateNDVIChartRBFValues(); return false;' />
                            -->
                        </fieldset>
                    </div>
                    <div style="clear:both"></div>

                    <div id="growth-chart-result" class='charts' style="display:none">

                        <div id='growth-chart' class='charts'>

                        </div>

                        <div id='growth-rate-chart' class='charts'>

                        </div>
                        <br>
                        <table id="growth-table">
                            <thead>
                            <tr>
                                <th>Max</th>
                                <th>Mday</th>
                                <th>Ehalf</th>
                                <th>Eday</th>
                                <th>Lhalf</th>
                                <th>Lday</th>
                                <th>Mdur</th>
                                <th>Edur</th>
                                <th>Ldur</th>
                                <th>ERGR</th>
                                <th>LRGR</th>
                                <th>Earea_tri</th>
                                <th>Larea_tri</th>
                                <th>Earea</th>
                                <th>Larea</th>
                            </tr>
                            </thead>
                            <tbody id="growth-values">
                            </tbody>
                        </table>

                        <input id="export-growth" type="button" class="button" value="Export Data"
                               onclick="ExportData('growth'); return false;"/>
                        <input id="export-chart" type="button" class="button" value="Export Charts"
                               onclick="ExportCharts('growth'); return false;"/>
                    </div>
                    <div id="ndvi-chart-result" class='charts' style="display:none">
                        <div id='ndvi-chart' class='charts'>
                        </div>
                        <div id="ndvi-table-wrapper" style="margin-top:20px">
                            <!--
                            <table id="ndvi-table">
                                <thead>
                                    <tr>
                                        <th>Max</th>
                                        <th>Mday</th>
                                        <th>Int</th>
                                        <th>Iday</th>
                                        <th>D1</th>
                                        <th>D2</th>
                                        <th>Area1</th>
                                        <th>Area2</th>
                                    </tr>
                                </thead>
                                <tbody id="ndvi-table-values">
                                </tbody>
                            </table>
                            -->
                        </div>
                        <input id="export-growth" type="button" class="button" value="Export Data"
                               onclick="ExportData('ndvi'); return false;"/>
                        <input id="export-ndvi-chart" type="button" class="button" value="Export Chart"
                               onclick="ExportCharts('ndvi'); return false;"/>
                    </div>
                </div>
            </form>
            <div id="canvas-list" style="display:none"></div>
            </body>
            </html>

            <?php
        } else {
            $memberOf = (implode("; ", $user_role_array));
            ?>
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <title><?php echo $pageName; ?></title>
            </head>
            <body>
            </br>
            <p>You do not currently have permission to access this tool.</p>
            <p>Please contact admin at
                <a href="mailto:<?= $admin_email ?>?
        &subject=Requesting%20access%20to%20the%20crop_analysis%20tool
        &body=Hi,%0D%0A%0D%0AThis%20is%20<?= $admin_email ?>.%20Please%20provide%20me%20access%20to%20the%20tool.">
                    <?= $admin_email ?></a>
                to request access to this tool.</p>
            </body>
            </html>
            <?php
        }
    }
}
?>