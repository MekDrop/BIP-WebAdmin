<?php
/**
 * About software info displayer
 *
 * @since       0.1
 * @author		MekDrop <github@mekdrop.name>
 */
?>
<code>
    BIP WebAdmin v.<?php echo $package['version']; ?> <?php echo htmlentities($package['author']); ?> <?php echo $package['years']; ?>
    <?php
    if (file_exists('CHANGELOG')) {
        ?> <br /><br /> <p><b>Changelog:</b>
        <blockquote>
            <?php echo nl2br(htmlentities(file_get_contents('CHANGELOG'))); ?>
        </blockquote>
    </p>
    <?php
}
?>
</code>
<br />
<br />
<hr />
<br />
<blockquote>
    <?php
    if (!isset($_SESSION['bip_version'])) {
        ob_start();
        passthru("bip -v");
        $_SESSION['bip_version'] = nl2br(htmlentities(ob_get_contents()));
        ob_end_clean();
    }
    echo $_SESSION['bip_version'];
    ?>
</blockquote>
