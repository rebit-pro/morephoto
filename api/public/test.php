<?php

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

?>


<h1>Test Page</h1>

<?php
\Bitrix\Main\Loader::includeModule("iblock");
$t = \Bitrix\Iblock\Elements\ElementGalleryTable::getList([
    'select' => ['ID', 'NAME', 'PHOTOS_'=> 'PHOTOS'],
    'filter' => ['=CODE' => "shkola-5-21102025"],
])->fetchAll();

echo '<pre>';
print_r($t);
echo '</pre>';

?>

<?php

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");