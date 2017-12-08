<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?=$title?></title>
</head>
<body>

<div id="container">

    <div id="body">
        <h2>if语句:</h2>
        <?php if ($option1===true): ?>
            <p>条件option1成立</p>
        <?php elseif ($option1===true): ?>
            <p>条件option2成立</p>
        <?php else: ?>
            <p>条件不成立</p>
        <?php endif; ?>

        <h2>switch语句:</h2>
        <?php switch ($switch):?>
<?php case 1:?>
                <p>switch1成立</p>
                <?php break;?>
                <p>switch2成立</p>
            <?php case 2:?>
                <p>switch3成立</p>
                <?php break;?>
            <?php default:?>
                <p>switch default成立</p>
            <?php endswitch;?>

        <h2>while语句:</h2>
        <?php while ($while):?>
            <span><?=$while?></span>
            <?php $while--;?>
        <?php endwhile;?>

        <h2>for语句:</h2>
        <?php for ($i=0;$i<5;$i++):?>
            <span><?=$i?>,</span>
        <?php endfor;?>

        <h2>foreach语句:</h2>
        <?php foreach ($strings as $value): ?>
            <span><?=$value?>,</span>
        <?php endforeach; ?>
    </div>
</div>

</body>
</html>