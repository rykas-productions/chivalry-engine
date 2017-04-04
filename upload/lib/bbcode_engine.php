<?php
/*
	File: lib/bbcode_engine.php
	Created: 6/21/2016 at 4:54PM Eastern Time
	Info: Allows developer to add bbcode functions, along with view what has been setup.
	Author: TheMasterGeneral
	Website: https://github.com/MasterGeneral156/chivalry-engine
*/
require('JBBCode/Parser.php');
require_once("JBBCode/visitors/SmileyVisitor.php");
$parser = new JBBCode\Parser();
$parser->addCodeDefinitionSet(new JBBCode\DefaultCodeDefinitionSet());

//URL with option
$builder = new JBBCode\CodeDefinitionBuilder('url', '<a href="{option}">{param}</a>');
$builder->setUseOption(true);
$parser->addCodeDefinition($builder->build());

//URL
$builder = new JBBCode\CodeDefinitionBuilder('url', '<a href="{param}">{param}</a>');
$parser->addCodeDefinition($builder->build());

//Quote with option 
$builder = new JBBCode\CodeDefinitionBuilder('quote', '<blockquote>{param} <footer>{option}</footer></blockquote>');
$builder->setUseOption(true);
$parser->addCodeDefinition($builder->build());

//Quote w/o option
$builder = new JBBCode\CodeDefinitionBuilder('quote', '<blockquote>{param}</blockquote>');
$parser->addCodeDefinition($builder->build());

//Keyboard
$builder = new JBBCode\CodeDefinitionBuilder('keyboard', '<kbd>{param}</kbd>');
$parser->addCodeDefinition($builder->build());

//Code
$builder = new JBBCode\CodeDefinitionBuilder('code', '<code>{param}</code>');
$parser->addCodeDefinition($builder->build());

//Rage
$builder = new JBBCode\CodeDefinitionBuilder('rage', '<div class="text-uppercase"><b><big>{param}</b></big></div>');
$parser->addCodeDefinition($builder->build());

//Abbreviate
$builder = new JBBCode\CodeDefinitionBuilder('abbr', '<abbr data-toggle="tooltip" title="{option}">{param}</abbr>');
$builder->setUseOption(true);
$parser->addCodeDefinition($builder->build());

//Highlight
$builder = new JBBCode\CodeDefinitionBuilder('highlight', '<mark>{param}</mark>');
$parser->addCodeDefinition($builder->build());

//Strikethrough
$builder = new JBBCode\CodeDefinitionBuilder('s', '<s>{param}</s>');
$parser->addCodeDefinition($builder->build());

//Right align
$builder = new JBBCode\CodeDefinitionBuilder('right', '<div align="right">{param}</div>');
$parser->addCodeDefinition($builder->build());

//Left align
$builder = new JBBCode\CodeDefinitionBuilder('left', '<div align="left">{param}</div>');
$parser->addCodeDefinition($builder->build());

//Size
$builder = new JBBCode\CodeDefinitionBuilder('size', '<font size="{option}">{param}</font>');
$builder->setUseOption(true);
$parser->addCodeDefinition($builder->build());

//YouTube Video
$builder = new JBBCode\CodeDefinitionBuilder('youtube', '<div class="embed-responsive embed-responsive-16by9"><iframe class="embed-responsive-item" id="ytplayer" type="text/html" src="https://www.youtube.com/embed/{param}/ frameborder="0"></iframe></div>');
$parser->addCodeDefinition($builder->build());