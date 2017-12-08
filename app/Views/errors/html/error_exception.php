
<div style="border:1px solid #990000;padding-left:20px;margin:0 0 10px 0;">

<h4>An uncaught Exception was encountered</h4>

<p>Type: <?php echo get_class($exception); ?></p>
<p>Message: <?php echo $message; ?></p>
<p>Filename: <?php echo $exception->getFile(); ?></p>
<p>Line Number: <?php echo $exception->getLine(); ?></p>

    <p>Backtrace:</p>
    <?php foreach ($exception->getTrace() as $error): ?>

        <?php if (isset($error['file'])): ?>

            <p style="margin-left:10px">
                File: <?php echo $error['file']; ?><br />
                Line: <?php echo $error['line']; ?><br />
                Function: <?php echo $error['function']; ?>
            </p>
        <?php endif ?>

    <?php endforeach ?>

</div>