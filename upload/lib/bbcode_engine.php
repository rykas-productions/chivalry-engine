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
$builder = new JBBCode\CodeDefinitionBuilder('quote', '<table class="table table-bordered"><tr><th>{option} Wrote</th></tr><tr><td>{param}</tr></td></table>');
$builder->setUseOption(true);
$parser->addCodeDefinition($builder->build());

//Quote w/o option
$builder = new JBBCode\CodeDefinitionBuilder('quote', '<table class="table table-bordered"><tr><th>Somebody Wrote</th></tr><tr><td>{param}</tr></td></table>');
$parser->addCodeDefinition($builder->build());

//Keyboard
$builder = new JBBCode\CodeDefinitionBuilder('kbd', '<kbd>{param}</kbd>');
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
$builder = new JBBCode\CodeDefinitionBuilder('mark', '<mark>{param}</mark>');
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
$builder = new JBBCode\CodeDefinitionBuilder('youtube', '<div class="embed-responsive embed-responsive-16by9">
                                                            <iframe class="embed-responsive-item" id="ytplayer"
                                                                type="text/html"
                                                                src="https://www.youtube.com/embed/{param}/ frameborder="0">
                                                            </iframe></div>');
$parser->addCodeDefinition($builder->build());

//Danger
$builder = new JBBCode\CodeDefinitionBuilder('danger', '<span class="text-danger">{param}</span>');
$parser->addCodeDefinition($builder->build());

//Info
$builder = new JBBCode\CodeDefinitionBuilder('info', '<span class="text-info">{param}</span>');
$parser->addCodeDefinition($builder->build());

//Success
$builder = new JBBCode\CodeDefinitionBuilder('success', '<span class="text-success">{param}</span>');
$parser->addCodeDefinition($builder->build());

//Muted
$builder = new JBBCode\CodeDefinitionBuilder('mute', '<span class="text-muted">{param}</span>');
$parser->addCodeDefinition($builder->build());

//Primary
$builder = new JBBCode\CodeDefinitionBuilder('primary', '<span class="text-primary">{param}</span>');
$parser->addCodeDefinition($builder->build());

//Warning
$builder = new JBBCode\CodeDefinitionBuilder('warning', '<span class="text-warning">{param}</span>');
$parser->addCodeDefinition($builder->build());

//Line
$builder = new JBBCode\CodeDefinitionBuilder('hr', '<hr></hr>');
$parser->addCodeDefinition($builder->build());

//Mp3 Player
$builder = new JBBCode\CodeDefinitionBuilder('mp3', '<audio controls>
															  <source src="{param}" type="audio/mpeg">
															Your browser does not support the audio element.
															</audio>');
$parser->addCodeDefinition($builder->build());
//Ogg Player
$builder = new JBBCode\CodeDefinitionBuilder('ogg', '<audio controls>
															  <source src="{param}" type="audio/ogg">
															Your browser does not support the audio element.
															</audio>');
$parser->addCodeDefinition($builder->build());
//Wav Player
$builder = new JBBCode\CodeDefinitionBuilder('wav', '<audio controls>
															  <source src="{param}" type="audio/wav">
															Your browser does not support the audio element.
															</audio>');
$parser->addCodeDefinition($builder->build());

//Mp4 Player
$builder = new JBBCode\CodeDefinitionBuilder('mp4', '<div class="embed-responsive embed-responsive-16by9">
                                                            <video controls>
															  <source src="{param}" type="video/mp4">
															Your browser does not support the video tag.
															</video></div>');
$parser->addCodeDefinition($builder->build());

//WebM Player
$builder = new JBBCode\CodeDefinitionBuilder('webm', '<div class="embed-responsive embed-responsive-16by9">
                                                            <video controls>
															  <source src="{param}" type="video/webm">
															Your browser does not support the video tag.
															</video></div>');
$parser->addCodeDefinition($builder->build());