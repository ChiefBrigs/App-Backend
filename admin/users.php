<?php
/**
 * Created by PhpStorm.
 * User: abderrahimelimame
 * Date: 8/7/16
 * Time: 23:38
 */
include 'header.php';
if ($_GB->getSession('admin') == false) {
    header("location:login.php");
}
?>

    <div class="box  box-info ">
        <?php
        if (isset($_GET['cmd']) && $_GET['cmd'] == 'users') {
            ?>


            <center>
                <div class="box-header">
                    <h3 class="box-title">Users List</h3>
                </div>
            </center>

            <div class="box-body">
                <table class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th style="text-align:center;   color: #0073b7 !important; font-size: 15px;">Username</th>
                        <th style="text-align:center;   color: #0073b7 !important; font-size: 15px;">Phone</th>
                        <th style="text-align:center;   color: #0073b7 !important; font-size: 15px;">Avatar</th>
                        <th style="text-align:center;   color: #0073b7 !important; font-size: 15px;">Country</th>
                        <th style="text-align:center;   color: #0073b7 !important; font-size: 15px;">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $rows = $_DB->CountRows('users');
                    $page = (isset($_GET['page']) && !empty($_GET['page'])) ? $Security->MA_INT($_GET['page']) : 1;
                    $_PAG = new Pagination($page, $rows, 20, 'users.php?cmd=users&page=#i#');
                    $query = $_DB->select('users', '*', '', '`id` DESC', $_PAG->limit);
                    while ($fetch = $_DB->fetchAssoc($query)) {
                        $username = $fetch['username'];
                        echo '<tr>';
                        echo '<td class="mdl-data-table__cell--non-numeric">';
                        if ($username == null) {
                            echo '<center><div class="btn-danger">** No username **</div></center>';
                        } else {
                            echo $fetch['username'];
                        }
                        echo '</td>';
                        echo '<td>';
                        echo $fetch['phone'];
                        echo '</td>';
                        echo '<td> ';
                        $userImage = $fetch['image'];
                        if ($userImage != null) { ?>
                            <img style="width: 100px ;height: 100px" alt="User Image" class="img-circle"
                                 src="../image/profile/<?php echo $userImage ?>">
                        <?php } else { ?>
                            <img style="width: 100px ;height: 100px" alt="User Image" class="img-circle"
                                 src="logo.png">
                            <?php
                        }
                        echo '</td>';
                        echo '<td>';
                        echo $fetch['country'];
                        echo '</td>';
                        echo '<td>';
                        echo '<a type="button"  href="users.php?cmd=deleteUser&id=' . $fetch['id'] . '" onclick="return checkDelete()"  class="btn btn-block btn-danger"> Delete </a>';
                        echo '</td >';
                        echo '</tr > ';
                    } ?>
                    </tbody>
                </table>
            </div>

            <?php
        } else if (isset($_GET['cmd'], $_GET['id']) && $_GET['cmd'] == 'deleteUser') {
            $id = $_DB->escapeString($_GET['id']);
            $delete = $_DB->delete('users', '`id` = ' . $id);
            if ($delete) {
                echo $_GB->ErrorDisplay('The user Deleted successfully', 'yes');
                echo $_GB->refreshPage('users.php?cmd=users', 1);
            } else {
                echo $_GB->ErrorDisplay('Failed to delete this user ,please try again later');
                echo $_GB->refreshPage('users.php?cmd=users', 1);
            }
        } ?>

    </div>
<?php
echo $_PAG->urls;
include 'footer.php'
?>
