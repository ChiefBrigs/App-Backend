<?php
/**
 * Created by PhpStorm.
 * User: abderrahimelimame
 * Date: 8/8/16
 * Time: 00:28
 */

include 'header.php';
if ($_GB->getSession('admin') == false) {
    header("location:login.php");
}
?>
<div class="box  box-info ">
    <?php
    if (isset($_GET['cmd']) && $_GET['cmd'] == 'groups') {
        ?>

        <center>
            <div class="box-header">
                <h3 class="box-title">Groups List</h3>
            </div>
        </center>

        <div class="box-body ">
            <table class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th style="text-align:center;   color: #0073b7 !important; font-size: 15px;">GroupName</th>
                    <th style="text-align:center;   color: #0073b7 !important; font-size: 15px;">Avatar</th>
                    <th style="text-align:center;   color: #0073b7 !important; font-size: 15px;">Date</th>
                    <th style="text-align:center;   color: #0073b7 !important; font-size: 15px;">Members</th>
                    <th style="text-align:center;   color: #0073b7 !important; font-size: 15px;">Actions</th>
                </tr>
                </thead>

                <tbody>
                <?php
                $rows = $_DB->CountRows('groups');
                $page = (isset($_GET['page']) && !empty($_GET['page'])) ? $Security->MA_INT($_GET['page']) : 1;
                $_PAG = new Pagination($page, $rows, 20, 'groups.php?cmd=groups&page=#i#');
                $query = $_DB->select('groups', '*', '', '`id` DESC', $_PAG->limit);
                while ($fetch = $_DB->fetchAssoc($query)) {
                    $username = $fetch['name'];
                    $id = $fetch['id'];
                    echo '<tr>';
                    echo '<td class="mdl-data-table__cell--non-numeric">';
                    if ($username == null) {
                        echo "NULL";
                    } else {
                        echo $fetch['name'];
                    }
                    echo '</td>';
                    echo '<td>';
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
                    echo $fetch['date'];
                    echo '</td>';
                    echo '<td>';
                    echo '<a  type="button" href="groups.php?cmd=groupMembers&groupID=' . $id . '" class="btn btn-block btn-success">View</a>';
                    echo '<td>';
                    echo '<a type="button" onclick="return checkDelete()"  href="groups.php?cmd=deleteGroup&groupID=' . $id . '" class="btn btn-block btn-danger"> Delete </button>';
                    echo ' </td > ';
                    echo '</tr >';
                } ?>
                </tbody>
            </table>
        </div>

        <?php
    } else if (isset($_GET['cmd'], $_GET['groupID']) && $_GET['cmd'] == 'groupMembers') {
        $groupID = $_DB->escapeString($_GET['groupID']);
        ?>

        <center>
            <div class="box-header">
                <h3 class="box-title">Members List</h3>
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
                    <th style="text-align:center;   color: #0073b7 !important; font-size: 15px;">Role</th>
                </tr>
                </thead>

                <tbody>
                <?php
                $rows = $_DB->CountRows('groups');
                $page = (isset($_GET['page']) && !empty($_GET['page'])) ? $Security->MA_INT($_GET['page']) : 1;
                $_PAG = new Pagination($page, $rows, 20, 'groups.php?cmd=groupMembers&page=#i#');
                $query = " SELECT  GM.id ,GM.role,GM.groupID,U.id AS userId,U.username,U.country,U.phone,U.image

                             FROM prefix_users U,prefix_groups G,prefix_group_members GM
                             WHERE
                             CASE
                             WHEN GM.userID = U.id
                             THEN GM.groupID = G.id
                              END
                              AND
                              G.id = {$groupID}
                             GROUP BY U.id   ORDER BY U.id DESC LIMIT {$_PAG->limit}";
                $query = $_DB->MySQL_Query($query);
                while ($fetch = $_DB->fetchAssoc($query)) {
                    $username = $fetch['username'];
                    echo '<tr>';
                    echo '<td class="mdl-data-table__cell--non-numeric">';
                    if ($username == null) {
                        echo '<center><div class="btn-danger">** No username **</div></center>';
                    } else {
                        echo $username;
                    }
                    echo '</td>';
                    echo '<td>';
                    echo $fetch['phone'];
                    echo '</td>';
                    echo '<td>';
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
                    echo $fetch['role'];
                    echo '</td>';
                    echo '</tr >';
                }

                ?>
                </tbody>
            </table>
        </div>

    <?php } else if (isset($_GET['cmd'], $_GET['groupID']) && $_GET['cmd'] == 'deleteGroup') {
        $id = $_DB->escapeString($_GET['groupID']);
        $delete = $_DB->delete('groups', '`id` = ' . $id);
        if ($delete) {
            echo $_GB->ErrorDisplay('The group Deleted successfully', 'yes');
            echo $_GB->refreshPage('groups.php?cmd=groups', 1);
        } else {
            echo $_GB->ErrorDisplay('Failed to delete this group please try again later');
            echo $_GB->refreshPage('groups.php?cmd=groups', 1);
        }
    } ?>
</div>

<?php
echo $_PAG->urls;
include 'footer.php'
?>
