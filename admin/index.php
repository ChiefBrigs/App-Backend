<?php
/**
 * Created by PhpStorm.
 * User: abderrahimelimame
 * Date: 7/8/16
 * Time: 02:00
 */
include 'header.php';
if ($_GB->getSession('admin') == false) {
    header("location:login.php");
}
?>
<div class="box box-info "></div>
<!-- Main content -->
<div class="content">

    <!-- Your Page Content Here -->
    <?php
    $totalUsers = $_DB->CountRows('users');
    $totalGroups = $_DB->CountRows('groups');
    $totalMessages = $_DB->CountRows('messages'); ?>

    <div class="row center-block">
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-aqua">
                <div class="inner">
                    <h3><?php echo $totalMessages ?></h3>
                    <p>Messages</p>
                </div>
                <a href="messages.php" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-green">
                <div class="inner">
                    <h3><?php echo $totalUsers ?></h3>
                    <p>Users</p>
                </div>
                <a href="users.php" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-red">
                <div class="inner">
                    <h3><?php echo $totalGroups ?></h3>

                    <p>Groups</p>
                </div>
                <a href="groups.php" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-yellow">
                <div class="inner">
                    <h3>21</h3>

                    <p>Calls ( to do next update)</p>
                </div>
                <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>

    </div>
    <div class="row">

        <div class="col-md-6">
            <!-- Map box -->
            <div class="box box-solid bg-aqua">
                <div class="box-header">
                    <!-- tools box -->
                    <div class="pull-right box-tools">
                        <button type="button" class="btn btn-primary btn-sm pull-right" data-widget="collapse"
                                data-toggle="tooltip" title="Collapse" style="margin-right: 5px;">
                            <i class="fa fa-minus"></i></button>
                    </div>
                    <!-- /. tools -->

                    <i class="fa fa-map-marker"></i>

                    <h3 class="box-title">
                        Visitors
                    </h3>
                </div>
                <div class="box-body bg-aqua">
                    <div id="regions_div" style="height: 300px; width: 100%;"></div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <!-- USERS LIST -->
            <div class="box box-danger">
                <div class="box-header with-border">
                    <h3 class="box-title">Latest Members</h3>

                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                class="fa fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i>
                        </button>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body no-padding">
                    <ul class="users-list clearfix">

                        <?php
                        $query = $_DB->select('users', '*', '', '`id` DESC', 6);
                        while ($fetch = $_DB->fetchAssoc($query)) {
                            $username = $fetch['username'];
                            $userImage = $fetch['image'];
                            echo '<li>';
                            if ($userImage != null) { ?>
                                <img alt="User Image" src="../image/profile/<?php echo $userImage ?>">
                            <?php } else { ?>
                                <img alt="User Image" src="logo.png">
                                <?php
                            }

                            echo '<a class="users-list-name" >';
                            if ($username == null) {
                                echo $fetch['phone'];
                            } else {
                                echo $fetch['username'];
                            }
                            echo '</a>';
                            echo '<span class="users-list-date">';
                            echo $fetch['status'];
                            echo '</span>';
                            echo '</li>';
                        } ?>
                    </ul>
                    <!-- /.users-list -->
                </div>
                <!-- /.box-body -->
                <div class="box-footer text-center">
                    <a href="users.php" class="uppercase">View All Users</a>
                </div>
                <!-- /.box-footer -->
            </div>

            <!-- /.info-box -->

            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title">Browser Usage</h3>

                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                class="fa fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i>
                        </button>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="chart-responsive">
                                <div id="pieChart"></div>
                            </div>
                            <!-- ./chart-responsive -->
                        </div>

                    </div>
                    <!-- /.row -->
                </div>
                <!-- /.box-body -->
            </div>
        </div>
    </div>
</div>
<!-- /.content -->

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>

<?php
$query = $_DB->selectDistinct('users', 'country', '', '`id` DESC');
$countries = array();
$userNumber = array();

while ($fetch = $_DB->fetchAssoc($query)) {
    $fetch['country'] = (empty($fetch['country'])) ? null : $fetch['country'];
    $fetch['userCounter'] = $_DB->CountRows('users', "`country`= '{$fetch['country']}'");
    array_push($countries, $fetch['country']);
    array_push($userNumber, $fetch['userCounter']);
}
$countriesData = array(['Country', 'Popularity']);
foreach ($countries as $k => $v) {
    $countriesData[] = array($v, $userNumber[$k]);
}
?>
<script>
    /*for google charts */

    google.charts.load('current', {'packages': ['geochart', 'corechart']});
    google.charts.setOnLoadCallback(drawRegionsMap);

    function drawRegionsMap() {

        var data = google.visualization.arrayToDataTable(<?php echo json_encode($countriesData)?>);

        var options = {
            colorAxis: {
                colors: ['#00c0ef', '#dd4b39', '#0073b7'],
                minValue: 0,
                maxValue: 2
            }, backgroundColor: {fill: '#00c0ef'}
        };

        var chart = new google.visualization.GeoChart(document.getElementById('regions_div'));

        chart.draw(data, options);

        function resizeHandler() {
            chart.draw(data, options);
        }

        if (window.addEventListener) {
            window.addEventListener('resize', resizeHandler, false);
        }
        else if (window.attachEvent) {
            window.attachEvent('onresize', resizeHandler);
        }
    }


    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {

        var data = google.visualization.arrayToDataTable(<?php echo json_encode($countriesData)?>);

        var options = {
            backgroundColor: {fill: '#ffffff'}
        };

        var chart = new google.visualization.PieChart(document.getElementById('pieChart'));

        chart.draw(data, options);

        function resizeHandler() {
            chart.draw(data, options);
        }

        if (window.addEventListener) {
            window.addEventListener('resize', resizeHandler, false);
        }
        else if (window.attachEvent) {
            window.attachEvent('onresize', resizeHandler);
        }
    }
</script>


<?php
include 'footer.php';
?>
