
A PHP Error was encountered

Severity:    <?php echo $severity, "\n"; ?>
Message:     <?php echo $message, "\n"; ?>
Filename:    <?php echo $filepath, "\n"; ?>
Line Number: <?php echo $line; ?>
Backtrace:
<?php	foreach (debug_backtrace() as $error): ?>
    <?php		if (isset($error['file'])): ?>
        File: <?php echo $error['file'], "\n"; ?>
        Line: <?php echo $error['line'], "\n"; ?>
        Function: <?php echo $error['function'], "\n\n"; ?>
    <?php		endif ?>
<?php	endforeach ?>
