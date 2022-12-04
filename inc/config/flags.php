<span class="label label-default label-primary">
    <?php echo $webui_actual_language; ?>
</span>
<?php
foreach ($languages as $langCode => $langName)
{
    if ($langCode != $webui_language_code)
    {
        echo '
            <a href="?page='.$_GET[page].'&amp;lang=' . $langCode . '">
                <img src="img/flags/flag-'. $langCode . '.png" alt="' . $langName . '" title="' . $langName . '" />
            </a>
        ';
    }
}
?>
