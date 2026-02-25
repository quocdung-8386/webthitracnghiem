<?php
$s=0;
$s += 1/3;
for($i=6; $i<=96; $i+=6){
    $s=$s+1/$i;
}   
$s += 1/99;
echo("Tổng dãy số là: ".$s);
?>
