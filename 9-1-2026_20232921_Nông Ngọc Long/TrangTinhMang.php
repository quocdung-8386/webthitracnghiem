<?php
$number = [7, 9, 8, 99, 12, 14];
$sum = 0;
foreach($number as $num){
    echo $num."<br>";
    $sum = $sum + $num;
}
echo $sum."<br>";
?>