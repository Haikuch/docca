<!DOCTYPE html>
<html lang="<?php echo LANGUAGE_CODE; ?>">
<head>
    <meta charset="utf-8">
    <title><?php echo $title.' - '.SITETITLE;?></title>
    <?php
    echo $meta;//place to pass data / plugable hook zone
    Assets::css([
        '*.css',
        Url::templatePath().'css/style.css',
    ]);
    echo $css; //place to pass data / plugable hook zone
    ?>
    <script type="text/javascript" src="<?= Url::templatePath().'js/jquery.js'; ?>"></script>
    <script type="text/javascript" src="<?= Url::templatePath().'js/jquery-ui_custom/jquery-ui.min.js'; ?>"></script>
    <script type="text/javascript" src="<?= Url::templatePath().'js/index.js'; ?>"></script>
    <script type="text/javascript" src="<?= Url::templatePath().'js/filterbar.js'; ?>"></script>
    <script type="text/javascript" src="<?= Url::templatePath().'js/edit.js'; ?>"></script>
    <script type="text/javascript" src="<?= Url::templatePath().'js/view.js'; ?>"></script>
    <link rel="shortcut icon" href="<?= Url::templatePath().'images/icon2.png'; ?>" />
</head>
<body>
<?php echo $afterBody; //place to pass data / plugable hook zone?>


<div id="menu">   
    
    <a href="<?= DIR . 'admin'; ?>" title="<?php echo Language::show('menu_admin', 'Main'); ?>">
        <img src="<?= Url::templatePath().'images/options2.png'; ?>">
    </a>
    
    <a href="<?= DIR . 'docs/find'; ?>" title="<?php echo Language::show('menu_search', 'Main'); ?>">
        <img src="<?= Url::templatePath().'images/search3.png'; ?>">
    </a>
    
    <a href="<?= DIR . 'docs'; ?>" title="<?php echo Language::show('menu_docs', 'Main'); ?>">
        <img src="<?= Url::templatePath().'images/list4.png'; ?>">
    </a>
    
    <a href="<?= DIR . 'docs/add'; ?>" title="<?php echo Language::show('menu_add', 'Main'); ?>">
        <img src="<?= Url::templatePath().'images/add2.png'; ?>">
    </a>
    
    <div id="title">
        <img src="<?= Url::templatePath().'images/title.png'; ?>"> <?= SITETITLE; ?>
    </div>
    
</div>
    
<div class="container">
