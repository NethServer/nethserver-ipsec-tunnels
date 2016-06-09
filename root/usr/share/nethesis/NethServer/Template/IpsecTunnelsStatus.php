<?php

if (count($view['status'])<= 0) {
    echo $T('notunnels_label');
} else {
    echo "<div class='DataTable small-dataTable'>";
    echo "<table class='dataTable no-footer' role='grid'>\n";
    echo "<thead><tr role='role'>";
    echo "<th class='ui-state-default'>".$T('name_label')."</th>";
    echo "<th class='ui-state-default'>".$T('localnets_label')."</th>";
    echo "<th class='ui-state-default'>".$T('remotenets_label')."</th>";
    echo "<th class='ui-state-default'>".$T('status_label')."</th>";
    echo "</tr></thead><tbody>";
    foreach ($view['status'] as $tunnel => $props) {
        echo "<tr role='role'>";
        echo "<td>$tunnel</td><td>{$props['localnets']}</td><td>{$props['remotenets']}</td>";
        if ($props['status']) {
           $status = '<i class="fa fa-check-circle" style="color: green; font-size: 150%"></i>';
        } else {
           $status = '<i class="fa fa-warning" style="color: red; font-size: 150%"></i>';
        }
        echo "<td>$status</td>";
        echo "</tr>";
    }
    echo "</tbody></table>";
    echo "</div>";
}
