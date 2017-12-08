<h4>A PHP Error was encountered</h4>

<p>Severity: <?php echo $severity; ?></p>
<p>Message:  <?php echo $message; ?></p>
<p>Filename: <?php echo $filepath; ?></p>
<p>Line Number: <?php echo $line; ?></p>
    <p>Backtrace:</p>
    <?php foreach (debug_backtrace() as $error): ?>

        <?php if (isset($error['file'])): ?>

            <p style="margin-left:10px">
                File: <?php echo $error['file'] ?><br />
                Line: <?php echo $error['line'] ?><br />
                Function: <?php echo $error['function'] ?>
            </p>

        <?php endif ?>

    <?php endforeach ?>
</div>